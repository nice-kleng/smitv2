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
                        <table class="table table-bordered table-hover text-nowrap" id="logbookTable">
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
                <form action="" method="POST">
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
                        name: 'aduan'
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

            $('form').on('submit', function(e) {
                e.preventDefault()

                let id = $('#id').val();
                let url = id ? `{{ route('log-book.update', ':log_book') }}`.replace(':log_book', id) :
                    `{{ route('log-book.store') }}`;
                let method = id ? 'PUT' : 'POST';
                let frmData = $(this).serialize();

                $.ajax({
                    type: method,
                    url: url,
                    data: frmData,
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                            });
                            $('#logbookModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
