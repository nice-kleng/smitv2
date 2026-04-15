<div class="card">
    <div class="card-header">
        <h3 class="card-title">Preview Laporan Stok - {{ $periode }}</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="laporanTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok Awal</th>
                        <th class="text-center">Stok Masuk</th>
                        <th class="text-center">Stok Keluar</th>
                        <th class="text-center">Stok Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['kode_barang'] }}</td>
                            <td>{{ $item['nama_barang'] }}</td>
                            <td>{{ $item['satuan'] }}</td>
                            <td>{{ $item['kategori'] }}</td>
                            <td class="text-center">{{ number_format($item['stok_awal'], 0, ',', '.') }}</td>
                            <td class="text-center text-success">{{ number_format($item['stok_masuk'], 0, ',', '.') }}
                            </td>
                            <td class="text-center text-danger">{{ number_format($item['stok_keluar'], 0, ',', '.') }}
                            </td>
                            <td class="text-center font-weight-bold">
                                {{ number_format($item['stok_akhir'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">TOTAL:</td>
                        <td class="text-center">{{ number_format(collect($laporan)->sum('stok_awal'), 0, ',', '.') }}
                        </td>
                        <td class="text-center text-success">
                            {{ number_format(collect($laporan)->sum('stok_masuk'), 0, ',', '.') }}</td>
                        <td class="text-center text-danger">
                            {{ number_format(collect($laporan)->sum('stok_keluar'), 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format(collect($laporan)->sum('stok_akhir'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Tanggal Cetak: {{ $tanggal_cetak }}
            </small>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#laporanTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'print'
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });
    </script>
@endpush
