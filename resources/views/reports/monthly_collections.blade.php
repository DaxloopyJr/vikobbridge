@extends('layouts.app')

@section('title', 'Collections Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Monthly Collections Report</h4>
        <a href="{{ route('reports.export', 'collections') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-2"></i>Export PDF</a>
    </div>

    <div class="row g-4 mb-4">
        @foreach($fundSummary as $fund)
            <div class="col-md-4 col-lg-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">{{ $fund->name }}</h6>
                        <h5 class="fw-bold mb-0">{{ number_format($fund->total, 0) }}</h5>
                        <small class="text-muted">{{ $fund->count }} payments</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Monthly Trend</h5></div>
        <div class="card-body"><canvas id="monthlyTrendChart" height="80"></canvas></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Collection Records</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Member</th><th>Fund</th><th>Amount</th><th>Channel</th><th>Date</th></tr></thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr>
                                        <td>{{ $collection->member?->full_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $collection->collectionFund?->name }}</span></td>
                                        <td class="fw-semibold">{{ number_format($collection->amount, 0) }} TZS</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $collection->payment_channel)) }}</td>
                                        <td>{{ $collection->payment_date->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No collections</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Member Summary</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Member</th><th>Total</th></tr></thead>
                            <tbody>
                                @forelse($memberSummary as $m)
                                    <tr><td>{{ $m->first_name }} {{ $m->last_name }}</td><td class="fw-semibold">{{ number_format($m->total, 0) }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-4">No data</td></tr>
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
new Chart(document.getElementById('monthlyTrendChart'), {
    type: 'bar',
    data: {
        labels: @json($monthlySummary->pluck('month')),
        datasets: [{
            label: 'Collections (TZS)',
            data: @json($monthlySummary->pluck('total')),
            backgroundColor: '#1a5f2a',
            borderRadius: 6
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
@endsection
