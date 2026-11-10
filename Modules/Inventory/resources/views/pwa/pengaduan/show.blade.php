@extends('inventory::pwa.layouts.app', ['title' => 'Detail pengaduan'])

@section('back', route('inventory.pwa.pengaduan.index'))
@section('topbar-title', 'Detail pengaduan')

@section('content')
    <div class="pb-28 space-y-4">
        @if(request()->boolean('saved'))
            <div role="status" aria-live="polite" class="rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm font-medium text-success">
                Tindakan tersimpan.
            </div>
        @endif

        <div class="pwa-card p-4">
            <div class="flex items-center justify-between gap-3">
                <p class="font-mono text-lg font-bold" translate="no">{{ $ticket->kd_ticket }}</p>
                @if($ticket->status === '0')
                    <span class="rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold text-brand">Menunggu</span>
                @else
                    <span class="rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success">Selesai</span>
                @endif
            </div>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-muted">Unit / ruangan</dt>
                    <dd class="text-right font-medium">{{ $ticket->ruangan?->unit?->nama_unit }} / {{ $ticket->ruangan?->nama_ruangan }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted">Dilaporkan</dt>
                    <dd><time data-local-datetime datetime="{{ $ticket->created_at?->toIso8601String() }}"></time></dd>
                </div>
                @if($ticket->jenisAduan)
                    <div class="flex justify-between gap-3">
                        <dt class="text-muted">Jenis aduan</dt>
                        <dd class="text-right font-medium">{{ $ticket->jenisAduan?->nama_jenis }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <section aria-labelledby="detail-heading" class="pwa-card p-4">
            <h2 id="detail-heading" class="text-sm font-bold">Detail aduan</h2>
            <p class="mt-2 text-sm text-ink">{{ $ticket->detail_aduan }}</p>
        </section>

        <section aria-labelledby="history-heading" class="pwa-card p-4">
            <h2 id="history-heading" class="text-sm font-bold">Riwayat</h2>
            <ol class="mt-2 space-y-1 text-sm">
                <li>Dilaporkan</li>
                <li>
                    @if($ticket->status === '0')
                        Menunggu tindakan
                    @else
                        Selesai
                    @endif
                    @if($ticket->teknisi)
                        — {{ $ticket->teknisi?->name }}
                    @endif
                </li>
            </ol>
        </section>

        @if($ticket->status !== '0')
            <section aria-labelledby="tindakan-heading" class="pwa-card p-4">
                <h2 id="tindakan-heading" class="text-sm font-bold">Tindakan</h2>
                <dl class="mt-2 space-y-2 text-sm">
                    <div>
                        <dt class="text-muted">Tindak lanjut</dt>
                        <dd>{{ $ticket->tindak_lanjut ?? 'Belum ada tindakan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted">Keterangan perbaikan</dt>
                        <dd>
                            @php($rawKeterangan = $ticket->getAttributes()['keterangan_perbaikan'] ?? null)
                            @if($rawKeterangan !== null)
                                {{ $ticket->keterangan_perbaikan }}
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>
        @endif
    </div>

    @if($ticket->status === '0')
        <div class="fixed bottom-0 inset-x-0 border-t border-line bg-surface pb-[calc(env(safe-area-inset-bottom)+12px)] pt-3">
            <div class="pwa-shell">
                <a href="{{ route('inventory.pwa.pengaduan.tindakan', $ticket->id) }}" class="pwa-button">Ambil tindakan</a>
            </div>
        </div>
    @endif
@endsection