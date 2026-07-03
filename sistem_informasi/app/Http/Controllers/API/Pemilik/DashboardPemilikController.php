<?php

namespace App\Http\Controllers\API\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\PengeluaranOperasional;
use App\Models\InventarisPerUnit;
use Illuminate\Support\Carbon;

class DashboardPemilikController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $pemasukan = Pembayaran::whereDate('tanggal_pembayaran', $today)
            ->where('status_verifikasi', 'valid')
            ->sum('nominal');

        $pengeluaran = PengeluaranOperasional::whereDate('tanggal_pengeluaran', $today)
            ->sum('jumlah_uang');

        $booking = Booking::whereDate('tanggal_kunjungan', $today)
            ->count();

        $pengunjung = Booking::whereDate('tanggal_kunjungan', $today)
            ->sum('jumlah_pengunjung');

        $inventaris = InventarisPerUnit::where('kondisi_unit', 'baik')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'pemasukan_hari_ini' => $pemasukan,
                'pengeluaran_hari_ini' => $pengeluaran,
                'booking_hari_ini' => $booking,
                'pengunjung_hari_ini' => $pengunjung,
                'inventaris_baik' => $inventaris,
            ]
        ]);
    }
}