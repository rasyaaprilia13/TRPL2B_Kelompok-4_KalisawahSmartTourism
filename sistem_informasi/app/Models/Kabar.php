<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabar extends Model
{
    protected $table = 'kabar';
    protected $primaryKey = 'id';

    protected $fillable = [
        'judul',
        'isi_kabar',
        'foto',
        'tanggal',
        'slug',
    ];

    public function getSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->judul);
        // return Str::slug($this->judul);
    }
}
