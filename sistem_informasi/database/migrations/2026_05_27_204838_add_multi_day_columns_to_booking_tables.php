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
        Schema::table('booking', function (Blueprint $table) {
            $table->integer('jumlah_malam')->default(1)->after('tanggal_selesai');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('paket_wisata_id');
        });

        Schema::table('booking_fasilitas', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('fasilitas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropColumn(['tanggal_selesai', 'jumlah_malam']);
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });

        Schema::table('booking_fasilitas', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
