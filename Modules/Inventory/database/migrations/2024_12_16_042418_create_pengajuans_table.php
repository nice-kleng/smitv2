<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Mailer\Transport\Dsn;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->string('pu')->nullable();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('barang_id')->constrained('master_barangs');
            $table->integer('harga')->nullable();
            $table->integer('harga_approved')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('jumlah_approved')->nullable();
            $table->date('dikeluarkan_pada')->nullable();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_approved')->nullable();
            $table->date('tanggal_realisasi')->nullable();
            $table->foreignId('approved_id')->nullable()->constrained('users');
            $table->enum('status', ['0', '1', '2', '3'])->default('1')->comment('0: Proses, 1:Ditolak, 2:Disetujui, 3: Barang Datang');
            $table->enum('jenis_pengajuan', ['0', '1'])->default('1')->comment('0: insidetil, 1: rutinitas ');
            // $table->string('keperluan')->nullable();
            $table->string('memo')->nullable();
            $table->string('disposisi')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('keterangan_peninjauan')->nullable();
            $table->foreignId('created_id')->constrained('users');
            $table->foreignId('updated_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
