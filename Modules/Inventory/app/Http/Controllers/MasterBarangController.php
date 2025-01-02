<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use App\Models\Satuan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\MasterBarang;
use Yajra\DataTables\Facades\DataTables;

class MasterBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterBarang::with('satuan', 'kategori')
                ->when(Auth::user()->hasRole('admin'), function ($query) {
                    $query->where('pu', Auth::user()->pu_kd);
                })
                ->orderBy('nama_barang', 'asc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('inventory.master_barang.edit', $row->id) . '" class="btn btn-primary"><i class="fas fa-edit"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('inventory::master_barang.master_barang');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $satuan = Satuan::orderBy('nama_satuan', 'asc')->get();
        $kategori = KategoriBarang::orderBy('nama_kategori', 'asc')->get();
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        return view('inventory::master_barang.form', compact('satuan', 'kategori', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'satuan_id' => 'required|exists:satuans,id',
            'kategori_id' => 'required|exists:kategori_barangs,id',
            'keterangan_barang' => 'nullable|string',
            'harga.*' => 'required|numeric|min:0',
            'stok.*' => 'required|integer|min:0',
            'keterangan.*' => 'nullable|string',
            'is_elektronik' => 'nullable|boolean',
            'jenis' => 'required|in:0,1',
            // Jika ada unit_pu, tambahkan validasi
            'unit_pu' => 'nullable|string',
        ]);

        $master_barang = MasterBarang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan_id' => $request->satuan_id,
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'keterangan' => $request->keterangan_barang ?? 'test',
            'pu' => $request->has('unit_pu') ? $request->unit_pu : Auth::user()->pu_kd,
            'is_elektronik' => $request->is_elektronik,
        ]);

        $stokData = [];
        if ($request->harga && $request->stok) {
            foreach ($request->harga as $key => $harga) {
                $stokData[] = [
                    'harga' => $harga,
                    'stok' => $request->stok[$key],
                    'keterangan' => $request->keterangan[$key],
                    'master_barang_id' => $master_barang->id,
                ];
            }
        }

        if (!empty($stokData)) {
            $master_barang->stoks()->createMany($stokData);
        }

        return redirect()->route('inventory.master_barang.index')->with('success', 'Barang berhasil ditambahkan');
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
        $master_barang = MasterBarang::with('stoks')->find($id);
        $satuan = Satuan::orderBy('nama_satuan', 'asc')->get();
        $kategori = KategoriBarang::orderBy('nama_kategori', 'asc')->get();
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        return view('inventory::master_barang.form', compact('master_barang', 'satuan', 'kategori', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'satuan_id' => 'required|exists:satuans,id',
            'kategori_id' => 'required|exists:kategori_barangs,id',
            'keterangan_barang' => 'nullable|string',
            'harga.*' => 'required|numeric|min:0',
            'stok.*' => 'required|integer|min:0',
            'keterangan.*' => 'nullable|string',
            'is_elektronik' => 'nullable|boolean',
            'jenis' => 'required|in:0,1',
            'unit_pu' => 'nullable|string',
        ]);

        $master_barang = MasterBarang::findOrFail($id);

        $master_barang->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan_id' => $request->satuan_id,
            'kategori_id' => $request->kategori_id,
            'jenis' => $request->jenis,
            'pu' => $request->has('unit_pu') ? $request->unit_pu : Auth::user()->pu_kd,
            'keterangan' => $request->keterangan_barang,
        ]);

        $existingStok = $master_barang->stoks()->get();

        $stokData = [];
        $updatedStokIds = [];

        if ($request->harga && $request->stok) {
            foreach ($request->harga as $index => $harga) {
                if (isset($request->stok[$index])) {
                    $stokId = $request->stok_id[$index] ?? null;
                    $data = [
                        'harga' => $harga,
                        'stok' => $request->stok[$index],
                        'keterangan' => $request->keterangan[$index] ?? null,
                    ];

                    if ($stokId && $existingStok->has($stokId)) {
                        $existingStok[$stokId]->update($data);
                        $updatedStokIds[] = $stokId;
                    } else {
                        $stokData[] = array_merge($data, ['master_barang_id' => $master_barang->id]);
                    }
                }
            }
        }

        if (!empty($stokData)) {
            $master_barang->stoks()->createMany($stokData);
        }

        foreach ($existingStok as $stok) {
            if (!in_array($stok->id, $updatedStokIds)) {
                $stok->delete();
            }
        }

        return redirect()->route('inventory.master_barang.index')->with('success', 'Barang berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        MasterBarang::find($id)->delete();
        return redirect()->route('inventory.master_barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function getStok($id)
    {
        $stok = MasterBarang::find($id)->stoks;
        return response()->json($stok);
    }
}
