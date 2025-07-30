@extends('layouts.app')

@section('title', 'Detail Jadwal Harian')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('jadwal.index') }}">Jadwal</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('jadwal.show', $jadwal->id) }}">{{ $jadwal->user->name }}</a></li>
                        <li class="breadcrumb-item active">Detail Harian</li>
                    </ol>
                </nav>

                <!-- Schedule Info Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-2">
                                    <i class="fas fa-calendar-day me-2"></i>
                                    Detail Jadwal Harian - {{ $jadwal->user->name }}
                                </h4>
                                <div class="row text-muted">
                                    <div class="col-md-3">
                                        <i class="fas fa-user me-1"></i>
                                        <strong>Staff:</strong> {{ $jadwal->user->name }}
                                    </div>
                                    <div class="col-md-3">
                                        <i class="fas fa-clock me-1"></i>
                                        <strong>Shift:</strong> {{ $jadwal->shift->name }}
                                    </div>
                                    <div class="col-md-3">
                                        <i class="fas fa-calendar me-1"></i>
                                        <strong>Periode:</strong> {{ $jadwal->formatted_period }}
                                    </div>
                                    <div class="col-md-3">
                                        <i class="fas fa-info me-1"></i>
                                        <strong>{{ $jadwal->week_info }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="toggleBulkEdit()">
                                        <i class="fas fa-edit me-1"></i>
                                        Bulk Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="exportDetails()">
                                        <i class="fas fa-download me-1"></i>
                                        Export
                                    </button>
                                    <a href="{{ route('jadwal.show', $jadwal->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters & Actions -->
                <div class="card mb-4" id="bulkEditPanel" style="display: none;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-tools me-2"></i>
                            Bulk Edit Kehadiran
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="bulkEditForm">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Pilih Semua</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            Select All
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="bulkStatus">
                                        <option value="">Pilih Status</option>
                                        <option value="present">Hadir</option>
                                        <option value="absent">Tidak Hadir</option>
                                        <option value="late">Terlambat</option>
                                        <option value="early_leave">Pulang Cepat</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jam Masuk</label>
                                    <input type="time" class="form-control" id="bulkCheckIn">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jam Keluar</label>
                                    <input type="time" class="form-control" id="bulkCheckOut">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Catatan</label>
                                    <input type="text" class="form-control" id="bulkNotes"
                                        placeholder="Catatan (opsional)">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-primary" onclick="applyBulkEdit()">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Jadwal Harian ({{ $details->count() }} hari)
                                </h6>
                            </div>
                            <div class="col-auto">
                                <!-- Summary badges -->
                                <div id="summaryBadges">
                                    @php
                                        $statusCounts = $details->groupBy('attendance_status')->map->count();
                                    @endphp
                                    @foreach ($statusCounts as $status => $count)
                                        <span
                                            class="badge badge-{{ $status === 'present' ? 'success' : ($status === 'absent' ? 'danger' : ($status === 'late' ? 'warning' : 'secondary')) }} me-1">
                                            {{ ucfirst($status) }}: {{ $count }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="detailsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" class="form-check-input" id="masterCheck"
                                                style="display: none;">
                                            #
                                        </th>
                                        <th width="12%">Tanggal</th>
                                        <th width="10%">Hari</th>
                                        <th width="12%">Jam Shift</th>
                                        <th width="12%">Jam Aktual</th>
                                        <th width="10%">Jam Kerja</th>
                                        <th width="12%">Status</th>
                                        <th width="20%">Catatan</th>
                                        <th width="7%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $index => $detail)
                                        <tr data-detail-id="{{ $detail['id'] }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input detail-check"
                                                    value="{{ $detail['id'] }}" style="display: none;">
                                                {{ $index + 1 }}
                                            </td>
                                            <td>
                                                <strong>{{ $detail['work_date'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $detail['day_name'] }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $detail['shift_time'] }}</small>
                                            </td>
                                            <td>
                                                <span class="actual-time">{{ $detail['actual_time'] }}</span>
                                            </td>
                                            <td>
                                                <span class="working-hours">
                                                    @if ($detail['working_hours'] > 0)
                                                        {{ $detail['working_hours'] }} jam
                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $detail['status_class'] }} status-badge">
                                                    {{ $detail['attendance_status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="notes-text">{{ $detail['notes'] ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="editAttendance({{ $detail['id'] }})" title="Edit Kehadiran">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clock me-2"></i>
                        Edit Kehadiran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="attendanceForm">
                    <div class="modal-body">
                        <input type="hidden" id="editDetailId">

                        <div class="mb-3">
                            <label class="form-label">Tanggal & Hari</label>
                            <div class="form-control-plaintext" id="editDateInfo"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jam Shift</label>
                            <div class="form-control-plaintext" id="editShiftTime"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="editCheckIn" class="form-label">Jam Masuk</label>
                                <input type="time" class="form-control" id="editCheckIn" name="check_in_time">
                            </div>
                            <div class="col-md-6">
                                <label for="editCheckOut" class="form-label">Jam Keluar</label>
                                <input type="time" class="form-control" id="editCheckOut" name="check_out_time">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editNotes" class="form-label">Catatan</label>
                            <textarea class="form-control" id="editNotes" name="notes" rows="3"
                                placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Status kehadiran akan otomatis diperbarui berdasarkan jam masuk dan keluar yang
                                diinput.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-download me-2"></i>
                        Export Detail Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        <div class="mb-3">
                            <label class="form-label">Format Export</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatPdf"
                                    value="pdf" checked>
                                <label class="form-check-label" for="formatPdf">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatExcel"
                                    value="excel">
                                <label class="form-check-label" for="formatExcel">
                                    <i class="fas fa-file-excel text-success me-2"></i>
                                    Excel
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatCsv"
                                    value="csv">
                                <label class="form-check-label" for="formatCsv">
                                    <i class="fas fa-file-csv text-info me-2"></i>
                                    CSV
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Include</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeStats" checked>
                                <label class="form-check-label" for="includeStats">
                                    Statistik Kehadiran
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeNotes" checked>
                                <label class="form-check-label" for="includeNotes">
                                    Catatan
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="processExport()">
                        <i class="fas fa-download me-1"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/jadwal/jadwal.css') }}"> --}}
    <style>
        .table td {
            vertical-align: middle;
        }

        .detail-check {
            margin-right: 8px;
        }

        #bulkEditPanel {
            border-left: 4px solid #0d6efd;
        }

        .working-hours {
            font-weight: 500;
        }

        .actual-time {
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .notes-text {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            font-size: 0.75em;
            padding: 0.375em 0.75em;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            let bulkEditMode = false;
            let selectedDetails = [];

            // Toggle bulk edit mode
            window.toggleBulkEdit = function() {
                bulkEditMode = !bulkEditMode;

                if (bulkEditMode) {
                    $('#bulkEditPanel').slideDown();
                    $('.detail-check, #masterCheck').show();
                    $('button[onclick="toggleBulkEdit()"]').html(
                        '<i class="fas fa-times me-1"></i> Cancel Bulk');
                } else {
                    $('#bulkEditPanel').slideUp();
                    $('.detail-check, #masterCheck').hide();
                    $('.detail-check').prop('checked', false);
                    $('#masterCheck').prop('checked', false);
                    selectedDetails = [];
                    $('button[onclick="toggleBulkEdit()"]').html('<i class="fas fa-edit me-1"></i> Bulk Edit');
                }
            };

            // Master checkbox handler
            $('#masterCheck').on('change', function() {
                const checked = $(this).is(':checked');
                $('.detail-check').prop('checked', checked);
                updateSelectedDetails();
            });

            // Individual checkbox handler
            $(document).on('change', '.detail-check', function() {
                updateSelectedDetails();

                // Update master checkbox
                const totalChecks = $('.detail-check').length;
                const checkedCount = $('.detail-check:checked').length;
                $('#masterCheck').prop('checked', totalChecks === checkedCount);
            });

            function updateSelectedDetails() {
                selectedDetails = [];
                $('.detail-check:checked').each(function() {
                    selectedDetails.push(parseInt($(this).val()));
                });
            }

            // Apply bulk edit
            window.applyBulkEdit = function() {
                if (selectedDetails.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Data',
                        text: 'Harap pilih minimal satu detail jadwal untuk diubah',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const updates = selectedDetails.map(detailId => ({
                    detail_id: detailId,
                    check_in_time: $('#bulkCheckIn').val() || null,
                    check_out_time: $('#bulkCheckOut').val() || null,
                    notes: $('#bulkNotes').val() || null,
                    attendance_status: $('#bulkStatus').val() || null
                }));

                Swal.fire({
                    title: 'Konfirmasi Bulk Update',
                    text: `Akan mengubah ${selectedDetails.length} data kehadiran. Lanjutkan?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Update',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processBulkUpdate(updates);
                    }
                });
            };

            function processBulkUpdate(updates) {
                const loadingToast = Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang mengupdate data kehadiran',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('jadwal.attendance.bulk-update') }}',
                    method: 'POST',
                    data: {
                        updates: updates
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        loadingToast.close();

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Update table rows
                                response.data.forEach(detail => {
                                    updateTableRow(detail);
                                });

                                // Reset bulk edit form
                                $('#bulkEditForm')[0].reset();
                                $('.detail-check').prop('checked', false);
                                $('#masterCheck').prop('checked', false);
                                selectedDetails = [];

                                // Update summary badges
                                updateSummaryBadges();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        loadingToast.close();
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response?.message || 'Terjadi kesalahan saat mengupdate data',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            // Edit single attendance
            window.editAttendance = function(detailId) {
                // Get current data from table row
                const row = $(`tr[data-detail-id="${detailId}"]`);
                const date = row.find('td:eq(1)').text().trim();
                const day = row.find('td:eq(2)').text().trim();
                const shiftTime = row.find('td:eq(3)').text().trim();
                const notes = row.find('.notes-text').text().trim();

                // Populate modal
                $('#editDetailId').val(detailId);
                $('#editDateInfo').text(`${date} - ${day}`);
                $('#editShiftTime').text(shiftTime);
                $('#editNotes').val(notes === '-' ? '' : notes);

                // Get current actual times (you might need to store these in data attributes)
                // For now, clear the inputs
                $('#editCheckIn').val('');
                $('#editCheckOut').val('');

                $('#editAttendanceModal').modal('show');
            };

            // Handle attendance form submission
            $('#attendanceForm').on('submit', function(e) {
                e.preventDefault();

                const detailId = $('#editDetailId').val();
                const formData = {
                    check_in_time: $('#editCheckIn').val(),
                    check_out_time: $('#editCheckOut').val(),
                    notes: $('#editNotes').val()
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
                            $('#editAttendanceModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Update table row
                            updateTableRow(response.data, detailId);
                            updateSummaryBadges();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response?.message ||
                                'Terjadi kesalahan saat mengupdate kehadiran',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            function updateTableRow(detail, detailId = null) {
                const targetId = detailId || detail.id;
                const row = $(`tr[data-detail-id="${targetId}"]`);

                // Update actual time
                row.find('.actual-time').text(detail.actual_time);

                // Update working hours
                row.find('.working-hours').text(
                    detail.working_hours > 0 ? `${detail.working_hours} jam` : '-'
                );

                // Update status badge
                const statusBadge = row.find('.status-badge');
                statusBadge.removeClass().addClass(`badge bg-${detail.status_class} status-badge`);
                statusBadge.text(detail.attendance_status);

                // Update notes
                row.find('.notes-text').text(detail.notes || '-');

                // Add highlight effect
                row.addClass('table-success');
                setTimeout(() => {
                    row.removeClass('table-success');
                }, 2000);
            }

            function updateSummaryBadges() {
                // This would require an AJAX call to get updated statistics
                // For now, we'll reload the page section or implement a counter
                $.ajax({
                    url: '{{ route('jadwal.attendance.summary') }}',
                    data: {
                        start_date: '{{ $jadwal->start_date->format('Y-m-d') }}',
                        end_date: '{{ $jadwal->end_date->format('Y-m-d') }}',
                        user_id: '{{ $jadwal->user_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update summary badges based on response.data
                            updateSummaryBadgesHtml(response.data);
                        }
                    }
                });
            }

            function updateSummaryBadgesHtml(summaryData) {
                let badgesHtml = '';
                const statusMap = {
                    'present': {
                        class: 'success',
                        label: 'Hadir'
                    },
                    'absent': {
                        class: 'danger',
                        label: 'Tidak Hadir'
                    },
                    'late': {
                        class: 'warning',
                        label: 'Terlambat'
                    },
                    'early_leave': {
                        class: 'info',
                        label: 'Pulang Cepat'
                    },
                    'scheduled': {
                        class: 'secondary',
                        label: 'Terjadwal'
                    }
                };

                Object.keys(statusMap).forEach(status => {
                    const count = summaryData[status] || 0;
                    if (count > 0) {
                        const statusInfo = statusMap[status];
                        badgesHtml +=
                            `<span class="badge bg-${statusInfo.class} me-1">${statusInfo.label}: ${count}</span>`;
                    }
                });

                $('#summaryBadges').html(badgesHtml);
            }

            // Export functionality
            window.exportDetails = function() {
                $('#exportModal').modal('show');
            };

            window.processExport = function() {
                const format = $('input[name="format"]:checked').val();
                const includeStats = $('#includeStats').is(':checked');
                const includeNotes = $('#includeNotes').is(':checked');

                const exportData = {
                    start_date: '{{ $jadwal->start_date->format('Y-m-d') }}',
                    end_date: '{{ $jadwal->end_date->format('Y-m-d') }}',
                    user_id: '{{ $jadwal->user_id }}',
                    format: format,
                    include_stats: includeStats,
                    include_notes: includeNotes
                };

                // Create form and submit for download
                const form = $('<form>', {
                    method: 'GET',
                    action: '{{ route('jadwal.export') }}'
                });

                Object.keys(exportData).forEach(key => {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: key,
                        value: exportData[key]
                    }));
                });

                $('body').append(form);
                form.submit();
                form.remove();

                $('#exportModal').modal('hide');
            };

            // Initialize tooltips
            $('[title]').tooltip();

            // Auto-refresh every 5 minutes for real-time updates
            setInterval(function() {
                if (!bulkEditMode && !$('.modal').hasClass('show')) {
                    // Silently refresh summary badges
                    updateSummaryBadges();
                }
            }, 300000); // 5 minutes
        });
    </script>
@endpush
