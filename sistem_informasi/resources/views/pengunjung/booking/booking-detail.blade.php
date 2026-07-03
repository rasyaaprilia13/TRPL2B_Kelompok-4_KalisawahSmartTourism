@extends('layouts.app')

@section('title', 'Rincian Booking')

@section('content')

<div class="bg-slate-50/50 min-h-screen pt-32 pb-20">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="flex justify-center mb-6">
            <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                {{ isset($is_preview) ? 'Review Reservasi' : 'Rincian Booking' }}
            </span>
        </div>

        <div class="text-center mb-10">

            <h2 class="text-blue-600 font-bold tracking-widest uppercase text-sm mb-2">
                Konfirmasi Pesanan
            </h2>

            <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">
                Detail Pesanan Anda
            </h1>

            <div class="w-16 h-1.5 bg-blue-600 mx-auto mt-4 rounded-full"></div>

        </div>

        {{-- DATA BOOKING --}}
        <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-sm border border-gray-100 mb-16">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-12 gap-x-8">

                {{-- NAMA --}}
                <div>
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Nama Pelanggan
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ $booking->nama_pemesan }}
                    </p>
                </div>

                {{-- HP --}}
                <div>
                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Nomor Whatsapp
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ $booking->no_hp }}
                    </p>
                </div>

                {{-- CHECKIN --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Tanggal Check-In
                    </h4>

                    @php
                        $tanggalCheckin = !empty($booking->tanggal_kunjungan)
                            ? \Carbon\Carbon::parse($booking->tanggal_kunjungan)
                            : now();
                    @endphp

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ $tanggalCheckin->translatedFormat('l, d F Y') }}
                    </p>

                </div>

                {{-- CHECKOUT --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Tanggal Check-Out
                    </h4>

                    @php
                        $tanggalCheckout =
                            $booking->tanggal_selesai
                            ?? \Carbon\Carbon::parse($booking->tanggal_kunjungan)
                                ->addDays((int)$booking->jumlah_malam)
                                ->toDateString();
                    @endphp

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ \Carbon\Carbon::parse($tanggalCheckout)->translatedFormat('l, d F Y') }}
                    </p>

                </div>

                {{-- JAM --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Jam Kedatangan
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ \Carbon\Carbon::parse($booking->jam)->format('H:i') }} WIB
                    </p>

                </div>

                {{-- JUMLAH --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Jumlah Pengunjung
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-slate-800">
                        {{ (int)$booking->jumlah_pengunjung }} Orang
                    </p>

                </div>

                {{-- MALAM --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Lama Menginap
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-blue-600">
                        {{ (int)$booking->jumlah_malam }} Malam
                    </p>

                </div>

                {{-- STATUS --}}
                <div>

                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Status Pembayaran
                    </h4>

                    <p class="text-xl md:text-2xl font-black text-amber-500 uppercase">
                        {{ str_replace('_', ' ', $booking->status_pembayaran ?? 'preview') }}
                    </p>

                </div>

            </div>

        </div>

        {{-- AKTIVITAS --}}
        <div class="flex items-center gap-3 mb-8">

            <div class="w-1.5 h-6 bg-amber-400 rounded-full"></div>

            <h2 class="text-lg md:text-xl font-black text-slate-800 tracking-wide uppercase">
                Aktivitas Per Hari
            </h2>

        </div>

        <div class="space-y-8 mb-16">

            @php
                $tanggalMulai = \Carbon\Carbon::parse($booking->tanggal_kunjungan);
                $totalMalam = (int)$booking->jumlah_malam;
                $runningGrandTotal = 0;
            @endphp

            @for($i = 0; $i < $totalMalam; $i++)

                @php
                    $itemsHari = collect($booking->items ?? [])->where('hari', $i);
                    $fasilitasHari = collect($booking->fasilitas ?? [])->where('hari', $i);

                    $tanggalHariIni =
                        $tanggalMulai
                            ->copy()
                            ->addDays((int)$i)
                            ->translatedFormat('l, d F Y');
                @endphp

                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">

                    {{-- HEADER HARI --}}
                    <div class="flex flex-wrap items-center gap-4 mb-6">

                        <div>
                            <h3 class="font-black text-lg text-slate-800 uppercase">
                                Hari {{ $i + 1 }}
                            </h3>

                            <p class="text-slate-500 mt-1">
                                {{ $tanggalHariIni }}
                            </p>
                        </div>

                        <span class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-700 text-sm font-bold">
                            Sampai malam ke-{{ $i + 1 }}
                        </span>

                    </div>

                    <div class="border-b border-gray-100 mb-8"></div>

                    {{-- PAKET --}}
                    <div>

                        <h4 class="text-sm font-black uppercase tracking-wide text-slate-800 mb-6">
                            Paket Wisata
                        </h4>

                        <div class="space-y-5">

                            @if($itemsHari->count() > 0)

                                @foreach($itemsHari as $item)

                                    @php
                                        $subtotal = $item->subtotal ?? ($item->harga * $item->qty);

                                        $runningGrandTotal += $subtotal;
                                    @endphp

                                    <div class="border border-slate-200 rounded-3xl p-6">

                                        <div class="flex items-start justify-between gap-5">

                                            <div>

                                                <h5 class="text-2xl font-black text-slate-800 uppercase mb-5">
                                                    {{ $item->paketWisata->nama_paket ?? '-' }}
                                                </h5>

                                                <div class="flex flex-wrap gap-3">

                                                    <span class="bg-slate-100 text-slate-700 text-sm font-bold px-4 py-2 rounded-2xl">
                                                        Qty {{ $item->qty }}
                                                    </span>

                                                    <span class="bg-blue-100 text-blue-700 text-sm font-bold px-4 py-2 rounded-2xl">
                                                        Rp {{ number_format($item->harga, 0, ',', '.') }} / paket
                                                    </span>

                                                    <span class="bg-green-100 text-green-700 text-sm font-bold px-4 py-2 rounded-2xl">
                                                        Kapasitas {{ $item->paketWisata->kapasitas ?? 0 }} Orang
                                                    </span>

                                                </div>

                                            </div>

                                            <div class="text-right min-w-[220px]">

                                                <p class="text-[11px] font-black tracking-[0.25em] text-slate-400 uppercase mb-2">
                                                    Subtotal
                                                </p>

                                                <span class="text-4xl font-black text-slate-900 leading-none">
                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            @else

                                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center text-slate-400 italic">
                                    Tidak ada paket pada hari ini.
                                </div>

                            @endif

                        </div>

                    </div>

                    {{-- FASILITAS --}}
                    @if($fasilitasHari->count() > 0)

                        <div class="mt-10 pt-10 border-t border-dashed border-gray-200">

                            <div class="flex items-center gap-3 mb-6">

                                <div class="w-2 h-2 rounded-full bg-blue-600"></div>

                                <h4 class="text-sm font-black uppercase tracking-wide text-slate-800">
                                    Fasilitas Tambahan
                                </h4>

                            </div>

                            <div class="space-y-5">

                                @foreach($fasilitasHari as $fas)

                                    @php
                                        $subtotal = $fas->subtotal ?? ($fas->harga * $fas->qty);

                                        $runningGrandTotal += $subtotal;
                                    @endphp

                                    <div class="border border-slate-200 rounded-3xl p-6">

                                        <div class="flex items-start justify-between gap-5">

                                            <div>

                                                <h5 class="text-2xl font-black text-slate-800 uppercase mb-5">
                                                    {{ $fas->fasilitas->nama_fasilitas ?? '-' }}
                                                </h5>

                                                <div class="flex flex-wrap gap-3">

                                                    <span class="bg-slate-100 text-slate-700 text-sm font-bold px-4 py-2 rounded-2xl">
                                                        Qty {{ $fas->qty }}
                                                    </span>

                                                    <span class="bg-blue-100 text-blue-700 text-sm font-bold px-4 py-2 rounded-2xl">
                                                        Rp {{ number_format($fas->harga, 0, ',', '.') }} / malam
                                                    </span>

                                                </div>

                                            </div>

                                            <div class="text-right min-w-[220px]">

                                                <p class="text-[11px] font-black tracking-[0.25em] text-slate-400 uppercase mb-2">
                                                    Subtotal
                                                </p>

                                                <span class="text-4xl font-black text-blue-600 leading-none">
                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif

                </div>

            @endfor

            {{-- TIKET TAMBAHAN --}}
            @if(($booking->subtotal_tiket_tambahan ?? 0) > 0)

                <div class="bg-orange-50 rounded-3xl p-6 md:p-8 shadow-sm border border-orange-100">

                    <div class="flex items-center gap-3 mb-6">

                        <div class="w-2 h-6 bg-orange-400 rounded-full"></div>

                        <h3 class="font-black text-sm md:text-base text-orange-900 uppercase tracking-wide">
                            Tiket Tambahan
                        </h3>

                    </div>

                    <div class="border-b border-orange-200 mb-8"></div>

                    <div class="flex items-start justify-between gap-5">

                        <div>

                            <p class="text-lg font-black text-orange-900 uppercase">
                                Tambahan Pengunjung
                            </p>

                            <p class="text-sm text-orange-700 mt-2">
                                {{ (int)$booking->jumlah_tiket_tambahan }} Orang × Rp 25.000
                            </p>

                        </div>

                        <div class="text-right min-w-[220px]">

                            <p class="text-[11px] font-black tracking-[0.25em] text-orange-400 uppercase mb-2">
                                Subtotal
                            </p>

                            <span class="text-4xl font-black text-orange-900 leading-none">
                                Rp {{ number_format($booking->subtotal_tiket_tambahan, 0, ',', '.') }}
                            </span>

                        </div>

                    </div>

                </div>

            @endif

        </div>

        {{-- GRAND TOTAL --}}
<div class="bg-white rounded-3xl p-8 md:p-10 shadow-sm border border-gray-100 mb-10">

    <div class="flex flex-col md:flex-row justify-between items-center gap-5">

        <div>
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">
                Total Pembayaran
            </p>

            <h2 class="text-2xl md:text-3xl font-black text-slate-800">
                Grand Total Booking
            </h2>
        </div>

        <div class="text-right">

            <p class="text-sm text-slate-400 font-semibold mb-2">
                Total Keseluruhan
            </p>

            <h1 class="text-4xl md:text-5xl font-black text-blue-600 leading-none">
                Rp {{ number_format($booking->total_harga_final ?? $runningGrandTotal, 0, ',', '.') }}
            </h1>

        </div>

    </div>

</div>

{{-- ACTION BUTTON --}}
<div class="flex flex-col sm:flex-row justify-center items-center gap-5 mt-6 mb-20">

    @if(isset($is_preview) && $is_preview)

        {{-- KEMBALI --}}
        <a
            href="{{ route('pengunjung.booking.booking-form') }}"
            class="group relative overflow-hidden px-8 py-4 rounded-2xl border border-slate-200 bg-white text-slate-700 font-bold transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(0,0,0,0.12)] hover:border-slate-300"
        >
            <span class="relative z-10">
                Kembali & Ubah
            </span>

            <div class="absolute inset-0 bg-slate-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
        </a>

        @if(session('error'))
            <div class="max-w-md mx-auto mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl text-red-700 shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-black text-sm uppercase tracking-wide">Gagal Memproses Booking</span>
                </div>
                <p class="text-xs font-medium opacity-90">{{ session('error') }}</p>
            </div>
        @endif

        {{-- LANJUT --}}
        <form
            action="{{ route('pengunjung.booking.confirm') }}"
            method="POST"
        >
            @csrf

            <button
                type="submit"
                class="group relative overflow-hidden px-10 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_35px_rgba(37,99,235,0.35)]"
            >
                <span class="relative z-10">
                    Konfirmasi & Lanjut Pembayaran
                </span>

                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition duration-300"></div>
            </button>
        </form>

    @else

        @php
            $statusPembayaran = $booking->status_pembayaran ?? 'belum_bayar';
        @endphp

        @if($statusPembayaran == 'belum_bayar')

            {{-- EDIT --}}
            <a
                href="{{ route('pengunjung.booking.edit', $booking->id) }}"
                class="group relative overflow-hidden px-8 py-4 rounded-2xl border border-blue-200 bg-blue-50 text-blue-700 font-bold transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(59,130,246,0.18)] hover:bg-blue-100"
            >
                <span class="relative z-10">
                    Edit Pesanan
                </span>

                <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition duration-300"></div>
            </a>

            {{-- PEMBAYARAN --}}
            <a
                href="{{ route('pengunjung.booking.booking-payment', $booking->id) }}"
                class="group relative overflow-hidden px-10 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_14px_35px_rgba(37,99,235,0.35)]"
            >
                <span class="relative z-10">
                    Lanjut Pembayaran
                </span>

                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition duration-300"></div>
            </a>

        @endif

    @endif

</div>

@endsection
