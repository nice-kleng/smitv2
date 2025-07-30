<?php

use App\Http\Controllers\AccountDBController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\LogBookController;
use App\Http\Controllers\MenuManagementController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserManagementController;
use App\Models\JadwalDetail;
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
        Route::get('/dashboard/detail-inventaris', 'getDetailInventaris')->name('dashboard.detail-inventaris');
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password.update');
    Route::get('/api/ruangans-by-unit', [ProfileController::class, 'getRuangansByUnit'])->name('api.ruangans-by-unit');

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

    Route::get('account-db', [AccountDBController::class, 'index'])->name('account-db.index');
    Route::get('account-db-list', [AccountDBController::class, 'list'])->name('account-db.list');
    Route::post('account-db', [AccountDBController::class, 'store'])->name('account-db.store');
    Route::put('account-db/{id}', [AccountDBController::class, 'update'])->name('account-db.update');
    Route::delete('account-db/{id}', [AccountDBController::class, 'destroy'])->name('account-db.destroy');
    Route::get('account-db/{id}', [AccountDBController::class, 'show'])->name('account-db.show');
    Route::resource('log-book', LogBookController::class);

    // Route::resource('jadwal', JadwalController::class)->except(['show']);
    // Route::get('/api/shifts', [JadwalController::class, 'getShifts'])->name('jadwal.shifts');
    // Route::post('jadwal/preview', [JadwalController::class, 'previewSchedule'])->name('jadwal.preview');
    // Jadwal Routes
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        // Basic CRUD
        Route::get('/', [JadwalController::class, 'index'])->name('index');
        Route::get('/create', [JadwalController::class, 'create'])->name('create');
        Route::post('/', [JadwalController::class, 'store'])->name('store');
        Route::get('/{jadwal}', [JadwalController::class, 'show'])->name('show');
        Route::get('/{jadwal}/edit', [JadwalController::class, 'edit'])->name('edit');
        Route::put('/{jadwal}', [JadwalController::class, 'update'])->name('update');
        Route::delete('/{jadwal}', [JadwalController::class, 'destroy'])->name('destroy');

        // Schedule Details
        Route::get('/{jadwal}/details', [JadwalController::class, 'showDetails'])->name('details');

        // Attendance Management
        Route::put('/detail/{detail}/attendance', [JadwalController::class, 'updateAttendance'])->name('detail.attendance');
        Route::post('/attendance/bulk-update', [JadwalController::class, 'bulkUpdateAttendance'])->name('attendance.bulk-update');
        Route::get('/jadwal-detail/{detail}', function (JadwalDetail $detail) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $detail->id,
                    'work_date' => $detail->work_date,
                    'actual_start_time' => $detail->actual_start_time,
                    'actual_end_time' => $detail->actual_end_time,
                    'notes' => $detail->notes,
                    'attendance_status' => $detail->attendance_status
                ]
            ]);
        })->name('jadwal.detail.show');

        // Daily Schedule
        Route::get('/daily/schedule', [JadwalController::class, 'getDailySchedule'])->name('daily.schedule');

        // Reports and Summary
        Route::get('/attendance/summary', [JadwalController::class, 'getAttendanceSummary'])->name('attendance.summary');
        Route::get('/export', [JadwalController::class, 'exportSchedule'])->name('export');

        // AJAX Endpoints
        Route::get('/shifts/list', [JadwalController::class, 'getShifts'])->name('shifts.list');
        Route::post('/preview', [JadwalController::class, 'previewSchedule'])->name('preview');

        Route::post('/test-work-days', [JadwalController::class, 'testWorkDays']);
        // Get shifts with work_days information
        Route::get('/shifts-with-work-days', [JadwalController::class, 'getShiftsWithWorkDays']);
        // Update shift work_days
        Route::put('/shifts/{shift}/work-days', [JadwalController::class, 'updateShiftWorkDays']);
    });

    // API Routes for mobile or external access
    Route::prefix('api/jadwal')->name('api.jadwal.')->group(function () {
        Route::get('/today', function () {
            return app(JadwalController::class)->getDailySchedule(
                new \Illuminate\Http\Request(['date' => now()->format('Y-m-d')])
            );
        })->name('today');

        Route::get('/user/{user}/current', function ($userId) {
            return app(JadwalController::class)->getDailySchedule(
                new \Illuminate\Http\Request([
                    'date' => now()->format('Y-m-d'),
                    'user_id' => $userId
                ])
            );
        })->name('user.current');

        Route::post('/checkin/{detail}', function (\App\Models\JadwalDetail $detail) {
            return app(JadwalController::class)->updateAttendance(
                new \Illuminate\Http\Request(['check_in_time' => now()->format('H:i')]),
                $detail
            );
        })->name('checkin');

        Route::post('/checkout/{detail}', function (\App\Models\JadwalDetail $detail) {
            return app(JadwalController::class)->updateAttendance(
                new \Illuminate\Http\Request(['check_out_time' => now()->format('H:i')]),
                $detail
            );
        })->name('checkout');

        Route::post('/test-work-days', [JadwalController::class, 'testWorkDays']);
        Route::get('/shifts-with-work-days', [JadwalController::class, 'getShiftsWithWorkDays']);
        Route::put('/shifts/{shift}/work-days', [JadwalController::class, 'updateShiftWorkDays']);
    });
});

Route::get('api/master/unit/{unit}/ruangan', [UnitController::class, 'getRuangan'])->name('api.master.unit.ruangan');
