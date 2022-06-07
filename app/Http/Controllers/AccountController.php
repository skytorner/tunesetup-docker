<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Validator,Redirect,Response;
use App\Models\Creative_upload as Creative;
use App\Models\Campaign as Campaign;
use App\Helpers\CreativeManager;
use App\Helpers\HtmlManager;
use Illuminate\Contracts\Cache\Store;
use App\Helpers\LandingPageManager;

class AccountController extends Controller
{
    /**
     * @var CreativeManager
     */
    private $creaManager;

    /**
     * @var HtmlManager
     */
    private $htmlManager;

    /**
     * @var LandingPageManager
     */
    private $lpManager;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->creaManager = new CreativeManager();
        $this->htmlManager = new HtmlManager();
        $this->lpManager   = new LandingPageManager();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $campaigns = Campaign::all();
        
        return view('account.kit.account', compact('campaigns'));
    }

    /**
     * creativeUpload Upload the client file into our server
     * @param Request contains file, $startShoot, $endShoot
     * @return Response contains the file Path, the campaign and shootingDates
     */
    public function creativeUpload(Request $request)
    {
        request()->validate(['fileUploaded'  => 'required|mimes:html,zip|max:4000']);

        if ($request->hasFile('fileUploaded')) {
            $campaignName = $request->campaign;
            $campaign     = Campaign::where('name', $campaignName)->first();
            $file         = $this->creaManager->uploadFile($request, $campaign);
            $imagesS3     = [];

            if(is_string($file)) {
                $creativeCode      = Storage::get($file);
                $creativeLocalPath = $file;
            } elseif(is_array($file)) {
                $creativeCode      = Storage::get($file['html']);
                $creativeLocalPath = $file['html'];

                if (isset($file['s3-url'])) {
                    $imagesS3 = $file['s3-url'];
                }
            }

            return Response()->json([
                'file'          => $creativeLocalPath,
                'campaign'      => $campaign,
                'html'          => $creativeCode,
                "imagesS3"      => $imagesS3,
            ]);
        }
    
        return Response()->json([
            "success" => false,
            "file"    => ''
        ], 500);
    }

    public function creativeCleanCode(Request $request)
    {
        $htmlCode  = $request->htmlCode;
        $cleanCode = $this->htmlManager->removeUnwantedData($htmlCode);

        return Response()->json([
            'html' => $cleanCode,
        ]);
    }

    /**
     * creativeCreate create a Creative Entity based on uploaded file
     * @return Response 
     */
    public function creativeCreate(Request $request)
    {
        //ici on reçoit le fichier html (+img path cas de zip)
        //on setup la creative ici
        
        if(Storage::exists($request->filepath)){
            $countChar = strlen($request->code);
            $filepath      = $request->filepath;
            $htmlCode      = $request->code;
            $title         = $request->title;
            $length        = $request->length;
            $campaignId    = $request->campaignNumber;
            $offerUrl      = $request->offerUrl;
            $startDate     = $request->startDate;
            $endDate       = $request->endDate;
            $creaName      = $request->creaName;
            $creativeDates = ['start_date' => $startDate, 'end_date' => $endDate];
            $campaign      = Campaign::where('campaign_id', $campaignId)->first();
            
            //we update the content of the creative
            Storage::put($filepath, $htmlCode);

            // on ouvre & extrait le fichier html
            // $creativeCode       = Storage::get($filepath);
            $fileOriginalName = File::basename($filepath);
            $ext              = '.'.File::extension($fileOriginalName);

            // Create creative for DB
            $creative                       = new Creative();
            $creative->code                 = $this->htmlManager->deleteDoctype($htmlCode);
            $creative->length               = $length;
            $creative->title                = $title;
            $creative->filename             = $filepath;
            $creative->original_name        = $fileOriginalName;
            $creative->campaign_number      = $campaignId;
            $creative->offer_url            = $offerUrl;
            $creative->offer_preview_url    = $this->lpManager->findLpPreviewUrl($offerUrl);
            $creative->lp_number_in_offer   = $this->creaManager->countOfferLandingPage($campaignId);
            $creative->doctype              = $this->htmlManager->extractDoctype($htmlCode);
            $creative->crea_display_name    = $creaName;
            $creative->crea_number_in_offer = $this->creaManager->countOfferCreative($campaignId);
            $creative->start_date           = $startDate;
            $creative->end_date             = $endDate;
            $creative->upload_user_id       = $request->user()->id;
            $creative->save();

            return Response()->json(['creative' => $creative]);
        }

        return Response()->json(['error' => $request->filepath." does not exist"], 500);
    }

    /**
     * Create LP on HO
     */
    public function createLandingPage(Request $request)
    {
        $creative        = Creative::where('id', $request->crea_id)->first();
        $createLpOnOffer = $this->lpManager->createLpOnOffer($request->campaign_id, $request->lp_name, $request->validated_lp, $creative);

        return Response()->json([
            "success"           => true,
            "response_lp"       => $createLpOnOffer,
            "lp_paired_name"    => $request->lp_name,
        ]);
    }

    /**
     * Install creative on HO after validation
     *
     */
    public function creativeInstall(Request $request)
    {
        // request : validated_lp, lp_name, validated_title, campaign_id, crea_id, protocol, html
        $creative        = Creative::where('id', $request->crea_id)->first();

        if(isset($request->useExistingLp, $request->existingLpId) && $request->useExistingLp && ($request->existingLpId !== null || !empty($request->existingLpId))) {
            $creative->ho_lp_id = $request->existingLpId;
            $creative->save();
        }

        $html            = $request->html;
        $creativeToTune  = $this->creaManager->uploadCreativeToTunes($request, $creative, $html);
        $this->creaManager->treatUploadedCreative($creativeToTune);
        $downloadRoute   = route('account-creative-test-download', ['id' => $creative->id]);
        
        return Response()->json([
            "success"           => true,
            "response_creative" => $creativeToTune,
            "lp_paired_name"    => $request->lp_name,
            "download_url"      => $downloadRoute,
        ]);
    }

    public function getKitTest(Request $request)
    {
        $creative          = Creative::where('id', $request->crea_id)->first();
        $html              = $request->html;
        $creative->code    = $this->htmlManager->deleteDoctype($html);
        $creative->doctype = $this->htmlManager->extractDoctype($html);
        $creative->save();
        $visualizeRoute    = route('account-creative-test-visualize', ['id' => $creative->id]);
        $downloadTestRoute = route('account-creative-kit-test-download', ['id' => $creative->id]);

        return Response()->json([
            "lp_paired"         => $creative->ho_lp_id,
            "crea_paired"       => $creative->ho_creative_id,
            "crea_paired_name"  => $request->creaname,
            "visualize_url"     => $visualizeRoute,
            "downloadTestRoute" => $downloadTestRoute
        ]);
    }

    /**
     * creativeTestDownload download KitTest action 
     */
    public function creativeTestDownload(Request $request)
    {
        $id               = $request->route('id');
        $creative         = Creative::where('id', $id)->first();
        $titleForCreaTest = $this->creaManager->titleKitTest($creative);
        $file             = public_path('storage/creative_traite/'.$titleForCreaTest);

        return Response()->download($file);
    }

    /**
     * creativeTestVisualize open Creative kitTest in a new tab 
     */
    public function creativePreview(Request $request)
    {
        $html = $request->html;

        return view('account.kit.test_kit_visualiser', compact('html'));
    }
    /**
     * creativeTestVisualize open Creative kitTest in a new tab 
     */
    public function creativeTestVisualize(Request $request)
    {
        $id       = $request->route('id');
        $creative = Creative::where('id', $id)->first();
        $html     = $this->htmlManager->addDoctype($creative->code, $creative->doctype);

        return view('account.kit.test_kit_visualiser', compact('html'));
    }

    /**
     * download creativeTest (with url test )
     */
    public function creativeKitTestDownload(Request $request)
    {
        $id               = $request->route('id');
        $creative         = Creative::where('id', $id)->first();
        $html             = $this->htmlManager->addDoctype($creative->code, $creative->doctype);
        $titleForCreaTest = $this->creaManager->titleKitTest($creative);
        Storage::disk('local')->put('public/kit_test/' . $titleForCreaTest, $creative->code);
        $file             = public_path('storage/kit_test/'.$titleForCreaTest);
        return Response()->download($file);
    }


    public function getLandingPageName(Request $request)
    {
        $campaignName  = $request->campaignName;
        $startDate     = ($request->start_date < 1 || $request->start_date == "undefined") ? today_date('dmy') : $request->start_date;
        $endDate       = ($request->end_date < 1 || $request->end_date == "undefined") ? '?' :  $request->end_date;
        $creativeDates = [
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        $campaign = Campaign::where('name', $campaignName)->first();
        $lpName   = $this->creaManager->setLandingPageName($campaign, $creativeDates, $pending = true);
        
        return Response()->json(['lpName' => $lpName, 'creativeDates' => $creativeDates]);
    }

    public function getCreativeName(Request $request)
    {
        $campaignName  = $request->campaignName;
        $startDate     = $request->start_date;
        $endDate       = $request->end_date;
        $creativeDates = [
            'start_date' => ($startDate < 1 || $startDate == "undefined") ? today_date('dmy') : $startDate,
            'end_date'   => ($endDate < 1 || $endDate == "undefined") ? '?' :  $endDate
        ];

        $campaign     = Campaign::where('name', $campaignName)->first();
        $creativeName = $this->creaManager->setCreativeName($campaign, $creativeDates, $pending = true);

        return Response()->json(['creativeName' => $creativeName]);
    }
    
    public function getTitleTag(Request $request)
    {
        $campaignName  = $request->campaignName;
        $startDate     = $request->start_date;
        $endDate       = $request->end_date;
        $creativeDates = [
            'start_date' => ($startDate < 1 || $startDate == "undefined") ? today_date('dmy') : $startDate,
            'end_date'   => ($endDate < 1 || $endDate == "undefined") ? '?' :  $endDate
        ];

        $campaign = Campaign::where('name', $campaignName)->first();
        $titleTag = $this->creaManager->defineTitle($campaign->name);

        return Response()->json(['titleTag' => $titleTag]);
    }

    public function getOfferTitle(Request $request)
    {
        $campaignName = $request->campaignName;

        return Response()->json(['campaignName' => $campaignName]);
    }

       
    public function getOfferProtocol(Request $request)
    {
        $campaignName = $request->campaignName;
        $campaign     = Campaign::where('name', $campaignName)->first();
        $protocol     = campaignProtocol($campaign->campaign_id);

        return Response()->json(['protocol' => $protocol]);
    }

    public function getCampaignId(Request $request)
    {
        $campaignName = $request->campaignName;
        $campaign     = Campaign::where('name', $campaignName)->first();

        return Response()->json(['campaign' => $campaign]);
    }

    public function getTrackingUrl(Request $request)
    {
        $creative    = Creative::where('id', $request->creaId)->first();
        $trackingUrl = $this->creaManager->getTrackingUrl($creative);
        
        return Response()->json(['trackingUrl' => $trackingUrl]);
    }

    public function getAllLandingPages(Request $request)
    {
       $landingPages = $this->creaManager->getOfferLandingPage($request->campaignId);

       return Response()->json(['landingPages' => $landingPages]);
    }
}
