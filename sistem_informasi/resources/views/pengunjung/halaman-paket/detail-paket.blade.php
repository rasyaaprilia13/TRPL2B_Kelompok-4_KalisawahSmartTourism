@extends('layouts.app')

{{-- Judul Dinamis --}}
@section('title', $kategori->nama_kategori . ' - Kalisawah Adventure')

@section('content')
    {{-- PERBAIKAN TINGGI HERO & PADDING: Menggunakan min-h dengan padding top gajah (pt-36) biar tulisan fix turun dari navbar --}}
    <section class="relative min-h-[70vh] w-full overflow-hidden flex items-center pt-36 pb-16 md:pt-44 md:pb-20">
        <img src="{{ $kategori->hero_image ? asset('storage/' . $kategori->hero_image) : asset('images/default-hero.jpg') }}"
             alt="{{ $kategori->nama_kategori }}"
             class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-transparent"></div>

        <div class="relative z-10 w-full px-6 md:px-20 lg:px-32 max-w-7xl mx-auto">
            <div class="max-w-2xl">
                <h1 class="text-primary text-4xl md:text-6xl font-bold mb-2 uppercase">{{ $kategori->nama_kategori }}</h1>
                <h2 class="font-script text-secondary text-3xl md:text-5xl mb-6">
                    {{ $kategori->tagline ?? 'Petualangan Terbaik Menanti Anda!' }}
                </h2>

                @if($kategori->deskripsi)
                    <p class="text-white/80 text-lg mb-8">{{ $kategori->deskripsi }}</p>
                @endif

                <div class="flex flex-wrap gap-4">
                    <a href="#pilihan-paket" class="bg-primary text-white px-8 py-4 rounded-lg font-bold hover:bg-hover-primary transition-all transform hover:-translate-y-1 shadow-lg">
                        Lihat Pilihan Paket
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- PERBAIKAN SEBELUM FOOTER: Mengunci padding pb-4 dan -mb-16/24 agar menempel rapat dengan footer di bawahnya --}}
    <section id="pilihan-paket" class="py-12 md:pt-16 md:pb-4 px-6 md:px-20 max-w-7xl mx-auto scroll-mt-20 -mb-16 md:-mb-24">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-bold text-dark-navy uppercase tracking-tight">Pilihan Paket <span class="text-secondary">{{ $kategori->nama_kategori }}</span></h2>
            <div class="w-24 h-1 bg-secondary mx-auto mt-4 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center items-stretch">
            @forelse($pakets as $paket)
            <div class="bg-white rounded-[32px] overflow-hidden shadow-xl border border-gray-100 flex flex-col group hover:shadow-2xl transition-all duration-300">
                <div class="relative h-64 overflow-hidden shrink-0">
                    <img src="{{ $paket->gambar ? asset('storage/' . $paket->gambar) : asset('images/default-package.jpg') }}"
                         alt="{{ $paket->nama_paket }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>

                <div class="p-8 flex flex-col flex-grow">
                    <h3 class="text-primary text-2xl font-black mb-2 uppercase">{{ $paket->nama_paket }}</h3>

                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                        {{ Str::limit($paket->deskripsi, 100) }}
                    </p>

                    <div class="flex gap-6 text-sm text-gray-500 mb-6 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-clock text-secondary"></i>
                            <span>{{ $paket->durasi }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-users text-secondary"></i>
                            <span>{{ $paket->kapasitas }} Orang</span>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6 flex-grow">
                        <h4 class="font-black text-dark-navy text-xs uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-4 bg-secondary rounded-full"></span> Fasilitas
                        </h4>
                        <ul class="text-gray-500 text-sm font-medium space-y-2">
                            @foreach($paket->fasilitas as $f)
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span>{{ $f->nama_fasilitas }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-gray-100 mt-auto">
                        <span class="text-dark-navy font-black text-2xl">Rp {{ number_format($paket->harga, 0, ',', '.') }}</span>
                        <span class="text-gray-400 text-[10px] font-bold block uppercase tracking-widest mt-1">Harga per paket</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20 text-gray-400">
                <i class="fa-solid fa-box-open text-4xl mb-4"></i>
                <p>Belum ada paket tersedia untuk kategori ini.</p>
            </div>
            @endforelse
        </div>

        @if($pakets->count() > 0)
        <div class="mt-10 bg-white border border-gray-100 p-8 md:p-12 rounded-[32px] shadow-lg text-center max-w-3xl mx-auto">
            <h3 class="text-2xl md:text-3xl font-bold text-dark-navy mb-4">Siap untuk petualangan ini?</h3>
            <p class="text-gray-500 mb-8">Pilih paket yang paling cocok untuk kebutuhan liburan Anda dan lakukan reservasi sekarang.</p>
            <a href="{{ route('pengunjung.booking.booking-form') }}" class="inline-block bg-primary text-white px-10 py-4 rounded-xl font-bold hover:bg-hover-primary transition-all shadow-lg shadow-primary/20 text-lg uppercase tracking-wider">
                Pesan Sekarang
            </a>
        </div>
        @endif
    </section>

@endsection
