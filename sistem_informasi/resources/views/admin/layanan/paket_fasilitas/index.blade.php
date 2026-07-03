@extends('layouts.admin')

@section('title', 'Hubungan Paket & Fasilitas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kelola Paket Fasilitas</h2>
            <p class="text-muted mb-0">Atur pembagian dan distribusi item fasilitas ke dalam tiap Paket Wisata</p>
        </div>
        <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i> Hubungkan Fasilitas
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Data di-query langsung di sini karena controller tidak mengirim data --}}
    @php
        $data = \App\Models\PaketFasilitas::with(['paketWisata', 'fasilitas'])->latest()->get();
    @endphp

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3" style="width: 5%">No.</th>
                            <th class="py-3" style="width: 30%">Nama Paket Wisata</th>
                            <th class="py-3" style="width: 25%">Fasilitas Terhubung</th>
                            <th class="py-3" style="width: 10%">Jumlah</th>
                            <th class="py-3" style="width: 20%">Keterangan</th>
                            <th class="py-3 text-center" style="width: 10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="fw-bold text-dark">{{ $item->paketWisata->nama_paket ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1 rounded-2">
                                    <i class="fas fa-box me-1"></i> {{ $item->fasilitas->nama_fasilitas ?? '-' }}
                                </span>
                            </td>
                            <td><span class="fw-bold text-secondary">{{ $item->jumlah }} Pcs</span></td>
                            <td><small class="text-muted">{{ $item->keterangan ?? '-' }}</small></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning btn-sm text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rounded-3" onclick="confirmDelete('{{ $item->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.paket-fasilitas.destroy', $item->id) }}" method="POST" style="display: none;">
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
                                        <h5 class="modal-title fw-bold">Edit Hubungan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.paket-fasilitas.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Pilih Paket Wisata</label>
                                                    <select name="paket_wisata_id" class="form-select rounded-3" required>
                                                        @foreach(\App\Models\PaketWisata::all() as $pw)
                                                            <option value="{{ $pw->id }}" {{ $item->paket_wisata_id == $pw->id ? 'selected' : '' }}>{{ $pw->nama_paket }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Pilih Item Fasilitas</label>
                                                    <select name="fasilitas_id" class="form-select rounded-3" required>
                                                        @foreach(\App\Models\Fasilitas::where('tipe_fasilitas', 'paket')->get() as $fasil)
                                                            <option value="{{ $fasil->id }}" {{ $item->fasilitas_id == $fasil->id ? 'selected' : '' }}>{{ $fasil->nama_fasilitas }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4"><label class="form-label fw-bold">Jumlah</label><input type="number" name="jumlah" class="form-control rounded-3" value="{{ $item->jumlah }}" min="1" required></div>
                                                <div class="col-md-8"><label class="form-label fw-bold">Keterangan</label><input type="text" name="keterangan" class="form-control rounded-3" value="{{ $item->keterangan }}"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4"><button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Perubahan</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data.</td></tr>
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
            <div class="modal-header border-0 pt-4 px-4"><h5 class="modal-title fw-bold">Hubungkan Fasilitas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('admin.paket-fasilitas.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Paket Wisata</label>
                            <select name="paket_wisata_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Paket --</option>
                                @foreach(\App\Models\PaketWisata::all() as $pw)<option value="{{ $pw->id }}">{{ $pw->nama_paket }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Item Fasilitas</label>
                            <select name="fasilitas_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Fasilitas --</option>
                                @foreach(\App\Models\Fasilitas::where('tipe_fasilitas', 'paket')->get() as $fasil)<option value="{{ $fasil->id }}">{{ $fasil->nama_fasilitas }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label fw-bold">Jumlah</label><input type="number" name="jumlah" class="form-control rounded-3" min="1" required></div>
                        <div class="col-md-8"><label class="form-label fw-bold">Keterangan</label><input type="text" name="keterangan" class="form-control rounded-3"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4"><button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Data</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({ title: 'Hapus data?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!' })
        .then((result) => { if (result.isConfirmed) { document.getElementById('delete-form-' + id).submit(); } })
    }
</script>
@endpush
