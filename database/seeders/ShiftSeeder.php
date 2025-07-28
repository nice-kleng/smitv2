<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        Shift::create([
            'name' => 'Pagi',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'work_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'description' => 'Shift pagi Senin-Jumat'
        ]);

        Shift::create([
            'name' => 'Middle',
            'start_time' => '10:00:00',
            'end_time' => '17:00:00',
            'work_days' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']),
            'description' => 'Shift middle Senin-Sabtu mulai jam 10:00'
        ]);
    }
}
