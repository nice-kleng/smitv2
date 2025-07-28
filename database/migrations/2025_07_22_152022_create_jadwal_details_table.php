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
        Schema::create('jadwal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('jadwals')->onDelete('cascade');
            $table->date('work_date');
            $table->string('day_name'); // monday, tuesday, etc
            $table->time('actual_start_time')->nullable(); // Untuk tracking kehadiran
            $table->time('actual_end_time')->nullable(); // Untuk tracking kehadiran
            $table->enum('attendance_status', ['hadir', 'tidak hadir', 'terlambat', 'pulang_awal'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['schedule_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_details');
    }
};
