<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Permintaan;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PermintaanSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Ambil data yang diperlukan dari database
        $masterBarangs = DB::table('master_barangs')->pluck('id')->toArray();

        // Ambil user dengan ID 4,5,6 beserta ruangan_id dan unit_id mereka
        $users = DB::table('users')
            ->join('ruangans', 'users.ruangan_id', '=', 'ruangans.id')
            ->whereIn('users.id', [4, 5, 6])
            ->select('users.id', 'users.ruangan_id', 'ruangans.unit_id')
            ->get();

        // Generate 50 kode permintaan unik
        $kodePermintaans = [];
        for ($i = 0; $i < 50; $i++) {
            $kodePermintaans[] = 'REQ-' . date('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
        }

        // Generate data permintaan
        foreach ($kodePermintaans as $kodePermintaan) {
            $tanggalPermintaan = $faker->dateTimeBetween('-1 year', 'now');
            $status = $faker->randomElement(['0', '1', '2', '3']); // 0=baru, 1=ditolak, 2=menunggu diambil, 3=selesai

            // Pilih user random dari koleksi users
            $selectedUser = $faker->randomElement($users->toArray());

            // Generate 1-5 items untuk setiap permintaan
            $itemCount = $faker->numberBetween(1, 5);

            for ($i = 0; $i < $itemCount; $i++) {
                Permintaan::create([
                    'kode_permintaan' => $kodePermintaan,
                    'unit_id' => $selectedUser->unit_id,
                    'ruangan_id' => $selectedUser->ruangan_id,
                    'barang_id' => $faker->randomElement($masterBarangs),
                    'jumlah' => $faker->numberBetween(1, 10),
                    'keterangan' => $faker->sentence(),
                    'status' => $status,
                    'tanggal_permintaan' => $tanggalPermintaan,
                    'created_id' => $selectedUser->id,
                    'updated_id' => $selectedUser->id,
                    'created_at' => $tanggalPermintaan,
                    'updated_at' => $tanggalPermintaan,
                ]);
            }
        }
    }
}
