<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('booking_items', function (Blueprint $table) {
        $table->integer('hari')->default(0)->after('paket_wisata_id');
    });
}

public function down(): void
{
    Schema::table('booking_items', function (Blueprint $table) {
        $table->dropColumn(['hari']);
    });
}
};
