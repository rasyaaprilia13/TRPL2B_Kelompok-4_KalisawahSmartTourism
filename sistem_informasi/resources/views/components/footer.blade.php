@php

    $footerCategories = \App\Models\KategoriPaket::all();

    $footerData = [
        'location' => [
            'name' => 'KaliSawah Adventure',
            'address' => 'Sumberagung, Sumberbulu, Kec. Songgon, Kabupaten Banyuwangi, Jawa Timur',
            'maps_link' => 'https://maps.app.goo.gl/UjpSTERGC5aBL6ry5'
        ],

        'about_menu' => [
            [
                'label' => 'Cerita Kalisawah',
                'url' => '#cerita-kalisawah'
            ],
            [
                'label' => 'Testimoni Wisata',
                'url' => '#testimoni'
            ],
        ],

        'contact' => [
            'phone' => '+62 08176421713',

            'socials' => [
                [
                    'icon' => 'fa-instagram',
                    'url' => 'https://www.instagram.com/kalisawah.adventure?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=='
                ],
                [
                    'icon' => 'fa-tiktok',
                    'url' => 'https://www.tiktok.com/@kalisawah.adventure?is_from_webapp=1&sender_device=pc'
                ],
                [
                    'icon' => 'fa-youtube',
                    'url' => 'https://youtube.com/@kalisawahadventure?si=0C2jBz31txaiGuM3'
                ],
            ]
        ],

        'copyright' => [
            'year' => date('Y'),
            'text' => 'Kalisawah Adventure | All Rights Reserved'
        ]
    ];

@endphp

<footer id="footer"
    style="background-color: #0B1224 !important;"
    class="text-white pt-20 pb-10 px-6 md:px-20 scroll-mt-20">

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">

        {{-- Logo & Lokasi --}}
        <div>

            <img src="{{ asset('images/logoKalisawah.png') }}"
                 alt="{{ $footerData['location']['name'] }}"
                 class="h-25 mb-50"
                 onerror="this.src='https://picsum.photos/200/50'">

            <h3 class="text-xl font-bold mb-4">
                {{ $footerData['location']['name'] }}
            </h3>

            <p class="text-gray-400 text-sm leading-relaxed mb-4">
                {{ $footerData['location']['address'] }}
            </p>

            <a href="{{ $footerData['location']['maps_link'] }}"
               target="_blank"
               class="text-secondary hover:underline flex items-center gap-2 text-sm font-semibold">

                <i class="fa-solid fa-location-dot"></i>

                Lihat di Maps

            </a>

        </div>

        {{-- Paket Wisata Dinamis --}}
        <div>

            <h3 class="text-lg font-bold mb-6 border-l-4 border-secondary pl-3">
                Paket Seru
            </h3>

            <ul class="space-y-3 text-gray-400 text-sm list-none p-0">

                @forelse($footerCategories as $category)

                    <li>

                        <a href="{{ route('pengunjung.booking.booking-form', ['kategori' => $category->id]) }}"
                           class="hover:text-secondary transition-colors block py-0.5">

                            {{ $category->nama_kategori }}

                        </a>

                    </li>

                @empty

                    <li class="text-gray-500">
                        Belum ada kategori paket
                    </li>

                @endforelse

            </ul>

        </div>

        {{-- Tentang --}}
        <div>

            <h3 class="text-lg font-bold mb-6 border-l-4 border-secondary pl-3">
                Tentang Kami
            </h3>

            <ul class="space-y-3 text-gray-400 text-sm list-none p-0">

                @foreach($footerData['about_menu'] as $menu)

                    <li>

                        <a href="{{ $menu['url'] }}"
                           class="hover:text-secondary transition-colors block py-0.5">

                            {{ $menu['label'] }}

                        </a>

                    </li>

                @endforeach

            </ul>

        </div>

        {{-- Kontak --}}
        <div>

            <h3 class="text-lg font-bold mb-6 border-l-4 border-secondary pl-3">
                Hubungi Kami
            </h3>

            <ul class="space-y-4 text-gray-400 text-sm list-none p-0">
            <li class="flex items-center gap-3">
                <i class="fa-solid fa-phone text-secondary"></i>

                <a href="https://wa.me/628176421713"
                target="_blank"
                class="hover:text-secondary transition-colors">
                    {{ $footerData['contact']['phone'] }}
                </a>
            </li>
            </ul>

            <div class="flex gap-4 mt-8">

                @foreach($footerData['contact']['socials'] as $social)

                    <a href="{{ $social['url'] }}"
                       class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors text-white">

                        <i class="fa-brands {{ $social['icon'] }}"></i>

                    </a>

                @endforeach

            </div>

        </div>

    </div>

    {{-- Bottom Footer --}}
    <div class="max-w-7xl mx-auto border-t border-white/10 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">

        <p>
            © {{ $footerData['copyright']['year'] }}
            {{ $footerData['copyright']['text'] }}
        </p>

        <div class="flex gap-6">

            <span class="hover:text-gray-400 cursor-pointer">
                Syarat & Ketentuan
            </span>

            <span class="hover:text-gray-400 cursor-pointer">
                Kebijakan Privasi
            </span>

        </div>

    </div>

</footer>
