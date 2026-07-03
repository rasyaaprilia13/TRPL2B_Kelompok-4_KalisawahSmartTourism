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
                Schema::create('inventaris_perunit', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('id_jenis_inventaris');
                $table->unsignedBigInteger('id_lokasi');

                $table->string('kode_barang')->unique();
                $table->date('tanggal_beli');
                $table->decimal('harga_beli', 15, 2);
                $table->string('sumber_pembelian')->nullable();

                $table->string('kondisi_unit');

                $table->timestamps();

                $table->foreign('id_jenis_inventaris')
                    ->references('id')
                    ->on('jenis_inventaris')
                    ->onDelete('cascade');

                $table->foreign('id_lokasi')
                    ->references('id_lokasi')
                    ->on('lokasi_penyimpanan')
                    ->onDelete('restrict');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_perunit');
    }
};
