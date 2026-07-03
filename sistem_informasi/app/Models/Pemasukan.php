<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    protected $table = 'pemasukan';

    protected $fillable = [
        'booking_id',
        'pembayaran_id',
        'kode_pemasukan',
        'nominal',
        'jenis_transaksi',
        'metode_pemasukan',
        'tanggal_masuk'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
