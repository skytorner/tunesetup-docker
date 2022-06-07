<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Blacklist as Blacklist;
use App\Models\Campaign;
use App\Models\Advertiser;
use Illuminate\Http\Request;
use App\Helpers\BlackListManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BlacklistController extends Controller
{

    /**
     * @var BlackListManager
     */
    private $blManager;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->blManager = new BlackListManager();
        $this->middleware('auth')->except('downloadBlacklist');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $campaigns   = Campaign::all();
        $advertisers = Advertiser::all();

        return view('account.blacklist.index', compact('campaigns', 'advertisers'));
    }

    public function upload(Request $request)
    {     
        request()->validate([
            'file' => 'required|mimes:txt,csv,xls,xlsx'
        ]);

        $extension = $request->file('file')->extension();

        if(in_array($extension, $this->blManager::EXCEL_EXTENSIONS)) {
            $fileContent    = $this->blManager->convertExcelToCsv($request);
            $content        = $this->blManager->prepareContent($fileContent);
            $savedExtension = $this->blManager::CSV_EXTENSION;
        } else {
            $content        = $this->blManager->prepareContent($request->file('file')->getContent());
            $savedExtension = $extension;
        }

        $delimiter       = $this->blManager->checkSeparator($content[0]);
        $encryptionInfos = $this->blManager->manageEncryption($content, $delimiter);
        
        if(!$delimiter) {
            return redirect()->back()->withErrors(['Unknow delimiter, it can be only : , or ; or TAB'])->withInput();
        }
        
        if($encryptionInfos === false) {
            return redirect()->back()->withErrors(['Problem with data encryption ( not MD5, not SHA1 or not usual @email)'])->withInput();
        }
        
        list($columns, $lines, $length, $content) = $this->blManager->analyseFile($content, $delimiter);
        list($path, $filename) = $this->blManager->storeBlackList($content, 1520, $savedExtension);

        $blacklist = new Blacklist();

        //prepare campaign or advertiser infos 
        if(isset($request->advertiser) && (null !== $request->advertiser)) {
            $blacklist->adv_id       = $request->advertiser;
            $hoResponse              = ho_short_call('Offer', 'findAllIdsByAdvertiserId', ['advertiser_id' => $request->advertiser]);
            $response                = json_decode($hoResponse);
            $campaigns               = $response->response->data;
            $blacklist->campaigns_id = $campaigns;

        } elseif (isset($request->campaign) && !empty($request->campaign)) {
            $blacklist->campaigns_id   = Arr::whereNotNull($request->campaign);
        }

        $blacklist->created_by     = auth()->user()->id;
        // $blacklist->aff_id         = auth()->user()->aff_id;
        $blacklist->filename       = $filename;
        $blacklist->extension      = $request->file->extension();
        $blacklist->separator      = $delimiter;
        $blacklist->encryption     = $encryptionInfos[0];
        $blacklist->header         = $encryptionInfos[1];
        $blacklist->nb_columns     = $columns;
        $blacklist->nb_lines       = $lines;
        $blacklist->content_length = $length;
        $blacklist->path           = $path;
        $blacklist->save();

        return redirect()->route('blacklist-edit', ['id' => $blacklist->id])->with('success', 'New Blacklist added with success, the name is '. $filename);
    }

    public function listBlackLists()
    {
        $blacklists = Blacklist::all();
        $encryptionsType = $this->blManager::ENCRYPTION_KEY_VAL;

        return view('account.blacklist.list', compact('blacklists', 'encryptionsType'));
    }

    public function deleteBlackList(Request $request)
    {
        $id        = $request->route('id');
        $blacklist = Blacklist::find($id);
        
        $query = $blacklist->delete();

        if(!$query) {
            return redirect()->back()->withErrors(["Can't delete blacklist with ID :$id"]);
        }

        return redirect()->back()->with('success', "Blacklist with ID : $id is successfully deleted" );
    }
   
    public function editBlackList(Request $request)
    {
        $id              = $request->route('id');
        $blacklist       = Blacklist::find($id);
        $campaigns       = Campaign::whereIn('id', $blacklist->campaigns_id)->get();
        $encryptionsType = $this->blManager::ENCRYPTION_KEY_VAL;

        return view('account.blacklist.edit', compact('blacklist', 'campaigns', 'encryptionsType'));
    }

    public function modifyBlackList(Request $request)
    {
        $blacklist = Blacklist::find($request->blacklist_id);
        $fileBL    = storage_path('app/public/blacklists/'.$blacklist->filename);
        
        if(!$fileBL) {
            return redirect()->back()->withErrors(["Couln't find $fileBL"]);
        } 
        
        if ($request->hasFile('file')) {
            request()->validate(['file' => 'mimes:txt,csv,xls,xlsx']);

            $extension = $request->file('file')->extension();

            if(in_array($extension, $this->blManager::EXCEL_EXTENSIONS)) {
                $fileContent    = $this->blManager->convertExcelToCsv($request);
                $content        = $this->blManager->prepareContent($fileContent);
                $savedExtension = $this->blManager::CSV_EXTENSION;
            } else {
                $content        = $this->blManager->prepareContent($request->file('file')->getContent());
                $savedExtension = $extension;
            }
    
            $delimiter       = $this->blManager->checkSeparator($content[0]);
            $encryptionInfos = $this->blManager->manageEncryption($content, $delimiter);
            
            //check nbr of column 1st 
            $contentByCol = explode($delimiter, $content[0]);
            $emailsSaved  = '';

            if(count($contentByCol) < 2) { // case when we have 1 col
                foreach ($content as $emailLine) {
                    if(false !== $hash = $this->blManager->findHash($emailLine)) {
                        $lineToAdd = $this->blManager->getFormattedNewLine($blacklist, $emailLine, $hash);
                        
                        if($this->blManager->edit($fileBL, $lineToAdd, $blacklist)) {
                            $emailsSaved .= "$emailLine \n";
                        }
                    }
                }

                return redirect()->back()->with('success', "Addresses $emailsSaved have been added to $fileBL with success");

            } else { //multi column
                // compare encryptions from file VS DB
                $encryptionInfos = $this->blManager->manageEncryption($content, $delimiter);
                
                // same encryptions configurations (same nbr of columns AND same order)
                if($encryptionInfos[0] == $blacklist->encryption) {

                    if(false === $hash = $this->blManager->findHash($contentByCol[0])) { // we have a header so we remove it before insert
                        unset($content[0]);
                        $content = array_values($content); //reindex the array after deletion
                    }

                    list($columns, $lines, $length, $content) = $this->blManager->analyseFile($content, $blacklist->separator);
                    list($path, $filename) = $this->blManager->storeBlackList($content, 1520, $blacklist->extension, $blacklist->filename);
                    $blacklist->nb_lines       += $lines;
                    $blacklist->content_length += $length;
                    $blacklist->save();
                }

                return redirect()->back()->with('success', "Addresses $emailsSaved have been added to $fileBL with success");
            }
        }

        // here we just give 1 email to submit
        $hash      = $this->blManager->findHash($request->address);                                // we figure out the encryption
        $lineToAdd = $this->blManager->getFormattedNewLine($blacklist, $request->address, $hash);

        if($this->blManager->edit($fileBL, $lineToAdd, $blacklist)) {
            return redirect()->back()->with('success', "Address $request->address added to $fileBL with success");
        }

        return redirect()->back()->withErrors(["Couln't add $lineToAdd into $fileBL"]);
    }

    public function addEmailsChunk(Request $request)
    {
        $blacklist = Blacklist::find($request->id);
        $file      = storage_path('app/public/blacklists/'.$blacklist->filename);
        
        $hash      = $this->blManager->findHash($request->address); // we figure out the encryption
        $lineToAdd = $this->blManager->getFormattedNewLine($blacklist, $request->address, $hash);

        if($this->blManager->edit($file, $lineToAdd, $blacklist)) {
            return Response()->json(['blacklist' => $blacklist, 'address' => $request->address]);
        }

        return Response()->json(['error' => "couldnt update the Blacklist"], 500);
    }

    public function downloadBlacklist(Request $request)
    {
        $filename     = $request->route('filename');
        $filenameExt  = File::extension($filename);
        $formatAsked  = $request->format;
        $blacklist    = Blacklist::where("filename", $filename)->first();
        $file         = public_path('storage/blacklists/'.$blacklist->filename);
        $emailsToSent = '';
        $spreadsheet  = IOFactory::load($file);
        $sheetData    = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $hashAsked    = $request->encryption;
        
        //check encrypt type
        if($request->has('encryption') && $blacklist->header !=='email') {
            $encryptions = explode($this->blManager::ENCRYPTION_TYPE_SEPARATOR, $blacklist->encryption);

            foreach ($encryptions as $encryptCol) { // check which column corresponds to requested encryption type
                $encryptionDetail = explode($this->blManager::ENCRYPTION_COL_SEPARATOR, $encryptCol);
                $column           = $encryptionDetail[0];
                $encryptionType   = $encryptionDetail[1];
                $encryptIdx       = $this->blManager::ENCRYPTION_KEY_VAL[$hashAsked];

                if ($encryptionType == $encryptIdx) { // we have a match
                    break;
                }
            }

            foreach($sheetData as $line) { // we only get email from $column
                $encryptedEmail = $line[$column];

                if(null !== $encryptedEmail || !empty($encryptedEmail)) {
                    $emailsToSent .= $encryptedEmail.PHP_EOL;
                }
            }

            $newFile = $this->blManager->changeFileExtension($formatAsked, $filenameExt, $blacklist->filename);

            if(!copy($file, $newFile)) {
                return redirect()->back()->withErrors(["Error while copying $newFile blacklist"]);
            }

            list($filepath, $filename) = $this->blManager->storeBlackList($emailsToSent, $blacklist->adv_id, $blacklist->extension, $blacklist->id.'/'.$request->encryption.'/'.$blacklist->filename, $overwrite = true);

            return  Response()->download($filepath);
        }elseif ($blacklist->header === 'email') { // the BL is originally just email, we need to do the conversion into $request->encryption before downloading it
            // $emails = Storage::disk('local')->get('/public/blacklists/'.$blacklist->filename);
            $sheetData = Arr::flatten($sheetData);
            $sheetData = array_map(function($item) use($hashAsked) {
                return hash($hashAsked, $item);
            }, $sheetData);

            $sheetData = implode(PHP_EOL, $sheetData);

           list($filepath, $filename) = $this->blManager->storeBlackList($sheetData, $blacklist->adv_id, $blacklist->extension, $blacklist->id.'/'.$request->encryption.'/'.$blacklist->filename, $overwrite = true);

           return  Response()->download($filepath);
        }
        
        // change format of file requested
        if( ($formatAsked === $this->blManager::TXT_EXTENSION && $filenameExt === $this->blManager::CSV_EXTENSION) 
            || ($formatAsked === $this->blManager::CSV_EXTENSION && $filenameExt === $this->blManager::TXT_EXTENSION) 
            ) {
            $newFile = $this->blManager->changeFileExtension($formatAsked, $filenameExt, $blacklist->filename);

            if(!copy($file, $newFile)) {
                return redirect()->back()->withErrors(["Error while copying $newFile blacklist"]);
            }

            return  Response()->download($newFile);
        }

        // original format file requested
        return Response()->download($file);
    }
}
