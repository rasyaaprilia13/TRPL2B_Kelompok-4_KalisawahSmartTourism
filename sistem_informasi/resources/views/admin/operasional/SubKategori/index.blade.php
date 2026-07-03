@extends('layouts.admin')

@section('title', 'Subkategori Inventaris')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gray-800 mb-1">Subkategori Inventaris</h2>
            <p class="text-muted small mb-0">Kelola master data subkategori berelasi dengan kategori induk</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Subkategori Baru
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
                <table class="table table-hover align-middle" id="tableSubkategori">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 10%">No.</th>
                            <th class="py-3" style="width: 40%">Nama Subkategori</th>
                            <th class="py-3" style="width: 30%">Kategori Induk</th>
                            <th class="py-3 text-center" style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subkategori as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $item->nama_subkategori }}</span>
                            </td>
                            <td>
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3">
                                    {{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id_subkategori }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDeleteSubkategori('{{ $item->id_subkategori }}', '{{ $item->nama_subkategori }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $item->id_subkategori }}" action="{{ route('admin.subkategori-inventaris.destroy', $item->id_subkategori) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit{{ $item->id_subkategori }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Subkategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.subkategori-inventaris.update', $item->id_subkategori) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Kategori Induk</label>
                                                <select name="id_kategori" class="form-select rounded-3" required>
                                                    @foreach($kategori as $kat)
                                                        <option value="{{ $kat->id_kategori }}" {{ $item->id_kategori == $kat->id_kategori ? 'selected' : '' }}>
                                                            {{ $kat->nama_kategori }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nama Subkategori</label>
                                                <input type="text" name="nama_subkategori" class="form-control rounded-3" value="{{ $item->nama_subkategori }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 rounded-3">Update Subkategori</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada subkategori inventaris tersedia.</td>
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
                <h5 class="modal-title fw-bold">Input Subkategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.subkategori-inventaris.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Kategori Induk</label>
                        <select name="id_kategori" class="form-select rounded-3" required>
                            <option value="">Pilih Kategori Induk</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Subkategori</label>
                        <input type="text" name="nama_subkategori" class="form-control rounded-3" placeholder="Contoh: Peralatan Elektronik, ATK Sekolah" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Subkategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    /**
     * KONFIRMASI HAPUS DATA
     */
    function confirmDeleteSubkategori(id, namaSubkategori) {
        Swal.fire({
            title: 'Hapus Subkategori?',
            text: `Apakah Anda yakin ingin menghapus subkategori "${namaSubkategori}"?`,
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
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
