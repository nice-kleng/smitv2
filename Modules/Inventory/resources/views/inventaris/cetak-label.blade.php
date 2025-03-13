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

        body {
            padding: 0;
            margin: 0;
        }

        /* Setting untuk ukuran label 50mm x 25mm (umum untuk label kecil) */
        .label {
            width: 104mm;
            /* Maksimum lebar printer ZD220 */
            height: 25mm;
            /* Tinggi label yang umum */
            display: inline-block;
            background: white;
            margin: 0;
            page-break-after: always;
        }

        .qr-info-container {
            border: 1px solid #000;
            background: white;
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .barcode-container {
            padding: 2mm;
            text-align: center;
            max-width: 100%;
            overflow: hidden;
        }

        .barcode-container svg {
            max-width: 100%;
            height: 10mm;
        }

        .info-container {
            padding: 1mm 2mm;
        }

        .info-row {
            margin: 1mm 0;
            font-size: 8pt;
            display: flex;
        }

        .label-text {
            color: #000;
            display: inline-block;
            width: 10mm;
            font-weight: normal;
        }

        .separator {
            display: inline-block;
            margin: 0 1mm;
        }

        .value-text {
            display: inline-block;
            font-weight: normal;
        }

        @media print {
            @page {
                size: 104mm 25mm;
                /* Ukuran sesuai dengan label */
                margin: 0;
            }

            html,
            body {
                width: 104mm;
                height: 25mm;
            }

            .label {
                page-break-after: always;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="label">
            <div class="qr-info-container">
                <div class="barcode-container">
                    {!! DNS1D::getBarcodeHTML($item->kode_barang, 'C39', 1.3, 80) !!}
                </div>
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
                </div>
            </div>
        </div>
    @endforeach
</body>

</html>
