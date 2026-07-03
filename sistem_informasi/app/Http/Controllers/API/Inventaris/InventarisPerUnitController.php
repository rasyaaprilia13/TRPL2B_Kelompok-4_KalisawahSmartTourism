<?php

namespace App\Http\Controllers\API\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\InventarisPerUnit;
use App\Models\JenisInventaris;
use App\Models\LokasiPenyimpanan;
use Illuminate\Http\Request;

class InventarisPerUnitController extends Controller
{
    /**
     * Menampilkan semua inventaris per unit
     */
    public function index()
    {
        $inventaris = InventarisPerUnit::with([
            'jenisInventaris.subkategori.kategori',
            'lokasi'
        ])->latest()->get();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data inventaris berhasil diambil',
        //     'data' => $inventaris
        // ], 200);

        $jenisInventaris = JenisInventaris::all();
        $lokasi = LokasiPenyimpanan::all();

        return view('admin.operasional.InventarisPerunit.index', compact('inventaris', 'jenisInventaris', 'lokasi'));
    }

    /**
     * Menambahkan inventaris baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'id_jenis_inventaris' =>
                'required|exists:jenis_inventaris,id',

            'id_lokasi' =>
                'required|exists:lokasi_penyimpanan,id_lokasi',

            'kode_barang' =>
                'required|string|max:100|unique:inventaris_perunit,kode_barang',

            'tanggal_beli' =>
                'required|date',

            'harga_beli' =>
                'required|numeric|min:0',

            'sumber_pembelian' =>
                'nullable|string|max:255',

            'kondisi_unit' =>
                'required|in:Baik,Rusak Ringan,Rusak Berat'
        ]);

        $inventaris = InventarisPerUnit::create([

            'id_jenis_inventaris' =>
                $validated['id_jenis_inventaris'],

            'id_lokasi' =>
                $validated['id_lokasi'],

            'kode_barang' =>
                trim($validated['kode_barang']),

            'tanggal_beli' =>
                $validated['tanggal_beli'],

            'harga_beli' =>
                $validated['harga_beli'],

            'sumber_pembelian' =>
                $validated['sumber_pembelian'] ?? null,

            'kondisi_unit' =>
                $validated['kondisi_unit']
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Inventaris berhasil ditambahkan',
        //     'data' => $inventaris
        // ], 201);
        return redirect()->back()->with('success', 'Inventaris berhasil ditambahkan');
    }

    /**
     * Menampilkan detail inventaris
     */
    public function show($id)
    {
        $inventaris = InventarisPerUnit::with([
            'jenisInventaris.subkategori.kategori',
            'lokasi'
        ])->find($id);

        if (!$inventaris) {
            return response()->json([
                'success' => false,
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $inventaris
        ], 200);
    }

    /**
     * Mengupdate inventaris
     */
    public function update(Request $request, $id)
    {
        $inventaris = InventarisPerUnit::find($id);

        if (!$inventaris) {
            return response()->json([
                'success' => false,
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([

            'id_jenis_inventaris' =>
                'required|exists:jenis_inventaris,id',

            'id_lokasi' =>
                'required|exists:lokasi_penyimpanan,id_lokasi',

            'kode_barang' =>
                'required|string|max:100|unique:inventaris_perunit,kode_barang,' . $id,

            'tanggal_beli' =>
                'required|date',

            'harga_beli' =>
                'required|numeric|min:0',

            'sumber_pembelian' =>
                'nullable|string|max:255',

            'kondisi_unit' =>
                'required|in:Baik,Rusak Ringan,Rusak Berat'
        ]);

        $inventaris->update([

            'id_jenis_inventaris' =>
                $validated['id_jenis_inventaris'],

            'id_lokasi' =>
                $validated['id_lokasi'],

            'kode_barang' =>
                trim($validated['kode_barang']),

            'tanggal_beli' =>
                $validated['tanggal_beli'],

            'harga_beli' =>
                $validated['harga_beli'],

            'sumber_pembelian' =>
                $validated['sumber_pembelian'] ?? null,

            'kondisi_unit' =>
                $validated['kondisi_unit']
        ]);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Inventaris berhasil diupdate',
        //     'data' => $inventaris
        // ], 200);
        return redirect()->back()->with('success', 'Inventaris berhasil diupdate');
    }

    /**
     * Menghapus inventaris
     */
    public function destroy($id)
    {
        $inventaris = InventarisPerUnit::find($id);

        if (!$inventaris) {
            return response()->json([
                'success' => false,
                'message' => 'Inventaris tidak ditemukan'
            ], 404);
        }

        $inventaris->delete();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Inventaris berhasil dihapus'
        // ], 200);
        return redirect()->back()->with('success', 'Inventaris berhasil dihapus');
    }
}
