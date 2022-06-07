<?php

namespace App\Helpers;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use Illuminate\Http\Request;
use App\Models\Campaign as Campaign;
use Illuminate\Support\Facades\Storage;
use App\Models\Creative_upload as Creative;
use Illuminate\Support\Str;
use ZipArchive;
use App\Helpers\HtmlManager;
use Illuminate\Support\Facades\File;

//todo  : 
// LP on doit vérifier que le mail redirige vers la LP corresopndante

class CreativeManager
{
    private $file;

    private $creative;

    /**
     * @var Options
     */
    private $options;


    const TRACKING_LINK_GENERIC = '{tracking_link}';

    const NO_SLASH = [
        '!--[if mso | IE]--',
        '![endif]--',
        '![endif]',
        '!--[if',
        '!--[if mso | ie]',
        '!DOCTYPE',
        '!doctype',
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        '<!DOCTYPE HTML PUBLIC "-',
        '<!doctype html public "-',
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">'
    ];

    const CREATIVE_UPLOAD_FOLDER = 'public/creative_upload/';

    private $imageExtension = ['jpeg', 'jpg', 'png', 'gif'];

    public function __construct()
    {
        $this->options = new Options;
        $this->options
            ->setCleanupInput(false)
            ->setRemoveScripts(false)
            ->setRemoveSmartyScripts(false)
            ->setRemoveStyles(false)
            ->setRemoveDoubleSpace(false)
            ->setPreserveLineBreaks(true)
            ->setNoSlash(self::NO_SLASH)
            ->addSelfClosingTags(self::NO_SLASH)
        ;
    }

    public function defineExtension($file)
    {
        $ext = explode('.', $file);
        $ext = "." . end($ext);

        return $ext;
    }

    /**
     * Function handleFile handle the request and save the sent file into our flesystem
     * it returns an array with the files (with path)
     *
     * @param Request $request
     * @param Campaign $campaign
     */
    public function uploadFile(Request $request, Campaign $campaign)
    {
        //on renvoi l'emplacement du fichier 
        $folderPath         = self::CREATIVE_UPLOAD_FOLDER.Str::slug($campaign->campaign_id).'/'.date('Y-m-d');
        $originalFilename   = $request->fileUploaded->getClientOriginalName(); 
        $file               = $request->file('fileUploaded');
        $ext                = self::defineExtension($originalFilename);
        
        if ($ext == '.zip') { 
            $zipFolderPath  = $folderPath.'/zip';
            $file           = $file->storeAs($zipFolderPath, $originalFilename); //save backup du zip
            $files          = self::unZipCreative($campaign, $file, $folderPath, $originalFilename); //create folder  in /public/$folderpath
            
            return $files;
        }

        $file = $file->storeAs($folderPath, $originalFilename); //create folder in /storage/app/$folderpath

        return $file;
    }

    /**
     * unZipCreative uncompressed an archive and upload the image on S3 bucket
     * @param Campaign $campaign : campaign linked to the creative
     * @param string $fileZip : path where the zip is stored
     * @param string $folderPath : path for extracted files
     * 
     * @return array|null path of files in uncompressed archive
     */
    public function unZipCreative(Campaign $campaign, $fileZip, $folderPath, $filename)
    {
        $fileZip = Storage::path($fileZip);
        $fileZip = iconv("UTF-8", "Windows-1252//IGNORE", $fileZip);
        $zip     = new ZipArchive();
        $res     = $zip->open($fileZip, ZipArchive::CREATE);
        $numFiles = $zip->numFiles;

        if ($res === TRUE) {

            $folderExtracted = '/'.Str::slug(File::basename($filename));
            $path = storage_path('app/'.$folderPath.$folderExtracted); // extract the zip inside the upload_folder
            $zip->extractTo($path); 
            $zip->close();
           
            $files = Storage::allFiles($folderPath.$folderExtracted);
            
            foreach ($files as $file) {
                $folderContents[substr(self::defineExtension($file), 1)] = $file;
                $extension = File::extension($file);

                if (in_array($extension, $this->imageExtension)) {
                    $fileToUpload = Storage::get($file);
                    $filename     = self::generateS3FolderName($campaign->name).'/'.File::basename($file);
                    Storage::disk('s3')->put($filename, $fileToUpload, 'public');
                    $publicUrls[]             = Storage::disk('s3')->url($filename);
                    $folderContents['s3-url'] = $publicUrls;
                }
            }

            return $folderContents; 
        }
        
        return false;
    }

    public function generateS3FolderName($campagnName)
    {
        $baseName    = self::defineTitle($campagnName);
        $cleanedName = preg_replace("/\(([^\)]+)\)/", "", $baseName); //get everything between parenthesis and delete it
        
        return $folderName = Str::slug($cleanedName);
    }

    // que fait l'action?
        //manageTitle :  
            //génération du titre 
            //remplacement dans le tag TITLE 
            //creative->code save 
        //removeUnwantedData 
            //suppression des liens newsletter 
            //creative->code save 
        //manageLP:
            //findLP URL
                //$creative->offer_url save
            //generation LP name
                //$creative->lp_number_in_offer save
            //get queryString parameters
    public function setupNewCreative(Creative $creative, Campaign $campaign, $ext, $creativeDates)
    { 
        // Main
        $htmlManager = new HtmlManager();
        $creative    = self::manageTitle($creative, $campaign, $ext, $creativeDates);
        $creative    = $htmlManager->removeUnwantedData($creative);

        list($lp, $urlParams, $lpName) = self::manageLp($creative,  $campaign, $creativeDates);

        return [$creative, $lp, $urlParams, $lpName];
    }

    public function manageLp(Creative $creative, Campaign $campaign, $creativeDates )
    {
        $pending    = "true";
        $domTreated = new Dom;
        $domTreated->loadStr($creative->code, $this->options);

        $lp        = self::findLp($domTreated);
        $lpName    = self::setLandingPageName($campaign, $creativeDates, $pending);
        // => Il faut d'abord clean l'url, s'assurer qu'il y a le ? etc... 
        $urlParams = self::getUrlParams($lp);
        $creative->offer_url = $lp;
        $creative->save();

        return [$lp, $urlParams, $lpName];
    }

    public function getUrlParams($link)
    {
        return parse_url($link, PHP_URL_QUERY);
    }

    public function findLp($domTreated)
    {
        $body    = $domTreated->find('body');  // Only href in the body
        $domBody = new Dom;

        // Prevent creative code error
        // if(!isset($body->outerHtml)) {
        //     echo 'Error : Problem in the creative code, it miss something in the HTML structure. We are not able to manage it automatically';
        //     exit;
        // }

        $domBody->loadStr($body->outerHtml, $this->options);
        $links = $domBody->find('a')[0];
        $link  = $links->getAttribute('href');

        // Verifiy if the link have a ? to handle parameters
        if (strpos($link, '?') === false) {
            $link = $link . '?';
        }

        return $link;
    }

    public function manageTitle(Creative $creative, Campaign $campaign, $ext, $creativeDates)
    {
        $dom     = new Dom;
        $dom->loadStr($creative->code, $this->options);
        $title   = $dom->find('title');
        $pending = true;

        if (count($title) && self::checkOrignalTitleContent($title->outerHtml)) { // la balise title est rempli
            $title->delete(); // On supprime pour la recréer
        }

        // dans tous les cas, on reset la balise title avec le nom de la campagne
        $title    = self::defineTitle($campaign->name);
        $creative = self::createTitleNode($dom, $title, $creative);
        $creative = self::setCreativeName($campaign, $creativeDates, $pending);  // Define the name of the creative file

        return $creative;
    }

    public function checkOrignalTitleContent($title)
    {
        $title = str_replace('<title>', "", $title);
        $title = str_replace('</title>', "", $title);

        preg_match('/\S+/', $title, $match);

        if (count($match) > 0) {
            return true;
        }

        return false;
    }

    //this function cause error depending on the html file
    public function createTitleNode(Dom $dom, $title, Creative $creative)
    {
        $head           = $dom->find('head')[0];
        $newNode        = "<title>" . $title . "</title>";
        
        $headFirstChild = $head->firstChild();
        try {
            $headFirstChild->setText($newNode);
            $creative->code  = $dom->innerHtml;
            $creative->title = $title;
            $creative->save();
    
            return $creative;

        } catch (\Throwable $th) {
            return false;
        }
       
    }

    public function defineTitle($campaign_name)
    {
        $newTitle = explode("-", $campaign_name);
        $newTitle = $newTitle[0];

        try {
            $newTitle = preg_match('/]\s(.*)/', $newTitle, $newTitles);

            if(!empty($newTitles)) {
                $newTitle = rtrim($newTitles[0]);
                $newTitle = ltrim($newTitle);
                $newTitle = substr($newTitle, 1);
            }else {
                $newTitle = $campaign_name;
            }
            
        } catch (Exception $e) {
            echo 'Error while define title L.84 creative.php : ',  $e->getMessage(), "\n";
        }

        return $newTitle;
    }

    public function setCreativeName(Campaign $campaign, $creativeDates, $pending = false)
    {
        $name = self::defineTitle($campaign->name);

        if ($pending) {
            $pending = "_pending";
        }

        $creativeNumber = self::countOfferCreative($campaign->campaign_id);
        $dateBegin      = $creativeDates['start_date'];                     // Faire une recherche input de la date de la crea
        $dateEnd        = $creativeDates['end_date'];                       // Faire une recherche input de la date de la crea

        if ($dateEnd < 1 || $dateEnd == "undefined") { // Si dateEnd n'a pas été défini 
            $dateEnd = "?"; // "%3F";
        }

        if ($dateBegin < 1 || $dateBegin == "undefined") {
            $dateBegin = today_date('dmy');
        }

        $creativeName = self::underscoreSpaces($name . "_crea" . $creativeNumber . "_du" . $dateBegin . "_au" . $dateEnd . $pending);

        return $creativeName;
    }

    public function setLandingPageName(Campaign $campaign, $creativeDates, $pending = false)
    {
        $name = self::defineTitle($campaign->name);

        if ($pending) {
            $pending = "_pending";
        }

        $count     = self::countOfferLandingPage($campaign->campaign_id);
        $dateBegin = $creativeDates['start_date'];                         // Faire une recherche input de la date de la crea
        $dateEnd   = $creativeDates['end_date'];                           // Faire une recherche input de la date de la crea

        if ($dateEnd < 1 || $dateEnd == "undefined") { // Si dateEnd n'a pas été défini 
            $dateEnd = "?"; // "%3F";
        }

        if ($dateBegin < 1 || $dateBegin == "undefined") {
            $dateBegin = today_date('dmy');
        }

        $lpName = self::underscoreSpaces($name . "_url" . $count . "_du" . $dateBegin . "_au" . $dateEnd . $pending);

        return $lpName;
    }


    public function underscoreSpaces($string)
    {
        $string = preg_replace('/\s{2,}/', ' ', $string); // remove double whitespaces

        return strtolower(preg_replace('/\s/', '_', ltrim($string)));
    }

    public function getOfferLandingPage($campaignId)
    {
        $existingLp = ho_short_call('Offer', 'getOfferUrls', ['id' => $campaignId]);

        $existingLp = json_decode($existingLp, true);
        $existingLp = $existingLp['response']['data'];

        return $existingLp;
    }

    public function countOfferLandingPage($campaignId)
    {
        $existingLp = self::getOfferLandingPage($campaignId);
        
        return count($existingLp) + 2;       // Default =1 and not comptabilized
    }

    public function getOfferCreatives($campaignId)
    {
        $existingCrea = ho_short_call('Offer', 'getOfferFiles', ['id' => $campaignId]);

        $existingCrea = json_decode($existingCrea, true);
        $existingCrea = $existingCrea['response']['data'];

        return $existingCrea;
    }

    public function countOfferCreative($campaignId)
    {
        $existingCrea = self::getOfferCreatives($campaignId);

        return count($existingCrea) + 1; // Default =1 and not comptabilized
    }

    public function uploadCreativeToTunes($request, $creative)
    {
        $htmlManager        = new HtmlManager();
        $creative->code     = $request->html;
        $creative->code     = $htmlManager->addDoctype($creative->code, $creative->doctype);
        $creativeAddedInfos = ho_putCreativeFile($request->campaign_id, $request->validated_title, $creative->code);
        
        $creative->ho_creative_id = $creativeAddedInfos['id'];
        $creative->code           = $htmlManager->deleteDoctype($creative->code);
        $creative->save();

        return $creative;
    }

    public function prepareKitTest($request, $creative)
    {
        $htmlManager = new HtmlManager();
        // Connaitre le tracking domaine
        $trackingBase = getTrackingDomain($creative->campaign_number);

        // Construire les paramètres
        $trackingUrl = $trackingBase . '&url_id=' . $creative->ho_lp_id . '&file_id=' . $creative->ho_creative_id; //file_id 
        // $trackingUrl = $trackingBase . '&url_id=' . $creative->ho_lp_id;

        // remplacer tracking_link par la nouvelle URL
        $creative->code = $htmlManager->replaceHrefLinks($creative->code, $trackingUrl);
        $creative->code = HtmlDecodeAccents($creative->code);
        // $creative->code = $htmlManager->addDoctype($creative->code, $creative->doctype);
        $creative->save();

        return $creative;
    }

    public function titleKitTest($creative)
    {
        $kitTestName = "test_" . removeSpaceFromString(removeForbiddenCharacters($creative->title)) . "_kit" . $creative->crea_number_in_offer . '.html';

        return $kitTestName;
    }

    public function treatUploadedCreative(Creative $testKit) 
    {
        $htmlManager = new HtmlManager();

        $titleForCreaTest = self::titleKitTest($testKit);
        $testKit->code    = $htmlManager->addDoctype($testKit->code, $testKit->doctype);
        $creativeStock    = Storage::disk('local')->put('public/creative_traite/' . $titleForCreaTest, $testKit->code);

        return public_path('storage/creative_traite/'.$titleForCreaTest);
    }    
    
    public function getTrackingUrl(Creative $creative)
    {
        $trackingBase = getTrackingDomain($creative->campaign_number);
        $trackingUrl  = $trackingBase . '&url_id=' . $creative->ho_lp_id . '&file_id=' . $creative->ho_creative_id;  //file_id 

        return $trackingUrl;
    }
}
