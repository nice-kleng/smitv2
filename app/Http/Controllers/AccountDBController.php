<?php

namespace App\Http\Controllers;

use App\Models\AccountDB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class AccountDBController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('account_db.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Get all accounts as JSON for AJAX.
     */
    public function list(Request $request)
    {
        $query = AccountDB::with('user')->orderBy('created_at', 'desc');
        if (!auth()->user()->hasRole('superadmin')) {
            $query->where('user_id', auth()->user()->id);
        }
        $accounts = $query->get();
        return response()->json($accounts);
    }

    /**
     * Store a newly created resource in storage (AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_url' => 'nullable|string|max:255',
            'username' => 'required|string|max:255',
            'penyedia' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|max:255',
        ]);
        $validated['user_id'] = optional(Auth::user())->id ?? 1; // Sementara, ganti sesuai kebutuhan
        $account = AccountDB::create($validated);
        return response()->json($account, 201);
    }

    /**
     * Show a single account (AJAX).
     */
    public function show($id)
    {
        $account = AccountDB::findOrFail($id);
        return response()->json($account);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccountDB $accountDB)
    {
        //
    }

    /**
     * Update the specified resource in storage (AJAX).
     */
    public function update(Request $request, $id)
    {
        $account = AccountDB::findOrFail($id);
        // Cek kepemilikan data
        if (!auth()->user()->hasRole('superadmin') && $account->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Anda tidak berhak mengedit data ini.'], 403);
        }
        $validated = $request->validate([
            'app_name' => 'string|max:255',
            'app_url' => 'nullable|string|max:255',
            'username' => 'string|max:255',
            'email' => 'string|max:255',
            'penyedia' => 'string|max:255',
            'password' => 'string|max:255',
        ]);
        $account->update($validated);
        return response()->json($account);
    }

    /**
     * Remove the specified resource from storage (AJAX).
     */
    public function destroy($id)
    {
        $account = AccountDB::findOrFail($id);
        // Cek kepemilikan data
        if (!auth()->user()->hasRole('superadmin') && $account->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Anda tidak berhak menghapus data ini.'], 403);
        }
        $account->delete();
        return response()->json(['success' => true]);
    }
}
