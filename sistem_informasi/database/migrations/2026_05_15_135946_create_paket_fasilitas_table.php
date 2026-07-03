<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_fasilitas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paket_wisata_id')
                ->constrained('paket_wisata')
                ->cascadeOnDelete();

            $table->foreignId('fasilitas_id')
                ->constrained('fasilitas')
                ->cascadeOnDelete();

            $table->integer('jumlah');

            $table->text('keterangan')->nullable();

            $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_fasilitas');
    }
};