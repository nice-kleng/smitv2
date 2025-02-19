<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermintaanSeeder::class,
        ]);
    }
}
