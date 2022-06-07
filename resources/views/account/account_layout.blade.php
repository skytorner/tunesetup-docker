@extends('layouts.app')

@section('content')

<nav id="sidebarMenu" class="col-md-1 col-lg-1 d-md-block bg-light sidebar collapse">
    @include('account.partial.nav.account-nav')
</nav>

<main role="main" class="col-md-11 ml-sm-auto col-lg-11 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Account > @yield('h1')</h1>
    </div>
    @yield('content-account')
</main>
@endsection