@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <form id="closingForm" method="POST" action="{{ route('inventory.closing.save-draft') }}">
            @csrf
            <input type="hidden" name="periode" value="{{ $periode }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-check"></i> Form Closing Stok
                            </h3>
                            <div class="card-tools">
                                <h5 class="mb-0">
                                    <span class="badge badge-light">{{ $periodeFormat }}</span>
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Closing per Stok:</strong> Setiap batch/harga dicatat terpisah</li>
                                    <li><strong>Harga per Unit:</strong> Otomatis dari master stok</li>
                                    <li><strong>Total Nilai:</strong> Dihitung otomatis (Stok × Harga)</li>
                                    <li><strong>Stok Fisik</strong> (opsional): Input hasil cek fisik gudang</li>
                                    <li>Jika ada <strong>selisih</strong>, wajib isi keterangan</li>
                                </ul>
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3 mb-2">
                                    <div class="card text-white bg-info h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3 display-4"><i class="fas fa-boxes"></i></div>
                                            <div>
                                                <div class="text-uppercase small">Total Stok Items</div>
                                                <div class="h4 mb-0">{{ $data->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="card text-white bg-success h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3 display-4"><i class="fas fa-arrow-up"></i></div>
                                            <div>
                                                <div class="text-uppercase small">Total Stok Masuk</div>
                                                <div class="h5 mb-0">{{ number_format($data->sum('stok_masuk')) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="card text-white bg-danger h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3 display-4"><i class="fas fa-arrow-down"></i></div>
                                            <div>
                                                <div class="text-uppercase small">Total Stok Keluar</div>
                                                <div class="h5 mb-0">{{ number_format($data->sum('stok_keluar')) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="card text-dark bg-warning h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="mr-3 display-4"><i class="fas fa-dollar-sign"></i></div>
                                            <div>
                                                <div class="text-uppercase small">Total Nilai Akhir</div>
                                                <div class="h5 mb-0">Rp
                                                    {{ number_format($data->sum('total_nilai_akhir'), 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter & Tools -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <input type="text" id="searchTable" class="form-control"
                                        placeholder="🔍 Cari barang...">
                                </div>
                                <div class="col-md-8 text-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="copyStokSistem()">
                                            <i class="fas fa-copy"></i> Copy Stok Sistem ke Fisik
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="hitungSemuaSelisih()">
                                            <i class="fas fa-calculator"></i> Hitung Semua Selisih
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" id="closingTable">
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
                                            <th width="8%" class="text-center bg-light">Stok Fisik</th>
                                            <th width="5%" class="text-center">Selisih</th>
                                            <th width="15%">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $index => $item)
                                            <tr data-barang="{{ strtolower($item->stok->barang->nama_barang ?? '') }}">
                                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                <td class="align-middle">{{ $item->stok->barang->kode_barang ?? '-' }}
                                                </td>
                                                <td class="align-middle">
                                                    <strong>{{ $item->stok->barang->nama_barang ?? '-' }}</strong>
                                                    @if ($item->stok->barang->is_elektronik ?? false)
                                                        <br><small class="badge badge-info">Elektronik</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-muted">{{ $item->stok->keterangan ?? '-' }}</small>
                                                </td>

                                                {{-- HANYA KIRIM STOK_ID, STOK_FISIK, DAN KETERANGAN --}}
                                                <input type="hidden" name="items[{{ $index }}][stok_id]"
                                                    value="{{ $item->stok_id }}">

                                                <td class="text-right align-middle">
                                                    <small>Rp
                                                        {{ number_format($item->harga_per_unit, 0, ',', '.') }}</small>
                                                </td>
                                                <td class="text-center align-middle">{{ number_format($item->stok_awal) }}
                                                </td>
                                                <td class="text-center align-middle text-success">
                                                    <strong>{{ number_format($item->stok_masuk) }}</strong>
                                                </td>
                                                <td class="text-center align-middle text-danger">
                                                    <strong>{{ number_format($item->stok_keluar) }}</strong>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <strong>{{ number_format($item->stok_akhir_sistem) }}</strong>
                                                </td>
                                                <td class="text-right align-middle">
                                                    <small class="total-nilai-display">Rp
                                                        {{ number_format($item->total_nilai_akhir, 0, ',', '.') }}</small>
                                                </td>
                                                <td class="bg-light">
                                                    <input type="number" name="items[{{ $index }}][stok_fisik]"
                                                        class="form-control form-control-sm stok-fisik"
                                                        data-index="{{ $index }}"
                                                        data-sistem="{{ $item->stok_akhir_sistem }}"
                                                        data-harga="{{ $item->harga_per_unit }}"
                                                        value="{{ $item->stok_fisik }}" min="0"
                                                        placeholder="Opsional">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-secondary selisih-badge"
                                                        id="selisih-{{ $index }}">
                                                        @if ($item->selisih !== null)
                                                            {{ $item->selisih > 0 ? '+' : '' }}{{ number_format($item->selisih) }}
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <textarea name="items[{{ $index }}][keterangan]" class="form-control form-control-sm keterangan-input"
                                                        rows="1" placeholder="Wajib jika ada selisih" data-index="{{ $index }}">{{ $item->keterangan }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="5" class="text-right">TOTAL:</td>
                                            <td class="text-center">{{ number_format($data->sum('stok_awal')) }}</td>
                                            <td class="text-center text-success">
                                                {{ number_format($data->sum('stok_masuk')) }}</td>
                                            <td class="text-center text-danger">
                                                {{ number_format($data->sum('stok_keluar')) }}</td>
                                            <td class="text-center">{{ number_format($data->sum('stok_akhir_sistem')) }}
                                            </td>
                                            <td class="text-right">
                                                <span id="grandTotalNilai">Rp
                                                    {{ number_format($data->sum('total_nilai_akhir'), 0, ',', '.') }}</span>
                                            </td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('inventory.closing.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-info" name="action" value="draft">
                                        <i class="fas fa-save"></i> Simpan Draft
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="confirmClosing()">
                                        <i class="fas fa-lock"></i> Proses Closing
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Konfirmasi Closing -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-lock"></i> Konfirmasi Closing
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian!</strong> Setelah closing, data tidak dapat diubah lagi.
                    </div>

                    <p><strong>Periode:</strong> {{ $periodeFormat }}</p>
                    <p><strong>Total Items:</strong> {{ $data->count() }} stok</p>
                    <p><strong>Total Nilai Akhir:</strong> Rp
                        {{ number_format($data->sum('total_nilai_akhir'), 0, ',', '.') }}</p>
                    <p><strong>Item dengan Selisih:</strong> <span id="itemWithSelisih">0</span></p>

                    <hr>
                    <p class="mb-0">Apakah Anda yakin ingin memproses closing stok?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmClosingBtn">
                        <i class="fas fa-lock"></i> Ya, Proses Closing
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Search table
                $('#searchTable').on('keyup', function() {
                    var value = $(this).val().toLowerCase();
                    $('#closingTable tbody tr').filter(function() {
                        $(this).toggle($(this).data('barang').indexOf(value) > -1);
                    });
                });

                // Auto calculate selisih and total nilai when stok fisik changed
                $('.stok-fisik').on('input', function() {
                    var index = $(this).data('index');
                    var stokSistem = parseInt($(this).data('sistem'));
                    var harga = parseFloat($(this).data('harga'));
                    var stokFisik = parseInt($(this).val()) || 0;
                    var selisih = stokFisik - stokSistem;

                    updateSelisihDisplay(index, selisih, stokFisik);

                    // Update total nilai display (hanya visual, server akan recalculate)
                    var totalNilai;
                    if (stokFisik > 0) {
                        totalNilai = stokFisik * harga;
                    } else {
                        totalNilai = stokSistem * harga;
                    }

                    $(this).closest('tr').find('.total-nilai-display').text('Rp ' + formatNumber(totalNilai));

                    // Update grand total
                    updateGrandTotal();
                });
            });

            function updateSelisihDisplay(index, selisih, stokFisik) {
                var badge = $('#selisih-' + index);
                var keterangan = $('textarea[data-index="' + index + '"]');

                if (stokFisik > 0) {
                    var text = selisih > 0 ? '+' + selisih : selisih;
                    badge.text(text);

                    if (selisih > 0) {
                        badge.removeClass('badge-secondary badge-danger').addClass('badge-success');
                    } else if (selisih < 0) {
                        badge.removeClass('badge-secondary badge-success').addClass('badge-danger');
                        keterangan.addClass('border-danger');
                    } else {
                        badge.removeClass('badge-success badge-danger').addClass('badge-secondary');
                        keterangan.removeClass('border-danger');
                    }
                } else {
                    badge.text('-').removeClass('badge-success badge-danger').addClass('badge-secondary');
                    keterangan.removeClass('border-danger');
                }
            }

            function updateGrandTotal() {
                var total = 0;
                $('.stok-fisik').each(function() {
                    var stokFisik = parseInt($(this).val()) || 0;
                    var harga = parseFloat($(this).data('harga'));
                    var stokSistem = parseInt($(this).data('sistem'));

                    if (stokFisik > 0) {
                        total += stokFisik * harga;
                    } else {
                        total += stokSistem * harga;
                    }
                });

                $('#grandTotalNilai').text('Rp ' + formatNumber(total));
            }

            function formatNumber(num) {
                return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function copyStokSistem() {
                if (confirm('Copy stok sistem ke semua stok fisik?')) {
                    $('.stok-fisik').each(function() {
                        var sistem = $(this).data('sistem');
                        $(this).val(sistem).trigger('input');
                    });
                    alert('Stok sistem berhasil di-copy!');
                }
            }

            function hitungSemuaSelisih() {
                $('.stok-fisik').trigger('input');
                alert('Semua selisih berhasil dihitung!');
            }

            function confirmClosing() {
                var itemWithSelisih = 0;
                var itemWithoutKeterangan = [];

                $('.stok-fisik').each(function() {
                    var index = $(this).data('index');
                    var stokFisik = parseInt($(this).val()) || 0;
                    var stokSistem = parseInt($(this).data('sistem'));
                    var selisih = stokFisik - stokSistem;
                    var keterangan = $('textarea[data-index="' + index + '"]').val().trim();

                    if (stokFisik > 0 && selisih !== 0) {
                        itemWithSelisih++;
                        if (!keterangan) {
                            itemWithoutKeterangan.push(index + 1);
                        }
                    }
                });

                if (itemWithoutKeterangan.length > 0) {
                    alert('Ada ' + itemWithoutKeterangan.length +
                        ' stok dengan selisih yang belum diberi keterangan!\nBaris: ' + itemWithoutKeterangan.join(', '));
                    return false;
                }

                $('#itemWithSelisih').text(itemWithSelisih);
                $('#confirmModal').modal('show');
            }

            $('#confirmClosingBtn').on('click', function() {
                var form = $('#closingForm');
                form.attr('action', '{{ route('inventory.closing.proses-closed') }}');
                form.submit();
            });
        </script>
    @endpush
@endsection
