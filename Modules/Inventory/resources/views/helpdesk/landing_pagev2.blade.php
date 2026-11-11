<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Helpdesk RSI Jombang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            --accent: #b45309;
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

        /* Floating elements background */
        .floating-shapes {
            display: none;
        }

        /* Interactive 3D IT scene */
        .it-scene {
            position: relative;
            height: 220px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, #1f3b47 0%, #294e58 55%, #1f6f67 100%);
            border: 1px solid #2f5660;
            overflow: hidden;
            box-shadow: 0 14px 30px rgba(22, 33, 43, 0.16);
            perspective: 1000px;
        }

        .it-scene-stage {
            position: absolute;
            inset: 0;
            transform-style: preserve-3d;
            transition: transform 0.15s ease-out;
            will-change: transform;
        }

        .scene-layer {
            position: absolute;
            transform-style: preserve-3d;
            transition: transform 0.2s ease-out;
            will-change: transform;
        }

        .scene-svg {
            display: block;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 18px rgba(15, 23, 42, 0.25));
        }

        .layer-grid {
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 34px 34px;
            opacity: 0.3;
            transform: translateZ(10px);
        }

        .layer-server {
            left: 8%;
            bottom: 12px;
            width: 110px;
            transform: translateZ(30px);
            animation: sceneFloatA 6s ease-in-out infinite;
        }

        .layer-laptop {
            left: 50%;
            bottom: 6px;
            width: 230px;
            margin-left: -115px;
            transform: translateZ(70px);
            animation: sceneFloatB 7s ease-in-out infinite;
        }

        .layer-gear {
            right: 16%;
            top: 18px;
            width: 82px;
            transform: translateZ(90px);
            animation: sceneSpinFloat 12s linear infinite;
        }

        .layer-wifi {
            right: 8%;
            bottom: 26px;
            width: 96px;
            transform: translateZ(55px);
            animation: sceneFloatA 5.5s ease-in-out infinite reverse;
        }

        .layer-tool {
            left: 18%;
            top: 18px;
            width: 72px;
            transform: translateZ(80px);
            animation: sceneFloatB 6.5s ease-in-out infinite reverse;
        }

        .layer-badge {
            left: 50%;
            top: 14px;
            transform: translateX(-50%) translateZ(110px);
        }

        .scene-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            color: #f4fbfa;
            font-weight: 600;
            font-size: 0.85rem;
            background: rgba(15, 118, 110, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.2);
        }

        .scene-hint {
            position: absolute;
            right: 14px;
            bottom: 10px;
            margin: 0;
            color: rgba(244, 251, 250, 0.85);
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        @keyframes sceneFloatA {
            0%, 100% { margin-bottom: 0; }
            50% { margin-bottom: 8px; }
        }

        @keyframes sceneFloatB {
            0%, 100% { margin-bottom: 0; }
            50% { margin-bottom: -6px; }
        }

        @keyframes sceneSpinFloat {
            0% { rotate: 0deg; }
            100% { rotate: 360deg; }
        }

        @media (max-width: 768px) {
            .it-scene {
                height: 180px;
            }

            .layer-laptop {
                width: 180px;
                margin-left: -90px;
            }

            .layer-server {
                width: 86px;
                left: 4%;
            }

            .layer-gear, .layer-wifi, .layer-tool {
                transform: translateZ(40px);
            }

            .layer-gear { width: 64px; right: 10%; }
            .layer-wifi { width: 74px; right: 2%; bottom: 18px; }
            .layer-tool { width: 56px; left: 10%; }
            .scene-hint { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            .scene-layer, .it-scene-stage {
                animation: none !important;
                transition: none !important;
            }
        }

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
            letter-spacing: 0.1px;
        }

        .navbar.scrolled .navbar-brand {
            color: var(--ink) !important;
        }

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

        .card-header {
            background: var(--surface-alt);
            padding: 22px 24px;
            border-bottom: 1px solid var(--line);
        }

        .card-title {
            color: var(--ink);
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--brand);
            font-size: 1.1rem;
        }

        .card-body {
            padding: 28px 24px;
            background: var(--surface);
        }

        /* Form styles */
        .form-label {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 28px;
            position: relative;
        }

        .form-select,
        .form-control {
            border: 1.5px solid #c9d6df;
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
            background: #fff;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: var(--surface-alt);
            border: 1.5px solid #c9d6df;
            border-left: none;
            border-radius: 0 10px 10px 0;
            color: var(--ink-muted);
            font-size: 1rem;
            padding: 13px 16px;
            transition: border-color 0.2s ease, color 0.2s ease;
        }

        .form-select:focus+.input-group-text,
        .form-control:focus+.input-group-text {
            border-color: var(--brand);
            color: var(--brand);
        }

        textarea.form-control {
            min-height: 140px;
            resize: vertical;
            font-family: inherit;
        }

        /* Select2 Custom Styling */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: auto !important;
            border: 1.5px solid #c9d6df !important;
            border-radius: 10px !important;
            padding: 13px 16px !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            background: #fff !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15) !important;
            background: #fff !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--ink) !important;
            line-height: 1.5 !important;
            padding: 0 !important;
            font-family: 'Inter', sans-serif !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #7a8a97 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7f8f transparent transparent transparent !important;
            border-width: 6px 6px 0 6px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #6b7f8f transparent !important;
            border-width: 0 6px 6px 6px !important;
        }

        /* Select2 Dropdown */
        .select2-dropdown {
            border: 1px solid #c9d6df !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 24px rgba(22, 33, 43, 0.12) !important;
            background: #fff !important;
            margin-top: 6px !important;
        }

        .select2-container--default .select2-results__option {
            padding: 10px 14px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500 !important;
            color: var(--ink) !important;
            border-bottom: 1px solid #edf2f5 !important;
        }

        .select2-container--default .select2-results__option:last-child {
            border-bottom: none !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: var(--brand) !important;
            color: #fff !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: var(--brand-soft) !important;
            color: var(--brand-strong) !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true].select2-results__option--highlighted {
            background: var(--brand) !important;
            color: #fff !important;
        }

        /* Fix untuk input group dengan select2 */
        .select2-with-icon {
            position: relative;
        }

        .select2-with-icon .input-group-text {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
            border-left: 1.5px solid #c9d6df;
            pointer-events: none;
        }

        .select2-with-icon .select2-container--default .select2-selection--single {
            padding-right: 56px !important;
            border-radius: 10px 0 0 10px !important;
            border-right: none !important;
        }

        .select2-with-icon .select2-container--default .select2-selection--single:focus,
        .select2-with-icon .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--brand) !important;
        }

        .select2-with-icon .select2-container--default .select2-selection--single:focus+.input-group-text,
        .select2-with-icon .select2-container--default.select2-container--focus .select2-selection--single+.input-group-text {
            border-color: var(--brand) !important;
            color: var(--brand);
        }

        /* Submit button */
        .submit-btn {
            background: var(--brand);
            color: #fff;
            border: 1px solid var(--brand-strong);
            border-radius: 10px;
            padding: 14px 20px;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            width: 100%;
        }

        .submit-btn:hover {
            background: var(--brand-strong);
            border-color: var(--brand-strong);
            color: #fff;
        }

        .submit-btn:active {
            background: #0d4f4a;
        }

        .submit-btn:focus-visible {
            outline: 3px solid rgba(15, 118, 110, 0.3);
            outline-offset: 2px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .main-content {
                padding: 120px 0 40px;
            }

            .card-header {
                padding: 30px 20px;
            }

            .card-title {
                font-size: 1.6rem;
            }

            .card-body {
                padding: 30px 20px;
            }

            .nav-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .nav-btn {
                padding: 10px 20px;
                font-size: 0.85rem;
            }

            .select2-container--default .select2-selection--single {
                padding: 14px 18px !important;
            }

            .select2-with-icon .select2-container--default .select2-selection--single {
                padding-right: 55px !important;
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
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
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

        /* .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .list-group-item {
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: rgba(102, 126, 234, 0.05) !important;
            transform: translateX(5px);
        } */

        /* start style card jadwal jaga*/
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
                    <a href="{{ route('helpdesk.antrean') }}" class="nav-btn">
                        <i class="fas fa-list-ol"></i>
                        <span class="d-none d-sm-inline">On Progress</span>
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

            <div class="row justify-content-center mb-3">
                <div class="col-lg-6 col-md-8">
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
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="main-card">
                        <div class="card-header">
                            <h1 class="card-title">
                                <i class="fas fa-tools"></i>
                                Tiket Permintaan Layanan IT
                            </h1>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('helpdesk.store-ticket') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="ruangan_id" class="form-label">
                                        <i class="fas fa-building me-2"></i>Unit
                                    </label>
                                    <div class="input-group select2-with-icon">
                                        <select id="ruangan_id" name="ruangan_id" class="form-select" required>
                                            <option value="" disabled selected>Pilih Unit</option>
                                            @foreach ($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}">
                                                    {{ Str::ucfirst($ruangan->unit->nama_unit) }} -
                                                    {{ Str::ucfirst($ruangan->nama_ruangan) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-text">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="detail_aduan" class="form-label">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Deskripsi Kerusakan
                                    </label>
                                    <div class="input-group">
                                        <textarea id="detail_aduan" name="detail_aduan" class="form-control"
                                            placeholder="Jelaskan detail kerusakan yang terjadi dengan lengkap...&#10;&#10;Contoh:&#10;- Komputer tidak bisa menyala&#10;- Printer tidak bisa mencetak&#10;- Jaringan internet terputus&#10;- AC tidak dingin"
                                            required></textarea>
                                        <span class="input-group-text align-items-start pt-3">
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" class="submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Kirim Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(window).scrollTop() > 20) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });

        $(document).ready(function() {
            // Initialize Select2
            $('#ruangan_id').select2({
                placeholder: 'Pilih Unit',
                width: '100%',
                dropdownParent: $('.select2-with-icon')
            });

            // Handle focus events for input group styling
            $('#ruangan_id').on('select2:open', function() {
                $(this).closest('.select2-with-icon').find('.input-group-text').addClass('focused');
            });

            $('#ruangan_id').on('select2:close', function() {
                $(this).closest('.select2-with-icon').find('.input-group-text').removeClass('focused');
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>
