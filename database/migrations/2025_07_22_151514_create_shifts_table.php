<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'Pagi', 'Middle'
            $table->time('start_time'); // 08:00 untuk pagi, 10:00 untuk middle
            $table->time('end_time'); // 17:00 untuk pagi, 17:00 untuk middle
            $table->json('work_days'); // ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] untuk pagi, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] untuk middle
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
