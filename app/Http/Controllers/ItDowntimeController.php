<?php

namespace App\Http\Controllers;

use App\Models\ItDowntime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ItDowntimeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ItDowntime::with('creator')->orderByDesc('mulai_at')->orderByDesc('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('mulai_at', function ($row) {
                    return $row->mulai_at ? $row->mulai_at->format('d/m/Y H:i') : '-';
                })
                ->editColumn('selesai_at', function ($row) {
                    return $row->selesai_at ? $row->selesai_at->format('d/m/Y H:i') : '-';
                })
                ->addColumn('durasi', function ($row) {
                    if (! $row->mulai_at) {
                        return '-';
                    }
                    $end = $row->selesai_at ?: Carbon::now();
                    $minutes = $row->mulai_at->diffInMinutes($end);

                    return $this->formatDurasi($minutes);
                })
                ->addColumn('status_label', function ($row) {
                    $ongoing = $row->status === 'berlangsung' || ! $row->selesai_at;
                    $label = $ongoing ? 'Berlangsung' : 'Selesai';
                    $class = $ongoing ? 'badge-warning' : 'badge-success';

                    return '<span class="badge '.$class.'">'.$label.'</span>';
                })
                ->addColumn('pelapor', function ($row) {
                    return $row->creator->name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" class="btn btn-sm btn-primary btn-edit mr-1" data-id="'.$row->id.'">Edit</a>';
                    $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'">Hapus</a>';

                    return $btn;
                })
                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }

        return view('it-downtime.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated['created_by'] = Auth::id();
        $validated['status'] = $request->filled('selesai_at') ? 'selesai' : 'berlangsung';

        ItDowntime::create($validated);

        return response()->json(['success' => true, 'message' => 'Data downtime berhasil ditambahkan']);
    }

    public function edit($id)
    {
        $downtime = ItDowntime::findOrFail($id);

        return response()->json([
            'id' => $downtime->id,
            'sistem' => $downtime->sistem,
            'kategori' => $downtime->kategori,
            'mulai_at' => $downtime->mulai_at ? $downtime->mulai_at->format('Y-m-d\TH:i') : null,
            'selesai_at' => $downtime->selesai_at ? $downtime->selesai_at->format('Y-m-d\TH:i') : null,
            'dampak' => $downtime->dampak,
            'penyebab' => $downtime->penyebab,
            'tindakan' => $downtime->tindakan,
            'pic' => $downtime->pic,
            'keterangan' => $downtime->keterangan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $downtime = ItDowntime::findOrFail($id);
        $validated = $this->validateData($request);
        $validated['status'] = $request->filled('selesai_at') ? 'selesai' : 'berlangsung';

        $downtime->update($validated);

        return response()->json(['success' => true, 'message' => 'Data downtime berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $downtime = ItDowntime::findOrFail($id);
        $downtime->delete();

        return response()->json(['success' => true, 'message' => 'Data downtime berhasil dihapus']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'sistem' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'mulai_at' => 'required|date',
            'selesai_at' => 'nullable|date|after_or_equal:mulai_at',
            'dampak' => 'nullable|string',
            'penyebab' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);
    }

    private function formatDurasi(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.' menit';
        }
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        if ($hours < 24) {
            return $hours.' jam'.($mins ? ' '.$mins.' menit' : '');
        }
        $days = intdiv($hours, 24);
        $remHours = $hours % 24;

        return $days.' hari'.($remHours ? ' '.$remHours.' jam' : '');
    }
}
