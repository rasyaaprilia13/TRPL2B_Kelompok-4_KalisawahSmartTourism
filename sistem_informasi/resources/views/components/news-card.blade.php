@props(['image', 'date', 'title', 'description', 'slug'])

<article class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover:shadow-2xl transition-all flex flex-col h-full group">
    <div class="relative h-64 overflow-hidden bg-gray-50 flex items-center justify-center">
        <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://picsum.photos/600/400'">
        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
    </div>
    <div class="p-8 flex flex-col flex-grow">
        <div class="flex items-center gap-3 text-gray-400 text-xs font-semibold mb-4">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-calendar text-primary"></i>
                {{ $date }}
            </span>
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-location-dot text-primary"></i>
                Glenmore, Banyuwangi
            </span>
        </div>
        <h3 class="text-primary text-xl font-bold mb-4 leading-tight group-hover:text-dark-navy transition-colors line-clamp-2">
            {{ $title }}
        </h3>
        <p class="text-gray-500 text-sm mb-8 line-clamp-3 leading-relaxed">
            {{ $description }}
        </p>
        <div class="mt-auto">
            <a href="{{ url('/kabar/' . $slug) }}" class="inline-flex items-center gap-2 text-primary font-bold hover:gap-3 transition-all group-hover:text-hover-primary text-sm">
                Baca Selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</article>
