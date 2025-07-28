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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->date('start_date'); // Tanggal mulai minggu
            $table->date('end_date'); // Tanggal akhir minggu
            $table->integer('week_number'); // Nomor minggu dalam tahun
            $table->integer('year');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index untuk performa query
            $table->index(['user_id', 'start_date', 'end_date']);
            $table->index(['week_number', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
