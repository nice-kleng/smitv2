<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Inventory\Models\HistoryInventaris;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\Permintaan;
use Modules\Inventory\Models\Transaksi;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permintaans = Permintaan::with(['barang', 'ruangan'])->select(
            DB::raw('substr(kode_permintaan, 1, 16) as kode_prefix'),
            DB::raw('MAX(tanggal_permintaan) as tanggal_permintaan'),
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(created_id) as created_id'),
            DB::raw('MAX(ruangan_id) as ruangan_id')
        )
            ->groupBy('kode_prefix')
            ->orderBy('tanggal_permintaan', 'desc');

        if (Auth::user()->hasRole('unit')) {
            $permintaans->where('created_id', auth()->id())
                ->where('ruangan_id', Auth::user()->ruangan_id)
                ->where('status', '0');
        }

        if (Auth::user()->hasRole('admin')) {
            $permintaans->where('pu', Auth::user()->pu_kd)
                ->where('status', '0')
                ->orWhere('status', '2');
        }

        if (Auth::user()->hasRole('superadmin')) {
            $permintaans->where('status', '3');
        }

        return view('inventory::permintaan.unit.permintaan', ['permintaans' => $permintaans->get()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = MasterBarang::orderBy('nama_barang', 'asc')->get();
        return view('inventory::permintaan.unit.form', ['barangs' => $barangs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jumlah.*' => 'required|numeric',
            'barang_id.*' => 'required|exists:master_barangs,id',
        ]);

        DB::beginTransaction();
        try {
            $kode_permintaan = 'PM' . date('YmdHis');

            foreach ($request->barang_id as $key => $barang) {
                $barangMaster = MasterBarang::find($barang);
                // if ($barangMaster->stok < $request->jumlah[$key]) {
                //     return redirect()->route('inventory.permintaan.index')->with('error', 'Stok ' . $barangMaster->nama_barang . ' tidak mencukupi');
                // }

                $permintaan = new Permintaan();
                $permintaan->kode_permintaan = $kode_permintaan . '-' . str_pad($key + 1, 3, '0', STR_PAD_LEFT);
                $permintaan->pu = $barangMaster->pu;
                $permintaan->barang_id = $barang;
                $permintaan->jumlah = $request->jumlah[$key];
                $permintaan->tanggal_permintaan = date('Y-m-d');
                $permintaan->status = '0';
                $permintaan->keterangan = $request->keperluan[$key];
                $permintaan->ruangan_id = Auth::user()->ruangan_id;
                $permintaan->created_id = Auth::user()->id;
                $permintaan->updated_id = Auth::user()->id;
                $permintaan->save();
            }

            DB::commit();
            return redirect()->route('inventory.permintaan.index')->with('success', 'Permintaan berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return redirect()->route('inventory.permintaan.index')->with('error', 'Permintaan gagal disimpan');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $permintaan = Permintaan::with(['barang', 'ruangan.unit'])
            ->where('kode_permintaan', 'like', "%$id%")
            ->get();
        return response()->json([
            'data' => $permintaan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permintaan = Permintaan::where('kode_permintaan', 'like', "%$id%")->first();
        $this->authorize('update', $permintaan);

        $permintaan = Permintaan::with(['barang', 'ruangan.unit'])
            ->where('kode_permintaan', 'like', "%$id%")
            ->get();
        $barangs = MasterBarang::orderBy('nama_barang', 'asc')->get();
        return view('inventory::permintaan.unit.form', [
            'barangs' => $barangs,
            'permintaan' => $permintaan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permintaan = Permintaan::where('kode_permintaan', 'like', "$id%")->first();
        $this->authorize('update', $permintaan);
        $request->validate([
            'jumlah.*' => 'required|numeric',
            'barang_id.*' => 'required|exists:master_barangs,id',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing permintaan items
            Permintaan::where('kode_permintaan', 'like', "$id%")->delete();

            // Create new permintaan items
            foreach ($request->barang_id as $key => $barang) {
                $barangMaster = MasterBarang::find($barang);

                $permintaan = new Permintaan();
                $permintaan->kode_permintaan = $id . '-' . str_pad($key + 1, 3, '0', STR_PAD_LEFT);
                $permintaan->pu = $barangMaster->pu;
                $permintaan->barang_id = $barang;
                $permintaan->jumlah = $request->jumlah[$key];
                $permintaan->tanggal_permintaan = date('Y-m-d');
                $permintaan->status = '0';
                $permintaan->keterangan = $request->keperluan[$key];
                $permintaan->ruangan_id = Auth::user()->ruangan_id;
                $permintaan->created_id = Auth::user()->id;
                $permintaan->updated_id = Auth::user()->id;
                $permintaan->save();
            }

            DB::commit();
            return redirect()->route('inventory.permintaan.index')->with('success', 'Permintaan berhasil diupdate');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('inventory.permintaan.index')->with('error', 'Permintaan gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', Permintaan::class);
        DB::beginTransaction();
        try {
            Permintaan::where('kode_permintaan', 'like', "%$id%")->delete();
            DB::commit();
            return redirect()->route('inventory.permintaan.index')->with('success', 'Permintaan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('inventory.permintaan.index')->with('error', 'Permintaan gagal dihapus');
        }
    }

    public function approve($id)
    {
        $this->authorize('approve', Permintaan::class);

        $permintaan = Permintaan::with(['barang', 'ruangan.unit'])
            ->where('kode_permintaan', 'like', "%$id%")
            ->get();

        return view('inventory::permintaan.admin.form_approval', [
            'permintaan' => $permintaan,
            'kode' => $id
        ]);
    }

    /**
     * Proses approvsl permintaan
     */
    public function prosesApprove(Request $request)
    {
        $this->authorize('approve', Permintaan::class);
        // Validate basic required fields
        $request->validate([
            'permintaan_id' => 'required|array',
            'permintaan_id.*' => 'required|exists:permintaans,id',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:master_barangs,id',
        ]);

        DB::beginTransaction();
        try {
            $approved_items = $request->approved_items ?? [];

            foreach ($request->permintaan_id as $key => $item) {
                $permintaan = Permintaan::findOrFail($item);

                if (in_array((string)$key, $approved_items)) {
                    if (empty($request->stok_id[$key]) || empty($request->jumlah_approve[$key])) {
                        return redirect()->route('inventory.permintaan.index')->with('error', 'Stok dan jumlah harus diisi untuk item yang disetujui');
                    }

                    $barang = MasterBarang::findOrFail($request->barang_id[$key]);
                    $stok = $barang->stoks()->findOrFail($request->stok_id[$key]);

                    if ($stok->stok < $request->jumlah_approve[$key]) {
                        return redirect()->route('inventory.permintaan.index')->with('error', "Stok {$barang->nama_barang} tidak mencukupi");
                    }

                    $stok->stok -= $request->jumlah_approve[$key];
                    $stok->save();

                    // Update permintaan and trigger approved observer
                    $permintaan->status = '2';
                    $permintaan->jumlah_approve = $request->jumlah_approve[$key];
                    $permintaan->tanggal_approve = date('Y-m-d');
                    $permintaan->approve_id = Auth::user()->id;
                    $permintaan->save();

                    // This will trigger the approved observer
                    event('eloquent.approved: ' . get_class($permintaan), [$permintaan]);

                    Transaksi::create([
                        'stok_id' => $stok->id,
                        'jumlah' => $request->jumlah_approve[$key],
                        'keterangan' => $request->keterangan[$key] ?? '',
                        'permintaan_id' => $permintaan->id,
                        'jenis' => '0',
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id
                    ]);
                } else {
                    $permintaan->update([
                        'status' => '1',
                        'tanggal_approve' => date('Y-m-d'),
                        'approve_id' => Auth::user()->id
                    ]);
                }
            }

            DB::commit();
            return redirect()
                ->route('inventory.permintaan.index')
                ->with('success', 'Permintaan berhasil diproses');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function getInventory(string $prefix)
    {
        $permintaan = Permintaan::with(['barang', 'ruangan.unit', 'transaksi.stok'])
            ->whereHas('barang', function ($q) {
                $q->where('jenis', '1');
            })
            ->where('kode_permintaan', 'like', "%$prefix%")
            ->where('status', '2')
            ->get();


        return response()->json([
            'data' => view('inventory::permintaan.unit.table_response', ['permintaan' => $permintaan, 'prefix' => $prefix])->render()
        ]);
    }

    public function prosesPengambilan(Request $request, string $prefix)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'penerima' => 'required',
            ]);

            $permintaan = Permintaan::where('kode_permintaan', 'like', "%$prefix%")
                ->where('status', '2')
                ->get();
            $ruangan_peminta_id = $permintaan->first()->ruangan_id;
            $unit_peminta_id = $permintaan->first()->ruangan->unit_id;
            $kdRuangan = $permintaan->first()->ruangan->kode_ruangan;

            foreach ($permintaan as $item) {
                $item->status = '3';
                $item->penerima = $request->penerima;
                // $item->tanggal_pengambilan = date('Y-m-d');
                $item->save();
            }

            if ($request->has('barang_id')) {
                foreach ($request->barang_id as $key => $barang_id) {
                    for ($i = 0; $i < ($request->jumlah_approved[$key]); $i++) {
                        $barang = MasterBarang::find($barang_id);
                        $nobarang = str_pad(Inventory::where('barang_id', $barang_id)->count() + 1, 3, '0', STR_PAD_LEFT);
                        $kdUnit = Str::upper(Auth::user()->pu_kd);
                        $isElektronik = $barang->is_elektronik;
                        $kdBarang = $barang->kode_barang;
                        $tahun_pengadaan = date('Y');

                        $inventory = new Inventory();
                        $inventory->kode_barang = "$kdUnit/$isElektronik/$kdBarang/$nobarang/$kdRuangan/$tahun_pengadaan";
                        $inventory->no_barang = $nobarang;
                        $inventory->barang_id = $barang_id;
                        $inventory->ruangan_id = $ruangan_peminta_id;
                        $inventory->harga_beli = $request->harga_beli[$key];
                        $inventory->satuan = $barang->satuan->nama_satuan;
                        $inventory->merk = $request->merk[$i] ?? '';
                        $inventory->type = $request->type[$i] ?? '';
                        $inventory->serial_number = $request->serial_number[$i] ?? '';
                        $inventory->spesifikasi = $request->spesifikasi[$i] ?? '';
                        $inventory->kepemilikan = 'Rs';
                        $inventory->save();

                        HistoryInventaris::create([
                            'inventory_id' => $inventory->id,
                            'unit_id' => $unit_peminta_id,
                            'ruangan_id' => $ruangan_peminta_id,
                            'kondisi' => '2',
                            'tanggal_mutasi' => date('Y-m-d'),
                            'keterangan' => 'Pengambilan barang',
                            'created_id' => Auth::user()->id,
                            'updated_id' => Auth::user()->id
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('inventory.permintaan.index')->with('success', 'Pengambilan barang berhasil diproses');
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            dd($th);
            return redirect()->route('inventory.permintaan.index')->with('error', 'Pengambilan barang gagal diproses');
        }
    }

    /** *
     * Riwayat permintaan
     */

    public function history()
    {
        $history = Permintaan::with(['barang', 'ruangan'])->select(
            DB::raw('substr(kode_permintaan, 1, 16) as kode_prefix'),
            DB::raw('MAX(tanggal_permintaan) as tanggal_permintaan'),
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(created_id) as created_id'),
            DB::raw('MAX(ruangan_id) as ruangan_id')
        )
            ->groupBy('kode_prefix')
            ->orderBy('tanggal_permintaan', 'desc');

        if (Auth::user()->hasRole('unit')) {
            $history->where('created_id', auth()->id())
                ->where('ruangan_id', Auth::user()->ruangan_id)
                ->where('status', '!=', '0');
        }

        if (Auth::user()->hasRole('admin')) {
            $history->where('pu', Auth::user()->pu_kd)
                ->where('status', '!=', '0');
        }

        if (Auth::user()->hasRole('superadmin')) {
            $history->where('status', '!=', '0');
        }

        return view('inventory::permintaan.unit.history', ['history' => $history->get()]);
    }

    public function unduhFormPermintaan(string $kode)
    {
        try {
            $permintaan = Permintaan::with(['barang', 'ruangan.unit', 'transaksi.stok', 'created_by', 'approve'])
                ->where('kode_permintaan', 'like', "%$kode%")
                ->get();

            if ($permintaan->isEmpty()) {
                return redirect()->back()->with('error', 'Data permintaan tidak ditemukan');
            }

            $pdf = PDF::loadView('inventory::permintaan.pdf.form_permintaan', [
                'permintaan' => $permintaan,
                'kode' => $kode
            ]);

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            return $pdf->stream('form_permintaan_' . $kode . '.pdf');
        } catch (\Exception $e) {
            Log::error('PDF Generation Error: ' . $e->getMessage());
            dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh PDF: ' . $e->getMessage());
        }
    }

    private function generateInitials($name)
    {
        $words = explode(' ', strtoupper($name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= $word[0];
            }
        }

        return $initials;
    }
}
