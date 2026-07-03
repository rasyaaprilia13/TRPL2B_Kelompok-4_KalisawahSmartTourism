@extends('layouts.admin')

@section('title', 'Daftar Paket Wisata')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-gray-800">Paket Wisata</h2>
        <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Tambah Paket Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-8">
                    <input type="text" id="searchInput" class="form-control bg-light border-0" placeholder="🔍 Cari nama paket..." onkeyup="filterData()">
                </div>
                <div class="col-md-4">
                    <select id="kategoriFilter" class="form-select bg-light border-0" onchange="filterData()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Notifikasi Error Umum / Peringatan Validasi --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Gagal Menyimpan Data. Silakan cek form kembali:</div>
            <ul class="mb-0 mt-1 sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="paketTable">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Paket</th>
                            <th>Kategori</th>
                            <th>Durasi</th>
                            <th>Harga</th>
                            <th>Kapasitas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->nama_paket }}</span></td>
                            <td class="kategori-col">{{ $item->kategori->nama_kategori ?? 'Umum' }}</td>
                            <td>{{ $item->durasi }}</td>
                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>{{ $item->kapasitas }} Orang</td>
                            <td>
                                <span class="badge {{ $item->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}"><i class="fas fa-edit"></i></button>

                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.paket-wisata.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.paket-wisata.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 p-4">
                                            <h5 class="modal-title fw-bold">Edit Paket Wisata</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4 pb-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nama Paket</label>
                                                    <input type="text" name="nama_paket" class="form-control @error('nama_paket') is-invalid @enderror" value="{{ old('nama_paket', $item->nama_paket) }}" required>
                                                    @error('nama_paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Kategori</label>
                                                    <select name="kategori_paket_id" class="form-select @error('kategori_paket_id') is-invalid @enderror" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ old('kategori_paket_id', $item->kategori_paket_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('kategori_paket_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">Harga (Rp)</label>
                                                    <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $item->harga) }}" required>
                                                    @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">Durasi</label>
                                                    <input type="text" name="durasi" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi', $item->durasi) }}" required>
                                                    @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold">Kapasitas</label>
                                                    <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ old('kapasitas', $item->kapasitas) }}" required>
                                                    @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Deskripsi</label>
                                                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" required>{{ old('deskripsi', $item->deskripsi) }}</textarea>
                                                    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                        <option value="Aktif" {{ old('status', $item->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                        <option value="Nonaktif" {{ old('status', $item->status) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                    </select>
                                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Gambar Paket</label>
                                                    @if($item->gambar)
                                                        <div class="mb-2"><img src="{{ asset('storage/' . $item->gambar) }}" alt="gambar" width="100" class="img-thumbnail"></div>
                                                    @endif
                                                    <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                                                    @error('gambar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 p-4">
                                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="8" class="text-center">Belum ada data.</td></tr>
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
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.paket-wisata.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title fw-bold">Tambah Paket Wisata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Paket</label>
                            <input type="text" name="nama_paket" class="form-control @error('nama_paket') is-invalid @enderror" value="{{ old('nama_paket') }}" required>
                            @error('nama_paket') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori_paket_id" class="form-select @error('kategori_paket_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('kategori_paket_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('kategori_paket_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" required>
                            @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Durasi</label>
                            <input type="text" name="durasi" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi') }}" required>
                            @error('durasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kapasitas</label>
                            <input type="number" name="kapasitas" class="form-control @error('kapasitas') is-invalid @enderror" value="{{ old('kapasitas') }}" required>
                            @error('kapasitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3" required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Gambar Paket</label>
                            <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*" required>
                            @error('gambar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Script otomatis membuka modal kembali jika terdapat error validasi
    document.addEventListener("DOMContentLoaded", function () {
        @if($errors->any())
            // Cari tahu modal mana yang harus dibuka berdasarkan request lama (old id)
            @if(old('_method') == 'PUT')
                // Ganti dengan logic pendeteksian target edit modal jika diperlukan, atau andalkan alert global di atas tabel
            @else
                var modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
                modalTambah.show();
            @endif
        @endif
    });

    function filterData() {
        let searchText = document.getElementById('searchInput').value.toLowerCase();
        let filterKategori = document.getElementById('kategoriFilter').value.toLowerCase();
        let table = document.getElementById('paketTable');
        let tr = table.getElementsByTagName('tr');
        for (let i = 1; i < tr.length; i++) {
            let tdNama = tr[i].getElementsByTagName('td')[1].textContent.toLowerCase();
            let tdKategori = tr[i].getElementsByTagName('td')[2].textContent.toLowerCase();
            tr[i].style.display = (tdNama.includes(searchText) && (filterKategori === "" || tdKategori === filterKategori)) ? "" : "none";
        }
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Paket?',
            text: "Data akan dihapus permanen!",
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
