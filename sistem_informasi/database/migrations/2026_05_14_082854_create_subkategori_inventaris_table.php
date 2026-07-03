<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subkategori_inventaris', function (Blueprint $table) {

            $table->id('id_subkategori');

            $table->unsignedBigInteger('id_kategori');

            $table->string('nama_subkategori');

            $table->timestamps();

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori_inventaris')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subkategori_inventaris');
    }
};