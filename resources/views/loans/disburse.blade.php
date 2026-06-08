@extends('layouts.app')

@section('title', 'Disburse Loan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white py-3"><h5 class="mb-0 fw-bold"><i class="bi bi-cash me-2"></i>Disburse Loan #{{ $loan->loan_number }}</h5></div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold">Loan Summary</h6>
                        <div class="row">
                            <div class="col-md-4"><small class="text-muted">Member</small><p class="mb-0 fw-semibold">{{ $loan->member?->full_name }}</p></div>
                            <div class="col-md-4"><small class="text-muted">Amount</small><p class="mb-0 fw-semibold">{{ number_format($loan->loan_amount, 0) }} TZS</p></div>
                            <div class="col-md-4"><small class="text-muted">EMI</small><p class="mb-0 fw-semibold">{{ number_format($loan->monthly_installment, 0) }} TZS x {{ $loan->loan_term_months }}</p></div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('loans.disburse', $loan) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Disbursement Date *</label><input type="date" name="disbursement_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">First Installment Date *</label><input type="date" name="first_installment_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 month')) }}" required></div>
                            <div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg me-2"></i>Confirm Disbursement</button>
                            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
