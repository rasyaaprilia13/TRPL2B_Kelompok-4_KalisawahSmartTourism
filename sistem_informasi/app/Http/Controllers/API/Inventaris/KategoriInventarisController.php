<?php

namespace App\Http\Controllers\API\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\KategoriInventaris;
use Illuminate\Http\Request;

class KategoriInventarisController extends Controller
{
    /**
     * Menampilkan semua data kategori inventaris
     */
    public function index()
    {
        $kategori = KategoriInventaris::latest()->get();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data kategori inventaris berhasil diambil',
        //     'data' => $kategori
        // ], 200);

        return view('admin.operasional.KategoriInventaris.index', compact('kategori'));

    }

    /**
     * Menambahkan kategori inventaris baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_inventaris,nama_kategori'
        ]);

        $kategori = KategoriInventaris::create([
            'nama_kategori' => trim($validated['nama_kategori'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Kategori inventaris berhasil ditambahkan',
        //     'data' => $kategori
        // ], 201);
        return redirect()->back()->with('success', 'Kategori inventaris berhasil ditambahkan');
    }

    /**
     * Menampilkan detail kategori inventaris
     */
    public function show($id)
    {
        $kategori = KategoriInventaris::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori inventaris tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kategori
        ], 200);
    }

    /**
     * Mengupdate kategori inventaris
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriInventaris::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori inventaris tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_inventaris,nama_kategori,' . $id . ',id_kategori'
        ]);

        $kategori->update([
            'nama_kategori' => trim($validated['nama_kategori'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Kategori inventaris berhasil diupdate',
        //     'data' => $kategori
        // ], 200);
        return redirect()->back()->with('success', 'Kategori inventaris berhasil diupdate');
    }

    /**
     * Menghapus kategori inventaris
     */
    public function destroy($id)
    {
        $kategori = KategoriInventaris::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori inventaris tidak ditemukan'
            ], 404);
        }

        $kategori->delete();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Kategori inventaris berhasil dihapus'
        // ], 200);

        return redirect()->back()->with('success', 'Kategori inventaris berhasil dihapus');
    }
}
