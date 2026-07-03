<?php

namespace App\Http\Controllers\API\Pembayaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pembayaran;
use App\Models\Booking;

use Illuminate\Support\Facades\Storage;

class PembayaranPengunjungController extends Controller
{
    // ======================================================
    // UPLOAD / UPDATE BUKTI PEMBAYARAN
    // ======================================================
    public function uploadBukti(
        Request $request,
        $bookingId
    ) {

        $request->validate([

            'bukti_pembayaran' =>
                'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // ==========================================
        // CEK BOOKING
        // ==========================================
        $booking = Booking::find($bookingId);

        if (!$booking) {

            return response()->json([
                'message' =>
                    'Booking tidak ditemukan'
            ], 404);
        }

        // ==========================================
        // CEK DATA PEMBAYARAN
        // ==========================================

        $pembayaran = Pembayaran::where(
            'booking_id',
            $booking->id
        )->first();

        if (!$pembayaran) {

            return response()->json([
                'message' =>
                    'Data pembayaran tidak ditemukan'
            ], 404);
        }

        // ==========================================
        // HAPUS FILE LAMA JIKA ADA
        // ==========================================
        if ($pembayaran->bukti_pembayaran) {

            Storage::disk('public')->delete(
                $pembayaran->bukti_pembayaran
            );
        }

        // ==========================================
        // UPLOAD FILE BARU
        // ==========================================
        $path = $request
            ->file('bukti_pembayaran')
            ->store(
                'pembayaran',
                'public'
            );

        // ==========================================
        // UPDATE PEMBAYARAN
        // NOMINAL DIISI ADMIN SAAT VERIFIKASI
        // ==========================================
        $pembayaran->update([

            'bukti_pembayaran' =>
                $path,

            'tanggal_pembayaran' =>
                now(),

            // upload ulang -> pending lagi
            'status_verifikasi' =>
                'pending',

            // reset catatan admin
            'catatan' =>
                null
        ]);

        // ==========================================
        // STATUS BOOKING
        // MENUNGGU VERIFIKASI ADMIN
        // ==========================================
        $booking->update([

            'status_booking' =>
                'pending'
        ]);

        return response()->json([

            'message' =>
                'Bukti pembayaran berhasil diupload',

            'data' =>
                $pembayaran
        ], 200);
    }
}
