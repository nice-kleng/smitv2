@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check"></i> Closing Stok Baru
                        </h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Info:</strong> Pilih periode yang akan di-closing. Pastikan semua transaksi sudah
                            lengkap.
                        </div>

                        <form action="{{ route('inventory.closing.generate') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="bulan">Bulan <span class="text-danger">*</span></label>
                                <select name="bulan" id="bulan"
                                    class="form-control @error('bulan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    <option value="1" {{ old('bulan', $defaultBulan) == 1 ? 'selected' : '' }}>Januari
                                    </option>
                                    <option value="2" {{ old('bulan', $defaultBulan) == 2 ? 'selected' : '' }}>Februari
                                    </option>
                                    <option value="3" {{ old('bulan', $defaultBulan) == 3 ? 'selected' : '' }}>Maret
                                    </option>
                                    <option value="4" {{ old('bulan', $defaultBulan) == 4 ? 'selected' : '' }}>April
                                    </option>
                                    <option value="5" {{ old('bulan', $defaultBulan) == 5 ? 'selected' : '' }}>Mei
                                    </option>
                                    <option value="6" {{ old('bulan', $defaultBulan) == 6 ? 'selected' : '' }}>Juni
                                    </option>
                                    <option value="7" {{ old('bulan', $defaultBulan) == 7 ? 'selected' : '' }}>Juli
                                    </option>
                                    <option value="8" {{ old('bulan', $defaultBulan) == 8 ? 'selected' : '' }}>Agustus
                                    </option>
                                    <option value="9" {{ old('bulan', $defaultBulan) == 9 ? 'selected' : '' }}>
                                        September</option>
                                    <option value="10" {{ old('bulan', $defaultBulan) == 10 ? 'selected' : '' }}>
                                        Oktober</option>
                                    <option value="11" {{ old('bulan', $defaultBulan) == 11 ? 'selected' : '' }}>
                                        November</option>
                                    <option value="12" {{ old('bulan', $defaultBulan) == 12 ? 'selected' : '' }}>
                                        Desember</option>
                                </select>
                                @error('bulan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tahun">Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" id="tahun"
                                    class="form-control @error('tahun') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($i = date('Y'); $i >= date('Y') - 3; $i--)
                                        <option value="{{ $i }}"
                                            {{ old('tahun', $defaultTahun) == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <strong>Checklist Sebelum Closing:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>✅ Semua transaksi sudah diinput</li>
                                    <li>✅ Tidak ada transaksi yang pending</li>
                                    <li>✅ Periode sebelumnya sudah di-closing</li>
                                    <li>✅ Siap melakukan stock opname (opsional)</li>
                                </ul>
                            </div>

                            <div class="form-group mb-0">
                                <a href="{{ route('inventory.closing.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-arrow-right"></i> Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
