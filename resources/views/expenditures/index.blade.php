@extends('layouts.app')

@section('title', 'Expenditures')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Expenditures</h4>
            <p class="text-muted mb-0">Manage group expenditures</p>
        </div>
        @can('create_expenditures')
        <a href="{{ route('expenditures.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Expenditure</a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="expenditures-table">
                <thead>
                    <tr><th>ID</th><th>Description</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#expenditures-table').DataTable({
        ajax: '{{ route("expenditures.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'expense_date', name: 'expense_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
