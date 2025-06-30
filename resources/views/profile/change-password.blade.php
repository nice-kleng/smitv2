@extends('layouts.app', ['title' => 'Ubah Password'])

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3">

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
                    </ol>
                </nav>

                <!-- Change Password Form -->
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-key"></i> Ubah Password
                        </h4>
                    </div>

                    <form action="{{ route('profile.change-password') }}" method="POST" id="changePasswordForm">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <!-- Error Messages -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Current Password -->
                            <div class="form-group">
                                <label for="current_password" class="required">
                                    <i class="fas fa-lock text-muted"></i> Password Saat Ini
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password"
                                        placeholder="Masukkan password saat ini" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye" id="current_password_icon"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="form-group">
                                <label for="password" class="required">
                                    <i class="fas fa-key text-muted"></i> Password Baru
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password"
                                        placeholder="Masukkan password baru (minimal 8 karakter)" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="password_icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" id="password_strength" role="progressbar" style="width: 0%">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <span id="password_strength_text">Kekuatan password akan ditampilkan di sini</span>
                                </small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="password_confirmation" class="required">
                                    <i class="fas fa-shield-alt text-muted"></i> Konfirmasi Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Ulangi password baru" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted" id="password_match_text"></small>
                            </div>

                            <!-- Security Warning -->
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Peringatan Keamanan:</h6>
                                <p class="mb-2">Setelah password berhasil diubah, Anda akan <strong>otomatis
                                        logout</strong> dari sistem dan diminta untuk login kembali dengan password baru.
                                </p>
                                <small class="text-dark">
                                    <i class="fas fa-shield-alt"></i> Ini adalah langkah keamanan untuk memastikan hanya
                                    Anda yang dapat mengakses akun dengan password baru.
                                </small>
                            </div>

                            <!-- Security Tips -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-shield-alt"></i> Tips Keamanan Password:</h6>
                                <ul class="mb-0 small">
                                    <li>Gunakan minimal 8 karakter</li>
                                    <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                                    <li>Hindari menggunakan informasi personal</li>
                                    <li>Jangan gunakan password yang sama untuk akun lain</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Card Footer with Buttons -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary mr-2" onclick="resetForm()">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-warning" id="submitBtn" disabled>
                                        <i class="fas fa-save"></i> Ubah Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="confirmModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> Konfirmasi Perubahan Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-shield-alt fa-3x text-warning"></i>
                    </div>
                    <h6 class="text-center mb-3">Peringatan Keamanan</h6>
                    <div class="alert alert-warning">
                        <p class="mb-2"><strong>Setelah password berhasil diubah, Anda akan:</strong></p>
                        <ul class="mb-2">
                            <li>Otomatis <strong>logout</strong> dari sistem</li>
                            <li>Diminta untuk <strong>login kembali</strong> dengan password baru</li>
                            <li>Semua session akan <strong>dihapus</strong> untuk keamanan</li>
                        </ul>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Ini adalah langkah keamanan standar untuk memastikan hanya
                            Anda yang dapat mengakses akun dengan password baru.
                        </small>
                    </div>
                    <p class="text-center mb-0">
                        <strong>Apakah Anda yakin ingin melanjutkan?</strong>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" onclick="submitPasswordForm()">
                        <i class="fas fa-check"></i> Ya, Ubah Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .progress-bar {
            transition: all 0.3s ease;
        }

        .password-weak {
            background-color: #dc3545;
        }

        .password-fair {
            background-color: #ffc107;
        }

        .password-good {
            background-color: #28a745;
        }

        .password-strong {
            background-color: #007bff;
        }
    </style>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function checkPasswordStrength(password) {
            let strength = 0;
            let feedback = '';

            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            const strengthBar = document.getElementById('password_strength');
            const strengthText = document.getElementById('password_strength_text');

            switch (strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.className = 'progress-bar password-weak';
                    feedback = 'Sangat Lemah';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.className = 'progress-bar password-weak';
                    feedback = 'Lemah';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.className = 'progress-bar password-fair';
                    feedback = 'Cukup';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.className = 'progress-bar password-good';
                    feedback = 'Baik';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'progress-bar password-strong';
                    feedback = 'Sangat Kuat';
                    break;
            }

            strengthText.textContent = 'Kekuatan password: ' + feedback;
            return strength;
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const matchText = document.getElementById('password_match_text');
            const submitBtn = document.getElementById('submitBtn');

            if (confirmPassword === '') {
                matchText.textContent = '';
                matchText.className = 'text-muted';
                submitBtn.disabled = true;
                return false;
            }

            if (password === confirmPassword) {
                matchText.textContent = '✓ Password cocok';
                matchText.className = 'text-success';

                // Enable submit button only if password is strong enough
                const strength = checkPasswordStrength(password);
                submitBtn.disabled = strength < 3;
                return true;
            } else {
                matchText.textContent = '✗ Password tidak cocok';
                matchText.className = 'text-danger';
                submitBtn.disabled = true;
                return false;
            }
        }

        function validateForm() {
            const currentPassword = document.getElementById('current_password').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (currentPassword === '' || password === '' || confirmPassword === '') {
                return false;
            }

            if (password.length < 8) {
                return false;
            }

            return checkPasswordMatch();
        }

        function resetForm() {
            document.getElementById('changePasswordForm').reset();
            document.getElementById('password_strength').style.width = '0%';
            document.getElementById('password_strength_text').textContent = 'Kekuatan password akan ditampilkan di sini';
            document.getElementById('password_match_text').textContent = '';
            document.getElementById('submitBtn').disabled = true;
        }

        function submitPasswordForm() {
            // Close modal
            $('#confirmModal').modal('hide');

            // Submit form
            document.getElementById('changePasswordForm').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            const currentPasswordField = document.getElementById('current_password');

            passwordField.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });

            confirmPasswordField.addEventListener('input', checkPasswordMatch);

            currentPasswordField.addEventListener('input', function() {
                const submitBtn = document.getElementById('submitBtn');
                if (this.value === '') {
                    submitBtn.disabled = true;
                } else {
                    checkPasswordMatch();
                }
            });

            // Prevent default form submission and show modal instead
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default submission

                if (!validateForm()) {
                    alert('Mohon lengkapi semua field dengan benar sebelum mengirim form.');
                    return;
                }

                // Show confirmation modal instead of direct submission
                $('#confirmModal').modal('show');
            });

            // Handle submit button click to show modal
            document.getElementById('submitBtn').addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission

                if (!validateForm()) {
                    alert('Mohon lengkapi semua field dengan benar sebelum mengirim form.');
                    return;
                }

                // Show confirmation modal
                $('#confirmModal').modal('show');
            });
        });
    </script>
@endsection
