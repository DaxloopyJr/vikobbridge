@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Activity Logs</h4>
            <p class="text-muted mb-0">Track all user activities in the group</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle" id="activity-logs-table">
                <thead>
                    <tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>IP Address</th><th>Date & Time</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#activity-logs-table').DataTable({
        ajax: '{{ route("activity-logs.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'user', name: 'user' },
            { data: 'action', name: 'action', orderable: false },
            { data: 'description', name: 'description' },
            { data: 'ip_address', name: 'ip_address', orderable: false },
            { data: 'created_at', name: 'created_at' }
        ],
        order: [[0, 'desc']],
        pageLength: 50
    });
});
</script>
@endpush
