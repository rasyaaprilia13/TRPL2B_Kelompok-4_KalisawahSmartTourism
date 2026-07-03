<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiPenyimpanan extends Model
{
    protected $table = 'lokasi_penyimpanan';

    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'nama_lokasi'
    ];

    public function inventaris()
    {
        return $this->hasMany(
            InventarisPerUnit::class,
            'id_lokasi'
        );
    }
}