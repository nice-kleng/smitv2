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
            'username' => 'superadmin',
            'email' => 'superadmin@smit.com',
            'password' => Hash::make('superadmin'),
            'unit_id' => 1,
            'ruangan_id' => 1,
            'pu_kd' => 'it',
        ]);
        $superadmin->assignRole('superadmin');

        $it = User::create([
            'name' => 'IT',
            'username' => 'user_it',
            'email' => 'it@smit.com',
            'password' => Hash::make('adminit'),
            'unit_id' => 1,
            'ruangan_id' => 1,
            'pu_kd' => 'it',
        ]);
        $it->assignRole('admin');

        $sarpras = User::create([
            'name' => 'Sarpras',
            'username' => 'user_sarpras',
            'email' => 'sarpras@smit.com',
            'password' => Hash::make('sarpras'),
            'unit_id' => 2,
            'ruangan_id' => 2,
            'pu_kd' => 'log',
        ]);
        $sarpras->assignRole('admin');

        // $umum = User::create([
        //     'name' => 'Umum',
        //     'email' => 'umum@smit.com',
        //     'password' => Hash::make('umum'),
        //     'unit_id' => 3,
        //     'ruangan_id' => 3,
        //     'pu_kd' => 'ipsrs',
        // ]);
        // $umum->assignRole('admin');

        $keuangan = User::create([
            'name' => 'Keuangan',
            'email' => 'keuangan@smit.com',
            'username' => 'user_keuangan',
            'password' => Hash::make('keuangan'),
            'unit_id' => 4,
            'ruangan_id' => 4,
            'pu_kd' => '0',
        ]);
        $keuangan->assignRole('keuangan');

        $pemasaran = User::create([
            'name' => 'Pemasaran',
            'username' => 'user_pemasaran',
            'email' => 'pemasaran@gmail.com',
            'password' => Hash::make('pemasaran'),
            'unit_id' => 5,
            'ruangan_id' => 6,
            'pu_kd' => '0',
        ]);

        $pemasaran->assignRole('unit');

        $general = User::create([
            'name' => 'General',
            'username' => 'user_general',
            'email' => 'general@smit.com',
            'password' => Hash::make('general'),
            'unit_id' => 1,
            'pu_kd' => '0',
        ]);
        $general->assignRole('umum');
    }
}
