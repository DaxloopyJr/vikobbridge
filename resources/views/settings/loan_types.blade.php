@extends('layouts.app')

@section('title', 'Loan Types')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="fw-bold mb-0">Settings</h4>
            <p class="text-muted mb-0">Manage your group configuration</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLoanTypeModal"><i class="bi bi-plus-lg me-2"></i>Add Loan Type</button>
    </div>

    @include('partials.settings_tabs')

    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Loan Types</h5>
            <small class="text-muted">Configure interest rates, processing fees, eligibility rules, and loan limits</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="loan-types-table">
                <thead>
                    <tr><th>Name</th><th>Rate</th><th>Proc. Fee</th><th>Eligibility</th><th>Max Term</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addLoanTypeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Loan Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.loan_types.store') }}">@csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. Emergency Loan" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Max Loan Amount (TZS)</label><input type="number" name="max_loan_amount" class="form-control" step="0.01" min="0"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Rate Type *</label>
                            <select name="rate_type" class="form-select" required>
                                <option value="flat">Flat Rate</option>
                                <option value="reducing_balance">Reducing Balance</option>
                                <option value="simple">Simple Interest</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label">Rate Percentage *</label><input type="number" name="rate_percentage" class="form-control" step="0.01" min="0" max="100" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Processing Fee (%)</label><input type="number" name="processing_fee" class="form-control" step="0.01" min="0" max="100" placeholder="0"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Max Term (Months) *</label><input type="number" name="max_loan_term_months" class="form-control" min="1" max="120" required></div>
                        <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="ltActive" checked><label class="form-check-label" for="ltActive">Active</label></div></div>
                    </div>

                    {{-- Eligibility Rules --}}
                    <hr>
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-shield-check me-2"></i>Eligibility Rules</h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="uses_eligibility_rules" class="form-check-input" value="1" id="ltEligibilityToggle" onchange="document.getElementById('eligibilityFields').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label" for="ltEligibilityToggle">Use eligibility rules for this loan type</label>
                        </div>
                    </div>
                    <div id="eligibilityFields" class="d-none">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min. Membership (Months)</label>
                                <input type="number" name="min_membership_months" class="form-control" min="0" placeholder="e.g. 6">
                                <small class="text-muted">Minimum months since member registration</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="requires_active_status" class="form-check-input" value="1" id="ltActiveStatus" checked>
                                    <label class="form-check-label" for="ltActiveStatus">Requires active member status</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Loan vs Hisa (x)</label>
                                <input type="number" name="max_loan_hisa_multiplier" class="form-control" step="0.01" min="0" placeholder="e.g. 3">
                                <small class="text-muted">Times total Hisa contributed this year</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

@foreach($loanTypes as $lt)
<div class="modal fade" id="editLoanTypeModal{{ $lt->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit {{ $lt->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.loan_types.update', $lt) }}">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ $lt->name }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Max Loan Amount (TZS)</label><input type="number" name="max_loan_amount" class="form-control" step="0.01" min="0" value="{{ $lt->max_loan_amount }}"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ $lt->description }}</textarea></div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label class="form-label">Rate Type *</label>
                            <select name="rate_type" class="form-select" required>
                                @foreach(['flat' => 'Flat Rate', 'reducing_balance' => 'Reducing Balance', 'simple' => 'Simple Interest'] as $val => $label)
                                    <option value="{{ $val }}" {{ $lt->rate_type == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3"><label class="form-label">Rate Percentage *</label><input type="number" name="rate_percentage" class="form-control" step="0.01" min="0" max="100" value="{{ $lt->rate_percentage }}" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Processing Fee (%)</label><input type="number" name="processing_fee" class="form-control" step="0.01" min="0" max="100" value="{{ $lt->processing_fee }}"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Max Term (Months) *</label><input type="number" name="max_loan_term_months" class="form-control" min="1" max="120" value="{{ $lt->max_loan_term_months }}" required></div>
                        <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="ltActive{{ $lt->id }}" {{ $lt->is_active ? 'checked' : '' }}><label class="form-check-label" for="ltActive{{ $lt->id }}">Active</label></div></div>
                    </div>

                    {{-- Eligibility Rules --}}
                    <hr>
                    <h6 class="fw-bold text-info mb-3"><i class="bi bi-shield-check me-2"></i>Eligibility Rules</h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="uses_eligibility_rules" class="form-check-input" value="1" id="ltEligibilityToggle{{ $lt->id }}" {{ $lt->uses_eligibility_rules ? 'checked' : '' }} onchange="document.getElementById('eligibilityFields{{ $lt->id }}').classList.toggle('d-none', !this.checked)">
                            <label class="form-check-label" for="ltEligibilityToggle{{ $lt->id }}">Use eligibility rules for this loan type</label>
                        </div>
                    </div>
                    <div id="eligibilityFields{{ $lt->id }}" class="{{ $lt->uses_eligibility_rules ? '' : 'd-none' }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Min. Membership (Months)</label>
                                <input type="number" name="min_membership_months" class="form-control" min="0" value="{{ $lt->min_membership_months }}" placeholder="e.g. 6">
                                <small class="text-muted">Minimum months since member registration</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="requires_active_status" class="form-check-input" value="1" id="ltActiveStatus{{ $lt->id }}" {{ $lt->requires_active_status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ltActiveStatus{{ $lt->id }}">Requires active member status</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Max Loan vs Hisa (x)</label>
                                <input type="number" name="max_loan_hisa_multiplier" class="form-control" step="0.01" min="0" value="{{ $lt->max_loan_hisa_multiplier }}" placeholder="e.g. 3">
                                <small class="text-muted">Times total Hisa contributed this year</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#loan-types-table').DataTable({
        ajax: '{{ route("settings.loan_types.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'rate', name: 'rate', orderable: false },
            { data: 'processing_fee', name: 'processing_fee' },
            { data: 'eligibility', name: 'eligibility', orderable: false },
            { data: 'term', name: 'term' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
