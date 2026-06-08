@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<style>
    .plans-hero { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); padding: 100px 0 60px; color: #fff; }
    .plan-card { background: #fff; border-radius: 20px; padding: 40px 30px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); transition: all 0.3s; height: 100%; position: relative; overflow: hidden; }
    .plan-card:hover { transform: translateY(-8px); box-shadow: 0 16px 48px rgba(0,0,0,0.12); }
    .plan-card.featured { border: 2px solid #f8b500; }
    .plan-card.featured::before { content: 'MOST POPULAR'; position: absolute; top: 20px; right: -35px; background: #f8b500; color: #1a1a2e; font-size: 0.7rem; font-weight: 700; padding: 5px 40px; transform: rotate(45deg); }
    .plan-price { font-size: 3rem; font-weight: 700; color: #1a5f2a; }
    .plan-price small { font-size: 1rem; color: #666; font-weight: 400; }
    .feature-list { list-style: none; padding: 0; }
    .feature-list li { padding: 8px 0; color: #555; font-size: 0.9rem; }
    .feature-list li i { color: #1a5f2a; margin-right: 10px; }
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

<section class="plans-hero">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Simple, Transparent Pricing</h1>
        <p class="lead opacity-75">Choose the plan that works best for your VICOBA group. All plans include a 14-day free trial.</p>
    </div>
</section>

<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            @forelse($plans as $plan)
                <div class="col-lg-4 col-md-6">
                    <div class="plan-card {{ $plan->slug === 'quarterly' ? 'featured' : '' }}">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">{{ $plan->name }}</h4>
                            <p class="text-muted small">{{ $plan->description }}</p>
                            <div class="plan-price">
                                {{ number_format($plan->price) }} <small>TZS/{{ $plan->billing_cycle === 'monthly' ? 'mo' : ($plan->billing_cycle === 'quarterly' ? 'qtr' : 'yr') }}</small>
                            </div>
                        </div>

                        <ul class="feature-list mb-4">
                            @if($plan->features)
                                @foreach($plan->features as $feature)
                                    <li><i class="bi bi-check-circle-fill"></i>{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>

                        <a href="{{ route('register') }}?plan={{ $plan->slug }}" class="btn {{ $plan->slug === 'quarterly' ? 'btn-warning' : 'btn-outline-primary' }} w-100 btn-lg">
                            <i class="bi bi-rocket me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No subscription plans available at the moment.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h4 class="fw-bold mb-2">Need a Custom Plan?</h4>
                            <p class="text-muted mb-0">For large VICOBA networks or special requirements, we offer customized solutions. Contact our sales team to discuss your needs.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-chat-dots me-2"></i>Contact Sales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center">
            <h4 class="fw-bold mb-4">Frequently Asked Questions</h4>
            <div class="row g-4 justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-start">
                            <h6 class="fw-bold"><i class="bi bi-question-circle text-primary me-2"></i>What happens after the 14-day trial?</h6>
                            <p class="text-muted mb-0">After your trial, you can choose to subscribe to any of our plans. If you don't subscribe, your group will be deactivated but your data will be preserved for 30 days.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-start">
                            <h6 class="fw-bold"><i class="bi bi-question-circle text-primary me-2"></i>Can I change my plan later?</h6>
                            <p class="text-muted mb-0">Yes, you can upgrade or downgrade your plan at any time. The price difference will be prorated.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-start">
                            <h6 class="fw-bold"><i class="bi bi-question-circle text-primary me-2"></i>Is my data secure?</h6>
                            <p class="text-muted mb-0">Absolutely. We use bank-grade encryption, regular backups, and our servers are hosted in secure data centers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-start">
                            <h6 class="fw-bold"><i class="bi bi-question-circle text-primary me-2"></i>Do you offer training?</h6>
                            <p class="text-muted mb-0">Yes! We offer free onboarding training for all new groups, plus ongoing support through phone, email, and WhatsApp.</p>
                        </div>
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
