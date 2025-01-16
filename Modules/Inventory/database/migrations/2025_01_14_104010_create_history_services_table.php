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
        Schema::create('history_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventaris_id')->constrained('inventories');
            $table->string('tempat_service')->nullable();
            $table->text('kerusakan')->nullable();
            $table->integer('biaya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_services');
    }
};
