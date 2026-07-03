<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    public function hitungTotal(Booking $booking)
    {
        // =========================================
        // HARGA PAKET DASAR
        // =========================================
        $total = $booking->paket->harga;

        // =========================================
        // TAMBAHAN ORANG (TIKET)
        // contoh: 25k/orang tambahan
        // =========================================
        if ($booking->jumlah_orang > $booking->paket->kapasitas) {

            $extra = $booking->jumlah_orang - $booking->paket->kapasitas;

            $total += $extra * 25000;
        }

        // =========================================
        // FASILITAS TAMBAHAN
        // =========================================
        foreach ($booking->fasilitas as $fasilitas) {

            $total +=
                $fasilitas->harga *
                $fasilitas->pivot->jumlah;
        }

        return $total;
    }

    // =========================================
    // HITUNG FINAL SETELAH DISKON
    // =========================================
    public function hitungFinal($total, $diskon = 0)
    {
        return $total - $diskon;
    }
}