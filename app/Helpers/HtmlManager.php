<?php

namespace App\Helpers;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use App\Models\Creative_upload as Creative;
use PhpParser\Builder\Class_;
use App\Helpers\CreativeManager;

class HtmlManager
{
    /**
     * @var Options
     */
    private $options;

    /**
     * @var CreativeManager
     */
    private $creaManager;

    const UNSUBSCRIBE_FILTER = [
        'voyez pas ce message correctement',
        'unsubscribe',
        'la version en ligne',
        'désinscrire'
    ];

    public function __construct()
    {
        $this->creaManager = new CreativeManager;
        $this->options = new Options;
        $this->options
            ->setCleanupInput(false)
            ->setRemoveScripts(false)
            ->setRemoveSmartyScripts(false)
            ->setRemoveStyles(false)
            ->setRemoveDoubleSpace(false)
            ->setPreserveLineBreaks(true)
            ->setNoSlash($this->creaManager::NO_SLASH)
            ->addSelfClosingTags($this->creaManager::NO_SLASH)
        ;
    }
    
    public function replaceHrefLinks($code, $linkToReplace) {
        $dom = new Dom;
        $dom->loadStr($code, $this->options);
    
        $body = $dom->find('body');
        $links = $body->find('a');
    
        foreach($links as $link) {
            $link->setAttribute('href', $linkToReplace);
        }
    
        return $dom->innerHtml;
    }

    public function removeUnsubscribeLinks($creativeCode) {
        $dom      = new Dom;
        $dom->loadStr($creativeCode, $this->options);
        $body     = $dom->find('body');        // Ne peu être que dans le body
        $divs     = $body->find('div');
        $div_nb   = 0;
        $div_size = strlen($body->innerHtml);
    
        foreach($divs as $div) { // On boucle les divs pour trouver la premiere petite qui pourrait contenir un unsubscribe link
            if(strpos_array($div->innerHtml, self::UNSUBSCRIBE_FILTER) < 0 ) { // on check que c'est pas une div qui englobe tout, donc moins de 5000 caractères
                $div_nb++;
            } else {
                $matches[] = $div_nb;
                $div_nb++;
            }
        }
    
        foreach($matches as $match) { // We search the smallest to optimize operation
            if(strlen($divs[$match]->innerHtml) < $div_size) {
                $div_size = strlen($divs[$match]->innerHtml);
                $smallest = $match;
            }
        }
    
        $parent = $divs[$smallest]->getParent(); // Take parent of smallest node to delete it
        $parent->delete();
    
        return $dom->innerHtml;
    }
    
    public function removeUnwantedData($creativeCode) { // Remove unsubscribe link for example
        $count = count_match(self::UNSUBSCRIBE_FILTER, $creativeCode); // To know how many "boucles" it must plan to replace all unwanted data
        
        if ($count < 1) {
            return $creativeCode;
        }

        for($i = 0; $i < $count; $i++) {
            $cleanCode = self::removeUnsubscribeLinks($creativeCode);
        }

        return $cleanCode;
    }
    
    // DOCTYPE MANAGEMENT
    // Find doctype in html code and extract it in a variable
    public function extractDoctype($html) { 
        $doctype = '';
        $doctype_pos = stripos($html, '<!DOCTYPE');

        if($doctype_pos !== false) {
            $doctype = substr($html, $doctype_pos, strpos($html, '>', $doctype_pos) - $doctype_pos + 1);
        }

        return $doctype;
    }
    
    // delete doctype in html code
    public function deleteDoctype($html) {
        $doctype_pos = stripos($html, '<!DOCTYPE');

        if($doctype_pos !== false) {
            $html = substr($html, 0, $doctype_pos) . substr($html, strpos($html, '>', $doctype_pos) + 1);
        }

        return $html;
    }
    
    // Add doctype in the first line of html code
    public function addDoctype($html, $doctype) {
        $html = $doctype . $html;

        return $html;
    }


    //todo : each hreflin should be replaced by {tracking_link}
    // public function changeLink($code, $linkToReplace) {
    
    //     $code = mb_convert_encoding($code, 'HTML-ENTITIES', "UTF-8");
    
    //     $doc = new DOMDocument();
    
    //     $doc->loadHTML($code);
        
    
    //     $count = 0;
    //     foreach($doc->getElementsByTagName('a') as $link) {
    //         // echo $link;
    //         // echo "value" .$link->nodeValue, PHP_EOL;
    //         $link->setAttribute("href", $linkToReplace);
    //         $count++;
    //     }
    
    //     return $doc->saveHTML();
    // }
}