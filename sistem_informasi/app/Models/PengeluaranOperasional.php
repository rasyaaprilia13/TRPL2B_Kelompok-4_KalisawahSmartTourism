<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranOperasional extends Model
{
    protected $table = 'pengeluaran_operasional';

    protected $fillable = [
        'id_kategori',
        'keterangan',
        'jumlah_uang',
        'tanggal_pengeluaran',
        'bukti_pengeluaran',
        'dicatat_oleh'
    ];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori');
    }
}