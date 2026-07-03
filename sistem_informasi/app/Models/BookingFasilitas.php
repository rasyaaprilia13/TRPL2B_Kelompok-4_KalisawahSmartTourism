<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFasilitas extends Model
{
    protected $table = 'booking_fasilitas';

    protected $fillable = [

        'booking_id',

        'fasilitas_id',

        // TAMBAHAN
        'hari',
        'tanggal',

        'qty',

        'harga',

        'subtotal'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE BOOKING
    |--------------------------------------------------------------------------
    */

    public function booking()
    {
        return $this->belongsTo(
            Booking::class,
            'booking_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE FASILITAS
    |--------------------------------------------------------------------------
    */

    public function fasilitas()
    {
        return $this->belongsTo(
            Fasilitas::class,
            'fasilitas_id'
        );
    }
}