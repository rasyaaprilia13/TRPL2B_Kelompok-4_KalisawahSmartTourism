<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [

        'booking_id',

        'tipe_pembayaran',

        'metode_pembayaran',

        'nominal',

        'bukti_pembayaran',

        'tanggal_pembayaran',

        'status_verifikasi',

        'catatan'
    ];

    // =========================================
    // RELASI KE BOOKING
    // =========================================
    public function booking()
    {
        return $this->belongsTo(
            Booking::class,
            'booking_id'
        );
    }
}