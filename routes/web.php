<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Account
// -- Creative Upload -- //
Route::get('/account', 'AccountController@index')->name('account');
Route::post('/account/creative-upload', 'AccountController@creativeUpload')->name('account-creative-upload');
Route::post('/account/creative-create', 'AccountController@creativeCreate')->name('account-creative-create');
Route::post('/account/creative-install', 'AccountController@creativeInstall')->name('account-creative-install');
Route::post('/account/lp-create', 'AccountController@createLandingPage')->name('account-lp-create');
Route::post('/account/creative-clean-code', 'AccountController@creativeCleanCode')->name('account-creative-clean-code');
Route::get('/account/creative-test-download/{id}', 'AccountController@creativeTestDownload')->name('account-creative-test-download');
Route::get('/account/creative-kit-test-visualise/{id}', 'AccountController@creativeTestVisualize')->name('account-creative-test-visualize');
Route::get('/account/creative-kit-test-download/{id}', 'AccountController@creativeKitTestDownload')->name('account-creative-kit-test-download');
Route::post('/account/creative-preview', 'AccountController@creativePreview')->name('account-creative-kit-preview');

Route::post('/account/get-landingpagename', 'AccountController@getLandingPageName')->name('account-creative-getlandingpagename');
Route::post('/account/get-creativename', 'AccountController@getCreativeName')->name('account-creative-getcreativename');
Route::post('/account/get-titletag', 'AccountController@getTitleTag')->name('account-creative-gettitletag');
Route::post('/account/get-offertitle', 'AccountController@getOfferTitle')->name('account-creative-getoffertitle');
Route::post('/account/get-offerprotocol', 'AccountController@getOfferProtocol')->name('account-creative-getofferprotocol');
Route::post('/account/get-campaignid', 'AccountController@getCampaignId')->name('account-creative-getcampaignid');
Route::post('/account/get-trackingurl', 'AccountController@getTrackingUrl')->name('account-creative-gettrackingurl');
Route::post('/account/get-kittest', 'AccountController@getKitTest')->name('account-creative-getkittest');
Route::post('/account/getall-landingpage', 'AccountController@getAllLandingPages')->name('account-creative-getall-landingpage');

// -- BlackLists -- //
Route::get('/blacklist', 'BlacklistController@index')->name('blacklist');
Route::post('/blacklist/upload', 'BlacklistController@upload')->name('blacklist-upload');
Route::get('/blacklists/list', 'BlacklistController@listBlackLists')->name('blacklists-list');
Route::get('/blacklist/delete/{id}', 'BlacklistController@deleteBlackList')->name('blacklist-delete');
Route::get('/blacklist/edit/{id}', 'BlacklistController@editBlackList')->name('blacklist-edit');
Route::post('/blacklist/modify/{id}', 'BlacklistController@modifyBlackList')->name('blacklist-modify');
Route::get('/blacklist/download/{filename}', 'BlacklistController@downloadBlacklist')->name('blacklist-download')->withoutMiddleware(['auth']);
Route::post('/blacklist/add-emails-chunk', 'BlacklistController@addEmailsChunk')->name('blacklist-add-emails-chunk');


// Affiliation
Route::get('/affiliation', 'AffiliationController@index')->name('affiliation');

// Configuration
Route::get('/configuration', 'ConfigurationController@index')->name('configuration');
