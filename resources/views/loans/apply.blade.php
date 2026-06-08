@php
$memberEligibilityJson = json_encode($memberEligibility);
$loanTypesJson = [];
foreach($loanTypes as $lt) {
    $loanTypesJson[$lt->id] = [
        'uses_eligibility_rules' => $lt->uses_eligibility_rules,
        'min_membership_months' => $lt->min_membership_months,
        'requires_active_status' => $lt->requires_active_status,
        'max_loan_hisa_multiplier' => $lt->max_loan_hisa_multiplier,
        'max_loan_amount' => $lt->max_loan_amount,
        'rate_percentage' => $lt->rate_percentage,
        'rate_type' => $lt->rate_type,
        'max_loan_term_months' => $lt->max_loan_term_months,
        'processing_fee' => $lt->processing_fee,
    ];
}
$loanTypesJsonEncoded = json_encode($loanTypesJson);
@endphp

@extends('layouts.app')

@section('title', 'Apply for Loan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Apply for Loan</h4>
            <p class="text-muted mb-0">Submit a loan application with collateral and cosigners</p>
        </div>
        <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    @if($errors->has('eligibility'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i><strong>Eligibility Check Failed:</strong> {{ $errors->first('eligibility') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <ul class="nav nav-tabs card-header-tabs" id="loanFormTabs">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('loans.create') }}"><i class="bi bi-pencil-square me-1"></i>Record Loan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#applyForm"><i class="bi bi-file-earmark-text me-1"></i>Apply Loan</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('loans.apply.submit') }}" enctype="multipart/form-data" id="applyLoanForm">
                        @csrf
                        <div class="row">
                            {{-- Applicant --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Applicant (Member) *</label>
                                <select name="member_id" id="memberSelect" class="form-select @error('member_id') is-invalid @enderror" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" data-status="{{ $member->status }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                    @endforeach
                                </select>
                                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Loan Type --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Loan Type *</label>
                                <select name="loan_type_id" id="loanTypeSelect" class="form-select @error('loan_type_id') is-invalid @enderror" required>
                                    <option value="">Select Loan Type</option>
                                    @foreach($loanTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('loan_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ $type->rate_percentage }}% {{ ucfirst(str_replace('_', ' ', $type->rate_type)) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('loan_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Eligibility Check Panel --}}
                            <div class="col-12 mb-3 d-none" id="eligibilityPanel">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white py-2 d-flex justify-content-between align-items-center">
                                        <strong><i class="bi bi-shield-check me-2"></i>Eligibility Check</strong>
                                        <span id="eligibilityBadge"></span>
                                    </div>
                                    <div class="card-body py-2" id="eligibilityBody">
                                        <p class="text-muted mb-0">Select a member and loan type to check eligibility.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Loan Amount (TZS) *</label>
                                <input type="number" name="loan_amount" id="loanAmount" class="form-control @error('loan_amount') is-invalid @enderror" step="100" min="1000" value="{{ old('loan_amount') }}" required>
                                @error('loan_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted d-none" id="maxLoanHint"></small>
                            </div>

                            {{-- Term --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Loan Term (Months) *</label>
                                <input type="number" name="loan_term_months" id="loanTerm" class="form-control @error('loan_term_months') is-invalid @enderror" min="1" max="60" value="{{ old('loan_term_months', 12) }}" required>
                                @error('loan_term_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Application Date --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Application Date *</label>
                                <input type="date" name="application_date" class="form-control" value="{{ old('application_date', date('Y-m-d')) }}" required>
                            </div>

                            {{-- Cosigners --}}
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Cosigners * <small class="text-muted">(at least 1 required)</small></label>
                                <select name="cosigner_ids[]" id="cosignerSelect" class="form-select @error('cosigner_ids') is-invalid @enderror" multiple required>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ collect(old('cosigner_ids'))->contains($member->id) ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                    @endforeach
                                </select>
                                @error('cosigner_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple. Selected cosigners must approve before officers review your application.</small>
                            </div>

                            {{-- Reason --}}
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Reason for Loan *</label>
                                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" required placeholder="Explain the purpose of this loan...">{{ old('reason') }}</textarea>
                                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Collateral Section --}}
                            <div class="col-12">
                                <hr class="my-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-primary mb-0"><i class="bi bi-shield-lock me-2"></i>Collateral Information</h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addCollateralBtn"><i class="bi bi-plus-lg me-1"></i>Add Collateral</button>
                                </div>
                            </div>

                            <div class="col-12" id="collateralContainer">
                                {{-- Collateral rows will be added here dynamically --}}
                                <div class="collateral-row border rounded p-3 mb-3" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-muted">Collateral #1</strong>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-semibold">Description *</label>
                                            <textarea name="collaterals[0][description]" class="form-control" rows="2" required placeholder="Describe the collateral (e.g., land title, vehicle, equipment)...">{{ old('collaterals.0.description') }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-0">
                                            <label class="form-label fw-semibold">Estimated Value (TZS) *</label>
                                            <input type="number" name="collaterals[0][estimated_value]" class="form-control" step="100" min="0" required>
                                        </div>
                                        <div class="col-md-6 mb-0">
                                            <label class="form-label fw-semibold">Document</label>
                                            <input type="file" name="collaterals[0][document]" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                            <small class="text-muted">JPG, PNG, PDF, max 5MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="bi bi-send me-2"></i>Submit Application</button>
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
                    <small class="opacity-75">Plan your loan before applying</small>
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

            {{-- Approval Workflow Info --}}
            <div class="card border-info">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Approval Process</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Submit application with collateral</li>
                        <li class="mb-2">Selected cosigners approve</li>
                        <li class="mb-2">Treasurer reviews and approves</li>
                        <li class="mb-2">Secretary reviews and approves</li>
                        <li class="mb-2">Chairman gives final approval</li>
                        <li>Loan is disbursed</li>
                    </ol>
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

    // ===== DATA =====
    var memberEligibility = {!! $memberEligibilityJson !!};
    var loanTypes = {!! $loanTypesJsonEncoded !!};
    var collateralCounter = 1;

    // ===== ELIGIBILITY CHECK =====
    function checkEligibility() {
        var memberId = $('#memberSelect').val();
        var loanTypeId = $('#loanTypeSelect').val();
        var loanAmount = parseFloat($('#loanAmount').val()) || 0;

        if (!memberId || !loanTypeId) {
            $('#eligibilityPanel').addClass('d-none');
            return;
        }

        var loanType = loanTypes[loanTypeId];
        if (!loanType || !loanType.uses_eligibility_rules) {
            $('#eligibilityPanel').addClass('d-none');
            return;
        }

        var memberData = memberEligibility[memberId];
        if (!memberData) {
            $('#eligibilityPanel').addClass('d-none');
            return;
        }

        $('#eligibilityPanel').removeClass('d-none');
        var errors = [];
        var checks = [];

        // Membership period
        if (loanType.min_membership_months && loanType.min_membership_months > 0) {
            var months = memberData.months_since_join;
            var pass = months >= loanType.min_membership_months;
            checks.push({
                label: 'Membership Period',
                detail: 'Required: ' + loanType.min_membership_months + ' months, Current: ' + months + ' months',
                pass: pass
            });
            if (!pass) errors.push('Membership period too short (' + months + ' / ' + loanType.min_membership_months + ' months)');
        }

        // Active status
        if (loanType.requires_active_status) {
            var pass = memberData.is_active;
            checks.push({
                label: 'Member Status',
                detail: 'Must be active, Current: ' + (memberData.is_active ? 'Active' : 'Inactive'),
                pass: pass
            });
            if (!pass) errors.push('Member status is not active');
        }

        // Hisa multiplier
        if (loanType.max_loan_hisa_multiplier && loanType.max_loan_hisa_multiplier > 0) {
            var hisaTotal = memberData.hisa_total;
            var maxLoan = hisaTotal * loanType.max_loan_hisa_multiplier;
            var pass = loanAmount <= maxLoan || loanAmount === 0;
            checks.push({
                label: 'Hisa-based Limit',
                detail: 'Hisa: ' + hisaTotal.toLocaleString() + ' TZS x ' + loanType.max_loan_hisa_multiplier + ' = Max: ' + maxLoan.toLocaleString() + ' TZS',
                pass: pass
            });
            if (!pass && loanAmount > 0) errors.push('Loan amount exceeds Hisa limit (' + loanAmount.toLocaleString() + ' / ' + maxLoan.toLocaleString() + ' TZS)');

            // Show max loan hint
            $('#maxLoanHint').removeClass('d-none').text('Max loan based on Hisa: ' + maxLoan.toLocaleString() + ' TZS');
        } else {
            $('#maxLoanHint').addClass('d-none');
        }

        // Max loan amount
        if (loanType.max_loan_amount && loanType.max_loan_amount > 0) {
            var pass = loanAmount <= loanType.max_loan_amount || loanAmount === 0;
            checks.push({
                label: 'Max Loan Amount',
                detail: 'Limit: ' + parseFloat(loanType.max_loan_amount).toLocaleString() + ' TZS',
                pass: pass
            });
            if (!pass && loanAmount > 0) errors.push('Exceeds max loan amount (' + loanAmount.toLocaleString() + ' / ' + parseFloat(loanType.max_loan_amount).toLocaleString() + ' TZS)');
        }

        // Render
        var html = '';
        checks.forEach(function(check) {
            var icon = check.pass ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
            html += '<div class="d-flex justify-content-between align-items-center mb-1 py-1 border-bottom">';
            html += '<div><small class="fw-semibold">' + check.label + '</small><br><small class="text-muted">' + check.detail + '</small></div>';
            html += '<div>' + icon + '</div>';
            html += '</div>';
        });

        if (errors.length > 0) {
            $('#eligibilityBadge').html('<span class="badge bg-danger">Not Eligible</span>');
            html += '<div class="alert alert-danger py-2 mt-2 mb-0"><small><strong>Issues:</strong> ' + errors.join('; ') + '</small></div>';
            $('#submitBtn').prop('disabled', true).addClass('btn-secondary').removeClass('btn-primary');
        } else {
            $('#eligibilityBadge').html('<span class="badge bg-success">Eligible</span>');
            $('#submitBtn').prop('disabled', false).addClass('btn-primary').removeClass('btn-secondary');
        }

        $('#eligibilityBody').html(html);
    }

    // Trigger eligibility check on member, loan type, or amount change
    $('#memberSelect, #loanTypeSelect, #loanAmount').on('change input', function() {
        checkEligibility();
    });

    // ===== MULTIPLE COLLATERALS =====
    $('#addCollateralBtn').on('click', function() {
        var index = collateralCounter++;
        var html = '<div class="collateral-row border rounded p-3 mb-3" data-index="' + index + '">';
        html += '<div class="d-flex justify-content-between align-items-center mb-2">';
        html += '<strong class="text-muted">Collateral #' + (index + 1) + '</strong>';
        html += '<button type="button" class="btn btn-outline-danger btn-sm remove-collateral"><i class="bi bi-trash"></i></button>';
        html += '</div>';
        html += '<div class="row">';
        html += '<div class="col-12 mb-3">';
        html += '<label class="form-label fw-semibold">Description *</label>';
        html += '<textarea name="collaterals[' + index + '][description]" class="form-control" rows="2" required placeholder="Describe the collateral..."></textarea>';
        html += '</div>';
        html += '<div class="col-md-6 mb-0">';
        html += '<label class="form-label fw-semibold">Estimated Value (TZS) *</label>';
        html += '<input type="number" name="collaterals[' + index + '][estimated_value]" class="form-control" step="100" min="0" required>';
        html += '</div>';
        html += '<div class="col-md-6 mb-0">';
        html += '<label class="form-label fw-semibold">Document</label>';
        html += '<input type="file" name="collaterals[' + index + '][document]" class="form-control" accept=".jpg,.jpeg,.png,.pdf">';
        html += '<small class="text-muted">JPG, PNG, PDF, max 5MB</small>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('#collateralContainer').append(html);
    });

    $(document).on('click', '.remove-collateral', function() {
        $(this).closest('.collateral-row').remove();
        // Renumber
        $('#collateralContainer .collateral-row').each(function(i) {
            $(this).find('strong.text-muted').text('Collateral #' + (i + 1));
        });
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
        var procFee = 0;
        if (loanTypeId && loanTypes[loanTypeId] && loanTypes[loanTypeId].processing_fee) {
            procFee = principal * (loanTypes[loanTypeId].processing_fee / 100);
        }

        $('#calcEMI').text(emi.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcInterest').text(totalInterest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcTotal').text(totalRepayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcPayments').text(term);
        $('#calcProcFee').text(procFee.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' TZS');
        $('#calcResults').removeClass('d-none');
    });

    // Sync calculator with form fields
    $('#loanAmount, #loanTerm, #loanTypeSelect').on('change input', function() {
        var amount = parseFloat($('#loanAmount').val()) || 0;
        var loanTypeId = $('#loanTypeSelect').val();
        var rate = 0;
        var rateType = 'flat';
        if (loanTypeId && loanTypes[loanTypeId]) {
            rate = loanTypes[loanTypeId].rate_percentage;
            rateType = loanTypes[loanTypeId].rate_type;
        }
        var term = parseInt($('#loanTerm').val()) || 12;

        if (amount > 0) {
            $('#calcAmount').val(amount);
            $('#calcRate').val(rate);
            $('#calcTerm').val(term);
            $('#calcRateType').val(rateType);
        }

        // Also re-check eligibility if amount changed
        checkEligibility();
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>.select2-container { width: 100% !important; }</style>
@endpush
