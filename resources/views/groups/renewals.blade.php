@extends('layouts.app')

@section('title', 'Subscription Renewals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Subscription Renewals</h4>
        <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bell me-2"></i>Expiring Soon (Next 14 Days)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Group</th><th>Chairperson</th><th>Plan</th><th>Expiry Date</th><th>Days Left</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($expiringSoon as $grp)
                            <tr>
                                <td><strong>{{ $grp->name }}</strong></td>
                                <td>{{ $grp->chairman?->full_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $grp->subscriptionPlan?->name }}</span></td>
                                <td>{{ $grp->subscription_end_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td><span class="badge bg-warning">{{ now()->diffInDays($grp->subscription_end_date) }} days</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#notifyModal{{ $grp->id }}"><i class="bi bi-envelope me-1"></i>Notify</button>
                                </td>
                            </tr>
                            <div class="modal fade" id="notifyModal{{ $grp->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header"><h5 class="modal-title">Send Notification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <form method="POST" action="{{ route('groups.notify', $grp) }}">@csrf
                                            <div class="modal-body">
                                                <p>To: {{ $grp->chairman?->full_name }} ({{ $grp->chairman?->phone_number }})</p>
                                                <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="3" maxlength="480">Your VICOBRIDGE subscription for {{ $grp->name }} expires in {{ now()->diffInDays($grp->subscription_end_date) }} days. Please renew to avoid service interruption.</textarea></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Send</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No subscriptions expiring soon</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-danger text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Expired Subscriptions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Group</th><th>Chairperson</th><th>Plan</th><th>Expired On</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($expired as $grp)
                            <tr>
                                <td><strong>{{ $grp->name }}</strong></td>
                                <td>{{ $grp->chairman?->full_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $grp->subscriptionPlan?->name }}</span></td>
                                <td><span class="badge bg-danger">{{ $grp->subscription_end_date?->format('M d, Y') ?? $grp->trial_ends_at?->format('M d, Y') }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#notifyModal{{ $grp->id }}"><i class="bi bi-envelope me-1"></i>Notify</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No expired subscriptions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
