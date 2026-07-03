<?php

namespace App\Http\Controllers\API\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\LokasiPenyimpanan;
use Illuminate\Http\Request;

class LokasiPenyimpananController extends Controller
{
    /**
     * Menampilkan semua lokasi penyimpanan
     */
    public function index()
    {
        $lokasi = LokasiPenyimpanan::latest()->get();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data lokasi penyimpanan berhasil diambil',
        //     'data' => $lokasi
        // ], 200);

        return view('admin.operasional.LokasiPenyimpanan.index', compact('lokasi'));
    }

    /**
     * Menambahkan lokasi penyimpanan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'nama_lokasi' =>
                'required|string|max:255|unique:lokasi_penyimpanan,nama_lokasi'
        ]);

        $lokasi = LokasiPenyimpanan::create([

            'nama_lokasi' =>
                trim($validated['nama_lokasi'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Lokasi penyimpanan berhasil ditambahkan',
        //     'data' => $lokasi
        // ], 201);

        return redirect()->route('admin.lokasi-penyimpanan.index')->with('success', 'Lokasi penyimpanan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail lokasi penyimpanan
     */
    public function show($id)
    {
        $lokasi = LokasiPenyimpanan::find($id);

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi penyimpanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lokasi
        ], 200);
    }

    /**
     * Mengupdate lokasi penyimpanan
     */
    public function update(Request $request, $id)
    {
        $lokasi = LokasiPenyimpanan::find($id);

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi penyimpanan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([

            'nama_lokasi' =>
                'required|string|max:255|unique:lokasi_penyimpanan,nama_lokasi,' . $id . ',id_lokasi'
        ]);

        $lokasi->update([

            'nama_lokasi' =>
                trim($validated['nama_lokasi'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Lokasi penyimpanan berhasil diupdate',
        //     'data' => $lokasi
        // ], 200);

        return redirect()->route('admin.lokasi-penyimpanan.index')->with('success', 'Lokasi penyimpanan berhasil diupdate');
    }

    /**
     * Menghapus lokasi penyimpanan
     */
    public function destroy($id)
    {
        $lokasi = LokasiPenyimpanan::find($id);

        if (!$lokasi) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi penyimpanan tidak ditemukan'
            ], 404);
        }

        $lokasi->delete();

    //     // return response()->json([
    //         'success' => true,
    //         'message' => 'Lokasi penyimpanan berhasil dihapus'
    //     ], 200);
    // }
        return redirect()->route('admin.lokasi-penyimpanan.index')->with('success', 'Lokasi penyimpanan berhasil dihapus');
}
}
