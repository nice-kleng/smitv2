@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i> Detail Closing Stok
                        </h3>
                        <div class="card-tools">
                            @if ($isClosed)
                                <span class="badge badge-success badge-lg">
                                    <i class="fas fa-lock"></i> CLOSED
                                </span>
                            @else
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-unlock"></i> DRAFT
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Info Header -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Periode</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $closings->first()->periode_format }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Barang</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $closings->count() }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Item Selisih</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $closings->filter(fn($c) => $c->has_selisih)->count() }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Closed By</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $isClosed ? $closings->first()->closedBy->name ?? '-' : '-' }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-user fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mb-3">
                            <a href="{{ route('inventory.closing.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            @if ($isClosed)
                                <a href="javascript:void(0)" class="btn btn-info" data-toggle="modal"
                                    data-target="#filterModal">
                                    <i class="fas fa-filter"></i> Filter
                                </a>
                                <a href="{{ route('inventory.laporan.stok.pdf') }}?bulan={{ explode('-', $periode)[1] }}&tahun={{ explode('-', $periode)[0] }}&kategori={{ request('kategori') ?? '' }}"
                                    class="btn btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </a>
                                <a href="{{ route('inventory.laporan.stok.excel') }}?bulan={{ explode('-', $periode)[1] }}&tahun={{ explode('-', $periode)[0] }}&kategori={{ request('kategori') ?? '' }}"
                                    class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            @endif
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap table-hover table-sm" id="detailTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="2%" class="text-center">No</th>
                                        <th width="8%">Kode</th>
                                        <th width="12%">Nama Barang</th>
                                        <th width="10%">Batch/Keterangan</th>
                                        <th width="8%" class="text-center">Harga/Unit</th>
                                        <th width="6%" class="text-center">Stok Awal</th>
                                        <th width="5%" class="text-center text-success">Masuk</th>
                                        <th width="5%" class="text-center text-danger">Keluar</th>
                                        <th width="6%" class="text-center">Stok Sistem</th>
                                        <th width="10%" class="text-center">Total Nilai</th>
                                        <th width="8%" class="text-center">Stok Fisik</th>
                                        <th width="5%" class="text-center">Selisih</th>
                                        <th width="15%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($closings as $index => $closing)
                                        <tr class="{{ $closing->has_selisih ? 'table-warning' : '' }}">
                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                            <td class="align-middle">{{ $closing->stok->barang->kode_barang ?? '-' }}</td>
                                            <td class="align-middle">
                                                <strong>{{ $closing->stok->barang->nama_barang }}</strong>
                                                @if ($closing->stok->barang->is_elektronik)
                                                    <br><small class="badge badge-info">Elektronik</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <small class="text-muted">{{ $closing->stok->keterangan ?? '-' }}</small>
                                            </td>
                                            <td class="text-right align-middle">
                                                <small>Rp
                                                    {{ number_format($closing->harga_per_unit, 0, ',', '.') }}</small>
                                            </td>
                                            <td class="text-center align-middle">{{ number_format($closing->stok_awal) }}
                                            </td>
                                            <td class="text-center align-middle text-success">
                                                <strong>{{ number_format($closing->stok_masuk) }}</strong>
                                            </td>
                                            <td class="text-center align-middle text-danger">
                                                <strong>{{ number_format($closing->stok_keluar) }}</strong>
                                            </td>
                                            <td class="text-center align-middle">
                                                <strong>{{ number_format($closing->stok_akhir_sistem) }}</strong>
                                            </td>
                                            <td class="text-right align-middle">
                                                <small>Rp
                                                    {{ number_format($closing->total_nilai_akhir, 0, ',', '.') }}</small>
                                            </td>
                                            <td class="text-center align-middle bg-light">
                                                @if ($closing->stok_fisik !== null)
                                                    {{ number_format($closing->stok_fisik) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($closing->selisih !== null)
                                                    @if ($closing->selisih > 0)
                                                        <span class="badge badge-success">
                                                            +{{ number_format($closing->selisih) }}
                                                        </span>
                                                    @elseif($closing->selisih < 0)
                                                        <span class="badge badge-danger">
                                                            {{ number_format($closing->selisih) }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">0</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($closing->keterangan)
                                                    <small>{{ $closing->keterangan }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light font-weight-bold">
                                    <tr>
                                        <td colspan="5" class="text-right">TOTAL:</td>
                                        <td class="text-center">{{ number_format($closings->sum('stok_awal')) }}</td>
                                        <td class="text-center text-success">
                                            {{ number_format($closings->sum('stok_masuk')) }}</td>
                                        <td class="text-center text-danger">
                                            {{ number_format($closings->sum('stok_keluar')) }}</td>
                                        <td class="text-center">{{ number_format($closings->sum('stok_akhir_sistem')) }}
                                        </td>
                                        <td class="text-right">
                                            <small>Rp
                                                {{ number_format($closings->sum('total_nilai_akhir'), 0, ',', '.') }}</small>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($closings->whereNotNull('stok_fisik')->sum('stok_fisik')) }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if ($isClosed)
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    Closed pada: {{ $closings->first()->closed_at->format('d F Y H:i:s') }}
                                    oleh {{ $closings->first()->closedBy->name }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="GET" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select class="form-control" id="kategori" name="kategori">
                                <option value="">-- Semua Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}"
                                        {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#detailTable').DataTable({
                    responsive: true,
                    pageLength: 50,
                });
            });
        </script>
    @endpush
@endsection
