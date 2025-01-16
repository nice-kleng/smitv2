@extends('inventory::layouts.master', ['title' => 'Jenis Aduan'])

@section('button-header')
    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop"
        title="Tambah Data"><i class="fas fa-plus"></i> Tambah Data</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped" style="width: 100%;" id="jenisTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Aduan</th>
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
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="form-jenis">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_jenis">Nama Jenis Pengaduan</label>
                            <input type="hidden" name="id" id="id">
                            <input type="text" class="form-control" name="nama_jenis" id="nama_jenis" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary submit-btn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var table = $('#jenisTable').DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            ajax: "{{ route('inventory.helpdesk.jenis-aduan.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nama_jenis',
                    name: 'nama_jenis'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let id = $('#id').val();
                let url = id ? `{{ route('inventory.helpdesk.jenis-aduan.update', ':id') }}`.replace(':id',
                        id) :
                    `{{ route('inventory.helpdesk.jenis-aduan.store') }}`;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    type: method,
                    url: url,
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        table.ajax.reload();
                        $('#staticBackdrop').modal('hide');
                        $('#form-jenis').trigger('reset');
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Ok'
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        if (errors) {
                            if (errors.nama_jenis) {
                                toastr.error(errors.nama_jenis);
                            }
                        }
                    }
                });
            });

            $(document).on('click', '.edit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: `{{ route('inventory.helpdesk.jenis-aduan.edit', ':id') }}`.replace(':id',
                        id),
                    dataType: 'json',
                    success: function(response) {
                        $('#staticBackdrop').modal('show');
                        $('#id').val(response.data.id);
                        $('#nama_jenis').val(response.data.nama_jenis);
                    }
                });
            });

            $(document).on('click', '.delete', function() {
                let id = $(this).data('id');
                if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                    $.ajax({
                        type: 'DELETE',
                        url: `{{ route('inventory.helpdesk.jenis-aduan.destroy', ':id') }}`.replace(
                            ':id',
                            id),
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': `{{ csrf_token() }}`
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            });
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
