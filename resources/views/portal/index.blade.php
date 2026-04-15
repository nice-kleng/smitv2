<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Portal Link SIMIT RSI Jombang - Kumpulan link aplikasi dan layanan">
    <meta name="author" content="IT RSI Jombang">

    <title>Portal SIMIT RSI Jombang</title>

    <!-- Fonts -->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 4 -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --portal-primary: #4e73df;
            --portal-primary-dark: #2e59d9;
            --portal-gradient-start: #667eea;
            --portal-gradient-end: #764ba2;
            --portal-bg: #f0f2f8;
            --portal-card-shadow: 0 8px 32px rgba(78, 115, 223, 0.08);
            --portal-card-hover-shadow: 0 16px 48px rgba(78, 115, 223, 0.18);
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--portal-bg);
            min-height: 100vh;
        }

        /* ===== HERO HEADER ===== */
        .portal-hero {
            background: linear-gradient(135deg, var(--portal-gradient-start) 0%, var(--portal-gradient-end) 100%);
            padding: 3rem 0 4rem;
            position: relative;
            overflow: hidden;
        }

        .portal-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .portal-hero::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
        }

        .portal-hero h1 {
            color: #fff;
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .portal-hero p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1.05rem;
            font-weight: 300;
        }

        .portal-hero .hero-icon {
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
        }

        /* ===== TAB FILTERS ===== */
        .portal-tabs {
            margin-top: -1.8rem;
            position: relative;
            z-index: 10;
        }

        .portal-tabs .nav-pills {
            background: #fff;
            border-radius: 50px;
            padding: 5px;
            display: inline-flex;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .portal-tabs .nav-link {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            transition: all 0.3s ease;
            border: none;
        }

        .portal-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--portal-gradient-start), var(--portal-gradient-end));
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
        }

        .portal-tabs .nav-link:hover:not(.active) {
            color: var(--portal-primary);
            background: rgba(78, 115, 223, 0.06);
        }

        .portal-tabs .badge-count {
            font-size: 0.7rem;
            padding: 2px 7px;
            border-radius: 20px;
            margin-left: 4px;
            font-weight: 700;
        }

        /* ===== PORTAL CARDS ===== */
        .portal-grid {
            padding: 2rem 0 3rem;
        }

        .portal-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.8rem;
            text-align: center;
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: var(--portal-card-shadow);
            border: 1px solid rgba(78, 115, 223, 0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--portal-gradient-start), var(--portal-gradient-end));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--portal-card-hover-shadow);
        }

        .portal-card:hover::before {
            opacity: 1;
        }

        .portal-card-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            font-size: 2rem;
            transition: all 0.3s ease;
        }

        .portal-card:hover .portal-card-icon {
            transform: scale(1.08);
        }

        .portal-card-icon.icon-internal {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1), rgba(78, 115, 223, 0.05));
            color: var(--portal-primary);
        }

        .portal-card-icon.icon-external {
            background: linear-gradient(135deg, rgba(28, 200, 138, 0.1), rgba(28, 200, 138, 0.05));
            color: #1cc88a;
        }

        .portal-card-icon img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 8px;
        }

        .portal-card-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: #2d3748;
            margin-bottom: 0.4rem;
        }

        .portal-card-desc {
            color: #718096;
            font-size: 0.82rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .portal-card-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .badge-internal {
            background: rgba(78, 115, 223, 0.1);
            color: var(--portal-primary);
        }

        .badge-external {
            background: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .portal-card-actions {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: auto;
        }

        .portal-card-actions .btn {
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.45rem 1rem;
            transition: all 0.2s ease;
        }

        .btn-open-link {
            background: linear-gradient(135deg, var(--portal-gradient-start), var(--portal-gradient-end));
            border: none;
            color: #fff;
        }

        .btn-open-link:hover {
            color: #fff;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transform: translateY(-1px);
        }

        .btn-qr {
            background: rgba(78, 115, 223, 0.08);
            border: 1px solid rgba(78, 115, 223, 0.15);
            color: var(--portal-primary);
        }

        .btn-qr:hover {
            background: rgba(78, 115, 223, 0.15);
            color: var(--portal-primary-dark);
        }

        /* ===== QR MODAL ===== */
        .qr-modal .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .qr-modal .modal-header {
            background: linear-gradient(135deg, var(--portal-gradient-start), var(--portal-gradient-end));
            border: none;
            padding: 1.2rem 1.5rem;
        }

        .qr-modal .modal-title {
            color: #fff;
            font-weight: 700;
        }

        .qr-modal .close {
            color: #fff;
            opacity: 0.8;
        }

        .qr-modal .close:hover {
            opacity: 1;
        }

        .qr-modal .qr-container {
            padding: 2rem;
            text-align: center;
        }

        .qr-modal .qr-container img {
            max-width: 280px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 12px;
            background: #fff;
            border: 2px solid #f0f2f8;
        }

        .qr-modal .qr-link-info {
            margin-top: 1rem;
            padding: 0.7rem 1rem;
            background: #f8f9fc;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #5a5c69;
            word-break: break-all;
        }

        .qr-modal .btn-download-qr {
            background: linear-gradient(135deg, var(--portal-gradient-start), var(--portal-gradient-end));
            border: none;
            color: #fff;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            margin-top: 1.2rem;
        }

        .qr-modal .btn-download-qr:hover {
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: #fff;
        }

        /* ===== FOOTER ===== */
        .portal-footer {
            background: #2d3748;
            color: rgba(255, 255, 255, 0.6);
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.85rem;
        }

        .portal-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .portal-footer a:hover {
            color: #fff;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* ===== SEARCH BAR ===== */
        .portal-search {
            position: relative;
            max-width: 400px;
            margin: 1.5rem auto 0;
        }

        .portal-search input {
            border-radius: 50px;
            border: 2px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            padding: 0.6rem 1.2rem 0.6rem 2.8rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .portal-search input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .portal-search input:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .portal-search .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .portal-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .portal-card:nth-child(1) { animation-delay: 0.05s; }
        .portal-card:nth-child(2) { animation-delay: 0.1s; }
        .portal-card:nth-child(3) { animation-delay: 0.15s; }
        .portal-card:nth-child(4) { animation-delay: 0.2s; }
        .portal-card:nth-child(5) { animation-delay: 0.25s; }
        .portal-card:nth-child(6) { animation-delay: 0.3s; }
        .portal-card:nth-child(7) { animation-delay: 0.35s; }
        .portal-card:nth-child(8) { animation-delay: 0.4s; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .portal-hero h1 {
                font-size: 1.6rem;
            }

            .portal-hero {
                padding: 2rem 0 3rem;
            }

            .portal-card {
                padding: 1.3rem;
            }

            .portal-card-icon {
                width: 65px;
                height: 65px;
                font-size: 1.6rem;
            }

            .portal-tabs .nav-link {
                padding: 0.4rem 1rem;
                font-size: 0.78rem;
            }
        }
    </style>
</head>

<body>

    {{-- ===== HERO HEADER ===== --}}
    <section class="portal-hero">
        <div class="container text-center position-relative" style="z-index: 5;">
            <div class="hero-icon">
                <i class="fas fa-hospital"></i>
            </div>
            <h1>Portal SIMIT</h1>
            <p>RSI Jombang — Kumpulan Link Aplikasi & Layanan</p>

            {{-- Search Bar --}}
            <div class="portal-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari link...">
            </div>
        </div>
    </section>

    {{-- ===== FILTER TABS ===== --}}
    <div class="container">
        <div class="portal-tabs text-center">
            <ul class="nav nav-pills" id="portalTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-filter="all" href="javascript:void(0)">
                        <i class="fas fa-th-large mr-1"></i> Semua
                        <span class="badge badge-light badge-count">{{ $links->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="internal" href="javascript:void(0)">
                        <i class="fas fa-building mr-1"></i> Internal
                        <span class="badge badge-light badge-count">{{ $internalLinks->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-filter="external" href="javascript:void(0)">
                        <i class="fas fa-external-link-alt mr-1"></i> External
                        <span class="badge badge-light badge-count">{{ $externalLinks->count() }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- ===== PORTAL GRID ===== --}}
    <section class="portal-grid">
        <div class="container">
            @if ($links->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-link"></i>
                    <h5>Belum Ada Link</h5>
                    <p>Portal link belum tersedia saat ini.</p>
                </div>
            @else
                <div class="row" id="portalContainer">
                    @foreach ($links as $link)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 portal-item" data-category="{{ $link->category }}"
                            data-title="{{ strtolower($link->title) }}">
                            <div class="portal-card">
                                {{-- Badge --}}
                                <div>
                                    <span class="portal-card-badge badge-{{ $link->category }}">
                                        {{ $link->category_label }}
                                    </span>
                                </div>

                                {{-- Icon / Image --}}
                                <div class="portal-card-icon icon-{{ $link->category }}">
                                    @if ($link->image)
                                        <img src="{{ $link->image_url }}" alt="{{ $link->title }}">
                                    @else
                                        <i class="{{ $link->display_icon }}"></i>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h5 class="portal-card-title">{{ $link->title }}</h5>

                                {{-- Description --}}
                                @if ($link->description)
                                    <p class="portal-card-desc">{{ $link->description }}</p>
                                @else
                                    <p class="portal-card-desc">&nbsp;</p>
                                @endif

                                {{-- Actions --}}
                                <div class="portal-card-actions">
                                    <a href="{{ $link->url }}" target="_blank" class="btn btn-open-link">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka
                                    </a>
                                    <button class="btn btn-qr" data-toggle="modal" data-target="#qrModal"
                                        data-title="{{ $link->title }}" data-url="{{ $link->url }}"
                                        data-qr-url="{{ route('portal.qr', $link->id) }}">
                                        <i class="fas fa-qrcode mr-1"></i> QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===== QR CODE MODAL ===== --}}
    <div class="modal fade qr-modal" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode mr-2"></i> QR Code — <span id="qrTitle"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="qr-container">
                        <div id="qrLoading" class="text-center py-4">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2 text-muted">Generating QR Code...</p>
                        </div>
                        <img id="qrImage" src="" alt="QR Code" style="display:none;">

                        <div class="qr-link-info" id="qrLinkInfo">
                            <i class="fas fa-link mr-1"></i> <span id="qrUrl"></span>
                        </div>

                        <a href="" id="btnDownloadQr" class="btn btn-download-qr" download>
                            <i class="fas fa-download mr-1"></i> Download QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FOOTER ===== --}}
    <footer class="portal-footer">
        <div class="container">
            <p class="mb-1">
                <i class="fas fa-hospital mr-1"></i> <strong>SIMIT</strong> — Sistem Manajemen Informasi Terpadu
            </p>
            <p class="mb-0">
                &copy; {{ date('Y') }} RSI Jombang. IT & PDE
                @auth
                    <span class="mx-2">|</span>
                    <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt mr-1"></i>Dashboard</a>
                @endauth
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // ===== QR Modal =====
            $('#qrModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var title = button.data('title');
                var url = button.data('url');
                var qrUrl = button.data('qr-url');

                $('#qrTitle').text(title);
                $('#qrUrl').text(url);
                $('#qrLoading').show();
                $('#qrImage').hide();

                // Load QR image
                var img = new Image();
                img.onload = function() {
                    $('#qrImage').attr('src', qrUrl).show();
                    $('#qrLoading').hide();
                };
                img.onerror = function() {
                    $('#qrLoading').html('<i class="fas fa-exclamation-triangle fa-2x text-warning"></i><p class="mt-2 text-muted">Gagal memuat QR Code</p>');
                };
                img.src = qrUrl;

                $('#btnDownloadQr').attr('href', qrUrl);
            });

            // ===== Tab Filter =====
            $('.portal-tabs .nav-link').on('click', function() {
                $('.portal-tabs .nav-link').removeClass('active');
                $(this).addClass('active');

                var filter = $(this).data('filter');
                filterCards(filter, $('#searchInput').val().toLowerCase());
            });

            // ===== Search =====
            $('#searchInput').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                var activeFilter = $('.portal-tabs .nav-link.active').data('filter');
                filterCards(activeFilter, searchTerm);
            });

            function filterCards(category, search) {
                $('.portal-item').each(function() {
                    var itemCategory = $(this).data('category');
                    var itemTitle = $(this).data('title');

                    var categoryMatch = (category === 'all') || (itemCategory === category);
                    var searchMatch = !search || itemTitle.indexOf(search) !== -1;

                    if (categoryMatch && searchMatch) {
                        $(this).fadeIn(300);
                    } else {
                        $(this).fadeOut(200);
                    }
                });
            }
        });
    </script>

</body>

</html>
