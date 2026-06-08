@extends('layouts.app')

@section('title', 'Collection Funds')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="fw-bold mb-0">Settings</h4>
            <p class="text-muted mb-0">Manage your group configuration</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFundModal"><i class="bi bi-plus-lg me-2"></i>Add Fund</button>
    </div>

    @include('partials.settings_tabs')

    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Collection Funds</h5>
            <small class="text-muted">Add/edit/delete predefined collection fund categories (Hisa, Jamii, Rejesho, Faini, Ada, Mradi etc.)</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="collection-funds-table">
                <thead>
                    <tr><th>Name</th><th>Type</th><th>Mandatory</th><th>Default Amount</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addFundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Collection Fund</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.collection_funds.store') }}">@csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label">Fund Type</label>
                            <select name="fund_type" class="form-select">
                                <option value="savings">Savings (Hisa)</option>
                                <option value="contribution">Contribution (Jamii)</option>
                                <option value="fee">Fee (Ada)</option>
                                <option value="fine">Fine (Faini)</option>
                                <option value="project">Project (Mradi)</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3"><label class="form-label">Default Amount</label><input type="number" name="default_amount" class="form-control" step="0.01"></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-check"><input type="checkbox" name="is_mandatory" class="form-check-input" value="1" id="mandatory"><label class="form-check-label" for="mandatory">Mandatory</label></div></div>
                        <div class="col-6"><div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="active" checked><label class="form-check-label" for="active">Active</label></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

@foreach($funds as $fund)
<div class="modal fade" id="editFundModal{{ $fund->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit {{ $fund->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.collection_funds.update', $fund) }}">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ $fund->name }}" required></div>
                    <div class="mb-3"><label class="form-label">Slug *</label><input type="text" name="slug" class="form-control" value="{{ $fund->slug }}" required></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2">{{ $fund->description }}</textarea></div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label">Fund Type</label>
                            <select name="fund_type" class="form-select">
                                @foreach(['savings' => 'Savings (Hisa)', 'contribution' => 'Contribution (Jamii)', 'fee' => 'Fee (Ada)', 'fine' => 'Fine (Faini)', 'project' => 'Project (Mradi)', 'other' => 'Other'] as $value => $label)
                                    <option value="{{ $value }}" {{ $fund->fund_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3"><label class="form-label">Default Amount</label><input type="number" name="default_amount" class="form-control" step="0.01" value="{{ $fund->default_amount }}"></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-check"><input type="checkbox" name="is_mandatory" class="form-check-input" value="1" id="mandatory{{ $fund->id }}" {{ $fund->is_mandatory ? 'checked' : '' }}><label class="form-check-label" for="mandatory{{ $fund->id }}">Mandatory</label></div></div>
                        <div class="col-6"><div class="form-check"><input type="checkbox" name="is_active" class="form-check-input" value="1" id="active{{ $fund->id }}" {{ $fund->is_active ? 'checked' : '' }}><label class="form-check-label" for="active{{ $fund->id }}">Active</label></div></div>
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
    $('#collection-funds-table').DataTable({
        ajax: '{{ route("settings.collection_funds.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'fund_type', name: 'fund_type', orderable: false },
            { data: 'is_mandatory', name: 'is_mandatory', orderable: false },
            { data: 'default_amount', name: 'default_amount' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
