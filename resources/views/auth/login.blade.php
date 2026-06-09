@extends('layouts.app')

@section('title', 'Login')

@section('content')
<style>
    .login-page { min-height: 100vh; display: flex; align-items: center; background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%); }
    .login-card { background: #fff; border-radius: 20px; box-shadow: 0 8px 40px rgba(0,0,0,0.1); overflow: hidden; }
    .login-sidebar { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); color: #fff; padding: 60px 40px; display: flex; flex-direction: column; justify-content: center; }
    .login-form { padding: 60px 40px; }
    .form-control { border-radius: 10px; padding: 12px 16px; border: 1px solid #e0e0e0; }
    .form-control:focus { border-color: #1a5f2a; box-shadow: 0 0 0 0.2rem rgba(26, 95, 42, 0.25); }
    .btn-login { background: #1a5f2a; border: none; border-radius: 10px; padding: 14px; font-weight: 600; }
    .btn-login:hover { background: #124620; }
    .back-link { color: #1a5f2a; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
</style>

<div class="login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="login-card">
                    <div class="row g-0">
                        <div class="col-lg-5">
                            <div class="login-sidebar">
                                <a href="{{ route('landing') }}" class="text-white text-decoration-none mb-4 d-inline-block">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Home
                                </a>
                                <h2 class="fw-bold mb-3">Welcome Back!</h2>
                                <p class="opacity-75">Login to access your VICOBA group dashboard, manage members, track collections, and monitor loans.</p>
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-shield-check fs-4 me-3 opacity-75"></i>
                                        <div>
                                            <strong>Secure Access</strong>
                                            <p class="mb-0 opacity-75 small">Your data is protected with encryption</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-graph-up fs-4 me-3 opacity-75"></i>
                                        <div>
                                            <strong>Real-Time Dashboard</strong>
                                            <p class="mb-0 opacity-75 small">Monitor your group's performance</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-headset fs-4 me-3 opacity-75"></i>
                                        <div>
                                            <strong>24/7 Support</strong>
                                            <p class="mb-0 opacity-75 small">We're here to help you succeed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="login-form">
                                <div class="text-center mb-4">
                                    <i class="bi bi-bank2 text-primary fs-1"></i>
                                    <h3 class="fw-bold mt-2">VICOBRIDGE</h3>
                                    <p class="text-muted">Sign in to your account</p>
                                </div>

                                <form method="POST" action="{{ route('login.submit') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                        <a href="#" class="back-link small">Forgot password?</a>
                                    </div>

                                    <button type="submit" class="btn btn-login btn-primary w-100 mb-3">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                    </button>
                                </form>

                                <div class="text-center mt-4">
                                    <p class="text-muted">Don't have an account? <a href="{{ route('register') }}" class="back-link fw-semibold">Register your group</a></p>
                                </div>

                                <hr class="my-4">

                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
