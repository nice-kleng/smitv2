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
            $table->string('day_name'); // monday, tuesday, etc.
            $table->datetime('actual_start_time')->nullable();
            $table->datetime('actual_end_time')->nullable();
            $table->enum('attendance_status', [
                'scheduled',
                'present',
                'absent',
                'late',
                'early_leave',
                'overtime'
            ])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['schedule_id', 'work_date']);
            $table->index('work_date');
            $table->index('attendance_status');

            // Unique constraint to prevent duplicate entries
            $table->unique(['schedule_id', 'work_date']);
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
