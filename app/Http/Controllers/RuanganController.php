<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny');
        if ($request->ajax()) {
            $data = Ruangan::with('unit')->orderBy('nama_ruangan', 'asc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-primary btn-edit" data-id="' . $row->id . '"><i class="fa fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-danger btn-delete" data-id="' . $row->id . '"><i class="fa fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        return view('master.ruangan', compact('units'));
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
            $this->authorize('create');
            $request->validate([
                'unit_id' => 'required|exists:units,id',
                'kode_ruangan' => 'required|string|max:255|unique:ruangans,kode_ruangan,except,id',
                'nama_ruangan' => 'required|string|max:255',
            ]);
            Ruangan::create($request->all());
            return response()->json(['success' => true, 'message' => 'Ruangan berhasil ditambahkan']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan', 500]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruangan $ruangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruangan $ruangan)
    {
        try {
            $this->authorize('updated', $ruangan);
            return response()->json(['success' => true, 'data' => $ruangan]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan', 500]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruangan $ruangan)
    {
        try {
            $this->authorize('update', $ruangan);
            $request->validate([
                'unit_id' => 'required|exists:units,id',
                'kode_ruangan' => 'required|string|max:255|unique:ruangans,kode_ruangan,' . $ruangan->id,
                'nama_ruangan' => 'required|string|max:255',
            ]);
            $ruangan->update($request->all());
            return response()->json(['success' => true, 'message' => 'Ruangan berhasil diubah']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan', 500]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruangan $ruangan)
    {
        try {
            $this->authorize('delete', $ruangan);
            $ruangan->delete();
            return response()->json(['success' => true, 'message' => 'Ruangan berhasil dihapus']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan', 500]);
        }
    }
}
