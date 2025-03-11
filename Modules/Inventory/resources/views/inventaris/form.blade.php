@extends('layouts.app', ['title' => isset($inventaris) ? 'Edit Inventaris' : 'Tambah Inventaris'])

@section('content')
    <div class="row">
        <div class="card-md-12">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ isset($inventaris) ? route('inventory.update', $inventaris->id) : route('inventory.store') }}"
                        method="post">
                        @csrf
                        @if (isset($inventaris))
                            @method('PUT')
                        @endif
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="kode_barang">Kode Inventaris</label>
                                <input type="text" class="form-control" name="kode_barang" id="kode_barang"
                                    value="{{ $inventaris->kode_barang ?? old('kode_barang') }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nama_alias">Nama Alias</label>
                                <input type="text" class="form-control" name="nama_alias" id="nama_alias"
                                    value="{{ $inventaris->nama_alias ?? old('nama_alias') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="no_barang">Nomor Barang</label>
                                <input type="text" name="no_barang" id="no_barang" class="form-control"
                                    value="{{ $inventaris->no_barang ?? old('no_barang') }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="barang_id">Barang</label>
                                <select name="barang_id" id="barang_id" class="form-control select2" required>
                                    <option value="">-- Master Barang --</option>
                                    @foreach ($barangs as $item)
                                        <option value="{{ $item->id }}"
                                            {{ isset($inventaris) && $inventaris->barang_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="ruangan_id">Ruangan</label>
                                <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach ($ruangans as $item)
                                        <option value="{{ $item->id }}"
                                            {{ isset($inventaris) && $inventaris->ruangan_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->unit->nama_unit }} - {{ $item->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="harga_beli">Harga Beli</label>
                                <input type="number" name="harga_beli" id="harga_beli" class="form-control"
                                    value="{{ $inventaris->harga_beli ?? old('harga_beli') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="satuan">Satuan</label>
                                <input type="text" name="satuan" id="satuan" class="form-control"
                                    value="{{ $inventaris->satuan ?? old('satuan') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="merk">Merk</label>
                                <input type="text" name="merk" id="merk" class="form-control"
                                    value="{{ $inventaris->merk ?? old('merk') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Type</label>
                                <input type="text" name="type" id="type" class="form-control"
                                    value="{{ $inventaris->type ?? old('type') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control"
                                    value="{{ $inventaris->serial_number ?? old('serial_number') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="kepemilikan">Kepemilikan</label>
                                <input type="text" name="kepemilikan" id="kepemilikan" class="form-control"
                                    value="{{ $inventaris->kepemilikan ?? old('kepemilikan') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tahun_pengadaan">Tahun Pengadaan</label>
                                <select name="tahun_pengadaan" id="tahun_pengadaan" class="form-control" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($year = date('Y'); $year >= 2000; $year--)
                                        <option value="{{ $year }}"
                                            {{ isset($inventaris) && $inventaris->tahun_pengadaan == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="2"
                                        {{ isset($inventaris) && $inventaris->status == 2 ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="1"
                                        {{ isset($inventaris) && $inventaris->status == 1 ? 'selected' : '' }}>Perlu
                                        Dihapuskan</option>
                                    <option value="0"
                                        {{ isset($inventaris) && $inventaris->status == 0 ? 'selected' : '' }}>Telah
                                        Dihapuskan</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="spesifikasi">Spesifikasi</label>
                                <textarea name="spesifikasi" id="spesifikasi" rows="3" class="form-control">{{ $inventaris->spesifikasi ?? old('spesifikasi') }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="catatan">Catatan</label>
                                <textarea name="catatan" id="catatan" rows="3" class="form-control">{{ $inventaris->catatan ?? old('catatan') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($inventaris) ? 'Update' : 'Simpan' }}</button>
                                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
