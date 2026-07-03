<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketFasilitas extends Model
{
    use HasFactory;

    protected $table = 'paket_fasilitas';

    protected $fillable = [
        'paket_wisata_id',
        'fasilitas_id',
        'jumlah',
        'keterangan'
    ];

    // relasi ke paket wisata
    public function paketWisata()
    {
        return $this->belongsTo(
            PaketWisata::class,
            'paket_wisata_id'
        );
    }

    // relasi ke fasilitas
    public function fasilitas()
    {
        return $this->belongsTo(
            Fasilitas::class,
            'fasilitas_id'
        );
    }
}