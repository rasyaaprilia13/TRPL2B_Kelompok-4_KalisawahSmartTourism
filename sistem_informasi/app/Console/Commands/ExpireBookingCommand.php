<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class ExpireBookingCommand extends Command
{
    protected $signature = 'booking:expire';
    protected $description = 'Membatalkan booking yang belum bayar dalam 24 jam';

    public function handle()
    {
        // Ambil data booking yang sudah lewat 24 jam
        $bookings = Booking::with('fasilitas')
            ->where('status_booking', 'menunggu_pembayaran')
            ->where('created_at', '<=', Carbon::now()->subHours(24))
            ->get();

        foreach ($bookings as $booking) {
            // Gunakan Transaction agar jika gagal, semua proses rollback
            DB::transaction(function () use ($booking) {
                
                // Kembalikan stok fasilitas
                foreach ($booking->fasilitas as $item) {
                    // Cukup gunakan increment langsung pada objek $item
                    // Tidak perlu mencari ulang ke DB (query find dihilangkan)
                    $item->increment('stok', $item->pivot->jumlah);
                }

                // Ubah status booking
                $booking->update([
                    'status_booking' => 'dibatalkan'
                ]);
            });
        }

        $this->info('Berhasil memproses ' . $bookings->count() . ' booking yang expired.');
    }
}