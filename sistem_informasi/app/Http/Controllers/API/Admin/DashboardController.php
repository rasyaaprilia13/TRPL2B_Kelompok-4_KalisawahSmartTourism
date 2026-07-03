<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;

use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooking = Booking::count();

        // $bookingHariIni = Booking::whereDate(
        //     'tanggal_booking',   // sesuaikan nama kolom
        //     Carbon::today()
        // )->count();

        $bookingHariIni = $bookingHariIni ?? 0;

        $totalPengunjung = Booking::sum(
            'jumlah_pengunjung'      // sesuaikan nama kolom
        );

        return view('admin.dashboard', compact(
            'totalBooking',
            'bookingHariIni',
            'totalPengunjung'
        ));
    }
}
