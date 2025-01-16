<?php

namespace App\Http\Controllers;

use App\Models\LogBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LogBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LogBook::query();
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('direktur')) {
                $data->where('user_id', Auth::user()->id);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return $row->staf->name;
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenis == '0' ? 'Harian' : 'Service';
                })
                ->addColumn('aduan', function ($row) {
                    if ($row->service()->count() > 0) {
                        return '<a href="javascript:void(0)" class="btn btn-sm btn-info" data-id="' . $row->service_id .  '">Lihat</a>';
                    }
                })
                ->addColumn('action', function () {
                    $btn = '<a href="" class="btn btn-sm btn-warning"><i class=fas fa-edit""></i></a>';
                    $btn .= ' <a href="" class="btn btn-sm btn-danger"><i class=fas fa-trash""></i></a>';
                    return $btn;
                })
                ->rawColumns(['aduan', 'action'])
                ->make(true);
        }

        return view('settings.users.lob_book');
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
        $request->validate([
            'kegiatan' => 'required',
            'keterangan' => 'required',
            'jenis' => 'required',
        ]);
        LogBook::create([
            'user_id' => Auth::user()->id,
            'kegiatan' => $request->kegiatan,
            'keterangan' => $request->keterangan,
            'jenis' => '0',
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
