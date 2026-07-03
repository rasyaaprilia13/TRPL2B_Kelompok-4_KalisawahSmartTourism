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
            Schema::create('jenis_inventaris', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_subkategori');
            $table->string('nama_barang');
            $table->text('spesifikasi')->nullable();

            $table->timestamps();

            $table->foreign('id_subkategori')
                ->references('id_subkategori')
                ->on('subkategori_inventaris')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_inventaris');
    }
};
