<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Controllers\JenisAduanController;
use Modules\Inventory\Http\Controllers\MasterBarangController;
use Modules\Inventory\Http\Controllers\PengajuanController;
use Modules\Inventory\Http\Controllers\PermintaanController;
use Modules\Inventory\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth', 'prefix' => '/inventory', 'as' => 'inventory.'], function () {
    Route::controller(InventoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        // Route::delete('/delete/{id}', 'destroy')->name('delete');

        // History Mutasi
        Route::get('/history-mutasi/{id}', 'historyMutasi')->name('history-mutasi');
        Route::post('/history-mutasi', 'storeHistoryMutasi')->name('store-mutasi');
        Route::get('/get-ruangan', 'getRuanganByUnit')->name('getRuangan');
        Route::delete('/history-mutasi/{id}', 'deleteHistoryMutasi')->name('deleteMutasi');

        // Inventaris unit
        // Route::get('/list', '')->name('unit.list');

        Route::post('/import', 'import')->name('import');
        Route::get('/template', 'downloadTemplate')->name('template');

        Route::get('/cetak-label', 'cetakLabelInventaris')->name('cetak-label');
    });

    Route::controller(MasterBarangController::class)->group(function () {
        Route::get('/master-barang', 'index')->name('master_barang.index');
        Route::get('/master-barang/create', 'create')->name('master_barang.create');
        Route::post('/master-barang', 'store')->name('master_barang.store');
        Route::get('/master-barang/{id}/edit', 'edit')->name('master_barang.edit');
        Route::put('/master-barang/{id}', 'update')->name('master_barang.update');
        Route::delete('/master-barang/{id}', 'destroy')->name('master_barang.destroy');
        Route::get('/master-barang/export', 'export')->name('master_barang.export');
        Route::post('/master-barang/import', 'import')->name('master_barang.import');
    });

    Route::controller(PengajuanController::class)->group(function () {
        Route::get('/pengajuan', 'index')->name('pengajuan.index');
        Route::get('/pengajuan/create', 'create')->name('pengajuan.create');
        Route::post('/pengajuan', 'store')->name('pengajuan.store');
        Route::get('/pengajuan-detail/{id}', 'show')->name('pengajuan.show');
        Route::get('/pengajuan/{prefix}/edit', 'edit')->name('pengajuan.edit');
        Route::put('/pengajuan/{prefix}', 'update')->name('pengajuan.update');
        Route::delete('/pengajuan/{prefix}', 'destroy')->name('pengajuan.destroy');
        Route::get('/pengajuan/{prefix}/approve', 'approve')->name('pengajuan.approve');
        Route::post('/pengajuan/{kode}/process-approval', 'processApproval')->name('pengajuan.process-approval');
        Route::post('/pengajuan/import', 'importFromExcel')->name('pengajuan.import');
        Route::get('/pengajuan/barang-datang/{prefix}', 'barangDatang')->name('pengajuan.barang-datang');
        Route::post('/pengajuan/proses-barang-datang', 'prosesBarangDatang')->name('pengajuan.proses-barang-datang');
    });

    Route::controller(PermintaanController::class)->group(function () {
        Route::get('/permintaan', 'index')->name('permintaan.index');
        Route::get('/permintaan/create', 'create')->name('permintaan.create');
        Route::get('/permintaan-detail/{id}', 'show')->name('permintaan.show');
        Route::post('/permintaan', 'store')->name('permintaan.store');
        Route::get('/permintaan/{id}/edit', 'edit')->name('permintaan.edit');
        Route::put('/permintaan/{id}', 'update')->name('permintaan.update');
        Route::delete('/permintaan/{id}', 'destroy')->name('permintaan.destroy');

        Route::get('/permintaan/{id}/approve', 'approve')->name('permintaan.approve');
        Route::post('/permintaan/approve-proccess', 'prosesApprove')->name('permintaan.approve.proses');
        Route::get('/getInventory/{prefix}', 'getInventory')->name('permintaan.get-inventory');
        Route::post('/proses-pengambilan/{prefix}', 'prosesPengambilan')->name('permintaan.proses-pengambilan');
        Route::get('/unduh-form-permintaan/{prefix}', 'unduhFormPermintaan')->name('permintaan.unduh-form-permintaan');
        Route::get('/history-permintaan', 'history')->name('permintaan.history');
        Route::get('permintaan/history/data', 'historyData')->name('permintaan.history.data');
    });

    Route::group(['prefix' => 'helpdesk', 'as' => 'helpdesk.'], function () {
        Route::resource('/jenis-aduan', JenisAduanController::class)->names('jenis-aduan');
        Route::controller(TicketController::class)->prefix('pengaduan')->group(function () {
            Route::get('/', 'listTicket')->name('ticket.index');
            Route::get('/getTindakan/{id}', 'getTindakan')->name('ticket.get-tindakan');
            Route::put('/tindakan/{id}', 'tindakan')->name('ticket.tindakan');
            Route::get('/rekap-service-luar', 'rekapServiceLuar')->name('ticket.rekapService');
            Route::get('/riwayat-service-teknisi', 'historyServiceTeknisi')->name('ticket.riwayat-service-teknisi');
            Route::get('/export-service', 'exportService')->name('ticket.epxort-service');
        });
    });
});

Route::get('/helpdesk', [TicketController::class, 'index'])->name('helpdesk.index');
Route::post('/helpdesk', [TicketController::class, 'store'])->name('helpdesk.store-ticket');
Route::get('/helpdesk/antrean', [TicketController::class, 'antrean'])->name('helpdesk.antrean');
