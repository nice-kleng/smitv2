@extends('inventory::layouts.master', ['title' => 'Data Laporan Pengaduan Unit'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap table-stripped" style="width: 100%;"
                            id="helpdesk-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Ticket</th>
                                    <th>Tanggal Pengaduan</th>
                                    <th>Unit</th>
                                    <th>Ruangan</th>
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

    <!-- Modal Detail-->
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
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tindakan-->
    <div class="modal fade" id="tindakanModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Form Tindakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="form-tindakan">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inventaris_id">Pilih Barang</label>
                                    <select name="inventaris_id" id="inventaris_id" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_aduan_id">Jenis Aduan</label>
                                    <select name="jenis_aduan_id" id="jenis_aduan_id" class="form-control">
                                        @foreach ($jenisaduan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tindak_lanjut">Tindak Lanjut</label>
                                    <textarea name="tindak_lanjut" id="tindak_lanjut" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="keterangan_perbaikan">Keterangan Perbaikan</label>
                                    <select name="keterangan_perbaikan" id="keterangan_perbaikan" class="form-control"
                                        required>
                                        <option value="0">-</option>
                                        <option value="1">Perbaikan Sendiri</option>
                                        <option value="2">Pemeliharaan</option>
                                        <option value="3">Perbaikan & Pemeliharaan</option>
                                        <option value="4">Service Luar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row service-container" style="display: none;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tempat_service">Tempat Service</label>
                                    <input type="text" name="tempat_service" id="tempat_service" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kerusakan">Kerusakan</label>
                                    <input type="text" name="kerusakan" id="kerusakan" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="biaya">Biaya</label>
                                    <input type="number" name="biaya" id="biaya" class="form-control">
                                </div>
                            </div>
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
            var table = $('#helpdesk-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('inventory.helpdesk.ticket.index') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'kd_ticket',
                        name: 'kd_ticket'
                    },
                    {
                        data: 'tanggal_pengaduan',
                        name: 'tanggal_pengaduan',
                    },
                    {
                        data: 'ruangan.unit.nama_unit',
                        name: 'ruangan.unit.nama_unit'
                    },
                    {
                        data: 'ruangan.nama_ruangan',
                        name: 'ruangan.nama_ruangan',
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('body').on('click', '.tindakan', function() {
                let id = $(this).data('id');
                $('#form-tindakan')[0].reset();

                $.ajax({
                    type: "get",
                    url: "{{ route('inventory.helpdesk.ticket.get-tindakan', ':id') }}".replace(
                        ':id', id),
                    dataType: "json",
                    success: function(response) {
                        $('#form-tindakan').attr('action',
                            `{{ route('inventory.helpdesk.ticket.tindakan', ':id') }}`
                            .replace(':id',
                                id));
                        $('#inventaris_id').empty();
                        $('#inventaris_id').append('<option value="">Pilih Barang</option>');

                        // Populate inventaris options and handle selected value
                        response.inventories.forEach(function(item) {
                            let selected = (item.id == response.ticket.inventaris_id) ?
                                'selected' : '';
                            $('#inventaris_id').append(
                                `<option value="${item.id}" ${selected}>${item.nama_barang}</option>`
                            );
                        });

                        if (response.ticket.inventaris_id) {
                            $('#inventaris_id').val(response.ticket.inventaris_id);
                        }
                        if (response.ticket.jenis_aduan_id) {
                            $('#jenis_aduan_id').val(response.ticket.jenis_aduan_id);
                        }
                        if (response.ticket.tindak_lanjut) {
                            $('#tindak_lanjut').val(response.ticket.tindak_lanjut);
                        }
                        if (response.ticket.keterangan_perbaikan) {
                            $('#keterangan_perbaikan').val(response.ticket
                                .keterangan_perbaikan);
                        }

                        $('#tindakanModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan dalam mengambil data');
                    }
                });
            });

            $('#keterangan_perbaikan').change(function(e) {
                e.preventDefault();
                let val = $(this).val();

                if (val == '4') {
                    $('.service-container').show();
                } else {
                    $('.service-container').hide();
                }
            });

            // $('#form-tindakan').on('submit', function(e) {
            //     e.preventDefault();
            //     $.ajax({
            //         url: $(this).attr('action'),
            //         method: 'PUT',
            //         data: $(this).serialize(),
            //         success: function(response) {
            //             $('#tindakanModal').modal('hide');
            //             table.ajax.reload();
            //             alert('Data berhasil disimpan');
            //         },
            //         error: function(xhr) {
            //             alert('Terjadi kesalahan dalam menyimpan data');
            //         }
            //     });
            // });
        });
    </script>
@endpush
