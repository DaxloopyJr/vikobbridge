<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VICOBRIDGE') - VICOBA Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a5f2a;
            --primary-dark: #124620;
            --primary-light: #e8f5e9;
            --secondary: #f8b500;
            --accent: #2e7d32;
            --dark: #1a1a2e;
            --light: #f5f6fa;
            --sidebar-width: 260px;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--light);
            min-height: 100vh;
        }

        /* ==================== STYLISH SIDEBAR ==================== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar.collapsed { width: 70px; }
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .sidebar-user,
        .sidebar.collapsed .nav-section,
        .sidebar.collapsed .sidebar-footer { display: none !important; }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }
        .sidebar.collapsed ~ .main-content { margin-left: 70px; }

        /* Logo */
        .logo-area {
            padding: 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #1a5f2a, #2e7d32);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .logo-text {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        /* User Mini Profile */
        .sidebar-user {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-initials { color: #fff; font-size: 0.7rem; font-weight: 600; }
        .user-info { display: flex; flex-direction: column; min-width: 0; }
        .user-name { color: #fff; font-size: 0.78rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { color: rgba(255,255,255,0.5); font-size: 0.68rem; white-space: nowrap; }

        /* Section Labels */
        .nav-section {
            padding: 14px 16px 4px;
            flex-shrink: 0;
        }
        .section-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Nav Container - override Bootstrap .nav */
        .sidebar .nav.sidebar-nav {
            display: flex !important;
            flex-direction: column !important;
            flex-wrap: nowrap !important;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 10px;
        }
        .sidebar .nav.sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar .nav.sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar .nav.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
        .sidebar .nav.sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }

        /* Nav Links - override Bootstrap .nav-link */
        .sidebar .nav.sidebar-nav .nav-link {
            color: rgba(255,255,255,0.6) !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 9px 18px !important;
            transition: all 0.2s ease;
            font-size: 0.85rem;
            display: flex !important;
            align-items: center;
            position: relative;
            border-left: 3px solid transparent;
            width: 100%;
            text-decoration: none;
            white-space: nowrap;
        }
        .sidebar .nav.sidebar-nav .nav-link:hover {
            color: rgba(255,255,255,0.9) !important;
            background: rgba(255,255,255,0.04);
            border-left-color: rgba(255,255,255,0.15);
        }
        .sidebar .nav.sidebar-nav .nav-link.active {
            color: #fff !important;
            background: rgba(26, 95, 42, 0.25);
            border-left-color: #1a5f2a;
        }
        .sidebar .nav.sidebar-nav .nav-link.active .nav-icon { color: #4caf50; }
        .nav-icon {
            width: 28px;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .nav-text { flex: 1; min-width: 0; }
        .nav-indicator {
            width: 6px;
            height: 6px;
            background: #4caf50;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            margin-left: 6px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 10px 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .collapse-btn {
            width: 100%;
            padding: 8px;
            background: rgba(255,255,255,0.06);
            border: none;
            border-radius: 8px;
            color: rgba(255,255,255,0.5);
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .collapse-btn:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .card .card-header {
            padding: 16px 24px;
            border-bottom: 1px solid #f0f0f0;
        }
        .card .card-header h5 {
            font-size: 0.95rem;
            font-weight: 700;
        }
        .card .card-header .text-muted {
            font-size: 0.8rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .text-primary { color: var(--primary) !important; }
        .bg-primary { background: var(--primary) !important; }

        .badge-trial { background: #fff3cd; color: #856404; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-expired { background: #f8d7da; color: #721c24; }
        .badge-pending { background: #cce5ff; color: #004085; }

        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            border-top: none;
            background: #f8f9fa;
        }

        .wizard-step {
            display: none;
        }

        .wizard-step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .step-item {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: 600;
            color: #666;
            position: relative;
        }

        .step-item.active {
            background: var(--primary);
            color: #fff;
        }

        .step-item.completed {
            background: var(--accent);
            color: #fff;
        }

        /* ==================== DataTables Custom Styling ==================== */
        .card > .card-body > .dataTables_wrapper,
        .card-body > .dataTables_wrapper {
            padding: 16px;
        }

        .dataTables_wrapper {
            margin: 0;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 16px;
            margin-left: 4px;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            padding: 6px 28px 6px 12px;
            border: 1px solid #e0e0e0;
            font-size: 0.85rem;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
            margin-right: 4px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            padding: 6px 12px;
            border: 1px solid #e0e0e0;
            font-size: 0.85rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1a5f2a;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(26, 95, 42, 0.25);
        }

        /* Table styling */
        table.dataTable {
            border-collapse: collapse !important;
            margin: 12px 0 !important;
            border-radius: 10px;
            overflow: hidden;
        }
        table.dataTable thead th {
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #444;
            background: #f0f2f5;
            padding: 14px 10px;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }
        table.dataTable thead th:first-child {
            border-top-left-radius: 10px;
        }
        table.dataTable thead th:last-child {
            border-top-right-radius: 10px;
        }
        table.dataTable tbody td {
            padding: 10px;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid #f0f0f0;
        }
        table.dataTable tbody tr:last-child td {
            border-bottom: none;
        }
        table.dataTable tbody tr:hover {
            background-color: #f0f7f0;
        }
        table.dataTable thead th:first-child,
        table.dataTable tbody td:first-child {
            padding-left: 16px;
        }
        table.dataTable thead th:last-child,
        table.dataTable tbody td:last-child {
            padding-right: 16px;
        }

        /* Info and Pagination row */
        .dataTables_wrapper .dataTables_info {
            margin-top: 16px;
            margin-left: 4px;
            font-size: 0.85rem;
            color: #888;
        }
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 16px;
            margin-right: 4px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 0.85rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #1a5f2a !important;
            color: #fff !important;
            border-color: #1a5f2a !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e8f5e9 !important;
            color: #1a5f2a !important;
            border-color: #1a5f2a !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
        }

        /* Action buttons in tables */
        .btn-group-sm > .btn, .btn-group-sm > .form-control {
            padding: 4px 8px;
            font-size: 0.8rem;
        }

        /* Empty state */
        .dataTables_empty {
            text-align: center;
            padding: 40px !important;
            color: #999;
            font-size: 0.9rem;
        }

        /* Submenu tags under settings links */
        .submenu-hint {
            padding: 0 18px 8px 46px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }
        .submenu-tag {
            font-size: 0.6rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .tag-savings { background: rgba(76, 175, 80, 0.15); color: #81c784; }
        .tag-contrib { background: rgba(33, 150, 243, 0.15); color: #64b5f6; }
        .tag-fee { background: rgba(156, 39, 176, 0.15); color: #ba68c8; }
        .tag-fine { background: rgba(244, 67, 54, 0.15); color: #e57373; }
        .tag-project { background: rgba(255, 152, 0, 0.15); color: #ffb74d; }
        .tag-year { background: rgba(0, 150, 136, 0.15); color: #4db6ac; }
        .tag-status { background: rgba(96, 125, 139, 0.15); color: #90a4ae; }
        .tag-active { background: rgba(76, 175, 80, 0.15); color: #81c784; }
        .tag-rate { background: rgba(3, 169, 244, 0.15); color: #4fc3f7; }
        .tag-pct { background: rgba(255, 87, 34, 0.15); color: #ff8a65; }
        .tag-withdraw { background: rgba(121, 85, 72, 0.15); color: #a1887f; }

        /* Colored Tabs for Settings */
        .nav-tabs-colored { border-bottom: none; gap: 6px; margin-bottom: 20px; }
        .nav-tabs-colored .nav-link {
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #fff;
            background: #6c757d;
            transition: all 0.2s;
        }
        .nav-tabs-colored .nav-link:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }
        .nav-tabs-colored .nav-link.active {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }
        .nav-tabs-colored .nav-link.tab-green { background: #1a5f2a; }
        .nav-tabs-colored .nav-link.tab-green.active { background: #124620; }
        .nav-tabs-colored .nav-link.tab-blue { background: #0d6efd; }
        .nav-tabs-colored .nav-link.tab-blue.active { background: #0b5ed7; }
        .nav-tabs-colored .nav-link.tab-orange { background: #fd7e14; }
        .nav-tabs-colored .nav-link.tab-orange.active { background: #e56b0a; }
        .nav-tabs-colored .nav-link.tab-purple { background: #6f42c1; }
        .nav-tabs-colored .nav-link.tab-purple.active { background: #5a36a0; }
        .nav-tabs-colored .nav-link.tab-teal { background: #20c997; }
        .nav-tabs-colored .nav-link.tab-teal.active { background: #1aab7d; }
        .nav-tabs-colored .nav-link.tab-red { background: #dc3545; }
        .nav-tabs-colored .nav-link.tab-red.active { background: #bb2d3b; }
        .nav-tabs-colored .nav-link.tab-dark { background: #343a40; }
        .nav-tabs-colored .nav-link.tab-dark.active { background: #1d2124; }

        @media (max-width: 768px) {
            .sidebar { width: 70px; height: 100vh; overflow: hidden; }
            .sidebar .nav-text,
            .sidebar .logo-text,
            .sidebar .sidebar-user,
            .sidebar .nav-section,
            .sidebar .sidebar-footer { display: none !important; }
            .sidebar .logo-area { justify-content: center; padding: 16px 8px; }
            .sidebar .nav.sidebar-nav { overflow: visible !important; }
            .sidebar .nav.sidebar-nav .nav-link { padding: 10px 20px !important; justify-content: center; }
            .sidebar .nav.sidebar-nav .nav-link .nav-icon { width: auto; font-size: 1.2rem; }
            .main-content { margin-left: 70px; }
            .nav-tabs-colored { flex-wrap: wrap; }
            .nav-tabs-colored .nav-link { font-size: 0.75rem; padding: 8px 12px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.topbar')
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }

            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(function(el) {
                    const bsAlert = new bootstrap.Alert(el);
                    setTimeout(() => bsAlert.close(), 5000);
                });
            }, 100);
        });

        // Global DataTables default configuration
        $.extend(true, $.fn.dataTable.defaults, {
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...',
                emptyTable: 'No records found',
                zeroRecords: 'No matching records found',
                search: '',
                searchPlaceholder: 'Search...',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0 to 0 of 0 entries',
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>'
                }
            },
            drawCallback: function(settings) {
                // Re-initialize any Bootstrap tooltips after table draw
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
