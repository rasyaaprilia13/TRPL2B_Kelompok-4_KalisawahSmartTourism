<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalisawah Adventure - Premium Outdoor & Petualangan Seru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Plus Jakarta Sans', 'sans-serif'],
                        'latin': ['Pacifico', 'cursive'],
                    },
                    colors: {
                        'primary': '#0066CC',
                        'primary-hover': '#005BB5',
                        'secondary': '#FFC236',
                        'dark-navy': '#0B1224',
                        'card-dark': '#131C35',
                        'light-bg': '#F8FAFC'
                    },
                    boxShadow: {
                        'premium': '0 20px 50px -12px rgba(11, 18, 36, 0.05)',
                        'premium-hover': '0 30px 60px -10px rgba(0, 102, 204, 0.12)',
                        'button-solid': '0 10px 24px -4px rgba(0, 102, 204, 0.4)',
                        'button-dark': '0 10px 24px -4px rgba(11, 18, 36, 0.25)',
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(11, 18, 24, 0.98) 30%, rgba(11, 18, 36, 0.85) 70%, rgba(0, 102, 204, 0.15) 100%);
        }
        .text-gradient {
            background: linear-gradient(to right, #FFFFFF 60%, #FFC236 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-light-bg text-slate-800 antialiased font-sans">

    @include('components.navbar')

    <section id="hero" x-data="{
        activeSlide: 0,
        slides: [
            @if(isset($landingSetting) && $landingSetting->hero_image_path)
                '{{ asset('storage/' . $landingSetting->hero_image_path) }}',
            @endif

            @if(isset($landingSetting) && $landingSetting->hero_image_path_2)
                '{{ asset('storage/' . $landingSetting->hero_image_path_2) }}',
            @endif

            @if(isset($landingSetting) && $landingSetting->hero_image_path_3)
                '{{ asset('storage/' . $landingSetting->hero_image_path_3) }}'
            @endif
        ].filter(Boolean),
        init() {
            if (this.slides.length > 1) {
                setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                }, 6000);
            }
        }
    }" class="relative min-h-[95vh] w-full flex items-center bg-dark-navy pt-40 pb-48 overflow-hidden">

        <div class="absolute inset-0 z-0">
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-1000"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 w-full h-full">
                    <img :src="slide" class="w-full h-full object-cover object-center" alt="Hero Asset">
                </div>
            </template>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto w-full px-6 md:px-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="space-y-8 lg:col-span-8 xl:col-span-7">
                <div class="inline-flex items-center gap-2.5 bg-white/10 px-4 py-2 rounded-xl border border-white/10 backdrop-blur-md">
                    <span class="w-2.5 h-2.5 rounded-full bg-secondary animate-ping"></span>
                    <span class="text-white font-semibold text-xs tracking-widest uppercase">Wonderful Songgon, Banyuwangi</span>
                </div>

                <div class="space-y-4">
                    <p class="font-latin text-secondary text-3xl md:text-5xl tracking-wide">Rafting & Outbound</p>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.15] text-gradient">
                        {!! nl2br(e($landingSetting->hero_title ?? 'Jelajahi Petualangan Tanpa Batas')) !!}
                    </h1>
                </div>

                <p class="text-slate-300 text-base md:text-lg font-light leading-relaxed max-w-xl">
                    {{ $landingSetting->hero_subtitle ?? 'Nikmati keseruan arung jeram, outbound training, camping eksklusif, hingga keindahan alam Songgon bersama fasilitator bersertifikasi nasional.' }}
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-5 pt-4">
                    <a href="{{ route('pengunjung.booking.booking-form', ['paket' => 'semua']) }}" class="bg-primary hover:bg-primary-hover text-white font-extrabold px-10 py-5 rounded-2xl transition-all duration-300 transform hover:-translate-y-1 text-sm uppercase tracking-widest text-center flex items-center justify-center gap-3 shadow-button-solid active:translate-y-0">
                        <i class="fa-solid fa-calendar-check text-base"></i> Booking Sekarang
                    </a>
                    <a href="{{ route('panduan.booking') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold px-10 py-5 rounded-2xl border border-white/20 backdrop-blur-sm transition-all duration-300 hover:border-white/40 text-sm uppercase tracking-widest text-center flex items-center justify-center gap-3 transform hover:-translate-y-1 active:translate-y-0">
                        <i class="fa-solid fa-book-open text-slate-300"></i> Panduan Booking
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex lg:col-span-4 xl:col-span-5 h-full items-center justify-end">
                <div class="flex items-center gap-4 bg-white/5 p-3 rounded-2xl backdrop-blur-md border border-white/10 shadow-2xl">
                    <button @click="activeSlide = (activeSlide - 1 + slides.length) % slides.length" class="w-14 h-14 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all duration-200 active:scale-90 border border-white/10">
                        <i class="fa-solid fa-chevron-left text-base"></i>
                    </button>
                    <button @click="activeSlide = (activeSlide + 1) % slides.length" class="w-14 h-14 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all duration-200 active:scale-90 border border-white/10">
                        <i class="fa-solid fa-chevron-right text-base"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section id="cari-booking" class="relative z-20 -mt-24 max-w-6xl mx-auto px-6">
        <div class="glass-card p-8 md:p-10 rounded-[32px] shadow-premium border border-white/60 space-y-8 transform transition-all duration-500 hover:shadow-premium-hover">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/60 pb-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                        <i class="fa-solid fa-magnifying-glass text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Cari & Lacak Status Reservasi</h3>
                        <p class="text-slate-500 text-xs mt-0.5">Masukkan data identitas sesuai dengan formulir saat Anda mendaftar</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('cari.booking.proses') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                @csrf
                <div class="space-y-2.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block">Nama Pemesan</label>
                    <div class="relative group">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-primary transition-colors"></i>
                        <input type="text" name="nama_pemesan" value="{{ request('nama_pemesan') }}" placeholder="Nama lengkap..." class="w-full pl-12 pr-4 py-5 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary text-xs font-semibold transition-all placeholder:text-slate-400 text-slate-700 shadow-sm" required>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block">No. WhatsApp</label>
                    <div class="relative group">
                        <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-primary transition-colors"></i>
                        <input type="tel" name="no_hp" value="{{ request('no_hp') }}" placeholder="Contoh: 081234..." class="w-full pl-12 pr-4 py-5 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary text-xs font-semibold transition-all placeholder:text-slate-400 text-slate-700 shadow-sm" required>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block">Tanggal Kunjungan</label>
                    <div class="relative group">
                        <i class="fa-solid fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-primary transition-colors"></i>
                        <input type="date" name="tanggal_kunjungan" value="{{ request('tanggal_kunjungan') }}" class="w-full pl-12 pr-4 py-5 bg-white border border-slate-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary text-xs font-semibold transition-all text-slate-600 shadow-sm" required>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full bg-slate-900 hover:bg-primary text-white font-extrabold py-4.5 px-6 rounded-2xl transition-all duration-300 shadow-button-dark hover:shadow-button-solid text-xs uppercase tracking-widest flex items-center justify-center gap-2 h-[56px] transform hover:-translate-y-0.5 active:translate-y-0">
                        Cari Data
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section id="trust-signal" class="pt-32 pb-16 px-6 max-w-7xl mx-auto space-y-24 scroll-mt-12">
        <div class="text-center max-w-3xl mx-auto space-y-3">
            <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">Core Advantages</span>
            <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Kenapa Memilih <span class="text-secondary">Kalisawah</span> Adventure?
            </h2>
            <div class="w-16 h-1.5 bg-primary mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-primary tracking-tight">{{ $settings['total_peserta'] ?? '20.000+' }}</p>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Total Peserta</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-primary tracking-tight">{{ $settings['total_perusahaan'] ?? '500+' }}</p>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Perusahaan</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-primary tracking-tight">{{ $settings['total_institusi'] ?? '300+' }}</p>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Institusi</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-primary tracking-tight">{{ $settings['tahun_berdiri'] ?? '10+' }}</p>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Tahun Berdiri</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-primary tracking-tight">{{ $settings['luas_area'] ?? '10+' }}</p>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Hektar Area</p>
            </div>
            <div class="bg-white p-8 rounded-3xl border border-slate-100 text-center space-y-1.5 shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300">
                <p class="text-3xl font-extrabold text-secondary tracking-tight">{{ $settings['rating_google'] ?? '4.9' }}</p>
                <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider"><i class="fa-solid fa-star text-secondary mr-1"></i> Google</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center bg-white p-8 md:p-16 rounded-[40px] border border-slate-100 shadow-premium">
            <div class="space-y-6 lg:col-span-7">
                <div class="space-y-3">
                    <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">Tentang Destinasi</span>
                    <h3 class="text-2xl md:text-4xl font-extrabold text-slate-900 leading-tight tracking-tight">Mengenal Lebih Dekat <br><span class="text-secondary">Kalisawah</span> Adventure</h3>
                </div>
                <p class="text-slate-500 text-base leading-relaxed font-normal">
                    {{ $settings['about_description'] ?? 'Kalisawah Adventure merupakan destinasi wisata alam terintegrasi di Banyuwangi yang menghadirkan pengalaman petualangan seru di tengah keindahan sungai dan hamparan persawahan Songgon. Didukung oleh pengelolaan yang profesional serta standar keselamatan yang terjamin, kami siap memberikan pengalaman wisata yang aman, nyaman, dan berkesan.' }}
                </p>
                <div class="pt-4">
                    <a href="{{ route('pengunjung.booking.booking-form', ['paket' => 'semua']) }}" class="inline-flex items-center gap-3 bg-primary hover:bg-primary-hover text-white text-xs font-extrabold py-5 px-10 rounded-2xl transition-all duration-300 shadow-button-solid transform hover:-translate-y-0.5 group">
                        MULAI RESERVASI SEKARANG <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 lg:col-span-5 w-full">
                <div class="space-y-6">
                    <div class="overflow-hidden rounded-[24px] shadow-premium group">
                        <img src="{{ isset($settings['about_img_1']) ? asset('storage/' . $settings['about_img_1']) : 'https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/e2/39/de/caption.jpg?w=1200&h=1200&s=1' }}" class="w-full h-52 object-cover object-center transform transition-transform duration-700 group-hover:scale-110" alt="Rafting Activity">
                    </div>
                    <div class="bg-blue-50/80 border border-blue-100 p-6 rounded-[24px] text-center">
                        <span class="block text-2xl font-black text-primary">Safe</span>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Certified Guides</span>
                    </div>
                </div>
                <div class="space-y-6 pt-10">
                    <div class="bg-amber-50/80 border border-amber-100 p-6 rounded-[24px] text-center">
                        <span class="block text-2xl font-black text-amber-600">Fun</span>
                        <span class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Exciting Games</span>
                    </div>
                    <div class="overflow-hidden rounded-[24px] shadow-premium group">
                        <img src="{{ isset($settings['about_img_2']) ? asset('storage/' . $settings['about_img_2']) : 'https://labirutour.co.id/wp-content/uploads/2025/11/Kalisawah-Adventure-Banyuwangi-.webp' }}" class="w-full h-52 object-cover object-center transform transition-transform duration-700 group-hover:scale-110" alt="Camping Activity">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="pilih-paket" class="py-24 px-6 bg-slate-100/50 border-t border-slate-200/40">
        <div class="max-w-7xl mx-auto space-y-20">

            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">
                    Our Packages
                </span>

                <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Pilih Paket Seru Kamu
                </h2>

                <div class="w-16 h-1.5 bg-primary mx-auto rounded-full"></div>
            </div>

            {{-- GRID PAKET --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                @forelse($categories as $category)

                    <div class="bg-white rounded-[32px] overflow-hidden shadow-premium hover:shadow-premium-hover hover:-translate-y-2 transition-all duration-300 flex flex-col border border-slate-200/40 group h-full">

                        {{-- IMAGE --}}
                        <div class="relative h-56 overflow-hidden bg-slate-100">

                            @if($category->gambar)
                                <img
                                    src="{{ asset('storage/' . $category->gambar) }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    alt="{{ $category->nama_kategori }}"
                                >
                            @else
                                <img
                                    src="https://images.unsplash.com/photo-1533240332313-0db49b459ad6?q=80&w=500"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    alt="Default Image"
                                >
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        </div>

                        {{-- CONTENT --}}
                        <div class="p-8 flex flex-col flex-1">

                            <div class="space-y-3">

                                <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-primary transition-colors duration-300 tracking-tight">
                                    {{ $category->nama_kategori }}
                                </h3>

                                <p class="text-slate-500 text-sm leading-relaxed line-clamp-3">
                                    {{ $category->deskripsi ?? 'Nikmati pengalaman wisata terbaik bersama Kalisawah Adventure.' }}
                                </p>

                            </div>

                            {{-- BUTTON --}}
                            <div class="mt-auto pt-8">


                                <a href="{{ route('kategori.detail', $category->slug ?? Str::slug($category->nama_kategori)) }}"
                                    class="inline-flex items-center justify-center gap-2 w-full bg-primary hover:bg-primary-hover text-white font-extrabold py-4 px-6 rounded-2xl transition-all duration-300 shadow-button-solid text-xs uppercase tracking-widest group/button"
                                >
                                    Lihat Pilihan Paket

                                    <i class="fa-solid fa-arrow-right text-[11px] transition-transform duration-300 group-hover/button:translate-x-1"></i>
                                </a>

                            </div>

                        </div>
                    </div>

                @empty

                    <div class="col-span-full text-center py-16 bg-white rounded-3xl text-slate-400 text-sm font-semibold border border-slate-200/60 shadow-premium">
                        Belum ada kategori paket aktif saat ini.
                    </div>

                @endforelse

            </div>

            {{-- EXPERIENCE --}}
            <div class="pt-20 border-t border-slate-200">

                <div class="text-center space-y-2 mb-12">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">
                        Experience
                    </h3>

                    <div class="w-8 h-1 bg-primary mx-auto rounded-full"></div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-10 md:gap-16 opacity-75 select-none transition-all duration-500">

                    @forelse($experiences as $exp)

                        @php
                            $logoPath = $exp->logo_image_path ?? ($exp->logo ?? null);
                            $companyName = $exp->company_name ?? ($exp->nama_instansi ?? 'Instansi');
                        @endphp

                    <div class="h-20 w-30 flex items-center justify-center transition-all duration-300 hover:scale-105">
                        <img
                            src="{{ asset('storage/' . $logoPath) }}"
                            alt="{{ $companyName }}"
                            class="max-h-full max-w-full object-contain"
                            title="{{ $companyName }}"
                        >
                    </div>

                    @empty

                        <div class="col-span-full text-center py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                            Belum ada logo mitra instansi yang diunggah.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>
    </section>

    <section id="cerita-kalisawah" class="py-28 bg-white px-6">
        <div class="max-w-7xl mx-auto space-y-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 border-b border-slate-100 pb-8">
                <div class="space-y-2">
                    <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">Kabar & Cerita</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Cerita Seru Terbaru</h2>
                    <p class="text-slate-500 text-sm">Dokumentasi serta rangkuman keseruan aktivitas terbaru kami</p>
                </div>
                <a href="{{ route('kabar.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-hover font-extrabold text-sm group transition-colors duration-300 tracking-wide bg-primary/5 px-6 py-4 rounded-2xl">
                    Lihat Semua Cerita <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1.5"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($kabars as $kabar)
                    @include('components.news-card', [
                        'image' => $kabar->foto ? asset('storage/' . $kabar->foto) : 'https://images.unsplash.com/photo-1488085061387-422e29b40080?q=80&w=500',
                        'date' => isset($kabar->tanggal) ? \Carbon\Carbon::parse($kabar->tanggal)->translatedFormat('d M Y') : date('d M Y'),
                        'title' => $kabar->judul,
                        'description' => Str::limit(strip_tags($kabar->isi), 120, '...'),
                        'slug' => $kabar->slug ?? \Illuminate\Support\Str::slug($kabar->judul)
                    ])
                @empty
                    <div class="col-span-full py-20 px-8 flex flex-col items-center justify-center text-center bg-slate-50 border border-dashed border-slate-200 rounded-[32px] space-y-4">
                        <div class="w-20 h-20 bg-white shadow-premium border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 text-2xl">
                            <i class="fa-regular fa-folder-open text-3xl text-primary/70"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-slate-800 font-extrabold text-base tracking-tight">Belum Ada Artikel Cerita</p>
                            <p class="text-slate-500 text-sm max-w-sm leading-relaxed">Catatan dokumentasi dan kabar kegiatan seru di <span class="text-secondary font-bold">Kalisawah</span> akan segera diperbarui oleh admin.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="testimoni" class="py-28 bg-slate-100/60 px-6 border-t border-b border-slate-200/40"
             x-data="{
                skip: 1,
                next() {
                    this.$refs.slider.scrollBy({ left: this.$refs.slider.offsetWidth, behavior: 'smooth' });
                },
                prev() {
                    this.$refs.slider.scrollBy({ left: -this.$refs.slider.offsetWidth, behavior: 'smooth' });
                }
             }">
        <div class="max-w-7xl mx-auto space-y-16">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-3">
                    <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">Reviews</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight text-left">Apa Kata Mereka?</h2>
                    <div class="w-16 h-1.5 bg-primary rounded-full"></div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm mr-2">
                        <button @click="prev()" class="w-12 h-12 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-all duration-200 active:scale-90 border border-slate-200">
                            <i class="fa-solid fa-chevron-left text-sm"></i>
                        </button>
                        <button @click="next()" class="w-12 h-12 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-all duration-200 active:scale-90 border border-slate-200">
                            <i class="fa-solid fa-chevron-right text-sm"></i>
                        </button>
                    </div>
                    <a href="{{ route('testimoni.create') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-hover text-white text-xs font-extrabold py-5 px-8 rounded-2xl transition-all duration-300 shadow-button-solid transform hover:-translate-y-0.5 active:translate-y-0 shrink-0 uppercase tracking-widest">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Ulasan
                    </a>
                </div>
            </div>

            <div class="relative">
            <div x-ref="slider" class="flex gap-8 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-6 scroll-smooth">

            @forelse($testimoni ?? [] as $testi)
            <div class="bg-white p-8 rounded-[24px] shadow-premium border border-slate-200/30 flex flex-col justify-between space-y-6 transform transition-all duration-300 hover:shadow-premium-hover snap-start shrink-0 w-full md:w-[calc(33.333%-22px)]">

            <div>
                {{-- 1. Teks Ulasan Pengunjung --}}
                <p class="text-slate-600 text-sm leading-relaxed font-normal italic">
                    "{{ $testi->ulasan }}"
                </p>

                {{-- 2. KOMPONEN BINTANG RATING (Dinamis sesuai nilai kolom 'rating' di database) --}}
                <div class="flex gap-1 mt-4">
                    @for($i = 0; $i < ($testi->rating ?? 5); $i++)
                        <i class="fa-solid fa-star text-secondary text-xs"></i>
                    @endfor
                    @for($i = 0; $i < (5 - ($testi->rating ?? 5)); $i++)
                        <i class="fa-solid fa-star text-slate-200 text-xs"></i>
                    @endfor
                </div>
            </div>

            {{-- 3. Informasi Profil User --}}
            <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                <img src="{{ $testi->foto_path ? asset('storage/' . $testi->foto_path) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=80' }}"
                    class="w-11 h-11 rounded-full object-cover border-2 border-primary/20"
                    alt="{{ $testi->nama }}">
                <div>
                    <h4 class="text-sm font-extrabold text-slate-900 tracking-tight">
                        {{ $testi->nama }}
                    </h4>
                    <span class="text-xs text-slate-400 block mt-0.5">
                        {{ $testi->instansi }}
                    </span>
                </div>
            </div>
            </div>
        @empty
            <div class="w-full text-center py-16 bg-white rounded-[24px] border border-dashed border-slate-300 text-slate-400 font-medium text-sm flex flex-col items-center justify-center gap-3 shadow-premium">
                <i class="fa-regular fa-comment-dots text-4xl text-primary/40"></i>
                <span>Belum ada ulasan dari pengunjung saat ini.</span>
            </div>
        @endforelse
                </div>
            </div>
        </div>
    </section>

    <section id="lokasi" class="py-24 px-6 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="space-y-4">
                    <span class="text-primary font-extrabold text-xs uppercase tracking-widest block">Lokasi Kami</span>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">Temukan Kami di <span class="text-secondary">Songgon</span></h2>
                    <div class="w-16 h-1.5 bg-primary rounded-full"></div>
                </div>

                <p class="text-slate-500 text-base leading-relaxed">
                    Kunjungi basecamp kami untuk memulai petualangan seru Anda. Kami berlokasi di area perkebunan yang sejuk, asri, dan mudah dijangkau di Songgon, Banyuwangi.
                </p>

                <div class="flex items-start gap-4 pt-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 shadow-inner">
                        <i class="fa-solid fa-location-dot text-xl"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-base font-extrabold text-slate-900">Alamat Lengkap</h4>
                        <p class="text-sm text-slate-500 leading-relaxed">Sumberagung, Sumberbulu, Kec. Songgon, Kabupaten Banyuwangi, Jawa Timur 68463.</p>
                    </div>
                </div>

                <div class="pt-6">
                    <a href="https://maps.app.goo.gl/hGfAXygyJ34U2wXV9" target="_blank" class="inline-flex items-center gap-3 bg-slate-900 hover:bg-primary text-white text-xs font-extrabold py-4 px-8 rounded-2xl transition-all duration-300 shadow-button-dark hover:shadow-button-solid transform hover:-translate-y-0.5 uppercase tracking-widest">
                        <i class="fa-solid fa-map-location-dot"></i> Buka di Google Maps
                    </a>
                </div>
            </div>

            <div class="h-[450px] w-full rounded-[32px] overflow-hidden shadow-premium border border-slate-200/50 relative group">
                <iframe src="https://maps.google.com/maps?q=Kalisawah%20Adventure%20Banyuwangi&hl=id&z=17&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="grayscale group-hover:grayscale-0 transition-all duration-700"></iframe>
            </div>
        </div>
    </section>
    @include('components.footer')
</body>
</html>
