@extends('layouts.admin')

@section('title', 'Manajemen Pembayaran')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Manajemen Pembayaran</h2>
            <p class="text-muted small">
                Kelola verifikasi bukti pembayaran, update status, dan integrasi kas pemasukan otomatis.
            </p>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI DARI CONTROLLER --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="pembayaranTable">
                    <thead class="text-secondary small text-uppercase">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Kode Booking</th>
                            <th>Pemesan</th>
                            <th>Bukti Transfer</th>
                            <th>Nominal</th>
                            <th>Status Verifikasi</th>
                            <th class="text-center" style="width: 10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                      @forelse($pembayaran as $key => $item)
                        @php
                            $statusBadge = $item->status_verifikasi == 'valid'
                                ? 'bg-success-subtle text-success border border-success'
                                : ($item->status_verifikasi == 'ditolak'
                                    ? 'bg-danger-subtle text-danger border border-danger'
                                    : 'bg-warning-subtle text-warning border border-warning');

                            // PERBAIKAN: Hitung akumulasi dari semua pembayaran valid untuk booking ini
                            $totalHargaFinal = (float) ($item->booking->total_harga_final ?? 0);
                            $totalSudahBayar = \App\Models\Pembayaran::where('booking_id', $item->booking_id)
                                                ->where('status_verifikasi', 'valid')
                                                ->sum('nominal');

                            $sisaTagihan = max(0, $totalHargaFinal - $totalSudahBayar);
                            $statusPembayaranBooking = $item->booking->status_pembayaran ?? 'belum_bayar';
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <span class="badge" style="background: #E6F0FF; color: #0066FF; font-weight:600; padding: 6px 12px;">
                                    {{ $item->booking->kode_booking ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->booking->nama_pemesan ?? '-' }}</div>
                                <small class="text-muted">{{ $item->booking->no_hp ?? '' }}</small>
                            </td>
                            <td>
                                @if($item->bukti_pembayaran)
                                    <button type="button" class="btn btn-sm btn-light border rounded-3 shadow-sm px-3"
                                            onclick="previewBukti('{{ asset('storage/' . $item->bukti_pembayaran) }}')">
                                        <i class="fas fa-eye text-primary me-1"></i> Lihat Bukti
                                    </button>
                                @else
                                    <span class="text-muted small italic">Belum Upload</span>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                <div>Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</div>
                                <small class="text-danger" style="font-size: 0.7rem;">Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $statusBadge }} text-uppercase px-3 py-2 rounded-pill small">
                                    {{ $item->status_verifikasi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary rounded-2 shadow-sm px-1 py-2"
                                    onclick="bukaModal(
                                        '{{ $item->id }}',
                                        '{{ $item->status_verifikasi }}',
                                        '{{ addslashes($item->catatan ?? '') }}',
                                        '{{ $sisaTagihan }}',
                                        '{{ $statusPembayaranBooking }}'
                                    )" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-double" style="font-size: 0.7rem;"></i> Proses
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data pembayaran masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODAL VERIFIKASI --}}
<div class="modal fade" id="modalVerifikasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-shield-alt text-primary me-2"></i>Verifikasi / Pelunasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formVerifikasi" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">

                    {{-- KOTAK INFO SISA TAGIHAN OTOMATIS --}}
                    <div id="box_sisa_tagihan" class="alert alert-warning border-0 rounded-3 mb-3 p-3 d-none">
                        <div class="small text-muted mb-1">Status Pembayaran Booking Saat Ini: <b id="text_status_pembayaran" class="text-uppercase text-primary"></b></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Sisa Tagihan Pengunjung:</span>
                            <span class="fw-bold text-danger fs-5" id="text_sisa_harga">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Pilih Tindakan / Status</label>
                        <select name="status_verifikasi" id="status_verifikasi" class="form-select rounded-3" required onchange="togglePelunasanField(this.value)">
                            <option value="pending">⏳ PENDING (Belum Dicek)</option>
                            <option value="valid">✅ VALID (ACC Bukti Pembayaran Pengunjung)</option>
                            <option value="pelunasan">💵 PELUNASAN HARI H (Bayar Sisa Tagihan di Lokasi)</option>
                            <option value="ditolak">❌ DITOLAK (Bukti Salah / Palsu)</option>
                        </select>
                    </div>

                    {{-- FIELD INPUT PELUNASAN --}}
                    <div id="field_pelunasan_langsung" class="d-none bg-light p-3 rounded-3 mb-3 border">
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-primary">Nominal Uang Sisa Pelunasan (Rp)</label>
                            <input type="number" name="nominal_pelunasan" id="nominal_pelunasan" class="form-control rounded-3 fw-bold text-success" placeholder="Masukkan jumlah pelunasan">
                        </div>
                        <div>
                            <label class="form-label fw-bold small text-primary">Metode Pembayaran Sisa</label>
                            <select name="metode_pelunasan" class="form-select rounded-3">
                                <option value="cash">💵 CASH / TUNAI</option>
                                <option value="transfer">💳 TRANSFER BANK</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catatan Internal / Keterangan</label>
                        <textarea name="catatan" id="catatan_verifikasi" class="form-control rounded-3" rows="3" placeholder="Masukkan catatan tambahan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light border rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitVerifikasi" class="btn btn-primary rounded-3 shadow-sm px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL POPUP PREVIEW BUKTI TRANSAKSI --}}
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-secondary">Foto Bukti Transfer Pemesan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img src="" id="imgPreviewTarget" class="img-fluid rounded-3 border shadow-sm" style="max-height: 500px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#pembayaranTable')) {
            $('#pembayaranTable').DataTable().destroy();
        }
        $('#pembayaranTable').DataTable({
            paging: true,
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            scrollX: true,
            language: {
                search: "Cari Pembayaran:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pembayaran"
            }
        });

        var formVerif = document.getElementById('formVerifikasi');
        if(formVerif) {
            formVerif.addEventListener('submit', function() {
                let btn = document.getElementById('btnSubmitVerifikasi');
                if(btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
                }
            });
        }
    });

    function previewBukti(urlSrc) {
        document.getElementById('imgPreviewTarget').src = urlSrc;
        var modalPreview = new bootstrap.Modal(document.getElementById('modalPreview'));
        modalPreview.show();
    }

    function togglePelunasanField(value) {
        let fieldPelunasan = document.getElementById('field_pelunasan_langsung');
        if (fieldPelunasan) {
            if (value === 'pelunasan') {
                fieldPelunasan.classList.remove('d-none');
            } else {
                fieldPelunasan.classList.add('d-none');
            }
        }
    }

    function bukaModal(id, status, catatan, sisaTagihan, statusPembayaran) {
        let form = document.getElementById('formVerifikasi');
        if(form) {
            form.action = '/admin/pembayaran/' + id;
        }

        if(document.getElementById('status_verifikasi')) {
            document.getElementById('status_verifikasi').value = status;
        }
        if(document.getElementById('catatan_verifikasi')) {
            document.getElementById('catatan_verifikasi').value = catatan;
        }

        let boxSisa = document.getElementById('box_sisa_tagihan');
        if(boxSisa) {
            boxSisa.classList.remove('d-none');
        }

        if(document.getElementById('text_status_pembayaran')) {
            document.getElementById('text_status_pembayaran').innerText = statusPembayaran.replace('_', ' ');
        }

        let formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        if(document.getElementById('text_sisa_harga')) {
            document.getElementById('text_sisa_harga').innerText = formatter.format(sisaTagihan);
        }

        let inputPelunasan = document.getElementById('nominal_pelunasan');
        if(inputPelunasan) {
            inputPelunasan.value = sisaTagihan;
            // Jika sisa tagihan di atas 0, batasi input maksimal agar tidak melebihi sisa tagihan asli
            if (parseFloat(sisaTagihan) > 0) {
                inputPelunasan.max = sisaTagihan;
            } else {
                inputPelunasan.removeAttribute('max');
            }
        }

        togglePelunasanField(status);

        var myModal = new bootstrap.Modal(document.getElementById('modalVerifikasi'));
        myModal.show();
    }
</script>
@endpush
