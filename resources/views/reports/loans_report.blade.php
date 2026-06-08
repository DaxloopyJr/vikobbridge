@extends('layouts.app')

@section('title', 'Loans Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Loans Report</h4>
        <a href="{{ route('reports.export', 'loans') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-2"></i>Export PDF</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Applied</p>
                <h5 class="fw-bold text-primary">{{ number_format($totalApplied, 0) }} TZS</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Disbursed</p>
                <h5 class="fw-bold text-success">{{ number_format($totalDisbursed, 0) }} TZS</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Repaid</p>
                <h5 class="fw-bold text-info">{{ number_format($totalRepaid, 0) }} TZS</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center">
                <p class="text-muted mb-1">Total Defaulted</p>
                <h5 class="fw-bold text-danger">{{ number_format($totalDefaulted, 0) }} TZS</h5>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Status Summary</h5></div>
        <div class="card-body">
            <div class="row">
                @foreach($statusSummary as $status)
                    <div class="col-md-3 mb-3">
                        <div class="d-flex justify-content-between p-3 rounded" style="background: #f8f9fa;">
                            <div>
                                <span class="text-muted">{{ ucfirst($status->status) }}</span>
                                <h5 class="fw-bold mb-0">{{ $status->count }}</h5>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Amount</small>
                                <h6 class="fw-bold mb-0">{{ number_format($status->total, 0) }}</h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Loan Records</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Loan No.</th><th>Member</th><th>Amount</th><th>EMI</th><th>Status</th><th>Progress</th></tr></thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td><code>{{ $loan->loan_number }}</code></td>
                                <td>{{ $loan->member?->full_name ?? 'N/A' }}</td>
                                <td>{{ number_format($loan->loan_amount, 0) }} TZS</td>
                                <td>{{ number_format($loan->monthly_installment, 0) }} TZS</td>
                                <td>
                                    @switch($loan->status)
                                        @case('pending') <span class="badge badge-pending">Pending</span> @break
                                        @case('disbursed') <span class="badge badge-active">Disbursed</span> @break
                                        @case('repaying') <span class="badge badge-active">Repaying</span> @break
                                        @case('completed') <span class="badge bg-success">Completed</span> @break
                                        @case('defaulted') <span class="badge badge-expired">Defaulted</span> @break
                                        @default <span class="badge bg-secondary">{{ $loan->status }}</span>
                                    @endswitch
                                </td>
                                <td style="width: 100px;">
                                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: {{ $loan->progress_percentage }}%"></div></div>
                                    <small class="text-muted">{{ number_format($loan->progress_percentage, 0) }}%</small>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No loans found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
