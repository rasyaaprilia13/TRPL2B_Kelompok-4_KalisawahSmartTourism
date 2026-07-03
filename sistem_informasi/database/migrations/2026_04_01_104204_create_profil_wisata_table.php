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
            Schema::create('profil_wisata', function (Blueprint $table) {
        $table->id('id');
        $table->string('nama_wisata', 255);
        $table->text('deskripsi');
        $table->string('alamat', 255);
        $table->string('no_hp', 20);
        $table->string('email', 255);
        $table->string('instagram', 100)->nullable();
        $table->string('tiktok', 100)->nullable();
        $table->text('maps_link')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_wisata');
    }
};
