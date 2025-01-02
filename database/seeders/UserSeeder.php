<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@smit.com',
            'password' => Hash::make('superadmin'),
            'unit_id' => 1,
            'ruangan_id' => 1,
            'pu_kd' => 'it',
        ]);
        $superadmin->assignRole('superadmin');

        $it = User::create([
            'name' => 'IT',
            'email' => 'it@smit.com',
            'password' => Hash::make('adminit'),
            'unit_id' => 1,
            'ruangan_id' => 1,
            'pu_kd' => 'it',
        ]);
        $it->assignRole('admin');

        $sarpras = User::create([
            'name' => 'Sarpras',
            'email' => 'sarpras@smit.com',
            'password' => Hash::make('sarpras'),
            'unit_id' => 2,
            'ruangan_id' => 2,
            'pu_kd' => 'log',
        ]);
        $sarpras->assignRole('admin');

        $umum = User::create([
            'name' => 'Umum',
            'email' => 'umum@smit.com',
            'password' => Hash::make('umum'),
            'unit_id' => 3,
            'ruangan_id' => 3,
            'pu_kd' => 'ipsrs',
        ]);
        $umum->assignRole('admin');

        $keuangan = User::create([
            'name' => 'Keuangan',
            'email' => 'keuangan@smit.com',
            'password' => Hash::make('keuangan'),
            'unit_id' => 4,
            'ruangan_id' => 4,
            'pu_kd' => '0',
        ]);
        $keuangan->assignRole('keuangan');

        $pemasaran = User::create([
            'name' => 'Pemasaran',
            'email' => 'pemasaran@gmail.com',
            'password' => Hash::make('pemasaran'),
            'unit_id' => 5,
            'ruangan_id' => 5,
            'pu_kd' => '0',
        ]);

        $pemasaran->assignRole('unit');
    }
}
