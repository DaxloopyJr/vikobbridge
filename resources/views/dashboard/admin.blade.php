@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Super Admin Dashboard</h4>
        <div class="text-muted small">{{ now()->format('l, F d, Y') }}</div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Groups</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalGroups) }}</h3>
                        <small class="text-primary">{{ $activeGroups }} active</small>
                    </div>
                    <div class="stat-icon" style="background: #e8f5e9;">
                        <i class="bi bi-buildings text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Members</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalMembers) }}</h3>
                        <small class="text-success">{{ $totalActiveMembers }} active</small>
                    </div>
                    <div class="stat-icon" style="background: #e3f2fd;">
                        <i class="bi bi-people text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Monthly Revenue</p>
                        <h3 class="fw-bold mb-0">{{ number_format($currentMonthRevenue, 0) }} TZS</h3>
                        <small class="text-warning">This month</small>
                    </div>
                    <div class="stat-icon" style="background: #fff3e0;">
                        <i class="bi bi-cash-stack text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Yearly Revenue</p>
                        <h3 class="fw-bold mb-0">{{ number_format($currentYearRevenue, 0) }} TZS</h3>
                        <small class="text-danger">{{ $expiredGroups }} expired groups</small>
                    </div>
                    <div class="stat-icon" style="background: #fce4ec;">
                        <i class="bi bi-graph-up-arrow text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Revenue Trend ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Groups by Plan</h5>
                </div>
                <div class="card-body">
                    <canvas id="planChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Recent Groups</h5>
                    <a href="{{ route('groups.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Group</th><th>Plan</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentGroups as $grp)
                                    <tr>
                                        <td>
                                            <strong>{{ $grp->name }}</strong><br>
                                            <small class="text-muted">{{ $grp->chairman?->full_name ?? 'N/A' }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $grp->subscriptionPlan?->name ?? 'N/A' }}</span></td>
                                        <td>
                                            @switch($grp->status)
                                                @case('active') <span class="badge badge-active">Active</span> @break
                                                @case('pending') <span class="badge badge-pending">Pending</span> @break
                                                @case('expired') <span class="badge badge-expired">Expired</span> @break
                                                @default <span class="badge bg-secondary">{{ $grp->status }}</span>
                                            @endswitch
                                        </td>
                                        <td><small>{{ $grp->subscription_date->format('M d, Y') }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No groups registered yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Recent Payments</h5>
                    <a href="{{ route('reports.admin.revenue') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Group</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->group?->name ?? 'N/A' }}</strong></td>
                                        <td class="fw-semibold">{{ number_format($payment->amount, 0) }} TZS</td>
                                        <td>
                                            @if($payment->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $payment->status }}</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $payment->paid_at?->format('M d, Y') ?? 'N/A' }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No payments yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const revCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Revenue (TZS)',
            data: {{ json_encode(array_values(array_replace(array_fill(1, 12, 0), $monthlyRevenue))) }},
            backgroundColor: '#1a5f2a',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

const planCtx = document.getElementById('planChart').getContext('2d');
new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: @json($groupsByPlan->pluck('name')),
        datasets: [{
            data: @json($groupsByPlan->pluck('total')),
            backgroundColor: ['#1a5f2a', '#f8b500', '#0d6efd', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush
@endsection
