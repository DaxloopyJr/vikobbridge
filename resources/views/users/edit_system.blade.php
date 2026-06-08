@extends('layouts.app')

@section('title', 'Edit System User')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Edit System User: {{ $user->full_name }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.system.update', $user) }}">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Password <small class="text-muted">(leave empty to keep current)</small></label><input type="password" name="password" class="form-control"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Role *</label><select name="role" class="form-select" required>@foreach($roles as $role)<option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>@endforeach</select></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Update User</button>
                            <a href="{{ route('users.system') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
