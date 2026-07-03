<?php

namespace App\Http\Controllers\API\Booking_pengunjung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

class TrackingBookingController extends Controller
{
    // ======================================================
    // CARI BOOKING
    // ======================================================
    public function tracking(Request $request)
{
            $request->validate([
            'nama_pemesan'      => 'required|string',
            'no_hp'             => 'required|string',
            'tanggal_kunjungan' => 'required|date',
        ]);

        $booking = Booking::with([
            'items.paketWisata',
            'fasilitas.fasilitas',
            'pembayaran'
        ])
        ->whereRaw(
            'LOWER(TRIM(nama_pemesan)) = ?',
            [strtolower(trim($request->nama_pemesan))]
        )
        ->whereRaw(
            'TRIM(no_hp) = ?',
            [trim($request->no_hp)]
        )
        ->whereDate(
            'tanggal_kunjungan',
            $request->tanggal_kunjungan
        )
        ->latest()
        ->first();

        return view(
            'pengunjung.landing-page.pencarian.search-results',
            [
                'booking' => $booking
            ]
        );
    }

    // ======================================================
    // DETAIL BOOKING
    // ======================================================
    public function detail($id)
    {
        $booking = Booking::with([

            // paket yang dibooking
            'items.paketWisata',

            // fasilitas tambahan
            'fasilitas.fasilitas',

            // pembayaran
            'pembayaran'

        ])->find($id);

        if (!$booking) {

            return response()->json([
                'message' => 'Booking tidak ditemukan'
            ], 404);
        }

        return response()->json([

            'message' => 'Detail booking ditemukan',

            'data' => [

                'id' => $booking->id,

                'kode_booking' => $booking->kode_booking,

                'nama_pemesan' => $booking->nama_pemesan,

                'no_hp' => $booking->no_hp,

                'tanggal_kunjungan' => $booking->tanggal_kunjungan,

                'tanggal_selesai' => $booking->tanggal_selesai,

                'jumlah_hari' => $booking->jumlah_hari,

                'jam' => $booking->jam,

                'jumlah_pengunjung' => $booking->jumlah_pengunjung,

                'jumlah_tiket_tambahan' =>
                    $booking->jumlah_tiket_tambahan,

                'harga_tiket_tambahan' =>
                    $booking->harga_tiket_tambahan,

                'subtotal_tiket_tambahan' =>
                    $booking->subtotal_tiket_tambahan,

                'catatan' => $booking->catatan,

                'status_booking' =>
                    $booking->status_booking,

                'status_pembayaran' =>
                    $booking->status_pembayaran,

                'total_harga' =>
                    $booking->total_harga,

                'diskon_manual' =>
                    $booking->diskon_manual,

                'total_harga_final' =>
                    $booking->total_harga_final,

                // paket booking
                'paket' =>
                    $booking->items,

                // fasilitas tambahan
                'fasilitas' =>
                    $booking->fasilitas,

                // pembayaran
                'pembayaran' =>
                    $booking->pembayaran,
            ]
        ]);
    }

    public function uploadBukti(Request $request, $id)
{
    $request->validate([
        'bukti_pembayaran' => [
            'required',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048'
        ]
    ]);

    $booking = Booking::findOrFail($id);

    $path = $request
        ->file('bukti_pembayaran')
        ->store('bukti-pembayaran', 'public');

    $pembayaran = Pembayaran::where(
        'booking_id',
        $booking->id
    )->latest()->first();

    if ($pembayaran) {

        // hapus file lama jika ada
        if (
            $pembayaran->bukti_pembayaran &&
            Storage::disk('public')->exists(
                $pembayaran->bukti_pembayaran
            )
        ) {
            Storage::disk('public')->delete(
                $pembayaran->bukti_pembayaran
            );
        }

        $pembayaran->update([
            'bukti_pembayaran' => $path,
            'status_verifikasi' => 'pending',
            'tanggal_pembayaran' => now()
        ]);

    } else {

        Pembayaran::create([
            'booking_id' => $booking->id,
            'tipe_pembayaran' => 'dp',
            'metode_pembayaran' => 'transfer',
            'nominal' => $booking->total_harga_final ?? $booking->total_harga,
            'bukti_pembayaran' => $path,
            'tanggal_pembayaran' => now(),
            'status_verifikasi' => 'pending'
        ]);
    }

    return view(
        'pengunjung.landing-page.pencarian.search-results',
        compact('booking')
    )->with(
        'success',
        'Bukti pembayaran berhasil diupload dan sedang menunggu konfirmasi admin.'
    );
}
}
