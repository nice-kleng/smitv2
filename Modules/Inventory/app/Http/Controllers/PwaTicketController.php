<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Inventory\Models\Ticket;

class PwaTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('ruangan.unit')
            ->where('status', '0')
            ->latest('created_at')
            ->get();

        return view('inventory::pwa.pengaduan.index', compact('tickets'));
    }

    public function show(string $id)
    {
        $ticket = Ticket::with(['ruangan.unit', 'jenisAduan', 'teknisi'])
            ->findOrFail($id);

        return view('inventory::pwa.pengaduan.show', compact('ticket'));
    }
}