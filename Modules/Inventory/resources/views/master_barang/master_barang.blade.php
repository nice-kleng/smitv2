@extends('inventory::layouts.master', ['title' => 'Master Barang'])

@section('content')
    <div class="card">
        <div class="card-header d-sm-flex justify-content-between">
            <a href="{{ route('inventory.master_barang.create') }}" class="btn btn-primary">Tambah Barang</a>
            <div>
                <a href="{{ route('inventory.master_barang.export') }}" class="btn btn-success" title="Export Data Barang"><i
                        class="fa fa-file-export"></i> Export</a>
                <a href="javascript:void(0)" class="btn btn-danger" data-toggle="modal" data-target="#importModal"
                    title="Import Data Barang"><i class="fa fa-file-import"></i> Import</a>
            </div>
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

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventory.master_barang.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih File</label>
                            <input type="file" name="file" id="file" class="form-control-file">
                            <small class="text-muted text-danger">File harus berformat .xls, .xlsx</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Import</button>
                    </div>
                </form>
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
