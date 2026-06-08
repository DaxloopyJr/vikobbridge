 {{-- Settings Colored Tab Navigation --}}
<ul class="nav nav-tabs-colored mb-4">
    <li class="nav-item">
        <a class="nav-link tab-green {{ request()->routeIs('settings.collection_funds*') ? 'active' : '' }}" href="{{ route('settings.collection_funds') }}">
            <i class="bi bi-wallet2 me-1"></i>Collection Funds
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-blue {{ request()->routeIs('settings.calendar_years*') ? 'active' : '' }}" href="{{ route('settings.calendar_years') }}">
            <i class="bi bi-calendar3 me-1"></i>Calendar Years
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-purple {{ request()->routeIs('settings.loan_types*') ? 'active' : '' }}" href="{{ route('settings.loan_types') }}">
            <i class="bi bi-percent me-1"></i>Loan Types
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-orange {{ request()->routeIs('settings.withdrawal*') ? 'active' : '' }}" href="{{ route('settings.withdrawal') }}">
            <i class="bi bi-box-arrow-left me-1"></i>Hisa Withdrawal
        </a>
    </li>
</ul>
