@extends('inventory::layouts.master', ['title' => isset($master_barang) ? 'Update Barang' : 'Tambah Barang'])

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('inventory.master_barang.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <form
                action="{{ isset($master_barang) ? route('inventory.master_barang.update', $master_barang->id) : route('inventory.master_barang.store') }}"
                method="post">
                @csrf
                @if (isset($master_barang))
                    @method('PUT')
                @endif
                <div class="form-group">
                    <label for="kode_barang">Kode Barang</label>
                    <input type="text" name="kode_barang" id="kode_barang" class="form-control"
                        value="{{ isset($master_barang) ? $master_barang->kode_barang : old('kode_barang', isset($master_barang) ? $master_barang->kode_barang : '') }}">
                </div>
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control"
                        value="{{ isset($master_barang) ? $master_barang->nama_barang : old('nama_barang', isset($master_barang) ? $master_barang->nama_barang : '') }}">
                </div>
                <div class="form-group">
                    <label for="satuan_id">Satuan</label>
                    <select name="satuan_id" id="satuan_id" class="form-control">
                        @foreach ($satuan as $item)
                            <option value="{{ $item->id }}"
                                {{ isset($master_barang) && $master_barang->satuan_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select name="kategori_id" id="kategori_id" class="form-control">
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}"
                                {{ isset($master_barang) && $master_barang->kategori_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                @if (auth()->user()->hasRole('superadmin'))
                    <div class="form-group">
                        <label for="unit_pu">Unit Penanggung jawab</label>
                        <select name="unit_pu" id="unit_pu" class="form-control">
                            <option value="it">IT & PDE</option>
                            <option value="log">Logistik</option>
                            <option value="ipsrs">IPSRS</option>
                        </select>
                    </div>
                @endif
                <div class="form-group">
                    <label for="jenis">Jenis Barang</label>
                    <select name="jenis" id="jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis Barang --</option>
                        <option value="0"
                            {{ isset($master_barang) && $master_barang->jenis == '0' ? 'selected' : '' }}>
                            Habis Pakai</option>
                        <option value="1"
                            {{ isset($master_barang) && $master_barang->jenis == '1' ? 'selected' : '' }}>
                            Iventaris</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_elektronik">Apakah Elektronik ?</label>
                    <select name="is_elektronik" id="is_elektronik" class="form-control">
                        <option value="1"
                            {{ isset($master_barang) && $master_barang->is_elektronik == 1 ? 'selected' : '' }}>Ya</option>
                        <option value="0"
                            {{ isset($master_barang) && $master_barang->is_elektronik == 0 ? 'selected' : '' }}>Tidak
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="keterangan_barang">Keterangan</label>
                    <textarea name="keterangan_barang" id="keterangan_barang" class="form-control">{{ isset($master_barang) ? $master_barang->keterangan : old('keterangan_barang', isset($master_barang) ? $master_barang->keterangan : '') }}</textarea>
                </div>

                <div class="stokBlock mt-3">
                    <a href="javascript:void(0)" class="btn btn-info btn-sm mb-1 addRow" title="Tambah Stok">Tambah
                        Stok</a>
                    <table class="table table-bordered" id="tblstok" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $oldInputs = old('harga') ? count(old('harga')) : 0;
                                $items = isset($master_barang) ? $master_barang->stoks : [];
                                $count = max($oldInputs, count($items), 1);
                            @endphp
                            @for ($i = 0; $i < $count; $i++)
                                <tr>
                                    <td>
                                        <input type="number" class="form-control" name="harga[]"
                                            value="{{ old('harga.' . $i, isset($items[$i]) ? $items[$i]->harga : '') }}"
                                            required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="stok[]"
                                            value="{{ old('stok.' . $i, isset($items[$i]) ? $items[$i]->stok : '') }}"
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="keterangan[]"
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

                <button type="submit" class="btn btn-primary">{{ isset($master_barang) ? 'Update' : 'Simpan' }}</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.addRow').click(function() {
                var newRow = `
                    <tr>
                        <td>
                            <input type="number" class="form-control" name="harga[]" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" name="stok[]" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="keterangan[]">
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-danger removeRow"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                `;
                $('.table tbody').append(newRow);
            });

            $(document).on('click', '.removeRow', function() {
                if ($('.table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('Tidak bisa menghapus baris pertama');
                }
            });
        });
    </script>
@endpush
