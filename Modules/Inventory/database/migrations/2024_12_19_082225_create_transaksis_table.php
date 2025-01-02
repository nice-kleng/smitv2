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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_id')->constrained('stoks');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->enum('jenis', ['0', '1']);
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaans');
            $table->foreignId('pengajuan_id')->nullable()->constrained('pengajuans');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
