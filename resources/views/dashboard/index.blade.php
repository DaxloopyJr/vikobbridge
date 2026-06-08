@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    @if($groupStatus['is_trial'])
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Trial Period Active:</strong> You have {{ $groupStatus['days_remaining'] }} days remaining in your free trial.
            <a href="{{ route('subscription.pay') }}" class="alert-link">Subscribe now</a> to continue without interruption.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($groupStatus['trial_expired'] || $groupStatus['subscription_expired'])
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Subscription Expired:</strong> Your access is limited. 
            <a href="{{ route('subscription.pay') }}" class="alert-link">Renew now</a> to restore full access.
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Members</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalMembers) }}</h3>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $activeMembers }} active</small>
                    </div>
                    <div class="stat-icon" style="background: #e8f5e9;">
                        <i class="bi bi-people text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Total Collections</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalCollections, 0) }} TZS</h3>
                        <small class="text-muted">This calendar year</small>
                    </div>
                    <div class="stat-icon" style="background: #fff3e0;">
                        <i class="bi bi-cash-coin text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Loans Disbursed</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalLoansProvided) }}</h3>
                        <small class="text-info">{{ number_format($totalLoanAmountDisbursed, 0) }} TZS total</small>
                    </div>
                    <div class="stat-icon" style="background: #e3f2fd;">
                        <i class="bi bi-bank text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1">Expenditure</p>
                        <h3 class="fw-bold mb-0">{{ number_format($totalExpenditure, 0) }} TZS</h3>
                        <small class="text-danger">{{ $loanDefaulters }} defaulters</small>
                    </div>
                    <div class="stat-icon" style="background: #fce4ec;">
                        <i class="bi bi-receipt text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Monthly Overview</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Collections</button>
                        <button class="btn btn-outline-secondary">Loans</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Collections by Fund</h5>
                </div>
                <div class="card-body">
                    <canvas id="fundChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Recent Collections</h5>
                    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Fund</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCollections as $collection)
                                    <tr>
                                        <td>{{ $collection->member?->full_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $collection->collectionFund?->name ?? 'N/A' }}</span></td>
                                        <td class="fw-semibold">{{ number_format($collection->amount, 0) }} TZS</td>
                                        <td><small>{{ $collection->payment_date->format('M d, Y') }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No collections recorded yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">Recent Loans</h5>
                    <a href="{{ route('loans.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLoans as $loan)
                                    <tr>
                                        <td>{{ $loan->member?->full_name ?? 'N/A' }}</td>
                                        <td class="fw-semibold">{{ number_format($loan->loan_amount, 0) }} TZS</td>
                                        <td>
                                            @switch($loan->status)
                                                @case('pending') <span class="badge badge-pending">Pending</span> @break
                                                @case('approved') <span class="badge badge-trial">Approved</span> @break
                                                @case('disbursed') <span class="badge badge-active">Disbursed</span> @break
                                                @case('repaying') <span class="badge badge-active">Repaying</span> @break
                                                @case('completed') <span class="badge bg-success">Completed</span> @break
                                                @case('defaulted') <span class="badge badge-expired">Defaulted</span> @break
                                                @default <span class="badge bg-secondary">{{ $loan->status }}</span>
                                            @endswitch
                                        </td>
                                        <td><small>{{ $loan->application_date->format('M d, Y') }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No loans recorded yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">New Members</h5>
                    <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Join Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMembers as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($member->profile_picture)
                                                    <img src="{{ asset('storage/' . $member->profile_picture) }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                                @else
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:0.7rem;">
                                                        {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                                    </div>
                                                @endif
                                                {{ $member->full_name }}
                                            </div>
                                        </td>
                                        <td>{{ $member->phone_number }}</td>
                                        <td><small>{{ $member->join_date->format('M d, Y') }}</small></td>
                                        <td>
                                            @if($member->status === 'active')
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $member->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No members registered yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('members.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-person-plus fs-4 d-block mb-2"></i>
                                <span class="small">Add Member</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('collections.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-cash-coin fs-4 d-block mb-2"></i>
                                <span class="small">Record Collection</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('loans.create') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-bank fs-4 d-block mb-2"></i>
                                <span class="small">Apply Loan</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('expenditures.create') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-receipt fs-4 d-block mb-2"></i>
                                <span class="small">Add Expenditure</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Collections',
            data: {{ json_encode(array_values(array_replace(array_fill(1, 12, 0), $monthlyCollections))) }},
            borderColor: '#1a5f2a',
            backgroundColor: 'rgba(26, 95, 42, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Loans',
            data: {{ json_encode(array_values(array_replace(array_fill(1, 12, 0), $monthlyLoans))) }},
            borderColor: '#f8b500',
            backgroundColor: 'rgba(248, 181, 0, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Expenditures',
            data: {{ json_encode(array_values(array_replace(array_fill(1, 12, 0), $monthlyExpenditures))) }},
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});

const fundCtx = document.getElementById('fundChart').getContext('2d');
new Chart(fundCtx, {
    type: 'doughnut',
    data: {
        labels: @json($collectionsByFund->pluck('name')),
        datasets: [{
            data: @json($collectionsByFund->pluck('total')),
            backgroundColor: ['#1a5f2a', '#f8b500', '#0d6efd', '#dc3545', '#6f42c1', '#20c997']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endpush
@endsection
