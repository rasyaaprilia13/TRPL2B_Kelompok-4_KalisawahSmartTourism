<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `pembayaran` MODIFY `tipe_pembayaran` ENUM('dp', 'lunas') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `pembayaran` MODIFY `tipe_pembayaran` ENUM('dp', 'pelunasan') NOT NULL");
    }
};
