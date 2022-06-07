TuneSetup :

composer require paquettg/php-html-parser --with-all-dependencies
Doc here : https://packagist.org/packages/paquettg/php-html-parser

For zip package :
https://github.com/zanysoft/laravel-zip

WAITING TASK FOR CREA UPDATING: Zip issue : "Malformed UTF-8 characters, possibly incorrectly encoded"

HOW TO USE : 
1. npm install
2. composer install
3. php artisan migrate
4. php artisan hasoffers:campaign:fill

Optionnal : composer dump-autoload ( permit to script to access .env vars if modified )


// Tunes API links : 
https://developers.tune.com/network/offer/
https://developers.tune.com/network/offerfile/
https://developers.tune.com/network/offerurl/

https://developers.tune.com/network/offer-getofferurls/#api-call-builder


// Steps : 
Create creative
Create LP 

Generate tracking & replace it in creative
http://tracking.djzlu.lu/aff_c?offer_id=$offerID&aff_id=$defaultAffiliateID&url_id=$LpID

Create creative in offers : 
https://developers.tune.com/network/offerfile-create/ -> A marché avec code minifier
Test offer : 4594
crea test : 16408

Pour savoir si S2S ou https pixel : https://developers.tune.com/network/offer-findbyid/

faire un base setup :

id, user_id, campaign_name, offer_id, lp_ho_id, creative_id, creative_ho_id, creative_number, lp_number, tracking_domain, tracking_link

faire un base creative_validated
id, user_id, offer_id, creative_name, creative_number, creative_origin_id, tracking_link, creative_internal_url

Pour le drag and drop : https://codepen.io/mahdaen/pen/Ejwodb ( faut mettre en opactié 0 et mettre une div de même taille au fond)

Pour trouver le lien de tracking : 
https://developers.tune.com/network/offer-generatetrackinglink/

Gestion HTML avec DomDocument ? 
https://www.php.net/manual/en/class.domdocument.php

// A FAIRE : 

OK - Gerer mieux les titles qui sont vide/pas rempli ou absent
NON - Ajouter lien crea/LP dans la description ? https://affdjz.api.hasoffers.com/Apiv3/json?NetworkToken=NETR6MgItrYrMsdQiUrf3ROPY0p2kI&Target=Offer&Method=update&id=4594&data[description]=

