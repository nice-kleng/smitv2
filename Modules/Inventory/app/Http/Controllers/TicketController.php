<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogBook;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\HistoryService;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\JenisAduan;
use Modules\Inventory\Models\Ticket;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangans = Ruangan::with('unit')->join('units', 'ruangans.unit_id', '=', 'units.id')->orderBy('units.nama_unit', 'asc')->orderBy('ruangans.nama_ruangan', 'asc')->get(['ruangans.*']);
        return view('inventory::helpdesk.landing_page', ['ruangans' => $ruangans]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required',
            'detail_aduan' => 'required',
        ]);

        $kd_ticket = 'T' . date('Ymd') . str_pad(Ticket::whereDate('created_at', now()->toDateString())->count() + 1, 4, '0', STR_PAD_LEFT);
        $request->merge(['kd_ticket' => $kd_ticket]);

        $ticket = Ticket::create($request->all());

        return redirect()->route('helpdesk.antrean')
            ->with('success', 'Laporan berhasil dikirimkan.');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('inventory::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('inventory::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function listTicket(Request $request)
    {
        $jenisAduan = JenisAduan::orderBy('nama_jenis', 'asc')->get();
        if ($request->ajax()) {
            $tickets = Ticket::with('ruangan.unit', 'teknisi', 'jenisAduan')->where('status', '0')->get();
            return DataTables::of($tickets)
                ->addIndexColumn()
                ->addColumn('tanggal_pengaduan', function ($row) {
                    return Carbon::parse($row->created_at)->isoFormat('D MMMM Y HH:mm');
                })
                ->addColumn('status', function ($row) {
                    return $row->status == 1 ? '<span class="badge badge-success">Selesai</span>' : '<span class="badge badge-warning">Pending</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-info btn-sm detail">View</a>';
                    $btn .= ' <a href="javascript:void(0)" class="btn btn-success btn-sm tindakan" data-id="' . $row->id . '">Tindakan</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('inventory::helpdesk.list', ['jenisaduan' => $jenisAduan]);
    }

    // public function detail(string $id)
    // {
    //     $ticket = Ticket::with('ruangan', 'teknisi', 'inventory', 'jenisAduan')->findOrFail($id);
    //     $ticket = $ticket->map(function ($data) {
    //         $data->tanggal_pengaduan = Carbon::parse($data->created_at)->isoFormat('D MMMM Y HH:mm');
    //         $data->status = $data->status == 1 ? '<span class="badge badge-success">Selesai</span>' : '<span class="badge badge-warning">Pending</span>';
    //         $data->teknisi = $data->teknisi ? $data->teknisi->name : '-';
    //         $data->inventaris = $data->inventaris ? $data->inventaris->barang->nama_barang : '-';
    //         return $data;
    //     });
    // }

    public function getTindakan(string $id)
    {
        $ticket = Ticket::with('jenisAduan')->findOrFail($id);
        $inventories = Inventory::with('barang')
            ->where('ruangan_id', $ticket->ruangan_id)
            ->get();

        return response()->json([
            'ticket' => $ticket,
            'inventories' => $inventories->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_barang' => $item->barang->nama_barang
                ];
            })
        ]);
    }

    public function tindakan(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'jenis_aduan_id' => 'required|exists:jenis_aduans,id',
                'tindak_lanjut' => 'required',
                'keterangan_perbaikan' => 'required',
            ]);

            $ticket = Ticket::findOrFail($id);
            $ticket->update([
                'inventaris_id' => $request->inventaris_id ?? null,
                'jenis_aduan_id' => $request->jenis_aduan_id,
                'tindak_lanjut' => $request->tindak_lanjut,
                'keterangan_perbaikan' => $request->keterangan_perbaikan,
                'status' => '1',
                'teknisi_id' => Auth::user()->id,
                'tanggal_perbaikan' => now(),
            ]);

            LogBook::create([
                'user_id' => Auth::user()->id,
                'kegiatan' => 'Menangani aduan ' . $ticket->kd_ticket,
                'keterangan' => 'Menangani aduan ' . $ticket->kd_ticket,
                'jenis' => '1',
                'service_id' => $ticket->id,
            ]);

            if ($request->keterangan_perbaikan == '4') {
                HistoryService::create([
                    'inventaris_id' => $request->inventaris_id,
                    'tempat_service' => $request->tempat_service,
                    'kerusakan' => $request->kerusakan,
                    'biaya' => $request->biaya ?? 0,
                    'tanggal_perbaikan' => date('Y-m-d'),
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tindakan berhasil disimpan.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal disimpan.'
            ]);
        }
    }

    public function rekapServiceLuar()
    {
        $historyService = HistoryService::with('inventory.barang')->get();
        return view('inventory::helpdesk.rekap_service', ['data' => $historyService]);
    }

    public function historyServiceTeknisi()
    {
        $historyService = Ticket::with('inventaris.barang', 'ruangan.unit');

        if (!Auth::user()->hasRole('superadmin')) {
            $historyService->where('teknisi_id', Auth::user()->id);
        }

        $historyService->where('status', 1)->orderBy('tanggal_perbaikan', 'desc');

        return view('inventory::helpdesk.history_service_teknisi', ['data' => $historyService->get()]);
    }

    public function antrean()
    {
        $tickets = Ticket::with('ruangan.unit')->where('status', '0')->orderBy('kd_ticket', 'asc')->get();
        return view('inventory::helpdesk.listGuest', ['tickets' => $tickets]);
    }
}
