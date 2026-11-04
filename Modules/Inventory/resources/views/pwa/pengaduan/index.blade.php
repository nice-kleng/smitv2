@extends('inventory::pwa.layouts.app', ['title' => 'Antrean pengaduan'])

@section('content')
    <h1 class="text-2xl font-bold">Antrean pengaduan</h1>
    <p class="pwa-muted mt-1">{{ $tickets->count() }} menunggu</p>

    <label for="queue-search" class="pwa-label mt-4">Cari tiket</label>
    <input id="queue-search" type="search" autocomplete="off" placeholder="Cari kode, unit, atau ruangan…" class="pwa-input">

    @if($tickets->isEmpty())
        <div class="pwa-card mt-4 p-4 text-center">
            <p class="pwa-muted">Tidak ada pengaduan menunggu. Antrean bersih.</p>
            <a href="{{ route('inventory.pwa.pengaduan.index') }}" class="pwa-button-secondary mt-3 inline-flex w-auto px-4">Muat ulang</a>
        </div>
    @else
        <ol id="ticket-list" class="mt-4 space-y-3">
            @foreach($tickets as $ticket)
                @php
                    $unit = $ticket->ruangan->unit->nama_unit ?? '';
                    $room = $ticket->ruangan->nama_ruangan ?? '';
                @endphp
                <li data-ticket-item data-search="{{ Str::lower($ticket->kd_ticket . ' ' . $unit . ' ' . $room . ' ' . $ticket->detail_aduan) }}">
                    <a href="{{ route('inventory.pwa.pengaduan.show', $ticket->id) }}" class="grid grid-cols-[52px_16px_minmax(0,1fr)] items-center gap-x-2 rounded-xl border border-line bg-surface p-3 min-h-[88px]">
                        <time data-local-time datetime="{{ $ticket->created_at?->toIso8601String() }}" class="text-xs font-medium text-muted"></time>
                        <span class="h-2 w-2 justify-self-center rounded-full bg-brand" aria-hidden="true"></span>
                        <div class="min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="truncate font-mono text-sm font-bold" translate="no">{{ $ticket->kd_ticket }}</span>
                                <span class="shrink-0 rounded-full bg-brand/10 px-2 py-0.5 text-xs font-semibold text-brand">Menunggu</span>
                            </div>
                            <p class="truncate text-sm text-muted">{{ $unit }} / {{ $room }}</p>
                            <p class="line-clamp-1 text-sm text-ink">{{ $ticket->detail_aduan }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ol>
    @endif

    <p id="search-empty" hidden>Tidak ada tiket yang cocok dengan pencarian.</p>

    <div class="mt-4">
        <a href="{{ route('inventory.pwa.pengaduan.index') }}" class="pwa-button-secondary">Muat ulang</a>
    </div>
@endsection