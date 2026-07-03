<?php

namespace App\Http\Controllers\API\Pemasukan;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar beserta relasi booking
        $query = Pemasukan::with('booking');

        // 1. Logika Filter Tanggal (Mulai s/d Selesai)
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_masuk', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        // 2. Logika Filter Metode Pembayaran
        if ($request->filled('metode')) {
            $query->where('metode_pemasukan', $request->metode);
        }

        // 3. Logika Pencarian Teks (Cari Kode Pemasukan, Nominal, atau Kode Booking)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pemasukan', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%")
                  ->orWhereHas('booking', function($b) use ($search) {
                      $b->where('kode_booking', 'like', "%{$search}%");
                  });
            });
        }

        $pemasukan = $query->latest()->get();

        // Total omzet bulan ini (Untuk Card Atas)
        $totalPemasukan = Pemasukan::whereMonth('tanggal_masuk', Carbon::now()->month)
                                    ->whereYear('tanggal_masuk', Carbon::now()->year)
                                    ->sum('nominal');

        return view('admin.kelola_operasional.pemasukan', compact('pemasukan', 'totalPemasukan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id'       => 'nullable|exists:bookings,id',
            'pembayaran_id'    => 'nullable',
            'nominal'          => 'required|numeric|min:0',
            'jenis_transaksi'  => 'required|in:dp,lunas',
            'metode_pemasukan' => 'required|in:transfer,cash,qris',
        ]);

        $kodePemasukan = 'REV-' . strtoupper(\Illuminate\Support\Str::random(8));

        Pemasukan::create([
            'kode_pemasukan'   => $kodePemasukan,
            'booking_id'       => $request->booking_id,
            'pembayaran_id'    => $request->pembayaran_id,
            'nominal'          => $request->nominal,
            'jenis_transaksi'  => $request->jenis_transaksi,
            'metode_pemasukan' => $request->metode_pemasukan,
            'tanggal_masuk'    => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Data pemasukan manual berhasil disimpan.');
    }

    // -------------------------------------------------------------------------
    // 🔥 INI FUNGSI SAKTI YANG TADI HILANG / BELUM ADA SAMA SEKALI COK!
    // -------------------------------------------------------------------------
    public function verifikasiPembayaran(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 1. Cari data pembayaran berdasarkan ID yang dikirim dari modal blade
            $pembayaran = Pembayaran::findOrFail($id);

            // Ambil status dari form, default ke 'disetujui' jika admin memvalidasi
            $statusVerifikasi = $request->status ?? $request->status_pembayaran ?? 'disetujui';

            // 2. Update status tabel pembayaran pengunjung jadi disetujui / valid
            $pembayaran->update([
                'status' => $statusVerifikasi,
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran ?? now()
            ]);

            // 3. Hubungkan ke Master Bookingnya
            $booking = Booking::find($pembayaran->booking_id);

            if ($booking) {
                // Ikut update status pembayaran di tabel bookings menjadi sesuai tipe pembayaran (dp/lunas)
                $booking->update([
                    'status_pembayaran' => strtolower($pembayaran->tipe_pembayaran),
                    'status_booking' => 'dikonfirmasi'
                ]);
            }

            // 4. SINKRONISASI KE TABEL PEMASUKAN (Wajib masuk ke laporan keuangan!)
            $jenisTransaksiBersih = in_array(strtolower($pembayaran->tipe_pembayaran), ['dp', 'lunas'])
                                    ? strtolower($pembayaran->tipe_pembayaran)
                                    : 'lunas';

            Pemasukan::updateOrCreate(
                ['booking_id' => $pembayaran->booking_id], // Cari berdasarkan booking id, kalau belum ada dibuat baru
                [
                    'pembayaran_id'    => $pembayaran->id,
                    'kode_pemasukan'   => 'REV-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'nominal'          => $pembayaran->nominal > 0 ? $pembayaran->nominal : ($booking->total_harga_final ?? 0),
                    'jenis_transaksi'  => $jenisTransaksiBersih,
                    'metode_pemasukan' => strtolower($pembayaran->metode_pembayaran ?? 'transfer'),
                    'tanggal_masuk'    => now()
                ]
            );

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran booking pengunjung VALID dan BERHASIL dicatat ke Pemasukan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memverifikasi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return redirect()->back()->with('success', 'Data pemasukan berhasil dihapus.');
    }
}
