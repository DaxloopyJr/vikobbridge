@extends('layouts.app')

@section('title', 'Subscription Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Group Subscription Report</h4>
        <a href="{{ route('reports.admin.subscriptions') }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-2"></i>Export</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4"><label class="form-label">Date From</label><input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}"></div>
                <div class="col-md-4"><label class="form-label">Date To</label><input type="date" name="date_to" class="form-control" value="{{ $dateTo }}"></div>
                <div class="col-md-4 d-flex align-items-end"><button type="submit" class="btn btn-primary"><i class="bi bi-filter me-2"></i>Filter</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Status Summary</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Status</th><th class="text-end">Count</th></tr></thead>
                            <tbody>
                                @foreach($statusSummary as $s)
                                    <tr><td><span class="badge bg-light text-dark">{{ ucfirst($s->status) }}</span></td><td class="text-end fw-semibold">{{ $s->count }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Plan Summary</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Plan</th><th class="text-end">Groups</th></tr></thead>
                            <tbody>
                                @foreach($planSummary as $p)
                                    <tr><td>{{ $p->name }}</td><td class="text-end fw-semibold">{{ $p->count }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Groups</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Group</th><th>Chairperson</th><th>Plan</th><th>Status</th><th>Payment</th><th>Sub. Date</th></tr></thead>
                    <tbody>
                        @forelse($groups as $grp)
                            <tr>
                                <td><strong>{{ $grp->name }}</strong></td>
                                <td>{{ $grp->chairman?->full_name ?? 'N/A' }}</td>
                                <td>{{ $grp->subscriptionPlan?->name }}</td>
                                <td>
                                    @switch($grp->status)
                                        @case('active') <span class="badge badge-active">Active</span> @break
                                        @case('pending') <span class="badge badge-pending">Pending</span> @break
                                        @case('expired') <span class="badge badge-expired">Expired</span> @break
                                        @default <span class="badge bg-secondary">{{ $grp->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($grp->payment_status)
                                        @case('paid') <span class="badge badge-active">Paid</span> @break
                                        @case('trial') <span class="badge badge-trial">Trial</span> @break
                                        @case('expired') <span class="badge badge-expired">Expired</span> @break
                                        @default <span class="badge bg-secondary">{{ $grp->payment_status }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $grp->subscription_date->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No groups found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groups->hasPages())<div class="card-footer bg-white">{{ $groups->links() }}</div>@endif
    </div>
</div>
@endsection
