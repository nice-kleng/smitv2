@props(['title', 'name', 'label'])

<div class="signature-box">
    <p>{{ $title }}</p>
    <img class="qr-code"
        src="data:image/png;base64,{{ base64_encode(
            QrCode::format('png')->merge(public_path('img/logo.png'), 0.3, true)->errorCorrection('M')->size(100)->generate($name ?? 'Belum ditandatangani'),
        ) }}"
        alt="QR {{ $title }}">
    <div class="signature-name">{{ $label ?? 'Belum ditandatangani' }}</div>
</div>
