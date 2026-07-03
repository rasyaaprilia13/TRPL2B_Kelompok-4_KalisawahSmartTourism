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
    Schema::create('kategori_paket', function (Blueprint $table) {
    $table->id();
    $table->string('nama_kategori');
    $table->text('deskripsi')->nullable();
    $table->string('gambar')->nullable();
    $table->string('tagline')->nullable();
    $table->string('hero_image')->nullable();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_paket');
    }
};
