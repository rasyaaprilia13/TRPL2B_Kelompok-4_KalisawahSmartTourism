@extends('layouts.admin')

@section('title', 'Daftar Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Fasilitas</h2>
            <p class="text-muted mb-0">Kelola item fasilitas tambahan, harga sewa, dan kuantitas stok</p>
        </div>
        <button class="btn btn-primary px-4 py-2 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Fasilitas
        </button>
    </div>

    {{-- Alert Global (hanya untuk sukses) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelFasilitas">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Gambar</th>
                            <th>Nama Fasilitas</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $item->gambar ? asset('storage/' . $item->gambar) : asset('img/default.png') }}"
                                     width="50" height="50" class="rounded-3" style="object-fit: cover;">
                            </td>
                            <td><span class="fw-bold text-dark">{{ $item->nama_fasilitas }}</span></td>
                            <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $item->harga ? 'Rp ' . number_format($item->harga, 0, ',', '.') : '-' }}</td>
                            <td>{{ $item->stok ?? 0 }}</td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($item->tipe_fasilitas) }}</span></td>
                            <td>
                                <span class="badge {{ $item->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDelete('{{ $item->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.fasilitas.destroy', $item->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow rounded-4">
                                    <div class="modal-header border-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Fasilitas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.fasilitas.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="row">
                                                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Nama</label><input type="text" name="nama_fasilitas" class="form-control rounded-3" value="{{ $item->nama_fasilitas }}" required></div>
                                                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Kategori</label>
                                                    <select name="kategori_fasilitas_id" class="form-select rounded-3">
                                                        @foreach(\App\Models\KategoriFasilitas::all() as $kat)
                                                            <option value="{{ $kat->id }}" {{ $item->kategori_fasilitas_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 mb-3"><label class="form-label fw-bold">Harga</label><input type="number" name="harga" class="form-control rounded-3" value="{{ $item->harga }}"></div>
                                                <div class="col-md-3 mb-3"><label class="form-label fw-bold">Stok</label><input type="number" name="stok" class="form-control rounded-3" value="{{ $item->stok }}"></div>
                                                <div class="col-md-3 mb-3"><label class="form-label fw-bold">Tipe</label>
                                                    <select name="tipe_fasilitas" class="form-select rounded-3">
                                                        @foreach(['informasi', 'paket', 'sewa'] as $tipe)
                                                            <option value="{{ $tipe }}" {{ $item->tipe_fasilitas == $tipe ? 'selected' : '' }}>{{ ucfirst($tipe) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-3"><label class="form-label fw-bold">Status</label>
                                                    <select name="status" class="form-select rounded-3">
                                                        <option value="aktif" {{ $item->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                        <option value="nonaktif" {{ $item->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Gambar (JPG/PNG, Max 2MB)</label>
                                                <input type="file" name="gambar" class="form-control rounded-3 @error('gambar') is-invalid @enderror" accept=".jpg,.jpeg,.png" onchange="validateFile(this)">
                                                @error('gambar')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3"><label class="form-label fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control rounded-3">{{ $item->deskripsi }}</textarea></div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4"><button type="submit" class="btn btn-primary px-4 rounded-3">Update Fasilitas</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold">Nama Fasilitas</label><input type="text" name="nama_fasilitas" class="form-control rounded-3" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label fw-bold">Kategori</label>
                            <select name="kategori_fasilitas_id" class="form-select rounded-3">
                                <option value="">Pilih Kategori</option>
                                @foreach(\App\Models\KategoriFasilitas::all() as $kat)<option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3"><label class="form-label fw-bold">Harga</label><input type="number" name="harga" class="form-control rounded-3"></div>
                        <div class="col-md-3 mb-3"><label class="form-label fw-bold">Stok</label><input type="number" name="stok" class="form-control rounded-3"></div>
                        <div class="col-md-3 mb-3"><label class="form-label fw-bold">Tipe</label>
                            <select name="tipe_fasilitas" class="form-select rounded-3"><option value="informasi">Informasi</option><option value="paket">Paket</option><option value="sewa">Sewa</option></select>
                        </div>
                        <div class="col-md-3 mb-3"><label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select rounded-3"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar (JPG/PNG, Max 2MB)</label>
                        <input type="file" name="gambar" class="form-control rounded-3 @error('gambar') is-invalid @enderror" accept=".jpg,.jpeg,.png" onchange="validateFile(this)">
                        @error('gambar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3"><label class="form-label fw-bold">Deskripsi</label><textarea name="deskripsi" class="form-control rounded-3" rows="2"></textarea></div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4"><button type="submit" class="btn btn-primary px-4 rounded-3">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    function validateFile(input) {
        const file = input.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        // Reset error styling
        input.classList.remove('is-invalid');
        
        if (file) {
            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file salah! Hanya JPG atau PNG yang diperbolehkan.');
                input.value = '';
                return false;
            }
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar! Maksimal 2MB.');
                input.value = '';
                return false;
            }
        }
    }
</script>
@endsection