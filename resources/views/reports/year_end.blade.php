@extends('layouts.app')

@section('title', 'Year-End Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Year-End Report</h4>
        <div>
            <span class="badge bg-primary fs-6">{{ $calendarYear->name }}</span>
            <a href="{{ route('reports.export', 'year-end') }}" class="btn btn-outline-primary btn-sm ms-2"><i class="bi bi-download me-2"></i>Export</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Collections</p>
                <h4 class="fw-bold text-success">{{ number_format($totalCollections, 0) }} TZS</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Profit</p>
                <h4 class="fw-bold text-primary">{{ number_format($totalProfit, 0) }} TZS</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Expenditure</p>
                <h4 class="fw-bold text-danger">{{ number_format($totalExpenditure, 0) }} TZS</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Net Position</p>
                <h4 class="fw-bold {{ $totalCollections - $totalExpenditure >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totalCollections - $totalExpenditure, 0) }} TZS</h4>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Collections by Fund</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Fund</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @foreach($fundTotals as $fund)
                                    <tr><td>{{ $fund->name }}</td><td class="text-end fw-semibold">{{ number_format($fund->total, 0) }} TZS</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Loan Summary</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr><td>Total Loans Applied</td><td class="text-end fw-semibold">{{ number_format($totalLoansApplied, 0) }} TZS</td></tr>
                                <tr><td>Total Disbursed</td><td class="text-end fw-semibold">{{ number_format($totalLoansDisbursed, 0) }} TZS</td></tr>
                                <tr><td>Total Repaid</td><td class="text-end fw-semibold text-success">{{ number_format($totalLoansRepaid, 0) }} TZS</td></tr>
                                <tr><td>Outstanding</td><td class="text-end fw-semibold text-danger">{{ number_format($totalLoansDisbursed - $totalLoansRepaid, 0) }} TZS</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Member Summary</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Member</th>
                            @foreach($fundTotals as $fund)
                                <th class="text-end">{{ $fund->name }}</th>
                            @endforeach
                            <th class="text-end">Total Loans</th>
                            <th class="text-end">Dividend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memberData as $data)
                            <tr>
                                <td>{{ $data['member']->full_name }}</td>
                                @foreach($fundTotals as $fund)
                                    <td class="text-end">{{ number_format($data['collections'][$fund->slug] ?? 0, 0) }}</td>
                                @endforeach
                                <td class="text-end fw-semibold">{{ number_format($data['total_loans'], 0) }}</td>
                                <td class="text-end fw-semibold text-success">{{ number_format($data['dividend'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($fundTotals) + 4 }}" class="text-center text-muted py-4">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
