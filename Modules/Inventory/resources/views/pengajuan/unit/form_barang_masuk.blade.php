@extends('inventory::layouts.master', ['title' => 'Transaksi Barang Masuk'])

@section('button-header')
    <a href="{{ route('inventory.pengajuan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <form action="{{ route('inventory.pengajuan.proses-barang-datang') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap" id="tbltransaksi" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah Approve</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuans as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->barang->nama_barang }} </td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td>
                                                <input type="hidden" name="jumlah[]" value="{{ $item->jumlah }}">
                                                <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                <select name="stok_id[]" id="stok_id" class="form-control">
                                                    <option value="">-- Pilih Stok --</option>
                                                    @foreach ($item->barang->stoks as $stok)
                                                        <option value="{{ $stok->id }}">{{ $stok->stok }} |
                                                            @currency($stok->harga)</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tbltransaksi').DataTable({
                paging: false,
                autoWidth: false,
                responsive: true,
                scrollCollpse: true,
                scrollX: true,
                scrollY: 400,
            });
        });
    </script>
@endpush
