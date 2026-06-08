@extends('layouts.app')

@section('title', 'System Users')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">System Users</h4>
            <p class="text-muted mb-0">Manage system administration users</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-plus-lg me-2"></i>Add User</button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="system-users-table">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Phone</th><th>Roles</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add System User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('users.system.store') }}">@csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" name="phone_number" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Password *</label><input type="password" name="password" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Role *</label>
                            <select name="role" class="form-select" required>
                                @foreach($roles as $role)<option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>@endforeach
                            </select>
                        </div>
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
    $('#system-users-table').DataTable({
        ajax: '{{ route("users.system.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'roles', name: 'roles', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
