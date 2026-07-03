@extends('layouts.admin')

@section('title', 'Kelola Kabar')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Kabar</h2>
            <p class="text-muted mb-0">Kelola konten kabar untuk halaman website</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Kabar
        </button>
    </div>

    {{-- NOTIFIKASI SUKSES (Sesuai dengan ->with('success') di Controller) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR VALIDASI (Menangkap error dari $request->validate) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Terjadi Kesalahan Input:</div>
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
                            <th style="width: 5%">No.</th>
                            <th style="width: 15%">Foto</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th class="text-center" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kabars as $item)
                        <tr>
                            {{-- Perbaikan nomor: Menyesuaikan nomor urut index dengan Pagination (paginate(6)) --}}
                            <td>{{ $loop->iteration + ($kabars->firstItem() - 1) }}</td>
                            <td>
                                {{-- Perbaikan Logika: Mengecek field foto, bukan field ID --}}
                                @if(!empty($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                    class="img-thumbnail"
                                    style="width: 80px; height: 60px; object-fit: cover;">
                                @else
                                    <span class="text-muted small fst-italic">Tidak ada foto</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $item->judul }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                            <td class="text-center">

                                {{-- HUBUNGKAN KE FUNCTION update() --}}
                                <button class="btn btn-warning btn-sm text-white mb-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- HUBUNGKAN KE FUNCTION destroy() --}}
                                <button class="btn btn-danger btn-sm mb-1" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.kabar.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                {{-- MODAL EDIT ( Sinkron dengan data yang di-update ) --}}
                                <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg text-start">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.kabar.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Kabar</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Judul</label>
                                                        <input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Tanggal</label>
                                                        <input type="date" name="tanggal" class="form-control" value="{{ $item->tanggal }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Isi Kabar</label>
                                                        <textarea name="isi_kabar" class="form-control" rows="4" required>{{ $item->isi_kabar }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold d-block">Foto Saat Ini</label>
                                                        @if(!empty($item->foto))
                                                            <img src="{{ asset('storage/' . $item->foto) }}" class="img-thumbnail mb-2" style="max-height: 100px;">
                                                        @endif
                                                        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                                                        <small class="text-muted fst-italic">Biarkan kosong jika tidak ingin mengubah foto. (Maks 2MB)</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada kabar tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- LINK PAGINATION (Membagi per 6 data sesuai dengan perintah Controller) --}}
            <div class="mt-3">
                {{ $kabars->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ( HUBUNGKAN KE FUNCTION store() ) --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.kabar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kabar Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Kabar</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul kabar..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Upload Foto</label>
                            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png" required>
                            <small class="text-muted fst-italic">Format: JPG, JPEG, PNG | Maks 2MB</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Isi Kabar</label>
                        <textarea name="isi_kabar" class="form-control" rows="5" placeholder="Tulis konten berita atau kabar di sini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kabar</button>
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
            title: 'Hapus Kabar?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
