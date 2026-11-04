<?php

namespace Tests\Support;

use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\Satuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Str;
use Modules\Inventory\Models\Inventory;
use Modules\Inventory\Models\JenisAduan;
use Modules\Inventory\Models\MasterBarang;
use Modules\Inventory\Models\Ticket;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class HelpdeskFixtures
{
    public static function user(string $role = 'teknisi', bool $withHelpdeskPermission = true): User
    {
        $roleModel = Role::findOrCreate($role);
        $permission = Permission::findOrCreate('manage-data-pengaduan');

        if ($withHelpdeskPermission) {
            $roleModel->givePermissionTo($permission);
        }

        $unit = Unit::create([
            'kode_unit' => 'U'.Str::upper(Str::random(5)),
            'nama_unit' => 'Unit '.Str::upper(Str::random(4)),
        ]);

        $user = User::create([
            'name' => 'User '.Str::upper(Str::random(4)),
            'username' => 'user'.Str::lower(Str::random(8)),
            'email' => Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'unit_id' => $unit->id,
            'pu_kd' => 'it',
        ]);

        $user->assignRole($roleModel);

        return $user;
    }

    public static function ticket(array $attributes = []): Ticket
    {
        $unit = Unit::create([
            'kode_unit' => 'U'.Str::upper(Str::random(5)),
            'nama_unit' => 'ICU',
        ]);
        $ruangan = Ruangan::create([
            'unit_id' => $unit->id,
            'kode_ruangan' => 'R'.Str::upper(Str::random(5)),
            'nama_ruangan' => 'Ruang 2',
        ]);
        $jenis = JenisAduan::create(['nama_jenis' => 'Jaringan']);

        return Ticket::create(array_merge([
            'kd_ticket' => 'T'.Str::upper(Str::random(8)),
            'ruangan_id' => $ruangan->id,
            'jenis_aduan_id' => $jenis->id,
            'detail_aduan' => 'AC tidak dingin',
            'status' => '0',
        ], $attributes));
    }

    public static function inventory(Ruangan $ruangan): Inventory
    {
        $satuan = Satuan::create(['nama_satuan' => 'Unit']);
        $kategori = KategoriBarang::create(['nama_kategori' => 'Elektronik']);
        $barang = MasterBarang::create([
            'kode_barang' => 'BRG'.Str::upper(Str::random(4)),
            'nama_barang' => 'AC',
            'satuan_id' => $satuan->id,
            'kategori_id' => $kategori->id,
            'keterangan' => '-',
        ]);

        return Inventory::create([
            'barang_id' => $barang->id,
            'ruangan_id' => $ruangan->id,
            'status' => '2',
        ]);
    }
}
