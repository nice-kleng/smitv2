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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('kd_ticket');
            $table->unsignedBigInteger('inventaris_id')->nullable();
            $table->unsignedBigInteger('teknisi_id')->nullable();
            $table->unsignedBigInteger('jenis_aduan_id')->nullable();
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->string('detail_aduan')->nullable();
            $table->string('tindak_lanjut')->nullable();
            $table->enum('status', ['0', '1'])->default('0');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('inventaris_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('teknisi_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jenis_aduan_id')->references('id')->on('jenis_aduans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
