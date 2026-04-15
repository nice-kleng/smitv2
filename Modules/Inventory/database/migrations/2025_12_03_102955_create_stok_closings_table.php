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
        Schema::create('stok_closings', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 7); // Format: 2024-01
            $table->foreignId('master_barang_id')->constrained('master_barangs')->onDelete('cascade');

            // Data stok
            $table->integer('stok_awal')->default(0)->comment('Stok awal bulan (dari closing bulan lalu)');
            $table->integer('stok_masuk')->default(0)->comment('Total transaksi masuk di bulan ini');
            $table->integer('stok_keluar')->default(0)->comment('Total transaksi keluar di bulan ini');
            $table->integer('stok_akhir_sistem')->default(0)->comment('Stok akhir menurut sistem (awal + masuk - keluar)');

            // Stock Opname
            $table->integer('stok_fisik')->nullable()->comment('Stok hasil cek fisik gudang');
            $table->integer('selisih')->nullable()->comment('Selisih antara stok fisik vs sistem');
            $table->text('keterangan')->nullable()->comment('Alasan jika ada selisih');

            // Status closing
            $table->boolean('is_closed')->default(false)->comment('Status: false=draft, true=closed');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('closed_at')->nullable();

            // Reopening (jika perlu buka kembali)
            $table->foreignId('reopened_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reopened_at')->nullable();
            $table->text('reopen_reason')->nullable();

            $table->timestamps();

            // Index
            $table->unique(['periode', 'master_barang_id'], 'unique_periode_barang');
            $table->index('periode');
            $table->index('is_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_closings');
    }
};
