<?php

namespace App\Http\Controllers\API\Kelola_fasilitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriFasilitas;

class KategoriFasilitasController extends Controller
{
    // tampil semua kategori
    public function index()
    {
        $data = KategoriFasilitas::all();

        // return response()->json($data, 200);

        return view('admin.kelola_fasilitas.kategori', compact('data'));
    }

    // tampil detail kategori
    public function show($id)
    {
        $data = KategoriFasilitas::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($data, 200);
    }

    // tambah kategori
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        $data = KategoriFasilitas::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        // return response()->json($data, 201);

        return redirect()->route('admin.kategori-fasilitas.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    // update kategori
    public function update(Request $request, $id)
    {
        $data = KategoriFasilitas::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        $data->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        // return response()->json($data, 200);

        return redirect()->route('admin.kategori-fasilitas.index')->with('success', 'Kategori berhasil diperbarui');
    }

    // hapus kategori
    public function destroy($id)
    {
        $data = KategoriFasilitas::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data->delete();

        // return response()->json([
        //     'message' => 'Berhasil dihapus'
        // ], 200);

        return redirect()->route('admin.kategori-fasilitas.index')->with('success', 'Kategori berhasil dihapus');
    }
}
