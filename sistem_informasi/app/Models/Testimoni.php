<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'testimoni';

    protected $fillable = [
        'nama',
        'instansi',
        'rating',
        'ulasan',
        'foto_path',
    ];
}
