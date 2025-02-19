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
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap" id="inventarisTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Inventaris</th>
                            <th>Nama Barang</th>
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
                ajax: "{{ route('inventory.index') }}",
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

                    url += '?ids=' + ids.join(',');
                }

                window.open(url, '_blank');
                $('#cetakLabelModal').modal('hide');
            });
        });
    </script>
@endpush
