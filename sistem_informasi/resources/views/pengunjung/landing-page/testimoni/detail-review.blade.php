@extends('layouts.app')

@section('title', 'Testimoni Pengunjung - Kalisawah Adventure')

@section('content')
<section class="pt-36 md:pt-44 pb-24 px-6 md:px-20 bg-[#f8f9fb] min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10 space-y-4">
            <h1 class="text-4xl md:text-6xl font-bold text-dark-navy mb-4">Kata <span class="text-secondary">Mereka</span></h1>
            <p class="text-gray-500 text-lg font-medium italic max-w-2xl mx-auto">Pendapat jujur dari mereka yang telah merasakan keseruan petualangan bersama Kalisawah Adventure</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

            @forelse ($testimonials ?? [] as $testimoni)
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 flex flex-col h-full group">
                    <div class="flex items-center gap-5 mb-8">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-soft-blue group-hover:border-secondary transition-colors shrink-0 shadow-sm">
                            {{-- Menggunakan foto_path dari database --}}
                            <img src="{{ $testimoni->foto_path ? asset('storage/' . $testimoni->foto_path) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=80' }}" alt="{{ $testimoni->nama }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-bold text-dark-navy text-lg leading-tight">{{ $testimoni->nama }}</h4>
                            <p class="text-secondary text-xs font-bold uppercase tracking-[0.1em] mt-1">{{ $testimoni->instansi }}</p>
                        </div>
                    </div>

                    <div class="flex gap-1.5 mb-6">
                        @for($i = 0; $i < ($testimoni->rating ?? 5); $i++)
                            <i class="fa-solid fa-star text-secondary text-sm"></i>
                        @endfor
                        @for($i = 0; $i < (5 - ($testimoni->rating ?? 5)); $i++)
                            <i class="fa-solid fa-star text-gray-200 text-sm"></i>
                        @endfor
                    </div>

                    <p class="text-gray-500 leading-relaxed italic flex-grow">
                        "{{ $testimoni->ulasan }}"
                    </p>

                    <div class="mt-8 flex justify-end opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="fa-solid fa-quote-right text-3xl text-primary"></i>
                    </div>
                </div>
            @empty
                {{-- Tampilan kalau belum ada data di database --}}
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 bg-white rounded-[2.5rem] border border-dashed border-gray-300 text-gray-400 font-medium shadow-sm">
                    <p>Belum ada ulasan dari pengunjung.</p>
                </div>
            @endforelse

        </div>

        <div class="mt-24 text-center">
            <div class="inline-flex items-center justify-center gap-2 px-6 py-2 bg-white rounded-full border border-gray-100 text-gray-400 text-xs font-bold uppercase tracking-widest shadow-sm">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Review Terverifikasi Pelanggan
            </div>
        </div>
    </div>
</section>
@endsection
