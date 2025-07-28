@extends('layouts.app', ['title' => 'Buat Jadwal Kerja'])

@section('button-header')
    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Buat Jadwal</h3>
                    </div>

                    <form id="schedule-form">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            required>
                                        <small class="form-text text-muted">Jadwal akan dimulai dari hari Senin pada minggu
                                            yang dipilih</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="months">Durasi (Bulan)</label>
                                        <select class="form-control" id="months" name="months" required>
                                            <option value="">Pilih Durasi</option>
                                            <option value="1">1 Bulan</option>
                                            <option value="2">2 Bulan</option>
                                            <option value="3" selected>3 Bulan</option>
                                            <option value="6">6 Bulan</option>
                                            <option value="12">12 Bulan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Konfigurasi Staff dan Shift Minggu Pertama</label>
                                <div class="card">
                                    <div class="card-body">
                                        <div id="staff-shift-container">
                                            <!-- Dynamic staff-shift assignments will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm" id="add-staff-shift">
                                            <i class="fas fa-plus"></i> Tambah Staff
                                        </button>
                                    </div>
                                </div>
                                <div id="schedule-error" class="text-danger mt-2" style="display: none;"></div>
                            </div>

                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Informasi Penjadwalan:</h5>
                                <ul class="mb-0">
                                    <li>Pilih staff dan tentukan shift untuk minggu pertama</li>
                                    <li>Sistem akan otomatis melakukan rotasi shift untuk minggu-minggu berikutnya</li>
                                    <li>Setiap staff akan bergantian shift secara berurutan</li>
                                    <li>Tidak boleh ada staff yang sama atau shift yang sama di minggu yang sama</li>
                                    <li>Jadwal akan dibuat untuk hari kerja sesuai konfigurasi masing-masing shift</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Buat Jadwal
                            </button>
                            <button type="reset" class="btn btn-secondary ml-2">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview Jadwal</h3>
                    </div>
                    <div class="card-body">
                        <div id="schedule-preview">
                            <p class="text-muted">Tambahkan staff dan shift untuk melihat preview jadwal</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Shift</h3>
                    </div>
                    <div class="card-body">
                        @if (isset($shifts) && $shifts->count() > 0)
                            @foreach ($shifts as $shift)
                                <div class="mb-3">
                                    <h5>{{ $shift->name }}</h5>
                                    <p class="mb-1">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                                    </p>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-calendar"></i>
                                        {{ implode(', ',array_map(function ($day) {return ucfirst($day);}, json_decode($shift->work_days))) }}
                                    </p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Tidak ada data shift</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h4 class="modal-title">
                        <i class="fas fa-check-circle"></i> Jadwal Berhasil Dibuat
                    </h4>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="success-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('jadwal.index') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> Lihat Semua Jadwal
                    </a>
                    <button type="button" class="btn btn-success" onclick="createAnother()">
                        <i class="fas fa-plus"></i> Buat Jadwal Lain
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Shift Row Template -->
    <template id="staff-shift-template">
        <div class="staff-shift-row mb-3 border rounded p-3" data-index="">
            <div class="row">
                <div class="col-md-5">
                    <label>Pilih Staff</label>
                    <select class="form-control staff-select" name="staff_schedules[INDEX_PLACEHOLDER][user_id]" required>
                        <option value="">-- Pilih Staff --</option>
                        @foreach ($staff as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label>Pilih Shift</label>
                    <select class="form-control shift-select" name="staff_schedules[INDEX_PLACEHOLDER][shift_id]"
                        required>
                        <option value="">-- Pilih Shift --</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }}
                                ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-staff-shift">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('styles')
    <style>
        .staff-shift-row {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .staff-shift-row:hover {
            background-color: #e9ecef;
        }

        .preview-week {
            border-left: 3px solid #007bff;
            padding-left: 10px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 0 5px 5px 0;
            padding: 10px;
        }

        .staff-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 5px 10px;
            background-color: white;
            border-radius: 3px;
            border-left: 2px solid #007bff;
        }

        .shift-badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }

        .shift-color-pagi {
            background-color: #ffc107;
            color: #212529;
        }

        .shift-color-middle {
            background-color: #17a2b8;
            color: white;
        }

        .shift-color-malam {
            background-color: #6f42c1;
            color: white;
        }

        #add-staff-shift {
            border: 2px dashed #28a745;
            background-color: transparent;
            color: #28a745;
        }

        #add-staff-shift:hover {
            background-color: #28a745;
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let staffShiftIndex = 0;

            // Set minimum date to today
            $('#start_date').attr('min', new Date().toISOString().split('T')[0]);

            // Add first staff-shift row on load
            addStaffShiftRow();

            // Add staff-shift row
            $('#add-staff-shift').click(function() {
                addStaffShiftRow();
            });

            // Remove staff-shift row
            $(document).on('click', '.remove-staff-shift', function() {
                const container = $('#staff-shift-container');
                if (container.children('.staff-shift-row').length > 1) {
                    $(this).closest('.staff-shift-row').remove();
                    updatePreview();
                    validateSchedule();
                } else {
                    toastr.warning('Minimal harus ada satu staff yang dijadwalkan');
                }
            });

            // Update preview when form changes
            $(document).on('change', '.staff-select, .shift-select', function() {
                updatePreview();
                validateSchedule();
            });

            $('#start_date, #months').change(function() {
                updatePreview();
            });

            // Form submission with improved error handling
            $('#schedule-form').submit(function(e) {
                e.preventDefault();

                const validation = validateSchedule();
                if (!validation.isValid) {
                    $('#schedule-error').show().text(validation.message);
                    toastr.error(validation.message);
                    return;
                }

                const months = parseInt($('#months').val());

                // Prepare form data with proper structure
                const formData = new FormData();
                const staffSchedules = [];

                $('.staff-shift-row').each(function(index) {
                    const staffId = $(this).find('.staff-select').val();
                    const shiftId = $(this).find('.shift-select').val();

                    if (staffId && shiftId) {
                        staffSchedules.push({
                            user_id: parseInt(staffId),
                            shift_id: parseInt(shiftId)
                        });
                    }
                });

                // Add data to FormData
                formData.append('_token', $('input[name="_token"]').val());
                formData.append('start_date', $('#start_date').val());
                formData.append('months', months);

                // Add staff schedules as proper array
                staffSchedules.forEach((schedule, index) => {
                    formData.append(`staff_schedules[${index}][user_id]`, schedule.user_id);
                    formData.append(`staff_schedules[${index}][shift_id]`, schedule.shift_id);
                });

                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...');

                $.ajax({
                    url: '{{ route('jadwal.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessModal(response.data, response.message, response.summary);
                            $('#schedule-error').hide();
                        } else {
                            toastr.error(response.message ||
                                'Terjadi kesalahan saat menyimpan jadwal');
                            $('#schedule-error').show().text(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Ajax error:', xhr);
                        let errorMessage = 'Terjadi kesalahan saat menyimpan jadwal';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = [];

                                Object.keys(errors).forEach(key => {
                                    if (Array.isArray(errors[key])) {
                                        errorMessages.push(...errors[key]);
                                    } else {
                                        errorMessages.push(errors[key]);
                                    }
                                });

                                errorMessage = errorMessages.join('<br>');
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }

                        toastr.error(errorMessage);
                        $('#schedule-error').show().text(errorMessage.replace(/<br>/g, '. '));
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Buat Jadwal');
                    }
                });
            });

            function addStaffShiftRow() {
                const template = $('#staff-shift-template').html();
                const newRow = $(template
                    .replace(/INDEX_PLACEHOLDER/g, staffShiftIndex)
                    .replace(/data-index=""/g, `data-index="${staffShiftIndex}"`)
                );

                $('#staff-shift-container').append(newRow);
                staffShiftIndex++;
                updatePreview();
            }

            function validateSchedule() {
                const staffSelects = $('.staff-select');
                const shiftSelects = $('.shift-select');

                let isValid = true;
                let message = '';

                // Get only filled rows
                const filledRows = [];
                $('.staff-shift-row').each(function() {
                    const staffId = $(this).find('.staff-select').val();
                    const shiftId = $(this).find('.shift-select').val();

                    if (staffId || shiftId) {
                        filledRows.push({
                            staffId: staffId,
                            shiftId: shiftId,
                            element: $(this)
                        });
                    }
                });

                // Check if at least one complete row exists
                const completeRows = filledRows.filter(row => row.staffId && row.shiftId);
                if (completeRows.length === 0) {
                    isValid = false;
                    message = 'Harap pilih minimal satu staff dan shift untuk dijadwalkan';
                }

                // Check for duplicate staff
                const selectedStaff = completeRows.map(row => row.staffId);
                const uniqueStaff = [...new Set(selectedStaff)];
                if (selectedStaff.length !== uniqueStaff.length) {
                    isValid = false;
                    message = 'Tidak boleh ada staff yang sama dipilih lebih dari sekali';
                }

                // Check for incomplete rows (staff selected but no shift, or vice versa)
                const incompleteRows = filledRows.filter(row => (row.staffId && !row.shiftId) || (!row.staffId &&
                    row.shiftId));
                if (incompleteRows.length > 0) {
                    isValid = false;
                    message =
                        'Pastikan setiap staff memiliki shift yang dipilih, atau hapus baris yang tidak lengkap';
                }

                // Validate required fields
                const startDate = $('#start_date').val();
                const months = $('#months').val();

                if (!startDate) {
                    isValid = false;
                    message = 'Tanggal mulai harus diisi';
                }

                if (!months) {
                    isValid = false;
                    message = 'Durasi bulan harus dipilih';
                }

                if (isValid) {
                    $('#schedule-error').hide();
                } else {
                    $('#schedule-error').show().text(message);
                }

                return {
                    isValid,
                    message
                };
            }

            function updatePreview() {
                const startDate = $('#start_date').val();
                const months = $('#months').val();

                if (!startDate || !months) {
                    $('#schedule-preview').html(
                        '<p class="text-muted">Pilih tanggal mulai dan durasi untuk melihat preview</p>');
                    return;
                }

                // Get staff-shift mappings
                const staffShifts = [];
                $('.staff-shift-row').each(function() {
                    const staffSelect = $(this).find('.staff-select');
                    const shiftSelect = $(this).find('.shift-select');
                    const staffValue = staffSelect.val();
                    const shiftValue = shiftSelect.val();

                    if (staffValue && shiftValue) {
                        const staffName = staffSelect.find('option:selected').text().split(' (')[0];
                        const shiftName = shiftSelect.find('option:selected').text().split(' (')[0];

                        staffShifts.push({
                            staffName: staffName,
                            shiftName: shiftName,
                            shiftId: shiftValue
                        });
                    }
                });

                if (staffShifts.length === 0) {
                    $('#schedule-preview').html(
                        '<p class="text-muted">Tambahkan staff dan shift untuk melihat preview jadwal</p>');
                    return;
                }

                let preview = '<h6>Preview Rotasi Shift:</h6>';

                // Calculate weeks to show (max 6 weeks for preview)
                const start = new Date(startDate);
                const weeksToShow = Math.min(parseInt(months) * 4, 6);

                // Create a copy of staffShifts for rotation simulation
                let currentStaffShifts = JSON.parse(JSON.stringify(staffShifts));

                for (let i = 0; i < weeksToShow; i++) {
                    const weekStart = new Date(start);
                    weekStart.setDate(start.getDate() + (i * 7));

                    preview += `
                <div class="preview-week">
                    <strong>Minggu ${i + 1}</strong>
                    <small class="text-muted">(${weekStart.toLocaleDateString('id-ID')})</small>
            `;

                    currentStaffShifts.forEach(staffShift => {
                        const shiftColor = getShiftColor(staffShift.shiftName);
                        preview += `
                    <div class="staff-info">
                        <span><strong>${staffShift.staffName}</strong></span>
                        <span class="badge ${shiftColor} shift-badge">${staffShift.shiftName}</span>
                    </div>
                `;
                    });

                    preview += '</div>';

                    // Simulate shift rotation for next week (same logic as backend)
                    if (currentStaffShifts.length > 1) {
                        const shifts = currentStaffShifts.map(s => ({
                            shiftName: s.shiftName,
                            shiftId: s.shiftId
                        }));

                        // Move last shift to first position
                        const lastShift = shifts.pop();
                        shifts.unshift(lastShift);

                        // Apply rotated shifts
                        currentStaffShifts.forEach((staff, index) => {
                            staff.shiftName = shifts[index].shiftName;
                            staff.shiftId = shifts[index].shiftId;
                        });
                    }
                }

                if (parseInt(months) * 4 > 6) {
                    preview += '<p class="text-muted"><small>... dan seterusnya untuk ' + months +
                        ' bulan</small></p>';
                }

                preview +=
                    '<div class="alert alert-info mt-2"><small><i class="fas fa-info-circle"></i> Setiap minggu shift akan berputar secara otomatis</small></div>';

                $('#schedule-preview').html(preview);
            }

            function getShiftColor(shiftName) {
                const name = shiftName.toLowerCase();
                if (name.includes('pagi')) return 'shift-color-pagi';
                if (name.includes('middle') || name.includes('siang')) return 'shift-color-middle';
                if (name.includes('malam')) return 'shift-color-malam';
                return 'badge-secondary';
            }

            function showSuccessModal(schedules, message, summary) {
                let content = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> ${message}
            </div>
        `;

                if (summary) {
                    content += `
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">${summary.total_staff}</h4>
                            <small class="text-muted">Staff</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">${summary.total_weeks}</h4>
                            <small class="text-muted">Minggu</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="text-info">${summary.start_date} - ${summary.end_date}</h6>
                            <small class="text-muted">Periode Jadwal</small>
                        </div>
                    </div>
                </div>
            `;
                }

                content += `
            <h5>Ringkasan Jadwal yang Dibuat:</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-striped">
                    <thead class="thead-light sticky-top">
                        <tr>
                            <th>Minggu</th>
                            <th>Tanggal Mulai</th>
                            <th>Staff & Shift</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                schedules.slice(0, 10).forEach(function(weekSchedule) { // Show only first 10 weeks
                    let staffShiftDisplay = '';
                    weekSchedule.schedules.forEach(function(schedule) {
                        const shiftColor = getShiftColor(schedule.shift_name);
                        staffShiftDisplay += `
                    <div class="mb-1">
                        <strong>${schedule.user_name}</strong>
                        <span class="badge ${shiftColor}">${schedule.shift_name}</span>
                    </div>
                `;
                    });

                    content += `
                <tr>
                    <td><strong>Minggu ${weekSchedule.week_number}</strong></td>
                    <td>${new Date(weekSchedule.week).toLocaleDateString('id-ID')}</td>
                    <td>${staffShiftDisplay}</td>
                </tr>
            `;
                });

                content += '</tbody></table></div>';

                if (schedules.length > 10) {
                    content +=
                        `<p class="text-muted text-center">... dan ${schedules.length - 10} minggu lainnya</p>`;
                }

                content += `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Total ${schedules.length} minggu jadwal telah dibuat.
                Detail jadwal harian akan otomatis dibuat sesuai dengan hari kerja masing-masing shift.
            </div>
        `;

                $('#success-content').html(content);
                $('#successModal').modal('show');
            }

            // Global functions
            window.createAnother = function() {
                $('#successModal').modal('hide');
                $('#schedule-form')[0].reset();
                $('#staff-shift-container').empty();
                $('#schedule-error').hide();
                staffShiftIndex = 0;
                addStaffShiftRow();
                updatePreview();
            };

            // Reset form
            $('button[type="reset"]').click(function() {
                $('#staff-shift-container').empty();
                $('#schedule-error').hide();
                staffShiftIndex = 0;
                addStaffShiftRow();
                updatePreview();
            });

            // Add preview schedule button
            $('<button type="button" class="btn btn-info ml-2" id="preview-btn"><i class="fas fa-eye"></i> Preview Jadwal</button>')
                .insertAfter('button[type="reset"]')
                .click(function() {
                    const validation = validateSchedule();
                    if (!validation.isValid) {
                        toastr.error(validation.message);
                        return;
                    }

                    const staffSchedules = [];
                    $('.staff-shift-row').each(function() {
                        const staffId = $(this).find('.staff-select').val();
                        const shiftId = $(this).find('.shift-select').val();

                        if (staffId && shiftId) {
                            staffSchedules.push({
                                user_id: parseInt(staffId),
                                shift_id: parseInt(shiftId)
                            });
                        }
                    });

                    $.ajax({
                        url: '/jadwal/preview',
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            staff_schedules: staffSchedules,
                            start_date: $('#start_date').val(),
                            weeks: 8
                        },
                        success: function(response) {
                            if (response.success) {
                                showPreviewModal(response.data);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Gagal membuat preview jadwal');
                        }
                    });
                });

            function showPreviewModal(previewData) {
                let content = '<div class="table-responsive"><table class="table table-sm">';
                content += '<thead><tr><th>Minggu</th><th>Tanggal</th><th>Staff & Shift</th></tr></thead><tbody>';

                previewData.forEach(function(week) {
                    let staffDisplay = '';
                    week.schedules.forEach(function(schedule) {
                        const shiftColor = getShiftColor(schedule.shift_name);
                        staffDisplay +=
                            `<div><strong>${schedule.user_name}</strong> <span class="badge ${shiftColor}">${schedule.shift_name}</span></div>`;
                    });

                    content += `<tr>
                <td>Minggu ${week.week_number}</td>
                <td>${new Date(week.week).toLocaleDateString('id-ID')}</td>
                <td>${staffDisplay}</td>
            </tr>`;
                });

                content += '</tbody></table></div>';

                // Create and show modal
                const modalHtml = `
            <div class="modal fade" id="previewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Preview Jadwal Shift</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">${content}</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                $('#previewModal').remove();
                $('body').append(modalHtml);
                $('#previewModal').modal('show');
            }
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            let staffShiftIndex = 0;

            // Set minimum date to today
            $('#start_date').attr('min', new Date().toISOString().split('T')[0]);

            // Add first staff-shift row on load
            addStaffShiftRow();

            // Add staff-shift row
            $('#add-staff-shift').click(function() {
                addStaffShiftRow();
            });

            // Remove staff-shift row
            $(document).on('click', '.remove-staff-shift', function() {
                const container = $('#staff-shift-container');
                if (container.children('.staff-shift-row').length > 1) {
                    $(this).closest('.staff-shift-row').remove();
                    updatePreview();
                    validateSchedule();
                } else {
                    toastr.warning('Minimal harus ada satu staff yang dijadwalkan');
                }
            });

            // Update preview when form changes
            $(document).on('change', '.staff-select, .shift-select', function() {
                updatePreview();
                validateSchedule();
            });

            $('#start_date, #months').change(function() {
                updatePreview();
            });

            // Form submission
            $('#schedule-form').submit(function(e) {
                e.preventDefault();

                const validation = validateSchedule();
                if (!validation.isValid) {
                    $('#schedule-error').show().text(validation.message);
                    return;
                }

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...');

                $.ajax({
                    url: '{{ route('jadwal.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showSuccessModal(response.data, response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            const errorMessages = Object.values(errors).flat();
                            toastr.error(errorMessages.join('<br>'));
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Buat Jadwal');
                    }
                });
            });

            // function addStaffShiftRow() {
            //     const template = $('#staff-shift-template').html();
            //     const newRow = $(template.replace(/data-index=""/g, `data-index="${staffShiftIndex}"`));

            //     $('#staff-shift-container').append(newRow);
            //     staffShiftIndex++;

            //     updatePreview();
            // }

            function addStaffShiftRow() {
                const template = $('#staff-shift-template').html();
                const newRow = $(template
                    .replace(/INDEX_PLACEHOLDER/g, staffShiftIndex)
                    .replace(/data-index=""/g, `data-index="${staffShiftIndex}"`)
                );

                $('#staff-shift-container').append(newRow);
                staffShiftIndex++;
                updatePreview();
            }

            function validateSchedule() {
                const staffSelects = $('.staff-select');
                const shiftSelects = $('.shift-select');

                let isValid = true;
                let message = '';

                // Check if at least one staff is selected
                const filledRows = staffSelects.filter(function() {
                    return $(this).val() !== '';
                }).length;

                if (filledRows === 0) {
                    isValid = false;
                    message = 'Harap pilih minimal satu staff untuk dijadwalkan';
                }

                // Check for duplicate staff
                const selectedStaff = [];
                staffSelects.each(function() {
                    const value = $(this).val();
                    if (value && selectedStaff.includes(value)) {
                        isValid = false;
                        message = 'Tidak boleh ada staff yang sama dipilih lebih dari sekali';
                    }
                    if (value) selectedStaff.push(value);
                });

                // Check for duplicate shifts
                const selectedShifts = [];
                shiftSelects.each(function() {
                    const value = $(this).val();
                    const staffValue = $(this).closest('.staff-shift-row').find('.staff-select').val();

                    if (value && staffValue) {
                        // if (selectedShifts.includes(value)) {
                        //     isValid = false;
                        //     message = 'Tidak boleh ada shift yang sama untuk staff yang berbeda di minggu yang sama';
                        // }
                        selectedShifts.push(value);
                    }
                });

                // Check for incomplete rows
                staffSelects.each(function() {
                    const staffValue = $(this).val();
                    const shiftValue = $(this).closest('.staff-shift-row').find('.shift-select').val();

                    if ((staffValue && !shiftValue) || (!staffValue && shiftValue)) {
                        isValid = false;
                        message = 'Pastikan setiap staff memiliki shift yang dipilih';
                    }
                });

                if (isValid) {
                    $('#schedule-error').hide();
                } else {
                    $('#schedule-error').show().text(message);
                }

                return {
                    isValid,
                    message
                };
            }

            function updatePreview() {
                const startDate = $('#start_date').val();
                const months = $('#months').val();

                if (!startDate || !months) {
                    $('#schedule-preview').html(
                        '<p class="text-muted">Pilih tanggal mulai dan durasi untuk melihat preview</p>');
                    return;
                }

                // Get staff-shift mappings
                const staffShifts = [];
                $('.staff-shift-row').each(function() {
                    const staffSelect = $(this).find('.staff-select');
                    const shiftSelect = $(this).find('.shift-select');
                    const staffValue = staffSelect.val();
                    const shiftValue = shiftSelect.val();

                    if (staffValue && shiftValue) {
                        const staffName = staffSelect.find('option:selected').text().split(' (')[0];
                        const shiftName = shiftSelect.find('option:selected').text().split(' (')[0];

                        staffShifts.push({
                            staffName: staffName,
                            shiftName: shiftName,
                            shiftId: shiftValue
                        });
                    }
                });

                if (staffShifts.length === 0) {
                    $('#schedule-preview').html(
                        '<p class="text-muted">Tambahkan staff dan shift untuk melihat preview jadwal</p>');
                    return;
                }

                let preview = '<h6>Preview Jadwal:</h6>';

                // Calculate weeks to show (max 6 weeks for preview)
                const start = new Date(startDate);
                const weeksToShow = Math.min(parseInt(months) * 4, 6);

                // Create a copy of staffShifts for rotation
                let currentStaffShifts = [...staffShifts];

                for (let i = 0; i < weeksToShow; i++) {
                    const weekStart = new Date(start);
                    weekStart.setDate(start.getDate() + (i * 7));

                    preview += `
                <div class="preview-week">
                    <strong>Minggu ${i + 1}</strong>
                    <small class="text-muted">(${weekStart.toLocaleDateString('id-ID')})</small>
            `;

                    currentStaffShifts.forEach(staffShift => {
                        const shiftColor = getShiftColor(staffShift.shiftName);
                        preview += `
                    <div class="staff-info">
                        <span><strong>${staffShift.staffName}</strong></span>
                        <span class="badge ${shiftColor} shift-badge">${staffShift.shiftName}</span>
                    </div>
                `;
                    });

                    preview += '</div>';

                    // Rotate shifts for next week
                    if (currentStaffShifts.length > 1) {
                        const lastShift = currentStaffShifts[currentStaffShifts.length - 1];
                        for (let j = currentStaffShifts.length - 1; j > 0; j--) {
                            currentStaffShifts[j].shiftName = currentStaffShifts[j - 1].shiftName;
                            currentStaffShifts[j].shiftId = currentStaffShifts[j - 1].shiftId;
                        }
                        currentStaffShifts[0].shiftName = lastShift.shiftName;
                        currentStaffShifts[0].shiftId = lastShift.shiftId;
                    }
                }

                if (parseInt(months) * 4 > 6) {
                    preview += '<p class="text-muted"><small>... dan seterusnya untuk ' + months +
                        ' bulan</small></p>';
                }

                $('#schedule-preview').html(preview);
            }

            function getShiftColor(shiftName) {
                const name = shiftName.toLowerCase();
                if (name.includes('pagi')) return 'shift-color-pagi';
                if (name.includes('middle') || name.includes('siang')) return 'shift-color-middle';
                if (name.includes('malam')) return 'shift-color-malam';
                return 'badge-secondary';
            }

            function showSuccessModal(schedules, message) {
                let content = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ${message}
                    </div>
                    <h5>Ringkasan Jadwal yang Dibuat:</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Minggu</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Staff & Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                schedules.forEach(function(weekSchedule, index) {
                    let staffShiftDisplay = '';
                    weekSchedule.schedules.forEach(function(schedule) {
                        const shiftColor = getShiftColor(schedule.shift_name);
                        staffShiftDisplay += `
                    <div class="mb-1">
                        <strong>${schedule.user_name}</strong>
                        <span class="badge ${shiftColor}">${schedule.shift_name}</span>
                    </div>
                `;
                    });

                    content += `
                <tr>
                    <td>Minggu ${weekSchedule.week_number}</td>
                    <td>${new Date(weekSchedule.week).toLocaleDateString('id-ID')}</td>
                    <td>${staffShiftDisplay}</td>
                </tr>
            `;
                });

                content += `
                    </tbody>
                </table>
            </div>
            <p class="text-muted">
                <i class="fas fa-info-circle"></i>
                Total ${schedules.length} minggu jadwal telah dibuat.
                Detail jadwal harian akan otomatis dibuat sesuai dengan hari kerja masing-masing shift.
            </p>
        `;

                $('#success-content').html(content);
                $('#successModal').modal('show');
            }

            window.createAnother = function() {
                $('#successModal').modal('hide');
                $('#schedule-form')[0].reset();
                $('#staff-shift-container').empty();
                $('#schedule-error').hide();
                staffShiftIndex = 0;
                addStaffShiftRow();
                updatePreview();
            };

            // Reset form
            $('button[type="reset"]').click(function() {
                $('#staff-shift-container').empty();
                $('#schedule-error').hide();
                staffShiftIndex = 0;
                addStaffShiftRow();
                updatePreview();
            });
        });
    </script> --}}
@endpush
