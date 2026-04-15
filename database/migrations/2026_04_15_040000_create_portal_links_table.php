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
        Schema::create('portal_links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('url');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('FontAwesome class e.g. fas fa-server');
            $table->string('image')->nullable()->comment('Path to uploaded image');
            $table->enum('category', ['internal', 'external'])->default('internal');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_links');
    }
};
