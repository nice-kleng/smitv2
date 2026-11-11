<?php

namespace App\Http\Controllers;

use App\Models\PortalLink;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PortalController extends Controller
{
    /**
     * Display public portal page.
     */
    public function index()
    {
        $links = PortalLink::with('unit')->active()
            ->orderBy('category')
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        $internalLinks = $links->where('category', 'internal');
        $externalLinks = $links->where('category', 'external');

        $groupByUnit = function ($collection) {
            return $collection->groupBy(function ($link) {
                return $link->unit->nama_unit ?? 'Lainnya';
            })->sortKeys();
        };

        $internalGroups = $groupByUnit($internalLinks);
        $externalGroups = $groupByUnit($externalLinks);

        return view('portal.index', compact('links', 'internalLinks', 'externalLinks', 'internalGroups', 'externalGroups'));
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

        $filename = 'qr-'.\Illuminate\Support\Str::slug($portalLink->title).'.png';

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Verify PIN for external links access (server-side).
     */
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $correctPin = PortalSetting::getValue('external_pin', config('portal.external_pin', 'amalsholeh009'));

        if ($request->pin === $correctPin) {
            session([
                'portal_external_unlocked' => true,
                'portal_external_unlocked_at' => now()->timestamp,
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'PIN salah'], 422);
    }

    /**
     * Check if external access is unlocked.
     */
    public function checkAccess()
    {
        $unlocked = session('portal_external_unlocked', false);
        $unlockedAt = session('portal_external_unlocked_at', 0);
        $timeout = 5 * 60; // 5 minutes in seconds

        if ($unlocked && (now()->timestamp - $unlockedAt) > $timeout) {
            session()->forget(['portal_external_unlocked', 'portal_external_unlocked_at']);
            $unlocked = false;
        }

        return response()->json([
            'unlocked' => $unlocked,
        ]);
    }
}
