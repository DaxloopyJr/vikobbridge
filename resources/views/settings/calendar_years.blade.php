@extends('layouts.app')

@section('title', 'Calendar Years')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h4 class="fw-bold mb-0">Settings</h4>
            <p class="text-muted mb-0">Manage your group configuration</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addYearModal"><i class="bi bi-plus-lg me-2"></i>Add Year</button>
    </div>

    @include('partials.settings_tabs')

    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Calendar Years</h5>
            <small class="text-muted">Add/edit/delete predefined calendar years (Year, Start, End, Status - Active/Not Active)</small>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="calendar-years-table">
                <thead>
                    <tr><th>Name</th><th>Period</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Calendar Year</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.calendar_years.store') }}">@csrf
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" placeholder="e.g. Financial Year 2024" required></div>
                    <div class="row">
                        <div class="col-4 mb-3"><label class="form-label">Year *</label><input type="text" name="year" class="form-control" placeholder="2024" required></div>
                        <div class="col-4 mb-3"><label class="form-label">Start Date *</label><input type="date" name="start_date" class="form-control" required></div>
                        <div class="col-4 mb-3"><label class="form-label">End Date *</label><input type="date" name="end_date" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_current" class="form-check-input" value="1" id="isCurrent"><label class="form-check-label" for="isCurrent">Set as Current Year</label></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
            </form>
        </div>
    </div>
</div>

@foreach($calendarYears as $year)
<div class="modal fade" id="editYearModal{{ $year->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit {{ $year->name }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('settings.calendar_years.update', $year) }}">@csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" class="form-control" value="{{ $year->name }}" required></div>
                    <div class="row">
                        <div class="col-4 mb-3"><label class="form-label">Year *</label><input type="text" name="year" class="form-control" value="{{ $year->year }}" required></div>
                        <div class="col-4 mb-3"><label class="form-label">Start Date *</label><input type="date" name="start_date" class="form-control" value="{{ $year->start_date->format('Y-m-d') }}" required></div>
                        <div class="col-4 mb-3"><label class="form-label">End Date *</label><input type="date" name="end_date" class="form-control" value="{{ $year->end_date->format('Y-m-d') }}" required></div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['active', 'inactive', 'closed'] as $s)<option value="{{ $s }}" {{ $year->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3"><div class="form-check mt-4"><input type="checkbox" name="is_current" class="form-check-input" value="1" id="isCurrent{{ $year->id }}" {{ $year->is_current ? 'checked' : '' }}><label class="form-check-label" for="isCurrent{{ $year->id }}">Set as Current Year</label></div></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#calendar-years-table').DataTable({
        ajax: '{{ route("settings.calendar_years.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'period', name: 'period', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
