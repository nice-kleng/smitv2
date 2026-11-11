<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Portal tautan aplikasi dan layanan RSI Jombang">
    <meta name="author" content="IT RSI Jombang">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal Aplikasi | RSI Jombang</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --bg: #eef5f1;
            --bg-2: #f7faf8;
            --surface: #ffffff;
            --surface-alt: #f1f6f3;
            --border: #d5e2da;
            --text: #132018;
            --muted: #4f6358;
            --accent: #0f766e;
            --accent-strong: #115e59;
            --accent-warm: #b45309;
            --accent-soft: #dff3ee;
            --accent-soft-2: #fff3e6;
            --focus: #0ea5a4;
            --danger: #b42318;
            --radius: 14px;
            --shadow: 0 10px 30px rgba(19, 32, 24, 0.06);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 420px at 10% -10%, rgba(15, 118, 110, 0.12), transparent 60%),
                radial-gradient(900px 360px at 100% 0%, rgba(180, 83, 9, 0.08), transparent 55%),
                linear-gradient(180deg, var(--bg-2) 0%, var(--bg) 100%);
            line-height: 1.5;
        }

        a, button, .btn, [role="button"], summary, label, select { cursor: pointer; }
        button:disabled, .btn:disabled, [aria-disabled="true"] { cursor: not-allowed; }
        :focus-visible { outline: 3px solid var(--focus); outline-offset: 2px; }

        .page-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(145deg, var(--accent), #0b5f58);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.02em;
            flex: 0 0 48px;
            box-shadow: 0 6px 16px rgba(15, 118, 110, 0.25);
        }

        .brand h1 {
            margin: 0;
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--text);
        }

        .brand p {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 0.96rem;
        }

        .meta-links {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .meta-links a {
            color: var(--accent-strong);
            font-weight: 600;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.7);
            border: 1px solid var(--border);
        }
        .meta-links a:hover { background: #fff; border-color: #b9cec3; }

        .control-panel {
            background: rgba(255,255,255,0.86);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            margin-bottom: 22px;
            box-shadow: var(--shadow);
            backdrop-filter: saturate(120%);
        }

        .controls-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
        }

        .portal-tabs .nav-pills {
            display: inline-flex;
            gap: 6px;
            margin: 0;
            padding: 4px;
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .portal-tabs .nav-link {
            border: 0;
            border-radius: 9px;
            padding: 9px 14px;
            color: var(--muted);
            font-weight: 600;
            font-size: 0.9rem;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s ease, background-color 0.15s ease, transform 0.15s ease;
        }

        .portal-tabs .nav-link.active {
            background: linear-gradient(145deg, var(--accent), #0b6b63);
            color: #fff;
            box-shadow: 0 4px 12px rgba(15,118,110,0.22);
        }

        .portal-tabs .nav-link:hover:not(.active) { color: var(--text); background: #fff; }
        .portal-tabs .nav-link:active { transform: translateY(1px); }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-strong);
            font-size: 0.78rem;
            font-weight: 700;
        }

        .portal-tabs .nav-link.active .badge-count {
            background: rgba(255,255,255,0.22);
            color: #fff;
        }

        .portal-search {
            position: relative;
            flex: 1 1 260px;
            max-width: 440px;
        }

        .portal-search .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #5f7569;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .portal-search input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 11px;
            padding: 11px 12px 11px 36px;
            font-size: 0.95rem;
            color: var(--text);
            background: #fff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .portal-search input::placeholder { color: #7b8a83; }
        .portal-search input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.16);
            outline: none;
        }

        .portal-grid { padding: 4px 0 24px; }

        .portal-group { margin-bottom: 30px; }
        .portal-group-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            padding: 6px 10px 6px 8px;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--accent-strong);
            background: var(--accent-soft);
            border: 1px solid #bfe3da;
            border-radius: 999px;
        }
        .portal-group-title::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            flex: 0 0 8px;
        }

        .portal-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }
        .portal-card::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), #14b8a6);
            opacity: 0;
            transition: opacity 0.18s ease;
        }

        .portal-card:hover {
            transform: translateY(-3px);
            border-color: #b9cec3;
            box-shadow: 0 12px 24px rgba(19, 32, 24, 0.08);
        }
        .portal-card:hover::after { opacity: 1; }

        .card-head {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }

        .portal-card-icon {
            width: 46px;
            height: 46px;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex: 0 0 46px;
            border: 1px solid var(--border);
            background: var(--surface-alt);
            color: var(--accent-strong);
        }

        .portal-card-icon.icon-internal { background: var(--accent-soft); color: var(--accent-strong); border-color: #bfe3da; }
        .portal-card-icon.icon-external { background: var(--accent-soft-2); color: #9a3412; border-color: #f2d8bd; }
        .portal-card-icon img { width: 28px; height: 28px; object-fit: contain; }

        .card-head-body { min-width: 0; flex: 1; }
        .portal-card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text);
            line-height: 1.3;
        }

        .portal-card-badge {
            display: inline-block;
            margin-top: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.01em;
        }

        .portal-card-desc {
            color: var(--muted);
            font-size: 0.9rem;
            margin: 0 0 14px;
            flex-grow: 1;
        }

        .portal-card-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .portal-card-actions .btn {
            border-radius: 9px;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 12px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            transition: background-color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }
        .portal-card-actions .btn:active { transform: translateY(1px); }

        .btn-open-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .btn-open-link:hover, .btn-open-link:focus { background: var(--accent-strong); border-color: var(--accent-strong); color: #fff; }
        .btn-qr { color: var(--accent-strong); border-color: #bfded6; background: #f7fcfa; }
        .btn-qr:hover { background: var(--accent-soft); color: var(--accent-strong); }

        .empty-state {
            text-align: center;
            padding: 48px 16px;
            color: var(--muted);
            background: var(--surface);
            border: 1px dashed #b9cec3;
            border-radius: var(--radius);
        }
        .empty-state strong { display: block; color: var(--text); font-size: 1.05rem; margin-bottom: 4px; }

        .portal-footer {
            border-top: 1px solid var(--border);
            padding: 18px 0 8px;
            color: var(--muted);
            font-size: 0.9rem;
            text-align: center;
        }
        .portal-footer a { color: var(--accent-strong); font-weight: 600; text-decoration: none; }
        .portal-footer a:hover { text-decoration: underline; }

        .qr-modal .modal-content, #pinModal .modal-content {
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .qr-modal .modal-header, #pinModal .modal-header {
            background: linear-gradient(145deg, var(--accent), #0b6b63);
            border-bottom: 0;
            padding: 14px 18px;
        }
        .qr-modal .modal-title, #pinModal .modal-title {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
        }
        .qr-modal .close, #pinModal .close { color: #fff; opacity: 0.9; }
        .qr-modal .qr-container { padding: 20px; text-align: center; }
        .qr-modal .qr-container img {
            max-width: 260px;
            width: 100%;
            height: auto;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            background: #fff;
        }
        .qr-modal .qr-link-info {
            margin-top: 12px;
            padding: 10px 12px;
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--muted);
            word-break: break-all;
        }
        .qr-modal .btn-download-qr {
            margin-top: 14px;
            background: var(--accent);
            border: 1px solid var(--accent);
            color: #fff;
            border-radius: 8px;
            padding: 9px 14px;
            font-weight: 600;
        }
        .qr-modal .btn-download-qr:hover { background: var(--accent-strong); color: #fff; }

        #pinModal .modal-body { background: #fff; }
        #pinInput {
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1.05rem;
            letter-spacing: 0.2em;
            padding: 10px;
        }
        #pinInput:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(15,118,110,0.16); }

        @media (max-width: 768px) {
            .page-shell { padding: 18px 14px 32px; }
            .brand h1 { font-size: 1.35rem; }
            .controls-row { flex-direction: column; align-items: stretch; }
            .portal-search { max-width: none; }
            .portal-tabs .nav-pills { width: 100%; }
            .portal-tabs .nav-item { flex: 1; }
            .portal-tabs .nav-link { width: 100%; justify-content: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <header class="topbar">
            <div class="brand">
                <div class="brand-mark" aria-hidden="true">RSI</div>
                <div>
                    <h1>Portal Aplikasi</h1>
                    <p>RSI Jombang, tautan layanan internal dan eksternal</p>
                </div>
            </div>
            <div class="meta-links">
                @auth
                    <a href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt mr-1" aria-hidden="true"></i>Dashboard</a>
                @endauth
            </div>
        </header>

        <section class="control-panel" aria-label="Filter dan pencarian">
            <div class="controls-row">
                <div class="portal-tabs">
                    <ul class="nav nav-pills" id="portalTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" data-filter="internal" href="javascript:void(0)" id="tabInternal" role="tab">
                                Internal
                                <span class="badge-count">{{ $internalLinks->count() }}</span>
                                <i class="fas fa-lock" id="lockIcon" style="font-size:0.75rem;" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" data-filter="external" href="javascript:void(0)" role="tab">
                                External
                                <span class="badge-count">{{ $externalLinks->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="portal-search">
                    <i class="fas fa-search search-icon" aria-hidden="true"></i>
                    <label for="searchInput" class="sr-only">Cari tautan</label>
                    <input type="text" id="searchInput" placeholder="Cari nama aplikasi..." autocomplete="off">
                </div>
            </div>
        </section>

        <main class="portal-grid">
            @if ($links->isEmpty())
                <div class="empty-state">
                    <strong>Belum ada tautan</strong>
                    <p class="mb-0">Daftar aplikasi akan muncul di sini setelah ditambahkan.</p>
                </div>
            @else
                <div id="portalContainer">
                    @foreach (['internal' => $internalGroups, 'external' => $externalGroups] as $cat => $groups)
                        @foreach ($groups as $unitName => $items)
                            <section class="portal-group" data-category="{{ $cat }}" aria-label="Unit {{ $unitName }}">
                                <h2 class="portal-group-title">{{ $unitName }}</h2>
                                <div class="row">
                                    @foreach ($items as $link)
                                        <div class="col-lg-4 col-md-6 mb-3 portal-item"
                                            data-category="{{ $link->category }}"
                                            data-title="{{ strtolower($link->title) }}">
                                            <article class="portal-card">
                                                <div class="card-head">
                                                    <div class="portal-card-icon icon-{{ $link->category }}">
                                                        @if ($link->image)
                                                            <img src="{{ $link->image_url }}" alt="">
                                                        @else
                                                            <i class="{{ $link->display_icon }}" aria-hidden="true"></i>
                                                        @endif
                                                    </div>
                                                    <div class="card-head-body">
                                                        <h3 class="portal-card-title">{{ $link->title }}</h3>
                                                        <span class="portal-card-badge badge-{{ $link->category }}">{{ $link->category_label }}</span>
                                                    </div>
                                                </div>

                                                @if ($link->description)
                                                    <p class="portal-card-desc">{{ $link->description }}</p>
                                                @else
                                                    <p class="portal-card-desc">Tautan aplikasi {{ $link->category_label }}.</p>
                                                @endif

                                                <div class="portal-card-actions">
                                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-open-link">
                                                        Buka tautan
                                                    </a>
                                                    <button type="button" class="btn btn-qr" data-toggle="modal" data-target="#qrModal"
                                                        data-title="{{ $link->title }}" data-url="{{ $link->url }}"
                                                        data-qr-url="{{ route('portal.qr', $link->id) }}">
                                                        Kode QR
                                                    </button>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    @endforeach
                </div>
            @endif
        </main>

        <div class="modal fade qr-modal" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kode QR: <span id="qrTitle"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="qr-container">
                            <div id="qrLoading" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                                <p class="mt-2 text-muted mb-0">Memuat kode QR...</p>
                            </div>
                            <img id="qrImage" src="" alt="Kode QR tautan" style="display:none;">

                            <div class="qr-link-info" id="qrLinkInfo">
                                <span id="qrUrl"></span>
                            </div>

                            <a href="" id="btnDownloadQr" class="btn btn-download-qr" download>
                                Unduh kode QR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="portal-footer">
            <p class="mb-1">SIMIT RSI Jombang</p>
            <p class="mb-0">&copy; {{ date('Y') }} IT dan PDE</p>
        </footer>

        <div class="modal fade" id="pinModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Akses internal</h5>
                        <button type="button" class="close" data-dismiss="modal" id="pinModalClose" aria-label="Tutup">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3" style="font-size:0.9rem;">Masukkan PIN untuk membuka tautan internal.</p>
                        <form id="pinForm">
                            <div class="form-group mb-3">
                                <label for="pinInput">PIN</label>
                                <input type="password" class="form-control" id="pinInput"
                                    placeholder="PIN" maxlength="20" autocomplete="off">
                                <div id="pinError" class="text-danger mt-2" style="font-size:0.85rem; display:none;">
                                    PIN salah. Coba lagi.
                                </div>
                            </div>
                            <button type="submit" class="btn btn-block" style="background:var(--accent);color:#fff;border:1px solid var(--accent);border-radius:8px;padding:10px;font-weight:600;">
                                Buka akses
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var internalUnlocked = false;

            // Check initial access status from server
            $.get("{{ route('portal.check-access') }}", function(res) {
                internalUnlocked = res.unlocked;
                if (internalUnlocked) {
                    $('#lockIcon').removeClass('fa-lock').addClass('fa-lock-open');
                }
            });

            // On page load, show only external links (default)
            filterCards('external', '');

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
                var btn = $(this);
                var filter = btn.data('filter');

                if (filter === 'internal') {
                    // Always check current status with server for Internal tab
                    $.get("{{ route('portal.check-access') }}", function(res) {
                        internalUnlocked = res.unlocked;

                        if (!internalUnlocked) {
                            $('#lockIcon').removeClass('fa-lock-open').addClass('fa-lock');
                            $('#pinInput').val('');
                            $('#pinError').hide();
                            $('#pinModal').modal('show');
                            setTimeout(function() { $('#pinInput').focus(); }, 500);
                        } else {
                            $('#lockIcon').removeClass('fa-lock').addClass('fa-lock-open');
                            switchTab(btn, filter);
                        }
                    });
                    return;
                }

                switchTab(btn, filter);
            });

            function switchTab(btn, filter) {
                $('.portal-tabs .nav-link').removeClass('active');
                btn.addClass('active');
                filterCards(filter, $('#searchInput').val().toLowerCase());
            }

            // ===== PIN Form =====
            $('#pinForm').on('submit', function(e) {
                e.preventDefault();
                var pin = $('#pinInput').val();

                $.ajax({
                    url: "{{ route('portal.verify-pin') }}",
                    method: 'POST',
                    data: {
                        pin: pin,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        internalUnlocked = true;
                        $('#pinModal').modal('hide');
                        $('#lockIcon').removeClass('fa-lock').addClass('fa-lock-open');

                        // Switch to internal tab
                        $('.portal-tabs .nav-link').removeClass('active');
                        $('#tabInternal').addClass('active');
                        filterCards('internal', $('#searchInput').val().toLowerCase());
                    },
                    error: function(xhr) {
                        $('#pinError').show();
                        $('#pinInput').val('').focus();
                        $('#pinInput').css('border-color', '#e74a3b');
                        setTimeout(function() {
                            $('#pinInput').css('border-color', '#e2e8f0');
                        }, 1500);
                    }
                });
            });

            // When PIN modal is dismissed, stay on current tab
            $('#pinModalClose').on('click', function() {
                // Return to external tab visually if we were trying to access internal and it's still locked
                if (!internalUnlocked) {
                    $('.portal-tabs .nav-link').removeClass('active');
                    $('.portal-tabs .nav-link[data-filter="external"]').addClass('active');
                    filterCards('external', $('#searchInput').val().toLowerCase());
                }
                $('#pinModal').modal('hide');
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

                    var categoryMatch = (itemCategory === category);
                    var searchMatch = !search || itemTitle.indexOf(search) !== -1;

                    $(this).toggle(categoryMatch && searchMatch);
                });

                $('.portal-group').each(function() {
                    var groupCategory = $(this).data('category');
                    var hasVisible = $(this).find('.portal-item:visible').length > 0;
                    $(this).toggle(groupCategory === category && hasVisible);
                });
            }
        });
    </script>

</body>

</html>
