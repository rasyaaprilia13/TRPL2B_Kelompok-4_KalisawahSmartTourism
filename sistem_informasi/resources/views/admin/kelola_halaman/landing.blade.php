@extends('layouts.admin')

@section('title', 'Kelola Landing Page')

@section('content')
<div class="container-fluid py-4">
    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Landing Page</h2>
            <p class="text-muted mb-0">Atur informasi konten utama dan multi-gambar slider yang tampil di bagian Hero Section.</p>
        </div>
        <div class="d-none d-sm-block">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-3">Multi-Image Mode</span>
        </div>
    </div>

    {{-- FLASH NOTIFICATION SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- PERBAIKAN: NOTIFIKASI ERROR JIKA FORMAT BUKAN GAMBAR / UKURAN OVERSIZE --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Gagal Menyimpan Data:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ ($setting && $setting->id) ? route('admin.landing-settings.update', $setting->id) : route('admin.landing-settings.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @if($setting && $setting->id)
            @method('PUT')
        @endif

        <div class="row g-4">
            {{-- FORM CONFIGURATION --}}
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-b border-light">
                            <i class="fas fa-edit text-primary fs-5"></i>
                            <h5 class="fw-bold mb-0">Konfigurasi Hero Konten</h5>
                        </div>

                        {{-- HERO TITLE --}}
                        <div class="mb-4">
                            <label class="form-label text-uppercase tracking-wider fw-bold text-muted small" style="font-size: 0.75rem;">Judul Utama (Hero Title)</label>
                            <input type="text"
                                   name="hero_title"
                                   value="{{ old('hero_title', $setting->hero_title ?? '') }}"
                                   placeholder="Contoh: Jelajahi Petualangan Tanpa Batas"
                                   class="form-control @error('hero_title') is-invalid @enderror py-2 rounded-3">
                            @error('hero_title')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- HERO SUBTITLE --}}
                        <div class="mb-4">
                            <label class="form-label text-uppercase tracking-wider fw-bold text-muted small" style="font-size: 0.75rem;">Sub-Judul / Deskripsi Pendek (Hero Subtitle)</label>
                            <textarea name="hero_subtitle"
                                      rows="4"
                                      placeholder="Tulis deskripsi pelengkap di sini..."
                                      class="form-control @error('hero_subtitle') is-invalid @enderror py-2 rounded-3" style="resize: none;">{{ old('hero_subtitle', $setting->hero_subtitle ?? '') }}</textarea>
                            @error('hero_subtitle')
                                <div class="invalid-feedback fw-semibold">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- INPUT GAMBAR BERUNTUN --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-b border-light">
                            <i class="fas fa-images text-success fs-5"></i>
                            <h5 class="fw-bold mb-0">Slot Gambar Slider</h5>
                        </div>

                        {{-- GAMBAR 1 --}}
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold text-dark small">Gambar Utama (Slider 1) <span class="text-muted fw-normal">(JPG, JPEG, PNG, WEBP | Max 2MB)</span></label>
                            <input type="file" name="hero_image" class="form-control @error('hero_image') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp" id="inputImg1">
                            @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- GAMBAR 2 --}}
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold text-dark small">Gambar Kedua (Slider 2) <span class="text-muted fw-normal">(JPG, JPEG, PNG, WEBP | Max 2MB)</span></label>
                            <input type="file" name="hero_image_2" class="form-control @error('hero_image_2') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp" id="inputImg2">
                            @error('hero_image_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- GAMBAR 3 --}}
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold text-dark small">Gambar Ketiga (Slider 3) <span class="text-muted fw-normal">(JPG, JPEG, PNG, WEBP | Max 2MB)</span></label>
                            <input type="file" name="hero_image_3" class="form-control @error('hero_image_3') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp" id="inputImg3">
                            @error('hero_image_3') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ACTION BUTTON --}}
                        <div class="pt-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold d-flex align-items-center gap-2 shadow-sm">
                                <i class="fas fa-save"></i>
                                {{ ($setting && $setting->id) ? 'Perbarui Pengaturan' : 'Simpan Pengaturan Baru' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIVE PREVIEW MONITOR (KANAN) --}}
            <div class="col-xl-6 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-b border-light">
                            <i class="fas fa-eye text-warning fs-5"></i>
                            <h5 class="fw-bold mb-0">Pratinjau Slider Aktif</h5>
                        </div>

                        <div class="d-flex flex-column gap-4">
                            {{-- PREVIEW 1 --}}
                            <div class="border rounded-3 p-2 bg-white text-center">
                                <span class="badge bg-secondary mb-2">Slider 1</span>
                                <img id="preview1" src="{{ ($setting && $setting->hero_image_path) ? asset('storage/' . $setting->hero_image_path) : 'https://placehold.co/600x300?text=Belum+Ada+Gambar+1' }}" class="w-100 rounded" style="height: 150px; object-fit: cover;">
                            </div>

                            {{-- PREVIEW 2 --}}
                            <div class="border rounded-3 p-2 bg-white text-center">
                                <span class="badge bg-secondary mb-2">Slider 2</span>
                                <img id="preview2" src="{{ ($setting && $setting->hero_image_path_2) ? asset('storage/' . $setting->hero_image_path_2) : 'https://placehold.co/600x300?text=Belum+Ada+Gambar+2' }}" class="w-100 rounded" style="height: 150px; object-fit: cover;">
                            </div>

                            {{-- PREVIEW 3 --}}
                            <div class="border rounded-3 p-2 bg-white text-center">
                                <span class="badge bg-secondary mb-2">Slider 3</span>
                                <img id="preview3" src="{{ ($setting && $setting->hero_image_path_3) ? asset('storage/' . $setting->hero_image_path_3) : 'https://placehold.co/600x300?text=Belum+Ada+Gambar+3' }}" class="w-100 rounded" style="height: 150px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi pembantu untuk handle live preview tiap komponen input secara instan
    function setupPreview(inputId, previewId) {
        document.getElementById(inputId).addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Daftarkan handler preview untuk masing-masing slot gambar
    setupPreview('inputImg1', 'preview1');
    setupPreview('inputImg2', 'preview2');
    setupPreview('inputImg3', 'preview3');
</script>
@endpush
