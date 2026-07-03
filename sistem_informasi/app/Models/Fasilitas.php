<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'kategori_fasilitas_id',
        'nama_fasilitas',
        'tipe_fasilitas',
        'harga',
        'stok',
        'deskripsi',
        'gambar',
        'status'
    ];

    // relasi ke kategori fasilitas
    public function kategori()
    {
        return $this->belongsTo(
            KategoriFasilitas::class,
            'kategori_fasilitas_id'
        );
    }

    // relasi ke paket fasilitas
    public function paketFasilitas()
    {
        return $this->hasMany(PaketFasilitas::class);
    }

    // relasi ke booking fasilitas
    public function bookingFasilitas()
    {
        return $this->hasMany(BookingFasilitas::class);
    }

    // Di dalam file App\Models\Fasilitas.php

    public function paketWisata()
    {
        // Kebalikan dari relasi di PaketWisata
        return $this->belongsToMany(
            PaketWisata::class, 
            'paket_fasilitas', 
            'id_fasilitas',    // FK dari model ini (Fasilitas) di tabel pivot
            'paket_wisata_id'  // FK dari model lawan (PaketWisata) di tabel pivot
        );
    }
}