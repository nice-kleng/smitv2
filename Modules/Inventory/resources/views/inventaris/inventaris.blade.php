@extends('inventory::layouts.master', ['title' => 'Data Inventaris'])

@section('content')
    <div class="card">
        <div class="card-header justify-content-between d-sm-flex">
            <a href="" class="btn btn-success" title="Export Data Inventaris"><i class="fa fa-file-excel"></i>
                Export</a>
            @if (auth()->user()->hasAnyRole(['superadmin', 'admin']))
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                    <i class="fa fa-file-import"></i> Import
                </button>
                <button type="button" class="btn btn-info" id="btnCetakLabel">
                    <i class="fa fa-print"></i> Cetak Label
                </button>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary" title="Input Inventaris"><i
                        class="fas fa-plus"></i> Add
                    Inventaris</a>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filterUnit">Filter Unit</label>
                    <select class="form-control" id="filterUnit">
                        <option value="">Pilih Unit</option>
                        @foreach (\App\Models\Unit::orderBy('nama_unit')->get() as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterRuangan">Filter Ruangan</label>
                    <select class="form-control" id="filterRuangan" disabled>
                        <option value="">Pilih Unit Terlebih Dahulu</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filterKondisi">Filter Kondisi</label>
                    <select class="form-control" id="filterKondisi">
                        <option value="">Semua Kondisi</option>
                        <option value="2">Baik</option>
                        <option value="1">Kurang Baik</option>
                        <option value="0">Rusak</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap" id="inventarisTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Inventaris</th>
                            <th>Nama Alias</th>
                            <th>Nama Barang/Jenis Barang</th>
                            <th>Merk</th>
                            <th>Type</th>
                            <th>Nomor Seri</th>
                            <th>Kategori Barang</th>
                            <th>Tahun Pegadaan</th>
                            <th>Unit</th>
                            <th>Ruangan</th>
                            <th>Harga Beli</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Kepemilikan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Inventaris</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File Excel</label>
                            <input type="file" class="form-control" name="file" required accept=".xlsx,.xls">
                        </div>
                        <small class="text-muted">Download template excel <a
                                href="{{ route('inventory.template') }}">disini</a></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cetakLabelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Label Inventaris</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Metode Cetak</label>
                        <select class="form-control" id="metodeCetak">
                            <option value="all">Semua Item</option>
                            <option value="selected">Item Terpilih</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipeCetak" id="tipeThermal"
                                value="thermal" checked>
                            <label class="form-check-label" for="tipeThermal">
                                Thermal
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipeCetak" id="tipePreview"
                                value="preview">
                            <label class="form-check-label" for="tipePreview">
                                Preview
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnProsesLabel">Cetak</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedItems = [];


            var table = $('#inventarisTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('inventory.index') }}",
                    data: function(d) {
                        d.unit_id = $('#filterUnit').val();
                        d.ruangan_id = $('#filterRuangan').val();
                        d.kondisi = $('#filterKondisi').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        checkboxes: {
                            selectRow: true
                        }
                    },
                    {
                        data: 'kode_barang',
                        name: 'kode_barang'
                    },
                    {
                        data: 'nama_alias',
                        name: 'nama_alias'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'merk',
                        name: 'merk'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'nomor_seri',
                        name: 'nomor_seri'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'tahun_pengadaan',
                        name: 'tahun_pengadaan'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'ruangan',
                        name: 'ruangan'
                    },
                    {
                        data: 'harga_beli',
                        name: 'harga_beli'
                    },
                    {
                        data: 'kondisi',
                        name: 'kondisi'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'kepemilikan',
                        name: 'kepemilikan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 0,
                    checkboxes: {
                        selectRow: true
                    }
                }],
                select: {
                    style: 'multi'
                },
                order: [
                    [1, 'asc']
                ],
            });

            // Unit change: load ruangans
            $('#filterUnit').change(function() {
                var unitId = $(this).val();
                if (unitId) {
                    $('#filterRuangan').prop('disabled', false);
                    $.get('/api/master/unit/' + unitId + '/ruangan', function(data) {
                        var options = '<option value="">Semua Ruangan</option>';
                        $.each(data, function(i, ruangan) {
                            options += '<option value="' + ruangan.id + '">' + ruangan
                                .nama_ruangan + '</option>';
                        });
                        $('#filterRuangan').html(options);
                    });
                } else {
                    $('#filterRuangan').html('<option value="">Pilih Unit Terlebih Dahulu</option>');
                    $('#filterRuangan').prop('disabled', true);
                }
                table.ajax.reload();
            });

            // Ruangan/kondisi change: reload table
            $('#filterRuangan, #filterKondisi').change(function() {
                table.ajax.reload();
            });

            $('#btnCetakLabel').click(function() {
                $('#cetakLabelModal').modal('show');
            });

            $('#btnProsesLabel').click(function() {
                let metode = $('#metodeCetak').val();
                let url = "{{ route('inventory.cetak-label') }}";

                if (metode === 'selected') {
                    let rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length === 0) {
                        alert('Pilih minimal satu item untuk dicetak');
                        return;
                    }

                    // Konversi ke array
                    let ids = [];
                    $.each(rows_selected, function(index, rowId) {
                        ids.push(rowId);
                    });

                    url += '?ids=' + ids.join(',') + '&tipe=' + $('input[name="tipeCetak"]:checked').val();
                }

                window.open(url, '_blank');
                $('#cetakLabelModal').modal('hide');
            });
        });
    </script>
@endpush
