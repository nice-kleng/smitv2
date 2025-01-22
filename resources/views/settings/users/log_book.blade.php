@extends('layouts.app', ['title' => 'Log Book'])

@section('button-header')
    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#logbookModal">
        <i class="fas fa-plus"></i> Tambah Log Book
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap" style="width: 100%;" id="logbookTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kegiatan</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Aduan</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="logbookModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logbookModalLabel">Form Log Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="logbookForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kegiatan">Kegiatan</label>
                            <input type="hidden" name="log_book_id" id="log_book_id">
                            <textarea name="kegiatan" id="kegiatan" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">Detail Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Kode Ticket</label>
                                <p id="kd_ticket"></p>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Detail Aduan</label>
                                <p id="detail_aduan"></p>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Jenis Aduan</label>
                                <p id="jenis_aduan"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight: bold;">Tindak Lanjut</label>
                                <p id="tindak_lanjut"></p>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Status</label>
                                <p id="status"></p>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Keterangan Perbaikan</label>
                                <p id="keterangan_perbaikan"></p>
                            </div>
                            <div class="form-group">
                                <label style="font-weight: bold;">Tanggal Perbaikan</label>
                                <p id="tanggal_perbaikan"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#logbookTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('log-book.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'kegiatan',
                        name: 'kegiatan'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis'
                    },
                    {
                        data: 'aduan',
                        name: 'aduan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('submit', '#logbookForm', function(e) {
                e.preventDefault();

                let id = $('#log_book_id').val();
                let formData = $(this).serialize();
                let method = id ? 'PUT' : 'POST';
                let url = id ? "{{ route('log-book.update', ':id') }}".replace(':id', id) :
                    "{{ route('log-book.store') }}";
                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#logbookModal').modal('hide');
                        $('#logbookForm')[0].reset();
                        $('#logbookForm').attr('action', '');
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success',
                            timer: 3000
                        });
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Data gagal disimpan',
                            icon: 'error',
                            timer: 3000
                        });
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let url = "{{ route('log-book.edit', ':id') }}".replace(':id', id);
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('#logbookModal').modal('show');
                        $('#logbookForm')[0].reset();
                        $('#log_book_id').val(response.id);
                        $('#kegiatan').val(response.kegiatan);
                        $('#keterangan').val(response.keterangan);
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                // Add your delete logic here
            });

            // Handle show service button click
            $(document).on('click', '.show-service', function() {
                var id = $(this).data('id');
                let url = "{{ route('log-book.show', ':id') }}".replace(':id', id);
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        console.log('Response:', response); // Untuk debugging
                        if (response.service) {
                            $('#serviceModal').modal('show');
                            $('#kd_ticket').text(response.service.kd_ticket || '-');
                            $('#detail_aduan').text(response.service.detail_aduan || '-');
                            $('#jenis_aduan').text(response.service.jenis_aduan ? response
                                .service.jenis_aduan.nama_jenis : '-');
                            $('#tindak_lanjut').text(response.service.tindak_lanjut || '-');
                            $('#status').text(response.service.status == 1 ? 'Selesai' :
                                'Pending');
                            $('#keterangan_perbaikan').text(response.service
                                .keterangan_perbaikan || '-');
                            $('#tanggal_perbaikan').text(response.service.tanggal_perbaikan ||
                                '-');
                        } else {
                            Swal.fire({
                                title: 'Info',
                                text: 'Data service tidak ditemukan',
                                icon: 'info',
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr); // Untuk debugging
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal mengambil data service',
                            icon: 'error',
                            timer: 3000
                        });
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus',
                    text: 'Apakah anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('log-book.destroy', ':id') }}".replace(':id',
                                id),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: 'Gagal menghapus data',
                                    icon: 'error',
                                    timer: 3000
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
