<?php 
use Illuminate\Support\Facades\Http;

function ho_call($target, $method, $fields, $sort = 'desc', $limit = '100000') {

    $token = env('HASOFFER_TOKEN');
    $domain = env('HASOFFER_DOMAIN');

    $field = '';
    foreach($fields as $param) {
        $field .= '&fields[]=' . $param;
    }

    $response = Http::get( $domain . 
        '?NetworkToken=' . $token . 
        '&Target=' . $target . 
        '&Method=' . $method . 
        $field .
        '&sort[id]=' . $sort .
        '&limit=' . $limit
    );

    return $response->getBody()->getContents();
}

function ho_short_call($target, $method, $params) {

    $token = env('HASOFFER_TOKEN');
    $domain = env('HASOFFER_DOMAIN');

    $param = '';
    foreach($params as $key => $value) {
        $param .= '&' . $key . '=' . $value;
    }

    $response = Http::get( $domain . 
        '?NetworkToken=' . $token . 
        '&Target=' . $target . 
        '&Method=' . $method . 
        $param
    );

    return $response->getBody()->getContents();
}

function ho_putCreativeFile($offer_id, $creative_name, $creativeCode) {

    $token = env('HASOFFER_TOKEN');
    $domain = env('HASOFFER_DOMAIN');

    // Remove .html
    $creative_name = removeExt($creative_name);

    //prepare html code to be sent with curl
    $data    = ['data' => ['code'=> $creativeCode]];
    $datas   = http_build_query($data, '', '&amp;');

    // HO Want Curl request for uploading creative... so not guzzle here
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://affdjz.api.hasoffers.com/Apiv3/json');
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'NetworkToken='.$token.'&Target=OfferFile&Method=create&data[display]='.$creative_name.'&data[offer_id]='.$offer_id.'&data[type]=email+creative&'.$datas);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);

    $response = curl_exec($ch);
    $info     = curl_getinfo($ch);
    
    curl_close($ch);
    // Decode the response into a PHP associative array
    $response = json_decode($response, true);

    // Make sure that there wasn't a problem decoding the repsonse
    if(json_last_error()!==JSON_ERROR_NONE){
        throw new RuntimeException(
            'API response not well-formed (json error code: '.json_last_error().')'
        );
    }

    return $response['response']['data']['OfferFile'];
}

function ho_putLandingPage($offer_id, $lp_name, $lp_url, $preview_url) {

    $token = env('HASOFFER_TOKEN');
    $domain = env('HASOFFER_DOMAIN');

    $response = Http::get( $domain . 
        '?NetworkToken=' . $token . 
        '&Target=OfferUrl&Method=create&data[offer_id]=' . $offer_id . 
        '&data[offer_url]=' . urlencode($lp_url) .
        '&data[name]=' . $lp_name .
        '&data[preview_url]=' . urlencode($preview_url)
    );

    return $response->getBody()->getContents();
}

function ho_findOfferInfos($offer_id, $fields) {

    $token = env('HASOFFER_TOKEN');
    $domain = env('HASOFFER_DOMAIN');

    $field = '';
    foreach($fields as $param) {
        $field .= '&fields[]=' . $param;
    }

    $response = Http::get( $domain . 
        '?NetworkToken=' . $token . 
        '&Target=Offer&Method=findById&id=' . $offer_id . 
        $field
    );

    return $response->getBody()->getContents();
}