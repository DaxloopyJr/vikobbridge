@extends('layouts.app')

@section('title', 'Contact')

@section('content')
<style>
    .contact-hero { background: linear-gradient(135deg, #1a5f2a 0%, #0d3320 100%); padding: 100px 0 60px; color: #fff; }
    .contact-form { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
    .contact-info { background: #fff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); height: 100%; }
    .contact-item { display: flex; align-items: flex-start; margin-bottom: 25px; }
    .contact-icon { width: 50px; height: 50px; background: #e8f5e9; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #1a5f2a; flex-shrink: 0; }
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

<section class="contact-hero">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
        <p class="lead opacity-75">We'd love to hear from you. Get in touch with our team.</p>
    </div>
</section>

<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="contact-form">
                    <h4 class="fw-bold mb-4">Send us a Message</h4>
                    <form method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required></textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contact-info">
                    <h4 class="fw-bold mb-4">Contact Information</h4>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Address</h6>
                            <p class="text-muted mb-0">VICOBRIDGE Tower, Plot 123<br>Ohio Street, Dar es Salaam<br>Tanzania</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Email</h6>
                            <p class="text-muted mb-0">support@vicobridge.com<br>info@vicobridge.com</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Phone</h6>
                            <p class="text-muted mb-0">+255 700 123 456<br>+255 713 789 012</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1">Business Hours</h6>
                            <p class="text-muted mb-0">Monday - Friday: 8AM - 6PM<br>Saturday: 9AM - 1PM</p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Follow Us</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-info btn-sm"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="btn btn-outline-success btn-sm"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="btn btn-outline-danger btn-sm"><i class="bi bi-instagram"></i></a>
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
