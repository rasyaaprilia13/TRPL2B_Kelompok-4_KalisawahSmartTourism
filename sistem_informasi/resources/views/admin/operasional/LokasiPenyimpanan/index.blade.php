@extends('layouts.admin')

@section('title', 'Lokasi Penyimpanan')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gray-800 mb-1">Lokasi Penyimpanan</h2>
            <p class="text-muted small mb-0">Kelola daftar lokasi atau tempat penyimpanan aset inventaris</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Lokasi Baru
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 10%">No.</th>
                            <th class="py-3" style="width: 70%">Nama Lokasi</th>
                            <th class="py-3 text-center" style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lokasi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->nama_lokasi }}</span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id_lokasi }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDelete('{{ $item->id_lokasi }}', '{{ $item->nama_lokasi }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id_lokasi }}" action="{{ route('admin.lokasi-penyimpanan.destroy', $item->id_lokasi) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $item->id_lokasi }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Lokasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.lokasi-penyimpanan.update', $item->id_lokasi) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nama Lokasi</label>
                                                <input type="text" name="nama_lokasi" class="form-control rounded-3" value="{{ $item->nama_lokasi }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 rounded-3">Update Lokasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data lokasi penyimpanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Input Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.lokasi-penyimpanan.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control rounded-3" placeholder="Contoh: Gudang A, Gedung Utama" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Hanya menyisakan fungsi konfirmasi hapus (Sama seperti Paket Wisata)
    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Lokasi Penyimpanan?',
            text: `Apakah Anda yakin ingin menghapus "${nama}"? Data yang dihapus tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 border-0 shadow-sm',
                confirmButton: 'btn btn-danger px-4 py-2 rounded-3',
                cancelButton: 'btn btn-light px-4 py-2 rounded-3'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form rahasia berdasarkan ID lokasi
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
