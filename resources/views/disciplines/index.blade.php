@php
$statusFilter = request('status', 'all');
$currentYear = $currentCalendarYear->year ?? date('Y');

// Determine view mode based on disciplineType
$isAll = is_null($disciplineType);
$isFines = $disciplineType === 'fine';
$isPenalties = $disciplineType === 'penalty';

// Page title and heading
$pageTitle = $isFines ? 'Fines' : ($isPenalties ? 'Penalties' : 'Fines & Penalties');
$headingIcon = $isPenalties ? 'bi-hammer' : 'bi-exclamation-triangle-fill';
$headingColor = $isPenalties ? 'text-purple' : 'text-danger';
$buttonText = $isFines ? 'Assign Fine' : ($isPenalties ? 'Assign Penalty' : 'Assign Fine / Penalty');
$modalTitle = $buttonText;
@endphp

@extends('layouts.app')

@section('title', 'Disciplines — ' . $pageTitle)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi {{ $headingIcon }} {{ $headingColor }} me-2"></i>{{ $pageTitle }}</h4>
            <p class="text-muted mb-0">
                {{ $isFines ? 'Manage member fines' : ($isPenalties ? 'Manage member penalties' : 'Manage member disciplinary fines and penalties') }}
            </p>
        </div>
        @can('create_collections')
        <button class="btn btn-{{ $isPenalties ? 'purple' : 'danger' }}" data-bs-toggle="modal" data-bs-target="#assignFineModal">
            <i class="bi bi-plus-lg me-2"></i>{{ $buttonText }}
        </button>
        @endcan
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-warning border-2 bg-warning bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h6 class="mb-1 text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">Pending</h6>
                    <h4 class="fw-bold mb-0 text-warning">{{ number_format($summary['pendingCount'], 0) }}</h4>
                    <small class="text-muted">{{ number_format($summary['pendingAmount'], 0) }} TZS</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success border-2 bg-success bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h6 class="mb-1 text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">Paid</h6>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($summary['paidCount'], 0) }}</h4>
                    <small class="text-muted">{{ number_format($summary['paidAmount'], 0) }} TZS</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary border-2 bg-secondary bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h6 class="mb-1 text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">Skipped</h6>
                    <h4 class="fw-bold mb-0 text-secondary">{{ number_format($summary['skippedCount'], 0) }}</h4>
                    <small class="text-muted">{{ number_format($summary['skippedAmount'], 0) }} TZS</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger border-2 bg-danger bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h6 class="mb-1 text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">Total Outstanding</h6>
                    <h4 class="fw-bold mb-0 text-danger">{{ number_format($summary['totalOutstanding'], 0) }} TZS</h4>
                    <small class="text-muted">Pending balance</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Type Filter Tabs (only show "All" view when not filtered) --}}
    <ul class="nav nav-tabs-colored mb-0" id="typeTabs">
        <li class="nav-item">
            <a class="nav-link tab-dark {{ $isAll ? 'active' : '' }}" href="{{ route('disciplines.index') }}">
                <i class="bi bi-grid me-1"></i>All
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-red {{ $isFines ? 'active' : '' }}" href="{{ route('disciplines.fines') }}">
                <i class="bi bi-exclamation-triangle me-1"></i>Fines
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link tab-purple {{ $isPenalties ? 'active' : '' }}" href="{{ route('disciplines.penalties') }}">
                <i class="bi bi-hammer me-1"></i>Penalties
            </a>
        </li>
    </ul>

    {{-- DataTable --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="disciplines-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        @if($isAll)
                        <th>Type</th>
                        @endif
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Assign Fine / Penalty Modal --}}
<div class="modal fade" id="assignFineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-{{ $isPenalties ? 'purple' : 'danger' }} text-white">
                <h5 class="modal-title fw-bold"><i class="bi {{ $headingIcon }} me-2"></i>{{ $modalTitle }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('disciplines.store') }}" id="assignFineForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- Member --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Member <span class="text-danger">*</span></label>
                            <select name="member_id" id="fineMember" class="form-select" required>
                                <option value="">Search member...</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Type: Fine vs Penalty --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="type" id="fineType" class="form-select" required>
                                @if($isAll)
                                    <option value="">Select category...</option>
                                    <option value="fine">Fine</option>
                                    <option value="penalty">Penalty</option>
                                @elseif($isFines)
                                    <option value="fine" selected>Fine</option>
                                @elseif($isPenalties)
                                    <option value="penalty" selected>Penalty</option>
                                @endif
                            </select>
                        </div>

                        {{-- Fine Type --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ $isPenalties ? 'Penalty' : 'Fine' }} Type <span class="text-danger">*</span></label>
                            <select name="fine_type" class="form-select" required>
                                <option value="">Select type...</option>
                                <option value="late_payment">Late Payment</option>
                                <option value="absence">Absence from Meeting</option>
                                <option value="late_contribution">Late Contribution</option>
                                <option value="misconduct">Misconduct</option>
                                <option value="violation">Rule Violation</option>
                                <option value="damage">Property Damage</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        {{-- Month --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                    <option value="{{ $m }}" {{ date('F') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Year --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ $currentYear }}" readonly style="background:#f8f9fa; font-weight:600;">
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Amount (TZS) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">TZS</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                        </div>

                        {{-- Reason --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Reason / Description <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this {{ $isPenalties ? 'penalty' : 'fine' }} is being assigned..." required></textarea>
                        </div>

                        {{-- Notes --}}
                        <div class="col-12 mb-0">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-{{ $isPenalties ? 'purple' : 'danger' }}">
                        <i class="bi {{ $headingIcon }} me-2"></i>{{ $modalTitle }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Record Payment Modal --}}
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash me-2"></i>Record Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="paymentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount</label>
                        <input type="text" class="form-control" id="paymentFineAmount" readonly style="background:#f8f9fa; font-weight:600;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Already Paid</label>
                        <input type="text" class="form-control" id="paymentPaidAmount" readonly style="background:#f8f9fa;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Balance</label>
                        <input type="text" class="form-control text-danger fw-bold" id="paymentBalance" readonly style="background:#fff3f3;">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Payment Amount (TZS) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">TZS</span>
                            <input type="number" name="paid_amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required id="paymentAmountInput">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-2"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    var statusFilter = '{{ $statusFilter }}';
    var typeFilter = '{{ $disciplineType }}'; // 'fine', 'penalty', or ''

    // Build columns array dynamically based on view mode
    var columns = [
        { data: 'id', name: 'id' },
        { data: 'member', name: 'member', orderable: false },
    ];

    // Only show Type column in "All" view
    @if($isAll)
    columns.push({ data: 'type', name: 'type', orderable: false });
    @endif

    columns.push(
        { data: 'amount', name: 'amount' },
        { data: 'paid', name: 'paid', orderable: false },
        { data: 'balance', name: 'balance', orderable: false },
        { data: 'month', name: 'month', orderable: false },
        { data: 'status', name: 'status', orderable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    );

    // Initialize DataTable
    var table = $('#disciplines-table').DataTable({
        ajax: {
            url: '{{ route("disciplines.data") }}',
            data: function(d) {
                if (statusFilter && statusFilter !== 'all') {
                    d.status = statusFilter;
                }
                if (typeFilter) {
                    d.type = typeFilter;
                }
            }
        },
        columns: columns,
        order: [[0, 'desc']],
        drawCallback: function(settings) {
            initTooltips();
            bindPaymentButtons();
        }
    });

    // Initialize tooltips
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(t) {
            return new bootstrap.Tooltip(t);
        });
    }

    // Bind payment buttons
    function bindPaymentButtons() {
        $('.btn-record-payment').off('click').on('click', function() {
            var id = $(this).data('id');
            var amount = $(this).data('amount');
            var paid = $(this).data('paid');
            var balance = amount - paid;

            $('#paymentForm').attr('action', '/disciplines/' + id + '/pay');
            $('#paymentFineAmount').val(parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' TZS');
            $('#paymentPaidAmount').val(parseFloat(paid).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' TZS');
            $('#paymentBalance').val(parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' TZS');
            $('#paymentAmountInput').val(balance > 0 ? balance.toFixed(2) : '').attr('max', balance);

            var modal = new bootstrap.Modal(document.getElementById('recordPaymentModal'));
            modal.show();
        });
    }

    // Initialize Select2 for member dropdown in modal
    $('#fineMember').select2({
        placeholder: 'Search member...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#assignFineModal')
    });

    // CSRF token setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
/* Colored tabs matching collections style */
.nav-tabs-colored {
    border-bottom: none;
    background: #f8f9fa;
    padding: 0.5rem 0.5rem 0;
    border-radius: 0.5rem 0.5rem 0 0;
}
.nav-tabs-colored .nav-link {
    border: none;
    border-radius: 0.375rem 0.375rem 0 0;
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6c757d;
    background: transparent;
    margin-right: 0.25rem;
    border-bottom: 3px solid transparent;
}
.nav-tabs-colored .nav-link:hover {
    color: #495057;
    background: rgba(0,0,0,0.03);
}
.nav-tabs-colored .nav-link.active {
    background: #fff;
    color: #212529;
    font-weight: 600;
    border-bottom: 3px solid #0d6efd;
}
.nav-tabs-colored .nav-link.tab-green.active { border-bottom-color: #198754; }
.nav-tabs-colored .nav-link.tab-orange.active { border-bottom-color: #fd7e14; }
.nav-tabs-colored .nav-link.tab-red.active { border-bottom-color: #dc3545; }
.nav-tabs-colored .nav-link.tab-gray.active { border-bottom-color: #6c757d; }
.nav-tabs-colored .nav-link.tab-blue.active { border-bottom-color: #0d6efd; }
.nav-tabs-colored .nav-link.tab-purple.active { border-bottom-color: #6f42c1; }
.nav-tabs-colored .nav-link.tab-teal.active { border-bottom-color: #20c997; }

/* Custom purple button styling */
.btn-purple { background-color: #6f42c1; border-color: #6f42c1; color: #fff; }
.btn-purple:hover { background-color: #5a35a0; border-color: #5a35a0; color: #fff; }
.text-purple { color: #6f42c1 !important; }
.bg-purple { background-color: #6f42c1 !important; }
</style>
@endpush
