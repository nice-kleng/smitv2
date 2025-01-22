<?php

namespace App\Http\Controllers;

use App\Models\LogBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class LogBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LogBook::with('staf');

            // Cek role menggunakan Spatie hasRole
            if (!Auth::user()->hasRole(['superadmin', 'direktur'])) {
                $data->where('user_id', Auth::user()->id);
            }

            $data->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return $row->staf->name ?? '-';
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenis == '0' ? 'Harian' : 'Service';
                })
                ->addColumn('aduan', function ($row) {
                    if ($row->service()->count() > 0) {
                        return '<a href="javascript:void(0)" class="btn btn-sm btn-info show-service" data-id="' . $row->service_id . '">Lihat</a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-sm btn-warning edit-btn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['aduan', 'action'])
                ->make(true);
        }

        return view('settings.users.log_book');
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
        try {
            $logBook = LogBook::with(['service.jenisAduan'])->findOrFail($id);
            return response()->json($logBook);
        } catch (\Exception $e) {
            Log::error('Error fetching log book data: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $logBook = LogBook::find($id);
        return response()->json($logBook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kegiatan' => 'required',
            'keterangan' => 'required',
        ]);
        $logBook = LogBook::find($id);
        $logBook->update([
            'kegiatan' => $request->kegiatan,
            'keterangan' => $request->keterangan,
        ]);
        return response()->json(['success' => true, 'message' => 'Data berhasil diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        LogBook::find($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }
}
