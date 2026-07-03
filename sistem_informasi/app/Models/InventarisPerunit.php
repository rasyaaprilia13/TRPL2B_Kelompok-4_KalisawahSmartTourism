<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarisPerUnit extends Model
{
    protected $table = 'inventaris_perunit';

    protected $fillable = [
        'id_jenis_inventaris',
        'id_lokasi',
        'kode_barang',
        'tanggal_beli',
        'harga_beli',
        'sumber_pembelian',
        'kondisi_unit'
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
        'harga_beli' => 'decimal:2'
    ];

    public function jenisInventaris()
    {
        return $this->belongsTo(
            JenisInventaris::class,
            'id_jenis_inventaris'
        );
    }

    public function lokasi()
    {
        return $this->belongsTo(
            LokasiPenyimpanan::class,
            'id_lokasi'
        );
    }
}