@extends('inventory::layouts.master', ['title' => isset($pengajuan) && count($pengajuan) > 0 ? 'Update Pengajuan' : 'Buat Pengajuan'])

@section('button-header')
    <a href="{{ route('inventory.pengajuan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <span>silahkan cek kembali inputan anda</span>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <a href="javascript:void(0)" class="btn btn-success" data-toggle="modal" data-target="#modalImport"
                        title="Import Pengajuan"> <i class="fas fa-file-excel"></i>
                        Import</a>
                </div>
                <form
                    action="{{ isset($pengajuan) && count($pengajuan) > 0 ? route('inventory.pengajuan.update', substr($pengajuan[0]->kode_pengajuan, 0, 11)) : route('inventory.pengajuan.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($pengajuan) && count($pengajuan) > 0)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="formTable" class="table table-bordered table-stripped text-nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Pilih Barang</th>
                                        <th style="width: 20%;">Jumlah</th>
                                        <th style="width: 20%;">Harga</th>
                                        <th style="width: 20%;">Keterangan</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="javascript:void(0)" class="btn btn-info btn-sm addRow">Tambah Barang</a>
                        <button type="submit"
                            class="btn btn-{{ isset($pengajuan) ? 'warning' : 'primary' }} float-right">{{ isset($pengajuan) && count($pengajuan) > 0 ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalImport" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Import Data Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('inventory.pengajuan.import') }}" method="POST" enctype="multipart/form-data"
                    id="importForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Format header: id_barang, jumlah, harga, keterangan</small>
                        </div>
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
            // Initialize DataTable
            var table = $('#formTable').DataTable({
                processing: true,
                pageLength: 10,
                data: [],
                columns: [{
                        data: 'barang_id',
                        render: function(data, type, row) {
                            var select = `<select name="barang_id[]" class="form-control select2">
                                <option value="">Pilih Barang</option>
                                @foreach ($masterBarang as $item)
                                    <option value="{{ $item->id }}" ${data == {{ $item->id }} ? 'selected' : ''}>
                                        {{ $item->nama_barang }}
                                    </option>
                                @endforeach
                            </select>`;
                            return select;
                        }
                    },
                    {
                        data: 'jumlah',
                        render: function(data) {
                            return `<input type="number" name="jumlah[]" class="form-control" value="${data || ''}">`;
                        }
                    },
                    {
                        data: 'harga',
                        render: function(data) {
                            return `<input type="number" name="harga[]" class="form-control" value="${data || ''}">`;
                        }
                    },
                    {
                        data: 'keterangan',
                        render: function(data) {
                            return `<input type="text" name="keterangan[]" class="form-control" value="${data || ''}">`;
                        }
                    },
                    {
                        data: null,
                        render: function() {
                            return `<a href="javascript:void(0)" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></a>`;
                        }
                    }
                ],
                drawCallback: function() {
                    // Reinitialize Select2 after draw
                    $('.select2').select2({
                        width: '100%',
                        theme: 'bootstrap4',
                    });
                },
                // Add these options to maintain pagination position
                pageResetOnReload: false,
                stateSave: true,
                stateDuration: -1 // -1 for session storage
            });

            // Modified Add row functionality
            $('.addRow').click(function() {
                var currentPage = table.page();
                var newRowData = {
                    barang_id: '',
                    jumlah: '',
                    harga: '',
                    keterangan: ''
                };

                // Add row and maintain current page
                table.row.add(newRowData).draw(false);

                // Go to last page if the new row isn't visible
                if (table.page.info().pages === currentPage + 1) {
                    table.page('last').draw(false);
                }
            });

            // Remove row functionality
            $('#formTable tbody').on('click', '.removeRow', function() {
                if (table.data().count() > 1) {
                    var currentPage = table.page();
                    table.row($(this).closest('tr')).remove().draw();
                    if (table.page.info().pages === currentPage + 1) {
                        table.page('last').draw(false);
                    }
                }
            });

            // Handle import form submission
            $('#importForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#modalImport').modal('hide');

                        // Clear existing data and add imported data
                        table.clear();
                        table.rows.add(response.data.map(function(item) {
                            return {
                                barang_id: item.id_barang,
                                jumlah: item.jumlah,
                                harga: item.harga,
                                keterangan: item.keterangan
                            };
                        })).draw();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush
