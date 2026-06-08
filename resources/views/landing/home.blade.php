@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<style>
    .landing-hero {
        background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 50%, #1a1a2e 100%);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }
    .landing-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .landing-nav {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .landing-nav .nav-link {
        color: rgba(255,255,255,0.8) !important;
        margin: 0 5px;
    }
    .landing-nav .nav-link:hover {
        color: #fff !important;
    }
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        color: rgba(255,255,255,0.8);
        line-height: 1.6;
    }
    .feature-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }
    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 2rem;
    }
    .stat-box {
        text-align: center;
        padding: 30px;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a5f2a;
    }
    .cta-section {
        background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%);
        border-radius: 24px;
        padding: 60px;
        color: #fff;
    }
    .btn-landing-primary {
        background: #f8b500;
        color: #1a1a2e;
        font-weight: 600;
        padding: 14px 36px;
        border-radius: 10px;
        border: none;
        transition: all 0.3s;
    }
    .btn-landing-primary:hover {
        background: #e0a500;
        color: #1a1a2e;
        transform: translateY(-2px);
    }
    .btn-landing-outline {
        background: transparent;
        color: #fff;
        font-weight: 600;
        padding: 14px 36px;
        border-radius: 10px;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s;
    }
    .btn-landing-outline:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
        border-color: #fff;
    }
    .how-it-works-step {
        position: relative;
        padding: 30px;
    }
    .step-number {
        width: 50px;
        height: 50px;
        background: #1a5f2a;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 20px;
    }
    .testimonial-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .footer {
        background: #1a1a2e;
        color: rgba(255,255,255,0.7);
        padding: 60px 0 30px;
    }
    .footer a {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
    }
    .footer a:hover {
        color: #fff;
    }
</style>

<!-- Navigation -->
<nav class="landing-nav navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand text-white fw-bold fs-4" href="{{ route('landing') }}">
            <i class="bi bi-bank2 me-2"></i>VICOBRIDGE
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('landing') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('plans') }}">Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                <li class="nav-item ms-lg-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                </li>
                <li class="nav-item ms-2">
                    <a href="{{ route('register') }}" class="btn btn-landing-primary btn-sm">Get Started</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="landing-hero d-flex align-items-center">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center min-vh-100 pt-5">
            <div class="col-lg-6">
                <div class="mb-4">
                    <span class="badge bg-white text-primary px-3 py-2 mb-3">#1 VICOBA Management Platform</span>
                </div>
                <h1 class="hero-title text-white mb-4">
                    Empower Your<br>
                    <span style="color: #f8b500;">Community Bank</span><br>
                    with Technology
                </h1>
                <p class="hero-subtitle mb-5">
                    VICOBRIDGE is the comprehensive digital solution for managing Village Community Banks (VICOBA).
                    Streamline collections, automate loans, track finances, and grow your group with confidence.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-landing-primary btn-lg">
                        <i class="bi bi-rocket-takeoff me-2"></i>Start Free Trial
                    </a>
                    <a href="{{ route('plans') }}" class="btn btn-landing-outline btn-lg">
                        <i class="bi bi-eye me-2"></i>View Plans
                    </a>
                </div>
                <div class="mt-4 text-white-50">
                    <small><i class="bi bi-shield-check me-1"></i> 14-day free trial &middot; No credit card required</small>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="position-relative">
                    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-body p-4" style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0 text-dark fw-bold">Dashboard Overview</h5>
                                <span class="badge bg-success">Live</span>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #e8f5e9;">
                                        <small class="text-muted d-block">Total Members</small>
                                        <strong class="fs-4 text-primary">248</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #fff3e0;">
                                        <small class="text-muted d-block">Collections</small>
                                        <strong class="fs-4 text-warning">12.5M</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #e3f2fd;">
                                        <small class="text-muted d-block">Active Loans</small>
                                        <strong class="fs-4 text-info">45</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3" style="background: #fce4ec;">
                                        <small class="text-muted d-block">Loan Repayments</small>
                                        <strong class="fs-4 text-danger">8.2M</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                            <small class="text-muted">Group Performance: 75% target achieved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">500+</div>
                    <small class="text-muted">VICOBA Groups</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">25K+</div>
                    <small class="text-muted">Members Managed</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">5B+</div>
                    <small class="text-muted">TZS Processed</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <div class="stat-number">99.9%</div>
                    <small class="text-muted">Uptime</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose VICOBRIDGE?</h2>
            <p class="text-muted">Everything you need to manage your VICOBA efficiently</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e8f5e9;">
                        <i class="bi bi-people-fill text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Member Management</h5>
                    <p class="text-muted">Register and manage members with complete profiles, dependents, guarantors, and inheritor details.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fff3e0;">
                        <i class="bi bi-cash-coin text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Collections Management</h5>
                    <p class="text-muted">Track all collections including Hisa, Jamii, Rejesho, Ada, Faini, and Mradi funds automatically.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e3f2fd;">
                        <i class="bi bi-bank text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Loan Management</h5>
                    <p class="text-muted">Full loan lifecycle from application to disbursement with automatic EMI schedule generation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #fce4ec;">
                        <i class="bi bi-graph-up-arrow text-danger"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Financial Reports</h5>
                    <p class="text-muted">Comprehensive reports including monthly collections, loan reports, year-end summaries, and balance sheets.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #f3e5f5;">
                        <i class="bi bi-shield-check text-purple" style="color: #7b1fa2;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Role-Based Access</h5>
                    <p class="text-muted">Secure access control with roles for Chairperson, Secretary, Treasurer, and Members.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background: #e0f2f1;">
                        <i class="bi bi-phone text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Payment Integration</h5>
                    <p class="text-muted">Integrated with Selcom Payment Gateway for seamless subscription and collection payments.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Get started in 4 simple steps</p>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="how-it-works-step text-center">
                    <div class="step-number mx-auto">1</div>
                    <h5 class="fw-bold">Register Group</h5>
                    <p class="text-muted">Sign up your VICOBA group with chairman details and get 14-day free trial.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="how-it-works-step text-center">
                    <div class="step-number mx-auto">2</div>
                    <h5 class="fw-bold">Complete Profile</h5>
                    <p class="text-muted">Fill in your profile details including location, guarantor, and family information.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="how-it-works-step text-center">
                    <div class="step-number mx-auto">3</div>
                    <h5 class="fw-bold">Add Members</h5>
                    <p class="text-muted">Register all group members and start recording collections and loans.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="how-it-works-step text-center">
                    <div class="step-number mx-auto">4</div>
                    <h5 class="fw-bold">Track & Grow</h5>
                    <p class="text-muted">Monitor finances with dashboards and reports to make informed decisions.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="cta-section text-center">
            <h2 class="fw-bold mb-3">Ready to Transform Your VICOBA?</h2>
            <p class="mb-4" style="opacity: 0.9;">Join hundreds of VICOBA groups already using VICOBRIDGE to manage their operations efficiently.</p>
            <a href="{{ route('register') }}" class="btn btn-landing-primary btn-lg">
                <i class="bi bi-rocket-takeoff me-2"></i>Start Your Free Trial
            </a>
            <p class="mt-3 mb-0" style="opacity: 0.7;"><small>14-day free trial &middot; No setup fees &middot; Cancel anytime</small></p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4 class="text-white mb-3"><i class="bi bi-bank2 me-2"></i>VICOBRIDGE</h4>
                <p>The comprehensive digital solution for managing Village Community Banks (VICOBA) in Tanzania and beyond.</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="text-white mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('landing') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('plans') }}">Plans</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="text-white mb-3">Resources</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 mb-4">
                <h6 class="text-white mb-3">Contact Us</h6>
                <p><i class="bi bi-envelope me-2"></i>support@vicobridge.com</p>
                <p><i class="bi bi-phone me-2"></i>+255 700 123 456</p>
                <p><i class="bi bi-geo-alt me-2"></i>Dar es Salaam, Tanzania</p>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center">
            <small>&copy; {{ date('Y') }} VICOBRIDGE. All rights reserved.</small>
        </div>
    </div>
</footer>
@endsection
