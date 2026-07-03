<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_wisata', function (Blueprint $table) {
            $table->id(); // primary key id
            $table->foreignId('kategori_paket_id')->constrained('kategori_paket')->cascadeOnDelete();
            $table->string('nama_paket');
            $table->text('deskripsi');
            $table->decimal('harga', 10, 2);
            $table->integer('kapasitas');
            $table->string('durasi');
            $table->string('status');
            $table->string('gambar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_wisata');
    }
};
