<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Inventaris - {{ $inventaris->kode_barang }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }
        .card { border: 0; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .label { color: #6c757d; font-size: .85rem; margin-bottom: 2px; }
        .value { font-weight: 600; }
        .badge-baik { background: #198754; }
        .badge-kurang { background: #ffc107; color: #212529; }
        .badge-rusak { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-4" style="max-width: 720px;">
        <div class="mb-3">
            <h4 class="mb-0">Informasi Inventaris</h4>
            <small class="text-muted">Data barang dan riwayat mutasi</small>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ $inventaris->barang->nama_barang ?? '-' }}</h5>
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <div class="label">Kode Inventaris</div>
                        <div class="value">{{ $inventaris->kode_barang ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Nama Alias</div>
                        <div class="value">{{ $inventaris->nama_alias ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Kategori</div>
                        <div class="value">{{ $inventaris->barang->kategori->nama_kategori ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Merk</div>
                        <div class="value">{{ $inventaris->merk ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Tipe</div>
                        <div class="value">{{ $inventaris->type ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Nomor Seri</div>
                        <div class="value">{{ $inventaris->serial_number ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Tahun Pengadaan</div>
                        <div class="value">{{ $inventaris->tahun_pengadaan ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Unit Saat Ini</div>
                        <div class="value">{{ $inventaris->ruangan->unit->nama_unit ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Ruangan Saat Ini</div>
                        <div class="value">{{ $inventaris->ruangan->nama_ruangan ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Kepemilikan</div>
                        <div class="value">{{ $inventaris->kepemilikan ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="label">Kondisi Terkini</div>
                        <div class="value">
                            @if ($kondisiTerakhir)
                                @php
                                    $raw = $kondisiTerakhir->getRawOriginal('kondisi');
                                    $cls = match ((string) $raw) { '2' => 'badge-baik', '1' => 'badge-kurang', '0' => 'badge-rusak', default => 'bg-secondary' };
                                @endphp
                                <span class="badge {{ $cls }}">{{ $kondisiTerakhir->kondisi }}</span>
                            @else
                                <span class="badge bg-secondary">Belum ada data</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Riwayat Mutasi</h5>
                @if ($riwayat->isEmpty())
                    <p class="text-muted mb-0">Belum ada riwayat mutasi.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Unit / Ruangan</th>
                                    <th>Kondisi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayat as $item)
                                    @php
                                        $raw = $item->getRawOriginal('kondisi');
                                        $cls = match ((string) $raw) { '2' => 'badge-baik', '1' => 'badge-kurang', '0' => 'badge-rusak', default => 'bg-secondary' };
                                    @endphp
                                    <tr>
                                        <td>{{ $item->tanggal_mutasi ? \Carbon\Carbon::parse($item->tanggal_mutasi)->isoFormat('D MMM Y') : '-' }}</td>
                                        <td>{{ ($item->unit->nama_unit ?? '-') . ' / ' . ($item->ruangan->nama_ruangan ?? '-') }}</td>
                                        <td><span class="badge {{ $cls }}">{{ $item->kondisi }}</span></td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
