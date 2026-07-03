<?php

namespace App\Http\Controllers\API\Booking_pengunjung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\PaketWisata;
use App\Models\Fasilitas;
use App\Models\KategoriPaket;
use App\Models\KategoriFasilitas;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

class PengunjungBookingController extends Controller
{
    /* =========================================================
        1. FORM
    ========================================================= */

    public function showForm(Request $request)
    {
        return view('pengunjung.booking.booking-form', [
            'kategoriPaket' => KategoriPaket::all(),
            'paket' => PaketWisata::with('kategori')->where('status', 'aktif')->get(),
            'kategoriFasilitas' => KategoriFasilitas::all(),
            'fasilitas' => Fasilitas::with('kategori')->where('tipe_fasilitas', 'sewa')->where('stok', '>', 0)->get(),
            'draftBooking' => $request->session()->get('temp_booking_data', []),
        ]);
    }

    public function getPaketByKategori($id)
    {
        return response()->json(
            PaketWisata::where('kategori_paket_id', $id)->where('status', 'aktif')->get()
        );
    }

    public function getFasilitasByKategori($kategoriId)
    {
        return response()->json(
            Fasilitas::where('kategori_fasilitas_id', $kategoriId)->where('tipe_fasilitas', 'sewa')->where('stok', '>', 0)->get()
        );
    }

    /* =========================================================
        2. REVIEW BOOKING
    ========================================================= */

    public function review(Request $request)
    {
        $request->validate([
            'nama_pemesan' => 'required|string',
            'no_hp' => ['required', 'digits_between:10,13', 'regex:/^08[0-9]+$/'],
            'tanggal_kunjungan' => 'required|date',
            'tanggal_checkout' => 'required|date',
            'jumlah_pengunjung' => 'required|integer|min:1',
            'jumlah_malam' => 'required|integer|min:1',
            'jam' => 'required'
        ], [
            'no_hp.regex' => 'Nomor HP harus diawali 08 dan hanya boleh berisi angka.',
            'no_hp.digits_between' => 'Nomor HP harus terdiri dari 10 sampai 13 digit.'
        ]);

        $input = $request->all();
        $request->session()->put('temp_booking_data', $input);

        return redirect()->route('pengunjung.booking.review.show');
    }

    public function showReview(Request $request)
    {
        $input = session('temp_booking_data');

        if (empty($input)) {
            return redirect()
                ->route('pengunjung.booking.booking-form')
                ->with('error', 'Sesi booking telah berakhir. Silakan ulangi pemesanan.');
        }

        $booking = $this->prepareBookingPreview($input);

        return view('pengunjung.booking.booking-detail', [
            'booking' => $booking,
            'is_preview' => true
        ]);
    }

    private function prepareBookingPreview(array $input)
    {
        $booking = (object) $input;
        $jumlahMalam = (int) ($input['jumlah_malam'] ?? 1);
        $jumlahHari = $jumlahMalam + 1;

        $structuredItems = collect();
        $structuredFasilitas = collect();
        $total = 0;
        $tanggalAwal = Carbon::parse($input['tanggal_kunjungan']);

        if (!empty($input['paket'])) {
            foreach ($input['paket'] as $hari => $paketIds) {
                foreach ($paketIds as $paketId) {
                    $paket = PaketWisata::find($paketId);
                    if (!$paket) continue;

                    $qty = (int) ($input['paket_qty'][$hari][$paketId] ?? 1);
                    $subtotal = $paket->harga * $qty;
                    $tanggalItem = $tanggalAwal->copy()->addDays((int)$hari)->toDateString();

                    $structuredItems->push((object)[
                        'hari' => (int)$hari,
                        'tanggal' => $tanggalItem,
                        'paket_wisata_id' => $paketId,
                        'qty' => $qty,
                        'harga' => $paket->harga,
                        'subtotal' => $subtotal,
                        'paketWisata' => $paket
                    ]);

                    $total += $subtotal;
                }
            }
        }

        if (!empty($input['fasilitas'])) {
            foreach ($input['fasilitas'] as $hari => $fasList) {
                foreach ($fasList as $fasId => $qty) {
                    $qty = (int) $qty;
                    if ($qty <= 0) continue;

                    $fas = Fasilitas::find($fasId);
                    if (!$fas) continue;

                    $subtotal = $fas->harga * $qty;
                    $tanggalFasilitas = $tanggalAwal->copy()->addDays((int)$hari)->toDateString();

                    $structuredFasilitas->push((object)[
                        'hari' => (int)$hari,
                        'tanggal' => $tanggalFasilitas,
                        'fasilitas_id' => $fasId,
                        'qty' => $qty,
                        'harga' => $fas->harga,
                        'subtotal' => $subtotal,
                        'fasilitas' => $fas
                    ]);

                    $total += $subtotal;
                }
            }
        }

        if (!empty($input['lahan'])) {
            foreach ($input['lahan'] as $hari => $fasId) {
                if (!$fasId) continue;

                $fas = Fasilitas::find($fasId);
                if (!$fas) continue;

                $qty = 1;
                $subtotal = $fas->harga;
                $tanggalLahan = $tanggalAwal->copy()->addDays((int)$hari)->toDateString();

                $structuredFasilitas->push((object)[
                    'hari' => (int)$hari,
                    'tanggal' => $tanggalLahan,
                    'fasilitas_id' => $fasId,
                    'qty' => $qty,
                    'harga' => $fas->harga,
                    'subtotal' => $subtotal,
                    'fasilitas' => $fas
                ]);

                $total += $subtotal;
            }
        }

       /* =========================================================
            PROSES HITUNG TIKET TAMBAHAN (OVER CAPACITY)
           ========================================================= */
        $jumlahPengunjung = (int) ($input['jumlah_pengunjung'] ?? 0);
        $kapasitasPerHari = [];

        foreach ($structuredItems as $item) {
            $hari = $item->hari;
            if (!isset($kapasitasPerHari[$hari])) {
                $kapasitasPerHari[$hari] = 0;
            }

            // ⭐ PERBAIKAN DI SINI: Deteksi paket aktivitas agar tidak merusak hitungan kapasitas utama
            $namaPaket = strtolower($item->paketWisata->nama_paket ?? '');
            if (str_contains($namaPaket, 'advanture') || str_contains($namaPaket, 'rafting')) {
                continue; // Abaikan paket aktivitas dari perhitungan batas over-capacity
            }

            $kapasitasPerHari[$hari] += ($item->paketWisata->kapasitas ?? 0) * $item->qty;
        }

        // Ambil kapasitas penginapan tertinggi yang tersedia pada hari tersebut
        $kapasitasMaksimal = !empty($kapasitasPerHari) ? max($kapasitasPerHari) : 0;
        $kelebihan = 0;
        $extra = 0;

        // Hitung selisih jika jumlah orang yang datang lebih banyak daripada kapasitas tenda/paket inap
        if ($kapasitasMaksimal > 0 && $jumlahPengunjung > $kapasitasMaksimal) {
            $kelebihan = $jumlahPengunjung - $kapasitasMaksimal; // Contoh: 4 orang - 2 (kapasitas camping) = 2 orang kelebihan
            $extra = $kelebihan * 25000; // 2 * 25.000 = 50.000
            $total += $extra; // Tambahkan biaya ke total harga review
        }

        $booking->jumlah_hari = $jumlahHari;
        $booking->jumlah_tiket_tambahan = $kelebihan;
        $booking->subtotal_tiket_tambahan = $extra;
        $booking->harga_tiket = 25000;
        $booking->items = $structuredItems;
        $booking->fasilitas = $structuredFasilitas;
        $booking->total_harga_final = $total;
        $booking->tanggal_selesai = $input['tanggal_checkout'] ?? null;

        return $booking;
    }

    /* =========================================================
        3. DETAIL BOOKING
    ========================================================= */

    public function showDetail($id)
    {
        $booking = Booking::with(['items.paketWisata', 'fasilitas.fasilitas'])->findOrFail($id);
        $total = 0;

        foreach ($booking->items as $item) {
            $item->subtotal = $item->harga * $item->qty;
            $total += $item->subtotal;
        }

        foreach ($booking->fasilitas as $fas) {
            $fas->subtotal = $fas->harga * $fas->qty;
            $total += $fas->subtotal;
        }

        $kapasitasPerHari = [];
        foreach ($booking->items as $item) {
            $hari = $item->hari;
            if (!isset($kapasitasPerHari[$hari])) {
                $kapasitasPerHari[$hari] = 0;
            }
            $kapasitasPerHari[$hari] += ($item->paketWisata->kapasitas ?? 0) * $item->qty;
        }

        $kapasitasMaksimal = !empty($kapasitasPerHari) ? max($kapasitasPerHari) : 0;
        $extraCharge = 0;
        $jumlahTiketTambahan = 0;

        if ($booking->jumlah_pengunjung > $kapasitasMaksimal) {
            $jumlahTiketTambahan = $booking->jumlah_pengunjung - $kapasitasMaksimal;
            $extraCharge = $jumlahTiketTambahan * 25000;
            $total += $extraCharge;
        }

        $booking->jumlah_tiket_tambahan = $jumlahTiketTambahan;
        $booking->subtotal_tiket_tambahan = $extraCharge;
        $booking->harga_tiket = 25000;
        $booking->total_harga_final = $total;

        return view('pengunjung.booking.booking-detail', compact('booking'));
    }

    /* =========================================================
        4. STORE BOOKING
    ========================================================= */

    public function confirmStore(Request $request)
    {
        $input = session('temp_booking_data');

        if (!$input) {
            return redirect()
                ->route('pengunjung.booking.booking-form')
                ->with('error', 'Sesi habis, silakan isi ulang.');
        }

        try {
            $bookingRecord = DB::transaction(function () use ($input) {
                $kodeBooking = 'BK-' . strtoupper(Str::random(8));
                $jumlahMalam = (int) ($input['jumlah_malam'] ?? 1);
                $jumlahHari = $jumlahMalam + 1;

                $tanggal = Carbon::parse($input['tanggal_kunjungan']);
                $tanggalSelesai = Carbon::parse($input['tanggal_checkout']);

                $items = [];
                $fasilitas = [];
                $totalPaket = 0;
                $totalFasilitas = 0;

                /* =========================
                   PAKET
                ========================= */
                if (!empty($input['paket'])) {
                    foreach ($input['paket'] as $hari => $paketIds) {
                        foreach ($paketIds as $paketId) {
                            $paket = PaketWisata::findOrFail($paketId);
                            $qty = (int) ($input['paket_qty'][$hari][$paketId] ?? 1);
                            $subtotal = $paket->harga * $qty;

                            $items[] = [
                                'hari' => (int)$hari,
                                'tanggal' => $tanggal->copy()->addDays((int)$hari)->toDateString(),
                                'paket_wisata_id' => $paketId,
                                'qty' => $qty,
                                'harga' => $paket->harga,
                                'subtotal' => $subtotal
                            ];
                            $totalPaket += $subtotal;
                        }
                    }
                }

                /* =========================
                   FASILITAS
                ========================= */
                if (!empty($input['fasilitas'])) {
                    foreach ($input['fasilitas'] as $hari => $fasList) {
                        foreach ($fasList as $fasId => $qty) {
                            $qty = (int)$qty;
                            if ($qty <= 0) continue;

                            // $fas = Fasilitas::findOrFail($fasId);
                            $fas = Fasilitas::find($fasId);
                            if (!$fas) continue;
                            $fas->decrement('stok', $qty);

                            $subtotal = $fas->harga * $qty;
                            $fasilitas[] = [
                                'hari' => (int)$hari,
                                'tanggal' => $tanggal->copy()->addDays((int)$hari)->toDateString(),
                                'fasilitas_id' => $fasId,
                                'qty' => $qty,
                                'harga' => $fas->harga,
                                'subtotal' => $subtotal
                            ];
                            $totalFasilitas += $subtotal;
                        }
                    }
                }

                /* =========================
                   ⭐ LAHAN
                ========================= */
                if (!empty($input['lahan'])) {
                    foreach ($input['lahan'] as $hari => $fasId) {
                        $fas = Fasilitas::find($fasId);
                        if (!$fas) continue;

                        $fas->decrement('stok', 1);
                        $subtotal = $fas->harga;

                        $fasilitas[] = [
                            'hari' => (int)$hari,
                            'tanggal' => $tanggal->copy()->addDays((int)$hari)->toDateString(),
                            'fasilitas_id' => $fasId,
                            'qty' => 1,
                            'harga' => $fas->harga,
                            'subtotal' => $subtotal
                        ];
                        $totalFasilitas += $subtotal;
                    }
                }

                /* =========================
                   TIKET TAMBAHAN
                ========================= */
                $kapasitasPerHari = [];
                foreach ($items as $i) {
                    $hari = $i['hari'];
                    if (!isset($kapasitasPerHari[$hari])) {
                        $kapasitasPerHari[$hari] = 0;
                    }
                    $paket = PaketWisata::find($i['paket_wisata_id']);
                    if ($paket) {
                        $kapasitasPerHari[$hari] += ($paket->kapasitas ?? 0) * $i['qty'];
                    }
                }

                $kapasitasMaksimal = !empty($kapasitasPerHari) ? max($kapasitasPerHari) : 0;
                $jumlahPengunjung = (int) $input['jumlah_pengunjung'];
                $jumlahTiketTambahan = 0;
                $subtotalTiketTambahan = 0;

                if ($jumlahPengunjung > $kapasitasMaksimal) {
                    $jumlahTiketTambahan = $jumlahPengunjung - $kapasitasMaksimal;
                    $subtotalTiketTambahan = $jumlahTiketTambahan * 25000;
                }

                /* =========================
                   CREATE BOOKING
                ========================= */
                $booking = Booking::create([
                    'kode_booking' => $kodeBooking,
                    'nama_pemesan' => strip_tags($input['nama_pemesan']),
                    'no_hp' => strip_tags($input['no_hp']),
                    'tanggal_kunjungan' => $input['tanggal_kunjungan'],
                    'tanggal_selesai' => $tanggalSelesai->toDateString(),
                    'jumlah_malam' => $jumlahMalam,
                    'jumlah_hari' => $jumlahHari,
                    'jam' => $input['jam'],
                    'jumlah_pengunjung' => $input['jumlah_pengunjung'],
                    'jumlah_tiket_tambahan' => $jumlahTiketTambahan,
                    'jumlah_tiket_tambahan' => 25000,
                    'subtotal_tiket_tambahan' => $subtotalTiketTambahan,
                    'total_harga' => $totalPaket + $totalFasilitas + $subtotalTiketTambahan,
                    'total_harga_final' => $totalPaket + $totalFasilitas + $subtotalTiketTambahan,
                    'status_booking' => 'pending',
                    'status_pembayaran' => 'belum_bayar',
                ]);

                foreach ($items as $i) {
                    $booking->items()->create($i);
                }

                foreach ($fasilitas as $f) {
                    $booking->fasilitas()->create($f);
                }

                return $booking;
            });

            session()->forget('temp_booking_data');
            return redirect()->route('pengunjung.booking.booking-payment', $bookingRecord->id);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /* =========================================================
        5. EDIT BOOKING
    ========================================================= */

    public function edit($id)
    {
        $booking = Booking::with(['items.paketWisata', 'fasilitas.fasilitas'])->findOrFail($id);

        return view('pengunjung.booking.booking-form', [
            'booking' => $booking,
            'paket' => PaketWisata::all(),
            'kategoriPaket' => KategoriPaket::all(),
            'fasilitas' => Fasilitas::with('kategori')->get()
        ]);
    }

    /* =========================================================
        6. UPDATE BOOKING
    ========================================================= */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $booking = Booking::with('fasilitas')->findOrFail($id);

            $jumlahMalam = (int) $request->jumlah_malam;
            $jumlahHari = $jumlahMalam + 1;
            $tanggalAwal = Carbon::parse($request->tanggal_kunjungan);

            foreach ($booking->fasilitas as $oldFas) {
                $fasitasModel = Fasilitas::find($oldFas->fasilitas_id);
                if ($fasitasModel) {
                    $fasitasModel->increment('stok', $oldFas->qty);
                }
            }

            $booking->update([
                'nama_pemesan' => $request->nama_pemesan,
                'no_hp' => $request->no_hp,
                'tanggal_kunjungan' => $request->tanggal_kunjungan,
                'tanggal_selesai' => $request->tanggal_checkout,
                'jam' => $request->jam,
                'jumlah_malam' => $jumlahMalam,
                'jumlah_hari' => $jumlahHari,
                'jumlah_pengunjung' => $request->jumlah_pengunjung,
            ]);

            $booking->items()->delete();
            $booking->fasilitas()->delete();

            $total = 0;

            /* =========================
               PAKET
            ========================= */
            if ($request->has('paket')) {
                foreach ($request->paket as $hari => $paketIds) {
                    foreach ($paketIds as $paketId) {
                        $paket = PaketWisata::find($paketId);
                        if (!$paket) continue;

                        $qty = (int) ($request->paket_qty[$hari][$paketId] ?? 1);
                        $subtotal = $paket->harga * $qty;

                        $booking->items()->create([
                            'hari' => (int)$hari,
                            'tanggal' => $tanggalAwal->copy()->addDays((int)$hari)->toDateString(),
                            'paket_wisata_id' => $paketId,
                            'qty' => $qty,
                            'harga' => $paket->harga,
                            'subtotal' => $subtotal
                        ]);

                        $total += $subtotal;
                    }
                }
            }

            /* =========================
               FASILITAS
            ========================= */
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $hari => $fasList) {
                    foreach ($fasList as $fasId => $qty) {
                        $qty = (int)$qty;
                        if ($qty <= 0) continue;

                        $fas = Fasilitas::find($fasId);
                        if (!$fas) continue;

                        $fas->decrement('stok', $qty);
                        $subtotal = $fas->harga * $qty;

                        $booking->fasilitas()->create([
                            'hari' => (int)$hari,
                            'tanggal' => $tanggalAwal->copy()->addDays((int)$hari)->toDateString(),
                            'fasilitas_id' => $fasId,
                            'qty' => $qty,
                            'harga' => $fas->harga,
                            'subtotal' => $subtotal
                        ]);

                        $total += $subtotal;
                    }
                }
            }

            /* =========================
               ⭐ LAHAN UPDATE
            ========================= */
            if ($request->has('lahan')) {
                foreach ($request->lahan as $hari => $fasId) {
                    if (!$fasId) continue;

                    $fas = Fasilitas::find($fasId);
                    if (!$fas) continue;

                    $fas->decrement('stok', 1);
                    $subtotal = $fas->harga;

                    $booking->fasilitas()->create([
                        'hari' => (int)$hari,
                        'tanggal' => $tanggalAwal->copy()->addDays((int)$hari)->toDateString(),
                        'fasilitas_id' => $fasId,
                        'qty' => 1,
                        'harga' => $fas->harga,
                        'subtotal' => $subtotal
                    ]);

                    $total += $subtotal;
                }
            }

            /* =========================
               TIKET TAMBAHAN
            ========================= */
            $booking->load('items.paketWisata');
            $kapasitasPerHari = [];

            foreach ($booking->items as $item) {
                $hari = $item->hari;
                if (!isset($kapasitasPerHari[$hari])) {
                    $kapasitasPerHari[$hari] = 0;
                }
                $kapasitasPerHari[$hari] += ($item->paketWisata->kapasitas ?? 0) * $item->qty;
            }

            $kapasitasMaksimal = !empty($kapasitasPerHari) ? max($kapasitasPerHari) : 0;
            $jumlahTiketTambahan = 0;
            $subtotalTiketTambahan = 0;

            if ($request->jumlah_pengunjung > $kapasitasMaksimal) {
                $jumlahTiketTambahan = $request->jumlah_pengunjung - $kapasitasMaksimal;
                $subtotalTiketTambahan = $jumlahTiketTambahan * 25000;
                $total += $subtotalTiketTambahan;
            }

            $booking->update([
                'jumlah_tiket_tambahan' => $jumlahTiketTambahan,
                'jumlah_tiket_tambahan' => 25000,
                'subtotal_tiket_tambahan' => $subtotalTiketTambahan,
                'total_harga' => $total,
                'total_harga_final' => $total
            ]);

            DB::commit();

            return redirect()->route('pengunjung.booking.booking-detail', $id)
                ->with('success', 'Berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /* =========================================================
        7. PAYMENT
    ========================================================= */

    public function showPayment($id)
    {
        return view('pengunjung.booking.booking-payment', [
            'booking' => Booking::findOrFail($id)
        ]);
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'tipe_pembayaran' => 'required|in:dp,lunas',
            'metode_pembayaran' => 'required|in:transfer,cash',
        ]);

        $booking = Booking::findOrFail($id);
        $nominal = $request->tipe_pembayaran === 'dp'
            ? max(100000, round($booking->total_harga_final * 0.10, 2))
            : $booking->total_harga_final;

        $pembayaran = Pembayaran::firstOrNew([
            'booking_id' => $booking->id,
        ]);

        $pembayaran->fill([
            'tipe_pembayaran' => $request->tipe_pembayaran,
            'metode_pembayaran' => $request->metode_pembayaran,
            'nominal' => $nominal,
            'tanggal_pembayaran' => now(),
            'status_verifikasi' => 'pending',
            'catatan' => null,
        ]);
        $pembayaran->save();

        $booking->update([
            'status_pembayaran' => $request->tipe_pembayaran === 'lunas' ? 'lunas' : $request->tipe_pembayaran,
            'catatan' => ($booking->catatan ?? '') . ' | ' . strip_tags($request->metode_pembayaran)
        ]);

        return redirect()->route('pengunjung.booking.booking-success', $id)
            ->with('success', 'Booking Berhasil Diproses, Silakan Upload Bukti Pembayaran.');
    }

    public function showSuccess($id)
    {
        return view('pengunjung.booking.booking-success', [
            'booking' => Booking::findOrFail($id)
        ]);
    }

    /* =========================================================
        8. UPLOAD BUKTI
    ========================================================= */

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $booking = Booking::findOrFail($id);

        $pembayaran = Pembayaran::firstOrNew([
            'booking_id' => $booking->id,
        ], [
            'tipe_pembayaran' => $booking->status_pembayaran === 'dp' ? 'dp' : 'lunas',
            'metode_pembayaran' => 'transfer',
            'nominal' => $booking->status_pembayaran === 'dp'
                ? max(100000, round($booking->total_harga_final * 0.10, 2))
                : $booking->total_harga_final,
            'tanggal_pembayaran' => now(),
            'status_verifikasi' => 'pending',
        ]);

        $file = $request->file('bukti_pembayaran');

        if (!$file->isValid()) {
            return back()->with('error', 'File tidak valid.');
        }

        $filename = 'BUKTI-' . $booking->kode_booking . '-' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/bukti_pembayaran', $filename);

        if ($pembayaran->bukti_pembayaran) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $booking->update([
            'bukti_pembayaran' => $filename,
            'status_pembayaran' => 'menunggu_verifikasi'
        ]);

        $pembayaran->update([
            'bukti_pembayaran' => $filename,
            'tanggal_pembayaran' => now(),
            'status_verifikasi' => 'pending',
        ]);

        return back()->with('success', 'Bukti berhasil diupload, menunggu verifikasi admin.');
    }
}
