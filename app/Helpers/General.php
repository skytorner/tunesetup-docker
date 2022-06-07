<?php 

const TEST_AFFILIATE_ID = 2;

function getTrackingDomain($offer_id) {

    $tracking = ho_short_call(
        'Offer', 
        'generateTrackingLink', 
        [
            'offer_id' => $offer_id, 
            'affiliate_id' => TEST_AFFILIATE_ID
        ]
    );

    $tracking = json_decode($tracking, true);        
    $trackingDomain = $tracking['response']['data']['click_url'];

    return $trackingDomain;
}

function today_date($format = "Y-m-d H:i:s") {
    return date($format, strtotime("today"));
}

function removeExt($string) {
    $string =  explode(".", $string);
    return $string[0];
}

function removeForbiddenCharacters($string) {
    $search = array("?");
    $replace = array("");
    return str_replace($search, $replace, $string);
}

function removeSpaceFromString($string) {
    $search = array(" ");
    $replace = array("_");
    return str_replace($search, $replace, $string);
}

function campaignProtocol($campaign_id) { // To know if S2S ( need tracking_link ) ou Image Pixel

    $protocol = ho_findOfferInfos($campaign_id, ["protocol"]);

    $protocol = json_decode($protocol, true);        
    $protocol = $protocol['response']['data']['Offer']['protocol'];

    return $protocol;
}

function minifieHTML($codeHtml) {
    //remove redundant (white-space) characters
    $replace = array(
        //remove tabs before and after HTML tags
        '/\>[^\S ]+/s'   => '>',
        '/[^\S ]+\</s'   => '<',
        //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
        '/([\t ])+/s'  => ' ',
        //remove leading and trailing spaces
        '/^([\t ])+/m' => '',
        '/([\t ])+$/m' => '',
        // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
        '~//[a-zA-Z0-9 ]+$~m' => '',
        //remove empty lines (sequence of line-end and white-space characters)
        '/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
        //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
        '/\>[\r\n\t ]+\</s'    => '><',
        //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
        '/}[\r\n\t ]+/s'  => '}',
        '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
        //remove new-line after JS's function or condition start; join with next line
        '/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
        '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
        //remove new-line after JS's line end (only most obvious and safe cases)
        '/\),[\r\n\t ]+/s'  => '),',
        //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
        '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
    );
    $body = preg_replace(array_keys($replace), array_values($replace), $codeHtml);

    //remove optional ending tags (see http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission )
    $remove = array(
        '</option>', '</li>', '</dt>', '</dd>', '</tr>', '</th>', '</td>'
    );
    
    return str_ireplace($remove, '', $body);
}

function doubleMinifieHTML($html)
{
   $search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'); # strip whitespaces between = "'

   $replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1");

    $html = preg_replace($search,$replace,$html);
    return $html;
}

function HtmlEncodeAccents($html)
{
   $search = array(
    '/é/',
    '/É/',
    '/ê/',
    '/Ê/',
    '/ë/',
    '/Ë/',
    '/è/',
    '/È/',
    '/à/',
    '/À/',
    '/â/',
    '/Â/',
    '/ô/',
    '/œ/',
    '/û/',
    '/ù/',
    '/ü/',
    '/ç/',
    '/î/',
    '/ï/',
    '/&/',
    );

   $replace = array(
    "&eacute;",
    "&EAcute;",
    "&ecirc;",
    "&Ecirc;",
    "&euml;",
    "&Euml;",
    "&egrave;",
    "&Egrave;",
    "&agrave;",
    "&Agrave;",
    "&acirc;",
    "&Acirc;",
    "&ocirc;",
    "&oelig;",
    "&ucirc;",
    "&ugrave;",
    "&uuml;",
    "&ccedil;",
    "&icirc;",
    "&iuml;",
    "&amp;"
    );

    $html = preg_replace($search,$replace,$html);
    return $html;
}

function HtmlDecodeAccents($html)
{
   $replace = array(
    'é',
    'ê',
    'ë',
    'è',
    'à',
    'â',
    'ô',
    'œ',
    'û',
    'ù',
    'ü',
    'ç',
    'î',
    'ï',
    '&',
    );

   $search = array(
    "/&eacute;/",
    "/&ecirc;/",
    "/&euml;/",
    "/&egrave;/",
    "/&agrave;/",
    "/&ecirc;/",
    "/&ocirc;/",
    "/&oelig;/",
    "/&ucirc;/",
    "/&ugrave;/",
    "/&uuml;/",
    "/&ccedil;/",
    "/&icirc;/",
    "/&iuml;/",
    "/&amp;/",
    );

    $html = preg_replace($search,$replace,$html);
    return $html;
}

function HtmlRemoveUrlCharacters($html)
{
   $search = array(
    '/&/',
    );

   $replace = array(
    "%26;"
    );

    $html = preg_replace($search,$replace,$html);
    return $html;
}

function strpos_array(string $string, array $array, int $offset = 0): int
{
    foreach($array as $line) {
        if(strpos($string, $line, $offset) !== false) {
            return strpos($string, $line, $offset); // stop on first true result
        }
    }
    return -1;
}

function count_match(array $expressions, string $string): int 
{    
    foreach($expressions as $expression) {
        preg_match_all("/" . $expression . "/", $string, $match);
        if(count($match) > 0 && !empty($match[0])) {
            $matches[] = $match[0];
        }
    }
    return isset($matches) ? count($matches) : 0;
}