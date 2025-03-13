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

        .label {
            width: 300px;
            height: 140px;
            display: inline-block;
            background: white;
            margin: 5px;
            margin-top: 50px;
        }

        .qr-info-container {
            border: 1px solid #000;
            margin: 10px;
            background: white;
        }

        .header {
            background: #006400;
            color: white;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .qr-code {
            width: 50px;
            height: 50px;
            display: inline;
            margin: 10px;
        }

        .info-container {
            padding: 0 10px 10px 10px;
        }

        .info-row {
            margin: 3px 0;
            font-size: 13px;
        }

        .label-text {
            color: #000;
            display: inline-block;
            width: 45px;
        }

        .separator {
            display: inline-block;
            margin: 0 5px;
        }

        .value-text {
            display: inline-block;
            font-weight: normal;
        }

        @media print {
            .label {
                margin: 0;
                page-break-inside: avoid;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="label">
            <div class="qr-info-container">
                <div class="header">
                    Inventaris RSI Jombang
                </div>
                {{-- <img class="qr-code"
                    src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(80)->generate($item->kode_barang)) }}"
                    alt="QR Code">
                <div class="info-container">
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
                </div> --}}
                <table>
                    <tr>
                        <td>
                            <img class="qr-code"
                                src="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(80)->generate($item->kode_barang)) }}"
                                alt="QR Code">
                        </td>
                        <td>
                            <div class="info-container">
                                <div style="font-size: 11px;">
                                    <span>Nama</span>
                                    <span>:</span>
                                    <span>{{ $item->barang->nama_barang }}</span>
                                </div>
                                <div style="font-size: 11px;">
                                    <span>No</span>
                                    <span>:</span>
                                    <span>{{ $item->kode_barang }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html>
