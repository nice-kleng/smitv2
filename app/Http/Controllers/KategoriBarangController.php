<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class KategoriBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kategori = KategoriBarang::when(
                Auth::user()->hasRole('admin'),
                function ($query) {
                    $query->where('pu', Auth::user()->pu_kd);
                }
            )->orderBy('nama_kategori', 'asc')->get();
            return DataTables::of($kategori)
                ->addIndexColumn()
                ->addColumn('action', function ($kategori) {
                    $btn = "<a href='javascript:void(0)' class='btn btn-warning btn-sm btn-edit' data-id='" . $kategori->id . "'><i class='fa fa-edit'></i></a>";
                    $btn .= " <a href='javascript:void(0)' class='btn btn-danger btn-sm btn-delete' data-id='" . $kategori->id . "'><i class='fas fa-trash'></i></a>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.kategori');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_kategori' => 'required|string|max:255',
            ]);
            $request->merge(['pu' => Auth::user()->pu_kd]);
            KategoriBarang::create($request->all());
            return redirect()->route('master.kategoriBarang.index')->with('success', 'Kategori berhasil ditambahkan');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('master.kategoriBarang.index')->with('error', 'Anda tidak memiliki izin untuk menambahkan kategori barang');
        } catch (\Throwable $th) {
            return redirect()->route('master.kategoriBarang.index')->with('error', 'Kategori gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriBarang $kategoriBarang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriBarang $kategoriBarang)
    {
        try {
            $this->authorize('update', $kategoriBarang);
            return response()->json(['success' => true, 'data' => $kategoriBarang]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengubah kategori barang'], 403);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Terjadi masalah di server'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriBarang $kategoriBarang)
    {
        try {
            $this->authorize('update', $kategoriBarang);
            $kategoriBarang->nama_kategori = $request->nama_kategori;
            $kategoriBarang->save();
            return redirect()->route('master.kategoriBarang.index')->with('success', 'Kategori berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('master.kategoriBarang.index')->with('error', 'Kategori gagal diubah');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('master.kategoriBarang.index')->with('error', 'Anda tidak memiliki izin untuk mengubah kategori barang');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriBarang $kategoriBarang)
    {
        try {
            $this->authorize('delete', $kategoriBarang);
            $kategoriBarang->delete();
            return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Kategori gagal dihapus', 'error' => $th->getMessage()]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus kategori barang'], 403);
        }
    }
}
