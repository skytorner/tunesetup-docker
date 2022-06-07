@extends('account.account_layout')

@section('content-account')

@section('h1')
Blacklist > New
@endsection

{{-- @include('account.partial.nav.blacklists-nav') --}}

<div class="card">
    <div class="card-header">
        {{ __('Blacklists Management') }}
    </div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="row mt-3 step-1">
            <div class="col-12 col-sm-12 col-lg-12">
                <h4>Upload new blacklist</h4>
                <form method="POST" enctype="multipart/form-data" id="creative_upload" action="{{ route('blacklist-upload') }}" >
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div id="dropzone">
                                                <div>DRAG N DROP HERE (TXT, CSV, XLS, XLSX) </div>
                                                <input type="file" name="file" placeholder="Choose File" id="file">
                                            </div>
                                        <span class="text-danger">{{ $errors->first('blacklistUploaded') }}</span>
                                        </div>
                                    </div>
                                </div>
                                

                                @if(!empty($errors))
                                <span class="text-danger">
                                    <ul>
                                    @foreach ($errors->all() as $errors)
                                        <li>{{ $errors }}</li>
                                    @endforeach
                                    </ul>
                                </span>  
                                @endif

                                @if(session('success'))
                                <span class="text-success">
                                    <ul>
                                        <li>{{ session('success') }}</li>
                                    </ul>
                                </span>  
                                @endif
                            </div>
                            
                            <label for="link-type1" class="form-check-label">Blacklist by</label>

                            <div class="form-check">
                                <input name="bl-link-type" value="advertiser" type="radio" class="form-check-input" id="link-type1">
                                <label for="link-type1" class="form-check-label">Advertiser</label>
                            </div>
                            <div class="form-check">
                                <input name="bl-link-type" value="campaign" type="radio" class="form-check-input" id="link-type2">
                                <label for="link-type2" class="form-check-label">Campaign</label>
                            </div>
                        </div>
                    </div>
                    <div id="block-campaign" class="row link-type-selector">
                        <div class="col-md-12">
                            <select id="campaignSelector" name="campaign[]" class="form-select form-select-md mb-3" aria-label="Campaigns select" data-placeholder="Select Your Campaign(s)" multiple>
                                @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->campaign_id }}">{{ $campaign->campaign_id }} : {{ $campaign->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr/>
                    </div>
                    
                    <div id="block-advertiser" class="row link-type-selector">
                        <div class="col-md-12">
                            <select id="advertiserSelector" name="advertiser" class="form-select form-select-md mb-3" aria-label="Campaigns select" data-placeholder="Select an Advertiser">
                                <option value></option>
                                @foreach($advertisers as $advertiser)
                                <option value="{{ $advertiser->adv_id }}">{{ $advertiser->company }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr/>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function()    {
    // chosen-js init
    $("#campaignSelector").chosen();
    $("#advertiserSelector").chosen();

    $("input[name=bl-link-type]").on('change', displaySelector);
});

function displaySelector() {
    let typeLink = $(this).val()

    switch (typeLink) {
        case 'advertiser' :
            $("#block-advertiser").toggle('slow');
            $("#block-campaign").toggle(false);
            $("#campaignSelector").val('').trigger("chosen:updated");
            break;
        case 'campaign' :
            $("#block-advertiser").toggle(false);
            $("#block-campaign").toggle('slow');
            $("#advertiserSelector").val('').trigger("chosen:updated");
            break;
        default:
            $("#block-campaign").toggle(false);
            $("#block-advertiser").toggle(false);
            $("#advertiserSelector").val('').trigger("chosen:updated");
            $("#campaignSelector").val('').trigger("chosen:updated");
    }
}

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
</script>
@endpush
