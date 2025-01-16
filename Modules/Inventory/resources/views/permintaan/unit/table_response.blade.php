<div class="table-responsive">
    <table class="table table-bordered text-nowrap" id="table-response">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Merk</th>
                <th>Type</th>
                <th>Seial Number</th>
                <th>Spesifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permintaan as $item)
                <tr>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>
                        <input type="hidden" name="barang_id[]" value="{{ $item->barang_id }}">
                        <input type="hidden" name="jumlah_approved[]" value="{{ $item->jumlah_approve }}">
                        <input type="hidden" name="harga_beli[]" value="{{ $item->transaksi->stok->harga }}">
                        <input type="text" name="merk[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="type[]" class="form-control">
                    </td>
                    <td>
                        <input type="text" name="serial_number[]" class="form-control">
                    </td>
                    <td>
                        <textarea name="spesifikasi[]" id="spesifikasi" class="form-control" rows="5"></textarea>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
