@extends('inventory::layouts.master', ['title' => isset($permintaan) && count($permintaan) > 0 ? 'Update Permintaan' : 'Buat Permintaan'])

@section('button-header')
    <a href="{{ route('inventory.permintaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
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
                    {{ isset($permintaan) && count($permintaan) > 0 ? 'Update Permintaan' : 'Buat Permintaan' }}
                </div>
                <form
                    action="{{ isset($permintaan) && count($permintaan) > 0 ? route('inventory.permintaan.update', substr($permintaan[0]->kode_permintaan, 0, 16)) : route('inventory.permintaan.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($permintaan) && count($permintaan) > 0)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="table-responsive">
                            <a href="javascript:void(0)" class="btn btn-info btn-sm addRow" title="Tambah Barang">
                                <i class="fas fa-plus"></i>Tambah Barang
                            </a>
                            <table class="table table-header text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Keperluan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldInputs = old('barang_id') ? count(old('barang_id')) : 0;
                                        $items = isset($permintaan) && count($permintaan) > 0 ? $permintaan : [];
                                        $count = max($oldInputs, count($items), 1);
                                    @endphp
                                    @for ($i = 0; $i < $count; $i++)
                                        <tr>
                                            <td>
                                                <select name="barang_id[]" id="barang_id" class="form-control select2"
                                                    required>
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach ($barangs as $barang)
                                                        <option value="{{ $barang->id }}"
                                                            {{ old('barang_id.' . $i) == $barang->id || (isset($items[$i]) && $items[$i]->barang_id == $barang->id) ? 'selected' : '' }}>
                                                            {{ $barang->nama_barang }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah[]" id="jumlah" class="form-control"
                                                    value="{{ old('jumlah.' . $i, isset($items[$i]) ? $items[$i]->jumlah : '') }}"
                                                    required>
                                            </td>
                                            <td>
                                                <input type="text" name="keperluan[]" id="keperluan" class="form-control"
                                                    value="{{ old('keperluan.' . $i, isset($items[$i]) ? $items[$i]->keperluan : '') }}">
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm"
                                                    title="Hapus Barang">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($permintaan) && count($permintaan) > 0 ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.addRow').on('click', function() {
                addRow();
            });

            function addRow() {
                var tr = '<tr>' +
                    '<td>' +
                    '<select name="barang_id[]" id="barang_id" class="form-control select2" required>' +
                    '<option value="">-- Pilih Barang --</option>' +
                    '@foreach ($barangs as $barang)' +
                    '<option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</td>' +
                    '<td>' +
                    '<input type="number" name="jumlah[]" id="jumlah" class="form-control" required>' +
                    '</td>' +
                    '<td>' +
                    '<input type="text" name="keperluan[]" id="keperluan" class="form-control">' +
                    '</td>' +
                    '<td>' +
                    '<a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Hapus Barang">' +
                    '<i class="fas fa-trash"></i>' +
                    '</a>' +
                    '</td>' +
                    '</tr>';
                $('.table tbody').append(tr);
                $('table tbody tr:last').find('select2').select2({
                    width: '100%',
                });
            }

            $('tbody').on('click', '.btn-danger', function() {
                if ($('.table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        });
    </script>
@endpush
