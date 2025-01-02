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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->nullable();
            $table->integer('no_barang')->nullable();
            $table->foreignId('barang_id')->constrained('master_barangs');
            $table->foreignId('ruangan_id')->constrained('ruangans');
            $table->integer('harga_beli')->nullable();
            $table->string('satuan')->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('tahun_pengadaan')->nullable();
            $table->enum('status', ['0', '1', '2'])->default('2');
            $table->string('catatan')->nullable();
            $table->string('kepemilikan')->nullable();
            $table->date('tgl_penghapusan')->nullable();
            $table->foreignId('penghapus_id')->nullable();
            $table->string('alasan_penghapusan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
