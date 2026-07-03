<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    public $timestamps = false;
    protected $table = 'paket_wisata';

    protected $fillable = [
        'kategori_paket_id',
        'nama_paket',
        'deskripsi',
        'harga',
        'kapasitas',
        'durasi',
        'status',
        'gambar',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriPaket::class, 'kategori_paket_id');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class, 'id_paket');
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'paket_fasilitas', 'paket_wisata_id', 'id')
                    ->withPivot('jumlah', 'keterangan');
    }

    public function getNamaAttribute()
    {
        return $this->nama_paket;
    }

    public function getGambarAttribute()
    {
        return $this->attributes['gambar'] ?? null;
    }
}

