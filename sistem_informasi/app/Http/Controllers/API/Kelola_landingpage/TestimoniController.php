<?php

namespace App\Http\Controllers\API\Kelola_landingpage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimoni;
use Illuminate\Support\Str;

class TestimoniController extends Controller
{
    // Menampilkan halaman form review bagi pengunjung
    public function create()
    {
        return view('pengunjung.landing-page.testimoni.review');
    }

    // Menyimpan data review ke database via AJAX/Fetch
    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'nama' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
            'ulasan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $testimoni = new Testimoni();
        $testimoni->nama = $request->nama;
        $testimoni->instansi = $request->instansi;
        $testimoni->rating = $request->rating;
        $testimoni->ulasan = $request->ulasan;

        // Proses upload foto profil jika ada
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Disimpan ke folder storage/app/public/testimonials
            $path = $file->storeAs('testimonials', $filename, 'public');

            // Catatan: Pastikan nama kolom di migrasi database Anda adalah 'foto_path'
            $testimoni->foto_path = $path;
        }

        $testimoni->save();

        // 3. Mengembalikan response JSON agar klop dengan JavaScript Fetch / AJAX Anda
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Review berhasil dikirim dan menunggu moderasi admin.'
            ], 201);
        }

        // Jalur alternatif jika form diakses tanpa AJAX (Normal submit)
        return redirect()->back()->with('success', 'Review berhasil dikirim dan menunggu moderasi admin.');
    }
}
