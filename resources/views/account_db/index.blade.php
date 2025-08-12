<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hospital Account Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .user-info {
            text-align: right;
            opacity: 0.9;
        }

        .user-info small {
            font-size: 0.8rem;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .header-section {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .hospital-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            border: none;
            padding: 20px;
            position: relative;
        }

        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00b894, #00a085, #55a3ff);
        }

        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #00a085 0%, #00b894 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 184, 148, 0.3);
        }

        .btn-danger-custom {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-danger-custom:hover {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        .btn-warning-custom:hover {
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(243, 156, 18, 0.3);
        }

        .table-custom {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table-custom thead th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.3s ease;
        }

        .table-custom tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
        }

        .table-custom tbody td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }

        .form-control-custom {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: #74b9ff;
            box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
            transform: translateY(-2px);
        }

        .modal-custom .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-custom .modal-header {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            border: none;
            border-radius: 20px 20px 0 0;
        }

        .app-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .stats-card {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            padding-left: 50px;
        }

        .search-box .fa-search {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #74b9ff;
            z-index: 5;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="main-container">
                    <!-- Header Section -->
                    <div class="header-section">
                        <div class="header-content">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="hospital-icon">
                                        <i class="fas fa-hospital"></i>
                                    </div>
                                    <h1 class="mb-2">Hospital Account Manager</h1>
                                    <p class="mb-0 opacity-75">Sistem Manajemen Akun Aplikasi untuk Pegawai Rumah Sakit
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-right">
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div class="user-info mr-3">
                                            <small class="text-white-50">Selamat datang,</small><br>
                                            <strong>{{ auth()->user()->name }}</strong>
                                        </div>
                                        @if (auth()->user()->roles->count() > 1 || auth()->user()->can('view-account-db'))
                                            <a href="{{ route('dashboard') }}"
                                                class="btn btn-outline-light btn-sm btn-logout"
                                                title="Kembali ke Dashboard">
                                                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Dashboard
                                            </a>
                                        @elseif (auth()->user()->roles->count() === 1 && auth()->user()->hasRole('umum'))
                                            <button class="btn btn-outline-light btn-sm btn-logout" onclick="logout()"
                                                title="Logout">
                                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                            </button>
                                        @endif
                                    </div>
                                    <div class="stats-card">
                                        <h3 class="mb-1" id="totalAccounts">0</h3>
                                        <small>Total Akun Tersimpan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="p-4">
                        <!-- Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control form-control-custom" id="searchInput"
                                        placeholder="Cari aplikasi atau username...">
                                </div>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <button class="btn btn-primary-custom btn-custom" data-toggle="modal"
                                    data-target="#addAccountModal">
                                    <i class="fas fa-plus mr-2"></i>Tambah Akun Baru
                                </button>
                            </div>
                        </div>

                        <!-- Accounts Table -->
                        <div class="card card-custom">
                            <div class="card-header card-header-custom">
                                <h5 class="mb-0">
                                    <i class="fas fa-list mr-2"></i>Daftar Akun Aplikasi
                                </h5>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade modal-custom" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus mr-2"></i>Tambah Akun Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
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
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>Edit Akun
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
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
        // Hapus array accounts, ganti dengan AJAX
        let accounts = [];

        function loadAccounts() {
            $.get('/account-db-list', function(data) {
                accounts = data;
                const tbody = document.getElementById('accountsTableBody');
                tbody.innerHTML = '';
                const isSuperadmin = {{ auth()->user()->hasRole('superadmin') ? 'true' : 'false' }};
                accounts.forEach(account => {
                    let pemilikTd = '';
                    if (isSuperadmin) {
                        pemilikTd = `<td>${account.user.name ?? '-'}</td>`;
                    }
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="app-icon">
                                    ${account.app_name.charAt(0).toUpperCase()}
                                </div>
                                <strong>${account.app_name}</strong>
                            </div>
                        </td>
                        <td>
                            ${account.app_url ? `<a href="${account.app_url}" target="_blank" class="text-primary"><i class="fas fa-external-link-alt mr-1"></i>Kunjungi</a>` : '-'}
                        </td>
                        ${pemilikTd}
                        <td><code>${account.penyedia}</code></td>
                        <td><code>${account.email}</code></td>
                        <td><code>${account.username}</code></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <input type="password" value="${account.password}" class="form-control form-control-sm border-0 bg-light" readonly style="max-width: 120px;">
                                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="togglePasswordVisibility(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </td>
                        <td><small class="text-muted">${formatDate(account.created_at)}</small></td>
                        <td>
                            <button class="btn btn-sm btn-warning-custom btn-custom mr-1" onclick="editAccount(${account.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger-custom btn-custom" onclick="deleteAccount(${account.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
                updateTotalAccounts();
            });
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
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#accountsTableBody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
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
