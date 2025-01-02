@extends('layouts.app', ['title' => 'Ruangan'])

@section('button-header')
    <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#createRuanganModal">Tambah</a>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Ruangan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap" id="ruanganTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Unit</th>
                                <th>Kode Ruangan</th>
                                <th>Nama Ruangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createRuanganModal" tabindex="-1" role="dialog" aria-labelledby="createRuanganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRuanganModalLabel">Tambah Ruangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('master.ruangan.store') }}" method="POST" id="createRuanganForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="unit_id">Unit</label>
                        <select name="unit_id" id="unit_id" class="form-control">
                            <option value="">Pilih Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_ruangan">Kode Ruangan</label>
                        <input type="text" name="kode_ruangan" id="kode_ruangan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_ruangan">Nama Ruangan</label>
                        <input type="text" name="nama_ruangan" id="nama_ruangan" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#ruanganTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.ruangan.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'unit.nama_unit', name: 'unit.nama_unit' },
                    { data: 'kode_ruangan', name: 'kode_ruangan' },
                    { data: 'nama_ruangan', name: 'nama_ruangan' },
                    { data: 'action', name: 'action' }
                ]
            });

            $('#createRuanganForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createRuanganModal').modal('hide');
                        $('#ruanganTable').DataTable().ajax.reload();
                        Swal.fire({
                            title: 'Berhasil',
                            text: response.message,
                            icon: 'success'
                        });
                    }, error: function(xhr) {
                        let message = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Gagal',
                            text: message,
                            icon: 'error'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit',function () {
                var id = $(this).data('id');
                $.ajax({
                    type: "get",
                    url: "{{ route('master.ruangan.edit', ':id') }}".replace(':id', id),
                    dataType: "json",
                    success: function (response) {
                        $('#createRuanganModal').modal('show');
                        $('#createRuanganForm')[0].reset();
                        $('#createRuanganForm').append('<input type="hidden" name="_method" value="PUT">');
                        $('#createRuanganForm').attr('action', "{{ route('master.ruangan.update', ':id') }}".replace(':id', id));
                        $('#createRuanganForm #unit_id').val(response.data.unit_id);
                        $('#createRuanganForm #kode_ruangan').val(response.data.kode_ruangan);
                        $('#createRuanganForm #nama_ruangan').val(response.data.nama_ruangan);
                    }, error: function(xhr) {
                        let message = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Gagal',
                            text: message,
                            icon: 'error'
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan menghapus ruangan ini',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var id = $(this).data('id');
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('master.ruangan.destroy', ':id') }}".replace(':id', id),
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#ruanganTable').DataTable().ajax.reload();
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success'
                                });
                            }, error: function(xhr) {
                                let message = 'Terjadi kesalahan pada server';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Gagal',
                                    text: message,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });


            $('#createRuanganModal').on('show.bs.modal', function(e) {
                $('#createRuanganForm')[0].reset();
                $('#createRuanganForm').attr('action', "{{ route('master.ruangan.store') }}");
                $('#createRuanganForm').find('input[name="_method"]').remove();
            });
        });
    </script>
@endpush
