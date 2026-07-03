<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $table = 'booking_items';

    protected $fillable = [

        'booking_id',

        'paket_wisata_id',

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
    | RELASI KE PAKET WISATA
    |--------------------------------------------------------------------------
    */

    public function paketWisata()
    {
        return $this->belongsTo(
            PaketWisata::class,
            'paket_wisata_id'
        );
    }
}