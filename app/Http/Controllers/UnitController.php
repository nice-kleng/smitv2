<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny');
        if ($request->ajax()) {
            $data = Unit::orderBy('nama_unit', 'asc')->get();
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

        return view('master.unit');
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
        $this->authorize('create', Unit::class);
        $request->validate([
            'kode_unit' => 'required|string|max:255|unique:units,kode_unit,except,id',
            'nama_unit' => 'required|string|max:255',
        ]);

        Unit::create($request->all());

        return response()->json(['success' => true, 'message' => 'Unit berhasil ditambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        // $this->authorize('update', $unit);
        // return response()->json($unit);
        try {
            $this->authorize('update', $unit);
            return response()->json($unit);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Unit tidak ditemukan', 'status' => 500]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $this->authorize('update', $unit);
        $request->validate([
            'kode_unit' => 'required|string|max:255|unique:units,kode_unit,' . $unit->id,
            'nama_unit' => 'required|string|max:255',
        ]);

        $unit->update($request->all());

        return response()->json(['success' => true, 'message' => 'Unit berhasil diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            $this->authorize('delete', $unit);
            $unit->delete();

            return response()->json(['success' => true, 'message' => 'Unit berhasil dihapus']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Unit tidak ditemukan', 'status' => 500]);
        }
    }

    public function getRuangan(string $unit)
    {
        $ruangan = Ruangan::where('unit_id', $unit)->orderBy('nama_ruangan', 'asc')->get();
        return response()->json($ruangan);
    }
}
