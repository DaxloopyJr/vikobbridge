@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Roles &amp; Permissions</h4>
            <p class="text-muted mb-0">Manage group roles and assign permissions. Super Admin permissions are not shown here.</p>
        </div>
    </div>

    {{-- Role Cards --}}
    <div class="row g-4">
        @foreach($roles as $role)
        @php
            $roleColor = match($role->name) {
                'group-admin' => 'primary',
                'chairperson' => 'success',
                'secretary' => 'info',
                'treasurer' => 'warning',
                'member' => 'secondary',
                default => 'dark',
            };
            $roleIcon = match($role->name) {
                'group-admin' => 'bi-person-gear',
                'chairperson' => 'bi-person-badge',
                'secretary' => 'bi-pen',
                'treasurer' => 'bi-cash-stack',
                'member' => 'bi-person',
                default => 'bi-person',
            };
            $roleDescription = match($role->name) {
                'group-admin' => 'Full access to group management including settings and user management.',
                'chairperson' => 'Leadership role with oversight on approvals, members, and reports.',
                'secretary' => 'Handles member records, collections entry, and meeting reports.',
                'treasurer' => 'Manages finances including collections, loans, and expenditures.',
                'member' => 'Basic access to view group data and apply for loans.',
                default => '',
            };
        @endphp
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <div class="role-icon bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} rounded-circle d-flex align-items-center justify-content-center me-3" style="width:42px;height:42px;">
                        <i class="bi {{ $roleIcon }} fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</h5>
                        <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ $roleDescription }}</p>

                    <form method="POST" action="{{ route('users.roles.update', $role) }}">
                        @csrf @method('PUT')

                        @foreach($categorizedPermissions as $category => $permissions)
                            @php
                                $rolePermNames = $role->permissions->pluck('name')->toArray();
                                $catHasAny = $permissions->filter(fn($p) => in_array($p->name, $rolePermNames))->count() > 0;
                            @endphp
                            <div class="permission-category mb-3">
                                <div class="category-header d-flex align-items-center mb-2">
                                    <span class="badge bg-light text-dark border">{{ $category }}</span>
                                    @if($catHasAny)
                                        <span class="ms-2 badge bg-success bg-opacity-25 text-success" style="font-size:0.6rem;">ACTIVE</span>
                                    @endif
                                </div>
                                <div class="permission-list ms-2">
                                    @foreach($permissions as $permission)
                                        @php
                                            $isChecked = in_array($permission->name, $rolePermNames);
                                            $permLabel = match($permission->name) {
                                                'view_members' => 'View Members',
                                                'create_members' => 'Create Members',
                                                'edit_members' => 'Edit Members',
                                                'delete_members' => 'Delete Members',
                                                'import_members' => 'Import Members',
                                                'export_members' => 'Export Members',
                                                'view_collections' => 'View Collections',
                                                'create_collections' => 'Create Collections',
                                                'edit_collections' => 'Edit Collections',
                                                'delete_collections' => 'Delete Collections',
                                                'import_collections' => 'Import Collections',
                                                'view_loans' => 'View Loans',
                                                'apply_loans' => 'Apply Loans',
                                                'approve_loans' => 'Approve Loans',
                                                'disburse_loans' => 'Disburse Loans',
                                                'manage_loan_repayments' => 'Manage Repayments',
                                                'edit_loans' => 'Edit Loans',
                                                'delete_loans' => 'Delete Loans',
                                                'view_expenditures' => 'View Expenditures',
                                                'create_expenditures' => 'Create Expenditures',
                                                'approve_expenditures' => 'Approve Expenditures',
                                                'edit_expenditures' => 'Edit Expenditures',
                                                'delete_expenditures' => 'Delete Expenditures',
                                                'view_group_reports' => 'View Reports',
                                                'export_reports' => 'Export Reports',
                                                'view_collection_reports' => 'Collection Reports',
                                                'view_loan_reports' => 'Loan Reports',
                                                'view_yearly_reports' => 'Year-End Reports',
                                                'view_financial_statements' => 'Financial Statements',
                                                'manage_group_users' => 'Manage Group Users',
                                                'assign_roles' => 'Assign Roles',
                                                'view_activity_logs' => 'View Activity Logs',
                                                'manage_collection_funds' => 'Collection Funds',
                                                'manage_calendar_years' => 'Calendar Years',
                                                'manage_loan_types' => 'Loan Types',
                                                'manage_group_settings' => 'Group Settings',
                                                'view_settings' => 'View Settings',
                                                'change_calendar_year' => 'Change Calendar Year',
                                                default => ucwords(str_replace('_', ' ', $permission->name)),
                                            };
                                        @endphp
                                        <div class="form-check permission-check mb-1">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                id="perm_{{ $role->name }}_{{ $permission->id }}"
                                                {{ $isChecked ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label small" for="perm_{{ $role->name }}_{{ $permission->id }}" style="font-size:0.82rem;">
                                                {{ $permLabel }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-{{ $roleColor }}">
                                <i class="bi bi-check-lg me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <div class="card mt-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Permission Legend</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2">Group Admin</span>
                        <small class="text-muted">Full group access</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success me-2">Chairperson</span>
                        <small class="text-muted">Leadership & approvals</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info me-2">Secretary</span>
                        <small class="text-muted">Records & reports</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning me-2">Treasurer</span>
                        <small class="text-muted">Finance management</small>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i><strong>Note:</strong> Super Admin permissions are system-level and cannot be managed from this page. Only group-level roles and permissions are shown.
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.permission-category {
    border-left: 2px solid #e9ecef;
    padding-left: 10px;
}
.permission-category:hover {
    border-left-color: #1a5f2a;
}
.category-header .badge {
    font-size: 0.7rem;
    padding: 4px 8px;
}
.permission-check .form-check-input:checked {
    background-color: #1a5f2a;
    border-color: #1a5f2a;
}
.permission-check .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(26, 95, 42, 0.25);
}
.role-icon {
    transition: transform 0.2s;
}
.card:hover .role-icon {
    transform: scale(1.05);
}
</style>
@endpush
