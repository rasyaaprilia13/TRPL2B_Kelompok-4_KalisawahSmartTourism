@extends('layouts.app')

@section('title', $kabar->judul . ' - Kalisawah Adventure')

@section('content')
    <!-- Detail Header -->
    <section class="pt-40 md:pt-48 pb-16 px-6 md:px-20 max-w-5xl mx-auto">
        <div class="mb-10">
            <div class="flex flex-wrap items-center gap-x-8 gap-y-4 mb-10">
                <span class="text-gray-400 text-sm flex items-center gap-2.5 font-medium">
                    <i class="fa-solid fa-calendar text-primary"></i>
                    {{ \Carbon\Carbon::parse($kabar->tanggal)->translatedFormat('d F Y') }}
                </span>
                <span class="text-gray-400 text-sm flex items-center gap-2.5 font-medium">
                    <i class="fa-solid fa-user text-primary"></i> Admin Kalisawah
                </span>
                <span class="text-gray-400 text-sm flex items-center gap-2.5 font-medium">
                    <i class="fa-solid fa-location-dot text-primary"></i> Glenmore, Banyuwangi
                </span>
            </div>
            <h1 class="text-3xl md:text-6xl font-bold text-dark-navy leading-[1.15] mb-8">
                {{ $kabar->judul }}
            </h1>
        </div>

        <!-- Main Image -->
        <div class="rounded-[3rem] overflow-hidden shadow-2xl mb-16">
            <img src="{{ $kabar->foto ? asset('storage/' . $kabar->foto) : 'https://picsum.photos/seed/' . $kabar->id . '/1200/800' }}" alt="{{ $kabar->judul }}" class="w-full object-cover">
        </div>

        <!-- Article Content -->
        <div class="prose prose-lg max-w-4xl text-gray-600 leading-relaxed space-y-8">
            {!! $kabar->isi_kabar !!}
        </div>

        <!-- Share & Tags -->
        <div class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-dark-navy uppercase tracking-widest">Share:</span>
                <div class="flex gap-2">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary hover:text-white transition-all">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary hover:text-white transition-all">
                        <i class="fa-brands fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 hover:bg-primary hover:text-white transition-all">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    @if(isset($relatedKabars) && $relatedKabars->count() > 0)
    <section class="py-24 bg-soft-blue px-6 md:px-20">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-2xl md:text-3xl font-bold text-dark-navy mb-12">Cerita Seru <span class="text-secondary">Lainnya</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedKabars as $related)
                <x-news-card
                    :image="$related->foto ? asset('storage/' . $related->foto) : 'https://picsum.photos/seed/' . $related->id . '/800/600'"
                    :date="\Carbon\Carbon::parse($related->tanggal)->translatedFormat('d F Y')"
                    :title="$related->judul"
                    :description="Str::limit(strip_tags($related->isi_kabar), 120)"
                    :slug="Str::slug($related->judul)"
                />
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
