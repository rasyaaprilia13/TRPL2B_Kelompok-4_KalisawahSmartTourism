<?php

namespace App\Http\Controllers\API\Kelola_pengeluaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriPengeluaran;

class KategoriPengeluaranController extends Controller
{
    // Tampilkan semua kategori
    public function index()
    {
        // return response()->json(KategoriPengeluaran::all(), 200);
        return view('admin.kelola_operasional.kategori_pengeluaran', ['data' => KategoriPengeluaran::all()]);
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        $kategori = KategoriPengeluaran::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        // return response()->json($kategori, 201);
        return redirect()->back()->with('success', 'Data berhasil disimpan');

    }

    // Detail kategori
    public function show($id)
    {
        $kategori = KategoriPengeluaran::find($id);

        if (!$kategori) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        // return response()->json($kategori, 200);
        return view('admin.kelola_pengeluaran.detail_kategori_pengeluaran', ['data' => $kategori]);
    }

    // 🔹 Update kategori
    public function update(Request $request, $id)
    {
        $kategori = KategoriPengeluaran::find($id);

        if (!$kategori) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        // return response()->json($kategori, 200);
        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    // 🔹 Hapus kategori
    public function destroy($id)
    {
        $kategori = KategoriPengeluaran::find($id);

        if (!$kategori) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        $kategori->delete();

    //     return response()->json([
    //     'message' => 'Data berhasil dihapus'
    // ], 200);
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
