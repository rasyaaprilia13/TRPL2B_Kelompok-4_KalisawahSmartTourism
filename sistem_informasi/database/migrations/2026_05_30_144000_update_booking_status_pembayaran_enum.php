<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `booking` MODIFY `status_pembayaran` ENUM('belum_bayar', 'dp', 'lunas', 'menunggu_verifikasi') NOT NULL DEFAULT 'belum_bayar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `booking` MODIFY `status_pembayaran` ENUM('belum_bayar', 'dp', 'lunas') NOT NULL DEFAULT 'belum_bayar'");
    }
};
