@extends('layouts.admin')

@section('title', 'Inventaris Per Unit')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gray-800 mb-1">
                Inventaris Per Unit
            </h2>
            <p class="text-muted small mb-0">
                Kelola dan lacak aset/barang inventaris secara detail per satuan unit
            </p>
        </div>

        <button type="button"
            class="btn btn-primary px-4 py-2 rounded-3 shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i>
            Register Unit Baru
        </button>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
            <div class="fw-bold mb-1">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Gagal Menyimpan Data:
            </div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Pembelian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaris as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->kode_barang }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    {{ $item->jenisInventaris->nama_barang ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $item->jenisInventaris->subkategori->nama_subkategori ?? '-' }}
                                </small>
                            </td>
                            <td>
                                {{ $item->lokasi->nama_lokasi ?? '-' }}
                            </td>
                            <td>
                                @if($item->kondisi_unit == 'Baik' || $item->kondisi_unit == 'Bagus')
                                    <span class="badge bg-success">Baik</span>
                                @elseif($item->kondisi_unit == 'Rusak Ringan')
                                    <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                @else
                                    <span class="badge bg-danger">Rusak Berat</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    Rp {{ number_format($item->harga_beli,0,',','.') }}
                                </div>
                                <small class="text-muted">
                                    {{ $item->tanggal_beli ? date('d M Y', strtotime($item->tanggal_beli)) : '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- EDIT BUTTON --}}
                                    <button type="button"
                                        class="btn btn-warning btn-sm text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- DELETE BUTTON --}}
                                    <button type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="confirmDeleteUnit('{{ $item->id }}', '{{ $item->kode_barang }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    {{-- FORM DELETE --}}
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('admin.inventaris-perunit.destroy', $item->id) }}"
                                        method="POST"
                                        style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data inventaris
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Tambah Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.inventaris-perunit.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Inventaris</label>
                            <select name="id_jenis_inventaris" class="form-select" required>
                                <option value="">Pilih</option>
                                @foreach($jenisInventaris as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <select name="id_lokasi" class="form-select" required>
                                <option value="">Pilih</option>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi_unit" class="form-select" required>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sumber Pembelian</label>
                            <input type="text" name="sumber_pembelian" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
@foreach($inventaris as $item)
<div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.inventaris-perunit.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Inventaris</label>
                            <select name="id_jenis_inventaris" class="form-select" required>
                                @foreach($jenisInventaris as $jenis)
                                    <option value="{{ $jenis->id }}" {{ $item->id_jenis_inventaris == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <select name="id_lokasi" class="form-select" required>
                                @foreach($lokasi as $lok)
                                    <option value="{{ $lok->id_lokasi }}" {{ $item->id_lokasi == $lok->id_lokasi ? 'selected' : '' }}>
                                        {{ $lok->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" value="{{ $item->kode_barang }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi_unit" class="form-select" required>
                                <option value="Baik" {{ $item->kondisi_unit == 'Baik' || $item->kondisi_unit == 'Bagus' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ $item->kondisi_unit == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat" {{ $item->kondisi_unit == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" class="form-control" value="{{ $item->tanggal_beli ? date('Y-m-d', strtotime($item->tanggal_beli)) : '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control" value="{{ $item->harga_beli }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Sumber Pembelian</label>
                            <input type="text" name="sumber_pembelian" class="form-control" value="{{ $item->sumber_pembelian }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

{{-- JAVASCRIPT SEKARANG BERDIRI SENDIRI DI LUAR SECTION UNTUK MENGHINDARI LAYOUT CRASH --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteUnit(id, kodeBarang) {
    // 1. Cek apakah lib SweetAlert aktif
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Data?',
            text: `Yakin ingin menghapus "${kodeBarang}" ?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                executeSubmitForm(id);
            }
        });
    } else {
        // 2. Jika SweetAlert gagal dimuat karena template/internet, gunakan alert bawaan browser
        if (confirm(`Yakin ingin menghapus "${kodeBarang}" ?`)) {
            executeSubmitForm(id);
        }
    }
}

function executeSubmitForm(id) {
    var form = document.getElementById('delete-form-' + id);
    if (form) {
        form.submit();
    } else {
        alert('Error: Elemen Form Hapus dengan ID ' + id + ' tidak ditemukan di halaman.');
    }
}
</script>
