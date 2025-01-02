@php
    use SimpleSoftwareIO\QrCode\Facades\QRCode;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 50px;
            vertical-align: middle;
        }

        .header h1 {
            font-size: 18px;
            margin: 5px 0;
        }

        .header p {
            font-size: 14px;
            margin: 5px 0;
        }

        .form-info {
            margin: 15px 0;
        }

        .form-info div {
            margin: 5px 0;
        }

        .form-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            white-space: nowrap;
        }

        .col-no {
            width: auto;
            min-width: 30px;
            white-space: nowrap;
        }

        .col-nama {
            width: auto;
            min-width: 200px;
        }

        .col-status {
            width: auto;
            min-width: 80px;
        }

        .col-keterangan {
            width: auto;
            min-width: 150px;
        }

        .col-jumlah {
            width: auto;
            min-width: 80px;
            white-space: nowrap;
        }

        .col-harga {
            width: auto;
            min-width: 100px;
            white-space: nowrap;
        }

        .col-total {
            width: auto;
            min-width: 100px;
            white-space: nowrap;
        }

        .total-row {
            font-weight: bold;
        }

        .total-row td {
            white-space: nowrap;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signatures {
            width: 100%;
            margin-top: 30px;
        }

        .signature-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 15px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            margin: 10px auto;
        }

        .signature-name {
            margin-top: 10px;
            font-size: 12px;
        }

        @media print {
            body {
                width: 100%;
                max-width: none;
                margin: 0;
                padding: 10px;
            }

            @page {
                size: landscape;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
</head>

<body>
    <div class="header">
        <img src="/api/placeholder/50/50" alt="Logo Rumah Sakit">
        <h1>RUMAH SAKIT ISLAM JOMBANG</h1>
        <p>Jl. Brigjen Kretarto 22A ☎ (0321) 860074 - 868972</p>
        <p>JOMBANG</p>
    </div>

    <div class="form-title">
        NOTA PERMINTAAN BARANG LOGISTIK GIZI<br>
        RUMAH TANGGA
    </div>

    <div class="form-info">
        <div>NOTA No. : {{ \App\Helpers\NotaHelper::generatePrintNota(date('dh')) }}</div>
        <div>RUANG : {{ $permintaan->first()->ruangan->nama_ruangan ?? '-' }}</div>
        <div>TANGGAL : {{ \Carbon\Carbon::parse($permintaan->first()->tanggal_permintaan)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">NO.</th>
                <th class="col-nama">NAMA BARANG</th>
                <th class="col-status">STATUS</th>
                <th class="col-keterangan">KETERANGAN</th>
                <th class="col-jumlah text-center">JUMLAH BARANG</th>
                <th class="col-harga text-right">HARGA SATUAN</th>
                <th class="col-total text-right">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_harga = 0;
                $total_barang = 0;
            @endphp
            @foreach ($permintaan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->status_label }}</td>
                    <td>{{ $item->transaksi->keterangan ?? '' }}</td>
                    <td>{{ $item->jumlah_approve ?? '-' }}</td>
                    <td>@currency($item->transaksi ? $item->transaksi->stok->harga : 0)</td>
                    <td>@currency($item->transaksi ? $item->jumlah_approve * $item->transaksi->stok->harga : 0)</td>
                </tr>
                @php
                    $total_harga += $item->transaksi ? $item->jumlah_approve * $item->transaksi->stok->harga : 0;
                    $total_barang += $item->jumlah_approve;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right" colspan="4">Jumlah Barang</td>
                <td class="text-center">{{ $total_barang }}</td>
                <td class="text-right">Jumlah Rp.</td>
                <td class="text-right">@currency($total_harga)</td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="signature-row">
            <x-qr-ttd title="Penerima Barang" :name="$permintaan->first()->penerima" :label="$permintaan->first()->penerima" />

            <x-qr-ttd title="Pengaju Barang" :name="$permintaan->first()->created_by->name" :label="$permintaan->first()->created_by->name" />

            @php
                $approve_name = $permintaan->first()->approve->name ?? 'Belum ditandatangani';
            @endphp
            <x-qr-ttd title="Petugas Logistik" :name="$approve_name" :label="$approve_name" />
        </div>
    </div>
</body>

</html>
