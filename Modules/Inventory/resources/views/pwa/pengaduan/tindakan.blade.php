@extends('inventory::pwa.layouts.app', ['title' => 'Tindakan'])

@section('back', route('inventory.pwa.pengaduan.show', $ticket->id))
@section('topbar-title', 'Tindakan')

@section('content')
    <form id="tindakan-form" method="POST" action="{{ route('inventory.helpdesk.ticket.tindakan', $ticket->id) }}" data-detail-url="{{ route('inventory.pwa.pengaduan.show', $ticket->id) }}" novalidate>
        @csrf
        @method('PUT')

        <div class="space-y-4 pb-28">
            <div class="pwa-card p-4 space-y-4">
                <div>
                    <label for="inventaris_id" class="pwa-label">Barang</label>
                    <select id="inventaris_id" name="inventaris_id" aria-describedby="error-inventaris_id" class="pwa-input">
                        <option value="">Pilih barang di ruangan ini</option>
                        @foreach($inventories as $inventory)
                            <option value="{{ $inventory->id }}" @selected(old('inventaris_id', $ticket->inventaris_id) == $inventory->id)>
                                {{ $inventory->nama_alias }} - {{ $inventory->barang?->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    <p id="error-inventaris_id" data-error-for="inventaris_id" class="pwa-error" aria-live="polite"></p>
                </div>

                <div>
                    <label for="jenis_aduan_id" class="pwa-label">Jenis aduan</label>
                    <select id="jenis_aduan_id" name="jenis_aduan_id" aria-describedby="error-jenis_aduan_id" class="pwa-input">
                        @foreach($jenisAduan as $jenis)
                            <option value="{{ $jenis->id }}" @selected(old('jenis_aduan_id', $ticket->jenis_aduan_id) == $jenis->id)>
                                {{ $jenis->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                    <p id="error-jenis_aduan_id" data-error-for="jenis_aduan_id" class="pwa-error" aria-live="polite"></p>
                </div>

                <div>
                    <label for="tindak_lanjut" class="pwa-label">Tindak lanjut</label>
                    <textarea id="tindak_lanjut" name="tindak_lanjut" rows="3" aria-describedby="error-tindak_lanjut" class="pwa-input">{{ old('tindak_lanjut', $ticket->tindak_lanjut) }}</textarea>
                    <p id="error-tindak_lanjut" data-error-for="tindak_lanjut" class="pwa-error" aria-live="polite"></p>
                </div>

                <fieldset>
                    <legend class="pwa-label">Keterangan perbaikan</legend>
                    <div class="space-y-2">
                        @foreach([
                            '1' => 'Perbaikan sendiri',
                            '2' => 'Pemeliharaan',
                            '3' => 'Perbaikan dan pemeliharaan',
                            '4' => 'Service luar',
                        ] as $value => $label)
                            <label class="flex items-center gap-3 rounded-lg border border-line bg-surface p-3">
                                <input type="radio" name="keterangan_perbaikan" value="{{ $value }}"
                                    @checked(old('keterangan_perbaikan', $ticket->getRawOriginal('keterangan_perbaikan')) == $value)
                                    aria-describedby="error-keterangan_perbaikan">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p id="error-keterangan_perbaikan" data-error-for="keterangan_perbaikan" class="pwa-error" aria-live="polite"></p>
                </fieldset>

                <div data-service-fields @if($ticket->getRawOriginal('keterangan_perbaikan') !== '4') hidden @endif class="space-y-4">
                    <div>
                        <label for="tempat_service" class="pwa-label">Tempat service</label>
                        <input id="tempat_service" name="tempat_service" type="text" value="{{ old('tempat_service') }}" aria-describedby="error-tempat_service" class="pwa-input" @if($ticket->getRawOriginal('keterangan_perbaikan') !== '4') disabled @endif>
                        <p id="error-tempat_service" data-error-for="tempat_service" class="pwa-error" aria-live="polite"></p>
                    </div>
                    <div>
                        <label for="kerusakan" class="pwa-label">Kerusakan</label>
                        <input id="kerusakan" name="kerusakan" type="text" value="{{ old('kerusakan') }}" aria-describedby="error-kerusakan" class="pwa-input" @if($ticket->getRawOriginal('keterangan_perbaikan') !== '4') disabled @endif>
                        <p id="error-kerusakan" data-error-for="kerusakan" class="pwa-error" aria-live="polite"></p>
                    </div>
                    <div>
                        <label for="biaya" class="pwa-label">Biaya</label>
                        <input id="biaya" name="biaya" type="number" inputmode="decimal" min="0" step="1" value="{{ old('biaya') }}" aria-describedby="error-biaya" class="pwa-input" @if($ticket->getRawOriginal('keterangan_perbaikan') !== '4') disabled @endif>
                        <p id="error-biaya" data-error-for="biaya" class="pwa-error" aria-live="polite"></p>
                    </div>
                </div>
            </div>

            <div id="form-toast" role="status" aria-live="polite" hidden></div>
        </div>

        <div class="fixed bottom-0 inset-x-0 border-t border-line bg-surface pb-[calc(env(safe-area-inset-bottom)+12px)] pt-3">
            <div class="pwa-shell">
                <button id="save-tindakan" type="submit" form="tindakan-form" class="pwa-button">Simpan tindakan</button>
            </div>
        </div>
    </form>
@endsection