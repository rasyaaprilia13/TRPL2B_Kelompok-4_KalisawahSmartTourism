@extends('layouts.admin')

@section('title', 'Daftar Logo Perusahaan Client')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Logo Client</h2>
            <p class="text-muted mb-0">Kelola logo perusahaan client untuk landing page</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Logo
        </button>
    </div>

    {{-- NOTIFIKASI SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR SYSTEM --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR VALIDASI FORMAT / UKURAN FILE --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Gagal Memproses Data:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">No.</th>
                            <th style="width: 25%">Logo</th>
                            <th>Nama Perusahaan</th>
                            <th class="text-center" style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logos as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $item->logo_image_path) }}"
                                     alt="{{ $item->company_name }}"
                                     class="img-thumbnail"
                                     style="max-height: 50px; object-fit: contain;">
                            </td>
                            <td class="fw-bold">{{ $item->company_name }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.client-logos.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        {{-- MODAL EDIT LOGO --}}
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.client-logos.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Logo Perusahaan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Perusahaan</label>
                                                <input type="text" name="company_name" class="form-control" value="{{ $item->company_name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Logo Baru <span class="text-muted">(Kosongkan jika tidak diubah)</span></label>
                                                {{-- PERBAIKAN: Ditambahkan accept file format gambar --}}
                                                <input type="file" name="logo_image_path" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                                <div class="form-text text-danger">Format wajib: JPG, JPEG, PNG, WEBP (Max: 2MB)</div>
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label d-block">Logo Saat Ini:</label>
                                                <img src="{{ asset('storage/' . $item->logo_image_path) }}" alt="" style="max-height: 40px; object-fit: contain;">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada logo perusahaan tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH LOGO --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.client-logos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Logo Perusahaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" name="company_name" class="form-control" required placeholder="Contoh: PT. Maju Mundur">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Logo</label>
                        {{-- PERBAIKAN: Ditambahkan accept file format gambar --}}
                        <input type="file" name="logo_image_path" class="form-control" required accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text text-danger">Format wajib: JPG, JPEG, PNG, WEBP (Max: 2MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
            title: 'Hapus Logo Client?',
            text: "Data dan file gambar akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
