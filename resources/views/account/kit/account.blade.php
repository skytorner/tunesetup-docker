@extends('account.account_layout')

@section('content-account')

@section('h1')
Kit
@endsection
<div class="row step-1">
    <div class="col-md-12">
        <h4>Upload your creative</h4>
        <label for="campaignSelector" class="form-label">Select the campaign</label>
        <input class="form-control" list="campaignsList" id="campaignSelector" placeholder="Type to search...">
        <datalist id="campaignsList">
            @foreach($campaigns as $campaign)
                {{-- @if ($campaign->campaign_id == "4594") --}}
                <option value="{{ $campaign->name }}"></option>
                {{-- @endif --}}
            @endforeach
        </datalist>
    </div>
</div>

<div class="row mt-3 step-1">
    <div class="col-6 col-sm-12 col-lg-6">
        <form method="POST" enctype="multipart/form-data" id="creative_upload" action="javascript:void(0)" >
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div id="dropzone">
                            <div>DRAG N DROP HERE </div>
                            <input type="file" name="fileUploaded" placeholder="Choose File" id="file">
                        </div>
                    <span class="text-danger">{{ $errors->first('fileUploaded') }}</span>
                    </div>
                </div>
            </div>     
        </form>
    </div>
    <div id="set_date" class="col-6 col-sm-12 col-lg-6 d-flex flex-column mt-2 d-flex align-items-left justify-content-center">
        <div class="d-flex flex-row d-flex align-items-center">
            <label for="start_date" class="w-50">Kitname start date</label>
            <input data-date-type="start" type="text" name="start_date" placeholder="ddmmyy" id="start_date" class="p-2 mx-2 w-50">
        </div>
        <div class="d-flex flex-row d-flex align-items-center my-1">
            <label for="start_date" class="w-50">Kitname end date</label>
            <input data-date-type="end" type="text" name="end_date" placeholder="ddmmyy" id="end_date" class="p-2 mx-2 w-50">
        </div>
    </div>
    <div class="col-md-12 text-center">
        <button id="btn-upload" type="button" class="col-md-12 text-center btn btn-primary">Upload</button>
    </div>
</div>

<form method="POST" id="creative_install" action="javascript:void(0)">
    <div class="row mt-3 step-2 top-border border-gray">
        <h2>Offer informations</h2>
        <div id="fileCard" class="col-12 col-sm-12 col-lg-12">
            <div id="fileHolder" class="card card-body">
                <h5>Uploaded File</h5>
                <p id="file_path"></p>
                <a target="_blank" id="btn-preview" href="{{ route('account-creative-kit-preview')}}">Preview</a>
            </div>
        </div><br>
        <div class="col-12 col-sm-12 col-lg-12">
            <div class="row">
                <div id="protocolDisplay" class="col-12 alert alert-success text-center" role="alert"></div>
            </div><br>

            <div id="holder-input-titleOffer" class="row">
                <div class="col-1 loader-spin">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col-11">
                    <h4 class="set_campaign_name">Title</h4>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-12 col-lg-12">

            <div class="mt-2 py-10">
                <label for="input-lpName" class="form-label">Landing Page Name</label>
                <div id="holder-input-lpName" class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col-11">
                        <input id="input-lpName" class="form-control form-control-sm w-100" type="text" />
                    </div>
                </div>
               
                <div class="container">
                    <div class="row ">
                        <div class="col-12 text-center btn-holder-lp">
                            <div class="form-check form-check-inline">
                                <button id="btn_all_lp" class="col-12 btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExistingLp" aria-expanded="false" aria-controls="collapseExistingLp">
                                    <i class="bi bi-arrow-repeat"></i> use an existing Landing Page
                                </button>
                              </div>
                            <div class="form-check form-check-inline">
                                <button id="btn_new_lp" class="col-12 btn btn-success" type="button" data-toggle="collapse" data-target="#collapseExistingLp" aria-expanded="false" aria-controls="collapseExistingLp">
                                    <i class="bi bi-plus-circle-dotted"></i> use a new Landing Page
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 collapse" id="collapseExistingLp">
                            <div class="input-group mb-3" id="existingLpHolder">
                                <select data-placeholder="Choose a LP" class="form-control form-control-chosen" name="existingLp" id="existingLpSelect"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-2 py-10">
                <label for="input-creaname" class="form-label">Creative Name</label>
                <div id="holder-input-creaname" class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col-11">
                        <input id="input-creaname" class="form-control form-control-sm w-100" type="text" />
                    </div>
                </div>
            </div>

            <div class="mt-2 py-10">
                <label for="input-titleTag" class="form-label">Balise Title</label>
                <div id="holder-input-titleTag" class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col-11">
                        <input id="input-titleTag" class="form-control form-control-sm w-100" type="text" />
                    </div>
                </div>
            </div>
{{-- only needed to check all urls and imgs in creative | check @creativeUpload  some lines are to uncomment too --}}
{{-- 
            <div class="mt-2 py-10">
                <label for="input-links" class="form-label">Liens présents <span id="links-count"></span></label>
                <div class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id ="links-holder" class="col-11"></div>
                </div>
            </div>

            <div class="mt-2 py-10">
                <label for="input-imgs" class="form-label">Images présentes <span id="imgs-count"></span></label>
                <div class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id ="imgs-holder" class="col-11"></div>
                </div>
            </div>  
--}}


            <div class="mt-2 py-10">
                <h2>Creative informations</h2>
                <label for="input-lp" class="form-label">Check the Landing Page URL</label>
               
                <div id="holder-input-lp" class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="col-11">
                        <textarea  id="input-lp" class="form-control" name="textarea-input-lp" rows="3"></textarea>
                    </div>
                </div> <br>

                <div class="row">
                    <button class="col-12 btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseUrlLp" aria-expanded="false" aria-controls="collapseUrlLp">
                        Select an other URL for the landing page (<span id="lp-url-count"></span>)
                    </button>
                </div>

                <div style="margin-top:15px" class="row">
                    <button id="btn-generate-parameter" class="col-12 btn btn-primary">Generate parameter </button>
                </div>

                <div class="row">
                    <div class="collapse" id="collapseUrlLp">
                        <div class="row" id="lpUrlsHolder"></div>
                    </div>
                </div>
            </div>

            <div id="holder-input-s3links"class="mt-2 py-10">
                <label for="input-s3-links" class="form-label">Uploaded Images on S3  <span id="s3-links-count"></span></label>
                <div class="row">
                    <div class="col-1 loader-spin">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div id ="input-s3-links-holder" class="col-11"></div>
                </div>
            </div>
        </div>

        <div id="parameters-space" class="col-12 col-sm-12 col-lg-12""></div>

        <div id="add-parameter" class="col-12 col-sm-12 col-lg-12 d-flex flex-column mt-2 d-flex justify-content-between">
            <button id="add-parameter" class="btn btn-secondary mt-2 py-10 w-100">Add new parameter </button>
        </div>

        <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
            <button id="reset" type="submit" class="btn btn-danger mt-2 py-10 w-30">Go back</button>
            <button id="confirm-button" type="submit" class="btn btn-primary mt-2 py-10 w-30">Confirm & Add this creative </button>
        </div>
        
    </div>

    <input id="campaign_id" class="hide" type="hidden" value="" />
    <input id="crea_id" class="hide" type="hidden" value="" />
    <input id="lp_name" class="hide" type="hidden" value="" />
    <input id="protocol" class="hide" type="hidden" value="" />
    <input id="hidden_start_date" class="hide" type="hidden" value="" />
    <input id="hidden_end_date" class="hide" type="hidden" value="" />
    <input id="hidden_use_existing_lp" class="hide" type="hidden" value="true" />
    <input id="hidden_existing_lp_id" class="hide" type="hidden" value="" />
</form>


<div class="row mt-3 step-3 top-border border-gray">

    <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
        <h5>Félicitations, votre kit à bien été installé</h5>
    </div>


    <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
        <a class="btn btn-primary custom-btn" id="kit_url" href="#"><i class="bi bi-download"></i> Télécharger la créative installée</a>
        <br />
    </div>

    <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
        <a class="btn btn-success custom-btn" target="_blank" id="kit_visualize" href="#"><i class="bi bi-box-arrow-up-right"></i> Afficher et tester la créative installée</a>
        <br />
    </div>

    <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
        <a class="btn btn-success custom-btn" target="_blank" id="btn_download_kittest" href="#"><i class="bi bi-download"></i> Télécharger le kit test</a>
        <br />
    </div>

    <div class="col-md-12 mt-4 d-flex flex-row justify-content-between align-items-center">
        <span id="crea_test"></span> => <span id="lp_test"></span>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

var   URL_QUERY_STRING      = [];
var   NEW_INPUT_INDEX       = 0;
let   TRACKING_URL          = ''
const TRACKING_LINK_GENERIC = "{tracking_link}";
var   HTML                  = '';                          // set in creativeUpload()
let   CLEAN_HTML_CODE       = ''
let   SORTABLE_IDX_START    = 0;
let   SORTABLE_IDX_END      = 0;
var   baseUrl               = window.location.origin+'/';
var   validatedLp           = undefined;
var   validatedTitle        = undefined;
var   validatedCampaignId   = undefined;
var   creaId                = undefined;
var   lpName                = undefined;
var   protocol              = undefined;
var   $startDate            = undefined;
var   $endDate              = undefined;
var   $campaignName         = '';

// change cursor on loader on every ajax call
$(document).ajaxStart(function() {
    $(document.body).css({'cursor' : 'wait'});
}).ajaxStop(function() {
    $(document.body).css({'cursor' : 'default'});
});

$(document).ready(function (e) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    init_steps(); // comment to show all fields

    $('#campaignSelector').on('change', campaignChange);
    $('#btn-upload').on('click', step1);
    $('#add-parameter').on('click', createNewParamsInputs);
    $('#confirm-button').on('click', step2);
    $('#input-lpName').on('keyup', delay(updateLpName, 250));

    $('#parameters-space').sortable();

    $('#parameters-space').on('sortstart', (event, ui) => {
        let divMoved           = ui.item;
            SORTABLE_IDX_START = divMoved.index();
    });

    $('#parameters-space').on('sortstop', (event, ui) => {
        let divMoved     = ui.item;
        SORTABLE_IDX_END = divMoved.index();
        swapUrlParamsArrayIdx(SORTABLE_IDX_START, SORTABLE_IDX_END);
    });

    $('#btn_all_lp').on('click', useExistingLp);

    $('#btn_new_lp').on('click', useNewLp);
    
    $('#existingLpSelect').chosen();
    
    $('button#btn-generate-parameter').on('click', () => {
        let urlLp            = $('textarea#input-lp').val();
            URL_QUERY_STRING = getLpParams(urlLp);
        createParamsInputs(URL_QUERY_STRING['urlParamsArray']);
    });

    $('#btn-preview').on('click', preview);
});

function updateLpName() {
    let lpNameTyped = $(this).val()
    $("#lp_name").val(lpNameTyped);
}

function useNewLp() {
    let newLpName = $('#lp_name').val();
    $(this).toggle(false);
    $('#btn_all_lp').toggle();
    $('#input-lpName').val(newLpName);
    $('#hidden_use_existing_lp').val(true);
}

function useExistingLp() {
    //reset on each click
    $(this).toggle(false)
    $('#btn_new_lp').toggle();
    $('#hidden_use_existing_lp').val(false);
    $('#input-lp').val('');
    $("#parameters-space").empty();

    getAllLandingPages()
    .then( (dataLP) => {
        $("#existingLpSelect").trigger("chosen:updated");
        $('#existingLpSelect')
        .change( () => {
            let existingLpId   = $('#existingLpSelect').val();
            let selectedOption = $('#existingLpSelect').chosen().find("option:selected");
            let existingLpName = selectedOption.text();
            let existingUrlLp  = selectedOption.attr('data-offer_url');

            $('#hidden_existing_lp_id').val(existingLpId);
            $('#input-lpName').val(existingLpName);
            
            //parameter section management
            $("#parameters-space").empty();
            $('#input-lp').val(existingUrlLp);
            URL_QUERY_STRING = getLpParams(existingUrlLp);
            createParamsInputs(URL_QUERY_STRING['urlParamsArray']);
        });
    })
    .catch( (error) => {
        alert(error);
    })
}

function campaignChange(){
    $campaignName = $(this).val();

    getCampaignId($campaignName)
    .then( (data) => {
        setHiddenCampaignId(data.campaign.campaign_id);
    })
    .catch( (error) => {
        alert(error);
    })
}

function landingPageChange(params) {
    $('#existingLpSelect').chosen().change(() => {
        console.log( $(this).val() );
    })
}

function getAllLandingPages(preview) {
    let campaignId = $('#campaign_id').val();

    return new Promise( (resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ route('account-creative-getall-landingpage') }}",
            data: {
                campaignId: campaignId,
                _method   : 'POST'       //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                for (const key in data.landingPages) {
                    if (Object.hasOwnProperty.call(data.landingPages, key)) {
                        const element = data.landingPages[key]['OfferUrl'];
                        console.log(element.offer_url);

                        let option = $('<option />')
                            .text(element.name)
                            .val(element.id)
                            .attr('data-offer_url', element.offer_url);
                        ;

                        $('#existingLpSelect').append(option);
                    }
                }
                resolve(data);
            },
            error: (jqXHR, textStatus, error) => {
                reject('getAllLandingPages : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
            }
        });
    });
}

function step1() {
    // console.log('step1');
    // console.log(HTMLhtml);
    $(".step-2").slideDown('fast');
    getLandingPageName($campaignName);
    getCreativeName($campaignName);
    getTitleTag($campaignName);
    getOfferTitle($campaignName);
    getOfferProtocol($campaignName);
    
    var formData = new FormData(document.getElementById("creative_upload"));
        formData.append('campaign', $campaignName);
    creativeUpload(formData);
    $(".step-1").hide();
}

function preview(e) {
    console.log(HTML);
    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-kit-preview') }}",
        data: {
            html   : HTML,
            _method: 'POST'  //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            console.log(data);
        }
    });
}

function creativeUpload($formData) {
    $.ajax({
        type       : 'POST',
        url        : "{{ route('account-creative-upload') }}",
        data       : $formData,
        dataType   : "json",
        cache      : false,
        contentType: false,
        processData: false,
        success    : (data) => {
            /** data : {file, campaign, html, imagesS3} **/
            
            displayLoader('input-lp', false);
            // //affichage du fichier uploadé
            $('#file_path').append(data.file);

            //check lp URL
            let lpUrls = findLpURL(data.html);
            /*
                ici pour chaque lpUrl, on génère 2 inputs : checkbox + text
                l'operateur choisi la bonne url et on poursuit le traitement
                c'est la val choisie qui devient $lpUrl[0]
            */
            if (lpUrls) {
                let lpUrl  = lpUrls[0];  //by default we selected the 1st link in the array
                let indice = 0;

                lpUrls.forEach((url) => {
                    var div = $('<div class="col-1"><div class="form-check"><input class="form-check-input" type="radio" name="lp-radio" data-indice="'+indice+'" id="lpRadioId-'+indice+'"></div></div><div class="col-11"><input class="form-control" type="text" name="lp-url-'+indice+'" id="lpInputTxt-'+indice+'" value="'+url+'"></div>')
                    $('#lpUrlsHolder').append(div);
                    indice++;
                });

                $("#input-lp").val(lpUrl);
                $("#lp-url-count").html("found " + lpUrls.length);
                selectUrlLp(0);
                $("input[type=radio][name=lp-radio]").on('change', selectUrlLp);
            }
            else {
                alert('no LP found 😭 \n Manually enter the LP URL and click on "generate parameter"');
                $('button#btn-generate-parameter').show('slow');
                $('textarea#input-lp').focus();
            }
            
            // //injection du nouveau title dans le html | c'est cette action qui bug la plupart du tps en PHP
            HTML = setTitleTag(data.html, data.titleTagText);

/* uncomment to check all links + imgs 
            getHrefLink(HTML);
            getImgSrc(HTML);
*/          
            // var newHtml = '';
            if(data.imagesS3.length > 0) {
                $("#holder-input-s3links").css({'display': 'block'});
                displayS3ImgUrl(data.imagesS3);
                newHtml = replaceSrcImage(HTMLhtml, data.imagesS3);
                HTML   = newHtml;
                displayLoader('input-s3links', false);
            }
        },
        error: (jqXHR, textStatus, error) => {
            alert('creativeUpload : \n status code ' + jqXHR.status + '\n details: ' +jqXHR.responseText +  '\n textStatus;: ' + textStatus);
        }
    });
}

function getLandingPageName(campaignName){
    // console.log('getLandingPageName' + campaignName);

    takeInputValues();

    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-getlandingpagename') }}",
        data: {
            campaignName: campaignName,
            end_date    : $endDate,
            start_date  : $startDate,
            _method     : 'POST'          //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            // console.log('getLandingPageName ' + data);
            displayLoader('input-lpName', false);
            $("#input-lpName").val(data.lpName);
            setHiddenLpName(data.lpName);
            setHiddenDate(data.creativeDates);
        },
        error: (jqXHR, textStatus, error) => {
            alert('getLandingPageName : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function getCreativeName(campaignName) {
    // console.log('getCreativeName ' + campaignName);
    takeInputValues();

    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-getcreativename') }}",
        data: {
            campaignName: campaignName,
            end_date    : $endDate,
            start_date  : $startDate,
            _method     : 'POST'          //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            // console.log("getCreativeName " + data );
            displayLoader('input-creaname', false);
            $("#input-creaname").val(data.creativeName);
        },
        error: (jqXHR, textStatus, error) => {
            alert('getCreativeName : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function getTitleTag(campaignName){
    // console.log('getTitleTag ' + campaignName);
    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-gettitletag') }}",
        data: {
            campaignName: campaignName,
            _method : 'POST'      //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            // console.log("getTitleTag " + data );
            displayLoader('input-titleTag', false);
            $("#input-titleTag").val(data.titleTag);
        },
        error: (jqXHR, textStatus, error) => {
            alert('getCreativeName : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function getOfferTitle(campaignName) {
    // console.log('getOfferTitle ' + campaignName);
    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-getoffertitle') }}",
        data: {
            campaignName: campaignName,
            _method : 'POST'      //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            // console.log("getOfferTitle " + data );
            displayLoader('input-titleOffer', false);
            setCampaignNameInTitle(data.campaignName)
        },
        error: (jqXHR, textStatus, error) => {
            alert('getOfferTitle : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function getOfferProtocol(campaignName) {
    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-getofferprotocol') }}",
        data: {
            campaignName: campaignName,
            _method : 'POST'      //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            // console.log("getOfferProtocol " + data );
            $("#protocolDisplay").html('Protocol : ' + data.protocol)
            setHiddenProtocol(data.protocol);
        },
        error: (jqXHR, textStatus, error) => {
            alert('getOfferProtocol : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function swapUrlParamsArrayIdx(idxStart, idxEnd) {
    let paramMoved  = URL_QUERY_STRING['urlParamsArray'][idxStart];
    let paramToSwap = URL_QUERY_STRING['urlParamsArray'][idxEnd]
    
    URL_QUERY_STRING['urlParamsArray'][idxStart] = paramToSwap;
    URL_QUERY_STRING['urlParamsArray'][idxEnd]   = paramMoved
    
    $("#parameters-space").empty();
    createParamsInputs( URL_QUERY_STRING['urlParamsArray']);
    reconstructUrl ();
}

function selectUrlLp(index = 0) {
    let indice           = (index == 0) ? index : $(this).data('indice');
    let lpUrl            = $("#lpInputTxt-" + indice).val();
        URL_QUERY_STRING = getLpParams(lpUrl);

    $("#parameters-space").empty();
    createParamsInputs(URL_QUERY_STRING['urlParamsArray']);
    $("#input-lp").val(lpUrl);
}

function getLpParams(lp) {

    let   urlParameters     = [];
    let   urlSegments       = lp.split("?");
    let   baseUrl           = urlSegments.shift();                     // we remove protocol + subdomain + domain principal from url so urlSegments will only keep query string
    let   urlSegmentsString = urlSegments.join('');                    // transform array to str
    const urlParams         = new URLSearchParams(urlSegmentsString);

    urlParameters['baseUrl']         = baseUrl;
    urlParameters['urlParamsArray']  = urlSegmentsString.split("&");
    urlParameters['URLSearchParams'] = urlParams;

    NEW_INPUT_INDEX =  urlParameters['urlParamsArray'].length + 1;

    return urlParameters;
}

/**
 * replaceInLp changes querystring's key or value 
 * onKeyUp in URL_QUERY_STRING['urlParamsArray'] 
 * and reinject the complete URL in the #input-lp
 * 
 * 
 * @param int indexKeyOrValue : 0 if we wanna change querystring key's | 1 if we wanna change querystring value's
 * @param mixed	value : typed value 
 * @param mixed	indexParamsArray : index of the parameter to change in URL_QUERY_STRING['urlParamsArray'] 
 * @return	void
 */
 function replaceInLp(indexKeyOrValue, value, indexParamsArray) {
    let paramToChange                                    = URL_QUERY_STRING['urlParamsArray'][indexParamsArray];  // we get the parameter to change
    let keyValue                                         = paramToChange.split("=");                              // we split key=value to retieve the former value
    keyValue[indexKeyOrValue]                            = value;                                                 // we change/create(if empty) of formerValue with value
    URL_QUERY_STRING['urlParamsArray'][indexParamsArray] = keyValue.join("=");                                    // we reinject the key=newValue in the globalUrlParameter as a str
    
    reconstructUrl();//update #input-lp.val with complete URL
}

function createParamsInputs(urlParams) {
    let i = 0; // we create an index so we know in URL_QUERY_STRING['urlParamsArray'] which entry need to be modified

    urlParams.forEach(function(urlParamsArray) {
        let keyValue          = urlParamsArray.split("=");
        let key               = keyValue[0];
        let value             = keyValue[1];

        let btnDelete             = '<button onClick="deleteNewParam(this)" id="btn-'+ i +'" type="button" class="btn btn-close btn-danger" aria-label="Close">&#10006</button>';
        var parametersOptions = '<option value="' + value + '">Initial</option><option value="{transaction_id}">{transaction_id}</option><option value="{aff_sub}">{aff_sub}</option><option value="{aff_sub3}">{aff_sub3}</option><option value="{aff_sub4}">{aff_sub4}</option><option value="{affiliate_id}">{affiliate_id}</option>';
        var newElement        = $('<div id="param-holder-'+ i +'" class="param--holder-drag d-flex flex-row d-flex align-items-center mt-2"><div class="w-50"><label style="padding-left:7px;" for="' + key + '-param" class="w-100">Value for <b>' + key + '</b></label></div><input data-indice="'+i+'" onKeyUp="reConstructLp(this)" type="text" name="' + key + '-param" placeholder="Value" value="' + value + '" id="key' + i + '-param-value" class="p-2 mx-2 w-30"><select data-indice_select="'+i+'" id="' + key + '-select" onChange="selectPreMadeParams(this)" class="form-select w-20 p-2">' + parametersOptions + '</select>'+btnDelete+'</div>');
        $("#parameters-space").append(newElement);
        i++;
    });
}

function createNewParamsInputs() {
    var newElement;
    var paramValeur = "value"+NEW_INPUT_INDEX;
    var paramKey    = "parameter"+NEW_INPUT_INDEX;

    URL_QUERY_STRING['urlParamsArray'].push(paramKey + "=" + paramValeur); // add new entry to globalArray
    
    let index = NEW_INPUT_INDEX - 1;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          // we remove 1 as we're working with indexes
    let btnDelete             = '<button onClick="deleteNewParam(this)" id="btn-'+ index +'" type="button" class="btn btn-close btn-danger" aria-label="Close">&#10006</button>';
    var thisParametersOptions = '<option value="' + paramValeur + '">Initial</option><option value="{transaction_id}">{transaction_id}</option><option value="{aff_sub}">{aff_sub}</option><option value="{aff_sub3}">{aff_sub3}</option><option value="{aff_sub4}">{aff_sub4}</option><option value="{affiliate_id}">{affiliate_id}</option>';
    var newParam              = $('<div data-id_parent="'+ index +'"  class="param--holder-drag d-flex flex-row d-flex align-items-center mt-2"><div class="w-50"><input data-indice_key="'+index+'" onKeyUp="reConstructLpNewKey(this)" type="text" name="' + paramKey + '-param" placeholder="Value" value="' + paramKey + '" id="' + index + '-param" class="p-2 mx-2 w-30"></div><input  data-indice ="'+index+'" onKeyUp="reConstructLp(this)" type="text" name="&' + paramKey + '-param" placeholder="Value" value="' + paramValeur + '" id="key' + index + '-param-value" class="p-2 mx-2 w-30"><select  data-indice_select="'+index+'" id="' + index + '-select" onChange="selectPreMadeParams(this)" name="' + paramKey + '-param" class="form-select w-20 p-2">' + thisParametersOptions + '</select>'+ btnDelete +'</div>');

    $("#parameters-space").append(newParam);

    NEW_INPUT_INDEX++;
    reconstructUrl();
}

function conditionalChaining(form, useNewLp, requiredFields) {

    if(useNewLp == "true") {
        createLandingPage(form)
        .then( (dataLandingPage) => {
            $("#lp_test").html(dataLandingPage.lp_paired_name);
            
            creativeInstall(form, CLEAN_HTML_CODE)
            .then( (dataInstalled) => {
                setDownloadUrl(dataInstalled);

                getTrackingUrl(requiredFields.creaId)
                .then( (trackingUrl) => {
                    // console.log(trackingUrl);
                    let kitTestHtml = replaceHrefLink(dataInstalled.response_creative.code, trackingUrl);

                    getKitTest(kitTestHtml, requiredFields, dataInstalled);
                })
                .catch( (error) => {
                    alert(error);
                })
            })
            .catch( (error) => {
                alert(error);
            })
        })
        .catch( (error) => {
            alert(error);
        })
    } else {
        creativeInstall(form, CLEAN_HTML_CODE)
        .then( (dataInstalled) => {
            // console.log(dataInstalled.response_creative.code);

            setDownloadUrl(dataInstalled);

            getTrackingUrl(requiredFields.creaId)
            .then( (trackingUrl) => {
                // console.log(trackingUrl);
                let kitTestHtml = replaceHrefLink(dataInstalled.response_creative.code, trackingUrl);
                
                getKitTest(kitTestHtml, requiredFields, dataInstalled);
            })
            .catch( (error) => {
                alert(error);
            })
        })
        .catch( (error) => {
            alert(error);
        })
    }
}

function step2() {

    let requiredFields = {
        file         : $('#file_path').html(),
        campaignId   : $('#campaign_id').val(),
        startDate    : $('#hidden_start_date').val(),
        endDate      : $('#hidden_end_date').val(),
        titleTag     : $('#input-titleTag').val(),
        creaName     : $('#input-creaname').val(),
        lpUrl        : $('#input-lp').val(),
        lpName       : $('#lp_name').val(),
        protocol     : $('#protocol').val(),
        useExistingLp: $('#hidden_use_existing_lp').val(),
        existingLpId : $('#hidden_existing_lp_id').val()
    };

    if(isTransactionIdOk()) {
        requiredFields.code   = HTML,
        requiredFields.length = HTML.length,

        creativeCreate(requiredFields, HTML)
        .then( (creative) => {
            $('#crea_id').val(creative.id)
            
            requiredFields.creaId = creative.id;
            CLEAN_HTML_CODE       = creative.code;
            
            var formDataValidation = new FormData(document.getElementById("creative_install"));

            formDataValidation.append('validated_lp', requiredFields.lpUrl);
            formDataValidation.append('lp_name', requiredFields.lpName);
            formDataValidation.append('validated_title', requiredFields.creaName);
            formDataValidation.append('campaign_id', requiredFields.campaignId);
            formDataValidation.append('crea_id', requiredFields.creaId);
            formDataValidation.append('protocol', requiredFields.protocol);
            formDataValidation.append('useExistingLp', requiredFields.useExistingLp);
            formDataValidation.append('existingLpId', requiredFields.existingLpId);
            
            let useNewLp =  $('#hidden_use_existing_lp').val();
            conditionalChaining(formDataValidation, useNewLp, requiredFields)
        })
        .catch( (error) => {
            alert(error);
        })
    }else{
        $("#input-lp").focus();
        alert("{transaction_id} parameter value is missing in your URL");
    }
}

function createLandingPage(formData) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type       : 'POST',
            url        : "{{ route('account-lp-create') }}",
            data       : formData,
            dataType   : "json",
            cache      : false,
            contentType: false,
            processData: false,
            success    : (data) => {
                /* data {success, response_lp, lp_paired_name } */
                resolve(data);
            },
            error: (jqXHR, textStatus, error) => {
                reject('creativeInstall : \nstatus code ' + jqXHR.status + ' \ndetails: ' +jqXHR.responseText);
            }
        });
    });
}

function creativeInstall(formDataValidation, htmlCode) {

    let htmlWithReplacedLink = replaceHrefLink(htmlCode, TRACKING_LINK_GENERIC);
    formDataValidation.append('html', htmlWithReplacedLink);

    return new Promise((resolve, reject) => {
        $.ajax({
            type       : 'POST',
            url        : "{{ route('account-creative-install') }}",
            data       : formDataValidation,
            dataType   : "json",
            cache      : false,
            contentType: false,
            processData: false,
            success    : (data) => {
                /* data {response_creative, response_lp, url_kit_test, lp_paired, lp_paired_name, crea_paired, crea_paired_name} */
                resolve(data);
            },
            error: (jqXHR, textStatus, error) => {
                reject('creativeInstall : \nstatus code ' + jqXHR.status + ' \ndetails: ' +jqXHR.responseText);
            }
        });
    });
}

function setDownloadUrl(params) {
    $("#kit_url").prop("href", params.download_url);
    $("#kit_url").attr("href", params.download_url);
    $("#kit_url").attr("download", params.crea_paired_name);
}

function getKitTest(html, requiredFields, dataFromCreaInstall) {
    $.ajax({
        type: 'POST',
        url : "{{ route('account-creative-getkittest') }}",
        data: {
            html    : html,
            crea_id : requiredFields.creaId,
            creaname: requiredFields.validated_title,
            _method : 'POST' //we need this so we can send string data to server
        },
        dataType: "json",
        success : (data) => {
            /* data  :(url_kit_test, lp_paired, crea_paired, crea_paired_name, download_url, visualize_url) */
            // console.log(data);
            displayKitTest(data, dataFromCreaInstall);
        
        },
        error: (jqXHR, textStatus, error) => {
            alert('getKitTest : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
        }
    }); 
}

function displayKitTest(dataFromKitTest, dataFromCreaInstall) {
    /* dataFromKitTest {
        success
        response_creative,  // creative with complete html code / replaced link by {tracking_link}
        response_lp,        // creative from LandingPage::createLpOnOffer
        lp_paired_name,     // $lpName
        visualize_url
    } */

    $(".step-2").slideUp('fast');
    $(".step-3").slideDown(2000);

    $("#kit_visualize").prop("href", dataFromKitTest.visualize_url);
    $("#kit_visualize").attr("href", dataFromKitTest.visualize_url);
    $("#btn_download_kittest").prop("href", dataFromKitTest.downloadTestRoute);
    $("#btn_download_kittest").attr("href", dataFromKitTest.downloadTestRoute);

    $("#crea_test").html($("#input-creaname").val());
}

function getCampaignId(campaignName) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ route('account-creative-getcampaignid') }}",
            data: {
                campaignName: campaignName,
                _method     : 'POST'          //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                resolve(data);
            },
            error: (jqXHR, textStatus, error) => {
                reject('getCampaignId : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
            }
        }); 
    });
}

function getTrackingUrl(creativeId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ route('account-creative-gettrackingurl') }}",
            data: {
                creaId: creativeId,
                _method : 'POST'      //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                // console.log("getTrackingUrl " +   data.trackingUrl );
                resolve( data.trackingUrl );
                // $("#protocolDisplay").html('Protocol : ' + data.trackingUrl)
            },
            error: (jqXHR, textStatus, error) => {
                reject('getTrackingUrl : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
            }
        }); 
    });
}

function setOfferDate() {
    let field        = $(this).data('date-type');
    let inputLp      = $("#input-lpName");
    let lpNameBase   = inputLp.val();
    let inputCrea    = $("#input-creaname");
    let creaNameBase = inputCrea.val();
    let typed        = $(this).val();
    
    switch (field) {
        case 'start':
            inputLp.val(lpName + typed + "_au");
            inputCrea.val(creaName + typed + "_au");
            break;

        case 'end':
            lpName   = inputLp.val();
            creaName = inputCrea.val();
            inputLp.val(lpName + typed + "_pending");
            inputCrea.val(creaName + typed + "_pending.html");
            break;
    }
}

/**
* not used -> url need to be created if needed 
*/
function getCreative($creaId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ url('account-creative-get') }}",
            data: {
                htmlCode: $creaId,
                _method : 'POST'      //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                resolve(data.creative);
            },
            error: (jqXHR, textStatus, error) => {
                reject('creativeCleanCode : \nstatus code '+jqXHR.status + '\n details: ' + jqXHR.responseText);
            }
        }); 
    });
}

function creativeCleanCode(htmlCode) {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ route('account-creative-clean-code') }}",
            data: {
                htmlCode: htmlCode,
                _method : 'POST'      //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                resolve(data.html);
            },
            error: (jqXHR, textStatus, error) => {
                reject('creativeCleanCode : \nstatus code ' + jqXHR.status + ' \n details: ' + jqXHR.responseText);
            }
        }); 
    });
}

function creativeCreate(requiredFields, cleanHtml) {
     /* requiredFields : {file, campaign, creativeDates, html, lpName, titleText} */

    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url : "{{ route('account-creative-create') }}",
            data: {
                filepath      : requiredFields.file,
                code          : cleanHtml,
                title         : requiredFields.titleTag,
                length        : cleanHtml.length,
                campaignNumber: requiredFields.campaignId,
                offerUrl      : requiredFields.lpUrl,
                startDate     : requiredFields.startDate,
                endDate       : requiredFields.endDate,
                creaName      : requiredFields.creaName,
                _method       : 'POST'                      //we need this so we can send string data to server
            },
            dataType: "json",
            success : (data) => {
                /** data {creative} **/
                resolve(data.creative);
            },
            error : (jqXHR, textStatus, error) => {
                reject('creativeCreate : \nstatus code ' + jqXHR.status + '\n details: ' + jqXHR.responseText);
            }
        }); 
    });
}

function takeInputValues() {
    try {
        $startDate = $('#start_date').val();
        $endDate   = $('#end_date').val();
    } catch(e) {
        console.log("Input selection problem");
    }
};

function getValidationElements() {
    validatedLp         = $('#input-lp').val();
    lpName              = $('#lp_name').text();
    validatedTitle      = $('#input-creaname').val(); //crea_name
    validatedCampaignId = $('#campaign_id').text();
    creaId              = $('#crea_id').text();
    protocol            = $('#protocol').text();
}

function displayS3ImgUrl($s3ImgUrls) {
    $s3ImgUrls.forEach( url => {
        $("#input-s3-links-holder").append("<p>" + url + "</p>");
    });
}

function replaceSrcImage(htmlCode, $imgUrls) {
    const cheerio = cheerioLib.load(htmlCode)
    let   imgs    = cheerio('img');

    $imgUrls.forEach($s3ImgUrl => {
        for (let index = 0 ; index <= imgs.length -1 ; index++) {
            let img       = imgs[index];
            let src       = img.attribs.src;
            let inHtmlImg = src.split("/").pop(); // get last array element which is filename
            let s3Img     = $s3ImgUrl.split("/").pop();

            if(inHtmlImg == s3Img) { // we need to check if filenames match before changing URL
                img.attribs.src = $s3ImgUrl;
            }
        }
    });
    // console.log('replaceSrcImage \n' + cheerio.html());

    return cheerio.html();
}

function replaceHrefLink(htmlCode, $url) {
    const cheerio = cheerioLib.load(htmlCode);
    let   links   = cheerio('a');
    // console.log(links);

    for (let i = 0 ; i <= links.length -1 ; i++) {
        let link          = links[i];
        link.attribs.href = $url;
    }

    return cheerio.html();
}

function isTransactionIdOk() {
    protocol = $('#protocol').val();

    if(protocol == "server" && findTransactionId()) {  
        return true;
    } else if (protocol == "https_img") {
        return true;
    }
    
    return false;
}

function findTransactionId() {
    lpAnalyse         = $("#input-lp").val();
    searchTransaction = "{transaction_id}";

    var tidPosition = lpAnalyse.indexOf(searchTransaction);

    if(tidPosition > 1) {
        return true;
    }
    
    return false;
}

function findLpURL(htmlCode) {
    const cheerio      = cheerioLib.load(htmlCode);
    let   links        = cheerio('a');
    let   linkReturned = false;
    let   ahrefs       = [];

    //looking for URL with parameters | use for loop to make return
    for (let index = 0 ; index <= links.length -1 ; index++) {
        let link = links[index].attribs.href;
        
        if (undefined !== link && link.indexOf("?") > - 1) {
            ahrefs.push(link);
        }
    }
    
    return (ahrefs.length > 0) ? ahrefs : false;
}

function getHrefLink(htmlCode) {
    const cheerio = cheerioLib.load(htmlCode)
    let   links   = cheerio('a');
    let   href    = []
    
    $("#links-count").html(links.length);
    //looking for URL with parameters | use for loop to make return
    for (let index = 0 ; index <= links.length -1 ; index++) {
        let link  = links[index].attribs.href;
        let input = $('<input id="input-crea-link-'+index+'" class="form-control form-control-sm w-100" type="text" value="'+link+'" />');
        $("#links-holder").append(input);
    }
}

function getImgSrc(htmlCode) {
    const cheerio = cheerioLib.load(htmlCode)
    let   imgs    = cheerio('img');

    $("#imgs-count").html(imgs.length);

    for (let index = 0 ; index <= imgs.length -1 ; index++) {
        let src   = imgs[index].attribs.src;
        let input = $('<input id="input-img-src-'+index+'" class="form-control form-control-sm w-100" type="text" value="'+src+'" />');
        $("#imgs-holder").append(input);
    }
}

function setTitleTag(htmlCode, $titleText) {
    const cheerio = cheerioLib.load(htmlCode);
    let   title   = cheerio('title');

    if(title.length < 1) { // no title tag in htmlCode
        cheerio('head').append('<title></title>');
        title = cheerio('title');
    }
    
    title.text($titleText);
    
    return cheerio.html();
}

function setCampaignNameInTitle(name) {
    $('.set_campaign_name').html(name);
}

function setHiddenCampaignId(idc) {
    $('#campaign_id').val(idc);
}

function setHiddenCreaId(idcrea) {
    $('#crea_id').val(idcrea); // hidden input with crea->id
}

function setHiddenLpName(lpName) {
    $('#lp_name').val(lpName); //hidden input with lp name
}

function setHiddenProtocol(protocol) {
    $('#protocol').val(protocol); // hidden in put with protocol
}

function setHiddenDate(creativeDates) {
    $('#hidden_start_date').val(creativeDates.start_date);
    $('#hidden_end_date').val(creativeDates.end_date);
}

function init_steps() {
    $(".step-2").fadeOut("fast");
    $(".step-3").fadeOut("fast");
}

function reconstructUrl () {
    $("#input-lp").val(URL_QUERY_STRING['baseUrl'] + "?" +  URL_QUERY_STRING['urlParamsArray'].join("&"));
}

// Management & reconstuction from mapping writed for LP before submitting
function reConstructLp(arg) {
    let valueIndex = 1; // we set to 1 to say that we want to change VALUE of the queryString
    replaceInLp(valueIndex, arg.value, arg.dataset.indice);
}

function updateParamInput(inputIndex, value) {
    $("#key" + inputIndex + "-param-value").val(value);
}

/**
 * reConstructLpNewKey updates the value of key's queryString
 * @param	mixed	arg	
 * @return	void
 */
 function reConstructLpNewKey(arg) {
    let keyIndex = 0; // we set to 0 to say that we want to change KEY of the queryString
    replaceInLp(keyIndex, arg.value, arg.dataset.indice_key);
}

function selectPreMadeParams(arg) {
    let valueIndex = 1; // we set to 1 to say that we want to change VALUE of the queryString
    replaceInLp(valueIndex, arg.value, arg.dataset.indice_select);
    updateParamInput(arg.dataset.indice_select, arg.value);
}

function deleteNewParam(arg) {
    let divToRemove   = arg.parentNode;
    let indexToRemove = divToRemove.dataset.id_parent;
    divToRemove.remove();
    NEW_INPUT_INDEX --;
    URL_QUERY_STRING['urlParamsArray'].splice(indexToRemove, 1);
    
    reconstructUrl();
}

function displayLoader($inputId, $show) {
    let spinner = $("#holder-"+ $inputId +" .loader-spin");
    let input = $(".loader-spin + div.col-11");

    if($show) {
        spinner.show();
        input.attr('class', 'col-11');
    }else{
        spinner.hide();
        input.attr('class', 'col-12');
    }
}

function removeBaseUrl(url) {
    urlSplitted = url.split("?");

    if(urlSplitted[1]) {
        baseUrl = urlSplitted[0];
        return urlSplitted[1];
    }
    
    return url;
}

//dropzone system
$(function() {
	  
  	$('#dropzone').on('dragover', function() {
		$(this).addClass('hover');
	});
	  
  	$('#dropzone').on('dragleave', function() {
		$(this).removeClass('hover');
	});
	  
  	$('#dropzone input').on('change', function(e) {
		var file = this.files[0];

		$('#dropzone').removeClass('hover');

		if (this.accept && $.inArray(file.type, this.accept.split(/, ?/)) == -1) {
			return alert('File type not allowed.');
		}

		$('#dropzone').addClass('dropped');
        $('#dropzone div').css({fontSize: 10, color: '#444'});
        $('#dropzone div').html(file.name);
	});
});

function delay(callback, ms) {
    var timer = 0;

    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

</script>
@endpush