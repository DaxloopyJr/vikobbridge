@extends('layouts.app')

@section('title', 'Record Loan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Record Loan</h4>
            <p class="text-muted mb-0">Manually record a loan with cosigners</p>
        </div>
        <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="loanFormTabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#recordForm"><i class="bi bi-pencil-square me-1"></i>Record Loan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('loans.apply') }}"><i class="bi bi-file-earmark-text me-1"></i>Apply Loan</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('loans.store') }}" id="recordLoanForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Member *</label>
                                <select name="member_id" id="memberSelect" class="form-select @error('member_id') is-invalid @enderror" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                    @endforeach
                                </select>
                                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Loan Type *</label>
                                <select name="loan_type_id" id="loanTypeSelect" class="form-select @error('loan_type_id') is-invalid @enderror" required>
                                    <option value="">Select Loan Type</option>
                                    @foreach($loanTypes as $type)
                                        <option value="{{ $type->id }}" data-rate="{{ $type->rate_percentage }}" data-rate-type="{{ $type->rate_type }}" data-proc-fee="{{ $type->processing_fee }}" {{ old('loan_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->rate_percentage }}% {{ ucfirst(str_replace('_', ' ', $type->rate_type)) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('loan_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Loan Amount (TZS) *</label>
                                <input type="number" name="loan_amount" id="loanAmount" class="form-control @error('loan_amount') is-invalid @enderror" step="100" min="1000" value="{{ old('loan_amount') }}" required>
                                @error('loan_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Interest Rate (%)</label>
                                <input type="number" name="interest_rate" id="interestRate" class="form-control @error('interest_rate') is-invalid @enderror" step="0.01" min="0" max="100" value="{{ old('interest_rate') }}">
                                <small class="text-muted">Leave empty to use loan type default</small>
                                @error('interest_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Loan Term (Months) *</label>
                                <input type="number" name="loan_term_months" id="loanTerm" class="form-control @error('loan_term_months') is-invalid @enderror" min="1" max="60" value="{{ old('loan_term_months', 12) }}" required>
                                @error('loan_term_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Application Date *</label>
                                <input type="date" name="application_date" class="form-control" value="{{ old('application_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">First Installment Date</label>
                                <input type="date" name="first_installment_date" class="form-control" value="{{ old('first_installment_date') }}">
                            </div>

                            {{-- Cosigners --}}
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Cosigners</label>
                                <select name="cosigner_ids[]" id="cosignerSelect" class="form-select" multiple>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ collect(old('cosigner_ids'))->contains($member->id) ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple cosigners. Selected cosigners must approve before the loan can be disbursed.</small>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Reason for Loan</label>
                                <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Record Loan</button>
                            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Loan Calculator --}}
        <div class="col-lg-4">
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Loan Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Principal Amount (TZS)</label>
                        <input type="number" id="calcAmount" class="form-control form-control-sm" step="100" min="1000" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Annual Interest Rate (%)</label>
                        <input type="number" id="calcRate" class="form-control form-control-sm" step="0.01" min="0" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Term (Months)</label>
                        <input type="number" id="calcTerm" class="form-control form-control-sm" min="1" max="60" placeholder="12">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Rate Type</label>
                        <select id="calcRateType" class="form-select form-select-sm">
                            <option value="flat">Flat Rate</option>
                            <option value="reducing_balance">Reducing Balance</option>
                            <option value="simple">Simple Interest</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm w-100" id="calcButton"><i class="bi bi-calculator me-1"></i>Calculate</button>

                    <div id="calcResults" class="mt-3 d-none">
                        <hr>
                        <div class="alert alert-primary py-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="fw-semibold">Monthly Installment:</small>
                                <strong class="fs-5" id="calcEMI">0 TZS</strong>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Total Interest:</small><strong class="text-warning" id="calcInterest">0 TZS</strong></div>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Total Repayment:</small><strong class="text-success" id="calcTotal">0 TZS</strong></div>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Number of Payments:</small><strong class="text-info" id="calcPayments">0</strong></div>
                        <div class="d-flex justify-content-between"><small class="text-muted">Processing Fee:</small><strong class="text-secondary" id="calcProcFee">0 TZS</strong></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Loan Types</h5></div>
                <div class="card-body">
                    @forelse($loanTypes as $type)
                        <div class="mb-3 pb-3 border-bottom">
                            <strong>{{ $type->name }}</strong>
                            <p class="text-muted small mb-1">{{ $type->description }}</p>
                            <span class="badge bg-light text-dark">{{ $type->rate_percentage }}% {{ ucfirst(str_replace('_', ' ', $type->rate_type)) }}</span>
                            @if($type->max_loan_amount)
                                <span class="badge bg-light text-dark">Max: {{ number_format($type->max_loan_amount, 0) }} TZS</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No loan types configured</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for cosigners
    $('#cosignerSelect').select2({
        placeholder: 'Select cosigners...',
        allowClear: true,
        width: '100%'
    });

    // Auto-fill rate when loan type changes
    $('#loanTypeSelect').on('change', function() {
        var rate = $(this).find(':selected').data('rate');
        if (rate && !$('#interestRate').val()) {
            $('#interestRate').val(rate);
        }
    });

    // ===== LOAN CALCULATOR =====
    function calculateEMI(principal, annualRate, termMonths) {
        if (annualRate <= 0) return principal / termMonths;
        var monthlyRate = (annualRate / 100) / 12;
        return principal * monthlyRate * Math.pow(1 + monthlyRate, termMonths) / (Math.pow(1 + monthlyRate, termMonths) - 1);
    }

    function calculateFlatInterest(principal, annualRate, termMonths) {
        return principal * (annualRate / 100) * (termMonths / 12);
    }

    $('#calcButton').on('click', function() {
        var principal = parseFloat($('#calcAmount').val()) || 0;
        var rate = parseFloat($('#calcRate').val()) || 0;
        var term = parseInt($('#calcTerm').val()) || 12;
        var rateType = $('#calcRateType').val();

        if (principal <= 0 || rate < 0 || term <= 0) {
            alert('Please enter valid values for all fields.');
            return;
        }

        var emi, totalInterest, totalRepayment;

        if (rateType === 'reducing_balance') {
            emi = calculateEMI(principal, rate, term);
            totalRepayment = emi * term;
            totalInterest = totalRepayment - principal;
        } else {
            totalInterest = calculateFlatInterest(principal, rate, term);
            totalRepayment = principal + totalInterest;
            emi = totalRepayment / term;
        }

        // Processing fee from selected loan type
        var loanTypeId = $('#loanTypeSelect').val();
        var procFeeRate = 0;
        $('#loanTypeSelect option').each(function() {
            if ($(this).val() == loanTypeId) {
                procFeeRate = parseFloat($(this).data('proc-fee')) || 0;
            }
        });
        var procFee = principal * (procFeeRate / 100);

        $('#calcEMI').text(emi.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcInterest').text(totalInterest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcTotal').text(totalRepayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcPayments').text(term);
        $('#calcProcFee').text(procFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcResults').removeClass('d-none');
    });

    // Sync calculator with form fields
    $('#loanAmount, #interestRate, #loanTerm, #loanTypeSelect').on('change input', function() {
        var amount = parseFloat($('#loanAmount').val()) || 0;
        var rate = parseFloat($('#interestRate').val()) || 0;
        var term = parseInt($('#loanTerm').val()) || 12;
        var rateType = $('#loanTypeSelect').find(':selected').data('rate-type') || 'flat';

        if (amount > 0) {
            $('#calcAmount').val(amount);
            $('#calcRate').val(rate);
            $('#calcTerm').val(term);
            $('#calcRateType').val(rateType);
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>.select2-container { width: 100% !important; }</style>
@endpush
