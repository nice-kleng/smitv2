<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\HistoryInventaris;
use Modules\Inventory\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Inventory::whereHas('barang', function ($query) {
                $query->where('pu', Auth::user()->pu_kd);
            })->with('barang.kategori', 'ruangan.unit', 'penghapus', 'historyMutasi')->get();

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('kode_barang', function ($row) {
                    return $row->kode_barang;
                })
                ->addColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang;
                })
                ->addColumn('merk', function ($row) {
                    return $row->merk ?? '-';
                })
                ->addColumn('type', function ($row) {
                    return $row->type ?? '-';
                })
                ->addColumn('nomor_seri', function ($row) {
                    return $row->no_seri ?? '-';
                })
                ->addColumn('kategori', function ($row) {
                    return $row->barang->kategori->nama_kategori;
                })
                ->addColumn('tahun_pengadaan', function ($row) {
                    return $row->tahun_pengadaan;
                })
                ->addColumn('unit', function ($row) {
                    return $row->ruangan->unit->nama_unit;
                })
                ->addColumn('ruangan', function ($row) {
                    return $row->ruangan->nama_ruangan;
                })
                ->addColumn('harga_beli', function ($row) {
                    return number_format($row->harga_beli, 0, ',', '.');
                })
                ->addColumn('kondisi', function ($row) {
                    $kondisi = $row->historyMutasi->sortByDesc('created_at')->first();
                    if (!$kondisi) return '<span class="badge badge-secondary">Belum ada kondisi</span>';

                    $badges = [
                        'Baik' => 'success',
                        'Kurang Baik' => 'warning',
                        'Rusak' => 'danger'
                    ];

                    $kondisiText = $kondisi->kondisi;
                    $badgeColor = $badges[$kondisiText] ?? 'secondary';
                    return '<span class="badge badge-' . $badgeColor . '">' . $kondisiText . '</span>';
                })
                ->addColumn('status', function ($row) {
                    return $row->penghapus ? 'Dihapus' : 'Aktif';
                })
                ->addColumn('kepemilikan', function ($row) {
                    return $row->kepemilikan ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('inventory.history-mutasi', $row->id) . '" class="mutasi btn btn-info btn-sm"><i class="fas fa-history"></i> Mutasi</a>';

                    // $btn .= ' <a href="javascript:void(0)" class="mutasi btn btn-warning btn-sm"><i class="fas fa-history"></i> Service</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'kondisi'])
                ->make(true);
        }
        return view('inventory::inventaris.inventaris');
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
        //
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

    public function historyMutasi(string $id)
    {
        $inventaris = Inventory::with('historyMutasi')->findOrFail($id);
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        return view('inventory::inventaris.mutasi', compact('inventaris', 'units'));
    }

    public function getRuanganByUnit()
    {
        $unitId = request('id');
        $ruangan = Ruangan::where('unit_id', $unitId)->get();
        return response()->json($ruangan);
    }

    public function storeHistoryMutasi(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required',
            'unit_id' => 'required',
            'ruangan_id' => 'required',
            'kondisi' => 'required',
            'tanggal_mutasi' => 'required',
        ]);

        $request->merge([
            'created_id' => Auth::id(),
            'updated_id' => Auth::id(),
        ]);
        HistoryInventaris::create($request->all());
        return redirect()->back()->with('success', 'Data berhasil disimpan');
    }

    public function deleteHistoryMutasi(string $id)
    {
        $history = HistoryInventaris::findOrFail($id);
        $history->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
