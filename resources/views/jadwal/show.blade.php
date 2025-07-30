@extends('layouts.app')

@section('title', 'Detail Jadwal - ' . $jadwal->user->name)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-calendar-check me-2"></i>
                            Detail Jadwal - {{ $jadwal->user->name }}
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="{{ route('jadwal.edit', $jadwal->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Edit Jadwal
                        </a>
                        <button type="button" class="btn btn-info" onclick="printSchedule()">
                            <i class="fas fa-print me-1"></i>
                            Print
                        </button>
                        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Schedule Information -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Jadwal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Staff:</strong></div>
                            <div class="col-sm-7">{{ $jadwal->user->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Shift:</strong></div>
                            <div class="col-sm-7">
                                <span class="badge badge-primary">{{ $jadwal->shift->name }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Jam Kerja:</strong></div>
                            <div class="col-sm-7">
                                {{ Carbon\Carbon::parse($jadwal->shift->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($jadwal->shift->end_time)->format('H:i') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Periode:</strong></div>
                            <div class="col-sm-7">{{ $jadwal->formatted_period }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Minggu:</strong></div>
                            <div class="col-sm-7">{{ $jadwal->week_info }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Status:</strong></div>
                            <div class="col-sm-7">
                                @php
                                    $statusClass =
                                        $jadwal->status === 'active'
                                            ? 'success'
                                            : ($jadwal->status === 'completed'
                                                ? 'info'
                                                : 'secondary');
                                @endphp
                                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($jadwal->status) }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-5"><strong>Total Hari:</strong></div>
                            <div class="col-sm-7">
                                <span class="badge badge-info">{{ $jadwal->scheduleDetails->count() }} hari</span>
                            </div>
                        </div>
                        @if ($jadwal->notes)
                            <div class="row mb-3">
                                <div class="col-sm-5"><strong>Catatan:</strong></div>
                                <div class="col-sm-7">{{ $jadwal->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Ringkasan Kehadiran
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $totalDays = $jadwal->scheduleDetails->count();
                            $presentDays = $jadwal->scheduleDetails
                                ->whereIn('attendance_status', ['present', 'late', 'early_leave', 'overtime'])
                                ->count();
                            $absentDays = $jadwal->scheduleDetails->where('attendance_status', 'absent')->count();
                            $scheduledDays = $jadwal->scheduleDetails->where('attendance_status', 'scheduled')->count();
                            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
                        @endphp

                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-success mb-1">{{ $presentDays }}</h3>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-danger mb-1">{{ $absentDays }}</h3>
                                    <small class="text-muted">Tidak Hadir</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-secondary mb-1">{{ $scheduledDays }}</h3>
                                    <small class="text-muted">Terjadwal</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border rounded p-3">
                                    <h3 class="text-primary mb-1">{{ $attendanceRate }}%</h3>
                                    <small class="text-muted">Tingkat Kehadiran</small>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mb-2" style="height: 20px;">
                            @if ($presentDays > 0)
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ ($presentDays / $totalDays) * 100 }}%"
                                    title="Hadir: {{ $presentDays }} hari">
                                </div>
                            @endif
                            @if ($absentDays > 0)
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ ($absentDays / $totalDays) * 100 }}%"
                                    title="Tidak Hadir: {{ $absentDays }} hari">
                                </div>
                            @endif
                            @if ($scheduledDays > 0)
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ ($scheduledDays / $totalDays) * 100 }}%"
                                    title="Terjadwal: {{ $scheduledDays }} hari">
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">
                            <span class="badge badge-success me-1">■</span> Hadir
                            <span class="badge badge-danger me-1">■</span> Tidak Hadir
                            <span class="badge badge-secondary me-1">■</span> Terjadwal
                        </small>
                    </div>
                </div>
            </div>

            <!-- Schedule Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar-day me-2"></i>
                                    Detail Jadwal Harian
                                </h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="bulkUpdateAttendance()">
                                        <i class="fas fa-edit me-1"></i>
                                        Bulk Update
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" onclick="markAllPresent()">
                                        <i class="fas fa-check me-1"></i>
                                        Mark All Present
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="scheduleDetailsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            #
                                            <input type="checkbox" id="selectAll" class="form-check">
                                        </th>
                                        <th width="15%">Tanggal</th>
                                        <th width="10%">Hari</th>
                                        <th width="15%">Jam Shift</th>
                                        <th width="15%">Jam Aktual</th>
                                        <th width="12%">Status</th>
                                        <th width="8%">Jam Kerja</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwal->scheduleDetails->sortBy('work_date') as $detail)
                                        <tr data-detail-id="{{ $detail->id }}">
                                            <td>
                                                <input type="checkbox" class="form-check detail-checkbox"
                                                    value="{{ $detail->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $detail->formatted_date }}</strong>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $detail->day_name_indonesian }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-outline-primary">
                                                    {{ $detail->formatted_shift_time }}
                                                </span>
                                            </td>
                                            <td id="actual-time-{{ $detail->id }}">
                                                <small class="text-muted">{{ $detail->formatted_actual_time }}</small>
                                            </td>
                                            <td id="status-{{ $detail->id }}">
                                                <span class="badge badge-{{ $detail->status_badge_class }}">
                                                    {{ $detail->status_indonesian }}
                                                </span>
                                            </td>
                                            <td id="working-hours-{{ $detail->id }}">
                                                @if ($detail->working_hours > 0)
                                                    <span class="badge badge-info">{{ $detail->working_hours }}h</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="updateAttendance({{ $detail->id }})"
                                                        title="Update Kehadiran">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    @if ($detail->attendance_status == 'scheduled')
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="quickCheckIn({{ $detail->id }})"
                                                            title="Quick Check-in">
                                                            <i class="fas fa-sign-in-alt"></i>
                                                        </button>
                                                    @endif
                                                    @if ($detail->actual_start_time && $detail->actual_end_time == null)
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="quickCheckOut({{ $detail->id }})"
                                                            title="Quick Check-out">
                                                            <i class="fas fa-sign-out-alt"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Tidak ada detail jadwal yang ditemukan</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Attendance Modal -->
    <div class="modal fade" id="updateAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateAttendanceForm">
                    <div class="modal-body">
                        <input type="hidden" id="detail_id" name="detail_id">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" id="check_in_time" name="check_in_time">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Keluar</label>
                                <input type="time" class="form-control" id="check_out_time" name="check_out_time">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Catatan kehadiran (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="bulkUpdateForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Pilih beberapa tanggal dari tabel untuk melakukan update kehadiran secara massal.
                        </div>

                        <div id="selectedDates" class="mb-3">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" id="bulk_check_in_time" name="check_in_time">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Keluar</label>
                                <input type="time" class="form-control" id="bulk_check_out_time"
                                    name="check_out_time">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status Kehadiran</label>
                            <select class="form-control" id="bulk_attendance_status" name="attendance_status">
                                <option value="">Tidak ada perubahan</option>
                                <option value="present">Hadir</option>
                                <option value="absent">Tidak Hadir</option>
                                <option value="late">Terlambat</option>
                                <option value="early_leave">Pulang Cepat</option>
                                <option value="overtime">Lembur</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="bulk_notes" name="notes" rows="3"
                                placeholder="Catatan untuk semua tanggal yang dipilih"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .badge-outline-primary {
            color: #007bff;
            border: 1px solid #007bff;
            background: transparent;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .btn-group .btn {
            margin-right: 2px;
        }

        .progress {
            background-color: #e9ecef;
        }

        @media print {

            .btn,
            .card-header .col-auto {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Select all checkbox functionality
            $('#selectAll').change(function() {
                $('.detail-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Individual checkbox change
            $('.detail-checkbox').change(function() {
                if (!$(this).is(':checked')) {
                    $('#selectAll').prop('checked', false);
                } else {
                    if ($('.detail-checkbox:checked').length === $('.detail-checkbox').length) {
                        $('#selectAll').prop('checked', true);
                    }
                }
            });
        });

        function updateAttendance(detailId) {
            $.get(`/jadwal/jadwal-detail/${detailId}`, function(response) {
                if (response.success) {
                    const detail = response.data;
                    $('#detail_id').val(detailId);

                    // Set existing values
                    if (detail.actual_start_time) {
                        $('#check_in_time').val(moment(detail.actual_start_time).format('HH:mm'));
                    }
                    if (detail.actual_end_time) {
                        $('#check_out_time').val(moment(detail.actual_end_time).format('HH:mm'));
                    }
                    $('#notes').val(detail.notes || '');

                    $('#updateAttendanceModal').modal('show');
                }
            });
        }

        // Handle update attendance form submission
        $('#updateAttendanceForm').submit(function(e) {
            e.preventDefault();

            const detailId = $('#detail_id').val();
            const formData = {
                check_in_time: $('#check_in_time').val(),
                check_out_time: $('#check_out_time').val(),
                notes: $('#notes').val()
            };

            $.ajax({
                url: `/jadwal/detail/${detailId}/attendance`,
                method: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Update table row
                        updateTableRow(detailId, response.data);
                        $('#updateAttendanceModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan'
                    });
                }
            });
        });

        // Quick check-in
        function quickCheckIn(detailId) {
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 5);

            Swal.fire({
                title: 'Quick Check-in',
                text: `Check-in pada ${currentTime}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Check-in',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/jadwal/detail/${detailId}/attendance`,
                        method: 'PUT',
                        data: {
                            check_in_time: currentTime
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                updateTableRow(detailId, response.data);
                                Swal.fire('Berhasil!', 'Check-in berhasil dicatat', 'success');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                        }
                    });
                }
            });
        }

        // Quick check-out
        function quickCheckOut(detailId) {
            const now = new Date();
            const currentTime = now.toTimeString().slice(0, 5);

            Swal.fire({
                title: 'Quick Check-out',
                text: `Check-out pada ${currentTime}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Check-out',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/jadwal/detail/${detailId}/attendance`,
                        method: 'PUT',
                        data: {
                            check_out_time: currentTime
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                updateTableRow(detailId, response.data);
                                Swal.fire('Berhasil!', 'Check-out berhasil dicatat', 'success');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                        }
                    });
                }
            });
        }

        // Bulk update attendance
        function bulkUpdateAttendance() {
            const selectedCheckboxes = $('.detail-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu tanggal untuk di-update'
                });
                return;
            }

            // Show selected dates
            let selectedDatesHtml = '<h6>Tanggal yang dipilih:</h6><ul>';
            selectedCheckboxes.each(function() {
                const row = $(this).closest('tr');
                const date = row.find('td:nth-child(2)').text().trim();
                const day = row.find('td:nth-child(3)').text().trim();
                selectedDatesHtml += `<li>${date} (${day})</li>`;
            });
            selectedDatesHtml += '</ul>';

            $('#selectedDates').html(selectedDatesHtml);
            $('#bulkUpdateModal').modal('show');
        }

        // Handle bulk update form submission
        $('#bulkUpdateForm').submit(function(e) {
            e.preventDefault();

            const selectedIds = $('.detail-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            const updates = selectedIds.map(id => ({
                detail_id: id,
                check_in_time: $('#bulk_check_in_time').val(),
                check_out_time: $('#bulk_check_out_time').val(),
                attendance_status: $('#bulk_attendance_status').val(),
                notes: $('#bulk_notes').val()
            }));

            $.ajax({
                url: '/jadwal/attendance/bulk-update',
                method: 'POST',
                data: {
                    updates: updates
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Update table rows
                        response.data.forEach(detail => {
                            updateTableRow(detail.id, detail);
                        });

                        $('#bulkUpdateModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Clear selections
                        $('.detail-checkbox').prop('checked', false);
                        $('#selectAll').prop('checked', false);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan'
                    });
                }
            });
        });

        // Mark all as present
        function markAllPresent() {
            Swal.fire({
                title: 'Mark All Present?',
                text: 'Ini akan menandai semua jadwal sebagai hadir dengan waktu saat ini',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Mark All Present',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const now = new Date();
                    const currentTime = now.toTimeString().slice(0, 5);

                    const allIds = $('.detail-checkbox').map(function() {
                        return $(this).val();
                    }).get();

                    const updates = allIds.map(id => ({
                        detail_id: id,
                        check_in_time: '08:00',
                        check_out_time: '17:00',
                        attendance_status: 'present',
                        notes: 'Bulk marked as present'
                    }));

                    $.ajax({
                        url: '/jadwal/attendance/bulk-update',
                        method: 'POST',
                        data: {
                            updates: updates
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                // Reload page to show updated data
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }

        // Update table row with new data
        function updateTableRow(detailId, data) {
            const row = $(`tr[data-detail-id="${detailId}"]`);

            // Update actual time
            $(`#actual-time-${detailId}`).html(`<small class="text-muted">${data.actual_time || 'Belum absen'}</small>`);

            // Update status
            $(`#status-${detailId}`).html(
                `<span class="badge badge-${data.status_class}">${data.attendance_status}</span>`);

            // Update working hours
            if (data.working_hours > 0) {
                $(`#working-hours-${detailId}`).html(`<span class="badge badge-info">${data.working_hours}h</span>`);
            } else {
                $(`#working-hours-${detailId}`).html(`<span class="text-muted">-</span>`);
            }

            // Update action buttons based on status
            updateActionButtons(detailId, data);
        }

        // Update action buttons based on attendance status
        function updateActionButtons(detailId, data) {
            const actionCell = $(`tr[data-detail-id="${detailId}"] td:last-child`);
            let buttonsHtml = `
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="updateAttendance(${detailId})"
                            title="Update Kehadiran">
                        <i class="fas fa-edit"></i>
                    </button>
            `;

            // Add quick check-in button if not checked in yet
            if (!data.actual_start_time) {
                buttonsHtml += `
                    <button type="button" class="btn btn-sm btn-outline-success"
                            onclick="quickCheckIn(${detailId})"
                            title="Quick Check-in">
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                `;
            }

            // Add quick check-out button if checked in but not checked out
            if (data.actual_start_time && !data.actual_end_time) {
                buttonsHtml += `
                    <button type="button" class="btn btn-sm btn-outline-warning"
                            onclick="quickCheckOut(${detailId})"
                            title="Quick Check-out">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                `;
            }

            buttonsHtml += '</div>';
            actionCell.html(buttonsHtml);
        }

        // Print schedule
        function printSchedule() {
            window.print();
        }

        // Reset modal forms when closed
        $('#updateAttendanceModal').on('hidden.bs.modal', function() {
            $('#updateAttendanceForm')[0].reset();
        });

        $('#bulkUpdateModal').on('hidden.bs.modal', function() {
            $('#bulkUpdateForm')[0].reset();
            $('#selectedDates').empty();
        });

        // Add moment.js for date formatting (include in head if not already included)
        if (typeof moment === 'undefined') {
            $('head').append(
                '<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"><\/script>');
        }
    </script>
@endpush
