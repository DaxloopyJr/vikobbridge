@extends('layouts.app')

@section('title', 'Add Expenditure')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Record Expenditure</h4>
        <a href="{{ route('expenditures.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenditures.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Category *</label><input type="text" name="category" class="form-control" placeholder="e.g. Office Supplies, Meeting Costs" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Amount (TZS) *</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Expense Date *</label><input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Payment Method *</label><select name="payment_method" class="form-select" required><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="mobile_money">Mobile Money</option></select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Reference Number</label><input type="text" name="reference_number" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Receipt</label><input type="file" name="receipt_attachment" class="form-control" accept="image/*,.pdf"></div>
                            <div class="col-12 mb-3"><label class="form-label">Description *</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
                            <div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Record Expenditure</button>
                            <a href="{{ route('expenditures.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Quick Tips</h5></div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Categorize expenses for better reporting</li>
                        <li class="mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Attach receipts for audit purposes</li>
                        <li><i class="bi bi-info-circle text-primary me-2"></i>All expenditures need approval</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
