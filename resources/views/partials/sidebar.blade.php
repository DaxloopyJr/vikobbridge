<div class="sidebar" id="sidebar">
    {{-- Logo Area --}}
    <div class="logo-area">
        <div class="logo-icon">
            <i class="bi bi-bank2"></i>
        </div>
        <span class="logo-text">VICOBRIDGE</span>
    </div>

    {{-- User Mini Profile --}}
    @auth
    @php
        $groupRoleName = $authUser->groupRoleName();
        $isAdmin = $authUser->isSuperAdmin();
    @endphp
    <div class="sidebar-user">
        <div class="user-avatar">
            @if($authUser->profile_picture)
                <img src="{{ asset('storage/' . $authUser->profile_picture) }}" alt="{{ $authUser->full_name }}">
            @else
                <span class="avatar-initials">{{ strtoupper(substr($authUser->first_name, 0, 1) . substr($authUser->last_name, 0, 1)) }}</span>
            @endif
        </div>
        <div class="user-info">
            <span class="user-name">{{ $authUser->full_name }}</span>
            <span class="user-role">
                @if($isAdmin)
                    <i class="bi bi-shield-check text-warning"></i> Super Admin
                @elseif($groupRoleName)
                    <i class="bi bi-person-circle text-info"></i> {{ ucfirst(str_replace('-', ' ', $groupRoleName)) }}
                @else
                    <i class="bi bi-person-circle text-secondary"></i> Member
                @endif
            </span>
        </div>
    </div>
    @endauth

    <nav class="nav flex-column sidebar-nav">

        {{-- ==================== MAIN ==================== --}}
        <div class="nav-section">
            <span class="section-label">Main</span>
        </div>

        @if($isAdmin)
            @if($authUser->hasGroupPermission('view_admin_dashboard') || $authUser->can('view_admin_dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                <span class="nav-text">Dashboard</span>
                @if(request()->routeIs('admin.dashboard'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('manage_groups') || $authUser->can('manage_groups'))
            <a href="{{ route('groups.index') }}" class="nav-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-buildings"></i></span>
                <span class="nav-text">Groups</span>
                @if(request()->routeIs('groups.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('manage_subscription_plans') || $authUser->can('manage_subscription_plans'))
            <a href="{{ route('settings.subscription_plans') }}" class="nav-link {{ request()->routeIs('settings.subscription_plans*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-credit-card"></i></span>
                <span class="nav-text">Subscription Plans</span>
                @if(request()->routeIs('settings.subscription_plans*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @else
            @if($authUser->hasGroupPermission('view_group_dashboard'))
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                <span class="nav-text">Dashboard</span>
                @if(request()->routeIs('dashboard'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @endif

        {{-- ==================== MEMBERS ==================== --}}
        @if($authUser->hasAnyGroupPermission(['view_members', 'create_members']))
        <div class="nav-section">
            <span class="section-label">Members</span>
        </div>
            @if($authUser->hasGroupPermission('view_members'))
            <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-people"></i></span>
                <span class="nav-text">Members</span>
                @if(request()->routeIs('members.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @endif

        {{-- ==================== FINANCE ==================== --}}
        @if($authUser->hasAnyGroupPermission(['view_collections', 'view_loans', 'view_expenditures']))
        <div class="nav-section">
            <span class="section-label">Finance</span>
        </div>
            @if($authUser->hasGroupPermission('view_collections'))
            <a href="{{ route('collections.index') }}" class="nav-link {{ request()->routeIs('collections.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-cash-coin"></i></span>
                <span class="nav-text">Collections</span>
                @if(request()->routeIs('collections.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('view_collections'))
            <a href="{{ route('disciplines.fines') }}" class="nav-link {{ request()->routeIs('disciplines.fines') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-exclamation-triangle text-danger"></i></span>
                <span class="nav-text">Fines</span>
                @if(request()->routeIs('disciplines.fines'))<span class="nav-indicator"></span>@endif
            </a>
            <a href="{{ route('disciplines.penalties') }}" class="nav-link {{ request()->routeIs('disciplines.penalties') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-hammer text-purple"></i></span>
                <span class="nav-text">Penalties</span>
                @if(request()->routeIs('disciplines.penalties'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('view_loans'))
            <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-bank"></i></span>
                <span class="nav-text">Loans</span>
                @if(request()->routeIs('loans.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('view_expenditures'))
            <a href="{{ route('expenditures.index') }}" class="nav-link {{ request()->routeIs('expenditures.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-receipt"></i></span>
                <span class="nav-text">Expenditures</span>
                @if(request()->routeIs('expenditures.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @endif

        {{-- ==================== REPORTS ==================== --}}
        @if($authUser->hasAnyGroupPermission(['view_group_reports', 'view_admin_reports']))
        <div class="nav-section">
            <span class="section-label">Reports</span>
        </div>
            @if($isAdmin && ($authUser->hasGroupPermission('view_admin_reports') || $authUser->can('view_admin_reports')))
            <a href="{{ route('reports.admin.revenue') }}" class="nav-link {{ request()->routeIs('reports.admin.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-graph-up"></i></span>
                <span class="nav-text">Revenue Report</span>
                @if(request()->routeIs('reports.admin.*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if(!$isAdmin && $authUser->hasGroupPermission('view_group_reports'))
            <a href="{{ route('reports.collections') }}" class="nav-link {{ request()->routeIs('reports.collections') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-graph-up"></i></span>
                <span class="nav-text">Collections</span>
                @if(request()->routeIs('reports.collections'))<span class="nav-indicator"></span>@endif
            </a>
            <a href="{{ route('reports.loans') }}" class="nav-link {{ request()->routeIs('reports.loans') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-file-text"></i></span>
                <span class="nav-text">Loans</span>
                @if(request()->routeIs('reports.loans'))<span class="nav-indicator"></span>@endif
            </a>
            <a href="{{ route('reports.year-end') }}" class="nav-link {{ request()->routeIs('reports.year-end') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-calendar-check"></i></span>
                <span class="nav-text">Year-End</span>
                @if(request()->routeIs('reports.year-end'))<span class="nav-indicator"></span>@endif
            </a>
            <a href="{{ route('reports.expenditure') }}" class="nav-link {{ request()->routeIs('reports.expenditure') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-receipt-cutoff"></i></span>
                <span class="nav-text">Expenditure</span>
                @if(request()->routeIs('reports.expenditure'))<span class="nav-indicator"></span>@endif
            </a>
            <a href="{{ route('reports.financial-statement') }}" class="nav-link {{ request()->routeIs('reports.financial-statement') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-balance-scale"></i></span>
                <span class="nav-text">Financial</span>
                @if(request()->routeIs('reports.financial-statement'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @endif

        {{-- ==================== USERS & SECURITY ==================== --}}
        @if($authUser->hasAnyGroupPermission(['manage_system_users', 'manage_group_users', 'assign_roles', 'view_activity_logs']))
        <div class="nav-section">
            <span class="section-label">Users &amp; Security</span>
        </div>
            @if($isAdmin && ($authUser->hasGroupPermission('manage_system_users') || $authUser->can('manage_system_users')))
            <a href="{{ route('users.system') }}" class="nav-link {{ request()->routeIs('users.system*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-person-gear"></i></span>
                <span class="nav-text">System Users</span>
                @if(request()->routeIs('users.system*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('manage_group_users'))
            <a href="{{ route('users.group') }}" class="nav-link {{ request()->routeIs('users.group*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-people-gear"></i></span>
                <span class="nav-text">Group Users</span>
                @if(request()->routeIs('users.group*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('assign_roles'))
            <a href="{{ route('users.roles') }}" class="nav-link {{ request()->routeIs('users.roles*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
                <span class="nav-text">Roles &amp; Permissions</span>
                @if(request()->routeIs('users.roles*'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
            @if($authUser->hasGroupPermission('view_activity_logs'))
            <a href="{{ route('activity-logs') }}" class="nav-link {{ request()->routeIs('activity-logs') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
                <span class="nav-text">Activity Logs</span>
                @if(request()->routeIs('activity-logs'))<span class="nav-indicator"></span>@endif
            </a>
            @endif
        @endif

        {{-- ==================== GROUP SETTINGS ==================== --}}
        @if(!$isAdmin && $authUser->hasAnyGroupPermission(['manage_collection_funds', 'manage_calendar_years', 'manage_loan_types', 'manage_group_settings']))
        <div class="nav-section">
            <span class="section-label">Group Settings</span>
        </div>

            @if($authUser->hasGroupPermission('manage_collection_funds'))
            <a href="{{ route('settings.collection_funds') }}" class="nav-link {{ request()->routeIs('settings.collection_funds*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-wallet2"></i></span>
                <span class="nav-text">Collection Funds</span>
                @if(request()->routeIs('settings.collection_funds*'))<span class="nav-indicator"></span>@endif
            </a>
            <div class="submenu-hint">
                <span class="submenu-tag tag-savings">Hisa</span>
                <span class="submenu-tag tag-contrib">Jamii</span>
                <span class="submenu-tag tag-fee">Ada</span>
                <span class="submenu-tag tag-fine">Faini</span>
                <span class="submenu-tag tag-fine">Penalties</span>
                <span class="submenu-tag tag-project">Mradi</span>
            </div>
            @endif

            @if($authUser->hasGroupPermission('manage_calendar_years'))
            <a href="{{ route('settings.calendar_years') }}" class="nav-link {{ request()->routeIs('settings.calendar_years*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-calendar3"></i></span>
                <span class="nav-text">Calendar Year</span>
                @if(request()->routeIs('settings.calendar_years*'))<span class="nav-indicator"></span>@endif
            </a>
            <div class="submenu-hint">
                <span class="submenu-tag tag-year">Year</span>
                <span class="submenu-tag tag-status">Start / End</span>
                <span class="submenu-tag tag-active">Active</span>
            </div>
            @endif

            @if($authUser->hasGroupPermission('manage_loan_types'))
            <a href="{{ route('settings.loan_types') }}" class="nav-link {{ request()->routeIs('settings.loan_types*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="bi bi-percent"></i></span>
                <span class="nav-text">Loan Types</span>
                @if(request()->routeIs('settings.loan_types*'))<span class="nav-indicator"></span>@endif
            </a>
            <div class="submenu-hint">
                <span class="submenu-tag tag-rate">Flat / Reducing</span>
                <span class="submenu-tag tag-pct">Rate %</span>
                <span class="submenu-tag tag-withdraw">Withdraw %</span>
            </div>
            @endif
        @endif

    </nav>

    {{-- Bottom collapse toggle --}}
    <div class="sidebar-footer">
        <button class="collapse-btn" id="sidebarToggle" title="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>
</div>
