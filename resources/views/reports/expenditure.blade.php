@extends('layouts.app')

@section('title', 'Expenditure Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Expenditure Report</h4>
        <a href="{{ route('reports.export', 'expenditure') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-2"></i>Export PDF</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Expenditure</p>
                <h3 class="fw-bold text-danger">{{ number_format($totalExpenditure, 0) }} TZS</h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Categories</p>
                <h3 class="fw-bold text-primary">{{ $categorySummary->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Expenditure Records</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Expense No.</th><th>Category</th><th>Description</th><th>Amount</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($expenditures as $exp)
                                    <tr>
                                        <td><code>{{ $exp->expense_number }}</code></td>
                                        <td><span class="badge bg-light text-dark">{{ $exp->category }}</span></td>
                                        <td>{{ $exp->description }}</td>
                                        <td class="fw-semibold">{{ number_format($exp->amount, 0) }} TZS</td>
                                        <td>{{ $exp->expense_date->format('M d, Y') }}</td>
                                        <td>
                                            @switch($exp->status)
                                                @case('pending') <span class="badge badge-pending">Pending</span> @break
                                                @case('approved') <span class="badge badge-active">Approved</span> @break
                                                @case('rejected') <span class="badge badge-expired">Rejected</span> @break
                                            @endswitch
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No expenditures</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Category Summary</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Category</th><th class="text-end">Amount</th><th class="text-end">Count</th></tr></thead>
                            <tbody>
                                @forelse($categorySummary as $cat)
                                    <tr><td>{{ $cat->category }}</td><td class="text-end fw-semibold">{{ number_format($cat->total, 0) }}</td><td class="text-end">{{ $cat->count }}</td></tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
