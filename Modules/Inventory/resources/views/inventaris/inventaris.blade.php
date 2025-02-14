@extends('inventory::layouts.master', ['title' => 'Data Inventaris'])

@section('content')
    <div class="card">
        <div class="card-header justify-content-between d-sm-flex">
            @if (auth()->user()->hasAnyRole(['superadmin', 'admin']))
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                    <i class="fa fa-file-import"></i> Import
                </button>
            @endif
            <a href="" class="btn btn-success" title="Export Data Inventaris"><i class="fa fa-file-excel"></i>
                Export</a>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#inventarisTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
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
                ]
            });
        });
    </script>
@endpush
