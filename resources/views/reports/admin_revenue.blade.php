@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Revenue Collection Report</h4>
        <a href="{{ route('reports.admin.revenue') }}?date_from={{ $dateFrom }}&date_to={{ $dateTo }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-2"></i>Export PDF</a>
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
            <div class="stat-card">
                <p class="text-muted mb-1">Total Revenue</p>
                <h3 class="fw-bold text-success">{{ number_format($totalRevenue, 0) }} TZS</h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <p class="text-muted mb-1">Total Transactions</p>
                <h3 class="fw-bold text-primary">{{ number_format($totalTransactions) }}</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Monthly Revenue Trend</h5></div>
        <div class="card-body"><canvas id="revenueTrendChart" height="80"></canvas></div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Revenue by Plan</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Plan</th><th>Total Revenue</th><th>Transactions</th></tr></thead>
                    <tbody>
                        @forelse($planBreakdown as $plan)
                            <tr><td>{{ $plan->name }}</td><td class="fw-semibold">{{ number_format($plan->total, 0) }} TZS</td><td>{{ $plan->count }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Recent Payments</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Group</th><th>Plan</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->group?->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $payment->subscriptionPlan?->name ?? 'N/A' }}</span></td>
                                <td class="fw-semibold">{{ number_format($payment->amount, 0) }} TZS</td>
                                <td>{{ $payment->paid_at?->format('M d, Y') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No payments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($recentPayments->hasPages())<div class="card-footer bg-white">{{ $recentPayments->links() }}</div>@endif
    </div>
</div>

@push('scripts')
<script>
new Chart(document.getElementById('revenueTrendChart'), {
    type: 'line',
    data: {
        labels: @json($monthlyBreakdown->pluck('month')),
        datasets: [{
            label: 'Revenue (TZS)',
            data: @json($monthlyBreakdown->pluck('total')),
            borderColor: '#1a5f2a',
            backgroundColor: 'rgba(26, 95, 42, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
