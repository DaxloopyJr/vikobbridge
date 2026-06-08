@extends('layouts.app')

@section('title', 'Loan Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Loan #{{ $loan->loan_number }}</h4>
            <p class="text-muted mb-0">{{ $loan->member?->full_name ?? 'N/A' }} — <span class="badge bg-light text-dark">{{ ucfirst($loan->source ?? 'recorded') }}</span></p>
        </div>
        <div class="d-flex gap-2">
            @if($loan->status == 'pending' && $loan->readyForDisbursement())
                <a href="{{ route('loans.disburse.form', $loan) }}" class="btn btn-success"><i class="bi bi-cash me-2"></i>Disburse</a>
            @endif
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            {{-- Loan Summary --}}
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Loan Summary</h5></div>
                <div class="card-body">
                    <div class="mb-3"><small class="text-muted d-block">Loan Amount</small><strong class="fs-5">{{ number_format($loan->loan_amount, 0) }} TZS</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Interest Rate</small><strong>{{ $loan->interest_rate }}% ({{ ucfirst(str_replace('_', ' ', $loan->rate_type)) }})</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Total Interest</small><strong>{{ number_format($loan->total_interest_amount, 0) }} TZS</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Total Repayment</small><strong class="text-primary">{{ number_format($loan->total_repayment_amount, 0) }} TZS</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Monthly EMI</small><strong>{{ number_format($loan->monthly_installment, 0) }} TZS</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Loan Term</small><strong>{{ $loan->loan_term_months }} months</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Amount Paid</small><strong class="text-success">{{ number_format($loan->amount_paid, 0) }} TZS</strong></div>
                    <div class="mb-3"><small class="text-muted d-block">Amount Remaining</small><strong class="text-danger">{{ number_format($loan->amount_remaining, 0) }} TZS</strong></div>
                    <div class="mb-0"><small class="text-muted d-block">Status</small>
                        @switch($loan->status)
                            @case('pending') <span class="badge bg-warning text-dark fs-6">Pending</span> @break
                            @case('approved') <span class="badge bg-info fs-6">Approved</span> @break
                            @case('disbursed') <span class="badge bg-primary fs-6">Disbursed</span> @break
                            @case('repaying') <span class="badge bg-success fs-6">Repaying</span> @break
                            @case('completed') <span class="badge bg-success fs-6">Completed</span> @break
                            @case('defaulted') <span class="badge bg-danger fs-6">Defaulted</span> @break
                            @case('rejected') <span class="badge bg-secondary fs-6">Rejected</span> @break
                            @default <span class="badge bg-secondary fs-6">{{ $loan->status }}</span>
                        @endswitch
                    </div>
                </div>
            </div>

            {{-- Approval Workflow --}}
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-check2-circle me-2"></i>Approval Workflow</h5></div>
                <div class="card-body">
                    {{-- Stage: Cosigners --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            @if($loan->anyCosignerRejected())
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            @elseif($loan->allCosignersApproved())
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @else
                                <i class="bi bi-clock-fill text-warning fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block">Cosigner Approval</strong>
                            <small class="text-muted">
                                @if($loan->anyCosignerRejected()) Rejected
                                @elseif($loan->allCosignersApproved()) All approved
                                @else {{ $loan->cosigners()->where('approved', true)->count() }}/{{ $loan->cosigners()->count() }} approved
                                @endif
                            </small>
                        </div>
                    </div>

                    {{-- Stage: Treasurer --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            @if($loan->treasurerApproved())
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @elseif($loan->anyCosignerRejected())
                                <i class="bi bi-dash-circle text-secondary fs-4"></i>
                            @else
                                <i class="bi bi-clock-fill text-warning fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block">Treasurer Approval</strong>
                            <small class="text-muted">
                                @if($loan->treasurerApproved()) Approved by {{ $loan->treasurerApprover?->full_name ?? 'N/A' }}
                                @elseif($loan->anyCosignerRejected()) N/A (cosigner rejected)
                                @else Pending
                                @endif
                            </small>
                        </div>
                    </div>

                    {{-- Stage: Secretary --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 me-3">
                            @if($loan->secretaryApproved())
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @elseif(!$loan->treasurerApproved())
                                <i class="bi bi-dash-circle text-secondary fs-4"></i>
                            @else
                                <i class="bi bi-clock-fill text-warning fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block">Secretary Approval</strong>
                            <small class="text-muted">
                                @if($loan->secretaryApproved()) Approved by {{ $loan->secretaryApprover?->full_name ?? 'N/A' }}
                                @elseif(!$loan->treasurerApproved()) Waiting for Treasurer
                                @else Pending
                                @endif
                            </small>
                        </div>
                    </div>

                    {{-- Stage: Chairman --}}
                    <div class="d-flex align-items-center mb-0">
                        <div class="flex-shrink-0 me-3">
                            @if($loan->chairmanApproved())
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @elseif(!$loan->secretaryApproved())
                                <i class="bi bi-dash-circle text-secondary fs-4"></i>
                            @else
                                <i class="bi bi-clock-fill text-warning fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <strong class="d-block">Chairman Approval</strong>
                            <small class="text-muted">
                                @if($loan->chairmanApproved()) Approved by {{ $loan->chairmanApprover?->full_name ?? 'N/A' }}
                                @elseif(!$loan->secretaryApproved()) Waiting for Secretary
                                @else Pending
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Timeline</h5></div>
                <div class="card-body">
                    <div class="mb-2"><small class="text-muted d-block">Application Date</small>{{ $loan->application_date->format('M d, Y') }}</div>
                    @if($loan->disbursement_date)
                        <div class="mb-2"><small class="text-muted d-block">Disbursement Date</small>{{ $loan->disbursement_date->format('M d, Y') }}</div>
                    @endif
                    @if($loan->first_installment_date)
                        <div class="mb-2"><small class="text-muted d-block">First Installment</small>{{ $loan->first_installment_date->format('M d, Y') }}</div>
                    @endif
                    @if($loan->reason)
                        <div class="mb-0"><small class="text-muted d-block">Reason</small>{{ $loan->reason }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Collateral (if applied loan) --}}
            @if($loan->collaterals->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock me-2"></i>Collateral</h5>
                    </div>
                    <div class="card-body">
                        @foreach($loan->collaterals as $collateral)
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-2"><strong>Description:</strong> {{ $collateral->description }}</p>
                                    <p class="mb-2"><strong>Estimated Value:</strong> {{ number_format($collateral->estimated_value, 0) }} TZS</p>
                                    <p class="mb-0">
                                        <strong>Status:</strong>
                                        @if($collateral->verified)
                                            <span class="badge bg-success">Verified</span>
                                            <small class="text-muted">by {{ $collateral->verifier?->full_name ?? 'N/A' }} on {{ $collateral->verified_at?->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending Verification</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    @if($collateral->document_path)
                                        <a href="{{ asset('storage/' . $collateral->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-earmark me-1"></i>View Document</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Cosigners --}}
            @if($loan->cosigners->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Cosigners</h5>
                        <span class="badge bg-light text-dark">{{ $loan->cosigners()->where('approved', true)->count() }}/{{ $loan->cosigners()->count() }} Approved</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Member</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($loan->cosigners as $cosigner)
                                    <tr>
                                        <td>{{ $cosigner->member?->first_name }} {{ $cosigner->member?->last_name }}</td>
                                        <td>
                                            @if($cosigner->approved) <span class="badge bg-success">Approved</span>
                                            @elseif($cosigner->rejected) <span class="badge bg-danger">Rejected</span>
                                            @else <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $cosigner->approved_at?->format('M d, Y') ?? $cosigner->rejected_at?->format('M d, Y') ?? '-' }}</small></td>
                                        <td>
                                            @can('approve_loans')
                                                @if(!$cosigner->approved && !$cosigner->rejected)
                                                    <form action="{{ route('loans.cosigner.approve', [$loan, $cosigner]) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></button></form>
                                                    <form action="{{ route('loans.cosigner.reject', [$loan, $cosigner]) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button></form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Officer Approval Actions --}}
            @if($loan->status == 'pending' && !$loan->anyCosignerRejected())
                <div class="card mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-gavel me-2"></i>Officer Approval Actions</h5></div>
                    <div class="card-body">
                        {{-- Treasurer --}}
                        @if($loan->allCosignersApproved() && !$loan->treasurerApproved())
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div>
                                    <strong>Treasurer Approval</strong>
                                    <small class="text-muted d-block">All cosigners approved. Ready for Treasurer review.</small>
                                </div>
                                @can('approve_loans')
                                    <form action="{{ route('loans.treasurer.approve', $loan) }}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Approve as Treasurer</button></form>
                                @endcan
                            </div>
                        @endif

                        {{-- Secretary --}}
                        @if($loan->treasurerApproved() && !$loan->secretaryApproved())
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div>
                                    <strong>Secretary Approval</strong>
                                    <small class="text-muted d-block">Treasurer approved. Ready for Secretary review.</small>
                                </div>
                                @can('approve_loans')
                                    <form action="{{ route('loans.secretary.approve', $loan) }}" method="POST">@csrf<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Approve as Secretary</button></form>
                                @endcan
                            </div>
                        @endif

                        {{-- Chairman --}}
                        @if($loan->secretaryApproved() && !$loan->chairmanApproved())
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Chairman Final Approval</strong>
                                    <small class="text-muted d-block">Secretary approved. Final approval needed from Chairman.</small>
                                </div>
                                @can('approve_loans')
                                    <form action="{{ route('loans.chairman.approve', $loan) }}" method="POST">@csrf<button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Final Approve as Chairman</button></form>
                                @endcan
                            </div>
                        @endif

                        @if($loan->chairmanApproved())
                            <div class="alert alert-success mb-0"><i class="bi bi-check-circle-fill me-2"></i>This loan has been fully approved by all officers. Ready for disbursement.</div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Rejection Alert --}}
            @if($loan->status == 'rejected')
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-x-circle-fill me-2"></i><strong>Loan Rejected</strong>
                    @if($loan->approval_notes)<p class="mb-0 mt-1">{{ $loan->approval_notes }}</p>@endif
                </div>
            @endif

            {{-- Repayment Schedule --}}
            @if(in_array($loan->status, ['disbursed', 'repaying', 'completed', 'defaulted']))
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Repayment Schedule</h5>
                        <div>
                            <span class="badge bg-success me-1">{{ $loan->paid_installments_count }} Paid</span>
                            <span class="badge bg-warning">{{ $loan->total_installments_count - $loan->paid_installments_count }} Pending</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr><th>#</th><th>Due Date</th><th>EMI</th><th>Principal</th><th>Interest</th><th>Balance</th><th>Paid</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->repayments as $repayment)
                                        <tr>
                                            <td>{{ $repayment->installment_number }}</td>
                                            <td>{{ $repayment->due_date->format('M d, Y') }}</td>
                                            <td>{{ number_format($repayment->emi_amount, 0) }}</td>
                                            <td>{{ number_format($repayment->principal_amount, 0) }}</td>
                                            <td>{{ number_format($repayment->interest_amount, 0) }}</td>
                                            <td>{{ number_format($repayment->balance_amount, 0) }}</td>
                                            <td>{{ $repayment->amount_paid > 0 ? number_format($repayment->amount_paid, 0) : '-' }}</td>
                                            <td>
                                                @switch($repayment->payment_status)
                                                    @case('paid') <span class="badge bg-success">Paid</span> @break
                                                    @case('pending') <span class="badge bg-warning text-dark">Pending</span> @break
                                                    @case('overdue') <span class="badge bg-danger">Overdue</span> @break
                                                    @case('skipped') <span class="badge bg-secondary">Skipped</span> @break
                                                    @default <span class="badge bg-secondary">{{ $repayment->payment_status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($repayment->payment_status != 'paid')
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $repayment->id }}"><i class="bi bi-cash"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @foreach($loan->repayments->where('payment_status', '!=', 'paid') as $repayment)
                    <div class="modal fade" id="paymentModal{{ $repayment->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Record Payment - Installment {{ $repayment->installment_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('loan-repayments.pay', $repayment) }}">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Amount (TZS)</label>
                                            <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $repayment->emi_amount }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Payment Channel</label>
                                            <select name="payment_channel" class="form-select" required>
                                                <option value="cash">Cash</option>
                                                <option value="bank">Bank Transfer</option>
                                                <option value="mobile_money">Mobile Money</option>
                                                <option value="selcom">Selcom</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Transaction Reference</label>
                                            <input type="text" name="transaction_reference" class="form-control">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Record Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
