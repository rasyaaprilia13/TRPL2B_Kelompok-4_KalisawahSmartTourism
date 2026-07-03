<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('booking')
                ->cascadeOnDelete();

            $table->enum('tipe_pembayaran', [
                'dp',
                'lunas'
            ]);

            $table->enum('metode_pembayaran', [
                'transfer',
                'cash'
            ]);

            $table->decimal('nominal', 12, 2);

            $table->string('bukti_pembayaran')
                ->nullable();

            $table->dateTime('tanggal_pembayaran');

            $table->enum('status_verifikasi', [
                'pending',
                'valid',
                'ditolak'
            ])->default('pending');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};