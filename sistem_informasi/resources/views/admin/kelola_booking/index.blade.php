@extends('layouts.admin')

@section('content')
<style>
    .modal-content { background: #f8fafc; border-radius: 1.5rem; border: none; overflow: hidden; }
    .modal-header { background: white; padding: 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; }
    .modal-body { padding: 2rem; background: #f8fafc; }
    .modal-footer { background: white; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem; }

    .form-section {
        background: white; border-radius: 1rem; padding: 1.75rem;
        margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 10px; }

    .payment-section { background: #fff7ed; border: 1px solid #fed7aa; }
    .payment-section .section-title { color: #9a3412; }

    .form-label { font-weight: 600; color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .form-control, .form-select { background-color: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.6rem 1rem; font-size: 0.95rem; font-weight: 500; transition: all 0.2s; }
    .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); background-color: #fff; }

    .input-group-qty-modern { display: flex; align-items: center; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 3px; width: fit-content; }
    .btn-qty { width: 34px; height: 34px; border-radius: 0.5rem; background: white; border: 1px solid #cbd5e1; font-weight: bold; color: #334155; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
    .btn-qty:hover { background: #e2e8f0; }
    .qty-input-modern { border: none !important; background: transparent !important; text-align: center; font-weight: 700; width: 45px; color: #1e293b; }

    .day-tabs { display: flex; gap: 10px; margin-bottom: 1.5rem; flex-wrap: wrap; }
    .day-tab { padding: 10px 20px; background: #f1f5f9; border-radius: 30px; font-size: 0.85rem; font-weight: 700; color: #64748b; cursor: pointer; border: 1px solid #cbd5e1; transition: all 0.2s; }
    .day-tab:hover { background: #e2e8f0; }
    .day-tab.active { background: #2563eb; color: white; border-color: #2563eb; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }

    .itinerary-panel { display: none; }
    .itinerary-panel.active { display: block; }

    .item-card { background: #ffffff; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s; }
    .item-card:hover { border-color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1); transform: translateY(-2px); }

    .item-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; transition: all 0.2s; margin-top: 0.5rem; }

    .summary-card { padding: 1.5rem; border-radius: 1rem; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .card-blue { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .btn-save { background: #2563eb; color: white; font-weight: 700; padding: 1rem 2rem; border-radius: 0.75rem; border: none; font-size: 1rem; transition: all 0.2s; }
    .btn-save:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); }

    .empty-state-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #64748b; }

    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .nowrap-table th, .nowrap-table td { white-space: nowrap; vertical-align: middle; }
</style>

<div class="container-fluid px-4 py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="fas fa-shield-alt me-2"></i> <strong>Peringatan Keamanan / Validasi:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h2 class="fw-bold text-dark mb-1">Daftar Booking Wisata</h2></div>
        <button type="button" class="btn btn-primary px-4 py-2 rounded-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahBooking">
            <i class="fas fa-plus me-2"></i> Tambah Booking
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ url('/admin/booking-admin') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Filter Bulan Kunjungan</label>
                    <select name="bulan" class="form-select rounded-3">
                        <option value="">Tampilkan Semua Bulan</option>
                        @php
                            $months = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-uppercase fw-bold text-secondary mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Tahun</label>
                    <select name="tahun" class="form-select rounded-3">
                        @for($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold flex-grow-1 py-2"><i class="fas fa-filter me-1"></i> Terapkan</button>
                    @if(request('bulan') || request('tahun'))
                        <a href="{{ url('/admin/booking-admin') }}" class="btn btn-light border rounded-3 fw-bold py-2"><i class="fas fa-undo"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 nowrap-table" id="bookingTable">
                    <thead class="text-secondary small text-uppercase bg-light">
                        <tr>
                            <th class="ps-4 text-center" style="width: 60px;">No</th>
                            <th>Kode</th>
                            <th>Pemesan</th>
                            <th>Kunjungan</th>
                            <th class="text-center">Orang</th>
                            <th class="text-end">Harga Awal</th>
                            <th class="text-end">Diskon</th>
                            <th class="text-end">Harga Final</th>
                            <th class="text-center">Status Booking</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center pe-4" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $b)
                        @php
                            $hargaAwal = round($b->total_harga);
                            $diskon = round($b->diskon_manual);
                            $hargaFinal = round($b->total_harga_final);
                        @endphp
                        <tr>
                            <td class="ps-4 text-center fw-medium text-secondary">{{ method_exists($data, 'firstItem') ? $data->firstItem() + $key : $key + 1 }}</td>
                            <td><span class="badge bg-primary px-3 py-2 rounded-3 fw-bold">{{ $b->kode_booking }}</span></td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $b->nama_pemesan }}</div>
                                <small class="text-muted d-block"><i class="fab fa-whatsapp text-success me-1"></i>{{ $b->no_hp }}</small>
                            </td>
                            <td><div class="fw-bold text-secondary">{{ date('d M Y', strtotime($b->tanggal_kunjungan)) }}</div></td>
                            <td class="text-center"><span class="badge bg-light text-dark border px-2 py-2">{{ $b->jumlah_pengunjung }} Org</span></td>
                            <td class="text-end fw-bold text-secondary">Rp {{ number_format($hargaAwal,0,',','.') }}</td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($diskon,0,',','.') }}</td>
                            <td class="text-end fw-bold text-primary">Rp {{ number_format($hargaFinal,0,',','.') }}</td>
                            <td class="text-center"><span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-medium">{{ $b->status_booking }}</span></td>
                            <td class="text-center"><span class="badge bg-info text-white px-3 py-2 rounded-pill fw-medium">{{ $b->status_pembayaran ?? '-' }}</span></td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info text-white rounded-3 shadow-sm px-2.5 py-2" data-bs-toggle="modal" data-bs-target="#modalShowBooking{{ $b->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button type="button" class="btn btn-sm btn-warning text-dark rounded-3 shadow-sm px-2.5 py-2" data-bs-toggle="modal" data-bs-target="#modalEditBooking{{ $b->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                   <!-- Tombol Pembayaran -->
                                   <button type="button" class="btn btn-sm btn-success text-white rounded-3 shadow-sm px-2.5 py-2" data-bs-toggle="modal" data-bs-target="#modalPembayaran{{ $b->id }}">
                                        <i class="fas fa-wallet"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-center bg-white rounded-bottom-4">
                {{ method_exists($data, 'links') ? $data->links('pagination::bootstrap-5') : '' }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahBooking" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ url('/admin/booking-admin') }}" method="POST" id="formTambahBooking" class="w-100 h-100 d-flex flex-column" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="jumlah_tiket_tambahan" id="hidden_tiket_tambahan" value="0">
            <input type="hidden" name="harga_tiket_tambahan" value="25000">
            <input type="hidden" name="subtotal_tiket_tambahan" id="hidden_subtotal_tiket" value="0">
            <input type="hidden" name="total_harga" id="hidden_total_harga" value="0">
            <input type="hidden" name="total_harga_final" id="hidden_total_harga_final" value="0">

            <div class="modal-content shadow-lg h-100">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-calendar-plus me-2"></i> Reservasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body custom-scrollbar">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-8">
                            <div class="form-section">
                                <div class="section-title"><i class="fas fa-user-circle"></i> Data Pemesan</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_pemesan" class="form-control" required placeholder="Masukkan nama pemesan">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" name="no_hp" class="form-control" required placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Check-In</label>
                                        <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan" class="form-control" required onchange="renderItinerary()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Check-Out</label>
                                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required onchange="renderItinerary()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Jam Check-In</label>
                                        <input type="time" name="jam" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Total Pengunjung</label>
                                        <div class="input-group-qty-modern w-100">
                                            <button type="button" class="btn-qty w-25" onclick="changeQtyValue('jml_pengunjung', -1)">-</button>
                                            <input type="number" name="jumlah_pengunjung" id="jml_pengunjung" class="qty-input-modern w-50" value="1" min="1" onchange="calculateTotal()">
                                            <button type="button" class="btn-qty w-25" onclick="changeQtyValue('jml_pengunjung', 1)">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Catatan Tambahan</label>
                                        <input type="text" name="catatan" class="form-control" placeholder="Catatan...">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary">
                                        <i class="fas fa-moon me-1"></i> Durasi: <span id="label_jml_malam" class="fw-bold">0</span> Malam
                                    </span>
                                    <input type="hidden" name="jumlah_malam" id="jml_malam" value="0">
                                </div>
                            </div>

                            <div class="form-section mb-0">
                                <div class="section-title"><i class="fas fa-map-marked-alt"></i> Pemilihan Paket & Fasilitas (Per Hari)</div>
                                <div id="day-tabs-container" class="day-tabs"></div>
                                <div id="itinerary-panels-container">
                                    <div class="empty-state-box">
                                        <i class="fas fa-calendar-alt fs-2 mb-2 text-muted"></i>
                                        <p class="mb-0 fw-bold">Silakan pilih tanggal Check-In dan Check-Out terlebih dahulu.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="sticky-top">
                                <div class="form-section payment-section">
                                    <div class="section-title"><i class="fas fa-wallet"></i> Pembayaran Form</div>
                                    <div class="mb-3">
                                        <label class="form-label">Tipe Pembayaran</label>
                                        <select name="tipe_pembayaran" class="form-select" required>
                                            <option value="dp">DP (Down Payment)</option>
                                            <option value="lunas">Lunas</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Metode</label>
                                        <select name="metode_pembayaran" class="form-select" required>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="cash">Cash / Tunai</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nominal Bayar (Rp)</label>
                                        <input type="text" id="nominal_bayar_input" class="form-control fw-bold text-success" onkeyup="formatRupiahRealtime(this, 'nominal_bayar')" required>
                                        <input type="hidden" name="nominal" id="nominal_bayar" value="0">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Upload Bukti (Opsional)</label>
                                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control" accept=".jpg,.jpeg,.png">
                                        <div id="file_error_msg" class="text-danger fw-bold small mt-1 d-none">
                                            <i class="fas fa-times-circle me-1"></i> <span id="file_error_text"></span>
                                        </div>
                                    </div>

                                    <div class="mb-0 border-top pt-3">
                                        <label class="form-label text-danger">Diskon Manual (Rp)</label>
                                        <input type="text" id="diskon_manual_input" class="form-control" onkeyup="formatRupiahRealtime(this, 'diskon_manual')">
                                        <input type="hidden" name="diskon_manual" id="diskon_manual" value="0">
                                    </div>
                                </div>

                                <div class="summary-card card-blue shadow-sm mb-3">
                                    <small class="d-block mb-3 fw-bold text-uppercase"><i class="fas fa-receipt"></i> Rincian Harga</small>

                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span class="text-secondary">Subtotal Paket Wisata:</span>
                                        <span id="disp-sub-paket" class="fw-bold text-dark">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span class="text-secondary">Subtotal Fasilitas:</span>
                                        <span id="disp-sub-fasilitas" class="fw-bold text-dark">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span class="text-danger">Tiket Tambahan (25k):</span>
                                        <span id="disp-sub-tiket" class="fw-bold text-danger">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span class="text-primary">Diskon:</span>
                                        <span id="disp-diskon" class="fw-bold text-primary">- Rp 0</span>
                                    </div>

                                    <hr class="my-2 border-primary opacity-20">

                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="fw-bold text-dark">Total Akhir:</span>
                                        <span id="disp-total" class="fs-4 fw-bold text-success">Rp 0</span>
                                    </div>
                                </div>

                                <div class="summary-card bg-white border shadow-sm">
                                    <small class="d-block mb-1 fw-bold text-uppercase text-secondary"><i class="fas fa-users"></i> Kapasitas Menginap / Hari</small>
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span id="disp-kapasitas" class="fs-2 fw-bold text-dark">0</span>
                                        <span class="fw-bold text-secondary">Orang</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3 border me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-save" id="btn_submit_booking">
                        <i class="fas fa-check-circle me-2"></i> Konfirmasi & Simpan Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($data as $b)
<div class="modal fade" id="modalShowBooking{{ $b->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-light">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-info me-2"></i> Rincian Lengkap Data Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded-3 border h-100">
                            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Informasi Pemesan</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td width="40%" class="text-secondary">Nama Lengkap</td><td class="fw-bold">: {{ $b->nama_pemesan }}</td></tr>
                                <tr><td class="text-secondary">No WhatsApp</td><td class="fw-bold">: {{ $b->no_hp }}</td></tr>
                                <tr><td class="text-secondary">Tgl Kunjungan</td><td class="fw-bold">: {{ date('d M Y', strtotime($b->tanggal_kunjungan)) }}</td></tr>
                                <tr><td class="text-secondary">Jml Pengunjung</td><td class="fw-bold">: {{ $b->jumlah_pengunjung }} Orang</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded-3 border h-100">
                            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Status & Bukti</h6>
                            <p class="mb-2"><span class="text-secondary d-inline-block" style="width: 120px;">Status Booking</span>: <span class="badge bg-warning text-dark">{{ $b->status_booking }}</span></p>

                           {{-- 1. Definisikan variabel tepat sebelum digunakan --}}
                        @php
                            $pembayaranTerakhir = $b->pembayaran->last();
                        @endphp

                        {{-- 2. Baru gunakan variabel tersebut --}}
                        @if($pembayaranTerakhir && $pembayaranTerakhir->bukti_pembayaran)
                            <div class="mt-3">
                                <p class="fw-bold text-secondary mb-1">Bukti Pembayaran Terakhir:</p>
                                <a href="{{ asset('storage/' . $pembayaranTerakhir->bukti_pembayaran) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $pembayaranTerakhir->bukti_pembayaran) }}"
                                        class="img-fluid rounded border"
                                        style="max-height: 150px; object-fit: contain;"
                                        alt="Bukti Pembayaran">
                                </a>
                            </div>
                        @else
                            <p class="text-muted fst-italic mt-3">Belum ada bukti pembayaran.</p>
                        @endif
                    </div>
                </div>
                </div>

                <div class="bg-white p-3 rounded-3 border mb-4">
                    <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-list me-2"></i>Item Paket & Fasilitas Terpilih</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="bg-light small fw-bold text-secondary text-uppercase">
                                <tr>
                                    <th>Hari Ke-</th>
                                    <th>Tipe Item</th>
                                    <th>Nama Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($b->items as $item)
                                <tr>
                                    <td class="text-center">{{ $item->hari }}</td>
                                    <td><span class="badge bg-primary">Paket Wisata</span></td>
                                    <td>{{ $item->paketWisata->nama_paket ?? '-' }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format(round($item->subtotal),0,',','.') }}</td>
                                </tr>
                                @endforeach
                                @foreach($b->fasilitas as $fas)
                                <tr>
                                    <td class="text-center">{{ $fas->hari }}</td>
                                    <td><span class="badge bg-success">Fasilitas</span></td>
                                    <td>{{ $fas->fasilitas->nama_fasilitas ?? '-' }}</td>
                                    <td class="text-center">{{ $fas->qty }}</td>
                                    <td class="text-end fw-bold">Rp {{ number_format(round($fas->subtotal),0,',','.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditBooking{{ $b->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
       <form action="{{ url('/admin/booking-admin/' . $b->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content shadow-lg rounded-4 border-0">
                <div class="modal-header bg-light">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-edit text-warning me-2"></i> Edit Data Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="bg-white p-4 rounded-3 border shadow-sm h-100">
                                <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Informasi Dasar</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Pemesan</label>
                                    <input type="text" name="nama_pemesan" class="form-control" value="{{ $b->nama_pemesan }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No WhatsApp</label>
                                    <input type="text" name="no_hp" class="form-control" value="{{ $b->no_hp }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Pengunjung</label>
                                    <input type="number" name="jumlah_pengunjung" id="edit_pengunjung_{{ $b->id }}" class="form-control" value="{{ $b->jumlah_pengunjung }}" required oninput="calcEditTotal({{ $b->id }})">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status Booking</label>
                                    <select name="status_booking" class="form-select">
                                        <option value="pending"{{ $b->status_booking == 'pending' ? 'selected' : '' }}> Menunggu Konfirmasi</option>
                                        <option value="dikonfirmasi"{{ $b->status_booking == 'dikonfirmasi' ? 'selected' : '' }}>Terkonfirmasi</option>
                                        <option value="selesai"{{ $b->status_booking == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan"{{ $b->status_booking == 'dibatalkan' ? 'selected' : '' }}> Dibatalkan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Kunjungan Awal</label>
                                    <input type="date" class="form-control" value="{{ \Carbon\Carbon::parse($b->tanggal_kunjungan)->format('Y-m-d') }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Tanggal Reschedule</label>
                                    <input type="date" name="tanggal_reschedule" class="form-control" value="{{ $b->tanggal_reschedule }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jumlah Reschedule</label>
                                    <input type="number" name="jumlah_reschedule" class="form-control" value="{{ $b->jumlah_reschedule ?? 0 }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alasan Reschedule</label>
                                    <textarea name="alasan_reschedule" class="form-control" rows="3">{{ $b->alasan_reschedule }}</textarea>
                                </div>

                                <div class="mb-3 pt-3 border-top">
                                    <label class="form-label text-danger fw-bold">Diskon Manual (Rp)</label>
                                    <input type="text" id="edit_diskon_input_{{ $b->id }}" class="form-control" value="{{ number_format(round($b->diskon_manual), 0, ',', '.') }}" onkeyup="formatRupiahEdit(this, {{ $b->id }})">
                                    <input type="hidden" name="diskon_manual" id="edit_diskon_{{ $b->id }}" value="{{ round($b->diskon_manual) }}">
                                </div>

                                <div class="p-3 bg-light rounded-3 border mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small text-secondary fw-semibold">Harga Dasar Sistem:</span>
                                        <span class="fw-bold text-dark">Rp <span id="label_edit_dasar_{{ $b->id }}">{{ number_format(round($b->total_harga), 0, ',', '.') }}</span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-secondary fw-semibold">Harga Final Tagihan:</span>
                                        <span class="fw-bold text-success fs-5">Rp <span id="label_edit_final_{{ $b->id }}">{{ number_format(round($b->total_harga_final), 0, ',', '.') }}</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="bg-white p-4 rounded-3 border shadow-sm mb-4">
                                <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Manajemen Paket Wisata</h6>
                                <div id="edit-paket-container-{{ $b->id }}" class="mb-3">
                                @foreach($b->items as $item)
                                <div class="row g-2 align-items-center mb-2 edit-paket-row-{{ $b->id }} edit-paket-item-row pb-2 border-bottom border-light">
                                    <div class="col-md-4">
                                        <div class="fw-bold text-dark">{{ $item->paketWisata->nama_paket ?? 'Paket Terhapus' }} <span class="badge bg-secondary ms-1">Kap: {{ $item->paketWisata->kapasitas ?? 0 }}</span></div>
                                        <small class="text-success fw-bold">Rp {{ number_format(round($item->harga),0,',','.') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">Hari Ke-</span>
                                            <input type="number" name="paket[{{ $item->id }}][hari]" class="form-control" value="{{ $item->hari }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">Qty</span>
                                            <input type="number" name="paket[{{ $item->id }}][qty]" class="form-control edit-qty-paket" value="{{ $item->qty }}" min="1" oninput="calcEditTotal({{ $b->id }})">
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.edit-paket-row-{{ $b->id }}').remove(); calcEditTotal({{ $b->id }})"><i class="fas fa-trash"></i></button>

                                        <input type="hidden" name="paket[{{ $item->id }}][paket_id]" value="{{ $item->paket_wisata_id }}">
                                        <input type="hidden" class="edit-harga-paket" value="{{ round($item->harga) }}">
                                        <input type="hidden" class="edit-kapasitas-paket" value="{{ $item->paketWisata->kapasitas ?? 0 }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-bold text-primary mb-2 small"><i class="fas fa-plus-circle me-1"></i> Tambah Paket Baru</label>
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <select id="add_paket_sel_{{ $b->id }}" class="form-select form-select-sm border">
                                                <option value="">-- Pilih Paket --</option>
                                                @foreach($groupedPaket ?? [] as $kat => $paketItems)
                                                <optgroup label="{{ $kat }}">
                                                    @foreach($paketItems as $p)
                                                    <option value="{{ $p->id }}" data-nama="{{ htmlspecialchars($p->nama_paket) }}" data-harga="{{ $p->harga }}" data-kapasitas="{{ $p->kapasitas }}">{{ $p->nama_paket }}</option>
                                                    @endforeach
                                                </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Hari</span>
                                                <input type="number" id="add_paket_hari_{{ $b->id }}" class="form-control border" value="1" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" id="add_paket_qty_{{ $b->id }}" class="form-control form-control-sm border" placeholder="Qty" min="1" value="1">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-primary w-100" onclick="addEditPaket({{ $b->id }})">Tambah</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-3 border shadow-sm">
                                <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Manajemen Fasilitas Tambahan</h6>
                               <div id="edit-fas-container-{{ $b->id }}" class="mb-3">
                                @foreach($b->fasilitas as $fas)
                                <div class="row g-2 align-items-center mb-2 edit-fas-row-{{ $b->id }} edit-fas-item-row pb-2 border-bottom border-light">
                                    <div class="col-md-4">
                                        <div class="fw-bold text-dark">{{ $fas->fasilitas->nama_fasilitas ?? 'Fasilitas Terhapus' }}</div>
                                        <small class="text-success fw-bold">Rp {{ number_format(round($fas->harga),0,',','.') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">Hari Ke-</span>
                                            <input type="number" name="fasilitas[{{ $fas->id }}][hari]" class="form-control" value="{{ $fas->hari }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">Qty</span>
                                            <input type="number" name="fasilitas[{{ $fas->id }}][qty]" class="form-control edit-qty-fas" value="{{ $fas->qty }}" min="1" oninput="calcEditTotal({{ $b->id }})">
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.edit-fas-row-{{ $b->id }}').remove(); calcEditTotal({{ $b->id }})"><i class="fas fa-trash"></i></button>

                                        <input type="hidden" name="fasilitas[{{ $fas->id }}][fasilitas_id]" value="{{ $fas->fasilitas_id }}">
                                        <input type="hidden" class="edit-harga-fas" value="{{ round($fas->harga) }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-bold text-primary mb-2 small"><i class="fas fa-plus-circle me-1"></i> Tambah Fasilitas Baru</label>
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <select id="add_fas_sel_{{ $b->id }}" class="form-select form-select-sm border">
                                                <option value="">-- Pilih Fasilitas --</option>
                                                @foreach($groupedFasilitas ?? [] as $kat => $fasItems)
                                                <optgroup label="{{ $kat }}">
                                                    @foreach($fasItems as $f)
                                                    <option value="{{ $f->id }}" data-nama="{{ htmlspecialchars($f->nama_fasilitas) }}" data-harga="{{ $f->harga }}">{{ $f->nama_fasilitas }}</option>
                                                    @endforeach
                                                </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">Hari</span>
                                                <input type="number" id="add_fas_hari_{{ $b->id }}" class="form-control border" value="1" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" id="add_fas_qty_{{ $b->id }}" class="form-control form-control-sm border" placeholder="Qty" min="1" value="1">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-primary w-100" onclick="addEditFasilitas({{ $b->id }})">Tambah</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3 border-top d-flex justify-content-end">
                    <button type="button" class="btn btn-light fw-bold px-4 border me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fas fa-save me-2"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalPembayaran{{ $b->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="fas fa-wallet text-success me-2"></i>Detail & Verifikasi Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <div class="mb-4 bg-light p-3 rounded-3 border">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-secondary d-block">Kode Booking</small>
                            <strong class="text-primary">{{ $b->kode_booking }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-secondary d-block">Nama Pemesan</small>
                            <strong class="text-dark">{{ $b->nama_pemesan }}</strong>
                        </div>
                    </div>
                </div>

                @if($b->pembayaran && $b->pembayaran->count() > 0)
    @foreach($b->pembayaran as $p)

        <form id="formVerifikasiPembayaran{{ $p->id }}" action="{{ route('admin.pembayaran.verifikasi', $p->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-light fw-bold py-2 text-secondary d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                    <span>ID PEMBAYARAN: #{{ $p->id }}</span>
                    <span class="badge bg-secondary text-uppercase">{{ $p->status_verifikasi }}</span>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Tipe Pembayaran</label>
                            <input type="text" class="form-control bg-light text-uppercase fw-bold" value="{{ $p->tipe_pembayaran }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Metode</label>
                            <input type="text" class="form-control bg-light text-uppercase fw-bold" value="{{ $p->metode_pembayaran }}" readonly>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Nominal Rupiah</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" value="Rp {{ number_format($p->nominal, 0, ',', '.') }}" readonly>
                            {{-- <input type="hidden" name="nominal" value="{{ $p->nominal }}"> --}}
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Status Verifikasi</label>
                            <select name="status_verifikasi" class="form-select border-primary fw-bold">
                                <option value="pending" {{ $p->status_verifikasi == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="valid" {{ $p->status_verifikasi == 'valid' ? 'selected' : '' }}>✅ Valid / Diterima</option>
                                <option value="ditolak" {{ $p->status_verifikasi == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold text-secondary mb-1">Catatan Admin</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tambahkan catatan verifikasi di sini...">{{ $p->catatan }}</textarea>
                    </div>

                    @if($p->bukti_pembayaran)
                        <div class="mt-3 p-2 bg-light rounded border text-center">
                            <span class="small d-block text-secondary fw-semibold mb-2">Lampiran Bukti Transfer:</span>
                            <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-3 mb-2">
                                <i class="fas fa-search-plus me-1"></i> Lihat Gambar Penuh
                            </a>
                            <div class="mt-1">
                                <img src="{{ asset('storage/' . $p->bukti_pembayaran) }}" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 text-end border-top pt-2">
                        <button type="submit" class="btn btn-success fw-bold btn-sm px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Status Transaksi #{{ $p->id }}
                        </button>
                    </div>
                </div>
            </div>

        </form> @endforeach
@else
                    <div class="alert alert-warning mb-0 text-center py-4 rounded-3">
                        <i class="fas fa-exclamation-triangle fs-4 d-block mb-2"></i>
                        <span>Belum ada riwayat transaksi pembayaran masuk untuk data booking ini.</span>
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary fw-bold px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endforeach

<script>
const groupedPaket = {!! json_encode($groupedPaket ?? []) !!};
const groupedFasilitas = {!! json_encode($groupedFasilitas ?? []) !!};

let pIdx = 0;
let fIdx = 0;
let editItemCounter = 999;

// VALIDASI FILE UPLOAD CLIENT-SIDE
function validateFile(input) {
    const file = input.files[0];
    const errorMsg = document.getElementById('file_error_msg');
    const errorText = document.getElementById('file_error_text');
    const submitBtn = document.getElementById('btn_submit_booking');

    if (file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024; // 2 MB

        if (!validTypes.includes(file.type)) {
            errorText.innerText = "Format file ditolak! Hanya boleh mengunggah file JPG atau PNG.";
            errorMsg.classList.remove('d-none');
            input.value = '';
            submitBtn.disabled = true;
            return;
        }

        if (file.size > maxSize) {
            errorText.innerText = "Ukuran file terlalu besar! Ukuran maksimal adalah 2MB.";
            errorMsg.classList.remove('d-none');
            input.value = '';
            submitBtn.disabled = true;
            return;
        }

        errorMsg.classList.add('d-none');
        submitBtn.disabled = false;
    }
}

function formatRupiahRealtime(input, hiddenId) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    input.value = rupiah;

    let numeric = parseInt(value.replace(/\./g, '')) || 0;
    document.getElementById(hiddenId).value = numeric;
    calculateTotal();
}

function formatRupiahEdit(input, bId) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    input.value = rupiah;

    let angka = parseInt(value.replace(/\./g, '')) || 0;
    document.getElementById('edit_diskon_' + bId).value = angka;
    calcEditTotal(bId);
}

function renderItinerary() {
    let ci = new Date(document.getElementById('tanggal_kunjungan').value);
    let co = new Date(document.getElementById('tanggal_selesai').value);

    if (isNaN(ci) || isNaN(co)) return;

    let totalMalam = Math.ceil((co - ci) / (1000 * 60 * 60 * 24));
    if (totalMalam < 1) totalMalam = 1;

    document.getElementById('jml_malam').value = totalMalam;
    document.getElementById('label_jml_malam').innerText = totalMalam;

    let tabs = document.getElementById('day-tabs-container');
    let panels = document.getElementById('itinerary-panels-container');
    tabs.innerHTML = '';
    panels.innerHTML = '';

    for (let i = 0; i < totalMalam; i++) {
        let cur = new Date(ci);
        cur.setDate(ci.getDate() + i);
        let dateStr = cur.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });

        tabs.insertAdjacentHTML('beforeend', `<div class="day-tab ${i===0?'active':''}" id="tab-${i}" onclick="switchTab(${i})">HARI ${i+1} (${dateStr})</div>`);

        let pktTabsHtml = `<div class="d-flex gap-2 overflow-auto custom-scrollbar pb-2 mb-3 border-bottom">`;
        let pktPanesHtml = `<div class="tab-content mb-4">`;
        let isFirstCat = true;
        for (const katName in groupedPaket) {
            let slug = katName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
            let activeBtn = isFirstCat ? 'btn-primary' : 'btn-light text-secondary border';
            let activePane = isFirstCat ? 'd-block' : 'd-none';

            pktTabsHtml += `<button type="button" class="btn btn-sm rounded-pill px-4 py-2 fw-bold text-nowrap ${activeBtn} cat-btn-${i}" onclick="switchCatTab(this, 'pane-${slug}-${i}', ${i})">${katName}</button>`;

            let cardsHtml = `<div class="row g-2">`;
            groupedPaket[katName].forEach(p => {
                let safeNama = p.nama_paket.replace(/'/g, "\\'");
                cardsHtml += `
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 item-card h-100 d-flex flex-column justify-content-between" onclick="addPaketData('${p.id}', '${safeNama}', '${p.harga}', '${p.kapasitas}', ${i})">
                        <div class="fw-bold text-dark mb-2">${p.nama_paket}</div>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="badge bg-info bg-opacity-10 text-info border border-info"><i class="fas fa-users"></i> ${p.kapasitas} Org</span>
                            <span class="text-success fw-bold">Rp ${parseInt(p.harga).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                </div>`;
            });
            cardsHtml += `</div>`;
            pktPanesHtml += `<div id="pane-${slug}-${i}" class="cat-pane-${i} ${activePane}">${cardsHtml}</div>`;
            isFirstCat = false;
        }
        pktTabsHtml += `</div>`;
        pktPanesHtml += `</div>`;

        let fasTabsHtml = `<div class="d-flex gap-2 overflow-auto custom-scrollbar pb-2 mb-3 border-bottom mt-4">`;
        let fasPanesHtml = `<div class="tab-content mb-4">`;
        let isFirstFasCat = true;
        for (const katName in groupedFasilitas) {
            let slug = katName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase() + '-fas';
            let activeBtn = isFirstFasCat ? 'btn-success' : 'btn-light text-secondary border';
            let activePane = isFirstFasCat ? 'd-block' : 'd-none';

            fasTabsHtml += `<button type="button" class="btn btn-sm rounded-pill px-4 py-2 fw-bold text-nowrap ${activeBtn} fas-btn-${i}" onclick="switchFasTab(this, 'pane-${slug}-${i}', ${i})">${katName}</button>`;

            let fCardsHtml = `<div class="row g-2">`;
            groupedFasilitas[katName].forEach(f => {
                let safeNama = f.nama_fasilitas.replace(/'/g, "\\'");
                fCardsHtml += `
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 item-card h-100 d-flex flex-column justify-content-between" onclick="addFasilitasData('${f.id}', '${safeNama}', '${f.harga}', ${i})">
                        <div class="fw-bold text-dark mb-2">${f.nama_fasilitas}</div>
                        <div class="text-success fw-bold text-end">Rp ${parseInt(f.harga).toLocaleString('id-ID')}</div>
                    </div>
                </div>`;
            });
            fCardsHtml += `</div>`;
            fasPanesHtml += `<div id="pane-${slug}-${i}" class="fas-pane-${i} ${activePane}">${fCardsHtml}</div>`;
            isFirstFasCat = false;
        }
        fasTabsHtml += `</div>`;
        fasPanesHtml += `</div>`;

        let panelHtml = `
        <div class="itinerary-panel ${i===0?'active':''}" id="panel-${i}">
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-box"></i> Pilih Paket Wisata (Hari ke-${i+1})</h6>
            ${pktTabsHtml}
            ${pktPanesHtml}

            <h6 class="fw-bold text-success mb-3 mt-4"><i class="fas fa-plus-circle"></i> Pilih Fasilitas Tambahan (Hari ke-${i+1})</h6>
            ${fasTabsHtml}
            ${fasPanesHtml}

            <div class="pt-3 border-top mt-4 bg-light p-3 rounded">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-shopping-cart"></i> Item Terpilih (Hari ke-${i+1}):</h6>
                <div id="selected-items-hari-${i}"></div>
            </div>
        </div>`;

        panels.insertAdjacentHTML('beforeend', panelHtml);
    }
    calculateTotal();
}

function addPaketData(id, nama, harga, kap, hariIndex) {
    let html = `
    <div class="row g-2 item-row pkt-row align-items-center bg-white p-2 rounded mb-2 border shadow-sm">
        <div class="col-md-7">
            <div class="fw-bold text-dark fs-6 mb-1">${nama} <span class="badge bg-secondary ms-1">Kap: ${kap}</span></div>
            <div class="text-primary small fw-bold">Rp ${parseInt(harga).toLocaleString('id-ID')}</div>
            <input type="hidden" name="paket[${pIdx}][paket_id]" value="${id}">
            <input type="hidden" name="paket[${pIdx}][hari]" value="${hariIndex + 1}">
            <input type="hidden" class="paket-harga-hidden" value="${harga}">
            <input type="hidden" class="paket-kapasitas-hidden" value="${kap}">
        </div>
        <div class="col-md-4">
            <div class="input-group-qty-modern mx-auto">
                <button type="button" class="btn-qty" onclick="changeQty(this, -1, true)">-</button>
                <input type="number" name="paket[${pIdx}][qty]" class="qty-input-modern qty-input text-center" value="1" min="1" oninput="calculateTotal()" readonly>
                <button type="button" class="btn-qty" onclick="changeQty(this, 1)">+</button>
            </div>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.pkt-row').remove(); calculateTotal()"><i class="fas fa-trash"></i></button>
        </div>
    </div>`;
    document.getElementById(`selected-items-hari-${hariIndex}`).insertAdjacentHTML('beforeend', html);
    pIdx++;
    calculateTotal();
}

function addFasilitasData(id, nama, harga, hariIndex) {
    let html = `
    <div class="row g-2 item-row fas-row align-items-center bg-white p-2 rounded mb-2 border shadow-sm">
        <div class="col-md-7">
            <div class="fw-bold text-dark fs-6 mb-1">${nama}</div>
            <div class="text-success small fw-bold">Rp ${parseInt(harga).toLocaleString('id-ID')}</div>
            <input type="hidden" name="fasilitas[${fIdx}][fasilitas_id]" value="${id}">
            <input type="hidden" name="fasilitas[${fIdx}][hari]" value="${hariIndex + 1}">
            <input type="hidden" class="fas-harga-hidden" value="${harga}">
        </div>
        <div class="col-md-4">
            <div class="input-group-qty-modern mx-auto">
                <button type="button" class="btn-qty" onclick="changeQty(this, -1, true)">-</button>
                <input type="number" name="fasilitas[${fIdx}][qty]" class="qty-input-modern qty-input text-center" value="1" min="1" oninput="calculateTotal()" readonly>
                <button type="button" class="btn-qty" onclick="changeQty(this, 1)">+</button>
            </div>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.fas-row').remove(); calculateTotal()"><i class="fas fa-trash"></i></button>
        </div>
    </div>`;
    document.getElementById(`selected-items-hari-${hariIndex}`).insertAdjacentHTML('beforeend', html);
    fIdx++;
    calculateTotal();
}

function switchTab(i) {
    document.querySelectorAll('.day-tab').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.itinerary-panel').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + i).classList.add('active');
    document.getElementById('panel-' + i).classList.add('active');
}

function switchCatTab(btn, paneId, dayIndex) {
    document.querySelectorAll(`.cat-btn-${dayIndex}`).forEach(el => {
        el.classList.remove('btn-primary');
        el.classList.add('btn-light', 'text-secondary', 'border');
    });
    btn.classList.remove('btn-light', 'text-secondary', 'border');
    btn.classList.add('btn-primary');
    document.querySelectorAll(`.cat-pane-${dayIndex}`).forEach(el => {
        el.classList.remove('d-block'); el.classList.add('d-none');
    });
    document.getElementById(paneId).classList.remove('d-none');
    document.getElementById(paneId).classList.add('d-block');
}

function switchFasTab(btn, paneId, dayIndex) {
    document.querySelectorAll(`.fas-btn-${dayIndex}`).forEach(el => {
        el.classList.remove('btn-success');
        el.classList.add('btn-light', 'text-secondary', 'border');
    });
    btn.classList.remove('btn-light', 'text-secondary', 'border');
    btn.classList.add('btn-success');
    document.querySelectorAll(`.fas-pane-${dayIndex}`).forEach(el => {
        el.classList.remove('d-block'); el.classList.add('d-none');
    });
    document.getElementById(paneId).classList.remove('d-none');
    document.getElementById(paneId).classList.add('d-block');
}

function changeQtyValue(id, val) {
    let input = document.getElementById(id);
    let newVal = parseInt(input.value) + val;
    if (newVal >= 1) { input.value = newVal; calculateTotal(); }
}

function changeQty(btn, val, allowDelete) {
    let input = btn.parentElement.querySelector('input');
    let newVal = parseInt(input.value) + val;
    if (newVal < 1) {
        if (allowDelete) { btn.closest('.item-row').remove(); } else { input.value = 1; }
    } else {
        input.value = newVal;
    }
    calculateTotal();
}

function calculateTotal() {
    let totalPaket = 0;
    let totalFasilitas = 0;
    let totalKapasitasMenginap = 0;

    document.querySelectorAll('.pkt-row').forEach(row => {
        let qty = parseInt(row.querySelector('.qty-input').value) || 0;
        let harga = parseFloat(row.querySelector('.paket-harga-hidden').value) || 0;
        let kap = parseInt(row.querySelector('.paket-kapasitas-hidden').value) || 0;

        totalPaket += (qty * harga);
        if (kap > 1) {
            totalKapasitasMenginap += (qty * kap);
        }
    });

    document.querySelectorAll('.fas-row').forEach(row => {
        let qty = parseInt(row.querySelector('.qty-input').value) || 0;
        let harga = parseFloat(row.querySelector('.fas-harga-hidden').value) || 0;
        totalFasilitas += (qty * harga);
    });

    let pengunjung = parseInt(document.getElementById('jml_pengunjung').value) || 0;
    let tiketTambahanQty = 0;

    if (totalKapasitasMenginap > 0 && pengunjung > totalKapasitasMenginap) {
        tiketTambahanQty = pengunjung - totalKapasitasMenginap;
    }

    let subtotalTiket = tiketTambahanQty * 25000;
    let diskonManual = parseFloat(document.getElementById('diskon_manual').value) || 0;

    let totalAwal = totalPaket + totalFasilitas + subtotalTiket;
    let totalAkhir = totalAwal - diskonManual;
    if (totalAkhir < 0) totalAkhir = 0;

    document.getElementById('disp-sub-paket').innerText = 'Rp ' + totalPaket.toLocaleString('id-ID');
    document.getElementById('disp-sub-fasilitas').innerText = 'Rp ' + totalFasilitas.toLocaleString('id-ID');
    document.getElementById('disp-sub-tiket').innerText = 'Rp ' + subtotalTiket.toLocaleString('id-ID');
    document.getElementById('disp-diskon').innerText = '- Rp ' + diskonManual.toLocaleString('id-ID');
    document.getElementById('disp-total').innerText = 'Rp ' + totalAkhir.toLocaleString('id-ID');
    document.getElementById('disp-kapasitas').innerText = totalKapasitasMenginap;

    document.getElementById('hidden_tiket_tambahan').value = tiketTambahanQty;
    document.getElementById('hidden_subtotal_tiket').value = subtotalTiket;
    document.getElementById('hidden_total_harga').value = totalAwal;
    document.getElementById('hidden_total_harga_final').value = totalAkhir;
}

function addEditPaket(bId) {
    let sel = document.getElementById('add_paket_sel_' + bId);
    let hari = document.getElementById('add_paket_hari_' + bId).value || 1;
    let qty = document.getElementById('add_paket_qty_' + bId).value || 1;

    if(sel.selectedIndex <= 0) return alert('Pilih paket wisata');

    let opt = sel.options[sel.selectedIndex];
    let pId = opt.value;
    let nama = opt.getAttribute('data-nama');
    let harga = parseFloat(opt.getAttribute('data-harga'));
    let kap = parseInt(opt.getAttribute('data-kapasitas')) || 0;
    editItemCounter++;

    let html = `
    <div class="row g-2 align-items-center mb-2 edit-paket-row-${bId} edit-paket-item-row pb-2 border-bottom border-light">
        <div class="col-md-4">
            <div class="fw-bold text-dark">${nama} <span class="badge bg-secondary ms-1">Kap: ${kap}</span></div>
            <small class="text-success fw-bold">Rp ${harga.toLocaleString('id-ID')}</small>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light">Hari Ke-</span>
                <input type="number" name="paket[new_${editItemCounter}][hari]" class="form-control" value="${hari}" min="1">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light">Qty</span>
                <input type="number" name="paket[new_${editItemCounter}][qty]" class="form-control edit-qty-paket" value="${qty}" min="1" oninput="calcEditTotal(${bId})">
            </div>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.edit-paket-row-${bId}').remove(); calcEditTotal(${bId})"><i class="fas fa-trash"></i></button>
            <input type="hidden" name="paket[new_${editItemCounter}][paket_id]" value="${pId}">
            <input type="hidden" class="edit-harga-paket" value="${harga}">
            <input type="hidden" class="edit-kapasitas-paket" value="${kap}">
        </div>
    </div>`;
    document.getElementById('edit-paket-container-' + bId).insertAdjacentHTML('beforeend', html);
    calcEditTotal(bId);
}

function addEditFasilitas(bId) {
    let sel = document.getElementById('add_fas_sel_' + bId);
    let hari = document.getElementById('add_fas_hari_' + bId).value || 1;
    let qty = document.getElementById('add_fas_qty_' + bId).value || 1;

    if(sel.selectedIndex <= 0) return alert('Pilih fasilitas');

    let opt = sel.options[sel.selectedIndex];
    let fId = opt.value;
    let nama = opt.getAttribute('data-nama');
    let harga = parseFloat(opt.getAttribute('data-harga'));
    editItemCounter++;

    let html = `
    <div class="row g-2 align-items-center mb-2 edit-fas-row-${bId} edit-fas-item-row pb-2 border-bottom border-light">
        <div class="col-md-4">
            <div class="fw-bold text-dark">${nama}</div>
            <small class="text-success fw-bold">Rp ${harga.toLocaleString('id-ID')}</small>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light">Hari Ke-</span>
                <input type="number" name="fasilitas[new_${editItemCounter}][hari]" class="form-control" value="${hari}" min="1">
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light">Qty</span>
                <input type="number" name="fasilitas[new_${editItemCounter}][qty]" class="form-control edit-qty-fas" value="${qty}" min="1" oninput="calcEditTotal(${bId})">
            </div>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.edit-fas-row-${bId}').remove(); calcEditTotal(${bId})"><i class="fas fa-trash"></i></button>
            <input type="hidden" name="fasilitas[new_${editItemCounter}][fasilitas_id]" value="${fId}">
            <input type="hidden" class="edit-harga-fas" value="${harga}">
        </div>
    </div>`;
    document.getElementById('edit-fas-container-' + bId).insertAdjacentHTML('beforeend', html);
    calcEditTotal(bId);
}

function calcEditTotal(id) {
    let modal = document.getElementById('modalEditBooking' + id);
    if (!modal) return;

    let pengunjung = parseInt(document.getElementById('edit_pengunjung_' + id)?.value) || 0;
    let diskon = parseInt(document.getElementById('edit_diskon_' + id)?.value) || 0;

    let totalPaket = 0;
    let totalFas = 0;
    let totalKapasitasMenginap = 0;

    // Hitung Paket berdasarkan row item yang ada di dalam modal edit spesifik
    modal.querySelectorAll('.edit-paket-item-row').forEach(row => {
        let qty = parseInt(row.querySelector('.edit-qty-paket')?.value) || 0;
        let harga = parseFloat(row.querySelector('.edit-harga-paket')?.value) || 0;
        let kap = parseInt(row.querySelector('.edit-kapasitas-paket')?.value) || 0;

        totalPaket += (qty * harga);
        if (kap >= 1) {
            totalKapasitasMenginap += (qty * kap);
        }
    });

    // Hitung Fasilitas
    modal.querySelectorAll('.edit-fas-item-row').forEach(row => {
        let qty = parseInt(row.querySelector('.edit-qty-fas')?.value) || 0;
        let harga = parseFloat(row.querySelector('.edit-harga-fas')?.value) || 0;
        totalFas += (qty * harga);
    });

    // Hitung Tiket Tambahan (Maksimal 0 kalau kapasitas mencukupi)
    let tiketTambahanQty = (totalKapasitasMenginap > 0 && pengunjung > totalKapasitasMenginap)
                           ? (pengunjung - totalKapasitasMenginap) : 0;

    let total = totalPaket + totalFas + (tiketTambahanQty * 25000);
    let final = Math.max(0, total - diskon);

    let lblDasar = document.getElementById('label_edit_dasar_' + id);
    let lblFinal = document.getElementById('label_edit_final_' + id);

    if (lblDasar) lblDasar.innerText = total.toLocaleString('id-ID');
    if (lblFinal) lblFinal.innerText = final.toLocaleString('id-ID');
}
</script>
@endsection
