<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = 'diskon';
    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'id_booking',
        'jumlah_diskon',
        'keterangan'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }
}
