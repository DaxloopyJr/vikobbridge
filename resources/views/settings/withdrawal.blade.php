@extends('layouts.app')

@section('title', 'Hisa Withdrawal Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="fw-bold mb-0">Settings</h4>
            <p class="text-muted mb-0">Configure Hisa withdrawal rules for your group</p>
        </div>
    </div>

    @include('partials.settings_tabs')

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-box-arrow-left me-2 text-warning"></i>Hisa Withdrawal Configuration</h5>
                    <small class="text-muted">Set rules for when members leave the group</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.withdrawal.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Withdrawal Percentage (%)</label>
                                <div class="input-group">
                                    <input type="number" name="hisa_withdrawal_percent" class="form-control form-control-lg" step="0.01" min="0" max="100" value="{{ $withdrawalPercent }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Percentage of total Hisa contributions a member is allowed to withdraw when leaving the group. 100% means they can withdraw all their Hisa.</small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Withdrawal Deduction (%)</label>
                                <div class="input-group">
                                    <input type="number" name="hisa_withdrawal_deduction_percent" class="form-control form-control-lg" step="0.01" min="0" max="100" value="{{ $withdrawalDeductionPercent }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Percentage deducted from total Hisa when a member withdraws. e.g. 10% deduction means member receives 90% of their Hisa.</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>How It Works</h6>
                            <p class="mb-2">When a member decides to leave the group, their Hisa withdrawal is calculated as:</p>
                            <ol class="mb-2">
                                <li>Total Hisa contributions for the member are summed up</li>
                                <li>The <strong>Withdrawal Percentage</strong> determines how much of that total they can claim (e.g. 100% = full amount)</li>
                                <li>The <strong>Deduction Percentage</strong> is then subtracted as a group fee (e.g. 10% deduction = 10% kept by the group)</li>
                                <li>Final amount = (Total Hisa &times; Withdrawal %) &times; (100% - Deduction %)</li>
                            </ol>
                            <p class="mb-0"><strong>Example:</strong> Member has 500,000 TZS in Hisa. Withdrawal % = 100%, Deduction % = 10%.<br>They receive: 500,000 &times; 100% &times; 90% = <strong>450,000 TZS</strong>. Group keeps 50,000 TZS.</p>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Quick Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Sample Hisa Amount (TZS)</label>
                        <input type="number" id="previewHisa" class="form-control" step="1000" min="0" value="500000" placeholder="500000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Withdrawal %</label>
                        <input type="text" id="previewWithdrawal" class="form-control bg-light" value="{{ $withdrawalPercent }}%" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deduction %</label>
                        <input type="text" id="previewDeduction" class="form-control bg-light" value="{{ $withdrawalDeductionPercent }}%" readonly>
                    </div>
                    <button type="button" class="btn btn-warning btn-sm w-100" id="previewCalc"><i class="bi bi-calculator me-1"></i>Calculate</button>

                    <div id="previewResult" class="mt-3 d-none">
                        <hr>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Total Hisa:</small><strong id="previewTotal">0 TZS</strong></div>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Eligible for Withdrawal:</small><strong class="text-primary" id="previewEligible">0 TZS</strong></div>
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Group Deduction:</small><strong class="text-danger" id="previewDeducted">0 TZS</strong></div>
                        <div class="d-flex justify-content-between"><small class="text-muted">Member Receives:</small><strong class="text-success" id="previewReceives">0 TZS</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var withdrawalPercent = parseFloat('{{ $withdrawalPercent }}') || 100;
    var deductionPercent = parseFloat('{{ $withdrawalDeductionPercent }}') || 0;

    $('#previewCalc').on('click', function() {
        var hisa = parseFloat($('#previewHisa').val()) || 0;
        if (hisa <= 0) {
            alert('Enter a valid Hisa amount');
            return;
        }

        var eligible = hisa * (withdrawalPercent / 100);
        var deducted = eligible * (deductionPercent / 100);
        var receives = eligible - deducted;

        $('#previewTotal').text(hisa.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' TZS');
        $('#previewEligible').text(eligible.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' TZS');
        $('#previewDeducted').text(deducted.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' TZS');
        $('#previewReceives').text(receives.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) + ' TZS');
        $('#previewResult').removeClass('d-none');
    });
});
</script>
@endpush
