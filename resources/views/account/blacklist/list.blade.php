@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush
@extends('account.account_layout')

@section('content-account')

@section('h1')
Blacklist > List
@endsection

{{-- @include('account.partial.nav.blacklists-nav') --}}

<div class="card">
    <div class="card-header">
        {{ __('Blacklists') }}
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <h4>Actives</h4>
                <table id="blackList" class="header-fixed table table-striped">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>adv</th>
                            <th>Campaign id</th>
                            <th>Nbr Lines</th>
                            <th>Link</th>
                            <th>Encryption</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blacklists as $blacklist)
                        @php
                            unset($encryptionsDefault);
                            unset($encryptions);
                            if ($blacklist->header == "email") {
                                $encryptionsDefault = $encryptionsType;
                            } else {
                                $encryptions = explode(',', $blacklist->header);
                            }
                        @endphp
                        <tr>
                            <td>{{ $blacklist->id }}</td>
                            <td>{{ (null !== $blacklist->advertiser) ? $blacklist->advertiser->company : '-'  }}</td>
                            <td>{{ Str::of(implode(', ',$blacklist->campaigns_id))->limit(12) }}</td>
                            <td>{{ $blacklist->nb_lines }}</td>
                            <td>
                                <a class="copyUrl" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'format' => File::extension($blacklist->filename)]) }}" target="_blank">
                                    <i class="bi bi-share-fill"></i>
                                </a>
                            </td>
                            <td>{{ $blacklist->header }}</td>
                            <td>
                                <a onclick="return confirm('delete blacklist with ID {{ $blacklist->id}}?')" href="{{ route('blacklist-delete', ['id' => $blacklist->id]) }}" class="btn btn-danger">Delete</a>
                                <a href="{{ route('blacklist-edit', ['id' => $blacklist->id]) }}" class="btn btn-primary">Edit</a>
                                @if (isset($encryptions))
                                    @foreach ($encryptions as $encryption)
                                        @if (!empty($encryption) && $encryption !== 'email')
                                            <a title="download in .TXT" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'encryption' => $encryption]) }}" class="btn btn-success btn-encrypt-ddl"><i class="bi bi-download"></i>  {{ $encryption }}</a>
                                        @endif
                                    @endforeach
                                @endif
                                
                                @if(isset($encryptionsDefault))
                                    @foreach ($encryptionsDefault as $encryption => $key )
                                        @if (!empty($encryption) && $encryption !== 'email')
                                            <a title="download in .TXT" href="{{ route('blacklist-download', ['filename' => $blacklist->filename, 'encryption' => $encryption]) }}" class="btn btn-success btn-encrypt-ddl"><i class="bi bi-download"></i> {{ $encryption }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function()    {
    // chosen-js init
  $('.copyUrl').on('click', copyUrl);
  $('#blackList').DataTable();
});

function copyUrl(e) {
    e.preventDefault();
    let copyTxt = $(this).attr('href');
    navigator.clipboard.writeText(copyTxt).then( () => {
        alert("Downurl copied !\n " + copyTxt);
    }).catch( () => {
        alert("couldnt copy url !\n " + copyTxt);
    });
    
}
</script>
@endpush