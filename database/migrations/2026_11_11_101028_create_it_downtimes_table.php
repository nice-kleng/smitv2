<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_downtimes', function (Blueprint $table) {
            $table->id();
            $table->string('sistem');
            $table->string('kategori')->nullable();
            $table->dateTime('mulai_at');
            $table->dateTime('selesai_at')->nullable();
            $table->string('status')->default('berlangsung');
            $table->text('dampak')->nullable();
            $table->text('penyebab')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('pic')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_downtimes');
    }
};
