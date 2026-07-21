<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Drip Irrigation System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    @if (app()->environment('production'))
        <link rel="icon" type="image/png" href="images/agrinexlogo.jpg" />
        <link rel="apple-touch-icon" href="images/agrinexlogo.jpg" />
        <link rel="manifest" href="images/manifest.json">
    @else
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="icon" type="image/png" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('AgrinexLogo.jpg') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('AgrinexLogo.jpg') }}" />
    @endif

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            color: white;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 12px rgba(34,197,94,0.08);
        }

        .sidebar-header {
            padding: 28px 20px 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 16px;
            margin-bottom: 10px;
            box-shadow: 0 2px 8px rgba(34,197,94,0.10);
            background: #fff;
            border: 2px solid #fff;
        }

        .sidebar-header h4 {
            margin: 0 0 2px 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar-header h4 i {
            color: #22c55e;
            font-size: 22px;
        }

        .sidebar-header small {
            color: #e0fce2;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.5px;
            display: block;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section-title {
            padding: 20px 20px 8px 20px;
            color: #bbf7d0;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-item {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 0 8px 4px 8px;
            transition: all 0.3s;
        }

        .menu-item.active {
            background: linear-gradient(90deg, #ffffff 0%, #dcfce7 100%);
            color: #166534;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(34,197,94,0.2);
            transform: translateX(2px);
        }
        
        .menu-item:hover {
            background-color: rgba(255,255,255,0.13);
            color: #fff;
        }

        .menu-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            height: var(--header-height);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 30px;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            margin: 12px 0 4px;
        }

        .stat-card-label {
            color: #6b7280;
            font-size: 14px;
        }

        /* Cards */
        .card-custom {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
        }

        .card-custom-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-custom-body {
            padding: 24px;
        }

        /* Node Status Badge */
        .node-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .node-badge.online {
            background-color: #d1fae5;
            color: #065f46;
        }

        .node-badge.offline {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Alert Badge */
        .alert-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .alert-badge.critical {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-badge.warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .alert-badge.info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Sensor Reading Display */
        .sensor-reading {
            display: flex;
            align-items: center;
            padding: 16px;
            background-color: #f9fafb;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .sensor-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            font-size: 20px;
        }

        .sensor-value {
            font-size: 24px;
            font-weight: 700;
        }

        .sensor-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Status Indicator */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-dot.online {
            background-color: #10b981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        }

        .status-dot.offline {
            background-color: #ef4444;
        }

        /* Pagination Custom Styling */
        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            color: #1e40af;
            border: 1px solid #e5e7eb;
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
            color: #1e3a8a;
            border-color: #d1d5db;
        }

        .pagination .page-item.active .page-link {
            background-color: #1e40af;
            border-color: #1e40af;
            color: white;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            background-color: #f9fafb;
            border-color: #e5e7eb;
            cursor: not-allowed;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
            outline: none;
        }

        /* Previous/Next Button Icons */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>

<body x-data="adminApp()">
    {{-- Global Splash Screen --}}
    @include('components.splash')
    
    {{-- Sidebar --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('AgrinexLogo.jpg') }}" alt="AgriNex Smart Drip Logo">
            <h4>Smart Drip</h4>
            <small>Irrigation System</small>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}"
                class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            <div class="menu-section-title">Data Management</div>

            <a href="{{ route('admin.sensor-node-data.index') }}"
                class="menu-item {{ request()->routeIs('admin.sensor-node-data.*') ? 'active' : '' }}">
                <i class="bi bi-thermometer-half"></i>
                <span>Sensor Node Data</span>
            </a>

            <a href="{{ route('admin.weather-data.index') }}"
                class="menu-item {{ request()->routeIs('admin.weather-data.*') ? 'active' : '' }}">
                <i class="bi bi-cloud-sun"></i>
                <span>Weather Data</span>
            </a>

            <a href="{{ route('admin.getdata-logs.index') }}"
                class="menu-item {{ request()->routeIs('admin.getdata-logs.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Getdata Logs</span>
            </a>

            <a href="{{ route('admin.irrigate-logs.index') }}"
                class="menu-item {{ request()->routeIs('admin.irrigate-logs.*') ? 'active' : '' }}">
                <i class="bi bi-water"></i>
                <span>Irrigate Logs</span>
            </a>

            <a href="{{ route('admin.valve-logs.index') }}"
                class="menu-item {{ request()->routeIs('admin.valve-logs.*') ? 'active' : '' }}">
                <i class="bi bi-toggle-on"></i>
                <span>Valve Logs</span>
            </a>

            <a href="{{ route('admin.node-logs.index') }}"
                class="menu-item {{ request()->routeIs('admin.node-logs.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-network"></i>
                <span>Node Logs</span>
            </a>

            <a href="{{ route('admin.json-backup.index') }}"
                class="menu-item {{ request()->routeIs('admin.json-backup.*') ? 'active' : '' }}">
                <i class="bi bi-database"></i>
                <span>JSON Backup</span>
            </a>

            <div class="menu-section-title">System</div>

            <a href="{{ route('nodes.index') }}"
                class="menu-item {{ request()->routeIs('nodes.*') ? 'active' : '' }}">
                <i class="bi bi-cpu"></i>
                <span>Sensor Nodes</span>
            </a>

            {{-- <a href="{{ route('plots.index') }}" class="menu-item {{ request()->routeIs('plots.*') ? 'active' : '' }}">
                <i class="bi bi-grid-3x3"></i>
                <span>Experimental Plots</span>
            </a> --}}

            <a href="{{ route('irrigation.index') }}"
                class="menu-item {{ request()->routeIs('irrigation.*') ? 'active' : '' }}">
                <i class="bi bi-water"></i>
                <span>Irrigation</span>
            </a>

            <a href="{{ route('alerts.index') }}"
                class="menu-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Alerts</span>
            </a>

            {{-- <a href="{{ route('weather.index') }}" class="menu-item {{ request()->routeIs('weather.*') ? 'active' : '' }}">
                <i class="bi bi-cloud-sun"></i>
                <span>Weather Data</span>
            </a> --}}

            <a href="{{ route('reports.index') }}"
                class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Reports</span>
            </a>

            <a href="{{ route('settings.index') }}"
                class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 ms-3">@yield('page-title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center">
                <div class="me-3">
                    <span class="status-dot online"></span>
                    <small class="text-muted">System Online</small>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <small class="text-muted">{{ Auth::user()->full_name }}</small>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 border-bottom">
                            <div class="small text-muted">Signed in as</div>
                            <div class="fw-bold">{{ Auth::user()->username }}</div>
                            <div class="small">
                                <span
                                    class="badge bg-{{ Auth::user()->role == 'admin' ? 'danger' : (Auth::user()->role == 'operator' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Alpine.js for reactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global Alpine.js data for admin pages
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminApp', () => ({
                showToast: false,
                toastMessage: '',
                toastType: 'info',
                
                displayToast(message, type = 'info') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 5000);
                }
            }));
        });

        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // CSRF Token for AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Setup AJAX defaults
        fetch.defaults = {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };

        // Auto-refresh session every 15 minutes to prevent 401 errors
        setInterval(function() {
            fetch('{{ route("agrinex.dashboard") }}', {
                method: 'HEAD',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).catch(function(error) {
                console.log('Session refresh ping failed:', error);
            });
        }, 15 * 60 * 1000); // 15 minutes
    </script>

    @stack('scripts')
</body>

</html>
