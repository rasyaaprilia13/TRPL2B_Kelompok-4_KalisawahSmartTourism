<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
        $table->id();
        $table->string('kode_booking')->unique();
        $table->string('nama_pemesan');
        $table->string('no_hp');
        $table->date('tanggal_kunjungan');
        $table->date('tanggal_selesai')->nullable();
        $table->integer('jumlah_hari')->default(1);
        $table->time('jam');
        $table->integer('jumlah_pengunjung');
        $table->integer('jumlah_tiket_tambahan')->default(0);
        $table->decimal('harga_tiket_tambahan', 12, 2)->default(25000);
        $table->decimal('subtotal_tiket_tambahan', 12, 2)->default(0);
        $table->text('catatan')->nullable();
        $table->decimal('total_harga', 12, 2)->default(0);
        $table->decimal('diskon_manual', 12, 2)->default(0);
        $table->decimal('total_harga_final', 12, 2)->default(0);
        $table->enum('status_booking', ['pending', 'dikonfirmasi', 'selesai', 'dibatalkan'])->default('pending');
        $table->enum('status_pembayaran', ['belum_bayar', 'dp', 'lunas', 'menunggu_verifikasi'])->default('belum_bayar');
        $table->date('tanggal_reschedule')->nullable();
        $table->text('alasan_reschedule')->nullable();
        $table->integer('jumlah_reschedule')->default(0);
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
