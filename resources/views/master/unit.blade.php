@extends('layouts.app', ['title' => 'Data Unit'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Unit</h4>
                    <div class="card-tools">
                        <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#createUnitModal">Tambah Unit</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="unitTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Unit</th>
                                    <th>Nama Unit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createUnitModal" tabindex="-1" role="dialog" aria-labelledby="createUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUnitModalLabel">Tambah Unit</h5>
                </div>
                <form action="{{ route('master.unit.store') }}" method="post" id="createUnitForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kode_unit">Kode Unit</label>
                            <input type="text" name="kode_unit" id="kode_unit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="nama_unit">Nama Unit</label>
                            <input type="text" name="nama_unit" id="nama_unit" class="form-control">
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
            $('#unitTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.unit.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'kode_unit', name: 'kode_unit' },
                    { data: 'nama_unit', name: 'nama_unit' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#createUnitForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#createUnitModal').modal('hide');
                            $('#unitTable').DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('master.unit.edit', ':id') }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#createUnitModal').modal('show');
                        $('#createUnitForm').attr('action', "{{ route('master.unit.update', ':id') }}".replace(':id', id));
                        $('#createUnitForm').append('<input type="hidden" name="_method" value="PUT">');
                        $('#kode_unit').val(response.kode_unit);
                        $('#nama_unit').val(response.nama_unit);
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Data akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('master.unit.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#unitTable').DataTable().ajax.reload();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr){
                                let message = 'Terjadi kesalahan pada server';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });

            $('#createUnitModal').on('hidden.bs.modal', function () {
                $('#createUnitForm')[0].reset();
                $('#createUnitForm').attr('action', "{{ route('master.unit.store') }}");
                $('#createUnitForm').find('input[name="_method"]').remove();
            });
        });
    </script>
@endpush

