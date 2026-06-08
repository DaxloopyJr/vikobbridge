@extends('layouts.app')

@section('title', 'Financial Statement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Financial Statement / Balance Sheet</h4>
        <span class="badge bg-primary fs-6">{{ $calendarYear->name }}</span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Assets (Collections)</h6>
                    <h3 class="fw-bold text-success">{{ number_format($totalAssets, 0) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Liabilities (Loans)</h6>
                    <h3 class="fw-bold text-danger">{{ number_format($totalLiabilities, 0) }} TZS</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Net Position</h6>
                    <h3 class="fw-bold {{ $netPosition >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netPosition, 0) }} TZS</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white py-3"><h5 class="mb-0 fw-bold">Assets (Income Sources)</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Fund</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                @foreach($fundTotals as $fund)
                                    <tr><td>{{ $fund->name }}</td><td class="text-end">{{ number_format($fund->total, 0) }} TZS</td></tr>
                                @endforeach
                                <tr class="table-success fw-bold"><td>Total Assets</td><td class="text-end">{{ number_format($totalAssets, 0) }} TZS</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-danger text-white py-3"><h5 class="mb-0 fw-bold">Liabilities & Expenditure</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Category</th><th class="text-end">Amount</th></tr></thead>
                            <tbody>
                                <tr><td>Outstanding Loans</td><td class="text-end">{{ number_format($totalLiabilities, 0) }} TZS</td></tr>
                                <tr><td>Total Expenditure</td><td class="text-end">{{ number_format($totalExpenditure, 0) }} TZS</td></tr>
                                <tr class="table-danger fw-bold"><td>Total Liabilities</td><td class="text-end">{{ number_format($totalLiabilities + $totalExpenditure, 0) }} TZS</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
