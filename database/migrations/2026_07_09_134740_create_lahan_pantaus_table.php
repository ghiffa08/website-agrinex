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
        Schema::create('lahan_pantaus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lahan', 100);
            $table->string('lokasi', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lahan_pantaus');
    }
};
