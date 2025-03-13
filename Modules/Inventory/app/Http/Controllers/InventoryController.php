<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\HistoryInventaris;
use Modules\Inventory\Models\Inventory;
use App\Imports\InventoryImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Inventory\Models\MasterBarang;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Inventory::with('barang.kategori', 'ruangan.unit', 'penghapus', 'historyMutasi');

            if (Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
                $data = $query->whereHas('barang', function ($q) {
                    $q->where('pu', Auth::user()->pu_kd);
                })->get();
            } else {
                $data = $query->where('ruangan_id', Auth::user()->ruangan_id)->get();
            }

            return datatables()->of($data)
                ->addColumn('id', function ($row) {
                    return $row->id;
                })
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

                    $btn .= ' <a href="' . route('inventory.edit', $row->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit/ce</a>';
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
        $barangs = MasterBarang::orderBy('nama_barang', 'asc')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan', 'asc')->get();
        return view('inventory::inventaris.create', compact('barangs', 'ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create');
        $request->validate([
            'kode_barang' => 'required',
            'no_barang' => 'required',
            'barang_id' => 'required|exists:master_barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
        ]);

        $request->merge([
            'created_id' => Auth::id(),
            'updated_id' => Auth::id(),
        ]);

        Inventory::create($request->all());
        return redirect()->route('inventory.index')->with('success', 'Data berhasil disimpan');
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
        $inventaris = Inventory::findOrFail($id);
        $barangs = MasterBarang::orderBy('nama_barang', 'asc')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan', 'asc')->get();
        return view('inventory::inventaris.form', compact('inventaris', 'barangs', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update');
        $request->validate([
            'kode_barang' => 'required',
            'no_barang' => 'required',
            'barang_id' => 'required|exists:master_barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
        ]);

        $inventaris = Inventory::findOrFail($id);
        $request->merge([
            'updated_id' => Auth::id(),
        ]);

        $inventaris->update($request->all());
        return redirect()->route('inventory.index')->with('success', 'Data berhasil diupdate');
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
        $this->authorize('create');
        DB::beginTransaction();
        try {
            $request->validate([
                'inventory_id' => 'required',
                'unit_id' => 'required',
                'ruangan_id' => 'required',
                'kondisi' => 'required',
                'tanggal_mutasi' => 'required',
            ]);

            $inventory = Inventory::find($request->inventory_id);
            $inventory->update(['ruangan_id' => $request->ruangan_id]);

            $request->merge([
                'created_id' => Auth::id(),
                'updated_id' => Auth::id(),
            ]);
            HistoryInventaris::create($request->all());
            DB::commit();
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            dd($th);
        }
    }

    public function deleteHistoryMutasi(string $id)
    {
        $history = HistoryInventaris::findOrFail($id);
        $history->delete();
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new InventoryImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data');
        }
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/inventory_template.xlsx');
        return response()->download($path);
    }

    // public function cetakLabelInventaris(Request $request)
    // {
    //     $query = Inventory::with('barang');

    //     if ($request->has('ids')) {
    //         $query->whereIn('id', explode(',', $request->ids));
    //     }

    //     if (Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
    //         $data = $query->whereHas('barang', function ($q) {
    //             $q->where('pu', Auth::user()->pu_kd);
    //         })->get();
    //     } else {
    //         $data = $query->where('ruangan_id', Auth::user()->ruangan_id)->get();
    //     }

    //     $pdf = PDF::loadView('inventory::inventaris.cetak-label', compact('data'));

    //     // Setting untuk A4
    //     $pdf->setPaper('A4', 'portrait');

    //     // Setting margin minimal
    //     $pdf->setOption(['margin-top' => 0, 'margin-right' => 0, 'margin-bottom' => 0, 'margin-left' => 100]);

    //     return $pdf->stream('label-inventaris.pdf');
    // }
    public function cetakLabelInventaris(Request $request)
    {
        $query = Inventory::with('barang');

        if ($request->has('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        if (Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
            $data = $query->whereHas('barang', function ($q) {
                $q->where('pu', Auth::user()->pu_kd);
            })->get();
        } else {
            $data = $query->where('ruangan_id', Auth::user()->ruangan_id)->get();
        }

        // Opsi 1: Output langsung untuk browser - bagus untuk testing
        if ($request->has('preview') && $request->preview == 'true') {
            return view('inventory::inventaris.cetak-label', compact('data'));
        }

        // Opsi 2: Menggunakan dompdf dengan setting ukuran A4 tapi tanpa margin
        $pdf = PDF::loadView('inventory::inventaris.cetak-label', compact('data'));

        // Setting A4 karena sepertinya printer Anda diset untuk itu berdasarkan screenshot
        $pdf->setPaper('A4', 'portrait');

        // Setting margin minimal untuk memaksimalkan area cetak
        $pdf->setOption([
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'dpi' => 203
        ]);

        return $pdf->stream('label-inventaris.pdf');
    }
}
