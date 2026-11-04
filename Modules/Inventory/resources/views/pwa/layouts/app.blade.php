<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#F4F6F4">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    <title>{{ $title ?? 'Helpdesk' }}</title>
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/pwa.css', 'resources/js/pwa.js'])
    <style>
        html { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-paper text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-surface focus:px-3 focus:py-2 focus:text-ink">Lewati ke konten</a>

    <header class="sticky top-0 z-40 bg-paper/95 backdrop-blur" style="padding-top: env(safe-area-inset-top);">
        <div class="pwa-shell flex h-14 items-center gap-3">
            @hasSection('back')
                <a href="@yield('back')" aria-label="Kembali" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface text-ink">←</a>
            @else
                <img src="{{ asset('img/logo.png') }}" width="32" height="32" alt="Logo">
            @endif
            <h1 class="truncate text-base font-bold">@yield('topbar-title', 'Helpdesk')</h1>
            <button id="menu-button" type="button" aria-label="Buka menu" aria-haspopup="dialog" aria-controls="menu-sheet" aria-expanded="false" class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface text-ink">☰</button>
        </div>
    </header>

    <main id="main" class="pwa-shell py-5">@yield('content')</main>

    <div id="toast" role="status" aria-live="polite" hidden></div>

    <dialog id="menu-sheet" aria-labelledby="menu-title" class="max-h-[85vh] w-full max-w-md rounded-t-2xl p-0 overscroll-contain">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <h2 id="menu-title" class="text-base font-bold">Menu</h2>
            <button id="menu-close" type="button" aria-label="Tutup menu" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-line bg-surface">✕</button>
        </div>
        <div class="space-y-3 p-4">
            @if(auth()->user()->hasRole('teknisi'))
                <div id="push-control" hidden>
                    <button id="enable-push" type="button" class="pwa-button-secondary w-full" hidden>
                        Aktifkan notifikasi
                    </button>
                    <p id="push-help" class="pwa-error" hidden>
                        Notifikasi diblokir. Buka pengaturan situs di Chrome untuk mengizinkan.
                    </p>
                </div>
            @endif
            <a href="{{ route('inventory.helpdesk.ticket.index') }}" class="pwa-button-secondary w-full">Buka dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="pwa-button-secondary w-full">Keluar</button>
            </form>
        </div>
    </dialog>

    @stack('scripts')
</body>
</html>