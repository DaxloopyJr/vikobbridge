@extends('layouts.app')

@section('title', 'Member Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ $member->full_name }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('members.edit', $member) }}" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit</a>
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    @if($member->profile_picture)
                        <img src="{{ asset('storage/' . $member->profile_picture) }}" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px;font-size:2.5rem;">
                            {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ $member->full_name }}</h4>
                    <p class="text-muted mb-2"><code>{{ $member->member_number }}</code></p>
                    <span class="badge {{ $member->status == 'active' ? 'badge-active' : 'bg-secondary' }}">{{ ucfirst($member->status) }}</span>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Personal Info</h5></div>
                <div class="card-body">
                    <div class="mb-2"><small class="text-muted d-block">Phone</small><strong>{{ $member->phone_number }}</strong></div>
                    @if($member->email)<div class="mb-2"><small class="text-muted d-block">Email</small><strong>{{ $member->email }}</strong></div>@endif
                    <div class="mb-2"><small class="text-muted d-block">Gender</small><strong>{{ ucfirst($member->gender) }}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Marital Status</small><strong>{{ ucfirst($member->marital_status) ?? 'N/A' }}</strong></div>
                    <div class="mb-0"><small class="text-muted d-block">Join Date</small><strong>{{ $member->join_date->format('M d, Y') }}</strong></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Collection Summary</h5></div>
                <div class="card-body">
                    @foreach($collectionFunds as $fund)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $fund->name }}</span>
                            <strong>{{ number_format($fundTotals[$fund->slug] ?? 0, 0) }} TZS</strong>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <strong class="text-primary">{{ number_format(array_sum($fundTotals), 0) }} TZS</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between"><h5 class="mb-0 fw-bold">Recent Collections</h5><a href="{{ route('collections.index') }}" class="btn btn-sm btn-outline-primary">View All</a></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Fund</th><th>Amount</th><th>Date</th><th>Channel</th></tr></thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr>
                                        <td>{{ $collection->collectionFund?->name }}</td>
                                        <td class="fw-semibold">{{ number_format($collection->amount, 0) }} TZS</td>
                                        <td>{{ $collection->payment_date->format('M d, Y') }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $collection->payment_channel)) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No collections</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between"><h5 class="mb-0 fw-bold">Loan History</h5><a href="{{ route('loans.index') }}" class="btn btn-sm btn-outline-primary">View All</a></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr><th>Loan No.</th><th>Amount</th><th>Status</th><th>Progress</th></tr></thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    <tr>
                                        <td><a href="{{ route('loans.show', $loan) }}"><code>{{ $loan->loan_number }}</code></a></td>
                                        <td>{{ number_format($loan->loan_amount, 0) }} TZS</td>
                                        <td>
                                            @switch($loan->status)
                                                @case('pending') <span class="badge badge-pending">Pending</span> @break
                                                @case('disbursed') <span class="badge badge-active">Disbursed</span> @break
                                                @case('completed') <span class="badge bg-success">Completed</span> @break
                                                @case('defaulted') <span class="badge badge-expired">Defaulted</span> @break
                                                @default <span class="badge bg-secondary">{{ $loan->status }}</span>
                                            @endswitch
                                        </td>
                                        <td style="width: 100px;">
                                            <div class="progress" style="height: 6px;"><div class="progress-bar bg-success" style="width: {{ $loan->progress_percentage }}%"></div></div>
                                            <small class="text-muted">{{ number_format($loan->progress_percentage, 0) }}%</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No loans</td></tr>
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
