<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;

use App\Http\Controllers\API\Kelola_landingpage\ProfilWisataController;
use App\Http\Controllers\API\Kelola_pengeluaran\KategoriPengeluaranController;
use App\Http\Controllers\API\Kelola_pengeluaran\PengeluaranOperasionalController;
use App\Http\Controllers\API\Kelola_landingpage\berita\BeritaController;
use App\Http\Controllers\API\Kelola_landingpage\experience\ClientLogosController;
use App\Http\Controllers\API\Kelola_landingpage\hero_section\LandingSettingsController;


use App\Http\Controllers\API\Kelola_landingpage\paket\KategoriPaketController;
use App\Http\Controllers\API\Kelola_landingpage\paket\PaketWisataController;
use App\Http\Controllers\API\Kelola_landingpage\paket\PaketFasilitasController;

use App\Http\Controllers\API\Inventaris\KategoriInventarisController;
use App\Http\Controllers\API\Inventaris\SubkategoriInventarisController;
use App\Http\Controllers\API\Inventaris\LokasiPenyimpananController;
use App\Http\Controllers\API\Inventaris\JenisInventarisController;
use App\Http\Controllers\API\Inventaris\InventarisPerUnitController;

use App\Http\Controllers\API\Kelola_fasilitas\KategoriFasilitasController;
use App\Http\Controllers\API\Kelola_fasilitas\FasilitasController;

use App\Http\Controllers\API\Kelola_booking\AdminBookingController;
use App\Http\Controllers\API\Pembayaran\PembayaranPengunjungController;
use App\Http\Controllers\API\Pembayaran\PembayaranAdminController;
use App\Http\Controllers\API\Booking_pengunjung\TrackingBookingController;
use App\Http\Controllers\API\Booking_pengunjung\PengunjungBookingController;

use App\Http\Controllers\API\Pemilik\DashboardPemilikController;

    Route::get('/test', function () {
        return "API WORKING";
    });

    // ROUTE PUBLIC //
    // untuk login admin
    Route::post('/login', [AuthController::class, 'login']);

    // untuk booking pengunjung
    Route::post('/bookings', [PengunjungBookingController::class, 'store'])->name('api.booking.store');

    // untuk pembayaran booking pengunjung
    Route::post('/pembayaran', [
        PembayaranPengunjungController::class,
        'store'
    ]);

    // untuk tracking booking pengunjung
    Route::post('/tracking-booking', [
        TrackingBookingController::class,
        'track'
    ]);

    Route::get('/tracking-booking/{id}', [
        TrackingBookingController::class,
        'detail'
    ]);



    // ROUTE PROTECTED //
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

    // ROUTE ADMIN //
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

        // pengelolaan paket wisata
        Route::apiResource('kategori-paket', KategoriPaketController::class);
        Route::apiResource('paket-wisata', PaketWisataController::class);
        Route::apiResource('paket-fasilitas', PaketFasilitasController::class);

        // pengelolaan landing page (masih ngambang)
        Route::apiResource('profil-wisata', ProfilWisataController::class);
        Route::apiResource('berita', BeritaController::class);
        Route::apiResource('client-logos', ClientLogosController::class);
        Route::apiResource('landing-settings', LandingSettingsController::class);

        // proses dan pengelolaan booking
        Route::apiResource('bookings', AdminBookingController::class);
        Route::post('/bookings/{id}/fasilitas',[AdminBookingController::class, 'tambahFasilitas']);
        Route::apiResource('pembayaran', PembayaranAdminController::class);

        // pengelolaan fasilitas
        Route::apiResource('kategori-fasilitas', KategoriFasilitasController::class);
        Route::apiResource('fasilitas', FasilitasController::class);
        Route::apiResource('paket-fasilitas', PaketFasilitasController::class);

        // pengelolaan inventaris
        Route::apiResource('kategori-inventaris', KategoriInventarisController::class);
        Route::apiResource('subkategori-inventaris', SubkategoriInventarisController::class);
        Route::apiResource('lokasi-penyimpanan', LokasiPenyimpananController::class);
        Route::apiResource('jenis-inventaris', JenisInventarisController::class);
        Route::apiResource('inventaris-unit', InventarisPerUnitController::class);

        // pengelolaan pengeluaran
        Route::apiResource('kategori-pengeluaran', KategoriPengeluaranController::class);
        Route::apiResource('pengeluaran-operasional', PengeluaranOperasionalController::class);

});

// ROUTE PEMILIK //
Route::middleware(['auth:sanctum', 'pemilik'])
    ->prefix('pemilik')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardPemilikController::class,
            'index'
        ]);

        Route::get('/laporan-pemasukan', [
            DashboardPemilikController::class,
            'laporanPemasukan'
        ]);

        Route::get('/laporan-pengeluaran', [
            DashboardPemilikController::class,
            'laporanPengeluaran'
        ]);

        Route::get('/laporan-booking', [
            DashboardPemilikController::class,
            'laporanBooking'
        ]);

});