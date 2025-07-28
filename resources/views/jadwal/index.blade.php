@extends('layouts.app', ['title' => 'Jadwal Kerja'])

@section('button-header')
    <a href="{{ route('jadwal.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Jadwal
    </a>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Jadwal Kerja</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" id="search-input" class="form-control float-right"
                                    placeholder="Pencarian...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select id="status-filter" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="month" id="month-filter" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <select id="shift-filter" class="form-control">
                                    <option value="">Semua Shift</option>
                                    <option value="Pagi">Pagi</option>
                                    <option value="Middle">Middle</option>
                                    <option value="Malam">Malam</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="reset-filter" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Reset Filter
                                </button>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table id="schedules-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Staff</th>
                                        <th>Shift</th>
                                        <th>Periode</th>
                                        <th>Minggu</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Schedule Details -->
    <div class="modal fade" id="scheduleDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Jadwal</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="schedule-detail-content">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Konfirmasi Hapus</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal ini?</p>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap4.min.css">
    <style>
        .btn-group .btn {
            margin-right: 2px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.875em;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#schedules-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jadwal.index') }}",
                    data: function(d) {
                        d.status = $('#status-filter').val();
                        d.month = $('#month-filter').val();
                        d.shift = $('#shift-filter').val();
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
                        name: 'week_number'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 25,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });

            // Filter functionality
            $('#status-filter, #month-filter, #shift-filter').change(function() {
                table.draw();
            });

            // Search functionality
            $('#search-input').keyup(function() {
                table.search(this.value).draw();
            });

            // Reset filters
            $('#reset-filter').click(function() {
                $('#status-filter, #month-filter, #shift-filter').val('');
                $('#search-input').val('');
                table.search('').draw();
            });

            // View schedule details
            window.viewSchedule = function(id) {
                $.ajax({
                    url: '/jadwal/' + id,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var schedule = response.data;
                            var content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Informasi Umum</h5>
                                <table class="table table-borderless">
                                    <tr><td><strong>Staff:</strong></td><td>${schedule.user.name}</td></tr>
                                    <tr><td><strong>Shift:</strong></td><td>${schedule.shift.name}</td></tr>
                                    <tr><td><strong>Periode:</strong></td><td>${schedule.start_date} - ${schedule.end_date}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${schedule.status === 'active' ? 'success' : 'secondary'}">${schedule.status}</span></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Detail Harian</h5>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <thead><tr><th>Tanggal</th><th>Hari</th><th>Status</th></tr></thead>
                                        <tbody>`;

                            if (schedule.schedule_details) {
                                schedule.schedule_details.forEach(function(detail) {
                                    content += `<tr>
                                <td>${detail.work_date}</td>
                                <td>${detail.day_name}</td>
                                <td>${detail.attendance_status || '-'}</td>
                            </tr>`;
                                });
                            }

                            content += `</tbody></table></div></div></div>`;

                            $('#schedule-detail-content').html(content);
                            $('#scheduleDetailModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('Gagal memuat detail jadwal');
                    }
                });
            };

            // Edit schedule
            window.editSchedule = function(id) {
                window.location.href = '/jadwal/' + id + '/edit';
            };

            // Delete schedule
            var deleteId = null;
            window.deleteSchedule = function(id) {
                deleteId = id;
                $('#deleteModal').modal('show');
            };

            $('#confirm-delete').click(function() {
                if (deleteId) {
                    $.ajax({
                        url: '/jadwal/' + deleteId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#deleteModal').modal('hide');
                            if (response.success) {
                                table.draw();
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            $('#deleteModal').modal('hide');
                            toastr.error('Gagal menghapus jadwal');
                        }
                    });
                }
            });
        });
    </script>
@endpush
