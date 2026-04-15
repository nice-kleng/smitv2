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
        Schema::table('stok_closings', function (Blueprint $table) {
            // Drop kolom lama master_barang_id
            $table->dropForeign(['master_barang_id']);
            $table->dropColumn('master_barang_id');

            // Tambah kolom stok_id
            $table->foreignId('stok_id')->after('periode')->constrained('stoks')->onDelete('cascade');

            // Tambah kolom harga
            $table->decimal('harga_per_unit', 15, 2)->default(0)->after('stok_akhir_sistem')
                ->comment('Harga per unit dari tabel stoks');

            $table->decimal('total_nilai_awal', 15, 2)->default(0)->after('harga_per_unit')
                ->comment('Stok awal × harga per unit');

            $table->decimal('total_nilai_masuk', 15, 2)->default(0)->after('total_nilai_awal')
                ->comment('Stok masuk × harga per unit');

            $table->decimal('total_nilai_keluar', 15, 2)->default(0)->after('total_nilai_masuk')
                ->comment('Stok keluar × harga per unit');

            $table->decimal('total_nilai_akhir', 15, 2)->default(0)->after('total_nilai_keluar')
                ->comment('Stok akhir × harga per unit');

            // Update unique constraint
            $table->dropUnique('unique_periode_barang');
            $table->unique(['periode', 'stok_id'], 'unique_periode_stok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_closings', function (Blueprint $table) {
            // Kembalikan ke struktur lama
            $table->dropForeign(['stok_id']);
            $table->dropUnique('unique_periode_stok');
            $table->dropColumn([
                'stok_id',
                'harga_per_unit',
                'total_nilai_awal',
                'total_nilai_masuk',
                'total_nilai_keluar',
                'total_nilai_akhir'
            ]);

            // Restore kolom lama
            $table->foreignId('master_barang_id')->after('periode')->constrained('master_barangs')->onDelete('cascade');
            $table->unique(['periode', 'master_barang_id'], 'unique_periode_barang');
        });
    }
};
