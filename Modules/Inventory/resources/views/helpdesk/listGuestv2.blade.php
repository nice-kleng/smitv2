<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk RSI Jombang - Antrean Perbaikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
    <style>
        :root {
            --bg: #eef3f6;
            --surface: #ffffff;
            --surface-alt: #f7fafb;
            --ink: #16212b;
            --ink-muted: #516170;
            --line: #d6e0e7;
            --brand: #0f766e;
            --brand-strong: #115e59;
            --brand-soft: #d9ece9;
            --success: #15803d;
            --success-soft: #dcfce7;
            --warning: #b45309;
            --warning-soft: #fef3c7;
            --info: #0369a1;
            --info-soft: #e0f2fe;
            --shadow-soft: 0 8px 24px rgba(22, 33, 43, 0.08);
            --radius: 14px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: var(--ink);
            background-color: var(--bg);
            background-image:
                linear-gradient(rgba(22, 33, 43, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(22, 33, 43, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 320px;
            background: linear-gradient(180deg, #dfe9ee 0%, rgba(223, 233, 238, 0) 100%);
            pointer-events: none;
            z-index: -1;
        }

        .schedule-card-header {
            background: var(--surface-alt);
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
        }

        .schedule-card-title {
            color: var(--ink);
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .schedule-card-title i {
            font-size: 1rem;
            color: var(--brand);
        }

        .schedule-container {
            padding: 16px 20px;
            background: var(--surface);
        }

        .petugas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .petugas-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            background: var(--surface-alt);
            border: 1px solid var(--line);
            border-radius: 10px;
        }

        .petugas-avatar {
            width: 34px;
            height: 34px;
            background: var(--brand-soft);
            color: var(--brand-strong);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-right: 10px;
        }

        .petugas-info {
            min-width: 0;
            flex: 1;
        }

        .petugas-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--ink);
            margin: 0 0 2px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .petugas-shift {
            font-size: 0.75rem;
            color: var(--ink-muted);
            font-weight: 500;
        }

        /* end style petugas jaga */

        .floating-shapes { display: none; }
        .shape { display: none; }

        /* Navbar */
        .navbar {
            background: #f8fbfc;
            border-bottom: 1px solid var(--line);
            padding: 14px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .navbar.scrolled {
            background: #f8fbfc;
            box-shadow: 0 4px 14px rgba(22, 33, 43, 0.06);
            padding: 12px 0;
        }

        .navbar-brand {
            color: var(--ink) !important;
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar.scrolled .navbar-brand { color: var(--ink) !important; }

        .navbar-brand i {
            font-size: 1.1rem;
            color: var(--brand);
            background: var(--brand-soft);
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            background: var(--surface);
            color: var(--ink);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 9px 14px;
            font-weight: 600;
            font-size: 0.88rem;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .nav-btn:hover {
            background: var(--surface-alt);
            border-color: #b8c8d2;
            color: var(--ink);
        }

        .navbar.scrolled .nav-btn {
            background: var(--surface);
            color: var(--ink);
            border-color: var(--line);
        }

        .navbar.scrolled .nav-btn:hover {
            background: var(--brand);
            color: #fff;
            border-color: var(--brand);
        }

        /* Main content */
        .main-content {
            min-height: 100vh;
            padding: 110px 0 48px;
            position: relative;
            z-index: 1;
        }

        /* Card styles */
        .main-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            position: relative;
        }

        .table-card-header {
            background: var(--surface-alt);
            padding: 18px 22px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .table-card-title {
            color: var(--ink);
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-card-title i {
            color: var(--brand);
            font-size: 1.1rem;
        }

        .refresh-btn {
            background: var(--surface);
            border: 1px solid var(--line);
            color: var(--ink);
            padding: 9px 14px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .refresh-btn:hover {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .refresh-btn i {
            transition: transform 0.3s ease;
        }

        .refresh-btn:hover i {
            transform: rotate(180deg);
        }

        /* Table container */
        .table-container {
            padding: 20px 22px;
            background: var(--surface);
        }

        /* DataTables customization */
        .dataTables_wrapper {
            margin-top: 20px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 25px;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #c9d6df;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
            color: var(--ink);
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
            outline: none;
            background: #fff;
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--ink-muted);
            font-weight: 500;
            margin-top: 20px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 25px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            padding: 8px 12px !important;
            margin: 0 3px !important;
            border: 1px solid var(--line) !important;
            background: var(--surface) !important;
            color: var(--ink) !important;
            font-weight: 600 !important;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease !important;
            text-decoration: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--brand-soft) !important;
            color: var(--brand-strong) !important;
            border-color: #9fd3cd !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--brand) !important;
            color: #fff !important;
            border-color: var(--brand) !important;
            font-weight: 700 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.45 !important;
            cursor: not-allowed !important;
            background: var(--surface-alt) !important;
            color: #8a9aa7 !important;
        }

        /* Table styles */
        .table {
            width: 100% !important;
            margin-bottom: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table thead th {
            background: var(--surface-alt);
            color: var(--ink);
            font-weight: 700;
            border-bottom: 2px solid var(--line);
            padding: 14px 12px;
            font-size: 0.9rem;
            white-space: nowrap;
            text-align: center;
        }

        .table tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #e6edf2;
            vertical-align: middle;
            text-align: center;
            font-weight: 500;
            color: var(--ink-muted);
        }

        .table tbody tr:hover {
            background: #f4f8fa;
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 16px;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 16px;
        }

        /* Status badges */
        .badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: none;
            letter-spacing: 0.1px;
        }

        .badge-success {
            background: var(--success-soft);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .badge-warning {
            background: var(--warning-soft);
            color: var(--warning);
            border: 1px solid #fde68a;
        }

        .badge-info {
            background: var(--info-soft);
            color: var(--info);
            border: 1px solid #bae6fd;
        }

        /* Stats cards */
        .stats-container {
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-soft);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--ink-muted);
            font-weight: 600;
            font-size: 0.92rem;
        }

        .stat-icon {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: var(--brand);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .main-content {
                padding: 120px 0 40px;
            }

            .table-card-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 30px 20px;
            }

            .table-card-title {
                font-size: 1.6rem;
            }

            .refresh-btn {
                width: 100%;
                justify-content: center;
            }

            .table-container {
                padding: 30px 15px;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .nav-btn {
                padding: 10px 20px;
                font-size: 0.85rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .schedule-card-header {
                padding: 15px 20px;
            }

            .schedule-card-title {
                font-size: 1.1rem;
            }

            .schedule-container {
                padding: 15px 20px;
            }

            .petugas-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .petugas-item {
                padding: 10px 12px;
            }

            .petugas-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
                margin-right: 10px;
            }

            .petugas-name {
                font-size: 0.85rem;
            }

            .petugas-shift {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .petugas-grid {
                gap: 8px;
            }

            .petugas-item {
                padding: 8px 10px;
            }
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Background kept minimal for clarity -->

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-headset"></i>
                    Helpdesk RSI Jombang
                </a>
                <div class="nav-buttons">
                    <a href="{{ route('helpdesk.index') }}" class="nav-btn">
                        <i class="fas fa-home"></i>
                        <span class="d-none d-sm-inline">Home</span>
                    </a>
                    <a href="{{ route('login') }}" class="nav-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="d-none d-sm-inline">Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number" id="pendingCount">
                                {{ $total_pending }}
                            </div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-number" id="completedCount">
                                {{ $total_selesai }}
                            </div>
                            <div class="stat-label">Selesai</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="stat-number" id="totalCount">
                                {{ $total_tickets }}
                            </div>
                            <div class="stat-label">Total Aduan</div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row g-3">
                    <div class="col-12">
                        <div class="main-card">
                            <div class="schedule-card-header">
                                <h4 class="schedule-card-title">
                                    <i class="fas fa-user-clock"></i>
                                    Petugas Jaga Hari Ini
                                </h4>
                            </div>
                            <div class="schedule-container">
                                @if ($petugasHariIni->isEmpty())
                                    <div class="text-center py-2">
                                        <i class="fas fa-info-circle fa-lg mb-2" style="color: var(--brand);"></i>
                                        <p class="text-muted small mb-0">Tidak ada petugas yang bertugas hari ini</p>
                                    </div>
                                @else
                                    <div class="petugas-grid">
                                        @foreach ($petugasHariIni as $petugas)
                                            <div class="petugas-item">
                                                <div class="petugas-avatar">
                                                    {{ substr($petugas->jadwal->user->name, 0, 1) }}
                                                </div>
                                                <div class="petugas-info">
                                                    <h6 class="petugas-name">{{ $petugas->jadwal->user->name }}</h6>
                                                    <span class="petugas-shift">
                                                        {{ date('H:i', strtotime($petugas->jadwal->shift->start_time)) }}-{{ date('H:i', strtotime($petugas->jadwal->shift->end_time)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Main Table Card -->
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="main-card">
                        <div class="table-card-header">
                            <h1 class="table-card-title">
                                <i class="fas fa-ticket-alt"></i>
                                Daftar Tiket Perbaikan
                            </h1>
                            <button class="refresh-btn" onclick="refreshTable()">
                                <i class="fas fa-sync-alt"></i>
                                <span class="d-none d-sm-inline">Refresh Data</span>
                            </button>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table" id="ticketTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Aduan</th>
                                            <th>Tanggal Pengaduan</th>
                                            <th>Unit</th>
                                            <th>Ruangan</th>
                                            <th>Kerusakan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->kd_ticket }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}
                                                </td>
                                                <td>{{ $item->ruangan->unit->nama_unit }}</td>
                                                <td>{{ $item->ruangan->nama_ruangan }}</td>
                                                <td>{{ $item->detail_aduan }}</td>
                                                <td>
                                                    @if ($item->status == 1)
                                                        <span class="badge badge-success">Selesai</span>
                                                    @elseif($item->status == 2)
                                                        <span class="badge badge-info">Diproses</span>
                                                    @else
                                                        <span class="badge badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3" style="margin-top: 30px;">
                <div class="col-12">
                    <div class="main-card">
                        <div class="schedule-card-header">
                            <h4 class="schedule-card-title">
                                <i class="fas fa-user-clock"></i>
                                Petugas Jaga Hari Ini
                            </h4>
                        </div>
                        <div class="schedule-container">
                            @if ($petugasHariIni->isEmpty())
                                <div class="text-center py-2">
                                    <i class="fas fa-info-circle fa-lg mb-2" style="color: var(--brand);"></i>
                                    <p class="text-muted small mb-0">Tidak ada petugas yang bertugas hari ini</p>
                                </div>
                            @else
                                <div class="petugas-grid">
                                    @foreach ($petugasHariIni as $petugas)
                                        <div class="petugas-item">
                                            <div class="petugas-avatar">
                                                {{ substr($petugas->jadwal->user->name, 0, 1) }}
                                            </div>
                                            <div class="petugas-info">
                                                <h6 class="petugas-name">{{ $petugas->jadwal->user->name }}</h6>
                                                <span class="petugas-shift">
                                                    {{ date('H:i', strtotime($petugas->jadwal->shift->start_time)) }}-{{ date('H:i', strtotime($petugas->jadwal->shift->end_time)) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script type="module">
        window.Echo.channel('ticket')
            .listen('.ticket.created', (e) => {
                // Cek apakah browser mendukung notifikasi
                if ('Notification' in window) {
                    if (Notification.permission === 'default') {
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                console.log('test');
                                showNotification(e);
                            }
                        });
                    } else if (Notification.permission === 'granted') {
                        showNotification(e);
                    }
                }
                var audio = new Audio("{{ asset('/notification.wav') }}");
                audio.play();
                refreshTable();
                if (typeof toastr !== 'undefined') {
                    toastr.success(e.message, 'Notifikasi Baru');
                }
            })
            .error((error) => {
                console.error('Echo error:', error);
                // Only show error if it's not an authorization error
                if (error.type !== 'AuthError' && typeof toastr !== 'undefined') {
                    toastr.error('Gagal terhubung ke sistem notifikasi', 'Error');
                }
            });

        // Fungsi untuk menampilkan notifikasi
        function showNotification(e) {
            const notificationOptions = {
                body: e.message || 'Pengaduan baru telah dibuat',
                icon: '/favicon.ico',
                tag: 'ticket-notification',
                requireInteraction: true
            };

            const notification = new Notification('Pengaduan Baru', notificationOptions);

            // Optional: Handle notification click
            notification.onclick = function(event) {
                event.preventDefault();
                window.focus(); // Fokus ke window aplikasi
                // Optional: Navigate to ticket page
                // window.location.href = '/tickets';
                notification.close();
            };
        }
    </script>

    <script>
        // SweetAlert notifications
        @if (session('success'))
            Swal.fire({
                title: 'Success',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#0f766e',
                background: '#ffffff',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        let ticketTable;

        $(document).ready(function() {
            // Initialize DataTable
            ticketTable = $('#ticketTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                // order: [
                //     [2, 'desc']
                // ], // Sort by date created descending
                columnDefs: [{
                    targets: [0, 6], // No and Status columns
                    orderable: false
                }],
                drawCallback: function(settings) {
                    // Add animation to new rows
                    $(this.api().table().body()).find('tr').each(function(index) {
                        $(this).css({
                            'opacity': '0',
                            'transform': 'translateY(20px)'
                        }).delay(index * 50).animate({
                            'opacity': '1'
                        }, {
                            duration: 400,
                            step: function(now, fx) {
                                if (fx.prop === 'opacity') {
                                    $(this).css('transform', 'translateY(' + (20 * (
                                        1 - now)) + 'px)');
                                }
                            }
                        });
                    });
                }
            });

            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Auto refresh every 30 seconds
            // setInterval(function() {
            //     refreshTable();
            // }, 60000);

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });

        // Refresh table function
        function refreshTable() {
            const refreshBtn = document.querySelector('.refresh-btn');
            const refreshIcon = refreshBtn.querySelector('i');

            // Add loading state
            refreshIcon.className = 'loading-spinner';
            refreshBtn.disabled = true;

            // Show loading toast
            Swal.fire({
                title: 'Memuat data...',
                text: 'Sedang mengambil data terbaru',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                background: '#ffffff',
                timer: 2000,
                timerProgressBar: true
            });

            // Simulate refresh (in real implementation, this would be an AJAX call)
            setTimeout(function() {
                // Reset button state
                refreshIcon.className = 'fas fa-sync-alt';
                refreshBtn.disabled = false;

                // Reload page to get fresh data
                window.location.reload();
            }, 2000);
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + R for refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                refreshTable();
            }

            // Escape to clear search
            if (e.key === 'Escape') {
                if (ticketTable) {
                    ticketTable.search('').draw();
                }
            }
        });

        // Add hover effects to stat cards
        $('.stat-card').on('mouseenter', function() {
            $(this).find('.stat-number').addClass('animate__animated animate__pulse');
        }).on('mouseleave', function() {
            $(this).find('.stat-number').removeClass('animate__animated animate__pulse');
        });

        // Enhanced table row interactions
        $('#ticketTable tbody').on('click', 'tr', function() {
            // Add subtle selection effect
            $(this).siblings().removeClass('table-active');
            $(this).addClass('table-active');

            // Optional: Show ticket details in modal
            const ticketCode = $(this).find('td:eq(1)').text();
            console.log('Selected ticket:', ticketCode);
        });

        // Progress bar for page loading
        $(window).on('load', function() {
            // Hide any loading indicators
            $('.loading-overlay').fadeOut();

            // Show success message for data load
            setTimeout(function() {
                const totalTickets = $('#totalCount').text();
                if (totalTickets > 0) {
                    Swal.fire({
                        title: 'Data Berhasil Dimuat',
                        text: `Menampilkan ${totalTickets} tiket aduan`,
                        icon: 'success',
                        confirmButtonColor: '#0f766e',
                        background: '#ffffff',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        position: 'top-end',
                        toast: true
                    });
                }
            }, 500);
        });

        // Add touch gestures for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });

        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleGesture();
        });

        function handleGesture() {
            if (touchEndX < touchStartX - 50) {
                // Swipe left - go to next page if possible
                const nextBtn = $('.dataTables_paginate .paginate_button.next:not(.disabled)');
                if (nextBtn.length) {
                    nextBtn.click();
                }
            }

            if (touchEndX > touchStartX + 50) {
                // Swipe right - go to previous page if possible
                const prevBtn = $('.dataTables_paginate .paginate_button.previous:not(.disabled)');
                if (prevBtn.length) {
                    prevBtn.click();
                }
            }
        }

        // Add print functionality
        function printTable() {
            window.print();
        }

        // Add export functionality (would need additional libraries in real implementation)
        function exportToCSV() {
            Swal.fire({
                title: 'Export Data',
                text: 'Fitur export akan segera tersedia',
                icon: 'info',
                confirmButtonColor: '#0f766e'
            });
        }

        // Service Worker registration for offline functionality
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('ServiceWorker registration successful');
            }).catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
        }
    </script>

    <!-- Print styles -->
    <style media="print">
        body {
            background: white !important;
        }

        .navbar,
        .refresh-btn,
        .floating-shapes {
            display: none !important;
        }

        .main-content {
            padding: 20px 0 !important;
        }

        .main-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }

        .table-card-header {
            background: #f8f9fa !important;
            color: #333 !important;
        }

        .badge {
            border: 1px solid #ddd !important;
            color: #333 !important;
            background: #f8f9fa !important;
        }
    </style>
</body>

</html>
