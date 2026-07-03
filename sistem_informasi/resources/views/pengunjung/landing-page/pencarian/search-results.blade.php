@extends('layouts.app')

@section('title', 'Tracking Booking')

@section('content')

@php
    $booking = $booking ?? null;

    $latestPembayaran = null;

    if ($booking && isset($booking->pembayaran)) {

        if ($booking->pembayaran instanceof \Illuminate\Support\Collection) {
            $latestPembayaran = $booking->pembayaran
                ->sortByDesc('created_at')
                ->first();
        } else {
            $latestPembayaran = $booking->pembayaran;
        }
    }
@endphp

<section class="pt-40 pb-20 bg-slate-50 min-h-screen">

    <div class="max-w-6xl mx-auto px-6">

    @if(session('success'))

    <div class="mb-8 bg-green-50 border border-green-200 rounded-2xl p-5">

        <div class="font-bold text-green-700">
            ✓ Berhasil
        </div>

        <p class="text-green-600 mt-1">
            {{ session('success') }}
        </p>

    </div>

    @endif

        <div class="text-center mb-12">

            <h1 class="text-4xl font-black text-slate-900">
                Hasil Pencarian Booking
            </h1>

            <p class="text-slate-500 mt-3">
                Status reservasi Anda di Kalisawah Adventure
            </p>

        </div>

        @if(!$booking)

            <div class="bg-white rounded-3xl p-16 shadow text-center">

                <div class="text-7xl text-red-400 mb-6">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>

                <h2 class="text-2xl font-black mb-3">
                    Data Tidak Ditemukan
                </h2>

                <p class="text-slate-500 mb-8">
                    Pastikan nama, nomor WhatsApp dan tanggal kunjungan sesuai data booking.
                </p>

                <a href="{{ route('landing-page.home') }}"
                   class="bg-primary text-white px-8 py-4 rounded-2xl font-bold">
                    Kembali
                </a>

            </div>

        @else

            <div class="bg-white rounded-[32px] shadow overflow-hidden">

                {{-- HEADER --}}
                <div class="bg-primary text-white p-8">

                    <div class="flex flex-wrap gap-6 justify-between">

                        <div>

                            <p class="text-sm opacity-80">
                                Kode Booking
                            </p>

                            <h2 class="text-3xl font-black">
                                {{ $booking->kode_booking ?? '-' }}
                            </h2>

                        </div>

                        <div>

                            <p class="text-sm opacity-80">
                                Status Booking
                            </p>

                            <span class="inline-block mt-2 px-5 py-2 rounded-full bg-yellow-400 text-slate-900 font-bold">
                                {{ strtoupper($booking->status_booking ?? 'PENDING') }}
                            </span>

                        </div>

                    </div>

                </div>

                {{-- CONTENT --}}
                <div class="p-8">

                    <div class="grid md:grid-cols-2 gap-8">

                        {{-- DATA PEMESAN --}}
                        <div>

                            <h3 class="font-black text-xl mb-5">
                                Data Pemesan
                            </h3>

                            <div class="space-y-4">

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Nama Pemesan
                                    </label>

                                    <p class="font-bold">
                                        {{ $booking->nama_pemesan }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Tanggal Kunjungan
                                    </label>

                                    <p class="font-bold">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kunjungan)->translatedFormat('d F Y') }}
                                    </p>
                                </div>

                                @if($booking->jam)
                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Jam Kunjungan
                                    </label>

                                    <p class="font-bold">
                                        {{ $booking->jam }}
                                    </p>
                                </div>
                                @endif

                            </div>

                        </div>

                        {{-- INFO BOOKING --}}
                        <div>

                            <h3 class="font-black text-xl mb-5">
                                Informasi Booking
                            </h3>

                            <div class="space-y-4">

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Total Pengunjung
                                    </label>

                                    <p class="font-bold">
                                        {{ $booking->jumlah_pengunjung ?? 0 }} Orang
                                    </p>
                                </div>

                                @if($booking->jumlah_tiket_tambahan)
                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Tiket Tambahan
                                    </label>

                                    <p class="font-bold">
                                        {{ $booking->jumlah_tiket_tambahan }} Orang
                                    </p>
                                </div>
                                @endif

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Total Harga
                                    </label>

                                    <p class="text-2xl font-black text-green-600">
                                        Rp {{ number_format($booking->total_harga_final ?? $booking->total_harga ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Status Pembayaran
                                    </label>

                                    <p class="font-bold">
                                        {{ strtoupper($latestPembayaran->status_verifikasi ?? 'PENDING') }}
                                    </p>
                                </div>

                                @if($latestPembayaran)

                                <div>

                                    <label class="text-slate-400 text-sm">
                                        Bukti Pembayaran
                                    </label>

                                    @if(
                                        !empty($latestPembayaran->bukti_pembayaran)
                                        && $latestPembayaran->status_verifikasi == 'pending'
                                    )

                                        <div class="mt-2 p-4 rounded-xl bg-green-50 border border-green-200">

                                            <div class="font-bold text-green-700">
                                                ✓ Bukti pembayaran sudah diupload
                                            </div>

                                            <p class="text-sm text-green-600 mt-1">
                                                Pembayaran sedang menunggu konfirmasi admin.
                                            </p>

                                        </div>

                                    @elseif(
                                        !empty($latestPembayaran->bukti_pembayaran)
                                        && $latestPembayaran->status_verifikasi == 'valid'
                                    )

                                        <div class="mt-2 p-4 rounded-xl bg-green-50 border border-green-200">

                                            <div class="font-bold text-green-700">
                                                ✓ Pembayaran telah diverifikasi admin
                                            </div>

                                            <p class="text-sm text-green-600 mt-1">
                                                Bukti pembayaran telah diterima.
                                            </p>

                                        </div>

                                    @elseif(
                                        $latestPembayaran->status_verifikasi == 'ditolak'
                                    )

                                        <div class="mt-2 p-4 rounded-xl bg-red-50 border border-red-200">

                                            <div class="font-bold text-red-700">
                                                ✕ Bukti pembayaran ditolak
                                            </div>

                                            <p class="text-sm text-red-600 mt-1">
                                                Silakan upload ulang bukti pembayaran yang sesuai.
                                            </p>

                                        </div>

                                    @else

                                        <div class="mt-2 p-4 rounded-xl bg-yellow-50 border border-yellow-200">

                                            <div class="font-bold text-yellow-700">
                                                ⚠ Bukti pembayaran belum diupload
                                            </div>

                                            <p class="text-sm text-yellow-700 mt-1">
                                                Silakan upload bukti pembayaran DP agar booking dapat diproses admin.
                                            </p>

                                        </div>

                                    @endif

                                </div>

                                @if(!empty($latestPembayaran->bukti_pembayaran))

                                <div>

                                    <label class="text-slate-400 text-sm">
                                        Bukti Yang Diupload
                                    </label>

                                    <div class="mt-2">

                                        <img
                                            src="{{ Storage::url($latestPembayaran->bukti_pembayaran) }}"
                                            alt="Bukti Pembayaran"
                                            class="w-64 rounded-xl border shadow"
                                        >

                                    </div>

                                </div>

                                @endif

                                <div>
                                    <label class="text-slate-400 text-sm">
                                        Status Verifikasi
                                    </label>

                                    <p class="font-bold">
                                        {{ strtoupper($latestPembayaran->status_verifikasi) }}
                                    </p>
                                </div>
                                @endif

                            </div>

                        </div>

                    </div>

                    <hr class="my-10">

                    {{-- PAKET --}}
                    <h3 class="font-black text-xl mb-6">
                        Paket Yang Dipesan
                    </h3>

                    <div class="space-y-4">

                        @forelse($booking->items ?? [] as $item)

                            <div class="border rounded-2xl p-5">

                                <div class="flex justify-between items-center">

                                    <div>

                                        <h4 class="font-bold">
                                            {{ $item->paketWisata->nama_paket ?? '-' }}
                                        </h4>

                                        <p class="text-slate-500 text-sm">
                                            Qty : {{ $item->qty ?? 1 }}
                                        </p>

                                    </div>

                                    <div class="font-black text-primary">
                                        Rp {{ number_format($item->subtotal ?? 0,0,',','.') }}
                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="border rounded-2xl p-5 text-center text-slate-500">
                                Tidak ada paket ditemukan.
                            </div>

                        @endforelse

                    </div>

                    {{-- FASILITAS --}}
                    @if(isset($booking->fasilitas) && count($booking->fasilitas) > 0)

                        <hr class="my-10">

                        <h3 class="font-black text-xl mb-6">
                            Fasilitas Tambahan
                        </h3>

                        <div class="space-y-4">

                            @foreach($booking->fasilitas as $fasilitas)

                                <div class="border rounded-2xl p-5 flex justify-between">

                                    <div>
                                        <h4 class="font-bold">
                                            {{ $fasilitas->fasilitas->nama_fasilitas ?? '-' }}
                                        </h4>
                                    </div>

                                    <div class="font-black text-primary">
                                        Rp {{ number_format($fasilitas->subtotal ?? 0,0,',','.') }}
                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @endif

                    @if(
                        !$latestPembayaran ||
                        empty($latestPembayaran->bukti_pembayaran) ||
                        $latestPembayaran->status_verifikasi == 'ditolak'
                    )

                    <hr class="my-10">

                    <div class="bg-blue-50 border border-blue-200 rounded-3xl p-8">

                        <h3 class="text-xl font-black mb-3">
                            Upload Bukti Pembayaran DP
                        </h3>

                        @if(
                            $latestPembayaran &&
                            $latestPembayaran->status_verifikasi == 'ditolak'
                        )

                            <p class="text-red-600 mb-6">
                                Bukti pembayaran sebelumnya ditolak admin.
                                Silakan upload ulang bukti pembayaran yang lebih jelas dan sesuai.
                            </p>

                        @else

                            <p class="text-slate-600 mb-6">
                                Booking Anda belum memiliki bukti pembayaran.
                                Silakan upload bukti transfer DP untuk diproses admin.
                            </p>

                        @endif

                        <form
                            action="{{ route('booking.upload-bukti', $booking->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf

                            <input
                                type="file"
                                name="bukti_pembayaran"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                                class="w-full border rounded-xl p-3"
                            >

                            <p class="text-xs text-slate-500 mt-2">
                                Format: JPG, JPEG, PNG, WEBP.
                                Maksimal 2 MB.
                            </p>

                            <button
                                type="submit"
                                class="mt-5 bg-primary text-white px-8 py-3 rounded-xl font-bold"
                            >
                                Upload Bukti Pembayaran
                            </button>

                        </form>

                    </div>

                    @endif

                    {{-- CATATAN ADMIN --}}
                    @if($latestPembayaran && $latestPembayaran->catatan)

                        <hr class="my-10">

                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6">

                            <h3 class="font-black mb-3">
                                Catatan Admin
                            </h3>

                            <p class="text-slate-700">
                                {{ $latestPembayaran->catatan }}
                            </p>

                        </div>

                    @endif

                    {{-- TOMBOL --}}
                    <div class="mt-12 text-center">

                        <a href="{{ route('landing-page.home') }}"
                           class="bg-slate-900 hover:bg-primary text-white px-10 py-4 rounded-2xl font-bold transition">
                            Kembali ke Beranda
                        </a>

                    </div>

                </div>

            </div>

        @endif

    </div>

</section>

@endsection