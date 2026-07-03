<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubkategoriInventaris extends Model
{
    protected $table = 'subkategori_inventaris';

    protected $primaryKey = 'id_subkategori';

    protected $fillable = [
        'id_kategori',
        'nama_subkategori'
    ];

    /**
     * Relasi ke kategori inventaris
     */
    public function kategori()
    {
        return $this->belongsTo(
            KategoriInventaris::class,
            'id_kategori',
            'id_kategori'
        );
    }

    /**
     * Relasi ke jenis inventaris
     */
    public function jenisInventaris()
    {
        return $this->hasMany(
            JenisInventaris::class,
            'id_subkategori',
            'id_subkategori'
        );
    }
}