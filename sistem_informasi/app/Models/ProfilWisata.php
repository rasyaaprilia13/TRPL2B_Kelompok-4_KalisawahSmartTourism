<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilWisata extends Model
{
    use HasFactory;

    protected $table = 'profil_wisata';
     public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_wisata',
        'deskripsi',
        'alamat',
        'no_hp',
        'email',
        'instagram',
        'tiktok',
        'maps_link'
    ];
}