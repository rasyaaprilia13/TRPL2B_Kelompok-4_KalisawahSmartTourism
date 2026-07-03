<?php

namespace App\Http\Controllers\API\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\SubkategoriInventaris;
use App\Models\KategoriInventaris;
use Illuminate\Http\Request;

class SubkategoriInventarisController extends Controller
{
    /**
     * Menampilkan semua data subkategori
     */
    public function index()
    {
        $subkategori = SubkategoriInventaris::with('kategori')
            ->latest()
            ->get();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data subkategori berhasil diambil',
        //     'data' => $subkategori
        // ], 200);
        // TAMBAHKAN SATU BARIS INI DI SINI:
        // Kita buat fisik variabel $kategori berisi semua data Kategori Inventaris
        $kategori = KategoriInventaris::all();

        return view('admin.operasional.Subkategori.index', compact('subkategori', 'kategori'));
    }

    /**
     * Menambahkan subkategori baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'id_kategori' =>
                'required|exists:kategori_inventaris,id_kategori',

            'nama_subkategori' =>
                'required|string|max:255'
        ]);

        $subkategori = SubkategoriInventaris::create([

            'id_kategori' =>
                $validated['id_kategori'],

            'nama_subkategori' =>
                trim($validated['nama_subkategori'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Subkategori berhasil ditambahkan',
        //     'data' => $subkategori
        // ], 201);

        return redirect()->route('admin.subkategori-inventaris.index')->with('success', 'Subkategori berhasil ditambahkan');
    }

    /**
     * Menampilkan detail subkategori
     */
    public function show($id)
    {
        $subkategori = SubkategoriInventaris::with('kategori')
            ->find($id);

        if (!$subkategori) {
            return response()->json([
                'success' => false,
                'message' => 'Subkategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subkategori
        ], 200);
    }

    /**
     * Mengupdate subkategori
     */
    public function update(Request $request, $id)
    {
        $subkategori = SubkategoriInventaris::find($id);

        if (!$subkategori) {
            return response()->json([
                'success' => false,
                'message' => 'Subkategori tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([

            'id_kategori' =>
                'required|exists:kategori_inventaris,id_kategori',

            'nama_subkategori' =>
                'required|string|max:255'
        ]);

        $subkategori->update([

            'id_kategori' =>
                $validated['id_kategori'],

            'nama_subkategori' =>
                trim($validated['nama_subkategori'])
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Subkategori berhasil diupdate',
        //     'data' => $subkategori
        // ], 200);
        return redirect()->route('admin.subkategori-inventaris.index')->with('success', 'Subkategori berhasil diupdate');
    }

    /**
     * Menghapus subkategori
     */
    public function destroy($id)
    {
        $subkategori = SubkategoriInventaris::find($id);

        if (!$subkategori) {
            return response()->json([
                'success' => false,
                'message' => 'Subkategori tidak ditemukan'
            ], 404);
        }

        $subkategori->delete();

        // return response()->json([
        // //     'success' => true,
        // //     'message' => 'Subkategori berhasil dihapus'
        // ], 200);
        return redirect()->route('admin.subkategori-inventaris.index')->with('success', 'Subkategori berhasil dihapus');
    }
}
