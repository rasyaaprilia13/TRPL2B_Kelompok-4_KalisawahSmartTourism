<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();

            // RELASI BOOKING
            $table->foreignId('booking_id')
                ->constrained('booking') // eksplisit biar aman
                ->cascadeOnDelete();

            // RELASI PEMBAYARAN
            $table->foreignId('pembayaran_id')
                ->nullable()
                ->constrained('pembayaran')
                ->nullOnDelete();

            $table->string('kode_pemasukan')->unique();
            $table->decimal('nominal', 15, 2);
            $table->enum('jenis_transaksi', ['dp', 'lunas']);
            $table->enum('metode_pemasukan', ['transfer', 'cash', 'qris']);
            $table->dateTime('tanggal_masuk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
