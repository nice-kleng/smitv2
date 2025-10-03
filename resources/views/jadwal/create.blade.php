@extends('layouts.app')

@section('title', 'Buat Jadwal Baru')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
                        <li class="breadcrumb-item active">Buat Jadwal Baru</li>
                    </ol>
                </nav>

                <form method="POST" action="{{ route('jadwal.store') }}" id="scheduleForm">
                    @csrf
                    <div class="row">
                        <!-- Form Input -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        Informasi Jadwal
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- Start Date -->
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">
                                                Tanggal Mulai <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date"
                                                value="{{ old('start_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                                required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Jadwal akan dimulai dari hari Senin pada minggu yang dipilih
                                            </small>
                                        </div>

                                        <!-- Duration -->
                                        <div class="col-md-6">
                                            <label for="months" class="form-label">
                                                Durasi (Bulan) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="range" class="form-range" id="monthsRange" min="1"
                                                    max="12" value="{{ old('months', 3) }}"
                                                    oninput="updateMonthsValue(this.value)">
                                                <input type="number"
                                                    class="form-control @error('months') is-invalid @enderror"
                                                    id="months" name="months" value="{{ old('months', 3) }}"
                                                    min="1" max="12" style="max-width: 80px;" required>
                                                <span class="input-group-text">Bulan</span>
                                            </div>
                                            @error('months')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small id="durationInfo" class="form-text text-muted">
                                                Perkiraan: 12 minggu (84 hari)
                                            </small>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Staff & Shift Assignment -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            Penugasan Staff & Shift
                                        </h6>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            onclick="addStaffRow()">
                                            <i class="fas fa-plus me-1"></i>
                                            Tambah Staff
                                        </button>
                                    </div>

                                    <div id="staffAssignments">
                                        <!-- Staff assignment rows will be added here -->
                                        <div class="staff-row mb-3 p-3 border rounded" data-index="0">
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="form-label">Staff <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control staff-select"
                                                        name="staff_schedules[0][user_id]" required>
                                                        <option value="">Pilih Staff</option>
                                                        @foreach ($staff as $s)
                                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Shift <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control shift-select"
                                                        name="staff_schedules[0][shift_id]" required>
                                                        <option value="">Pilih Shift</option>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ $shift->id }}"
                                                                data-time="{{ $shift->start_time }} - {{ $shift->end_time }}">
                                                                {{ $shift->name }} ({{ $shift->start_time }} -
                                                                {{ $shift->end_time }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                                        onclick="removeStaffRow(0)" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Informasi:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Shift akan dirotasi setiap minggu antar staff</li>
                                            <li>Setiap staff akan mendapat jadwal harian selama periode yang ditentukan</li>
                                            <li>Status kehadiran dapat dikelola setelah jadwal dibuat</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-info" onclick="previewSchedule()">
                                            <i class="fas fa-eye me-1"></i>
                                            Preview Jadwal
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn"
                                            onclick="submitForm()">
                                            <i class="fas fa-save me-1"></i>
                                            Simpan Jadwal
                                        </button>
                                        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Panel -->
                        <div class="col-lg-4">
                            <div class="card sticky-top">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Ringkasan Jadwal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="scheduleSummary">
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                            <p>Lengkapi form untuk melihat ringkasan jadwal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        Preview Rotasi Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent">
                        <div class="text-center py-4">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat preview jadwal...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAndSubmit()">
                        <i class="fas fa-check me-1"></i>
                        Konfirmasi & Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/jadwal/jadwal.css') }}">
    <style>
        .staff-row {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .staff-row:hover {
            background-color: #e9ecef;
        }

        .sticky-top {
            top: 20px;
        }

        .form-range {
            width: 100%;
        }

        .preview-week {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #ffffff;
        }

        .preview-week h6 {
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .staff-assignment {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let staffRowIndex = 1;

            // Initialize form
            updateSummary();
            updateDurationInfo();

            // Event listeners
            $('#start_date, #months').on('change', function() {
                updateSummary();
                updateDurationInfo();
            });

            $('#monthsRange').on('input', function() {
                $('#months').val(this.value);
                updateSummary();
                updateDurationInfo();
            });

            $('#months').on('input', function() {
                $('#monthsRange').val(this.value);
                updateSummary();
                updateDurationInfo();
            });

            $(document).on('change', '.staff-select, .shift-select', function() {
                updateSummary();
                validateStaffAssignments();
            });

            // Form submission
            $('#scheduleForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return false;
                }

                submitForm();
            });

            // Functions
            window.updateMonthsValue = function(value) {
                $('#months').val(value);
                updateSummary();
                updateDurationInfo();
            };

            window.addStaffRow = function() {
                const newRow = `
            <div class="staff-row mb-3 p-3 border rounded" data-index="${staffRowIndex}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Staff <span class="text-danger">*</span></label>
                        <select class="form-control staff-select"
                                name="staff_schedules[${staffRowIndex}][user_id]"
                                required>
                            <option value="">Pilih Staff</option>
                            @foreach ($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Shift <span class="text-danger">*</span></label>
                        <select class="form-control shift-select"
                                name="staff_schedules[${staffRowIndex}][shift_id]"
                                required>
                            <option value="">Pilih Shift</option>
                            @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}"
                                    data-time="{{ $shift->start_time }} - {{ $shift->end_time }}">
                                {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button"
                                class="btn btn-outline-danger btn-sm w-100"
                                onclick="removeStaffRow(${staffRowIndex})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

                $('#staffAssignments').append(newRow);
                staffRowIndex++;
                updateRemoveButtons();
                updateSummary();
            };

            window.removeStaffRow = function(index) {
                $(`.staff-row[data-index="${index}"]`).remove();
                updateRemoveButtons();
                updateSummary();
                validateStaffAssignments();
            };

            function updateRemoveButtons() {
                const rows = $('.staff-row');
                if (rows.length <= 1) {
                    rows.find('button[onclick*="removeStaffRow"]').prop('disabled', true);
                } else {
                    rows.find('button[onclick*="removeStaffRow"]').prop('disabled', false);
                }
            }

            function updateSummary() {
                const startDate = $('#start_date').val();
                const months = parseInt($('#months').val()) || 0;
                const staffCount = $('.staff-row').length;

                if (!startDate || !months) {
                    $('#scheduleSummary').html(`
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <p>Lengkapi form untuk melihat ringkasan jadwal</p>
                </div>
            `);
                    return;
                }

                // Calculate end date
                const start = new Date(startDate);
                const end = new Date(start);
                end.setMonth(end.getMonth() + months);

                // Calculate weeks
                const diffTime = Math.abs(end - start);
                const diffWeeks = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 7));
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                const summaryHtml = `
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Tanggal Mulai:</span>
                    <strong>${formatDate(start)}</strong>
                </div>
            </div>
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Tanggal Selesai:</span>
                    <strong>${formatDate(end)}</strong>
                </div>
            </div>
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Durasi:</span>
                    <strong>${months} Bulan</strong>
                </div>
            </div>
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Minggu:</span>
                    <strong>${diffWeeks} Minggu</strong>
                </div>
            </div>
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Hari:</span>
                    <strong>${diffDays} Hari</strong>
                </div>
            </div>
            <div class="summary-item mb-3">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Jumlah Staff:</span>
                    <strong>${staffCount} Orang</strong>
                </div>
            </div>
            <hr>
            <div class="summary-item">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Detail Jadwal:</span>
                    <strong class="text-primary">${diffDays * staffCount} Entries</strong>
                </div>
            </div>
        `;

                $('#scheduleSummary').html(summaryHtml);
            }

            function updateDurationInfo() {
                const months = parseInt($('#months').val()) || 0;
                if (months > 0) {
                    const weeks = Math.ceil(months * 4.33);
                    const days = Math.ceil(months * 30.44);
                    $('#durationInfo').text(`Perkiraan: ${weeks} minggu (${days} hari)`);
                }
            }

            function validateForm() {
                // Reset previous errors
                $('.is-invalid').removeClass('is-invalid');

                let isValid = true;

                // Validate start date
                if (!$('#start_date').val()) {
                    $('#start_date').addClass('is-invalid');
                    isValid = false;
                }

                // Validate months
                if (!$('#months').val() || parseInt($('#months').val()) < 1) {
                    $('#months').addClass('is-invalid');
                    isValid = false;
                }

                // Validate staff assignments
                const assignments = validateStaffAssignments();
                if (!assignments.valid) {
                    isValid = false;
                }

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Tidak Valid',
                        text: 'Harap lengkapi semua field yang diperlukan',
                        confirmButtonText: 'OK'
                    });
                }

                return isValid;
            }

            function validateStaffAssignments() {
                let valid = true;
                const selectedStaff = [];
                const errors = [];

                $('.staff-row').each(function() {
                    const staffId = $(this).find('.staff-select').val();
                    const shiftId = $(this).find('.shift-select').val();

                    // Check if staff and shift are selected
                    if (!staffId) {
                        $(this).find('.staff-select').addClass('is-invalid');
                        valid = false;
                    }

                    if (!shiftId) {
                        $(this).find('.shift-select').addClass('is-invalid');
                        valid = false;
                    }

                    // Check for duplicate staff
                    if (staffId && selectedStaff.includes(staffId)) {
                        $(this).find('.staff-select').addClass('is-invalid');
                        errors.push('Staff tidak boleh dipilih lebih dari sekali');
                        valid = false;
                    } else if (staffId) {
                        selectedStaff.push(staffId);
                    }
                });

                return {
                    valid,
                    errors
                };
            }

            window.previewSchedule = function() {
                if (!validateForm()) {
                    return;
                }

                const formData = {
                    staff_schedules: [],
                    start_date: $('#start_date').val(),
                    weeks: 4 // Preview 4 weeks
                };

                $('.staff-row').each(function() {
                    const staffId = $(this).find('.staff-select').val();
                    const shiftId = $(this).find('.shift-select').val();

                    if (staffId && shiftId) {
                        formData.staff_schedules.push({
                            user_id: parseInt(staffId),
                            shift_id: parseInt(shiftId)
                        });
                    }
                });

                $('#previewModal').modal('show');

                $.ajax({
                    url: '{{ route('jadwal.preview') }}',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            renderPreview(response.data);
                        } else {
                            $('#previewContent').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${response.message}
                        </div>
                    `);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        $('#previewContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${response?.message || 'Terjadi kesalahan saat memuat preview'}
                    </div>
                `);
                    }
                });
            };

            function renderPreview(previewData) {
                let html = '';

                previewData.forEach((week, index) => {
                    html += `
                <div class="preview-week">
                    <h6>
                        <i class="fas fa-calendar-week me-2"></i>
                        Minggu ${week.week_number} - ${formatDate(new Date(week.week))} sampai ${formatDate(new Date(week.week_end))}
                    </h6>
                    <div class="row">
            `;

                    week.schedules.forEach(schedule => {
                        html += `
                    <div class="col-md-6 mb-2">
                        <div class="staff-assignment">
                            <div>
                                <strong>${schedule.user_name}</strong>
                                <br>
                                <small class="text-muted">${schedule.shift_name}</small>
                            </div>
                        </div>
                    </div>
                `;
                    });

                    html += '</div></div>';
                });

                $('#previewContent').html(html);
            }

            function submitForm() {
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                const formData = new FormData($('#scheduleForm')[0]);

                $.ajax({
                    url: '{{ route('jadwal.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '{{ route('jadwal.index') }}';
                            });
                        } else {
                            handleFormErrors(response);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (xhr.status === 422) {
                            handleFormErrors(response);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response?.message ||
                                    'Terjadi kesalahan saat menyimpan jadwal',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    complete: function() {
                        $('#submitBtn').prop('disabled', false).html(
                            '<i class="fas fa-save me-1"></i> Simpan Jadwal');
                    }
                });
            }

            window.confirmAndSubmit = function() {
                $('#previewModal').modal('hide');
                submitForm();
            };

            function handleFormErrors(response) {
                if (response.errors) {
                    Object.keys(response.errors).forEach(field => {
                        $(`[name="${field}"]`).addClass('is-invalid');
                    });
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: response.message || 'Harap periksa kembali data yang diinput',
                    confirmButtonText: 'OK'
                });
            }

            function formatDate(date) {
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        });
    </script>
@endpush
