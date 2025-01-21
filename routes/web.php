<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\LogBookController;
use App\Http\Controllers\MenuManagementController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.auth.login');
})->middleware('guest');

Route::controller(AuthenticateController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'authenticate')->name('authenticate');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::prefix('/settings')->name('settings.')->group(function () {
        Route::get('/', function () {
            return view('settings.index');
        })->name('index');

        Route::controller(MenuManagementController::class)->prefix('/menus')->group(function () {
            Route::get('/', 'index')->name('menu.index');
            Route::post('/', 'store')->name('menu.store');
            Route::get('/create', 'create')->name('menu.create');
            Route::get('/{menu}', 'edit')->name('menu.edit');
            Route::put('/{menu}', 'update')->name('menu.update');
            Route::delete('/{menu}', 'destroy')->name('menu.destroy');
            Route::post('/update-order', 'updateOrder')->name('menu.update-order');
        });

        Route::controller(UserManagementController::class)->prefix('/users')->group(function () {
            Route::get('/', 'index')->name('users.index');
            Route::post('/', 'store')->name('users.store');
            Route::get('/create', 'create')->name('users.create');
            Route::get('/{user}', 'edit')->name('users.edit');
            Route::put('/{user}', 'update')->name('users.update');
            Route::delete('/{user}', 'destroy')->name('users.destroy');
            Route::get('/getRole/{user}', 'getRoles')->name('users.roles');
            Route::put('/getRole/{user}', 'updateRoles')->name('users.roles');
            Route::get('/getPermission/{user}', 'getPermissions')->name('users.permissions');
            Route::put('/getPermission/{user}', 'updatePermissions')->name('users.permissions');
        });

        Route::controller(RoleController::class)->prefix('/roles')->group(function () {
            Route::get('/', 'index')->name('role.index');
            Route::get('/create', 'create')->name('role.create');
            Route::post('/', 'store')->name('role.store');
            Route::get('/{role}', 'edit')->name('role.edit');
            Route::put('/{role}', 'update')->name('role.update');
            Route::delete('/{role}', 'destroy')->name('role.destroy');
        });

        Route::controller(PermissionController::class)->prefix('/permissions')->group(function () {
            Route::get('/', 'index')->name('permission.index');
            Route::get('/create', 'create')->name('permission.create');
            Route::post('/', 'store')->name('permission.store');
            Route::get('/{permission}', 'edit')->name('permission.edit');
            Route::put('/{permission}', 'update')->name('permission.update');
            Route::delete('/{permission}', 'destroy')->name('permission.destroy');
            Route::post('/generate', 'generateForModule')->name('permission.generate');
        });
    });

    Route::prefix('/master')->name('master.')->group(function () {
        Route::controller(UnitController::class)->prefix('/unit')->group(function () {
            Route::get('/', 'index')->name('unit.index');
            Route::get('/create', 'create')->name('unit.create');
            Route::post('/', 'store')->name('unit.store');
            Route::get('/{unit}', 'edit')->name('unit.edit');
            Route::put('/{unit}', 'update')->name('unit.update');
            Route::delete('/{unit}', 'destroy')->name('unit.destroy');
            Route::get('/getRuangan/{}', 'getRuangan')->name('unit.getRuangan');;
        });

        Route::controller(RuanganController::class)->prefix('/ruangan')->group(function () {
            Route::get('/', 'index')->name('ruangan.index');
            Route::get('/create', 'create')->name('ruangan.create');
            Route::post('/', 'store')->name('ruangan.store');
            Route::get('/{ruangan}', 'edit')->name('ruangan.edit');
            Route::put('/{ruangan}', 'update')->name('ruangan.update');
            Route::delete('/{ruangan}', 'destroy')->name('ruangan.destroy');
        });

        Route::controller(SatuanController::class)->prefix('/satuan')->group(function () {
            Route::get('/', 'index')->name('satuan.index');
            Route::get('/create', 'create')->name('satuan.create');
            Route::post('/', 'store')->name('satuan.store');
            Route::get('/{satuan}', 'edit')->name('satuan.edit');
            Route::put('/{satuan}', 'update')->name('satuan.update');
            Route::delete('/{satuan}', 'destroy')->name('satuan.destroy');
        });

        Route::resource('/kategoriBarang', KategoriBarangController::class);
    });

    Route::resource('log-book', LogBookController::class);
});

Route::get('api/master/unit/{unit}/ruangan', [UnitController::class, 'getRuangan'])->name('api.master.unit.ruangan');
