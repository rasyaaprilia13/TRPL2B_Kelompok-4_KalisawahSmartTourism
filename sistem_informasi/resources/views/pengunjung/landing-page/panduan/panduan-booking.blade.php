@extends('layouts.app')

@section('title', 'Panduan Booking - Kalisawah Adventure')

@section('content')
    <section class="relative h-[50vh] min-h-[400px] w-full overflow-hidden flex items-center justify-center text-center">
        <img src="https://picsum.photos/id/1015/1920/1080" alt="Hero Background" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-dark-navy/70"></div>
        <div class="relative z-10 px-6 max-w-5xl mx-auto pt-20">
            <h1 class="text-white text-4xl md:text-6xl font-black mb-6 leading-tight">
                Panduan <span class="text-secondary">Booking</span>
            </h1>
            <p class="text-gray-200 text-lg md:text-xl font-medium max-w-2xl mx-auto leading-relaxed">
                Ikuti langkah mudah berikut untuk melakukan pemesanan paket wisata di Kalisawah Adventure secara praktis dan cepat.
            </p>
        </div>
    </section>

    <section class="py-24 px-6 md:px-20 bg-white">
        <div class="max-w-4xl mx-auto">

            <div class="space-y-20 relative">
                <div class="absolute left-[50%] top-0 bottom-0 w-1 bg-gray-100 -translate-x-1/2 hidden md:block"></div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 text-center md:text-right order-2 md:order-1">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">1. Pilih Paket Adventure</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Jelajahi berbagai paket wisata kami (Rafting, Camping, Outbound, dll). Klik tombol <strong class="text-primary">"Detail Paket"</strong> untuk melihat info lengkap harga dan fasilitas.
                        </p>
                    </div>
                    <div class="relative z-10 w-20 h-20 bg-primary text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-primary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div class="flex-1 order-3 hidden md:block"></div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 order-3 md:order-1 hidden md:block"></div>
                    <div class="relative z-10 w-20 h-20 bg-secondary text-dark-navy rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-secondary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left order-2 md:order-3">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">2. Isi Form Booking</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Masukkan data diri Anda (Nama, WhatsApp, Tanggal Kunjungan) dan pilih paket yang diinginkan. Pastikan data sudah sesuai sebelum menekan tombol konfirmasi.
                        </p>
                    </div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 text-center md:text-right order-2 md:order-1">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">3. Konfirmasi Booking</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Sistem akan memproses data Anda. Setelah konfirmasi, Anda akan diarahkan ke halaman status pemesanan dengan status awal <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">Pending</span>.
                        </p>
                    </div>
                    <div class="relative z-10 w-20 h-20 bg-primary text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-primary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="flex-1 order-3 hidden md:block"></div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 order-3 md:order-1 hidden md:block"></div>
                    <div class="relative z-10 w-20 h-20 bg-secondary text-dark-navy rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-secondary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left order-2 md:order-3">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">4. Verifikasi Admin</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Tim admin kami akan segera melakukan validasi data dan mengecek ketersediaan jadwal. Status akan berubah menjadi <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">Confirmed</span> jika disetujui.
                        </p>
                    </div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 text-center md:text-right order-2 md:order-1">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">5. Kode Booking Dibuat</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Setelah disetujui, sistem akan meng-generate <strong class="text-primary text-lg">Kode Booking Unik</strong>. Simpan kode ini sebagai bukti reservasi sah Anda.
                        </p>
                    </div>
                    <div class="relative z-10 w-20 h-20 bg-primary text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-primary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div class="flex-1 order-3 hidden md:block"></div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 order-3 md:order-1 hidden md:block"></div>
                    <div class="relative z-10 w-20 h-20 bg-secondary text-dark-navy rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-secondary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left order-2 md:order-3">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">6. Cek Status Booking</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Kapan pun Anda bisa memantau status reservasi di menu "Cek Status". Informasi paket, tanggal, dan kode booking akan tampil lengkap di sana.
                        </p>
                    </div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 text-center md:text-right order-2 md:order-1">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">7. Selesai / Check-in</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Pada hari H, cukup tunjukkan Kode Booking Anda kepada petugas loket untuk proses check-in. Anda siap menikmati petualangan!
                        </p>
                    </div>
                    <div class="relative z-10 w-20 h-20 bg-primary text-white rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-primary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="flex-1 order-3 hidden md:block"></div>
                </div>

                <div class="relative flex flex-col md:flex-row items-center gap-8 md:gap-20">
                    <div class="flex-1 order-3 md:order-1 hidden md:block"></div>
                    <div class="relative z-10 w-20 h-20 bg-secondary text-dark-navy rounded-3xl flex items-center justify-center text-3xl shadow-xl shadow-secondary/20 order-1 md:order-2 shrink-0">
                        <i class="fa-solid fa-house-circle-check"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left order-2 md:order-3">
                        <h3 class="text-2xl font-black text-dark-navy mb-4">8. Fitur Cari Booking</h3>
                        <p class="text-gray-500 leading-relaxed">
                            Lupa kode booking? Tenang! Gunakan fitur <strong class="text-primary">"Cari Booking"</strong> di halaman utama hanya dengan memasukkan Nama atau Nomor HP Anda.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-16 bg-soft-blue rounded-[3rem] p-10 md:p-16 text-center shadow-inner">
                <h2 class="text-3xl md:text-4xl font-black text-dark-navy mb-6">Sudah Paham Caranya?</h2>
                <p class="text-gray-600 mb-10 max-w-lg mx-auto">Jangan tunda lagi, amankan jadwal petualanganmu sekarang juga di Kalisawah Adventure!</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('landing-page.home') }}#paket-seru" class="bg-primary text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-primary/20 hover:bg-hover-primary hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-widest">
                        Pilih Paket
                    </a>
                    <a href="{{ route('landing-page.home') }}#pencarian-booking" class="bg-white text-primary border-2 border-primary px-10 py-4 rounded-2xl font-black hover:bg-blue-50 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-widest">
                        Cari Booking
                    </a>
                </div>
            </div>

        </div> </section> @endsection
