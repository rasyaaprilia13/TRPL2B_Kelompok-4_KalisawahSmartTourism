<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('booking')
                ->cascadeOnDelete();

            $table->foreignId('paket_wisata_id')
                ->constrained('paket_wisata')
                ->cascadeOnDelete();

            $table->integer('qty')->default(1);

            $table->decimal('harga', 12, 2);

            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};