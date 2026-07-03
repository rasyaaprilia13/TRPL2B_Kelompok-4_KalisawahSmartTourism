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
            Schema::create('pengeluaran_operasional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori')->constrained('kategori_pengeluaran');
            $table->text('keterangan');
            $table->decimal('jumlah_uang', 10, 2);
            $table->date('tanggal_pengeluaran');
            $table->string('bukti_pengeluaran')->nullable();
            $table->string('dicatat_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_operasional');
    }
};
