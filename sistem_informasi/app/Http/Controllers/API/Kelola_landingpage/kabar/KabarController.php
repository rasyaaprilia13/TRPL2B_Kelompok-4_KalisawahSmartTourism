<?php

namespace App\Http\Controllers\API\Kelola_landingpage\kabar;

use App\Http\Controllers\Controller;
use App\Models\Kabar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KabarController extends Controller
{
    // GET semua kabar
    public function index()
    {
        // return response()->json(Kabar::all(), 200);
        // $kabars = Kabar::paginate(10);
        $kabars = Kabar::orderBy('created_at', 'desc')->paginate(6);
        return view('admin.kelola_halaman.kabar', compact('kabars'));
    }

    // GET detail kabar
    public function show($id)
    {
        $kabar = Kabar::find($id);

        if (!$kabar) {
            return response()->json(['message' => 'Kabar tidak ditemukan'], 404);
        }

        // return response()->json($kabar, 200);
        return view('admin.kelola_halaman.kabar_detail', compact('kabar'));
    }

    // POST tambah kabar
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'       => 'required|string|max:255',
            'isi_kabar'  => 'required',
            'tanggal'     => 'required|date',
            'foto'        => 'required|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        // simpan foto ke storage dengan sudah dibuat nama unik
        $path = $request->file('foto')->store('kabar', 'public');

        $kabar = Kabar::create([
            'judul'       => $validated['judul'],
            'isi_kabar'  => $validated['isi_kabar'],
            'tanggal'     => $validated['tanggal'],
            'foto'        => $path,
            'slug'       => Str::slug($validated['judul']),
        ]);

        // return response()->json([
        //     'message' => 'Kabar berhasil ditambahkan',
        //     'data'    => $kabar
        // ], 201);

        return redirect()->route('admin.kabar.index')->with('success', 'Kabar berhasil ditambahkan');
    }

    // PUT update kabar
    public function update(Request $request, $id)
{
    // Cari data kabar berdasarkan ID
    $kabar = Kabar::findOrFail($id);

    // Validasi input dari form
    $validated = $request->validate([
        'judul' => 'sometimes|required|string|max:255',
        'isi_kabar' => 'sometimes|required',
        'tanggal' => 'sometimes|required|date',
        'foto' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Masukkan hasil validasi ke variabel data
    $data = $validated;

    // Cek apakah user upload foto baru
    if ($request->hasFile('foto')) {

        if ($kabar->foto && Storage::disk('public')->exists($kabar->foto)) {
            Storage::disk('public')->delete($kabar->foto);
        }

        // Simpan foto baru ke storage/public/kabar
        $data['foto'] = $request->file('foto')->store('kabar', 'public');
    }

    if (isset($validated['judul'])) {
        $data['slug'] = Str::slug($validated['judul']);
    }

    // Update data ke database
    $kabar->update($data);

    return redirect()->route('admin.kabar.index')->with('success', 'Kabar berhasil diperbarui');
}

    // DELETE kabar
    public function destroy($id)
    {
        $kabar = Kabar::find($id);

        if (!$kabar) {
            return response()->json(['message' => 'Kabar tidak ditemukan'], 404);
        }

        // hapus foto dari storage
        if ($kabar->foto && Storage::exists($kabar->foto)) {
            Storage::delete($kabar->foto);
        }

        $kabar->delete();

        // return response()->json(['message' => 'Kabar berhasil dihapus'], 200);
        return redirect()->route('admin.kabar.index')->with('success', 'Kabar berhasil dihapus');

    }

    // // GET tampilkan foto (private access)
    // public function getFoto($id)
    // {
    //     $kabar = Kabar::find($id);

    //     if (!$kabar || !Storage::exists($kabar->foto)) {
    //         return response()->json(['message' => 'Foto tidak ditemukan'], 404);
    //     }

    //     $file = Storage::get($kabar->foto);
    //     $type = Storage::mimeType($kabar->foto);

    //     return response($file, 200)->header('Content-Type', $type);
    // }

    // PENGUNJUNG: list kabar
    public function publicIndex()
    {
    $kabars = Kabar::orderBy('created_at', 'desc')->paginate(6);

    return view('pengunjung.landing-page.kabar.kabar', compact('kabars'));
}

    // PENGUNJUNG: detail kabar
    public function publicShow($slug)
{
    $kabar = Kabar::where('slug', $slug)->firstOrFail();

    $relatedKabars = Kabar::where('id', '!=', $kabar->id)
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();

    return view('pengunjung.landing-page.kabar.detail', compact('kabar', 'relatedKabars'));
}
}
