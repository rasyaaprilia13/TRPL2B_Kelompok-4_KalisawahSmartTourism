<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_fasilitas_id')
                  ->nullable()
                  ->constrained('kategori_fasilitas')
                  ->onDelete('set null');

            $table->string('nama_fasilitas');

            $table->enum('tipe_fasilitas', [
                'informasi',
                'paket',
                'sewa'
            ]);

            $table->decimal('harga', 12, 2)->nullable();

            $table->integer('stok')->nullable();

            $table->text('deskripsi')->nullable();

            $table->string('gambar')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};