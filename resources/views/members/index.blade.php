@extends('layouts.app')

@section('title', 'Members')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Members</h4>
            <p class="text-muted mb-0">{{ $group->name ?? 'Group' }} members</p>
        </div>
        @can('create_members')
        <a href="{{ route('members.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Register Member</a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="members-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Phone</th><th>Gender</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#members-table').DataTable({
        ajax: '{{ route("members.data") }}',
        columns: [
            { data: 'member_number', name: 'member_number' },
            { data: 'name', name: 'name' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'gender', name: 'gender', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'join_date', name: 'join_date' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
