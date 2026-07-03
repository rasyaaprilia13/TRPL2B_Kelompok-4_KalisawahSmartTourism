@extends('layouts.admin')

@section('title', 'Jenis Inventaris')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gray-800 mb-1">Jenis Inventaris</h2>
            <p class="text-muted small mb-0">Kelola master data jenis inventaris barang beserta spesifikasinya</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Jenis Baru
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
                <table class="table table-hover align-middle" id="tableJenisInventaris">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 5%">No.</th>
                            <th class="py-3" style="width: 25%">Nama Barang</th>
                            <th class="py-3" style="width: 20%">Subkategori</th>
                            <th class="py-3" style="width: 35%">Spesifikasi</th>
                            <th class="py-3 text-center" style="width: 15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisInventaris as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $item->nama_barang }}</span>
                            </td>
                            <td>
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3">
                                    {{ $item->subkategori->nama_subkategori ?? 'Tanpa Subkategori' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                    {{ $item->spesifikasi ?? '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id_jenis_inventaris }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDeleteJenis('{{ $item->id_jenis_inventaris }}', '{{ $item->nama_barang }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- PERBAIKAN: Menggunakan objek $item langsung agar menghindari kesalahan parameter {jenis_inventari} -->
                                    <form id="delete-form-{{ $item->id_jenis_inventaris }}" action="{{ route('admin.jenis-inventaris.destroy', $item) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="modalEdit{{ $item->id_jenis_inventaris }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Jenis Inventaris</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- PERBAIKAN: Menggunakan objek $item langsung pada form action update -->
                                    <form action="{{ route('admin.jenis-inventaris.update', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Subkategori</label>
                                                <select name="id_subkategori" class="form-select rounded-3" required>
                                                    @foreach($subkategori as $sub)
                                                        <option value="{{ $sub->id_subkategori }}" {{ $item->id_subkategori == $sub->id_subkategori ? 'selected' : '' }}>
                                                            {{ $sub->nama_subkategori }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control rounded-3" value="{{ $item->nama_barang }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">Spesifikasi</label>
                                                <textarea name="spesifikasi" class="form-control rounded-3" rows="3">{{ $item->spesifikasi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 rounded-3">Update Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data jenis inventaris tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Input Jenis Inventaris Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.jenis-inventaris.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Subkategori</label>
                        <select name="id_subkategori" class="form-select rounded-3" required>
                            <option value="">Pilih Subkategori</option>
                            @foreach($subkategori as $sub)
                                <option value="{{ $sub->id_subkategori }}">{{ $sub->nama_subkategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control rounded-3" placeholder="Contoh: Laptop ASUS Core i7, Kursi Kerja Hidrolik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Spesifikasi</label>
                        <textarea name="spesifikasi" class="form-control rounded-3" rows="3" placeholder="Contoh: RAM 16GB, SSD 512GB, Warna Hitam"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Data</button>
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
     * KONFIRMASI HAPUS DATA JENIS INVENTARIS
     */
    function confirmDeleteJenis(id, namaBarang) {
        Swal.fire({
            title: 'Hapus Jenis Inventaris?',
            text: `Apakah Anda yakin ingin menghapus barang "${namaBarang}"?`,
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
