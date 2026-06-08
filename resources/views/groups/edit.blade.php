@extends('layouts.app')

@section('title', 'Edit Group')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Edit Group: {{ $group->name }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('groups.update', $group) }}">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="form-label">Group Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $group->name) }}" required></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $group->email) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $group->phone_number) }}"></div>
                            <div class="col-md-6 mb-3"><label class="form-label">Status *</label><select name="status" class="form-select" required>
                                @foreach(['active', 'inactive', 'suspended'] as $s)<option value="{{ $s }}" {{ old('status', $group->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
                            </select></div>
                            <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $group->description) }}</textarea></div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Update Group</button>
                            <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
