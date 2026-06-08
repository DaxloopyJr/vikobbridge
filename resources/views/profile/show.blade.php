@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px;font-size:2.5rem;">
                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ $user->full_name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="badge bg-primary">{{ ucfirst(str_replace('-', ' ', $user->getRoleNames()->first() ?? 'Member')) }}</span>
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Personal Information</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Full Name</small><strong>{{ $user->full_name }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Gender</small><strong>{{ ucfirst($user->gender) }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Phone</small><strong>{{ $user->phone_number }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Email</small><strong>{{ $user->email }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Marital Status</small><strong>{{ ucfirst($user->marital_status) }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Location</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Region</small><strong>{{ $user->region }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">District</small><strong>{{ $user->district }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Ward</small><strong>{{ $user->ward }}</strong></div>
                        <div class="col-md-6 mb-3"><small class="text-muted d-block">Village/Street</small><strong>{{ $user->village }} {{ $user->street ? '/ ' . $user->street : '' }}</strong></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Security</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-key me-2"></i>Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
