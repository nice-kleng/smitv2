<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang - {{ $periode }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info table {
            width: 100%;
        }

        .info td {
            padding: 3px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th {
            background-color: #4472C4;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #333;
        }

        table.data td {
            padding: 6px 5px;
            border: 1px solid #666;
            font-size: 10px;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.data tfoot td {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 8px 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #666;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            margin-right: 50px;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 150px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Stok Barang</h2>
        <p>Periode: {{ $periode }}</p>
        <p style="font-size: 10px;">Dicetak pada: {{ $tanggal_cetak }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Kode</th>
                <th width="15%">Nama Barang</th>
                <th width="8%">Satuan</th>
                <th width="8%">Stok Awal</th>
                <th width="7%">Masuk</th>
                <th width="7%">Keluar</th>
                <th width="8%">Stok Akhir</th>
                <th width="10%">Harga/Unit</th>
                <th width="10%">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalStokAwal = 0;
                $totalStokMasuk = 0;
                $totalStokKeluar = 0;
                $totalStokAkhir = 0;
                $totalNilaiAkhir = 0;
            @endphp

            @forelse($laporan as $index => $item)
                @php
                    $totalStokAwal += $item['stok_awal'];
                    $totalStokMasuk += $item['stok_masuk'];
                    $totalStokKeluar += $item['stok_keluar'];
                    $totalStokAkhir += $item['stok_akhir'];
                    $totalNilaiAkhir += $item['total_nilai_akhir'];
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['kode_barang'] }}</td>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td class="text-center">{{ $item['satuan'] }}</td>
                    <td class="text-center">{{ number_format($item['stok_awal'], 0, ',', '.') }}</td>
                    <td class="text-center text-success">{{ number_format($item['stok_masuk'], 0, ',', '.') }}</td>
                    <td class="text-center text-danger">{{ number_format($item['stok_keluar'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($item['stok_akhir'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['harga_per_unit'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['total_nilai_akhir'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-center">{{ number_format($totalStokAwal, 0, ',', '.') }}</td>
                <td class="text-center text-success">{{ number_format($totalStokMasuk, 0, ',', '.') }}</td>
                <td class="text-center text-danger">{{ number_format($totalStokKeluar, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totalStokAkhir, 0, ',', '.') }}</td>
                <td colspan="1"></td>
                <td class="text-right">Rp {{ number_format($totalNilaiAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p>Kepala Gudang</p>
            <div class="signature-line"></div>
            <p>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            </p>
        </div>
    </div>

    <div class="footer">
        <p>Catatan: Laporan ini digenerate secara otomatis oleh sistem</p>
    </div>
</body>

</html>
