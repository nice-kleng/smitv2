@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laporan Stok Barang</h3>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ route('inventory.laporan.stok.preview') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bulan">Bulan <span class="text-danger">*</span></label>
                                        <select name="bulan" id="bulan" class="form-control" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November
                                            </option>
                                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tahun">Tahun <span class="text-danger">*</span></label>
                                        <select name="tahun" id="tahun" class="form-control" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                                <option value="{{ $i }}"
                                                    {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Tampilkan Laporan
                                    </button>
                                    <button type="button" class="btn btn-success" id="exportExcel">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                    <button type="button" class="btn btn-danger" id="exportPdf">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preview Area -->
                <div id="previewArea" class="mt-4">
                    <!-- Preview akan ditampilkan di sini -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Preview laporan
                $('#filterForm').on('submit', function(e) {
                    e.preventDefault();

                    const bulan = $('#bulan').val();
                    const tahun = $('#tahun').val();

                    if (!bulan || !tahun) {
                        alert('Harap pilih bulan dan tahun!');
                        return;
                    }

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'GET',
                        data: {
                            bulan: bulan,
                            tahun: tahun
                        },
                        beforeSend: function() {
                            $('#previewArea').html(
                                '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p>Memuat data...</p></div>'
                                );
                        },
                        success: function(response) {
                            $('#previewArea').html(response);
                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan saat memuat data');
                            $('#previewArea').html('');
                        }
                    });
                });

                // Export Excel
                $('#exportExcel').on('click', function() {
                    const bulan = $('#bulan').val();
                    const tahun = $('#tahun').val();

                    if (!bulan || !tahun) {
                        alert('Harap pilih bulan dan tahun!');
                        return;
                    }

                    window.location.href = "{{ route('inventory.laporan.stok.excel') }}?bulan=" + bulan +
                        "&tahun=" + tahun;
                });

                // Export PDF
                $('#exportPdf').on('click', function() {
                    const bulan = $('#bulan').val();
                    const tahun = $('#tahun').val();

                    if (!bulan || !tahun) {
                        alert('Harap pilih bulan dan tahun!');
                        return;
                    }

                    window.location.href = "{{ route('inventory.laporan.stok.pdf') }}?bulan=" + bulan +
                        "&tahun=" + tahun;
                });
            });
        </script>
    @endpush
@endsection
