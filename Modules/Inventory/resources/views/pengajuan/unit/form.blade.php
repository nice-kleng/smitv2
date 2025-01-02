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
                <form
                    action="{{ isset($pengajuan) && count($pengajuan) > 0 ? route('inventory.pengajuan.update', substr($pengajuan[0]->kode_pengajuan, 0, 11)) : route('inventory.pengajuan.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($pengajuan) && count($pengajuan) > 0)
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped text-nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Pilih Barang</th>
                                        <th style="width: 20%;">Jumlah</th>
                                        <th style="width: 20%;">Harga</th>
                                        <th style="width: 20%;">Keterangan</th>
                                        <th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $oldInputs = old('barang_id') ? count(old('barang_id')) : 0;
                                        $items = isset($pengajuan) && count($pengajuan) > 0 ? $pengajuan : [];
                                        $count = max($oldInputs, count($items), 1);
                                    @endphp

                                    @for ($i = 0; $i < $count; $i++)
                                        <tr>
                                            <td>
                                                <select name="barang_id[]" class="form-control select2">
                                                    <option value="">Pilih Barang</option>
                                                    @foreach ($masterBarang as $barang)
                                                        <option value="{{ $barang->id }}"
                                                            {{ old('barang_id.' . $i) == $barang->id || (isset($items[$i]) && $items[$i]->barang_id == $barang->id) ? 'selected' : '' }}>
                                                            {{ $barang->nama_barang }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah[]" class="form-control"
                                                    value="{{ old('jumlah.' . $i, isset($items[$i]) ? $items[$i]->jumlah : '') }}">
                                            </td>
                                            <td>
                                                <input type="number" name="harga[]" class="form-control"
                                                    value="{{ old('harga.' . $i, isset($items[$i]) ? $items[$i]->harga : '') }}">
                                            </td>
                                            <td>
                                                <input type="text" name="keterangan[]" class="form-control"
                                                    value="{{ old('keterangan.' . $i, isset($items[$i]) ? $items[$i]->keterangan : '') }}">
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-danger removeRow"><i
                                                        class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // // Initialize Select2 for existing elements
            // $('.select2').select2();

            $('.addRow').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <select name="barang_id[]" class="form-control select2">
                                <option value="">Pilih Barang</option>
                                @foreach ($masterBarang as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_barang }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="jumlah[]" class="form-control">
                        </td>
                        <td>
                            <input type="number" name="harga[]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="keterangan[]" class="form-control">
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                `;
                $('.table tbody').append(newRow);
                // Initialize Select2 for the newly added select element
                $('.table tbody tr:last').find('.select2').select2({
                    width: '100%',
                    theme: 'bootstrap4',
                });
            });

            $(document).on('click', '.removeRow', function() {
                if ($('.table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                }
            });
        });
    </script>
@endpush
