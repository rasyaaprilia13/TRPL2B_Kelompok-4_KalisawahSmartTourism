<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom slug dulu (nullable)
        Schema::table('kategori_paket', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nama_kategori');
        });

        // 2. Ambil semua data lama
        $dataKategori = DB::table('kategori_paket')->get();

        // 3. Isi slug otomatis
        foreach ($dataKategori as $item) {

            $slug = Str::slug($item->nama_kategori);

            // cek kalau slug sama
            $count = DB::table('kategori_paket')
                ->where('slug', $slug)
                ->count();

            if ($count > 0) {
                $slug = $slug . '-' . $item->id;
            }

            DB::table('kategori_paket')
                ->where('id', $item->id)
                ->update([
                    'slug' => $slug
                ]);
        }

        // 4. Baru ubah jadi unique
        Schema::table('kategori_paket', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('kategori_paket', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};