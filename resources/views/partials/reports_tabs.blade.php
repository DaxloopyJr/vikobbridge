{{-- Reports Colored Tab Navigation --}}
<ul class="nav nav-tabs-colored mb-4">
    <li class="nav-item">
        <a class="nav-link tab-green {{ request()->routeIs('reports.collections') ? 'active' : '' }}" href="{{ route('reports.collections') }}">
            <i class="bi bi-graph-up me-1"></i>Collections
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-blue {{ request()->routeIs('reports.loans') ? 'active' : '' }}" href="{{ route('reports.loans') }}">
            <i class="bi bi-file-text me-1"></i>Loans
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-orange {{ request()->routeIs('reports.year-end') ? 'active' : '' }}" href="{{ route('reports.year-end') }}">
            <i class="bi bi-calendar-check me-1"></i>Year-End
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-red {{ request()->routeIs('reports.expenditure') ? 'active' : '' }}" href="{{ route('reports.expenditure') }}">
            <i class="bi bi-receipt-cutoff me-1"></i>Expenditure
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-purple {{ request()->routeIs('reports.financial-statement') ? 'active' : '' }}" href="{{ route('reports.financial-statement') }}">
            <i class="bi bi-balance-scale me-1"></i>Financial
        </a>
    </li>
</ul>
