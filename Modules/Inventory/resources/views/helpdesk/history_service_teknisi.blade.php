@extends('inventory::layouts.master', ['title' => 'Riwayat Service Teknisi'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Add filter form -->
                    <form id="filter-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Awal</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Jenis Aduan</label>
                                    <select class="form-control" id="jenis_aduan" name="jenis_aduan">
                                        <option value="">Semua</option>
                                        @foreach ($jenisAduan as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ruangan</label>
                                    <select class="form-control" id="ruangan" name="ruangan">
                                        <option value="">Semua</option>
                                        @foreach ($ruangans as $ruangan)
                                            <option value="{{ $ruangan->id }}">
                                                {{ $ruangan->unit->nama_unit }}-{{ $ruangan->nama_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" id="filter-btn">Filter</button>
                        <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                        <button type="button" class="btn btn-success" id="export-btn">Export Excel</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped text-nowrap" id="serviceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Ticket</th>
                                    <th>Tanggal Pengaduan</th>
                                    <th>Unit</th>
                                    <th>Ruangan</th>
                                    <th>Kode Inventaris</th>
                                    <th>Nama Barang</th>
                                    <th>Status</th>
                                    <th>Keterangan Perbaikan</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Enable/disable end date based on start date
            $('#start_date').change(function() {
                $('#end_date').prop('disabled', !$(this).val());
                if (!$(this).val()) {
                    $('#end_date').val('');
                }
            });

            var table = $('#serviceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory.helpdesk.ticket.riwayat-service-teknisi') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.jenis_aduan = $('#jenis_aduan').val();
                        d.ruangan = $('#ruangan').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kd_ticket',
                        name: 'kd_ticket'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'ruangan.unit.nama_unit',
                        name: 'ruangan.unit.nama_unit'
                    },
                    {
                        data: 'ruangan.nama_ruangan',
                        name: 'ruangan.nama_ruangan'
                    },
                    {
                        data: function(row) {
                            return row.inventaris ? row.inventaris.kode_barang : '-';
                        },
                        name: 'inventaris.kode_barang'
                    },
                    {
                        data: function(row) {
                            return row.inventaris ? row.inventaris.barang.nama_barang : '-';
                        },
                        name: 'inventaris.barang.nama_barang'
                    },
                    {
                        data: function(row) {
                            return row.status == 1 ?
                                '<span class="badge badge-success">Selesai</span>' :
                                '<span class="badge badge-warning">Pending</span>';
                        },
                        name: 'status'
                    },
                    {
                        data: 'keterangan_perbaikan',
                        name: 'keterangan_perbaikan'
                    },
                    {
                        data: function(row) {
                            return row.tanggal_perbaikan ? row.tanggal_perbaikan : '-';
                        },
                        name: 'tanggal_perbaikan'
                    }
                ],
                columnDefs: [{
                    targets: [7], // status column
                    render: function(data) {
                        return data;
                    },
                    escapeHtml: false
                }]
            });

            // Filter button click handler
            $('#filter-btn').click(function() {
                table.draw();
            });

            // Reset button click handler
            $('#reset-btn').click(function() {
                $('#filter-form')[0].reset();
                $('#end_date').prop('disabled', true);
                table.draw();
            });

            // Export button click handler
            $('#export-btn').click(function() {
                let url = "{{ route('inventory.helpdesk.ticket.epxort-service') }}";
                let params = {
                    start_date: $('#start_date').val(),
                    end_date: $('#end_date').val(),
                    jenis_aduan: $('#jenis_aduan').val(),
                    ruangan: $('#ruangan').val()
                };

                url += '?' + $.param(params);
                window.location.href = url;
            });
        });
    </script>
@endpush
