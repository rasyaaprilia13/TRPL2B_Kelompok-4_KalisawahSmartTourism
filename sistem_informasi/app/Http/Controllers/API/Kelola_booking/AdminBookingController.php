<?php

namespace App\Http\Controllers\API\Kelola_booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingFasilitas;
use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Pemasukan;
use App\Models\PaketWisata;

class AdminBookingController extends Controller
{
   public function index(Request $request)
    {
        $query = Booking::with(['items.paketWisata', 'fasilitas.fasilitas', 'pembayaran'])->latest();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_kunjungan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_kunjungan', $request->tahun);
        } else {
            $query->whereYear('tanggal_kunjungan', date('Y'));
        }

        // REVISI: Menggunakan paginate(10) agar muncul maksimal 10 per halaman dengan tombol Next/Prev
        $data = $query->paginate(10);
        // Pertahankan query string saat next page
        $data->appends($request->all());

        $fasilitasList = Fasilitas::where('tipe_fasilitas', 'sewa')->get();
        $paketList = PaketWisata::all();

        // -------------------------------------------------------------------------
        // PERBAIKAN: Menyediakan variabel mentah yang diminta oleh index.blade.php
        // -------------------------------------------------------------------------
        $pakets = $paketList;
        $fasilitas = $fasilitasList;
        // -------------------------------------------------------------------------

        $groupedFasilitas = [];
        foreach ($fasilitasList as $f) {
            $katName = $this->parseKategoriName($f->kategori);
            $groupedFasilitas[$katName][] = $f;
        }

        $groupedPaket = [
            'Semua Paket' => []
        ];

        foreach ($paketList as $p) {
            $katName = $this->parseKategoriName($p->kategori);
            $groupedPaket['Semua Paket'][] = $p;
            if ($katName !== 'Semua Paket') {
                $groupedPaket[$katName][] = $p;
            }
        }

        // PERBAIKAN: Menambahkan 'pakets' dan 'fasilitas' ke dalam compact()
        return view('admin.kelola_booking.index', compact('data', 'groupedFasilitas', 'groupedPaket', 'pakets', 'fasilitas'));
    }
    private function parseKategoriName($kategori)
    {
        if (empty($kategori)) return 'Semua Paket';

        if (is_object($kategori)) {
            return $kategori->nama_kategori ?? $kategori->NAMA_KATEGORI ?? 'Semua Paket';
        }

        if (is_array($kategori)) {
            return $kategori['NAMA_KATEGORI'] ?? $kategori['nama_kategori'] ?? 'Semua Paket';
        }

        if (is_string($kategori)) {
            $dec = json_decode(html_entity_decode($kategori), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($dec)) {
                return $dec['NAMA_KATEGORI'] ?? $dec['nama_kategori'] ?? 'Semua Paket';
            }
            if (preg_match('/"NAMA_KATEGORI"\s*:\s*"([^"]+)"/i', $kategori, $matches) || preg_match('/"nama_kategori"\s*:\s*"([^"]+)"/i', $kategori, $matches)) {
                return $matches[1];
            }
            if (strtoupper($kategori) === 'UMUM') {
                return 'Semua Paket';
            }
            return $kategori;
        }

        return 'Semua Paket';
    }

    public function show($id)
    {
        $booking = Booking::with(['items.paketWisata', 'fasilitas.fasilitas', 'pembayaran'])->findOrFail($id);
        return view('admin.kelola_booking.show', compact('booking'));
    }

  public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // -----------------------------------------------------------------
            // TAMBAHAN 1: Tentukan status pembayaran secara dinamis & aman
            // -----------------------------------------------------------------
            $nominalBayar = round($request->nominal ?? 0);
            $totalHargaFinal = round($request->total_harga_final ?? 0);

            if ($nominalBayar >= $totalHargaFinal && $totalHargaFinal > 0) {
                $statusPembayaran = 'lunas';
            } elseif ($nominalBayar > 0 && $nominalBayar < $totalHargaFinal) {
                $statusPembayaran = 'dp';
            } else {
                $statusPembayaran = 'belum_bayar';
            }
            // -----------------------------------------------------------------

            $booking = Booking::create([
                'kode_booking' => 'BK-' . time(),
                'nama_pemesan' => $request->nama_pemesan,
                'no_hp' => $request->no_hp,
                'tanggal_kunjungan' => $request->tanggal_kunjungan,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jam' => $request->jam ?? '14:00', // Default jam jika user tidak input
                'jumlah_pengunjung' => $request->jumlah_pengunjung,
                'catatan' => $request->catatan,
                'jumlah_malam' => $request->jumlah_malam,
                'total_harga' => round($request->total_harga),
                'total_harga_final' => $totalHargaFinal,
                'diskon_manual' => round($request->diskon_manual ?? 0),
                'jumlah_tiket_tambahan' => $request->jumlah_tiket_tambahan ?? 0,
                'subtotal_tiket_tambahan' => round($request->subtotal_tiket_tambahan ?? 0),
                'status_booking' => 'dikonfirmasi',

                // TAMBAHAN 2: Masukkan status pembayaran ke tabel booking
                'status_pembayaran' => $statusPembayaran
            ]);

            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran_dp', 'public');
            }

            Pembayaran::create([
                'booking_id' => $booking->id,
                'tipe_pembayaran' => $request->tipe_pembayaran,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nominal' => $nominalBayar,
                'bukti_pembayaran' => $buktiPath,

                // TAMBAHAN 3: Set status pembayaran 'disetujui' karena diinput langsung oleh admin
                'status' => 'disetujui',
                'tanggal_pembayaran' => now()
            ]);

            // -----------------------------------------------------------------
            // TAMBAHAN 4: Otomatis catat ke tabel Pemasukan jika berstatus Lunas / DP
            // -----------------------------------------------------------------
            if ($statusPembayaran === 'lunas' || $statusPembayaran === 'dp') {
                Pemasukan::create([
                    'booking_id'       => $booking->id,
                    'pembayaran_id'    => null,
                    'kode_pemasukan'   => 'PEM-' . time(),
                    'nominal'          => $nominalBayar,

                    // UBAH BARIS INI: Gunakan kata standar yang muat di database
                    'jenis_transaksi'  => $request->tipe_pembayaran,

                    'metode_pemasukan' => $request->metode_pembayaran ?? 'cash',
                    'tanggal_masuk'    => now()
                ]);
            }
            // -----------------------------------------------------------------


            if ($request->has('paket')) {
                foreach ($request->paket as $p) {

                    if (!isset($p['paket_id'], $p['qty'], $p['hari'])) continue;

                    $paket = \App\Models\PaketWisata::find($p['paket_id']);
                    if (!$paket) continue;

                    $harga = $paket->harga;

                    $tanggal = \Carbon\Carbon::parse($request->tanggal_kunjungan)
                                ->addDays($p['hari'] - 1)
                                ->format('Y-m-d');

                    $subtotal = $p['qty'] * $harga;
                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'paket_wisata_id' => $p['paket_id'],
                        'hari' => $p['hari'],
                        'tanggal' => $tanggal,
                        'qty' => $p['qty'],
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $f) {

                    if (!isset($f['fasilitas_id'], $f['qty'], $f['hari'])) continue;

                    $fas = \App\Models\Fasilitas::find($f['fasilitas_id']);
                    if (!$fas) continue;

                    $harga = $fas->harga;

                    $tanggal = \Carbon\Carbon::parse($request->tanggal_kunjungan)
                                ->addDays($f['hari'] - 1)
                                ->format('Y-m-d');

                    $subtotal = $f['qty'] * $harga;

                    \App\Models\BookingFasilitas::create([
                        'booking_id' => $booking->id,
                        'fasilitas_id' => $f['fasilitas_id'],
                        'hari' => $f['hari'],
                        'tanggal' => $tanggal,
                        'qty' => $f['qty'],
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            DB::commit();
            return redirect('/admin/booking-admin')->with('success', 'Data booking berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
  public function update(Request $request, $id)
{
        DB::beginTransaction();
        try {
            $booking = Booking::with('pembayaran')->findOrFail($id);
            $diskonBaru = round($request->diskon_manual ?? $booking->diskon_manual);

            $tanggalAktif = $booking->tanggal_kunjungan;
            if (!empty($request->tanggal_reschedule)) {
                $tanggalAktif = $request->tanggal_reschedule;
            }

            // 1. Back-up data item lama ke memory sebelum dihapus dari database
            $oldItems = $booking->items()->get()->keyBy('id');
            $oldFasilitas = $booking->fasilitas()->get()->keyBy('id');

            // Bersihkan relasi paket & fasilitas lama di database
            $booking->items()->delete();
            $booking->fasilitas()->delete();

            $totalPaket = 0;
            $kapasitasPerHari = [];

            // 2. Simpan ulang Paket Wisata
            if ($request->has('paket')) {
                foreach ($request->paket as $key => $p) {
                    $paketId = $p['paket_wisata_id'] ?? $p['paket_id'] ?? $p['id'] ?? null;

                    if (!$paketId && $oldItems->has($key)) {
                        $paketId = $oldItems->get($key)->paket_wisata_id;
                    }

                    if (!$paketId) continue;

                    $paketModel = PaketWisata::find($paketId);
                    if (!$paketModel) continue;

                    $qty = $p['qty'] ?? 1;
                    $hari = $p['hari'] ?? 1;
                    $subtotal = $qty * $paketModel->harga;
                    $totalPaket += $subtotal;

                    if (!isset($kapasitasPerHari[$hari])) {
                        $kapasitasPerHari[$hari] = 0;
                    }
                    if ($paketModel->kapasitas > 1) {
                        $kapasitasPerHari[$hari] += ($qty * $paketModel->kapasitas);
                    }

                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'paket_wisata_id' => $paketId,
                        'hari' => $hari,
                        'tanggal' => Carbon::parse($tanggalAktif)->addDays($hari - 1)->format('Y-m-d'),
                        'qty' => $qty,
                        'harga' => $paketModel->harga,
                        'subtotal' => $subtotal
                    ]);
                }
            }

            $totalFasilitas = 0;
            // 3. Simpan ulang Fasilitas
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $key => $f) {
                    $fasilitasId = $f['fasilitas_id'] ?? $f['id'] ?? null;

                    if (!$fasilitasId && $oldFasilitas->has($key)) {
                        $fasilitasId = $oldFasilitas->get($key)->fasilitas_id;
                    }

                    if (!$fasilitasId) continue;

                    $fasModel = Fasilitas::find($fasilitasId);
                    if (!$fasModel) continue;

                    $qty = $f['qty'] ?? 1;
                    $hari = $f['hari'] ?? 1;
                    $subtotal = $qty * $fasModel->harga;
                    $totalFasilitas += $subtotal;

                    BookingFasilitas::create([
                        'booking_id' => $booking->id,
                        'fasilitas_id' => $fasilitasId,
                        'hari' => $hari,
                        'tanggal' => Carbon::parse($tanggalAktif)->addDays($hari - 1)->format('Y-m-d'),
                        'qty' => $qty,
                        'harga' => $fasModel->harga,
                        'subtotal' => $subtotal
                    ]);
                }
            }

            // 4. Hitung ulang tiket tambahan secara otomatis
            $kapasitasMaksimal = !empty($kapasitasPerHari) ? max($kapasitasPerHari) : 0;
            $jumlahPengunjung = $request->jumlah_pengunjung ?? $booking->jumlah_pengunjung;

            $tiketTambahanQty = 0;
            if ($kapasitasMaksimal > 0 && $jumlahPengunjung > $kapasitasMaksimal) {
                $tiketTambahanQty = $jumlahPengunjung - $kapasitasMaksimal;
            }
            $subtotalTiket = $tiketTambahanQty * 25000;

            // 5. Kalkulasi Final Keuangan
            $totalHargaSistem = $totalPaket + $totalFasilitas + $subtotalTiket;
            $hargaFinalBaru = $totalHargaSistem - $diskonBaru;
            if ($hargaFinalBaru < 0) {
                $hargaFinalBaru = 0;
            }

            // -------------------------------------------------------------------------
            // PERBAIKAN RADIKAL: MENANGKAP SEGALA BENTUK INPUT VALIDASI STATUS
            // -------------------------------------------------------------------------
            // Ambil input dari segala kemungkinan nama field di form blade admin kamu
            $statusInput = $request->status_pembayaran ?? $request->status ?? $request->status_booking ?? $booking->status_pembayaran;
            $statusPembayaranBaru = strtolower(trim($statusInput));

            // Jika admin memilih status 'lunas', 'dp', ATAU status booking dikonfirmasi/disetujui
            if (in_array($statusPembayaranBaru, ['lunas', 'dp', 'dikonfirmasi', 'disetujui'])) {

                // Ambil data pembayaran milik pengunjung
                $pembayaranTerakhir = $booking->pembayaran()->latest()->first();

                // Normalisasi tipe transaksi wajib 'dp' atau 'lunas' agar sesuai ENUM PemasukanController
                $jenisTransaksiBersih = in_array($statusPembayaranBaru, ['dp', 'lunas']) ? $statusPembayaranBaru : 'lunas';

                // Jika data pembayaran dari pengunjung belum ada di DB, paksa buat di sini
                if (!$pembayaranTerakhir) {
                    $pembayaranTerakhir = Pembayaran::create([
                        'booking_id' => $booking->id,
                        'tipe_pembayaran' => $jenisTransaksiBersih,
                        'metode_pembayaran' => $request->metode_pembayaran ?? 'transfer',
                        'nominal' => $hargaFinalBaru,
                        'status' => 'disetujui',
                        'tanggal_pembayaran' => now()
                    ]);
                } else {
                    // Jika sudah ada (data pending dari pengunjung), paksa ubah jadi disetujui
                    $pembayaranTerakhir->update([
                        'status' => 'disetujui',
                        'tipe_pembayaran' => $jenisTransaksiBersih,
                        'tanggal_pembayaran' => $pembayaranTerakhir->tanggal_pembayaran ?? now()
                    ]);
                }

                // Ambil nominal uang asli
                $nominalMasuk = $pembayaranTerakhir->nominal > 0 ? $pembayaranTerakhir->nominal : $hargaFinalBaru;

                if ($nominalMasuk > 0) {
                    // UPDATE ATAU CREATE: Sinkronisasi langsung ke tabel Pemasukan
                    Pemasukan::updateOrCreate(
                        ['booking_id' => $booking->id],
                        [
                            'pembayaran_id'    => $pembayaranTerakhir->id,
                            'kode_pemasukan'   => 'REV-' . strtoupper(\Illuminate\Support\Str::random(8)),
                            'nominal'          => $nominalMasuk,
                            'jenis_transaksi'  => $jenisTransaksiBersih, // Dipastikan bernilai 'dp' atau 'lunas'
                            'metode_pemasukan' => strtolower($pembayaranTerakhir->metode_pembayaran ?? $request->metode_pemasukan ?? 'transfer'),
                            'tanggal_masuk'    => now()
                        ]
                    );
                }
            }
            // -------------------------------------------------------------------------

            // 6. Update ke master data Booking
            $booking->update([
                'nama_pemesan' => $request->nama_pemesan ?? $booking->nama_pemesan,
                'no_hp' => $request->no_hp ?? $booking->no_hp,
                'jumlah_pengunjung' => $jumlahPengunjung,
                'tanggal_reschedule' => $request->tanggal_reschedule,
                'alasan_reschedule' => $request->alasan_reschedule,
                'jumlah_reschedule' => $request->jumlah_reschedule ?? 0,
                'diskon_manual' => $diskonBaru,
                'jumlah_tiket_tambahan' => $tiketTambahanQty,
                'subtotal_tiket_tambahan' => $subtotalTiket,
                'total_harga' => $totalHargaSistem,
                'total_harga_final' => $hargaFinalBaru,
                'status_booking' => $request->status_booking ?? $booking->status_booking,
                'status_pembayaran' => in_array($statusPembayaranBaru, ['lunas', 'dp']) ? $statusPembayaranBaru : $booking->status_pembayaran,
            ]);

            DB::commit();
            return back()->with('success', 'Data pemesanan dan tagihan berhasil diupdate serta dicatat ke pemasukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $booking = Booking::with(['fasilitas.fasilitas'])->findOrFail($id);

        DB::beginTransaction();

        try {
            foreach ($booking->fasilitas as $item) {
                if ($item->fasilitas && $item->fasilitas->tipe_fasilitas === 'sewa') {
                    $item->fasilitas->increment('stok', $item->qty);
                }
            }

            $booking->items()->delete();
            $booking->fasilitas()->delete();

            foreach($booking->pembayaran as $pembayaran) {
                if ($pembayaran->bukti_pembayaran) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }
                $pembayaran->delete();
            }

            $booking->delete();

            DB::commit();

            return back()->with('success', 'Booking berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
