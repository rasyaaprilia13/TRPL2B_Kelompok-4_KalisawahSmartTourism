<?php

namespace App\Http\Controllers\API\Kelola_landingpage\paket;

use App\Http\Controllers\Controller;
use App\Models\PaketFasilitas;
use Illuminate\Http\Request;


class PaketFasilitasController extends Controller
{
    // GET /api/paket-fasilitas
    public function index()
    {
        // return response()->json(PaketFasilitas::all(), 200);

        return view('admin.layanan.paket_fasilitas.index');
    }

    // POST /api/paket-fasilitas
    public function store(Request $request)
    {
      $request->validate([
            'paket_wisata_id' => 'required|integer|exists:paket_wisata,id',
            'fasilitas_id' => 'required|integer|exists:fasilitas,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $data = PaketFasilitas::create($request->all());

        // return response()->json([
        //     'message' => 'Paket fasilitas berhasil ditambahkan',
        //     'data'    => $data
        // ], 201);

        return redirect()->route('admin.paket-fasilitas.index')->with('success', 'Paket fasilitas berhasil ditambahkan!');

    }

    // GET /api/paket-fasilitas/{id}
    public function show($id)
    {
        return response()->json(PaketFasilitas::findOrFail($id), 200);
    }

    // PUT /api/paket-fasilitas/{id}
    public function update(Request $request, $id)
    {
        $paketFasilitas = PaketFasilitas::findOrFail($id);

        $request->validate([
            'paket_wisata_id'     => 'required|integer|exists:paket_wisata,id',
            'fasilitas_id' => 'required|integer|exists:fasilitas,id',
            'jumlah'       => 'required|integer|min:1',
            'keterangan'   => 'nullable|string',
        ]);

        $paketFasilitas->update($request->all());

        // return response()->json(['message' => 'Paket fasilitas berhasil diupdate','data'    => $paketFasilitas], 200);
        return redirect()->route('admin.paket-fasilitas.index')->with('success', 'Paket fasilitas berhasil diupdate!');
    }

    // DELETE /api/paket-fasilitas/{id}
    public function destroy($id)
    {
        PaketFasilitas::destroy($id);

        // return response()->json([
        //     'message' => 'Paket fasilitas berhasil dihapus'
        // ], 200);
        return redirect()->route('admin.paket-fasilitas.index')->with('success', 'Paket fasilitas berhasil dihapus!');
    }
}
