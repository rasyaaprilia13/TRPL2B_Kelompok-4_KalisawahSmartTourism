@extends('layouts.admin')

@section('title', 'Kategori Paket Wisata')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Kategori Paket Wisata</h2>

        <button class="btn btn-primary px-4 py-2"
                data-bs-toggle="modal"
                data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i>
            Tambah Kategori
        </button>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- JIKA VALIDASI GAGAL SECARA UMUM --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-times-circle me-2"></i>
            Data gagal disimpan. Silahkan cek kembali inputan Anda pada modal form.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3">No.</th>
                            <th class="py-3">Nama Kategori</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3">Tagline</th>
                            <th class="py-3">Slug</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kategoris as $index => $item)
                        <tr>
                            <td>{{ $kategoris->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 p-2 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        @if($item->gambar)
                                            <img src="{{ asset('storage/' . $item->gambar) }}" width="40" height="40" alt="icon" style="object-fit: cover; border-radius: 4px;">
                                        @else
                                            <i class="fas fa-image text-muted"></i>
                                        @endif
                                    </div>
                                    <span class="fw-bold">{{ $item->nama_kategori }}</span>
                                </div>
                            </td>
                            <td class="text-muted" style="max-width: 300px;">
                                {{ Str::limit($item->deskripsi, 80) }}
                            </td>
                            <td>{{ $item->tagline ?? '-' }}</td>
                            <td>{{ $item->slug }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- EDIT --}}
                                    <button class="btn btn-warning btn-sm text-white rounded-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- DELETE --}}
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDelete('{{ $item->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id }}"
                                          action="{{ route('admin.kategori-paket.destroy', $item->id) }}"
                                          method="POST"
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT --}}
                        <div class="modal fade modal-edit-class" id="modalEdit{{ $item->id }}" data-id="{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <form action="{{ route('admin.kategori-paket.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        {{-- Menyimpan status ID modal jika validasi error --}}
                                        <input type="hidden" name="modal_id" value="{{ $item->id }}">

                                        <div class="modal-body px-4">
                                            <div class="mb-3 text-center">
                                                <label class="d-block mb-2 text-muted small">Thumbnail Saat Ini</label>
                                                <img src="{{ asset('storage/' . $item->gambar) }}" class="rounded-3 border" width="80" height="80" style="object-fit: cover;">
                                            </div>

                                            @if($item->hero_image)
                                            <div class="mb-3 text-center">
                                                <label class="d-block mb-2 text-muted small">Hero Image Saat Ini</label>
                                                <img src="{{ asset('storage/' . $item->hero_image) }}" class="rounded-3 border" width="120" height="80" style="object-fit: cover;">
                                            </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Kategori</label>
                                                <input type="text" name="nama_kategori" class="form-control rounded-3 @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori', $item->nama_kategori) }}" required>
                                                @error('nama_kategori')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
                                                @error('deskripsi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tagline</label>
                                                <input type="text" name="tagline" class="form-control rounded-3 @error('tagline') is-invalid @enderror" value="{{ old('tagline', $item->tagline) }}">
                                                @error('tagline')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Ubah Thumbnail</label>
                                                <input type="file" name="gambar" class="form-control rounded-3 @error('gambar') is-invalid @enderror" accept="image/*">
                                                @error('gambar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Ubah Hero Image</label>
                                                <input type="file" name="hero_image" class="form-control rounded-3 @error('hero_image') is-invalid @enderror" accept="image/*">
                                                @error('hero_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Data Kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $kategoris->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Kategori Wisata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.kategori-paket.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control rounded-3 @error('nama_kategori') is-invalid @enderror" value="{{ old('nama_kategori') }}" placeholder="Contoh: Arung Jeram" required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Deskripsi kategori..." required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tagline</label>
                        <input type="text" name="tagline" class="form-control rounded-3 @error('tagline') is-invalid @enderror" value="{{ old('tagline') }}" placeholder="Petualangan Seru Menanti Anda">
                        @error('tagline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Thumbnail / Card Image</label>
                        <input type="file" name="gambar" class="form-control rounded-3 @error('gambar') is-invalid @enderror" accept="image/*" required>
                        @error('gambar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Hero Image</label>
                        <input type="file" name="hero_image" class="form-control rounded-3 @error('hero_image') is-invalid @enderror" accept="image/*">
                        @error('hero_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Kategori?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // Otopopup Modal jika terjadi Error Validasi pada sisi klien
    document.addEventListener("DOMContentLoaded", function() {
        @if ($errors->any())
            // Ambil ID modal yang error dari session flash lama jika ada
            let modalId = "{{ old('modal_id') }}";
            if (modalId) {
                let myModal = new bootstrap.Modal(document.getElementById('modalEdit' + modalId));
                myModal.show();
            } else {
                let myModal = new bootstrap.Modal(document.getElementById('modalTambah'));
                myModal.show();
            }
        @endif
    });
</script>
@endpush
