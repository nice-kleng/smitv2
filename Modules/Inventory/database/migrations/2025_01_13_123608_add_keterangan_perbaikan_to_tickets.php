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
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('keterangan_perbaikan', ['0', '1', '2', '3', '4'])->default('0')->comment('0 = -, 1 = Perbaikan Sendiri, 2 = Pemeliharaan, 3 = Perbaikan dan Pemeliharaan, 4 = Service Luar')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('keterangan_perbaikan');
        });
    }
};
