@extends('layouts.app')

@section('title', 'Group Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ $group->name }}</h4>
        <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Group Information</h5>
                    <div class="mb-2"><small class="text-muted d-block">Registration Number</small><code>{{ $group->registration_number }}</code></div>
                    <div class="mb-2"><small class="text-muted d-block">Location</small>{{ $group->village }}, {{ $group->ward }}, {{ $group->district }}, {{ $group->region }}</div>
                    <div class="mb-2"><small class="text-muted d-block">Subscription Plan</small>{{ $group->subscriptionPlan?->name ?? 'N/A' }}</div>
                    <div class="mb-2"><small class="text-muted d-block">Status</small>
                        @switch($group->status)
                            @case('active') <span class="badge badge-active">Active</span> @break
                            @case('pending') <span class="badge badge-pending">Pending</span> @break
                            @case('expired') <span class="badge badge-expired">Expired</span> @break
                            @case('suspended') <span class="badge bg-dark">Suspended</span> @break
                        @endswitch
                    </div>
                    <div class="mb-2"><small class="text-muted d-block">Payment Status</small>
                        @switch($group->payment_status)
                            @case('paid') <span class="badge badge-active">Paid</span> @break
                            @case('trial') <span class="badge badge-trial">Trial</span> @break
                            @case('pending') <span class="badge badge-pending">Pending</span> @break
                            @case('expired') <span class="badge badge-expired">Expired</span> @break
                        @endswitch
                    </div>
                    <div class="mb-0"><small class="text-muted d-block">Members</small>{{ $group->members()->count() }} total ({{ $group->activeMembers()->count() }} active)</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Chairperson</h5>
                    @if($group->chairman)
                        <p class="mb-1"><strong>{{ $group->chairman->full_name }}</strong></p>
                        <p class="mb-1 text-muted"><i class="bi bi-envelope me-1"></i>{{ $group->chairman->email }}</p>
                        <p class="mb-0 text-muted"><i class="bi bi-phone me-1"></i>{{ $group->chairman->phone_number }}</p>
                    @else
                        <p class="text-muted">No chairperson assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Payment History</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Transaction ID</th><th>Plan</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td><code>{{ $payment->transaction_id }}</code></td>
                                        <td>{{ $payment->subscriptionPlan?->name }}</td>
                                        <td class="fw-semibold">{{ number_format($payment->amount, 0) }} TZS</td>
                                        <td>
                                            @switch($payment->status)
                                                @case('completed') <span class="badge bg-success">Completed</span> @break
                                                @case('pending') <span class="badge badge-pending">Pending</span> @break
                                                @case('failed') <span class="badge badge-expired">Failed</span> @break
                                                @default <span class="badge bg-secondary">{{ $payment->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No payment history</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
