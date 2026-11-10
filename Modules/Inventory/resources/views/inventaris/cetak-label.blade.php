<!DOCTYPE html>
<html>

<head>
    <title>Label Inventaris</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        @page {
            size: 104mm 25mm;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 104mm;
        }

        .label {
            width: 104mm;
            height: 25mm;
            display: block;
            background: white;
            margin: 0;
            padding: 0;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .label + .label {
            page-break-before: always;
        }

        .qr-info-container {
            border: 1px solid #000;
            background: white;
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .qr-info-container td {
            vertical-align: middle;
            padding: 0;
        }

        .barcode-cell {
            width: 22mm;
            text-align: center;
        }

        .barcode-cell img {
            width: 20mm;
            height: 20mm;
            display: block;
            margin: 0 auto;
        }

        .info-cell {
            padding: 0 2mm;
        }

        .info-row {
            margin: 0;
            font-size: 8pt;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
        }

        .label-text {
            color: #000;
            display: inline-block;
            width: 10mm;
        }

        .separator {
            display: inline-block;
            margin: 0 1mm;
        }

        .value-text {
            display: inline-block;
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="label">
            <table class="qr-info-container">
                <tr>
                    <td class="barcode-cell">
                        <img src="data:image/png;base64,{!! DNS2D::getBarcodePNG(route('inventory.track', $item->id), 'QRCODE', 6, 6) !!}" alt="QR">
                    </td>
                    <td class="info-cell">
                        <div class="info-row">
                            <span class="label-text">Nama</span>
                            <span class="separator">:</span>
                            <span class="value-text">{{ $item->barang->nama_barang }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label-text">No</span>
                            <span class="separator">:</span>
                            <span class="value-text">{{ $item->kode_barang }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>

</html>
