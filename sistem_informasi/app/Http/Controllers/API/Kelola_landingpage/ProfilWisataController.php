<?php

namespace App\Http\Controllers\API\Kelola_landingpage;

use App\Http\Controllers\Controller;
use App\Models\ProfilWisata;
use App\Models\KategoriPaket;
use App\Models\testimoni;
use App\Models\Kabar;
use App\Models\LandingSetting;
use Illuminate\Http\Request;

class ProfilWisataController extends Controller
{
    public function index()
    {
        $categories = KategoriPaket::all();
        $kabars = Kabar::latest('tanggal')->take(3)->get();

        // AMBIL DATA LOGO INSTANSI DARI TABEL client_logos
        $experiences = \DB::table('client_logos')->get();

        // 2. AMBIL DATA SETTING LANDING PAGE TERBARU
        $landingSetting = LandingSetting::first();

        $testimoni = \App\Models\Testimoni::latest()->get();

        // Kirim semua variabel ($categories, $kabars, $experiences, $landingSetting) ke view home
        return view('pengunjung.landing-page.home', compact('categories', 'kabars', 'experiences', 'landingSetting', 'testimoni'));
    }

    /**
     * METHOD BARU: Menampilkan daftar paket berdasarkan kategori yang dipilih dari Home
     */
    public function detailKategori($id)
    {
        $kategori = \App\Models\KategoriPaket::findOrFail($id);

        // Mengambil paket wisata yang terhubung dengan kategori ini
        $pakets = \App\Models\PaketWisata::where('kategori_paket_id', $kategori->id)
            ->with('fasilitas')
            ->get();

        // Mengarahkan ke halaman detail khusus list paket
        return view('pengunjung.halaman-paket.detail-paket', compact('kategori', 'pakets'));
    }

    public function kabarIndex()
    {
        $kabars = \App\Models\Berita::latest('tanggal')->paginate(6);
        return view('pengunjung.landing-page.kabar.index', compact('kabars'));
    }

    public function showKabar($slug)
    {
        $kabars = \App\Models\Berita::all();
        $berita = $kabars->first(function ($item) use ($slug) {
            return \Illuminate\Support\Str::slug($item->judul) === $slug;
        });

        if (!$berita) {
            abort(404);
        }

        $relatedKabars = \App\Models\Berita::where('id', '!=', $berita->id)
            ->latest('tanggal')
            ->take(3)
            ->get();

        return view('pengunjung.landing-page.kabar.detail', compact('berita', 'relatedKabars'));
    }

    public function camping()
    {
        $kategori = \App\Models\KategoriPaket::where('nama_kategori', 'like', '%camping%')->first();

        if ($kategori) {
            $pakets = \App\Models\PaketWisata::where(function($query) use ($kategori) {
                    $query->where('kategori_paket_id', $kategori->kategori_paket_id ?? $kategori->id)
                          ->orWhere('kategori_paket_id', $kategori->id);
                })
                ->with('fasilitas')
                ->get();
        } else {
            $pakets = collect();
        }

        return view('pengunjung.landing-page.halaman-paket.detail-paket', compact('pakets'));
    }

    public function adventureGame()
    {
        $kategori = \App\Models\KategoriPaket::where('nama_kategori', 'like', '%adventure%')
            ->orWhere('nama_kategori', 'like', '%panahan%')
            ->orWhere('nama_kategori', 'like', '%shooting%')
            ->first();

        if ($kategori) {
            $pakets = \App\Models\PaketWisata::where(function($query) use ($kategori) {
                    $query->where('id_kategori', $kategori->id_kategori ?? $kategori->id)
                          ->orWhere('kategori_paket_id', $kategori->id);
                })
                ->with('fasilitas')
                ->get();
        } else {
            $pakets = collect();
        }

        if ($pakets->isEmpty()) {
            $pakets = collect([
                (object)[
                    'id_paket' => 991,
                    'nama_paket' => 'SHOOTING TARGET',
                    'harga' => 10000,
                    'deskripsi' => "5 Peluru tembak sasaran\nSenapan angin target standar\nProtector, safety goggles, & instruksi keselamatan",
                    'gambar' => 'https://images.unsplash.com/photo-1595590424283-b8f17842773f?q=80&w=600&auto=format&fit=crop'
                ],
                (object)[
                    'id_paket' => 992,
                    'nama_paket' => 'PANAHAN',
                    'harga' => 15000,
                    'deskripsi' => "10 Anak Panah\nBusur panah (recurve bow) standar pemula\nFinger tab, arm guard, & instruksi teknik dasar",
                    'gambar' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?q=80&w=600&auto=format&fit=crop'
                ]
            ]);
        }

        return view('pengunjung.landing-page.halaman-paket.adventure-game', compact('pakets'));
    }

    public function bookingAdventureGame()
    {
        $kategori = \App\Models\KategoriPaket::where('nama_kategori', 'like', '%adventure%')
            ->orWhere('nama_kategori', 'like', '%panahan%')
            ->orWhere('nama_kategori', 'like', '%shooting%')
            ->first();

        $pakets = collect();
        if ($kategori) {
            $pakets = \App\Models\PaketWisata::where(function($query) use ($kategori) {
                    $query->where('id_kategori', $kategori->id_kategori ?? $kategori->id)
                          ->orWhere('kategori_paket_id', $kategori->id);
                })
                ->get();
        }

        if ($pakets->isEmpty()) {
            $pakets = collect([
                (object)['nama_paket' => 'SHOOTING TARGET', 'harga' => 10000],
                (object)['nama_paket' => 'PANAHAN', 'harga' => 15000]
            ]);
        }

        return view('pengunjung.booking.adventure-game', compact('kategori', 'pakets'));
    }

    public function create()
    {
        return view('profil.create');
    }

    public function store(Request $request)
    {
        ProfilWisata::create($request->all());
        return redirect('/profil');
    }

    public function edit($id)
    {
        $data = ProfilWisata::findOrFail($id);
        return view('profil.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        ProfilWisata::findOrFail($id)->update($request->all());
        return redirect('/profil');
    }

    public function destroy($id)
    {
        ProfilWisata::destroy($id);
        return redirect()->back();
    }

    public function showKategori($slug)
    {
        $kategori = \App\Models\KategoriPaket::where('slug', $slug)->firstOrFail();

        $pakets = \App\Models\PaketWisata::with('fasilitas')
            ->where('kategori_paket_id', $kategori->id)
            ->get();

        return view(
            'pengunjung.halaman-paket.detail-paket',
            compact('kategori', 'pakets')
        );
    }

}
