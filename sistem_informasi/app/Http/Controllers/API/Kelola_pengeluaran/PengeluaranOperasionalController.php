<?php

namespace App\Http\Controllers\API\Kelola_pengeluaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PengeluaranOperasional;

class PengeluaranOperasionalController extends Controller
{
    public function index()
    {
        // return response()->json(
        //     PengeluaranOperasional::with('kategori')->get(),
        //     200
        // );
        return view('admin.kelola_operasional.pengeluaran', [
        'data' => PengeluaranOperasional::with('kategori')->get(),
        'kategori' => \App\Models\KategoriPengeluaran::all() // Pastikan kategori juga dikirim
        ]);
        // return view('admin.kelola_operasional.pengeluaran', [
        //     'pengeluaran' => PengeluaranOperasional::with('kategori')->get()
        // ]);
    }

    private function validateData(Request $request)
    {
        return $request->validate([
            'id_kategori'         => 'required|exists:kategori_pengeluaran,id',
            'keterangan'          => 'required|string',
            'jumlah_uang'         => 'required|numeric|min:1',
            'tanggal_pengeluaran' => 'required|date',
            'bukti_pengeluaran'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'dicatat_oleh'        => 'required|string|max:100',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('bukti_pengeluaran')) {
            $file = $request->file('bukti_pengeluaran');
            // simpan ke storage/app/bukti_pengeluaran
            $path = $file->store('bukti_pengeluaran');
            $data['bukti_pengeluaran'] = $path;
        }

        $result = PengeluaranOperasional::create($data);

        // return response()->json($result, 201);
        return redirect()->route('admin.pengeluaran.index')->with('success', 'Data pengeluaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $data = PengeluaranOperasional::with('kategori')->find($id);

        if (!$data) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        // return response()->json($data, 200);
        return view('admin.kelola_operasional.pengeluaran.show', [
            'pengeluaran' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = PengeluaranOperasional::find($id);

        if (!$item) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        $data = $this->validateData($request);

        if ($request->hasFile('bukti_pengeluaran')) {
            $file = $request->file('bukti_pengeluaran');
            $path = $file->store('bukti_pengeluaran');
            $data['bukti_pengeluaran'] = $path;
        }

        $item->update($data);

        // return response()->json($item, 200);
        return redirect()->route('admin.pengeluaran.index')->with('success', 'Data pengeluaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $data = PengeluaranOperasional::find($id);

        if (!$data) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        $data->delete();

        // return response()->json([
        //     'message' => 'Data berhasil dihapus'
        // ], 200);
        return redirect()->route('admin.pengeluaran.index')->with('success', 'Data pengeluaran berhasil dihapus');
    }
}
