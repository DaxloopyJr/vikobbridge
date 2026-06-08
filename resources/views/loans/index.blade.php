@extends('layouts.app')

@section('title', 'Loans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Loans</h4>
            <p class="text-muted mb-0">Manage group loans</p>
        </div>
        @can('apply_loans')
        <div class="d-flex gap-2">
            <a href="{{ route('loans.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Record Loan</a>
            <a href="{{ route('loans.apply') }}" class="btn btn-outline-primary"><i class="bi bi-file-earmark-text me-2"></i>Apply Loan</a>
        </div>
        @endcan
    </div>

    @php $tab = request('tab', 'all'); @endphp
    <ul class="nav nav-tabs-colored mb-3" id="loanTabs">
        <li class="nav-item"><a class="nav-link tab-blue {{ $tab == 'all' ? 'active' : '' }}" href="?tab=all"><i class="bi bi-grid me-1"></i>All ({{ $summaryStats['total_pending'] + $summaryStats['total_disbursed'] + $summaryStats['total_completed'] + $summaryStats['total_defaulted'] }})</a></li>
        <li class="nav-item"><a class="nav-link tab-orange {{ $tab == 'pending' ? 'active' : '' }}" href="?tab=pending"><i class="bi bi-clock me-1"></i>Pending ({{ $summaryStats['total_pending'] }})</a></li>
        <li class="nav-item"><a class="nav-link tab-green {{ $tab == 'disbursed' ? 'active' : '' }}" href="?tab=disbursed"><i class="bi bi-cash me-1"></i>Active ({{ $summaryStats['total_disbursed'] }})</a></li>
        <li class="nav-item"><a class="nav-link tab-teal {{ $tab == 'completed' ? 'active' : '' }}" href="?tab=completed"><i class="bi bi-check-circle me-1"></i>Completed ({{ $summaryStats['total_completed'] }})</a></li>
        <li class="nav-item"><a class="nav-link tab-red {{ $tab == 'defaulted' ? 'active' : '' }}" href="?tab=defaulted"><i class="bi bi-exclamation-triangle me-1"></i>Defaulted ({{ $summaryStats['total_defaulted'] }})</a></li>
    </ul>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="loans-table">
                <thead>
                    <tr><th>ID</th><th>Member</th><th>Amount</th><th>Type</th><th>Rate</th><th>Status</th><th>Date</th><th>Actions</th></tr>
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
    $('#loans-table').DataTable({
        ajax: {
            url: '{{ route("loans.data") }}',
            data: function(d) { d.tab = currentTab; }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'member', name: 'member' },
            { data: 'amount', name: 'amount' },
            { data: 'type', name: 'type', orderable: false },
            { data: 'rate', name: 'rate', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'date', name: 'date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
