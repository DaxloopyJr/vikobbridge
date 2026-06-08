@extends('layouts.app')

@section('title', 'Edit Collection')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da; border-radius: 8px; height: 42px; padding: 6px 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px; right: 8px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; padding-left: 4px; }
    .select2-dropdown { border-radius: 8px; border: 1px solid #ced4da; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #1a5f2a; }
    .select2-container { width: 100% !important; }
    .hisa-card { border-left: 4px solid #fd7e14; background: linear-gradient(135deg, #fff8f0 0%, #fff 100%); }
    .hisa-card .card-header { background: transparent; border-bottom: 1px solid #ffe0b2; }
    .new-year-toggle { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; background: #fff3e0; border: 1px solid #ffe0b2; }
    .new-year-toggle .form-check-input:checked { background-color: #fd7e14; border-color: #fd7e14; }
    .new-year-toggle .form-check-input:focus { box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25); }
    .balance-field { animation: slideDown 0.3s ease; }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); max-height: 0; } to { opacity: 1; transform: translateY(0); max-height: 200px; } }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Edit Collection</h4>
                <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit Collection</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('collections.update', $collection) }}" id="collectionForm">
                        @csrf @method('PUT')
                        <div class="row">
                            {{-- Member - Select2 Searchable --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Member *</label>
                                <select name="member_id" id="member" class="form-select" required>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ $collection->member_id == $member->id ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Collection Fund - Select2 --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Collection Fund *</label>
                                <select name="collection_fund_id" id="collectionFund" class="form-select" required>
                                    @foreach($funds as $fund)
                                        <option value="{{ $fund->id }}" data-slug="{{ $fund->slug }}" {{ $collection->collection_fund_id == $fund->id ? 'selected' : '' }}>{{ $fund->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Amount (TZS) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">TZS</span>
                                    <input type="number" name="amount" class="form-control" step="0.01" value="{{ $collection->amount }}" required>
                                </div>
                            </div>

                            {{-- Payment Date --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Payment Date *</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ $collection->payment_date->format('Y-m-d') }}" required>
                            </div>

                            {{-- Payment Channel --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Payment Channel *</label>
                                <select name="payment_channel" class="form-select" required>
                                    @foreach(['cash', 'bank', 'mobile_money', 'selcom', 'other'] as $ch)
                                        <option value="{{ $ch }}" {{ $collection->payment_channel == $ch ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $ch)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Month --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Month *</label>
                                <select name="month" id="month" class="form-select" required>
                                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                        <option value="{{ $m }}" {{ old('month', $collection->month) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Year --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Year *</label>
                                <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $collection->year) }}" readonly style="background:#f8f9fa; font-weight:600;">
                                <small class="text-muted">From calendar year</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Control Number</label>
                                <input type="text" name="payment_control_number" class="form-control" value="{{ $collection->payment_control_number }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transaction Reference</label>
                                <input type="text" name="transaction_reference" class="form-control" value="{{ $collection->transaction_reference }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ $collection->notes }}</textarea>
                            </div>
                        </div>

                        {{-- Hisa Conditional Section --}}
                        @php
                            $selectedFund = $funds->firstWhere('id', $collection->collection_fund_id);
                            $isHisa = $selectedFund && $selectedFund->slug === 'hisa';
                        @endphp
                        <div class="card hisa-card mb-3" id="hisaSection" style="{{ $isHisa ? '' : 'display:none;' }}">
                            <div class="card-header py-3">
                                <h5 class="mb-0 fw-bold text-warning"><i class="bi bi-piggy-bank me-2"></i>Hisa (Savings) - Additional</h5>
                            </div>
                            <div class="card-body">
                                <div class="new-year-toggle mb-3">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_new_calendar_year" id="isNewCalendarYear" value="1" {{ $collection->is_new_calendar_year ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="isNewCalendarYear">Is this the first/begin of a new calendar year?</label>
                                    </div>
                                </div>
                                <div class="balance-field" id="balanceField" style="{{ $collection->is_new_calendar_year ? '' : 'display:none;' }}">
                                    <label class="form-label fw-semibold text-warning"><i class="bi bi-arrow-left-circle me-1"></i>Balance Carried Forward (TZS)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning bg-opacity-25">TZS</span>
                                        <input type="number" name="balance_carried_forward" id="balanceCarriedForward" class="form-control" step="0.01" min="0" value="{{ $collection->balance_carried_forward }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Update</button>
                            <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#member').select2({ placeholder: 'Search member...', allowClear: false });
    $('#collectionFund').select2({ placeholder: 'Select fund...', allowClear: false });

    // Show/hide Hisa section based on fund
    $('#collectionFund').on('change', function() {
        var slug = $(this).find(':selected').data('slug');
        if (slug === 'hisa') {
            $('#hisaSection').slideDown(200);
        } else {
            $('#hisaSection').slideUp(200);
            $('#isNewCalendarYear').prop('checked', false);
            $('#balanceField').hide();
            $('#balanceCarriedForward').val('');
        }
    });

    // Balance field toggle
    $('#isNewCalendarYear').on('change', function() {
        if ($(this).is(':checked')) {
            $('#balanceField').slideDown(200);
        } else {
            $('#balanceField').slideUp(200);
            $('#balanceCarriedForward').val('');
        }
    });
});
</script>
@endpush
