@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Subscription Plans</h4>
            <p class="text-muted mb-0">Manage subscription plans for groups</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal"><i class="bi bi-plus-lg me-2"></i>Add Plan</button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="subscription-plans-table">
                <thead>
                    <tr><th>Name</th><th>Price</th><th>Duration</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Subscription Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.subscription_plans.store') }}">@csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" required></div>
                        <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Price (TZS) *</label><input type="number" name="price" class="form-control" step="100" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Billing Cycle *</label><select name="billing_cycle" class="form-select" required><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="annually">Annually</option></select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Duration (Days) *</label><input type="number" name="duration_days" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Display Order</label><input type="number" name="display_order" class="form-control" value="0"></div>
                        <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="planActive" checked><label class="form-check-label" for="planActive">Active</label></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save Plan</button></div>
            </form>
        </div>
    </div>
</div>

@foreach($plans as $plan)
<div class="modal fade" id="editPlanModal{{ $plan->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit {{ $plan->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.subscription_plans.update', $plan) }}">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ $plan->name }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" value="{{ $plan->slug }}" required></div>
                        <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ $plan->description }}</textarea></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Price (TZS) *</label><input type="number" name="price" class="form-control" step="100" value="{{ $plan->price }}" required></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Billing Cycle *</label><select name="billing_cycle" class="form-select" required>@foreach(['monthly', 'quarterly', 'annually'] as $cycle)<option value="{{ $cycle }}" {{ $plan->billing_cycle == $cycle ? 'selected' : '' }}>{{ ucfirst($cycle) }}</option>@endforeach</select></div>
                        <div class="col-md-4 mb-3"><label class="form-label">Duration (Days) *</label><input type="number" name="duration_days" class="form-control" value="{{ $plan->duration_days }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Display Order</label><input type="number" name="display_order" class="form-control" value="{{ $plan->display_order }}"></div>
                        <div class="col-md-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="planActive{{ $plan->id }}" {{ $plan->is_active ? 'checked' : '' }}><label class="form-check-label" for="planActive{{ $plan->id }}">Active</label></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Update Plan</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#subscription-plans-table').DataTable({
        ajax: '{{ route("settings.subscription_plans.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price' },
            { data: 'duration', name: 'duration' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
