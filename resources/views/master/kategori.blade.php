@extends('layouts.app', ['title' => 'Kategori Barang'])

@section('button-header')
    <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#createKategoriModal">Tambah</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Kategori</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="kategoriTable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createKategoriModal" tabindex="-1" role="dialog"
        aria-labelledby="createKategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createKategoriModalLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createKategoriForm" action="{{ route('master.kategoriBarang.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama_kategori">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            $('#createKategoriModal').on('shown.bs.modal', function() {
                $('#nama_kategori').focus();
            });

            $('#kategoriTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.kategoriBarang.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nama_kategori',
                        name: 'nama_kategori'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "get",
                    url: "{{ route('master.kategoriBarang.edit', ':id') }}".replace(':id', id),
                    dataType: "json",
                    success: function(response) {
                        $('#createKategoriModal').modal('show');
                        $('#createKategoriForm').attr('action',
                            "{{ route('master.kategoriBarang.update', ':id') }}".replace(
                                ':id', id));
                        $('#createKategoriForm').append(
                            '<input type="hidden" name="_method" value="put">');
                        $('#createKategoriForm').find('input[name="nama_kategori"]').val(
                            response.data.nama_kategori);
                    },
                    error: function(xhr, status, error) {
                        let message = 'Terjadi masalah di server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: message
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete', function() {
                swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan menghapus kategori ini',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = $(this).data('id');
                        let url = "{{ route('master.kategoriBarang.destroy', ':id') }}".replace(
                            ':id', id);
                        $.ajax({
                            type: "delete",
                            url: url,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                $('#kategoriTable').DataTable().ajax.reload();
                                swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Kategori berhasil dihapus'
                                });
                            },
                            error: function(xhr, status, error) {
                                let message = 'Terjadi masalah di server';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: message
                                });
                            }
                        });
                    }
                });
            });

            $('#createKategoriModal').on('hidden.bs.modal', function() {
                $('#createKategoriForm').attr('action', "{{ route('master.kategoriBarang.store') }}");
                $('#createKategoriForm').find('input[name="_method"]').remove();
                $('#createKategoriForm')[0].reset();
            });
        });
    </script>
@endpush
