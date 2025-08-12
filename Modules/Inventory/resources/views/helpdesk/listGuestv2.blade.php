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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-light: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 20px 60px rgba(0, 0, 0, 0.15);
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-muted: #718096;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Style tambahan untuk petugas jaga */
        /* .avatar {
            transition: all 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .bg-light {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .bg-light:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            background-color: white !important;
        } */

        .schedule-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 25px;
            border: none;
            position: relative;
            overflow: hidden;
            border-radius: 20px 20px 0 0;
        }

        .schedule-card-title {
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .schedule-card-title i {
            font-size: 1.2rem;
        }

        .schedule-container {
            padding: 20px 25px;
            background: rgba(255, 255, 255, 0.98);
        }

        .petugas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .petugas-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            background: rgba(102, 126, 234, 0.03);
            border: 1px solid rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .petugas-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .petugas-item:hover::before {
            transform: scaleY(1);
        }

        .petugas-item:hover {
            background: rgba(102, 126, 234, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .petugas-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
            margin-right: 12px;
            transition: all 0.3s ease;
        }

        .petugas-item:hover .petugas-avatar {
            transform: scale(1.1);
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .petugas-info {
            min-width: 0;
            flex: 1;
        }

        .petugas-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0 0 3px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .petugas-shift {
            font-size: 0.75rem;
            color: #667eea;
            font-weight: 500;
            background: rgba(102, 126, 234, 0.1);
            padding: 2px 8px;
            border-radius: 8px;
            display: inline-block;
        }

        /* end style petugas jaga */

        /* Floating elements background */
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            animation: float 25s infinite linear;
        }

        .shape:nth-child(1) {
            width: 120px;
            height: 120px;
            top: 10%;
            left: 80%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 80px;
            height: 80px;
            top: 70%;
            left: 10%;
            animation-delay: -8s;
        }

        .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 40%;
            right: 85%;
            animation-delay: -16s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-40px) rotate(120deg);
            }

            66% {
                transform: translateY(40px) rotate(240deg);
            }

            100% {
                transform: translateY(0px) rotate(360deg);
            }
        }

        /* Navbar */
        .navbar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: var(--shadow-light);
            padding: 15px 0;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand {
            color: var(--text-primary) !important;
        }

        .navbar-brand i {
            font-size: 1.6rem;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
        }

        .nav-btn {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .nav-btn:hover::before {
            left: 100%;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: var(--shadow-light);
            color: white;
        }

        .navbar.scrolled .nav-btn {
            background: var(--glass-bg);
            color: var(--text-primary);
            border-color: rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled .nav-btn:hover {
            background: var(--primary-gradient);
            color: white;
        }

        /* Main content */
        .main-content {
            min-height: 100vh;
            padding: 140px 0 60px;
            position: relative;
            z-index: 1;
        }

        /* Card styles */
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 28px;
            box-shadow: var(--shadow-heavy);
            overflow: hidden;
            position: relative;
            animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-card-header {
            background: var(--primary-gradient);
            padding: 40px 30px;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .table-card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: shimmer 4s ease-in-out infinite;
        }

        @keyframes shimmer {

            0%,
            100% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(180deg);
            }
        }

        .table-card-title {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 2;
        }

        .table-card-title i {
            font-size: 1.8rem;
        }

        .refresh-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .refresh-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .refresh-btn:hover::before {
            left: 100%;
        }

        .refresh-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .refresh-btn i {
            transition: transform 0.3s ease;
        }

        .refresh-btn:hover i {
            transform: rotate(180deg);
        }

        /* Table container */
        .table-container {
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.98);
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
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: var(--text-primary);
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
            background: white;
            transform: translateY(-1px);
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 20px;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 25px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 16px !important;
            padding: 12px 18px !important;
            margin: 0 5px !important;
            border: 2px solid transparent !important;
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-gradient) !important;
            color: white !important;
            border-color: transparent !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary-gradient) !important;
            color: white !important;
            border-color: transparent !important;
            font-weight: 700 !important;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
            transform: translateY(-1px);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: 0.4 !important;
            cursor: not-allowed !important;
            transform: none !important;
            background: #f8fafc !important;
            color: #a0aec0 !important;
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
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            border: none;
            padding: 20px 16px;
            font-size: 0.95rem;
            white-space: nowrap;
            text-align: center;
            position: relative;
        }

        .table thead th:first-child {
            border-top-left-radius: 16px;
        }

        .table thead th:last-child {
            border-top-right-radius: 16px;
        }

        .table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            vertical-align: middle;
            text-align: center;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 16px;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 16px;
        }

        /* Status badges */
        .badge {
            padding: 10px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.8s;
        }

        .badge:hover::before {
            left: 100%;
        }

        .badge-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .badge-warning {
            background: var(--warning-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .badge-info {
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        /* Stats cards */
        .stats-container {
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-light);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

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
                                        <i class="fas fa-info-circle fa-lg mb-2" style="color: #667eea;"></i>
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
                                    <i class="fas fa-info-circle fa-lg mb-2" style="color: #667eea;"></i>
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
                confirmButtonColor: '#667eea',
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: `rgba(0,0,0,0.4)`,
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
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: `rgba(0,0,0,0.4)`,
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
                        confirmButtonColor: '#667eea',
                        background: 'rgba(255, 255, 255, 0.95)',
                        backdrop: `rgba(0,0,0,0.4)`,
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
                confirmButtonColor: '#667eea'
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
