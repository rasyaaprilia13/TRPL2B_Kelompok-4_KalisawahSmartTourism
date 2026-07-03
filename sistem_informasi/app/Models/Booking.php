<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'kode_booking',
        'nama_pemesan',
        'no_hp',
        'tanggal_kunjungan',
        'tanggal_selesai',
        'jumlah_malam',
        'jumlah_hari',
        'jam',
        'jumlah_pengunjung',
        'jumlah_tiket_tambahan',
        'harga_tiket_tambahan',
        'subtotal_tiket_tambahan',
        'catatan',
        'total_harga',
        'diskon_manual',
        'total_harga_final',
        'status_booking',
        'status_pembayaran',
        'tanggal_reschedule',
        'alasan_reschedule',
        'jumlah_reschedule'
    ];

    public function items()
    {
        return $this->hasMany(BookingItem::class, 'booking_id');
    }

    public function fasilitas()
    {
        return $this->hasMany(BookingFasilitas::class, 'booking_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'booking_id');
    }
}
