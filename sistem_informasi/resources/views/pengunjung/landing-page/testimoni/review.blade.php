@extends('layouts.app')

@section('title', 'Tulis Review - Kalisawah Adventure')

@section('content')
<section class="pt-36 md:pt-44 pb-24 px-6 md:px-20 bg-white min-h-screen">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center space-y-4">
            <h1 class="text-3xl md:text-5xl font-bold text-dark-navy mb-4">Tulis <span class="text-secondary">Review</span></h1>
            <p class="text-gray-500 font-medium italic">Bagikan pengalaman serumu bersama Kalisawah Adventure.</p>
        </div>

        <!-- Form -->
        <form id="review-form" action="{{ route('testimoni.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Profile Picture Upload -->
            <div class="flex flex-col items-center md:items-start gap-6 mb-12">
                <label class="block text-xs font-bold text-dark-navy uppercase tracking-[0.2em] ml-1">Foto Profil</label>
                <div class="relative group">
                    <input type="file" name="foto" id="foto" class="hidden" accept="image/*" onchange="previewImage(this)">
                    <label for="foto" class="relative block w-32 h-32 rounded-full overflow-hidden border-4 border-soft-blue cursor-pointer hover:border-secondary transition-all group bg-light-gray/20">
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 group-hover:text-secondary transition-colors" id="upload-placeholder">
                            <i class="fa-solid fa-camera text-2xl mb-1"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tighter">Upload</span>
                        </div>
                        <img id="image-preview" class="hidden w-full h-full object-cover">
                        <div id="hover-overlay" class="hidden absolute inset-0 bg-black/40 items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                             <i class="fa-solid fa-sync text-white text-xl"></i>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-xs font-bold text-dark-navy uppercase tracking-[0.2em] mb-3 ml-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" placeholder="Nama Anda" required
                        class="w-full h-14 px-6 bg-light-gray/10 border-b-2 border-gray-100 focus:border-secondary outline-none transition-all text-dark-navy font-medium placeholder:text-gray-300">
                </div>

                <!-- Instansi -->
                <div>
                    <label for="instansi" class="block text-xs font-bold text-dark-navy uppercase tracking-[0.2em] mb-3 ml-1">Instansi / Sekolah</label>
                    <input type="text" name="instansi" id="instansi" placeholder="Contoh: Universitas Jember" required
                        class="w-full h-14 px-6 bg-light-gray/10 border-b-2 border-gray-100 focus:border-secondary outline-none transition-all text-dark-navy font-medium placeholder:text-gray-300">
                </div>
            </div>

            <!-- Star Rating -->
            <div>
                <label class="block text-xs font-bold text-dark-navy uppercase tracking-[0.2em] mb-3 ml-1">Rating Pengalaman</label>
                <div class="flex flex-row-reverse justify-end gap-2" id="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" class="hidden peer" checked />
                    <label for="star5" class="cursor-pointer text-3xl text-gray-200 peer-hover:text-secondary peer-checked:text-secondary transition-all">
                        <i class="fa-solid fa-star"></i>
                    </label>

                    <input type="radio" id="star4" name="rating" value="4" class="hidden peer" />
                    <label for="star4" class="cursor-pointer text-3xl text-gray-200 peer-hover:text-secondary peer-checked:text-secondary transition-all">
                        <i class="fa-solid fa-star"></i>
                    </label>

                    <input type="radio" id="star3" name="rating" value="3" class="hidden peer" />
                    <label for="star3" class="cursor-pointer text-3xl text-gray-200 peer-hover:text-secondary peer-checked:text-secondary transition-all">
                        <i class="fa-solid fa-star"></i>
                    </label>

                    <input type="radio" id="star2" name="rating" value="2" class="hidden peer" />
                    <label for="star2" class="cursor-pointer text-3xl text-gray-200 peer-hover:text-secondary peer-checked:text-secondary transition-all">
                        <i class="fa-solid fa-star"></i>
                    </label>

                    <input type="radio" id="star1" name="rating" value="1" class="hidden peer" />
                    <label for="star1" class="cursor-pointer text-3xl text-gray-200 peer-hover:text-secondary peer-checked:text-secondary transition-all">
                        <i class="fa-solid fa-star"></i>
                    </label>
                </div>
            </div>

            <!-- Ulasan -->
            <div>
                <label for="ulasan" class="block text-xs font-bold text-dark-navy uppercase tracking-[0.2em] mb-3 ml-1">Ulasan / Testimoni</label>
                <textarea name="ulasan" id="ulasan" rows="4" placeholder="Tuliskan pengalaman Anda..." required
                    class="w-full p-6 bg-light-gray/10 border-2 border-gray-100 rounded-2xl focus:border-secondary outline-none transition-all text-dark-navy font-medium placeholder:text-gray-300 resize-none leading-relaxed"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-4 pt-6">
                <a href="{{ route('landing-page.home') }}" class="w-full sm:w-1/2 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-center shadow-lg shadow-gray-100/50 hover:-translate-y-1 active:scale-95 active:translate-y-[2px] transition-all duration-150">
                    Kembali
                </a>
                <button type="submit" class="w-full sm:w-1/2 py-4 bg-secondary hover:bg-yellow-500 text-dark-navy rounded-xl font-bold text-center shadow-lg shadow-secondary/20 hover:shadow-secondary/40 hover:-translate-y-1 active:scale-95 active:translate-y-[2px] transition-all duration-150">
                    Kirim Review
                </button>
            </div>
        </form>

        <div class="mt-16 flex items-center justify-center gap-2 text-gray-300">
            <div class="w-12 h-[1px] bg-gray-100"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest">Kalisawah Adventure</p>
            <div class="w-12 h-[1px] bg-gray-100"></div>
        </div>
    </div>
</section>

<!-- SUCCESS NOTIFICATION (MODAL) -->
<div id="success-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center px-6">
    <div class="absolute inset-0 bg-dark-navy/60 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-[2.5rem] p-8 md:p-12 max-w-sm w-full text-center shadow-2xl transform scale-90 opacity-0 transition-all duration-300" id="modal-content">
        <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-6">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <h2 class="text-2xl font-bold text-dark-navy mb-4">Review Berhasil Dikirim!</h2>
        <p class="text-gray-500 leading-relaxed">
            Terima kasih sudah berbagi pengalaman di Kalisawah Adventure ✨
        </p>
        <div class="mt-8 pt-6 border-t border-gray-50">
            <p class="text-xs text-gray-400 italic">Mengarahkan kembali ke beranda...</p>
        </div>
    </div>
</div>

<script>
    // Image Preview Logic
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const overlay = document.getElementById('hover-overlay');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Form Submission & Success Logic
    document.getElementById('review-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Tahan reload halaman otomatis

        const formData = new FormData(this);
        const modal = document.getElementById('success-modal');
        const content = document.getElementById('modal-content');

        // Kirim data secara real-time ke Laravel Controller
        fetch("{{ route('testimoni.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // JIKA BERHASIL MASUK DATABASE, BARU MUNCULKAN ANIMASI MODAL SUKSES
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                setTimeout(() => {
                    content.classList.remove('scale-90', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Redirect kembali ke beranda setelah 3 detik
                setTimeout(() => {
                    window.location.href = "{{ route('landing-page.home') }}";
                }, 3000);
            } else {
                return response.json().then(data => {
                    alert('Gagal mengirim review: ' + (data.message || 'Terjadi kesalahan validasi.'));
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi masalah koneksi jaringan ke server.');
        });
    });
</script>

<style>
    #star-rating label:hover,
    #star-rating label:hover ~ label,
    #star-rating input:checked ~ label {
        color: #FFC236;
    }
</style>
@endsection
