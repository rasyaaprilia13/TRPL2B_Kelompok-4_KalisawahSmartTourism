<?php

namespace App\Http\Controllers\API\Pembayaran;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pembayaran;
use App\Models\Booking;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranAdminController extends Controller
{
    // ======================================================
    // LIST PEMBAYARAN
    // ======================================================
    public function index()
    {
        return view('admin.kelola_pembayaran.pembayaran', [
            // Tambahkan relasi ke pembayaran milik booking agar kita bisa hitung total DP
            'pembayaran' => Pembayaran::with(['booking.pembayaran' => function($q) {
                $q->where('status_verifikasi', 'valid'); // Cuma hitung yang VALID
            }])->latest()->get()
        ]);
    }

    // ======================================================
    // DETAIL PEMBAYARAN (JSON)
    // ======================================================
    public function show($id)
    {
        $data = Pembayaran::with('booking')->find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        return response()->json($data, 200);
    }

    // ======================================================
    // UPDATE VERIFIKASI PEMBAYARAN (ADMIN) - FIX TOTAL SINKRONISASI
    // ======================================================
    public function update(Request $request, $id)
    {
        // Tambahkan 'pelunasan' ke dalam daftar validasi in:...
        $request->validate([
            'status_verifikasi' => 'required|in:pending,valid,ditolak,pelunasan',
            'nominal' => 'nullable',
            'nominal_pelunasan' => 'nullable', // Tambahan validasi untuk pelunasan hari H
            'metode_pelunasan' => 'nullable|in:transfer,cash,qris',
            'catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $pembayaran = Pembayaran::find($id);

            if (!$pembayaran) {
                return back()->with('error', 'Pembayaran tidak ditemukan');
            }

            $booking = Booking::find($pembayaran->booking_id);

            if (!$booking) {
                return back()->with('error', 'Booking tidak ditemukan');
            }

            // =========================================================================
            // 💡 LOGIKA BARU: JIKA ADMIN MEMILIH PELUNASAN CASH/TRANSFER DI TEMPAT HARI H
            // =========================================================================
            if ($request->status_verifikasi === 'pelunasan') {
                $nominalInput = $request->nominal_pelunasan;
                $nominalPelunasan = ($nominalInput !== null) ? (int) preg_replace('/[^0-9]/', '', $nominalInput) : 0;

                if ($nominalPelunasan <= 0) {
                    return back()->with('error', 'Nominal pelunasan harus diisi dan lebih dari 0!');
                }

                // 1. Buat record pembayaran baru (sebagai pembayaran ke-2 / Pelunasan)
                $pembayaranBaru = Pembayaran::create([
                    'booking_id'        => $booking->id,
                    'tipe_pembayaran'   => 'lunas', // Tipenya disetel lunas
                    'metode_pembayaran' => $request->metode_pelunasan ?? 'cash',
                    'nominal'           => $nominalPelunasan,
                    'status_verifikasi' => 'valid', // Langsung valid karena diinput admin langsung
                    'tanggal_pembayaran'=> now(),
                    'catatan'           => $request->catatan ?? 'Pelunasan sisa tagihan langsung di lokasi oleh Admin'
                ]);

                // 2. Catat nominal uang pelunasan ke Kas Master Pemasukan Wisata
                \App\Models\Pemasukan::create([
                    'pembayaran_id'    => $pembayaranBaru->id,
                    'booking_id'       => $booking->id,
                    'kode_pemasukan'   => 'PEM-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'nominal'          => $nominalPelunasan,
                    'jenis_transaksi'  => 'lunas',
                    'metode_pemasukan' => strtolower($request->metode_pelunasan ?? 'cash'),
                    'tanggal_masuk'    => now()
                ]);

                // 3. Jalankan sinkronisasi bawaan controller agar status booking otomatis ter-update mengikuti total uang masuk
                $this->syncBooking($booking);

                DB::commit();
                return back()->with('success', 'Pelunasan berhasil dicatat, status booking otomatis diperbarui!');
            }

            // =========================================================================
            // ⚙️ LOGIKA BAWAAN: JIKA CUMA VERIFIKASI ACC DATA PENGUNJUNG (VALID / TOLAK)
            // =========================================================================
            $nominalInput = $request->nominal;
            if ($nominalInput !== null) {
                $nominalCleaned = preg_replace('/[^0-9]/', '', $nominalInput);
                $nominalFinal = ($nominalCleaned !== '' && (int)$nominalCleaned > 0) ? (int)$nominalCleaned : $pembayaran->nominal;
            } else {
                $nominalFinal = $pembayaran->nominal;
            }

            // Update status pembayaran pengunjung
            $pembayaran->update([
                'status_verifikasi' => $request->status_verifikasi,
                'nominal'           => $nominalFinal,
                'catatan'           => $request->catatan
            ]);

            // Jika statusnya valid, catat data DP/Transfer awal tersebut ke Kas Pemasukan
            if ($request->status_verifikasi === 'valid') {
                \App\Models\Pemasukan::updateOrCreate(
                    ['pembayaran_id' => $pembayaran->id],
                    [
                        'booking_id'       => $booking->id,
                        'kode_pemasukan'   => 'REV-' . strtoupper(\Illuminate\Support\Str::random(8)),
                        'nominal'          => $nominalFinal,
                        'jenis_transaksi'  => strtolower($pembayaran->tipe_pembayaran ?? 'dp'),
                        'metode_pemasukan' => strtolower($pembayaran->metode_pembayaran ?? 'transfer'),
                        'tanggal_masuk'    => now()
                    ]
                );
            }

            // Jalankan sinkronisasi status booking bawaan
            $this->syncBooking($booking);

            DB::commit();
            return back()->with('success', 'Status verifikasi pembayaran berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    // ======================================================
    // DELETE PEMBAYARAN
    // ======================================================
    public function destroy($id)
    {
        $data = Pembayaran::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data pembayaran tidak ditemukan'
            ], 404);
        }

        if ($data->bukti_pembayaran) {
            Storage::disk('public')->delete($data->bukti_pembayaran);
        }

        $data->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus'
            ], 200);
    }

    public function detailBooking($bookingId)
    {
        $booking = Booking::with('pembayaran')
                    ->findOrFail($bookingId);

        return view(
            'admin.kelola_booking.modal_pembayaran',
            compact('booking')
        );
    }

    // ======================================================
    // 🔥 BAGIAN UTAMA YANG DIUPDATE: SINKRONISASI REAL-TIME SISA TAGIHAN
    // ======================================================
    private function syncBooking($booking)
    {
        // 💡 JIKA STATUS SUDAH 'SELESAI' ATAU 'DIBATALKAN' SECARA MANUAL, JANGAN DIUBAH LAGI
        if (in_array($booking->status_booking, ['selesai', 'dibatalkan'])) {
            return;
        }

        // 1. Hitung total nominal pembayaran yang murni berstatus VALID
        $totalValid = (int) Pembayaran::where('booking_id', $booking->id)
            ->where('status_verifikasi', 'valid')
            ->sum('nominal');

        // 2. Hitung jumlah transaksi yang masih PENDING dan DITOLAK
        $jumlahPending = Pembayaran::where('booking_id', $booking->id)->where('status_verifikasi', 'pending')->count();
        $jumlahDitolak = Pembayaran::where('booking_id', $booking->id)->where('status_verifikasi', 'ditolak')->count();

        // 3. Bersihkan nilai total_harga_final agar menjadi angka integer murni
        $totalTagihan = (int) preg_replace('/[^0-9]/', '', $booking->total_harga_final);

        // 4. 💡 LOGIKA PERHITUNGAN MATEMATIKA REAL-TIME SISA TAGIHAN
        $sisaTagihanReal = $totalTagihan - $totalValid;
        if ($sisaTagihanReal < 0) {
            $sisaTagihanReal = 0; // Jaga-jaga agar tidak minus
        }

        // Ambil data pembayaran terakhir yang valid untuk dicek tipenya
        $pembayaranTerakhir = Pembayaran::where('booking_id', $booking->id)
            ->where('status_verifikasi', 'valid')
            ->latest()
            ->first();

        // === JALANKAN LOGIKA PENENTUAN STATUS & UPDATE SISA TAGIHAN KE DATABASE ===
        if ($totalValid >= $totalTagihan && $totalTagihan > 0) {
            $booking->update([
                'status_booking'    => 'dikonfirmasi',
                'status_pembayaran' => 'lunas',
                'sisa_tagihan'      => 0 // Uang masuk pas/lebih = Sisa Tagihan Rp 0 (LUNAS)
            ]);
        } elseif ($totalValid > 0) {
            $tipeStatus = ($pembayaranTerakhir && $pembayaranTerakhir->tipe_pembayaran === 'lunas') ? 'lunas' : 'dp';

            // Pengkondisian tambahan: jika sisa tagihan real sudah 0, set status ke lunas
            $statusAkhir = ($sisaTagihanReal === 0) ? 'lunas' : $tipeStatus;

            $booking->update([
                'status_booking'    => 'dikonfirmasi',
                'status_pembayaran' => $statusAkhir,
                'sisa_tagihan'      => $sisaTagihanReal // Menyimpan sisa kekurangan pembayaran real-time
            ]);
        } else {
            if ($jumlahDitolak > 0 && $jumlahPending == 0) {
                $booking->update([
                    'status_booking'    => 'dibatalkan',
                    'status_pembayaran' => 'dp',
                    'sisa_tagihan'      => $totalTagihan // Belum ada uang masuk, sisa tagihan = total harga penuh
                ]);
            } else {
                $booking->update([
                    'status_booking'    => 'pending',
                    'status_pembayaran' => 'dp',
                    'sisa_tagihan'      => $totalTagihan // Belum ada uang masuk, sisa tagihan = total harga penuh
                ]);
            }
        }
    }
}
