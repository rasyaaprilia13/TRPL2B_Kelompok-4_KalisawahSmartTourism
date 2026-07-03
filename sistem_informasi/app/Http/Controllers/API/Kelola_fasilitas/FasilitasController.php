<?php

namespace App\Http\Controllers\API\Kelola_fasilitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fasilitas;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    // tampil semua fasilitas
    public function index()
    {
        $data = Fasilitas::with('kategori')->get();

        // return response()->json($data, 200);

        return view('admin.kelola_fasilitas.fasilitas', compact('data'));

    }

    // tampil detail fasilitas
    public function show($id)
    {
        $data = Fasilitas::with('kategori')->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($data, 200);
    }

    // tambah fasilitas
    public function store(Request $request)
    {
        $request->validate([
            'kategori_fasilitas_id' => 'nullable|exists:kategori_fasilitas,id',
            'nama_fasilitas' => 'required|string|max:255',

            'tipe_fasilitas' => 'required|in:informasi,paket,sewa',

            'harga' => 'nullable|numeric|min:0',

            'stok' => 'nullable|integer|min:0',

            'deskripsi' => 'nullable|string',

            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'status' => 'required|in:aktif,nonaktif'
        ]);

        // validasi fasilitas sewa
        if ($request->tipe_fasilitas == 'sewa') {

            // harga wajib
            if ($request->harga === null) {
                // return response()->json([
                //     'message' => 'Harga wajib diisi untuk fasilitas sewa'
                // ], 422);

                return redirect()->back()->with('error', 'Harga wajib diisi untuk fasilitas sewa');
            }

            // stok wajib
            if ($request->stok === null) {
                return response()->json([
                    'message' => 'Stok wajib diisi untuk fasilitas sewa'
                ], 422);
            }
        }

        // upload gambar
        $path = null;

        if ($request->hasFile('gambar')) {

            $path = $request->file('gambar')
                            ->store('fasilitas', 'public');
        }

        // simpan data
        $data = Fasilitas::create([
            'kategori_fasilitas_id' => $request->kategori_fasilitas_id,
            'nama_fasilitas' => $request->nama_fasilitas,
            'tipe_fasilitas' => $request->tipe_fasilitas,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'gambar' => $path,
            'status' => $request->status
        ]);

        // return response()->json([
        //     'message' => 'Fasilitas berhasil ditambahkan',
        //     'data' => $data
        // ], 201);

        return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan');
    }

    // update fasilitas
    public function update(Request $request, $id)
    {
        $data = Fasilitas::find($id);

        if (!$data) {
            // return response()->json([
            //     'message' => 'Data tidak ditemukan'
            // ], 404);

            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        $request->validate([
            'kategori_fasilitas_id' => 'nullable|exists:kategori_fasilitas,id',
            'nama_fasilitas' => 'required|string|max:255',
            'tipe_fasilitas' => 'required|in:informasi,paket,sewa',
            'harga' => 'nullable|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        // validasi fasilitas sewa
        if ($request->tipe_fasilitas == 'sewa') {

            if ($request->harga === null) {
                // return response()->json([
                //     'message' => 'Harga wajib diisi untuk fasilitas sewa'
                // ], 422);

                return redirect()->back()->with('error', 'Harga wajib diisi untuk fasilitas sewa')->withInput();
            }

            if ($request->stok === null) {
                // return response()->json([
                //     'message' => 'Stok wajib diisi untuk fasilitas sewa'
                // ], 422);

                return redirect()->back()->with('error', 'Stok wajib diisi untuk fasilitas sewa')->withInput();
            }
        }

        // ganti gambar
        if ($request->hasFile('gambar')) {

            // hapus gambar lama
            if ($data->gambar) {

                Storage::disk('public')
                        ->delete($data->gambar);
            }

            // upload gambar baru
            $data->gambar = $request->file('gambar')
                                    ->store('fasilitas', 'public');
        }

        // update data
        $data->update([
            'kategori_fasilitas_id' => $request->kategori_fasilitas_id,
            'nama_fasilitas' => $request->nama_fasilitas,
            'tipe_fasilitas' => $request->tipe_fasilitas,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status
        ]);

        //  return response()->json([
        //     'message' => 'Fasilitas berhasil diupdate',
        //     'data' => $data
        // ], 200);

        return redirect()->back()->with('success', 'Fasilitas berhasil diupdate');
    }

    // hapus fasilitas
    public function destroy($id)
    {
        $data = Fasilitas::find($id);

        if (!$data) {
            // return response()->json([
            //     'message' => 'Data tidak ditemukan'
            // ], 404);

            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // hapus gambar
        if ($data->gambar) {

            Storage::disk('public')
                    ->delete($data->gambar);
        }

        $data->delete();

        // return response()->json([
        //     'message' => 'Fasilitas berhasil dihapus'
        // ], 200);

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus');
    }

        public function indexPublic()
    {
        $data = Fasilitas::where('status', 'aktif')
            ->select(
                'nama_fasilitas',
                'stok',
                'harga',
            )
            ->get();

        // return response()->json($data, 200);

        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus');
    }

    public function fasilitasBooking()
{
    $data = Fasilitas::where(
            'tipe_fasilitas',
            'sewa'
        )
        ->where(
            'status',
            'aktif'
        )
        ->where(
            'stok',
            '>',
            0
        )
        ->latest()
        ->get();

    return response()->json($data, 200);
}
}
