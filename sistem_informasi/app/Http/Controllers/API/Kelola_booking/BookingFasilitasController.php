<?php

namespace App\Http\Controllers\API\Kelola_booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\BookingFasilitas;
use App\Models\Fasilitas;

class BookingFasilitasController extends Controller
{
    // tampil semua booking fasilitas
    public function index()
    {
        $data = BookingFasilitas::with([
            'booking',
            'fasilitas'
        ])->get();

        return response()->json($data, 200);
    }

    // detail booking fasilitas
    public function show($id)
    {
        $data = BookingFasilitas::with([
            'booking',
            'fasilitas'
        ])->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($data, 200);
    }

    // tambah booking fasilitas
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:booking,id',

            'fasilitas_id' => 'required|exists:fasilitas,id',

            'qty' => 'required|integer|min:1'
        ]);

        // ambil data fasilitas
        $fasilitas = Fasilitas::find(
            $request->fasilitas_id
        );

        // validasi tipe fasilitas
        if ($fasilitas->tipe_fasilitas != 'sewa') {

            return response()->json([
                'message' => 'Fasilitas ini tidak dapat disewa'
            ], 422);
        }

        // validasi stok
        if ($request->qty > $fasilitas->stok) {

            return response()->json([
                'message' => 'Stok fasilitas tidak mencukupi'
            ], 422);
        }

        // hitung subtotal
        $subtotal = $request->qty * $fasilitas->harga;

        // simpan booking fasilitas
        $data = BookingFasilitas::create([
            'booking_id' => $request->booking_id,

            'fasilitas_id' => $request->fasilitas_id,

            'qty' => $request->qty,

            // simpan harga saat transaksi
            'harga' => $fasilitas->harga,

            'subtotal' => $subtotal
        ]);

        // kurangi stok fasilitas
        $fasilitas->stok -= $request->qty;

        $fasilitas->save();

        return response()->json([
            'message' => 'Booking fasilitas berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    // update booking fasilitas
    public function update(Request $request, $id)
    {
        $data = BookingFasilitas::find($id);

        if (!$data) {

            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        // ambil fasilitas
        $fasilitas = Fasilitas::find(
            $data->fasilitas_id
        );

        /*
        KEMBALIKAN STOK LAMA DULU
        */

        $fasilitas->stok += $data->qty;

        /*
        CEK STOK BARU
        */

        if ($request->qty > $fasilitas->stok) {

            return response()->json([
                'message' => 'Stok fasilitas tidak mencukupi'
            ], 422);
        }

        /*
        HITUNG SUBTOTAL BARU
        */

        $subtotal = $request->qty * $fasilitas->harga;

        /*
        UPDATE DATA
        */

        $data->update([
            'qty' => $request->qty,

            // update harga terbaru
            'harga' => $fasilitas->harga,

            'subtotal' => $subtotal
        ]);

        /*
        KURANGI STOK BARU
        */

        $fasilitas->stok -= $request->qty;

        $fasilitas->save();

        return response()->json([
            'message' => 'Booking fasilitas berhasil diupdate',
            'data' => $data
        ], 200);
    }

    // hapus booking fasilitas
    public function destroy($id)
    {
        $data = BookingFasilitas::find($id);

        if (!$data) {

            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // ambil fasilitas
        $fasilitas = Fasilitas::find(
            $data->fasilitas_id
        );

        /*
        KEMBALIKAN STOK
        */

        $fasilitas->stok += $data->qty;

        $fasilitas->save();

        // hapus data
        $data->delete();

        return response()->json([
            'message' => 'Booking fasilitas berhasil dihapus'
        ], 200);
    }
}