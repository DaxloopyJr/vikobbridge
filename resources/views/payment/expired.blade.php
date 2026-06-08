@extends('layouts.app')

@section('title', 'Subscription Expired')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
            <h2 class="fw-bold mt-4">Subscription Expired</h2>
            <p class="text-muted">Your group's subscription has expired. Renew now to continue using all features.</p>

            @if(isset($group))
                <div class="card mt-4 text-start">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $group->name }}</h5>
                        <p class="text-muted">Current Plan: {{ $group->subscriptionPlan?->name ?? 'N/A' }}</p>
                        @if($group->subscription_end_date)
                            <p class="text-muted">Expired on: {{ $group->subscription_end_date->format('M d, Y') }}</p>
                        @elseif($group->trial_ends_at)
                            <p class="text-muted">Trial ended on: {{ $group->trial_ends_at->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('subscription.pay') }}" class="btn btn-primary btn-lg me-2">
                    <i class="bi bi-credit-card me-2"></i>Renew Subscription
                </a>
                <a href="{{ route('subscription.control-number') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-upc-scan me-2"></i>Get Control Number
                </a>
            </div>

            <div class="mt-5">
                <h6 class="fw-bold">Available Plans</h6>
                <div class="row g-3 mt-2">
                    @foreach($plans ?? [] as $plan)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="fw-bold">{{ $plan->name }}</h6>
                                    <h5 class="text-primary fw-bold">{{ number_format($plan->price, 0) }} TZS</h5>
                                    <small class="text-muted">/{{ $plan->billing_cycle }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
