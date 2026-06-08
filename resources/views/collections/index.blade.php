@extends('layouts.app')

@section('title', 'Collections')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Collections</h4>
            <p class="text-muted mb-0">Manage group collections by fund type</p>
        </div>
        <div class="d-flex gap-2">
            @can('create_collections')
            <a href="{{ route('disciplines.index') }}" class="btn btn-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>Fines &amp; Penalties
            </a>
            <a href="{{ route('collections.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Record Collection
            </a>
            @endcan
        </div>
    </div>

    {{-- Fund Summary Cards --}}
    <div class="row g-3 mb-4">
        @foreach($fundTotals as $ft)
        @php
            $cardClass = match($ft->slug) {
                'hisa' => 'border-success bg-success bg-opacity-10',
                'jamii' => 'border-primary bg-primary bg-opacity-10',
                'rejesho' => 'border-info bg-info bg-opacity-10',
                'ada' => 'border-purple bg-opacity-10',
                'faini' => 'border-danger bg-danger bg-opacity-10',
                'mradi' => 'border-warning bg-warning bg-opacity-10',
                default => 'border-secondary',
            };
            $textClass = match($ft->slug) {
                'hisa' => 'text-success',
                'jamii' => 'text-primary',
                'rejesho' => 'text-info',
                'ada' => 'text-purple',
                'faini' => 'text-danger',
                'mradi' => 'text-warning',
                default => 'text-secondary',
            };
        @endphp
        <div class="col-md-4 col-lg-2">
            <div class="card {{ $cardClass }} border-2">
                <div class="card-body text-center py-3">
                    <h6 class="mb-1 text-uppercase" style="font-size:0.7rem;letter-spacing:0.5px;">{{ $ft->name }}</h6>
                    <h4 class="fw-bold mb-0 {{ $textClass }}">{{ number_format($ft->total, 0) }} TZS</h4>
                    <small class="text-muted">{{ $ft->count }} records</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Fund Type Tabs --}}
    <ul class="nav nav-tabs-colored mb-0" id="fundTabs">
        <li class="nav-item">
            <a class="nav-link tab-dark {{ $activeFund == 'all' ? 'active' : '' }}" href="{{ route('collections.index') }}">
                <i class="bi bi-grid me-1"></i>All
            </a>
        </li>
        @foreach($funds as $fund)
        @php
            $tabClass = match($fund->slug) {
                'hisa' => 'tab-green',
                'jamii' => 'tab-blue',
                'rejesho' => 'tab-teal',
                'ada' => 'tab-purple',
                'faini' => 'tab-red',
                'mradi' => 'tab-orange',
                default => 'tab-dark',
            };
            $ft = $fundTotals->firstWhere('slug', $fund->slug);
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $tabClass }} {{ $activeFund == $fund->id ? 'active' : '' }}" href="{{ route('collections.index', ['fund_id' => $fund->id]) }}">
                <i class="bi bi-wallet2 me-1"></i>{{ $fund->name }}
                @if($ft)
                <span class="badge bg-dark bg-opacity-25 ms-1" style="font-size:0.6rem;">{{ $ft->count }}</span>
                @endif
            </a>
        </li>
        @endforeach
    </ul>

    {{-- DataTable --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="collections-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Fund</th>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Date</th>
                        <th>Channel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var activeFundId = '{{ $activeFund }}';

    $('#collections-table').DataTable({
        ajax: {
            url: '{{ route("collections.data") }}',
            data: function(d) {
                if (activeFundId && activeFundId !== 'all') {
                    d.fund_id = activeFundId;
                }
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'member', name: 'member' },
            { data: 'fund', name: 'fund', orderable: false },
            { data: 'amount', name: 'amount' },
            { data: 'month', name: 'month', orderable: false },
            { data: 'payment_date', name: 'payment_date' },
            { data: 'channel', name: 'channel', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

});
</script>
@endpush
