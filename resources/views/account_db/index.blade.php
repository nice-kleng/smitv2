<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIAkun - Manajemen Akun</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f5f7;
            --surface: #ffffff;
            --border: #e3e8ee;
            --text: #1f2937;
            --muted: #6b7280;
            --accent: #0f766e;
            --accent-hover: #115e59;
            --accent-soft: #e6f4f1;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
            --warn: #b45309;
            --warn-hover: #92400e;
            --radius: 8px;
            --focus: 0 0 0 3px rgba(15, 118, 110, 0.25);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
        }

        button, a, .btn, summary, select, label[for], [role="button"] { cursor: pointer; }
        button:disabled, .btn:disabled, [aria-disabled="true"] { cursor: not-allowed; }

        .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }

        .main-container { background: transparent; }

        .page-header {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 24px;
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .page-subtitle {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .user-meta { color: var(--muted); font-size: 0.9rem; }
        .user-meta strong { color: var(--text); }

        .stat {
            display: inline-flex;
            align-items: baseline;
            gap: 8px;
            padding: 8px 12px;
            background: var(--accent-soft);
            color: var(--accent);
            border-radius: 6px;
            font-weight: 600;
        }
        .stat-label { font-size: 0.85rem; font-weight: 500; color: var(--accent-hover); }
        .stat-value { font-size: 1.1rem; }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .search-box {
            position: relative;
            flex: 1 1 280px;
            max-width: 420px;
        }

        .search-box .fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.9rem;
            pointer-events: none;
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 0.95rem;
            color: var(--text);
            background: var(--surface);
        }

        .search-box .form-control-custom {
            padding-left: 36px;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: var(--focus);
        }

        .btn {
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 9px 14px;
            border: 1px solid transparent;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }

        .btn:focus { outline: none; box-shadow: var(--focus); }

        .btn-primary-custom, .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .btn-primary-custom:hover, .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #fff;
        }

        .btn-outline-secondary {
            color: var(--text);
            border-color: var(--border);
            background: #fff;
        }
        .btn-outline-secondary:hover { background: #f8fafc; color: var(--text); }

        .btn-danger-custom {
            background: #fff;
            border-color: #fecaca;
            color: var(--danger);
        }
        .btn-danger-custom:hover { background: #fef2f2; color: var(--danger-hover); border-color: #fca5a5; }

        .btn-warning-custom {
            background: #fff;
            border-color: #fde68a;
            color: var(--warn);
        }
        .btn-warning-custom:hover { background: #fffbeb; color: var(--warn-hover); border-color: #fcd34d; }

        .btn-logout {
            background: #fff;
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 600;
        }
        .btn-logout:hover { background: #f8fafc; color: var(--text); }

        .card-custom {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            background: var(--surface);
        }
        .card-header-custom h5 { margin: 0; font-size: 1rem; font-weight: 600; color: var(--text); }

        .table-custom { margin: 0; color: var(--text); font-size: 0.92rem; }
        .table-custom thead th {
            background: #f8fafc;
            color: var(--muted);
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--border);
            border-top: 0;
            padding: 12px 14px;
            white-space: nowrap;
        }
        .table-custom tbody td {
            padding: 12px 14px;
            border-top: 1px solid var(--border);
            vertical-align: middle;
        }
        .table-custom tbody tr:hover { background: #f9fafb; }

        .app-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: var(--accent-soft);
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            margin-right: 10px;
            flex: 0 0 32px;
        }

        .app-cell { display: flex; align-items: center; }
        .text-link { color: var(--accent); }
        .text-link:hover { color: var(--accent-hover); text-decoration: underline; }

        .modal-content { border: 1px solid var(--border); border-radius: 10px; }
        .modal-header { border-bottom: 1px solid var(--border); background: #fff; }
        .modal-footer { border-top: 1px solid var(--border); }
        .modal-title { font-weight: 600; font-size: 1.05rem; }
        .modal-body .form-group label { font-weight: 600; font-size: 0.9rem; color: var(--text); margin-bottom: 6px; }
        .modal-body .form-control { border: 1px solid var(--border); border-radius: 6px; padding: 10px 12px; }
        .modal-body .form-control:focus { border-color: var(--accent); box-shadow: var(--focus); }
        .modal-body .input-group .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .modal-body .input-group-append .btn { border-top-left-radius: 0; border-bottom-left-radius: 0; }

        .btn-sm { padding: 6px 10px; font-size: 0.85rem; }

        .empty-state { text-align: center; color: var(--muted); padding: 32px 16px; }

        .pagination .page-link { color: var(--accent); border-color: var(--border); }
        .pagination .page-item.active .page-link { background: var(--accent); border-color: var(--accent); color: #fff; }
        .pagination .page-link:focus { box-shadow: var(--focus); }
        .pagination .page-item.disabled .page-link { color: var(--muted); }

        @media (max-width: 768px) {
            .page { padding: 16px 12px 32px; }
            .page-header { padding: 16px; }
            .header-actions { margin-top: 12px; }
            .toolbar { flex-direction: column; align-items: stretch; }
            .search-box { max-width: none; }
            .toolbar .btn { width: 100%; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="main-container">
            <header class="page-header">
                <div class="d-flex flex-wrap justify-content-between align-items-start">
                    <div>
                        <h1 class="page-title">SIAkun</h1>
                        <p class="page-subtitle">Sistem Informasi Manajemen Akun</p>
                        <div class="mt-3 stat" aria-label="Total akun tersimpan">
                            <span class="stat-value" id="totalAccounts">0</span>
                            <span class="stat-label">akun tersimpan</span>
                        </div>
                    </div>
                    <div class="header-actions text-md-right mt-3 mt-md-0">
                        <div class="user-meta mb-2">Masuk sebagai <strong>{{ auth()->user()->name }}</strong></div>
                        @if (auth()->user()->roles->count() > 1 || auth()->user()->can('view-account-db'))
                            <a href="{{ route('dashboard') }}" class="btn btn-logout btn-logout-header" title="Kembali ke Dashboard">
                                <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>Kembali ke Dashboard
                            </a>
                        @elseif (auth()->user()->roles->count() === 1 && auth()->user()->hasRole('umum'))
                            <button type="button" class="btn btn-logout btn-logout-header" onclick="logout()" title="Logout">
                                <i class="fas fa-sign-out-alt mr-1" aria-hidden="true"></i>Logout
                            </button>
                        @endif
                    </div>
                </div>
            </header>

            <main class="p-0">
                <div class="toolbar">
                    <div class="search-box">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <label for="searchInput" class="sr-only">Cari akun</label>
                        <input type="text" class="form-control form-control-custom" id="searchInput"
                            placeholder="Cari aplikasi, username, atau penyedia...">
                    </div>
                    <button type="button" class="btn btn-primary-custom" data-toggle="modal" data-target="#addAccountModal">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i>Tambah Akun
                    </button>
                </div>

                <div class="card card-custom">
                    <div class="card-header card-header-custom">
                        <h5>Daftar Akun Aplikasi</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th>Aplikasi</th>
                                        <th>URL</th>
                                        @if (auth()->user()->hasRole('superadmin'))
                                            <th>Penanggung Jawab</th>
                                        @endif
                                        <th>Penyedia</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Password</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="accountsTableBody">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center p-3 border-top" id="paginationWrapper">
                            <small class="text-muted mb-2 mb-sm-0" id="paginationInfo"></small>
                            <nav aria-label="Navigasi halaman akun">
                                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade modal-custom" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAccountForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appName">Nama Aplikasi *</label>
                                    <input type="text" class="form-control form-control-custom" id="appName"
                                        placeholder="Contoh: SIMRS, e-Klaim, dll" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appUrl">URL Aplikasi</label>
                                    <input type="url" class="form-control form-control-custom" id="appUrl"
                                        placeholder="https://example.com">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="penyedia">Penyedia *</label>
                                    <input type="text" class="form-control form-control-custom" id="penyedia"
                                        placeholder="Nama penyedia aplikasi" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-custom" id="email"
                                        placeholder="Email kontak">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username *</label>
                                    <input type="text" class="form-control form-control-custom" id="username"
                                        placeholder="Username login" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-custom"
                                            id="password" placeholder="Password login" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="togglePassword()">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary-custom btn-custom" onclick="saveAccount()">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade modal-custom" id="editAccountModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Akun</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm">
                        <input type="hidden" id="editAccountId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editAppName">Nama Aplikasi *</label>
                                    <input type="text" class="form-control form-control-custom" id="editAppName"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editAppUrl">URL Aplikasi</label>
                                    <input type="url" class="form-control form-control-custom" id="editAppUrl">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editPenyedia">Penyedia *</label>
                                    <input type="text" class="form-control form-control-custom" id="editPenyedia"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editEmail">Email</label>
                                    <input type="email" class="form-control form-control-custom" id="editEmail">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editUsername">Username *</label>
                                    <input type="text" class="form-control form-control-custom" id="editUsername"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editPassword">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-custom"
                                            id="editPassword" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="toggleEditPassword()">
                                                <i class="fas fa-eye" id="toggleEditIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning-custom btn-custom" onclick="updateAccount()">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script>
        let accounts = [];
        let filteredAccounts = [];
        let currentPage = 1;
        const perPage = 10;

        function loadAccounts() {
            $.get('/account-db-list', function(data) {
                accounts = data || [];
                applyFilter();
            });
        }

        function getSearchTerm() {
            return (document.getElementById('searchInput').value || '').toLowerCase().trim();
        }

        function applyFilter(resetPage = true) {
            const term = getSearchTerm();
            if (!term) {
                filteredAccounts = accounts.slice();
            } else {
                filteredAccounts = accounts.filter(account => {
                    const owner = (account.user && account.user.name) ? account.user.name : '';
                    const haystack = [
                        account.app_name, account.app_url, account.penyedia,
                        account.email, account.username, owner
                    ].join(' ').toLowerCase();
                    return haystack.includes(term);
                });
            }
            if (resetPage) currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const tbody = document.getElementById('accountsTableBody');
            tbody.innerHTML = '';
            const isSuperadmin = {{ auth()->user()->hasRole('superadmin') ? 'true' : 'false' }};
            const colCount = isSuperadmin ? 9 : 8;
            updateTotalAccounts();

            if (!filteredAccounts.length) {
                const msg = accounts.length ? 'Tidak ada akun yang cocok dengan pencarian.' : 'Belum ada akun tersimpan. Tambah akun pertama untuk mulai.';
                tbody.innerHTML = `<tr><td colspan="${colCount}" class="empty-state">${msg}</td></tr>`;
                renderPagination(0);
                return;
            }

            const totalPages = Math.max(1, Math.ceil(filteredAccounts.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            const pageItems = filteredAccounts.slice(start, start + perPage);

            pageItems.forEach(account => {
                let pemilikTd = '';
                if (isSuperadmin) {
                    pemilikTd = `<td>${(account.user && account.user.name) ? account.user.name : '-'}</td>`;
                }
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="app-cell">
                            <div class="app-icon">${(account.app_name || '?').charAt(0).toUpperCase()}</div>
                            <strong>${account.app_name || '-'}</strong>
                        </div>
                    </td>
                    <td>
                        ${account.app_url ? `<a href="${account.app_url}" target="_blank" rel="noopener" class="text-link"><i class="fas fa-external-link-alt mr-1" aria-hidden="true"></i>Buka</a>` : '-'}
                    </td>
                    ${pemilikTd}
                    <td>${account.penyedia || '-'}</td>
                    <td>${account.email || '-'}</td>
                    <td><code>${account.username || '-'}</code></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <input type="password" value="${account.password || ''}" class="form-control form-control-sm border-0 bg-light" readonly style="max-width: 120px;" aria-label="Password">
                            <button class="btn btn-sm btn-outline-secondary ml-2" type="button" onclick="togglePasswordVisibility(this)" aria-label="Tampilkan password">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                    <td><small class="text-muted">${formatDate(account.created_at)}</small></td>
                    <td>
                        <button class="btn btn-sm btn-warning-custom mr-1" type="button" onclick="editAccount(${account.id})" aria-label="Edit akun">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                        </button>
                        <button class="btn btn-sm btn-danger-custom" type="button" onclick="deleteAccount(${account.id})" aria-label="Hapus akun">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            renderPagination(filteredAccounts.length);
        }

        function renderPagination(totalItems) {
            const pagination = document.getElementById('pagination');
            const info = document.getElementById('paginationInfo');
            const wrapper = document.getElementById('paginationWrapper');
            pagination.innerHTML = '';

            if (!totalItems) {
                info.textContent = '';
                wrapper.style.display = 'none';
                return;
            }

            wrapper.style.display = '';
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage + 1;
            const end = Math.min(currentPage * perPage, totalItems);
            info.textContent = `Menampilkan ${start}-${end} dari ${totalItems} akun`;

            if (totalPages <= 1) return;

            const addItem = (label, page, disabled = false, active = false, aria = '') => {
                const li = document.createElement('li');
                li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                if (aria) a.setAttribute('aria-label', aria);
                a.textContent = label;
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (disabled || active) return;
                    currentPage = page;
                    renderTable();
                });
                li.appendChild(a);
                pagination.appendChild(li);
            };

            addItem('‹', currentPage - 1, currentPage === 1, false, 'Sebelumnya');

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);
            if (currentPage <= 3) endPage = Math.min(5, totalPages);
            if (currentPage >= totalPages - 2) startPage = Math.max(1, totalPages - 4);

            if (startPage > 1) {
                addItem('1', 1, false, currentPage === 1);
                if (startPage > 2) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">…</span>';
                    pagination.appendChild(li);
                }
            }
            for (let p = startPage; p <= endPage; p++) {
                addItem(String(p), p, false, p === currentPage);
            }
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">…</span>';
                    pagination.appendChild(li);
                }
                addItem(String(totalPages), totalPages, false, currentPage === totalPages);
            }

            addItem('›', currentPage + 1, currentPage === totalPages, false, 'Berikutnya');
        }

        function updateTotalAccounts() {
            document.getElementById('totalAccounts').textContent = accounts.length;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function toggleEditPassword() {
            const passwordInput = document.getElementById('editPassword');
            const toggleIcon = document.getElementById('toggleEditIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function togglePasswordVisibility(button) {
            const input = button.parentElement.querySelector('input');
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function saveAccount() {
            const appName = document.getElementById('appName').value;
            const penyedia = document.getElementById('penyedia').value;
            const email = document.getElementById('email').value;
            const appUrl = document.getElementById('appUrl').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (!appName || !username || !password || !penyedia) {
                showAlert('Mohon isi semua field yang wajib diisi (*)', 'danger');
                return;
            }

            $.ajax({
                url: '/account-db',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    app_name: appName,
                    app_url: appUrl,
                    username: username,
                    password: password,
                    penyedia: penyedia,
                    email: email
                },
                success: function(response) {
                    $('#addAccountModal').modal('hide');
                    showAlert('Akun berhasil ditambahkan', 'success');
                    loadAccounts();
                    document.getElementById('addAccountForm').reset();
                },
                error: function(xhr) {
                    showAlert('Gagal menambahkan akun: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                        'danger');
                }
            });
            //     url: '/account-db',
            //         method: 'POST',
            //         data: {
            //             app_name: appName,
            //             app_url: appUrl,
            //             username: username,
            //             password: password,
            //             penyedia: penyedia,
            //             email: email,
            //             _token: $('meta[name="csrf-token"]').attr('content')
            //         },
            //         success: function() {
            //             $('#addAccountModal').modal('hide');
            //             setTimeout(function() {
            //                 showAlert('Akun berhasil ditambahkan!', 'success');
            //                 window.scrollTo({
            //                     top: 0,
            //                     behavior: 'smooth'
            //                 });
            //             }, 400);
            //             loadAccounts();
            //             document.getElementById('addAccountForm').reset();
            //         },
            //         error: function(xhr) {
            //             alert('Gagal menambah akun: ' + xhr.responseText);
            //         }
            // });
        }

        function editAccount(id) {
            const account = accounts.find(a => a.id === id);
            if (!account) return;
            document.getElementById('editAccountId').value = account.id;
            document.getElementById('editAppName').value = account.app_name;
            document.getElementById('editAppUrl').value = account.app_url;
            document.getElementById('editUsername').value = account.username;
            document.getElementById('editPassword').value = account.password;
            document.getElementById('editPenyedia').value = account.penyedia;
            document.getElementById('editEmail').value = account.email;
            $('#editAccountModal').modal('show');
        }

        function updateAccount() {
            const id = parseInt(document.getElementById('editAccountId').value);
            const appName = document.getElementById('editAppName').value;
            const appUrl = document.getElementById('editAppUrl').value;
            const username = document.getElementById('editUsername').value;
            const password = document.getElementById('editPassword').value;
            const penyedia = document.getElementById('editPenyedia').value;
            const email = document.getElementById('editEmail').value;

            if (!appName || !username || !password || !penyedia) {
                showAlert('Mohon isi semua field yang wajib diisi (*)', 'danger');
                return;
            }

            $.ajax({
                url: `/account-db/${id}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    app_name: appName,
                    app_url: appUrl,
                    username: username,
                    password: password,
                    penyedia: penyedia,
                    email: email
                },
                success: function(response) {
                    $('#editAccountModal').modal('hide');
                    showAlert('Akun berhasil diperbarui', 'success');
                    loadAccounts();
                },
                error: function(xhr) {
                    showAlert('Gagal memperbarui akun: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'),
                        'danger');
                }
            });
        }

        function deleteAccount(id) {
            if (confirm('Apakah Anda yakin ingin menghapus akun ini?')) {
                $.ajax({
                    url: '/account-db/' + id,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        setTimeout(function() {
                            showAlert('Akun berhasil dihapus!', 'danger');
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }, 200);
                        loadAccounts();
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            showAlert(xhr.responseJSON.message || 'Anda tidak berhak menghapus data ini.',
                                'danger');
                        } else {
                            alert('Gagal menghapus akun: ' + xhr.responseText);
                        }
                    }
                });
            }
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;

            document.querySelector('.main-container').insertBefore(alertDiv, document.querySelector('.main-container')
                .firstChild);

            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            applyFilter(true);
        });

        // Load accounts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAccounts();
        });

        // Logout function
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                $.post({
                    url: '/logout',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        window.location.href = '/login';
                    },
                    error: function(xhr) {
                        showAlert('Logout gagal!', 'danger');
                    }
                });
            }
        }
    </script>
</body>

</html>
