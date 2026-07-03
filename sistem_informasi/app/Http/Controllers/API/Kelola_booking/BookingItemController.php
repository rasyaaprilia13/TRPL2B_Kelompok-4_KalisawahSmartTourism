<?php

namespace App\Http\Controllers\API\Kelola_booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\BookingItem;

class BookingItemController extends Controller
{
    // =========================================
    // TAMBAH ITEM KE BOOKING
    // =========================================

    public function store(Request $request)
    {
        $request->validate([
            'id_booking' => 'required|exists:booking,id',
            'nama_item' => 'required|string',
            'qty' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric'
        ]);

        $subtotal = $request->qty * $request->harga_satuan;

        $item = BookingItem::create([
            'id_booking' => $request->id_booking,
            'nama_item' => $request->nama_item,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'subtotal' => $subtotal
        ]);

        return response()->json([
            'message' => 'Item berhasil ditambahkan',
            'data' => $item
        ]);
    }

    // =========================================
    // LIHAT ITEM DALAM BOOKING
    // =========================================

    public function index($id_booking)
    {
        $items = BookingItem::where('id_booking', $id_booking)->get();

        return response()->json($items);
    }

    // =========================================
    // HAPUS ITEM
    // =========================================

    public function destroy($id)
    {
        $item = BookingItem::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus'
        ]);
    }
}