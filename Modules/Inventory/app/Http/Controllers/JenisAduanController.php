<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Models\JenisAduan;
use Yajra\DataTables\DataTables;

class JenisAduanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisAduan::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="delete btn btn-sm btn-danger">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('inventory::helpdesk.jenis_aduan.jenis_aduan');
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
            'nama_jenis' => 'required'
        ]);

        JenisAduan::create(['nama_jenis' => $request->nama_jenis]);
        return response()->json([
            'success' => 'Data berhasil disimpan'
        ]);
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
        $jenis = JenisAduan::find($id);
        return response()->json([
            'data' => $jenis
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenis' => 'required'
        ]);

        JenisAduan::find($id)->update($request->all());
        return response()->json([
            'success' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        JenisAduan::find($id)->delete();
        return response()->json([
            'success' => 'Data berhasil dihapus'
        ]);
    }
}
