@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Payment History</h4>
        <a href="{{ route('subscription.pay') }}" class="btn btn-success"><i class="bi bi-credit-card me-2"></i>Make Payment</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Transaction ID</th><th>Plan</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td><code>{{ $payment->transaction_id }}</code></td>
                                <td>{{ $payment->subscriptionPlan?->name ?? 'N/A' }}</td>
                                <td class="fw-semibold">{{ number_format($payment->amount, 0) }} TZS</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>
                                    @switch($payment->status)
                                        @case('completed') <span class="badge bg-success">Completed</span> @break
                                        @case('pending') <span class="badge badge-pending">Pending</span> @break
                                        @case('failed') <span class="badge badge-expired">Failed</span> @break
                                        @default <span class="badge bg-secondary">{{ $payment->status }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $payment->paid_at?->format('M d, Y H:i') ?? $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No payment history</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
