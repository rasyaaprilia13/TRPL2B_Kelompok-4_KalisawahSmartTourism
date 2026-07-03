<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriPaket extends Model
{
    public $timestamps = false;

    // Nama tabel
    protected $table = 'kategori_paket';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'tagline',
        'hero_image',
        'gambar',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    // 1 kategori punya banyak paket wisata
    public function paketWisata()
    {
        return $this->hasMany(PaketWisata::class, 'kategori_paket_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getNamaAttribute()
    {
        return $this->nama_kategori;
    }

    public function getLinkAttribute()
    {
        return route('kategori.detail', $this->slug);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO GENERATE SLUG
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama_kategori);
            }
        });

        static::updating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama_kategori);
            }
        });
    }
}
