@props(['name', 'instansi', 'review', 'image', 'rating' => 5])

<div class="bg-white p-8 rounded-3xl shadow-lg relative pt-14 flex flex-col h-full group hover:-translate-y-1.5 transition-all duration-300 border border-gray-50">
    <div class="absolute -top-6 left-8 w-14 h-14 rounded-full overflow-hidden border-4 border-white shadow-md bg-gray-100 flex items-center justify-center">
        <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://picsum.photos/100/100'">
    </div>
    
    <div class="text-secondary text-5xl font-serif absolute top-4 right-8 opacity-20 group-hover:opacity-30 transition-opacity select-none">"</div>
    
    <div class="flex gap-1 mb-4">
        @for($i=0; $i<min(5, max(1, $rating)); $i++)
            <i class="fa-solid fa-star text-secondary text-[11px]"></i>
        @endfor
    </div>
    
    <p class="text-gray-500 text-sm italic mb-8 leading-relaxed flex-grow">
        "{{ $review }}"
    </p>
    
    <div class="mt-auto pt-5 border-t border-gray-100">
        <h4 class="font-bold text-dark-navy group-hover:text-primary transition-colors text-base">{{ $name }}</h4>
        <p class="text-secondary text-[11px] font-bold uppercase tracking-wider mt-0.5">{{ $instansi }}</p>
    </div>
</div>