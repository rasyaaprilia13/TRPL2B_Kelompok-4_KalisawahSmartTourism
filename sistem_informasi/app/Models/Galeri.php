<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';
    protected $primaryKey = 'id_galeri';

     public $timestamps = false;
     
    protected $fillable = [
        'judul',
        'foto',
        'tanggal_upload'
    ];
}
