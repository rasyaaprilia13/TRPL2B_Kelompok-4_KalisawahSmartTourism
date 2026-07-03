<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    protected $table = 'kategori_pengeluaran';

    protected $fillable = ['nama_kategori'];

    public $timestamps = false;

    public function pengeluaran()
    {
        return $this->hasMany(PengeluaranOperasional::class, 'id_kategori');
    }
}