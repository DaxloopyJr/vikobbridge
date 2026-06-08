@extends('layouts.app')

@section('title', 'Group Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Group Management</h4>
            <p class="text-muted mb-0">Manage all registered VICOBA groups</p>
        </div>
        <a href="{{ route('groups.renewals') }}" class="btn btn-warning">
            <i class="bi bi-bell me-2"></i>Renewals
        </a>
    </div>

    {{-- Colored Tabs --}}
    <ul class="nav nav-tabs-colored mb-3" id="groupTabs">
        <li class="nav-item"><a class="nav-link tab-orange {{ $tab == 'pending' ? 'active' : '' }}" href="?tab=pending"><i class="bi bi-clock-history me-1"></i>Pending Approval</a></li>
        <li class="nav-item"><a class="nav-link tab-green {{ $tab == 'approved' ? 'active' : '' }}" href="?tab=approved"><i class="bi bi-check-circle me-1"></i>Approved</a></li>
        <li class="nav-item"><a class="nav-link tab-red {{ $tab == 'expired' ? 'active' : '' }}" href="?tab=expired"><i class="bi bi-exclamation-triangle me-1"></i>Expired</a></li>
        <li class="nav-item"><a class="nav-link tab-blue {{ $tab == 'all' ? 'active' : '' }}" href="?tab=all"><i class="bi bi-grid me-1"></i>All Groups</a></li>
    </ul>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="groups-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Chairperson</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var currentTab = '{{ $tab }}';
    $('#groups-table').DataTable({
        ajax: {
            url: '{{ route("groups.data") }}',
            data: function(d) {
                d.tab = currentTab;
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'chairman', name: 'chairman' },
            { data: 'plan', name: 'plan', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'subscription_end_date', name: 'subscription_end_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
