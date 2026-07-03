<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisInventaris extends Model
{
    protected $table = 'jenis_inventaris';

    protected $fillable = [
        'id_subkategori',
        'nama_barang',
        'spesifikasi'
    ];

    public function subkategori()
    {
        return $this->belongsTo(
            SubkategoriInventaris::class,
            'id_subkategori'
        );
    }

    public function inventarisPerUnit()
    {
        return $this->hasMany(
            InventarisPerUnit::class,
            'id_jenis_inventaris'
        );
    }
}