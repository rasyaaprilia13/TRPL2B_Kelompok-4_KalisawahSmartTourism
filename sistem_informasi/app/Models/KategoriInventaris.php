<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInventaris extends Model
{
    protected $table = 'kategori_inventaris';

    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori'
    ];

    public function subkategori()
    {
        return $this->hasMany(
            SubkategoriInventaris::class,
            'id_kategori'
        );
    }
}