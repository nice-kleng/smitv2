@props(['title', 'name', 'label'])

<div class="signature-box">
    <p>{{ $title }}</p>
    @if ($name === 'Belum ditandatangani')
        <div class="qr-code" style="border: 1px solid #000; display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 10px; text-align: center;">Belum ditandatangani</span>
        </div>
    @else
        <img class="qr-code"
            src="data:image/png;base64,{{ base64_encode(
                QrCode::format('png')->merge(public_path('img/logo.png'), 0.3, true)->errorCorrection('M')->size(100)->generate($name ?? 'Belum ditandatangani'),
            ) }}"
            alt="QR {{ $title }}">
    @endif
    <div class="signature-name">{{ $label ?? 'Belum ditandatangani' }}</div>
</div>
