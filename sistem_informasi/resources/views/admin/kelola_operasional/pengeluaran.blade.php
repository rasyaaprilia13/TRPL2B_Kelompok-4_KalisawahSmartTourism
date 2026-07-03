@extends('layouts.admin')

@section('title', 'Data Pengeluaran')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Pengeluaran Operasional</h2>
            <p class="text-muted mb-0">Kelola catatan biaya operasional harian</p>
        </div>

        <div class="d-flex gap-2">
            <div class="dropdown">

            </div>

            <button class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i> Tambah Pengeluaran
            </button>
        </div>
    </div>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert Gagal Temukan Data --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Box Rangkuman Error Validasi Form --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <div class="fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Validasi Gagal! Periksa kembali inputan Anda:</div>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff; overflow: hidden;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 56px; height: 56px; background-color: #e8f8f5;">
                    <i class="fas fa-wallet fa-lg" style="color: #1abc9c;"></i>
                </div>
                <div>
                    <span class="text-secondary fw-semibold small d-block mb-1">Total Keseluruhan Bulan Ini</span>
                    <h2 class="fw-bold text-success mb-1" style="color: #2ecc71 !important;">
                        Rp {{ number_format($data->sum('jumlah_uang') ?? 0, 0, ',', '.') }}
                    </h2>
                    <span class="text-muted small">Periode: 1 - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="pe-4 opacity-50 d-none d-md-block">
                <svg width="220" height="60" viewBox="0 0 220 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 45C30 50 45 15 75 35C105 55 120 5 150 20C180 35 195 2 220 5" stroke="#2ecc71" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="220" cy="5" r="4" fill="#2ecc71"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted small fw-bold">Tanggal</label>
                    <div class="input-group">
                        <input type="date" class="form-control" placeholder="Pilih tanggal">
                        <span class="input-group-text bg-light text-muted">s/d</span>
                        <input type="date" class="form-control" placeholder="Pilih tanggal">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold">Kategori</label>
                    <select class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari keterangan atau kategori...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Tanggal Pengeluaran</th>
                            <th>Dicatat Oleh</th>
                            <th>Bukti</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-light text-primary px-2.5 py-1.5 rounded-2 border text-capitalize">
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="text-danger fw-semibold">Rp {{ number_format($item->jumlah_uang, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pengeluaran)->format('d M Y') }}</td>
                            <td><span class="text-muted">{{ $item->dicatat_oleh ?? 'Admin' }}</span></td>
                            <td>
                                @if($item->bukti_pengeluaran)
                                    <a href="{{ asset('storage/' . $item->bukti_pengeluaran) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-warning btn-sm text-white rounded-2" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-2" onclick="confirmDelete('{{ $item->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.pengeluaran.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <form action="{{ route('admin.pengeluaran.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Edit Pengeluaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Kategori</label>
                                                <select name="id_kategori" class="form-select rounded-3 @error('id_kategori') is-invalid @enderror" required>
                                                    @foreach($kategori as $k)
                                                        <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                                            {{ $k->nama_kategori }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Keterangan</label>
                                                <input type="text" name="keterangan" class="form-control rounded-3 @error('keterangan') is-invalid @enderror" value="{{ old('keterangan', $item->keterangan) }}" required>
                                                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Jumlah Uang (Rp)</label>
                                                <input type="number" name="jumlah_uang" class="form-control rounded-3 @error('jumlah_uang') is-invalid @enderror" value="{{ old('jumlah_uang', $item->jumlah_uang) }}" required>
                                                @error('jumlah_uang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Tanggal</label>
                                                <input type="date" name="tanggal_pengeluaran" class="form-control rounded-3 @error('tanggal_pengeluaran') is-invalid @enderror" value="{{ old('tanggal_pengeluaran', $item->tanggal_pengeluaran) }}" required>
                                                @error('tanggal_pengeluaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Dicatat Oleh</label>
                                                <input type="text" name="dicatat_oleh" class="form-control rounded-3 @error('dicatat_oleh') is-invalid @enderror" value="{{ old('dicatat_oleh', $item->dicatat_oleh) }}" required>
                                                @error('dicatat_oleh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-muted small">Bukti Baru (Opsional)</label>
                                                @if($item->bukti_pengeluaran)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $item->bukti_pengeluaran) }}" class="img-thumbnail" width="100">
                                                    </div>
                                                @endif
                                                <input type="file" name="bukti_pengeluaran" class="form-control rounded-3 @error('bukti_pengeluaran') is-invalid @enderror" accept="image/*">
                                                <div class="form-text">Format: JPG, JPEG, PNG (Maks. 2MB)</div>
                                                @error('bukti_pengeluaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-3">Update Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open d-block mb-2 fa-2x opacity-50"></i>
                                Belum ada data pengeluaran.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Pengeluaran Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Kategori</label>
                        <select name="id_kategori" class="form-select rounded-3 @error('id_kategori') is-invalid @enderror" required>
                            <option value="" disabled {{ old('id_kategori') == '' ? 'selected' : '' }}>-- Pilih Kategori --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id }}" {{ old('id_kategori') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('id_kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control rounded-3 @error('keterangan') is-invalid @enderror" placeholder="Contoh: Pembelian ATK Kantor" value="{{ old('keterangan') }}" required>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Jumlah Uang (Rp)</label>
                        <input type="number" name="jumlah_uang" class="form-control rounded-3 @error('jumlah_uang') is-invalid @enderror" placeholder="Contoh: 150000" min="1" value="{{ old('jumlah_uang') }}" required>
                        @error('jumlah_uang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Tanggal</label>
                        <input type="date" name="tanggal_pengeluaran" class="form-control rounded-3 @error('tanggal_pengeluaran') is-invalid @enderror" value="{{ old('tanggal_pengeluaran', date('Y-m-d')) }}" required>
                        @error('tanggal_pengeluaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Dicatat Oleh</label>
                        <input type="text" name="dicatat_oleh" class="form-control rounded-3 @error('dicatat_oleh') is-invalid @enderror" placeholder="Nama Admin" value="{{ old('dicatat_oleh') }}" required>
                        @error('dicatat_oleh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">Bukti Pengeluaran (Opsional)</label>
                        <input type="file" name="bukti_pengeluaran" class="form-control rounded-3 @error('bukti_pengeluaran') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Format: JPG, JPEG, PNG (Maks. 2MB)</div>
                        @error('bukti_pengeluaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if($errors->any())
            @if(session('last_action') === 'store')
                var modalTambah = new bootstrap.Modal(document.getElementById('modalTambah'));
                modalTambah.show();
            @elseif(session('last_action') === 'update' && session('action_id'))
                var modalEdit = new bootstrap.Modal(document.getElementById('modalEdit' + '{{ session("action_id") }}'));
                modalEdit.show();
            @endif
        @endif
    });

    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data akan dihapus permanen!",
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
