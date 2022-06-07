@extends('account.account_layout')

@section('content-account')
@section('h1')
Blacklist > Edit blacklist n° {{ $blacklist->id}}
@endsection
{{-- @include('account.partial.nav.blacklists-nav') --}}

<div class="card">
    <div class="card-header text-center">
        <h3>BlackList {{ $blacklist->id }} </h3>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row mt-3 step-1">
            <div class="col-12 col-sm-12 col-lg-12">
               
                <dl class="row">
                    <dt class="col-sm-3">Filename : </dt>
                    <dd class="col-sm-9">
                        <a title="download in .CSV" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'format' => 'csv']) }}" class=""> 
                            {{  $blacklist->filename }}
                        </a>
                    </dd>
                    
                    <dt class="col-sm-3">Format : </dt> 
                    <dd class="col-sm-9">{{ $blacklist->header }}</dd>

                    <dt class="col-sm-3">Number of lines : </dt> 
                    <dd id="dd-nbrLines" class="col-sm-9">{{ $blacklist->nb_lines }}</dd>
                            
                    @if (null !== $blacklist->advertiser)
                    <dt class="col-sm-3">Advertiser : </dt>
                    <dd class="col-sm-9">{{ $blacklist->advertiser->company }}</dd>
                    @endif
                    @if ($campaigns->isNotEmpty())
                    <dt class="col-sm-3">Campaigns associated with</dt>
                    <dd class="col-sm-9">
                        @foreach ($campaigns as $campaign)
                        <p class="col-sm-9"> {{ $campaign->name }}</p>
                        @endforeach
                    </dd>
                    @endif
                </dl>
                
                <div class="row mt-3">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <form method="POST" enctype="multipart/form-data" id="modify-blacklist" action="{{ route('blacklist-modify', ['id' => $blacklist->id]) }}" >
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <textarea name="adress" id="txtarea-emails" placeholder="emails list" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <button id="btn-submit" class="btn btn-primary">Add</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <div id="dropzone">
                                            <div>DRAG N DROP HERE (TXT, CSV, XLS, XLSX) </div>
                                            <input type="file" name="file" placeholder="Choose File" id="file">
                                        </div>
                                    </div>
                                </div>
                            </div>     
                            <input type="hidden" class="hidden" id="hidden-blacklist-id" name="blacklist_id" value="{{ $blacklist->id }}">
                        </form>
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

                <div class="row justify-content-md-center">
                    @php
                        if ($blacklist->header == "email") {
                            $encryptionsDefault = $encryptionsType;
                        } else {
                            $encryptions = explode(',', $blacklist->header);
                        }
                    @endphp

                    @if (isset($encryptions))
                        @foreach ($encryptions as $encryption)
                            @if (!empty($encryption) && $encryption !== 'email')
                            <div class="col-md-auto">
                                <a title="download in .TXT" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'encryption' => $encryption]) }}" class="btn btn-success"><i class="bi bi-download"></i> {{ $encryption }}</a>
                            </div>
                            @endif
                        @endforeach
                    @endif
                    
                    @if(isset($encryptionsDefault))
                        @foreach ($encryptionsDefault as $encryption => $key )
                            @if (!empty($encryption) && $encryption !== 'email')
                            <div class="col-md-auto">
                                <a title="download in .TXT" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'encryption' => $encryption]) }}" class="btn btn-success"><i class="bi bi-download"></i> {{ $encryption }}</a>
                            </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

$(document).ajaxStart(function() {
    $(document.body).css({'cursor' : 'wait'});
}).ajaxStop(function() {
    $(document.body).css({'cursor' : 'default'});
});

$(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('#btn-submit').on('click', (e) => {
        e.preventDefault();

        let emails      = $("#txtarea-emails").val().trim().split('\n');
        let blacklistId = $("#hidden-blacklist-id").val();
        let promises    = [];

        emails.forEach( email => {
            let request = $.ajax({
                type : "POST",
                url  : "{{ route('blacklist-add-emails-chunk') }}",
                data : {
                    id     : blacklistId,
                    address: email,
                    _method: 'POST'
                },
                dataType: 'json',
                success : (data) => {
                    $("#dd-nbrLines").text(data.blacklist.nb_lines)
                    console.log("return", data.blacklist, data.address);
                },
                error: (jqxhr, textStatus, error) => {
                    alert('blacklist edit: \nstatus code' + jqxhr.status + '\n details : ' + jqxhr.responseText )
                }
            });

            promises.push(request);
        });

        $.when.apply(null, promises).done(() => {
            $("form#modify-blacklist").submit();
            console.log('done');
        });
    });
    
    //dropzone system
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
