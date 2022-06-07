<div class="sidebar-sticky pt-3">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('account') }}">Kit Setup</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('blacklist') }}">BlackLists</a>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('blacklists-list') }}" class="nav-link">{{ __('BlackLists list') }}</a></li>
                <li class="nav-item"><a href="{{ route('blacklist') }}" class="nav-link">{{ __('Add new blacklist') }}</a></li>
            </ul>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('account') }}">Logs</a>
        </li> --}}
    </ul>
</div>