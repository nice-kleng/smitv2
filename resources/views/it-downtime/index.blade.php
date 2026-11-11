@extends('layouts.app', ['title' => 'Manajemen Downtime IT'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Manajemen Downtime IT</h4>
                    <a href="javascript:void(0)" class="btn btn-primary" id="btnCreate">Catat Downtime</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="downtimeTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Sistem/Layanan</th>
                                    <th>Kategori</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                    <th>PIC</th>
                                    <th>Pelapor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="downtimeModal" tabindex="-1" role="dialog" aria-labelledby="downtimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downtimeModalLabel">Catat Downtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="downtimeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sistem">Sistem/Layanan <span class="text-danger">*</span></label>
                                    <input type="text" name="sistem" id="sistem" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kategori">Kategori</label>
                                    <select name="kategori" id="kategori" class="form-control">
                                        <option value="">Pilih kategori</option>
                                        <option value="Aplikasi">Aplikasi</option>
                                        <option value="Jaringan">Jaringan</option>
                                        <option value="Server">Server</option>
                                        <option value="Database">Database</option>
                                        <option value="Perangkat">Perangkat</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mulai_at">Waktu Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="mulai_at" id="mulai_at" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selesai_at">Waktu Selesai</label>
                                    <input type="datetime-local" name="selesai_at" id="selesai_at" class="form-control">
                                    <small class="text-muted">Kosongkan jika masih berlangsung.</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pic">PIC</label>
                                    <input type="text" name="pic" id="pic" class="form-control" placeholder="Penanggung jawab penanganan">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dampak">Dampak</label>
                            <textarea name="dampak" id="dampak" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="penyebab">Penyebab</label>
                            <textarea name="penyebab" id="penyebab" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tindakan">Tindakan/Resolusi</label>
                            <textarea name="tindakan" id="tindakan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#downtimeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('it-downtime.index') }}",
                order: [[3, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'sistem', name: 'sistem' },
                    { data: 'kategori', name: 'kategori', defaultContent: '-' },
                    { data: 'mulai_at', name: 'mulai_at' },
                    { data: 'selesai_at', name: 'selesai_at', defaultContent: '-' },
                    { data: 'durasi', name: 'durasi', orderable: false, searchable: false },
                    { data: 'status_label', name: 'status', orderable: false, searchable: false },
                    { data: 'pic', name: 'pic', defaultContent: '-' },
                    { data: 'pelapor', name: 'pelapor', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            var formAction = "{{ route('it-downtime.store') }}";
            var formMethod = 'POST';

            function resetForm() {
                $('#downtimeForm')[0].reset();
                $('#downtimeForm').find('input[name="_method"]').remove();
                formAction = "{{ route('it-downtime.store') }}";
                formMethod = 'POST';
            }

            $('#btnCreate').on('click', function() {
                resetForm();
                $('#downtimeModalLabel').text('Catat Downtime');
                $('#downtimeModal').modal('show');
            });

            $('#downtimeModal').on('hidden.bs.modal', function() {
                resetForm();
            });

            $('#downtimeForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: formAction,
                    type: formMethod,
                    data: $(this).serialize(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#downtimeModal').modal('hide');
                            table.ajax.reload(null, false);
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: response.message || 'Terjadi kesalahan' });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Gagal', text: message });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('it-downtime.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        resetForm();
                        formAction = "{{ route('it-downtime.update', ':id') }}".replace(':id', id);
                        formMethod = 'POST';
                        $('#downtimeForm').append('<input type="hidden" name="_method" value="PUT">');
                        $('#sistem').val(data.sistem);
                        $('#kategori').val(data.kategori || '');
                        $('#mulai_at').val(data.mulai_at || '');
                        $('#selesai_at').val(data.selesai_at || '');
                        $('#pic').val(data.pic || '');
                        $('#dampak').val(data.dampak || '');
                        $('#penyebab').val(data.penyebab || '');
                        $('#tindakan').val(data.tindakan || '');
                        $('#keterangan').val(data.keterangan || '');
                        $('#downtimeModalLabel').text('Edit Downtime');
                        $('#downtimeModal').modal('show');
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data tidak dapat dimuat' });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Data downtime akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('it-downtime.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(response) {
                                if (response.success) {
                                    table.ajax.reload(null, false);
                                    Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                                }
                            },
                            error: function(xhr) {
                                var message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan';
                                Swal.fire({ icon: 'error', title: 'Gagal', text: message });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
