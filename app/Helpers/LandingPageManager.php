<?php

namespace App\Helpers;

use App\Models\Creative_upload as Creative;

class LandingPageManager {

    public function createLpOnOffer($offer_id, $lp_name, $lp_url, Creative $creative) {

        // $creative->offer_url         = $lp_url;
        // $creative->offer_preview_url = self::findLpPreviewUrl($lp_url);

        $newLpInfosResponse = ho_putLandingPage($offer_id, $lp_name, $lp_url, $creative->offer_preview_url);
        $newLpInfosResponse = json_decode($newLpInfosResponse, true);
        $existingCrea       = $newLpInfosResponse['response']['data']['OfferUrl']['id'];

        $creative->ho_lp_id = $existingCrea;
        $creative->save();

        return $creative;
    }

    public function findLpPreviewUrl($lp_url) {

        $base_url = explode("?", $lp_url);
        return $base_url[0];
    }
}