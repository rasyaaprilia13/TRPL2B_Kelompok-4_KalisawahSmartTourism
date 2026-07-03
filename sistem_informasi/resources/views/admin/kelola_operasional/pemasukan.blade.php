@extends('layouts.admin')

@section('title', 'Data Pemasukan')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Data Pemasukan</h2>
            <p class="text-muted mb-0">Kelola catatan pemasukan dari booking & pembayaran</p>
        </div>

        <div class="d-flex gap-2">
            <div class="dropdown">
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3" aria-labelledby="dropdownExport">
                    <li><a class="dropdown-menu-item dropdown-item py-2" href="#"><i class="fas fa-file-excel text-success me-2"></i> Export Excel</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ERROR ALERT --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDATION ALERT --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4">
            <div class="fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i> Validasi Gagal!
            </div>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff; position: relative; overflow: hidden;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                     style="width: 56px; height: 56px; background-color: #e8f5e9;">
                    <i class="fas fa-wallet fa-lg" style="color: #2e7d32;"></i>
                </div>
                <div>
                    <span class="text-secondary fw-semibold small d-block mb-1">Total Keseluruhan Bulan Ini</span>
                    <h2 class="fw-bold text-success mb-1">
                        Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                    </h2>
                    <span class="text-muted text-xs shadow-none">Periode: 1 - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <div class="pe-4 opacity-50 d-none d-md-block">
                <svg width="220" height="60" viewBox="0 0 220 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 45C30 50 45 15 75 35C105 55 120 5 150 20C180 35 195 2 220 5" stroke="#2e7d32" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="220" cy="5" r="4" fill="#2e7d32"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            {{-- REVISI 1: BUNGKUS DENGAN FORM UNTUK MEMFUNGSIKAN FILTER --}}
            <form action="{{ url()->current() }}" method="GET">
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold">Tanggal</label>
                        <div class="input-group">
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}" onchange="this.form.submit()">
                            <span class="input-group-text bg-light text-muted">s/d</span>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">Metode</label>
                        <select name="metode" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Metode</option>
                            <option value="transfer" {{ request('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="cash" {{ request('metode') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="qris" {{ request('metode') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kode booking atau nominal..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Booking</th>
                            <th>Nominal</th>
                            <th>Jenis</th>
                            <th>Metode</th>
                            <th>Tanggal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pemasukan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->kode_pemasukan }}</td>
                            <td>{{ $item->booking->kode_booking ?? '-' }}</td>
                            <td class="text-success fw-semibold">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge bg-info text-dark text-xs px-2.5 py-1.5 rounded-2">
                                    {{ strtoupper($item->jenis_transaksi) }}
                                </span>
                            </td>
                            <td>{{ ucfirst($item->metode_pemasukan) }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm rounded-2" onclick="confirmDelete('{{ $item->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.pemasukan.destroy', $item->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open d-block mb-2 fa-2x opacity-50"></i>
                                Belum ada data pemasukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
