@extends('layouts.app', ['title' => 'Satuan'])

@section('button-header')
    <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#createSatuanModal">Tambah</a>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Satuan</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="satuanTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createSatuanModal" tabindex="-1" role="dialog" aria-labelledby="createSatuanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSatuanModalLabel">Tambah Satuan</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('master.satuan.store') }}" method="post" id="createSatuanForm">
                    @csrf
                    <div class="form-group">
                        <label for="nama_satuan">Nama Satuan</label>
                        <input type="text" name="nama_satuan" id="nama_satuan" class="form-control">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

<script>
    $(document).ready(function () {
        $('#satuanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('master.satuan.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_satuan', name: 'nama_satuan' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.btn-edit', function () {
            var id = $(this).data('id');
            $.ajax({
                type: "get",
                url: "{{ route('master.satuan.edit', ':id') }}".replace(':id', id),
                dataType: "json",
                success: function (response) {
                    $('#createSatuanForm').attr('action', "{{ route('master.satuan.update', ':id') }}".replace(':id', id));
                    $('#createSatuanForm').append('<input type="hidden" name="_method" value="PUT">');
                    $('#nama_satuan').val(response.data.nama_satuan);
                    $('#createSatuanModal').modal('show');
                },
                error: function (xhr, status, error) {
                    let message = 'Terjadi masalh di server';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    swal.fire({
                        title: 'Oops...',
                        text: message,
                        icon: 'error',
                        button: 'OK',
                    });
                }
            });
        });

        $('#createSatuanForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#createSatuanModal').modal('hide');
                    // $('#createSatuanForm')[0].reset();
                    $('#satuanTable').DataTable().ajax.reload();
                    swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil disimpan',
                        icon: 'success',
                        button: 'OK',
                    });
                },
                error: function (xhr, status, error) {
                    let message = 'Terjadi masalh di server';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    swal({
                        title: 'Oops...',
                        text: message,
                        icon: 'error',
                        button: 'OK',
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete', function () {
            swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Data akan dihapus secara permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = $(this).data('id');
                    $.ajax({
                        type: "delete",
                        url: "{{ route('master.satuan.destroy', ':id') }}".replace(':id', id),
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('#satuanTable').DataTable().ajax.reload();

                            swal.fire({
                                title: 'Berhasil!',
                                text: 'Data berhasil dihapus',
                                icon: 'success',
                                button: 'OK',
                            });
                        },
                        error: function (xhr, status, error) {
                            let message = 'Terjadi masalh di server';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            swal.fire({
                                title: 'Oops...',
                                text: message,
                                icon: 'error',
                                button: 'OK',
                            });
                        }
                    });
                }
            });
        });

        $('#createSatuanModal').on('hidden.bs.modal', function () {
            $('#createSatuanForm').attr('action', "{{ route('master.satuan.store') }}");
            $('#createSatuanForm').find('input[name="_method"]').remove();
            $('#createSatuanForm')[0].reset();
        });
    });
</script>

@endpush
