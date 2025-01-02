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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permintaan');
            $table->string('pu')->default('log');
            $table->foreignId('barang_id')->constrained('master_barangs');
            $table->integer('jumlah');
            $table->integer('jumlah_approve')->nullable();
            $table->date('tanggal_permintaan');
            $table->date('tanggal_approve')->nullable();
            $table->enum('status', ['0', '1', '2', '3'])->default('0');
            $table->text('keterangan')->nullable();
            $table->string('penerima')->nullable();
            $table->foreignId('ruangan_id')->constrained('ruangans');
            $table->foreignId('approve_id')->nullable()->constrained('users');
            $table->foreignId('created_id')->constrained('users');
            $table->foreignId('updated_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaans');
    }
};
