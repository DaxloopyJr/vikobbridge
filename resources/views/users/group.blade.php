@extends('layouts.app')

@section('title', 'Group Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Group Users</h4>
            <p class="text-muted mb-0">Manage users from member profiles for {{ $group->name ?? 'group' }}</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupUserModal"><i class="bi bi-plus-lg me-2"></i>Add User from Member</button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="group-users-table">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addGroupUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add User from Member</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('users.group.store') }}">@csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Member *</label>
                        <select name="member_id" class="form-select" required>
                            <option value="">Select Member</option>
                            @foreach($members as $member)<option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Role *</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)<option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#group-users-table').DataTable({
        ajax: '{{ route("users.group.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'joined_at', name: 'joined_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
