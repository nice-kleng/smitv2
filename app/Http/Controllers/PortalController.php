<?php

namespace App\Http\Controllers;

use App\Models\PortalLink;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PortalController extends Controller
{
    /**
     * Display public portal page.
     */
    public function index()
    {
        $links = PortalLink::active()
            ->orderBy('category')
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        $internalLinks = $links->where('category', 'internal');
        $externalLinks = $links->where('category', 'external');

        return view('portal.index', compact('links', 'internalLinks', 'externalLinks'));
    }

    /**
     * Generate and download QR Code for a portal link.
     */
    public function generateQr(PortalLink $portalLink)
    {
        $qrCode = QrCode::format('png')
            ->size(400)
            ->margin(2)
            ->generate($portalLink->url);

        $filename = 'qr-' . \Illuminate\Support\Str::slug($portalLink->title) . '.png';

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
