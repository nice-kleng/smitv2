@extends('layouts.app')

@section('title', 'Manajemen Jadwal')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Manajemen Jadwal Staff
                                </h4>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group" role="group">
                                    {{-- <button type="button" class="btn btn-primary" onclick="createSchedule()">
                                        <i class="fas fa-plus me-1"></i>
                                        Buat Jadwal
                                    </button> --}}
                                    <a href="{{ route('jadwal.create') }}" class="btn btn-primary"><i
                                            class="fas fa-plus"></i>Buat Jadwal</a>
                                    <button type="button" class="btn btn-info" onclick="showCalendarView()">
                                        <i class="fas fa-calendar me-1"></i>
                                        Calendar View
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportSchedule()">
                                        <i class="fas fa-download me-1"></i>
                                        Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card-body border-bottom">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter Tanggal</label>
                                <input type="text" class="form-control" id="dateRange"
                                    placeholder="Pilih rentang tanggal">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Staff</label>
                                <select class="form-control" id="filterStaff">
                                    <option value="">Semua Staff</option>
                                    @foreach ($staff ?? [] as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Shift</label>
                                <select class="form-control" id="filterShift">
                                    <option value="">Semua Shift</option>
                                    @foreach ($shifts ?? [] as $shift)
                                        <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-outline-primary" onclick="applyFilters()">
                                        <i class="fas fa-filter me-1"></i>
                                        Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="jadwalTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Staff</th>
                                        <th width="12%">Shift</th>
                                        <th width="20%">Periode</th>
                                        <th width="12%">Week Info</th>
                                        <th width="10%">Detail</th>
                                        <th width="10%">Status</th>
                                        <th width="16%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('jadwal.modals.create-modal')

    <!-- Calendar Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Calendar View - Jadwal Staff</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const jadwalTable = $('#jadwalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jadwal.index') }}",
                    data: function(d) {
                        d.date_range = $('#dateRange').val();
                        d.staff_id = $('#filterStaff').val();
                        d.status = $('#filterStatus').val();
                        d.shift_id = $('#filterShift').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'staff_name',
                        name: 'user.name'
                    },
                    {
                        data: 'shift_name',
                        name: 'shift.name'
                    },
                    {
                        data: 'period',
                        name: 'start_date'
                    },
                    {
                        data: 'week_info',
                        name: 'week_number',
                        orderable: false
                    },
                    {
                        data: 'details_count',
                        name: 'details_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [3, 'desc']
                ],
                language: {
                    processing: "Memuat data...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data yang tersedia"
                }
            });

            // Initialize date range picker
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "id"
            });

            // Apply filters
            window.applyFilters = function() {
                jadwalTable.ajax.reload();
            };

            // Functions for actions
            window.createSchedule = function() {
                $('#createModal').modal('show');
            };

            window.viewSchedule = function(id) {
                window.location.href = `/jadwal/${id}`;
            };

            window.viewScheduleDetails = function(id) {
                window.location.href = `/jadwal/${id}/details`;
            };

            window.editSchedule = function(id) {
                window.location.href = `/jadwal/${id}/edit`;
            };

            window.deleteSchedule = function(id) {
                Swal.fire({
                    title: 'Hapus Jadwal?',
                    text: "Data jadwal dan semua detail hariannya akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/jadwal/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    jadwalTable.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON;
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan',
                                    'error');
                            }
                        });
                    }
                });
            };

            window.showCalendarView = function() {
                $('#calendarModal').modal('show');

                // Initialize calendar after modal is shown
                $('#calendarModal').on('shown.bs.modal', function() {
                    if (!window.calendarInitialized) {
                        initializeCalendar();
                        window.calendarInitialized = true;
                    }
                });
            };

            window.exportSchedule = function() {
                $('#exportModal').modal('show');
            };

            function initializeCalendar() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    events: function(info, successCallback, failureCallback) {
                        // Debug log untuk melihat parameter
                        console.log('Calendar requesting data:', {
                            start: info.start.toISOString().split('T')[0],
                            end: info.end.toISOString().split('T')[0]
                        });

                        $.ajax({
                            url: '{{ route('jadwal.daily.schedule') }}',
                            method: 'GET',
                            data: {
                                start: info.start.toISOString().split('T')[0],
                                end: info.end.toISOString().split('T')[0]
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                if (response.success && response.data) {
                                    const events = response.data.map(detail => {
                                        const title =
                                            `${detail.staff_name} - ${detail.shift_name}`;
                                        const backgroundColor = getStatusColor(
                                            detail.attendance_status);

                                        return {
                                            title: title,
                                            start: detail.work_date,
                                            backgroundColor: backgroundColor,
                                            borderColor: backgroundColor,
                                            textColor: '#ffffff',
                                            extendedProps: {
                                                detail: detail
                                            }
                                        };
                                    });

                                    console.log('Calendar events:', events);
                                    successCallback(events);
                                } else {
                                    console.error('Invalid response format:', response);
                                    successCallback([]);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Calendar AJAX Error:', {
                                    status: xhr.status,
                                    statusText: xhr.statusText,
                                    responseText: xhr.responseText,
                                    error: error
                                });

                                // Show user-friendly error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error Loading Calendar',
                                    text: 'Gagal memuat data kalendar. Status: ' +
                                        xhr.status,
                                    footer: xhr.responseJSON?.message ||
                                        'Silakan coba lagi atau hubungi administrator.'
                                });

                                failureCallback(error);
                            }
                        });
                    },
                    eventClick: function(info) {
                        const detail = info.event.extendedProps.detail;

                        // Show detail in modal or alert
                        Swal.fire({
                            title: 'Detail Jadwal',
                            html: `
                                <div class="text-start">
                                    <p><strong>Staff:</strong> ${detail.staff_name}</p>
                                    <p><strong>Shift:</strong> ${detail.shift_name}</p>
                                    <p><strong>Tanggal:</strong> ${detail.work_date}</p>
                                    <p><strong>Jam Kerja:</strong> ${detail.shift_time}</p>
                                    <p><strong>Status:</strong> ${detail.attendance_status}</p>
                                    ${detail.notes ? `<p><strong>Catatan:</strong> ${detail.notes}</p>` : ''}
                                </div>
                            `,
                            width: '400px'
                        });
                    },
                    loading: function(bool) {
                        if (bool) {
                            console.log('Calendar loading...');
                        } else {
                            console.log('Calendar loaded');
                        }
                    }
                });

                calendar.render();

                // Store calendar instance globally for debugging
                window.debugCalendar = calendar;
            }

            function getStatusColor(status) {
                const colors = {
                    'scheduled': '#6c757d',
                    'present': '#198754',
                    'absent': '#dc3545',
                    'late': '#fd7e14',
                    'early_leave': '#0dcaf0',
                    'overtime': '#6f42c1'
                };
                return colors[status] || '#6c757d';
            }
        });
    </script>
@endpush
