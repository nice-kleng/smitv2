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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 70%;
            animation-delay: -10s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 30%;
            right: 30%;
            animation-delay: -15s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-30px) rotate(120deg);
            }

            66% {
                transform: translateY(30px) rotate(240deg);
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

        .card-header {
            background: var(--primary-gradient);
            padding: 40px 30px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
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

        .card-title {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .card-title i {
            margin-right: 15px;
            font-size: 1.8rem;
        }

        .card-body {
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.98);
        }

        /* Form styles */
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 28px;
            position: relative;
        }

        .form-select,
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 20px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-2px);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid #e2e8f0;
            border-left: none;
            border-radius: 0 16px 16px 0;
            color: #667eea;
            font-size: 1.1rem;
            padding: 16px 20px;
            transition: all 0.3s ease;
        }

        .form-select:focus+.input-group-text,
        .form-control:focus+.input-group-text {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.15);
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
            border: 2px solid #e2e8f0 !important;
            border-radius: 16px !important;
            padding: 16px 20px !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: none !important;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea !important;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
            background: white !important;
            transform: translateY(-2px);
            outline: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-primary) !important;
            line-height: 1.5 !important;
            padding: 0 !important;
            font-family: 'Inter', sans-serif !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            right: 20px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #667eea transparent transparent transparent !important;
            border-width: 6px 6px 0 6px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #667eea transparent !important;
            border-width: 0 6px 6px 6px !important;
        }

        /* Select2 Dropdown */
        .select2-dropdown {
            border: 2px solid #667eea !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin-top: 8px !important;
        }

        .select2-container--default .select2-results__option {
            padding: 12px 20px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 500 !important;
            color: var(--text-primary) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }

        .select2-container--default .select2-results__option:last-child {
            border-bottom: none !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: var(--primary-gradient) !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background: rgba(102, 126, 234, 0.1) !important;
            color: #667eea !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true].select2-results__option--highlighted {
            background: var(--primary-gradient) !important;
            color: white !important;
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
            border-left: 2px solid #e2e8f0;
            pointer-events: none;
        }

        .select2-with-icon .select2-container--default .select2-selection--single {
            padding-right: 60px !important;
            border-radius: 16px 0 0 16px !important;
            border-right: none !important;
        }

        .select2-with-icon .select2-container--default .select2-selection--single:focus,
        .select2-with-icon .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea !important;
        }

        .select2-with-icon .select2-container--default .select2-selection--single:focus+.input-group-text,
        .select2-with-icon .select2-container--default.select2-container--focus .select2-selection--single+.input-group-text {
            border-color: #667eea !important;
            background: rgba(102, 126, 234, 0.15) !important;
        }

        /* Submit button */
        .submit-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 18px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
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
    </style>
</head>

<body>
    <!-- Floating background shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
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
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="main-card">
                        <div class="card-header">
                            <h1 class="card-title">
                                <i class="fas fa-tools"></i>
                                Form Laporan Kerusakan
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
            if ($(window).scrollTop() > 50) {
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
