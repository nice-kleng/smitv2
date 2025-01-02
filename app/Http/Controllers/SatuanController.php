<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $satuan = Satuan::orderBy('nama_satuan', 'asc')->get();
            return DataTables::of($satuan)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" class="btn btn-primary btn-edit" data-id="' . $row->id . '">Edit</a> <a href="javascript:void(0)" class="btn btn-danger btn-delete" data-id="' . $row->id . '">Delete</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.satuan');
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
            $this->authorize('create', Satuan::class);
            $satuan = new Satuan();
            $satuan->nama_satuan = $request->nama_satuan;
            $satuan->pu = Auth::user()->pu_kd;
            $satuan->save();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus ruangan'], 403);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Terjadi masalah di server'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Satuan $satuan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satuan $satuan)
    {
        try {
            $this->authorize('update', $satuan);
            return response()->json(['success' => true, 'data' => $satuan]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengubah data satuan'], 403);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Terjadi masalah di server'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $satuan)
    {
        try {
            $this->authorize('update', $satuan);
            $satuan->nama_satuan = $request->nama_satuan;
            $satuan->save();
            return response()->json(['success' => true, 'message' => 'Data berhasil diubah']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk mengubah data satuan'], 403);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Terjadi masalah di server'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satuan $satuan)
    {
        try {
            $this->authorize('delete', $satuan);
            $satuan->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus data satuan'], 403);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Terjadi masalah di server'], 500);
        }
    }
}
