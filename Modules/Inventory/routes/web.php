<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Http\Controllers\MasterBarangController;
use Modules\Inventory\Http\Controllers\PengajuanController;
use Modules\Inventory\Http\Controllers\PermintaanController;

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
    // Route::resource('inventory', InventoryController::class)->names('inventory');
    Route::controller(MasterBarangController::class)->group(function () {
        Route::get('/master-barang', 'index')->name('master_barang.index');
        Route::get('/master-barang/create', 'create')->name('master_barang.create');
        Route::post('/master-barang', 'store')->name('master_barang.store');
        Route::get('/master-barang/{id}/edit', 'edit')->name('master_barang.edit');
        Route::put('/master-barang/{id}', 'update')->name('master_barang.update');
        Route::delete('/master-barang/{id}', 'destroy')->name('master_barang.destroy');
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
    });
});
