<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\Pengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Pengajuan::select(
            DB::raw('substr(pengajuans.kode_pengajuan, 1, 16) as kode_prefix'),
            DB::raw('MAX(pengajuans.tanggal_pengajuan) as tanggal_pengajuan'),
            DB::raw('MAX(pengajuans.status) as status'),
            DB::raw('MAX(pengajuans.created_at) as created_at'),
            DB::raw('MAX(u.name) as created_by'),
            DB::raw('MAX(un.nama_unit) as unit_name'),
            DB::raw('MAX(k.nama_kategori) as kategori_barang'),
            DB::raw('GROUP_CONCAT(pengajuans.status) as status_group')
        )
            ->join('users as u', 'pengajuans.created_id', '=', 'u.id')
            ->join('units as un', 'pengajuans.unit_id', '=', 'un.id')
            ->join('master_barangs as mb', 'pengajuans.barang_id', '=', 'mb.id')
            ->join('kategori_barangs as k', 'mb.kategori_id', '=', 'k.id')
            ->groupBy('kode_prefix');

        if (Auth::user()->hasRole('admin')) {
            $data->where('pengajuans.unit_id', Auth::user()->unit_id)
                ->whereNotNull('pengajuans.created_id')
                // ->havingRaw("GROUP_CONCAT(pengajuans.status) LIKE '%2%'");
                ->whereIn('pengajuans.status', ['0', '2']);
        }

        if (Auth::user()->hasRole('keuangan')) {
            $data->whereNotNull('pengajuans.created_id')
                ->where('pengajuans.status', '0');
        }

        if (Auth::user()->hasRole('superadmin')) {
            $data->whereNotNull('pengajuans.created_id');
        }

        $data->orderBy('created_at', 'desc');

        return view('inventory::pengajuan.unit.pengajuan', [
            'data' => $data->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $masterBarang = MasterBarang::orderBy('nama_barang', 'asc')->get();
        return view('inventory::pengajuan.unit.form', ['masterBarang' => $masterBarang]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jumlah.*' => 'required|numeric',
            'barang_id' => 'required|exists:master_barangs,id',
        ]);

        try {
            $baseKode = 'PGJ-' . date('dmyhis');

            $pengajuans = [];
            foreach ($request->barang_id as $key => $barangId) {
                // $kode = $baseKode . str_pad($key, 3, '0', STR_PAD_LEFT);
                $pengajuans[] = [
                    'kode_pengajuan' => $baseKode . str_pad($key + 1, 3, '0', STR_PAD_LEFT),
                    'unit_id' => Auth::user()->unit_id,
                    'pu' => Auth::user()->pu_kd,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$key],
                    'jumlah' => $request->jumlah[$key],
                    'jenis_pengajuan' => '1',
                    'tanggal_pengajuan' => date('Y-m-d'),
                    'status' => '0',
                    'keterangan' => $request->keterangan[$key],
                    'created_id' => Auth::user()->id,
                    'updated_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Pengajuan::insert($pengajuans);
            return redirect()->route('inventory.pengajuan.index')->with('success', 'Pengajuan berhasil disimpan');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $pengajuan = Pengajuan::with(['barang', 'unit'])->where('kode_pengajuan', 'like', "%$id%")->get();

        return response()->json($pengajuan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode)
    {
        $pengajuan = Pengajuan::where('kode_pengajuan', 'like', "%$kode%")->get();
        $masterBarang = MasterBarang::orderBy('nama_barang', 'asc')->get();
        return view('inventory::pengajuan.unit.form', [
            'pengajuan' => $pengajuan,
            'masterBarang' => $masterBarang
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kode)
    {
        $request->validate([
            'jumlah.*' => 'required|numeric',
            'barang_id' => 'required|exists:master_barangs,id',
        ]);

        DB::beginTransaction();
        try {
            $pengajuans = Pengajuan::where('kode_pengajuan', 'like', "%$kode%")->get();
            foreach ($pengajuans as $pengajuan) {
                $pengajuan->delete();
            }

            $baseKode = 'PGJ-' . date('dmyhis');

            $newPengajuans = [];
            foreach ($request->barang_id as $key => $barangId) {
                // $kode_new = $baseKode . str_pad($key + 1, 3, '0', STR_PAD_LEFT);
                $newPengajuans[] = [
                    'kode_pengajuan' => $baseKode . str_pad($key + 1, 3, '0', STR_PAD_LEFT),
                    'unit_id' => Auth::user()->unit_id,
                    'pu' => Auth::user()->pu_kd,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$key],
                    'jumlah' => $request->jumlah[$key],
                    'jenis_pengajuan' => '1',
                    'tanggal_pengajuan' => date('Y-m-d'),
                    'status' => '0',
                    'keterangan' => $request->keterangan[$key],
                    'created_id' => Auth::user()->id,
                    'updated_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Pengajuan::insert($newPengajuans);
            DB::commit();
            return redirect()->route('inventory.pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return redirect()->route('inventory.pengajuan.index')->with('error', 'Pengajuan gagal diperbarui');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengajuan = Pengajuan::find($id);
        $pengajuan->delete();
        return redirect()->route('inventory.pengajuan.index')->with('success', 'Pengajuan berhasil dihapus');
    }

    public function approve($kode)
    {
        $pengajuans = Pengajuan::with(['barang', 'unit'])
            ->where('kode_pengajuan', 'like', "%$kode%")
            ->where('status', '0') // Only allow approval for pending submissions
            ->get();

        if ($pengajuans->isEmpty()) {
            return redirect()->route('inventory.pengajuan.index')
                ->with('error', 'Data pengajuan tidak ditemukan atau sudah diproses');
        }

        return view('inventory::pengajuan.keuangan.form_approval', [
            'pengajuans' => $pengajuans,
            'kode' => $kode
        ]);
    }

    public function processApproval(Request $request, $kode)
    {
        $request->validate([
            'harga_approve.*' => 'required|numeric|min:0',
            'jumlah_approve.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pengajuans = Pengajuan::where('kode_pengajuan', 'like', "%$kode%")
                ->where('status', '0')
                ->get();

            if ($pengajuans->isEmpty()) {
                throw new \Exception('Data pengajuan tidak ditemukan atau sudah diproses');
            }

            foreach ($pengajuans as $pengajuan) {
                // $isApproved = isset($request->approve[$pengajuan->id]);
                $isApproved = in_array($pengajuan->id, $request->approve ?? []);

                // Update with approval data
                $pengajuan->update([
                    'status' => $isApproved ? '2' : '1', // 2 = approved, 1 = rejected
                    'harga_approve' => $isApproved ? ($request->harga_approve[$pengajuan->id] ?? 0) : 0,
                    // 'harga_approve' => $request->harga_apparove[$pengajuan->id] ?? 0,
                    'jumlah_approve' => $isApproved ? ($request->jumlah_approve[$pengajuan->id] ?? 0) : 0,
                    // 'jumlah_approve' => $request->jumlah_approve[$pengajuan->id] ?? 0,
                    'updated_id' => Auth::user()->id,
                    'approved_at' => now(),
                    'approved_by' => Auth::user()->id,
                    'keterangan_peninjauan' => $request->keterangan_approve[$pengajuan->id] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.pengajuan.index')
                ->with('success', 'Pengajuan berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventory.pengajuan.index')
                ->with('error', 'Gagal memproses pengajuan: ' . $e->getMessage());
        }
    }

    public function importFromExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file format'], 422);
        }

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $importedData = [];
            foreach ($rows as $row) {
                if (!empty($row[0])) { // Check if id_barang is not empty
                    $importedData[] = [
                        'id_barang' => $row[0],
                        'jumlah' => $row[1] ?? 0,
                        'harga' => $row[2] ?? 0,
                        'keterangan' => $row[3] ?? ''
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $importedData
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing file: ' . $e->getMessage()], 500);
        }
    }
}
