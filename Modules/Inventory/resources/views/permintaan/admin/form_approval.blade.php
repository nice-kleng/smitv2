@extends('inventory::layouts.master', ['title' => isset($permintaan) && count($permintaan) > 0 ? 'Update Permintaan' : 'Buat Permintaan'])

@section('button-header')
    <a href="{{ route('inventory.permintaan.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Approval</h4>
                </div>
                <form action="{{ route('inventory.permintaan.approve.proses') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th>Jumlah Diminta</th>
                                        <th>Stok</th>
                                        <th>Jumlah Disetujui</th>
                                        <th>Keterangan (Opsional)</th>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" checked>
                                                <label class="form-check-label" for="checkAll">
                                                    Pilih Semua
                                                </label>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permintaan as $key => $item)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="barang_id[]" value="{{ $item->barang_id }}">
                                                <input type="hidden" name="permintaan_id[]" value="{{ $item->id }}">
                                                {{ $item->barang->nama_barang }}
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="jumlah[]"
                                                    value="{{ $item->jumlah }}" readonly>
                                            </td>
                                            <td>
                                                <select name="stok_id[]" id="stok_id_{{ $key }}"
                                                    class="form-control">
                                                    <option value="">-- Pilih Stok --</option>
                                                    @foreach ($item->barang->stoks as $stok)
                                                        <option value="{{ $stok->id }}">
                                                            {{ $stok->stok . '|' . $stok->harga }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah_approve[]"
                                                    id="jumlah_approve_{{ $key }}" class="form-control"
                                                    value="{{ $item->jumlah }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="keterangan[]"
                                                    id="keterangan_{{ $key }}" class="form-control">
                                            </td>
                                            <td>
                                                <div class="form-check text-center">
                                                    <input class="form-check-input position-static item-checkbox"
                                                        type="checkbox" name="approved_items[]" value="{{ $key }}"
                                                        checked>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"
                            onclick="return confirm('Apakah Anda Yakin?')">Approve</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#checkAll').on('change', function() {
                $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@endpush
