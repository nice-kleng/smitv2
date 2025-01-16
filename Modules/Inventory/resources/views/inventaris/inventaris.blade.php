@extends('inventory::layouts.master', ['title' => 'Data Inventaris'])

@section('content')
    <div class="card">
        <div class="card-header justify-content-between d-sm-flex">
            <a href="" class="btn btn-primary">Tambah Inventaris</a>
            <a href="" class="btn btn-success" title="Export Data Inventaris"><i class="fa fa-file-excel"></i> Export</a>
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
