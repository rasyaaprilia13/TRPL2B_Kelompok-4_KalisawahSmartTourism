<?php

namespace App\Http\Controllers\API\Kelola_landingpage\paket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KategoriPaket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KategoriPaketController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN SEMUA DATA
    |--------------------------------------------------------------------------
    |*/
    public function index()
    {
        $kategoris = KategoriPaket::orderBy('id', 'desc')->paginate(6);

        return view(
            'admin.layanan.kategoriPaket.index',
            compact('kategoris')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN 1 DATA
    |--------------------------------------------------------------------------
    |*/
    public function show($id)
    {
        $data = KategoriPaket::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($data, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE DATA
    |--------------------------------------------------------------------------
    |*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tagline'       => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'gambar'        => 'required|image|mimes:jpg,jpeg,png,webp|max:2048', // TETAP REQUIRED SESUAI BLADE
            'hero_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload Thumbnail
        |--------------------------------------------------------------------------
        |*/
        if ($request->hasFile('gambar')) {

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('kategori_paket', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Hero Image
        |--------------------------------------------------------------------------
        |*/
        if ($request->hasFile('hero_image')) {

            $validated['hero_image'] = $request
                ->file('hero_image')
                ->store('kategori_paket/hero', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Slug
        |--------------------------------------------------------------------------
        |*/
        $slug = Str::slug($request->nama_kategori);

        // cek apakah slug sudah dipakai
        $count = KategoriPaket::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . time();
        }

        $validated['slug'] = $slug;

        /*
        |--------------------------------------------------------------------------
        | Simpan Data
        |--------------------------------------------------------------------------
        |*/
        KategoriPaket::create($validated);

        return redirect()
            ->route('admin.kategori-paket.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DATA
    |--------------------------------------------------------------------------
    |*/
    public function update(Request $request, $id)
    {
        $data = KategoriPaket::find($id);

        if (!$data) {
            // DIUBAH: Menggunakan redirect back jika diakses dari Web Admin agar tidak return JSON mentah
            return redirect()
                ->route('admin.kategori-paket.index')
                ->with('error', 'Data tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tagline'       => 'nullable|string|max:255',
            'deskripsi'     => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hero_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update Thumbnail
        |--------------------------------------------------------------------------
        |*/
        if ($request->hasFile('gambar')) {

            // hapus gambar lama
            if (
                $data->gambar &&
                Storage::disk('public')->exists($data->gambar)
            ) {
                Storage::disk('public')->delete($data->gambar);
            }

            $validated['gambar'] = $request
                ->file('gambar')
                ->store('kategori_paket', 'public');
        } else {
            // BENAHI: Mencegah kolom gambar ter-update/tertimpa null di database saat tidak ganti gambar
            unset($validated['gambar']);
        }

        /*
        |--------------------------------------------------------------------------
        | Update Hero Image
        |--------------------------------------------------------------------------
        |*/
        if ($request->hasFile('hero_image')) {

            // hapus hero lama
            if (
                $data->hero_image &&
                Storage::disk('public')->exists($data->hero_image)
            ) {
                Storage::disk('public')->delete($data->hero_image);
            }

            $validated['hero_image'] = $request
                ->file('hero_image')
                ->store('kategori_paket/hero', 'public');
        } else {
            // BENAHI: Mencegah kolom hero_image tertimpa null di database saat tidak ganti gambar
            unset($validated['hero_image']);
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Slug Baru
        |--------------------------------------------------------------------------
        |*/
        // BENAHI: Slug baru hanya digenerate jika user mengubah nama kategori asli
        if ($data->nama_kategori !== $request->nama_kategori) {
            $slug = Str::slug($request->nama_kategori);

            // cek slug selain id saat ini
            $count = KategoriPaket::where('slug', $slug)
                ->where('id', '!=', $id)
                ->count();

            if ($count > 0) {
                $slug = $slug . '-' . time();
            }

            $validated['slug'] = $slug;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Database
        |--------------------------------------------------------------------------
        |*/
        $data->update($validated);

        return redirect()
            ->route('admin.kategori-paket.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE DATA
    |--------------------------------------------------------------------------
    |*/
    public function destroy($id)
    {
        $data = KategoriPaket::find($id);

        if (!$data) {
            // DIUBAH: Menggunakan redirect back jika diakses dari Web Admin agar tidak return JSON mentah
            return redirect()
                ->route('admin.kategori-paket.index')
                ->with('error', 'Data tidak ditemukan');
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Thumbnail
        |--------------------------------------------------------------------------
        |*/
        if (
            $data->gambar &&
            Storage::disk('public')->exists($data->gambar)
        ) {
            Storage::disk('public')->delete($data->gambar);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Hero Image
        |--------------------------------------------------------------------------
        |*/
        if (
            $data->hero_image &&
            Storage::disk('public')->exists($data->hero_image)
        ) {
            Storage::disk('public')->delete($data->hero_image);
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Database
        |--------------------------------------------------------------------------
        |*/
        $data->delete();

        return redirect()
            ->route('admin.kategori-paket.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }
}
