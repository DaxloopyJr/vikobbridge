@extends('layouts.app')

@section('title', 'About')

@section('content')
<style>
    .about-hero { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); padding: 100px 0 60px; color: #fff; }
    .about-section { padding: 60px 0; }
    .value-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); height: 100%; }
    .team-member { text-align: center; }
    .team-member img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; }
    .landing-nav { background: rgba(26, 95, 42, 0.95); backdrop-filter: blur(10px); }
    .landing-nav .nav-link { color: rgba(255,255,255,0.8) !important; margin: 0 5px; }
    .landing-nav .navbar-brand { color: #fff !important; }
    .footer { background: #1a1a2e; color: rgba(255,255,255,0.7); padding: 60px 0 30px; }
</style>

<nav class="landing-nav navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="{{ route('landing') }}"><i class="bi bi-bank2 me-2"></i>VICOBRIDGE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="{{ route('landing') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('plans') }}">Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
                <li class="nav-item ms-lg-3"><a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a></li>
                <li class="nav-item ms-2"><a href="{{ route('register') }}" class="btn btn-warning btn-sm">Get Started</a></li>
            </ul>
        </div>
    </div>
</nav>

<section class="about-hero">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">About VICOBRIDGE</h1>
        <p class="lead opacity-75">Transforming Village Community Banks through technology</p>
    </div>
</section>

<section class="about-section bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Our Mission</h2>
                <p class="text-muted mb-4">VICOBRIDGE was born from a simple observation: VICOBA groups across Tanzania were managing millions of shillings using paper records, spreadsheets, and memory. This led to errors, disputes, and missed opportunities for growth.</p>
                <p class="text-muted mb-4">Our mission is to provide affordable, accessible, and powerful technology that empowers these community banks to operate with the same efficiency and transparency as formal financial institutions.</p>
                <p class="text-muted">We believe that when VICOBA groups thrive, entire communities benefit from increased access to savings, credit, and financial education.</p>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="card-body p-5" style="background: linear-gradient(135deg, #e8f5e9 0%, #fff 100%);">
                        <div class="row text-center">
                            <div class="col-6 mb-4">
                                <h3 class="text-primary fw-bold">500+</h3>
                                <small class="text-muted">Active Groups</small>
                            </div>
                            <div class="col-6 mb-4">
                                <h3 class="text-primary fw-bold">25,000+</h3>
                                <small class="text-muted">Members</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-primary fw-bold">5B+ TZS</h3>
                                <small class="text-muted">Processed</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-primary fw-bold">26</h3>
                                <small class="text-muted">Regions Covered</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Core Values</h2>
            <p class="text-muted">The principles that guide everything we do</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-heart-fill text-danger fs-1 mb-3"></i>
                    <h5 class="fw-bold">Community First</h5>
                    <p class="text-muted">We design every feature with the needs of VICOBA members and leaders in mind.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-shield-lock-fill text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold">Trust & Security</h5>
                    <p class="text-muted">Financial data is protected with bank-grade security and regular backups.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-lightbulb-fill text-warning fs-1 mb-3"></i>
                    <h5 class="fw-bold">Continuous Innovation</h5>
                    <p class="text-muted">We constantly improve our platform based on feedback from the VICOBA community.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-universal-access text-success fs-1 mb-3"></i>
                    <h5 class="fw-bold">Accessibility</h5>
                    <p class="text-muted">Our platform works on any device with internet, no expensive hardware needed.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-graph-up-arrow text-info fs-1 mb-3"></i>
                    <h5 class="fw-bold">Transparency</h5>
                    <p class="text-muted">Every transaction is recorded and auditable, building trust within groups.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card text-center">
                    <i class="bi bi-people-fill text-purple fs-1 mb-3" style="color: #7b1fa2;"></i>
                    <h5 class="fw-bold">Collaboration</h5>
                    <p class="text-muted">We work with financial institutions and regulators to strengthen the sector.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Key Advantages</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Automated Record Keeping</h5>
                        <p class="text-muted">No more paper registers. All member data, collections, and loans are stored securely in the cloud.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Real-Time Financial Reports</h5>
                        <p class="text-muted">Generate comprehensive reports instantly for better decision making and transparency.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Loan Management with EMI</h5>
                        <p class="text-muted">Automatic EMI calculation, repayment schedules, and defaulter tracking.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Role-Based Access Control</h5>
                        <p class="text-muted">Different permissions for chairpersons, secretaries, treasurers, and members.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Calendar Year Management</h5>
                        <p class="text-muted">Manage multiple calendar years and track performance across different periods.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="bi bi-check-lg text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h5 class="fw-bold">Payment Gateway Integration</h5>
                        <p class="text-muted">Integrated with Selcom for seamless payment collection and subscription management.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4 class="text-white mb-3"><i class="bi bi-bank2 me-2"></i>VICOBRIDGE</h4>
                <p>The comprehensive digital solution for managing Village Community Banks (VICOBA).</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-4">
                <h6 class="text-white mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('landing') }}" class="text-white-50">Home</a></li>
                    <li><a href="{{ route('about') }}" class="text-white-50">About</a></li>
                    <li><a href="{{ route('plans') }}" class="text-white-50">Plans</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white-50">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 mb-4">
                <h6 class="text-white mb-3">Contact</h6>
                <p class="text-white-50"><i class="bi bi-envelope me-2"></i>support@vicobridge.com</p>
                <p class="text-white-50"><i class="bi bi-phone me-2"></i>+255 700 123 456</p>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-white-50">
            <small>&copy; {{ date('Y') }} VICOBRIDGE. All rights reserved.</small>
        </div>
    </div>
</footer>
@endsection
