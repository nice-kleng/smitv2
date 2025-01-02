@extends('inventory::layouts.master', ['title' => 'Master Barang'])

@section('content')
    <div class="card">
        <div class="card-header justify-content-between d-sm-flex">
            <a href="{{ route('inventory.master_barang.create') }}" class="btn btn-primary">Tambah Barang</a>
            <a href="{{ route('inventory.master_barang.export') }}" class="btn btn-success" title="Export Data Barang"><i
                    class="fa fa-file-excel"></i> Export</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-nowrap" id="barangTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#barangTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory.master_barang.index') }}",
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, {
                    data: 'kode_barang',
                    name: 'kode_barang'
                }, {
                    data: 'nama_barang',
                    name: 'nama_barang'
                }, {
                    data: 'satuan.nama_satuan',
                    name: 'satuan'
                }, {
                    data: 'kategori.nama_kategori',
                    name: 'kategori'
                }, {
                    data: 'keterangan',
                    name: 'keterangan'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }]
            });
        });
    </script>
@endpush
