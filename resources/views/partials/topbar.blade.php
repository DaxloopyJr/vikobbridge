<div class="topbar d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        @if(isset($currentGroup) && !$authUser->isSuperAdmin())
            <div class="d-flex align-items-center">
                <span class="text-muted small me-2">Group:</span>
                <span class="fw-semibold text-primary">{{ $currentGroup->name }}</span>

                @if(isset($currentCalendarYear))
                    <span class="mx-2 text-muted">|</span>
                    <span class="text-muted small me-2">Year:</span>
                    <form method="POST" action="{{ route('dashboard.calendar-year') }}" class="d-inline" id="calendarYearForm">
                        @csrf
                        <select name="calendar_year_id" class="form-select form-select-sm d-inline-block w-auto" onchange="document.getElementById('calendarYearForm').submit()">
                            @foreach($currentGroup->calendarYears ?? [] as $year)
                                <option value="{{ $year->id }}" {{ (isset($currentCalendarYear) && $currentCalendarYear->id == $year->id) ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if(isset($currentGroup))
                    @php
                        $daysLeft = $currentGroup->daysUntilExpiry();
                    @endphp
                    @if($currentGroup->isOnTrial())
                        <span class="badge badge-trial ms-2">Trial: {{ $daysLeft }} days left</span>
                    @elseif($daysLeft <= 14 && $daysLeft > 0)
                        <span class="badge badge-pending ms-2">{{ $daysLeft }} days left</span>
                    @elseif($daysLeft <= 0)
                        <span class="badge badge-expired ms-2">Expired</span>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center">
        @if(isset($userGroups) && $userGroups->count() > 1 && !$authUser->isSuperAdmin())
            <form method="POST" action="{{ route('dashboard.group') }}" class="me-3" id="groupSwitchForm">
                @csrf
                <select name="group_id" class="form-select form-select-sm" onchange="document.getElementById('groupSwitchForm').submit()">
                    @foreach($userGroups as $g)
                        <option value="{{ $g->id }}" {{ (isset($currentGroup) && $currentGroup->id == $g->id) ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                @if(isset($authUser) && $authUser->profile_picture)
                    <img src="{{ asset('storage/' . $authUser->profile_picture) }}" class="rounded-circle me-2" width="32" height="32" alt="">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                        {{ isset($authUser) ? substr($authUser->first_name, 0, 1) . substr($authUser->last_name, 0, 1) : 'U' }}
                    </div>
                @endif
                <span class="d-none d-md-inline">{{ isset($authUser) ? $authUser->full_name : 'User' }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
