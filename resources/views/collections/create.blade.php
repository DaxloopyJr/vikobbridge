@extends('layouts.app')

@section('title', 'Record Collection')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: 8px;
        height: 42px;
        padding: 6px 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 4px;
    }
    .select2-dropdown {
        border-radius: 8px;
        border: 1px solid #ced4da;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1a5f2a;
    }
    .select2-container { width: 100% !important; }

    /* Hisa conditional card */
    .hisa-card {
        border-left: 4px solid #fd7e14;
        background: linear-gradient(135deg, #fff8f0 0%, #fff 100%);
    }
    .hisa-card .card-header {
        background: transparent;
        border-bottom: 1px solid #ffe0b2;
    }
    .new-year-toggle {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border-radius: 10px;
        background: #fff3e0;
        border: 1px solid #ffe0b2;
    }
    .new-year-toggle .form-check-input:checked {
        background-color: #fd7e14;
        border-color: #fd7e14;
    }
    .new-year-toggle .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
    }
    .balance-field {
        animation: slideDown 0.3s ease;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); max-height: 0; }
        to { opacity: 1; transform: translateY(0); max-height: 200px; }
    }
    .fund-badge {
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    .fund-hisa { background: #e8f5e9; color: #2e7d32; }
    .fund-jamii { background: #e3f2fd; color: #1565c0; }
    .fund-ada { background: #f3e5f5; color: #6a1b9a; }
    .fund-faini { background: #ffebee; color: #c62828; }
    .fund-default { background: #f5f5f5; color: #616161; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0">Record Collection</h4>
                    <p class="text-muted mb-0">Record a new collection payment</p>
                </div>
                <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>

            <form method="POST" action="{{ route('collections.store') }}" id="collectionForm">
                @csrf

                {{-- Top info bar --}}
                @if($currentCalendarYear)
                <div class="alert alert-info d-flex align-items-center mb-3">
                    <i class="bi bi-calendar3 me-2"></i>
                    <div>
                        <strong>Current Calendar Year:</strong> {{ $currentCalendarYear->name }}
                        <small class="text-muted ms-2">({{ \Carbon\Carbon::parse($currentCalendarYear->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($currentCalendarYear->end_date)->format('M d, Y') }})</small>
                    </div>
                </div>
                @endif

                {{-- Main Card --}}
                <div class="card mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2 text-primary"></i>Collection Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            {{-- Member - Select2 Searchable --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Member *</label>
                                <select name="member_id" id="member" class="form-select @error('member_id') is-invalid @enderror" required>
                                    <option value="">Search member...</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('member_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Collection Fund - Select2 --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Collection Fund *</label>
                                <select name="collection_fund_id" id="collectionFund" class="form-select @error('collection_fund_id') is-invalid @enderror" required>
                                    <option value="">Select fund...</option>
                                    @foreach($funds as $fund)
                                        @php
                                            $badgeClass = match($fund->slug) {
                                                'hisa' => 'fund-hisa',
                                                'jamii' => 'fund-jamii',
                                                'ada' => 'fund-ada',
                                                'faini' => 'fund-faini',
                                                default => 'fund-default',
                                            };
                                        @endphp
                                        <option
                                            value="{{ $fund->id }}"
                                            data-slug="{{ $fund->slug }}"
                                            data-name="{{ $fund->name }}"
                                            {{ old('collection_fund_id') == $fund->id ? 'selected' : '' }}
                                        >
                                            {{ $fund->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="selectedFundBadge" class="mt-2" style="display:none;">
                                    <span class="fund-badge" id="fundBadgeText"></span>
                                </div>
                                @error('collection_fund_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Amount (TZS) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">TZS</span>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount') }}" placeholder="0.00" required>
                                </div>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Payment Date --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Payment Date *</label>
                                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Payment Channel --}}
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Payment Channel *</label>
                                <select name="payment_channel" class="form-select @error('payment_channel') is-invalid @enderror" required>
                                    <option value="cash" {{ old('payment_channel', 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank" {{ old('payment_channel') == 'bank' ? 'selected' : '' }}>Bank</option>
                                    <option value="mobile_money" {{ old('payment_channel') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="selcom" {{ old('payment_channel') == 'selcom' ? 'selected' : '' }}>Selcom</option>
                                    <option value="other" {{ old('payment_channel') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Month --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Month *</label>
                                <select name="month" id="month" class="form-select @error('month') is-invalid @enderror" required>
                                    <option value="">Select month...</option>
                                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                                        <option value="{{ $month }}" {{ old('month', date('F')) == $month ? 'selected' : '' }}>{{ $month }}</option>
                                    @endforeach
                                </select>
                                @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Year --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Year *</label>
                                <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror"
                                    value="{{ old('year', $currentCalendarYear->year ?? date('Y')) }}"
                                    min="2000" max="2100" readonly style="background:#f8f9fa; font-weight:600;">
                                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Auto-filled from selected calendar year</small>
                            </div>
                        </div>

                        {{-- Calendar Year Selector (affects year field) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Calendar Year</label>
                            <select name="calendar_year_id" id="calendarYearSelect" class="form-select">
                                <option value="">Use current year</option>
                                @foreach($calendarYears as $cy)
                                    <option value="{{ $cy->id }}" data-year="{{ $cy->year }}" {{ ($currentCalendarYear && $currentCalendarYear->id == $cy->id) || old('calendar_year_id') == $cy->id ? 'selected' : '' }}>
                                        {{ $cy->name }} ({{ $cy->year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Control Number</label>
                                <input type="text" name="payment_control_number" class="form-control" value="{{ old('payment_control_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transaction Reference</label>
                                <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Hisa-Specific Conditional Card --}}
                <div class="card hisa-card mb-4" id="hisaSection" style="display:none;">
                    <div class="card-header py-3">
                        <h5 class="mb-0 fw-bold text-warning">
                            <i class="bi bi-piggy-bank me-2"></i>Hisa (Savings) - Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- New Calendar Year Toggle --}}
                        <div class="new-year-toggle mb-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="is_new_calendar_year" id="isNewCalendarYear" value="1" {{ old('is_new_calendar_year') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isNewCalendarYear">
                                    Is this the first/begin of a new calendar year?
                                </label>
                            </div>
                        </div>

                        {{-- Balance Carried Forward (shown when toggle is on) --}}
                        <div class="balance-field" id="balanceField" style="display:none;">
                            <label class="form-label fw-semibold text-warning">
                                <i class="bi bi-arrow-left-circle me-1"></i>Balance Carried Forward from Previous Year (TZS)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning bg-opacity-25">TZS</span>
                                <input type="number" name="balance_carried_forward" id="balanceCarriedForward" class="form-control form-control-lg" step="0.01" min="0" value="{{ old('balance_carried_forward', '0.00') }}" placeholder="0.00">
                            </div>
                            <small class="text-muted">Enter the total savings balance from the previous calendar year that is being carried forward.</small>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-2"></i>Record Collection
                    </button>
                    <a href="{{ route('collections.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for Member (searchable)
    var $member = $('#member').select2({
        placeholder: 'Search for a member...',
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for Collection Fund
    var $fund = $('#collectionFund').select2({
        placeholder: 'Select collection fund...',
        allowClear: true,
        width: '100%'
    });

    // Fund badge classes
    var fundBadgeClasses = {
        'hisa': 'fund-hisa',
        'jamii': 'fund-jamii',
        'ada': 'fund-ada',
        'faini': 'fund-faini'
    };

    // Handle fund change - show/hide Hisa section
    $fund.on('change', function() {
        var selected = $(this).find(':selected');
        var slug = selected.data('slug');
        var name = selected.data('name');

        // Show/hide fund badge
        var $badge = $('#selectedFundBadge');
        var $badgeText = $('#fundBadgeText');
        if (slug && name) {
            var badgeClass = fundBadgeClasses[slug] || 'fund-default';
            $badgeText.text(name).attr('class', 'fund-badge ' + badgeClass);
            $badge.show();
        } else {
            $badge.hide();
        }

        // Show/hide Hisa conditional section
        var $hisaSection = $('#hisaSection');
        if (slug === 'hisa') {
            $hisaSection.slideDown(200);
        } else {
            $hisaSection.slideUp(200);
            // Reset Hisa fields when hidden
            $('#isNewCalendarYear').prop('checked', false);
            $('#balanceField').hide();
            $('#balanceCarriedForward').val('');
        }
    });

    // Handle "New Calendar Year" toggle
    $('#isNewCalendarYear').on('change', function() {
        if ($(this).is(':checked')) {
            $('#balanceField').slideDown(200);
        } else {
            $('#balanceField').slideUp(200);
            $('#balanceCarriedForward').val('');
        }
    });

    // Calendar Year selector updates the Year field
    $('#calendarYearSelect').on('change', function() {
        var selected = $(this).find(':selected');
        var year = selected.data('year');
        if (year) {
            $('#year').val(year);
        } else {
            // Default to current year if "Use current year" selected
            $('#year').val('{{ date('Y') }}');
        }
    });

    // Trigger fund change on page load (for old values)
    var oldFundId = '{{ old('collection_fund_id') }}';
    if (oldFundId) {
        $fund.val(oldFundId).trigger('change');
    }

    // Trigger toggle on page load (for old values)
    @if(old('is_new_calendar_year'))
        $('#balanceField').show();
    @endif
});
</script>
@endpush
