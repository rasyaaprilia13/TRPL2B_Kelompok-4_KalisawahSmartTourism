@extends('layouts.app')

@php
    $draftBooking = $draftBooking ?? session('temp_booking_data') ?? [];
@endphp

@section('content')

<div class="container mx-auto px-4 pt-32 md:pt-40 pb-40">
    <div class="max-w-5xl mx-auto">

        {{-- HEADER --}}
        <div class="text-center mb-14">
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
                {{ isset($booking) ? 'Ubah Reservasi' : 'Reservasi Paket Wisata' }}
            </h1>

            <p class="text-slate-500 mt-3 text-base leading-relaxed max-w-3xl mx-auto">
                Tentukan jadwal check-in dan check-out, pilih paket wisata setiap malam,
                lalu tambahkan fasilitas sesuai kebutuhan selama menginap.
            </p>
        </div>

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="bg-red-50 rounded-3xl p-5 mb-10 border border-red-100">
                <h3 class="font-bold text-red-700 mb-3">Terjadi Kesalahan</h3>

                <ul class="space-y-2 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ isset($booking) ? route('pengunjung.booking.update', $booking->id) : route('pengunjung.booking.review') }}"
            method="POST"
            id="form-reservasi"
        >
            @csrf

            @if(isset($booking))
                @method('PUT')
            @endif

            <div id="hidden-inputs"></div>

            {{-- CARD DATA PEMESAN --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 sm:p-10 mb-10">

                <div class="mb-8 pb-5 border-b border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-800">
                        Data Pemesan
                    </h2>
                </div>

                {{-- INPUT ATAS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="nama_pemesan"
                            value="{{ old('nama_pemesan', isset($booking) ? $booking->nama_pemesan : ($draftBooking['nama_pemesan'] ?? '')) }}"
                            class="w-full h-14 px-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none"
                            placeholder="Masukkan nama pemesan"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Nomor WhatsApp
                        </label>

                        <input
                            type="text"
                            name="no_hp"
                            value="{{ old('no_hp', isset($booking) ? $booking->no_hp : ($draftBooking['no_hp'] ?? '')) }}"
                            class="w-full h-14 px-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none"
                            placeholder="08xxxxxxxxxx"
                            required
                        >
                    </div>

                </div>

                {{-- INPUT BAWAH --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">

                    {{-- CHECKIN --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Check-In
                        </label>

                        <input
                            type="date"
                            name="tanggal_kunjungan"
                            id="tanggal_checkin"
                            value="{{ old('tanggal_kunjungan', isset($booking) ? $booking->tanggal_kunjungan : ($draftBooking['tanggal_kunjungan'] ?? '')) }}"
                            class="w-full h-14 px-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    {{-- CHECKOUT --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Check-Out
                        </label>

                        <input
                            type="date"
                            name="tanggal_checkout"
                            id="tanggal_checkout"
                            value="{{ old('tanggal_checkout', isset($booking) ? (\Carbon\Carbon::parse($booking->tanggal_selesai)->format('Y-m-d')) : ($draftBooking['tanggal_checkout'] ?? '')) }}"
                            class="w-full h-14 px-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    {{-- JAM --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Jam Check-In
                        </label>

                        <input
                            type="time"
                            name="jam"
                            value="{{ old('jam', isset($booking) ? \Carbon\Carbon::parse($booking->jam)->format('H:i') : ($draftBooking['jam'] ?? '')) }}"
                            class="w-full h-14 px-5 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 outline-none"
                            required
                        >
                    </div>

                    {{-- JUMLAH MALAM --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Jumlah Malam
                        </label>

                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl h-14 px-2">

                            <button
                                type="button"
                                id="btn-kurang-malam"
                                class="w-10 h-10 rounded-xl bg-white border border-slate-200 font-bold"
                            >
                                -
                            </button>

                            <input
                                type="number"
                                name="jumlah_malam"
                                id="lama_menginap"
                                min="1"
                                value="{{ old('jumlah_malam', isset($booking) ? $booking->jumlah_malam : ($draftBooking['jumlah_malam'] ?? 1)) }}"
                                class="w-16 text-center bg-transparent border-none focus:ring-0 font-bold"
                            >

                            <button
                                type="button"
                                id="btn-tambah-malam"
                                class="w-10 h-10 rounded-xl bg-white border border-slate-200 font-bold"
                            >
                                +
                            </button>

                        </div>
                    </div>

                    {{-- JUMLAH ORANG --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Jumlah Orang
                        </label>

                        <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl h-14 px-2">

                            <button
                                type="button"
                                id="btn-kurang-p"
                                class="w-10 h-10 rounded-xl bg-white border border-slate-200 font-bold"
                            >
                                -
                            </button>

                            <input
                                type="number"
                                name="jumlah_pengunjung"
                                id="jumlah_pengunjung"
                                value="{{ old('jumlah_pengunjung', isset($booking) ? $booking->jumlah_pengunjung : ($draftBooking['jumlah_pengunjung'] ?? 1)) }}"
                                class="w-16 text-center bg-transparent border-none focus:ring-0 font-bold"
                            >

                            <button
                                type="button"
                                id="btn-tambah-p"
                                class="w-10 h-10 rounded-xl bg-white border border-slate-200 font-bold"
                            >
                                +
                            </button>

                        </div>
                    </div>

                </div>

                {{-- KETERANGAN --}}
                <div class="mt-8 space-y-4">

                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                        <p class="text-sm text-blue-700 leading-relaxed">
                            Jumlah malam otomatis dihitung dari tanggal
                            <span class="font-bold">check-in</span>
                            sampai
                            <span class="font-bold">check-out</span>.
                        </p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
                        <p class="text-sm text-yellow-800 leading-relaxed">
                            Jika jumlah orang melebihi total kapasitas paket yang dipilih,
                            maka akan dikenakan
                            <span class="font-bold">biaya tiket tambahan Rp 25.000 / orang</span>.
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                        <p class="text-sm text-slate-700 leading-relaxed">
                            Fasilitas tambahan dihitung
                            <span class="font-bold">per malam menginap</span>.
                            Semakin lama menginap, maka total biaya fasilitas akan menyesuaikan.
                        </p>
                    </div>

                </div>

            </div>

            {{-- CARD PAKET --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 sm:p-10">

                {{-- TAB HARI --}}
                <div
                    id="container-tab"
                    class="flex overflow-x-auto hide-scrollbar gap-3 mb-8 pb-4 border-b border-slate-100"
                ></div>

                {{-- ISI --}}
                <div id="container-konten-hari"></div>

            </div>

            {{-- BUTTON --}}
            <div class="flex flex-col-reverse sm:flex-row gap-5 justify-center mt-16 mb-32 items-center">

                <a
                    href="{{ route('landing-page.home') }}"
                    class="px-10 py-4 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold transition w-full sm:w-auto text-center"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-12 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition w-full sm:w-auto shadow-lg"
                >
                    {{ isset($booking) ? 'Simpan Perubahan' : 'Lanjutkan Reservasi' }}
                </button>

            </div>

        </form>

    </div>
</div>

{{-- MODAL PAKET --}}
<div id="modal-pilih-paket" class="fixed inset-0 z-[999] hidden overflow-y-auto">

    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="flex items-center justify-center min-h-screen p-4 relative z-20">

        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl flex flex-col max-h-[90vh]">

            {{-- HEADER --}}
            <div class="flex justify-between items-center p-8 border-b border-slate-100">

                <h3 class="font-bold text-2xl text-slate-800">
                    Pilih Paket
                </h3>

                <button
                    type="button"
                    id="btn-close-modal"
                    class="w-11 h-11 rounded-full bg-slate-100 hover:bg-slate-200"
                >
                    ✕
                </button>

            </div>

            {{-- CONTENT --}}
            <div class="p-8 overflow-y-auto">

                @foreach($kategoriPaket as $kategori)

                    <div class="mb-10">

                        <h4 class="text-blue-600 font-black uppercase tracking-widest text-sm mb-5">
                            {{ $kategori->nama_kategori }}
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            @foreach($paket->where('kategori_paket_id', $kategori->id) as $p)

                                <div
                                    class="item-paket-modal border-2 border-slate-200 rounded-3xl p-6 hover:border-blue-500 cursor-pointer transition"
                                    data-id="{{ $p->id }}"
                                    data-nama="{{ $p->nama_paket }}"
                                    data-harga="{{ $p->harga }}"
                                    data-kapasitas="{{ $p->kapasitas ?? 0 }}"
                                >

                                    <h5 class="font-bold text-lg text-slate-800 mb-3">
                                        {{ $p->nama_paket }}
                                    </h5>

                                    <div class="flex items-center justify-between">

                                        <div>
                                            <p class="text-xs text-slate-400 font-bold">
                                                Harga
                                            </p>

                                            <p class="font-black text-blue-600">
                                                Rp {{ number_format($p->harga,0,',','.') }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-xs text-slate-400 font-bold">
                                                Kapasitas
                                            </p>

                                            <p class="font-bold text-slate-700">
                                                {{ $p->kapasitas ?? 0 }} Orang
                                            </p>
                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>

    @php
        $statePackages = [];
        if(isset($booking))
        {
            foreach($booking->items->groupBy('hari') as $hari => $items)
            {
                foreach($items as $item)
                {
                    $statePackages[$hari][] = [
                        'id' => $item->paket_wisata_id,
                        'nama' => $item->paketWisata->nama_paket ?? '-',
                        'qty' => $item->qty,
                        'harga' => $item->harga,
                        'kapasitas' => $item->paketWisata->kapasitas ?? 0,
                    ];
                }
            }
        }

        $stateFasilitas = [];
        if(isset($booking))
        {
            foreach($booking->fasilitas->groupBy('hari') as $hari => $items)
            {
                foreach($items as $item)
                {
                    $stateFasilitas[$hari][$item->fasilitas_id] = $item->qty;
                }
            }
        }
    @endphp

    let statePackages = @json($statePackages);
    let stateFasilitas = @json($stateFasilitas);
    let targetHariAktif = 0;
    let activeFasCategory = {};

    const allFasilitas = @json($fasilitas);
    const allPaket = @json($paket);
    const SERVER_DRAFT = @json($draftBooking ?? []);
    const OLD_DRAFT = {
        nama_pemesan: @json(old('nama_pemesan')),
        no_hp: @json(old('no_hp')),
        tanggal_kunjungan: @json(old('tanggal_kunjungan')),
        tanggal_checkout: @json(old('tanggal_checkout')),
        jam: @json(old('jam')),
        jumlah_malam: @json(old('jumlah_malam')),
        jumlah_pengunjung: @json(old('jumlah_pengunjung')),
        paket: @json(old('paket', [])),
        paket_qty: @json(old('paket_qty', [])),
        fasilitas: @json(old('fasilitas', [])),
        lahan: @json(old('lahan', [])),
    };

    const STORAGE_KEY = 'booking_form_draft_v1';
    let isEditing = @json(isset($booking));

    function isNonEmptyObject(value) {
        return value && typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length > 0;
    }

    function applyDraftToState(draft) {
        if (!draft || typeof draft !== 'object') {
            return;
        }

        statePackages = {};
        stateFasilitas = {};

        if (draft.paket && typeof draft.paket === 'object') {
            Object.entries(draft.paket).forEach(([hari, paketIds]) => {
                if (!Array.isArray(paketIds)) {
                    return;
                }

                paketIds.forEach(paketId => {
                    if (!statePackages[hari]) {
                        statePackages[hari] = [];
                    }

                    const qty = Number(draft.paket_qty?.[hari]?.[paketId] ?? 1) || 1;
                    const paket = allPaket.find(p => String(p.id) === String(paketId));

                    statePackages[hari].push({
                        id: paketId,
                        nama: paket?.nama_paket ?? 'Paket',
                        qty: qty,
                        harga: paket?.harga ?? 0,
                        kapasitas: paket?.kapasitas ?? 0,
                    });
                });
            });
        }

        if (draft.fasilitas && typeof draft.fasilitas === 'object') {
            Object.entries(draft.fasilitas).forEach(([hari, fasList]) => {
                if (!stateFasilitas[hari]) {
                    stateFasilitas[hari] = {};
                }

                if (typeof fasList === 'object') {
                    Object.entries(fasList).forEach(([fasId, qty]) => {
                        stateFasilitas[hari][fasId] = Number(qty) || 0;
                    });
                }
            });
        }

        if (draft.lahan && typeof draft.lahan === 'object') {
            Object.entries(draft.lahan).forEach(([hari, fasId]) => {
                if (!fasId) {
                    return;
                }

                if (!stateFasilitas[hari]) {
                    stateFasilitas[hari] = {};
                }

                stateFasilitas[hari][fasId] = 1;
            });
        }
    }

    function buildServerDraft() {
        if (isNonEmptyObject(OLD_DRAFT.paket) || isNonEmptyObject(OLD_DRAFT.fasilitas) || OLD_DRAFT.nama_pemesan) {
            return OLD_DRAFT;
        }

        if (isNonEmptyObject(SERVER_DRAFT)) {
            return SERVER_DRAFT;
        }

        return null;
    }

    function saveDraft()
    {
        try {
            if(isEditing) return;

            const form = document.getElementById('form-reservasi');

            const data = {
                nama_pemesan: form.elements['nama_pemesan']?.value || '',
                no_hp: form.elements['no_hp']?.value || '',
                tanggal_kunjungan: form.elements['tanggal_kunjungan']?.value || '',
                tanggal_checkout: form.elements['tanggal_checkout']?.value || '',
                jam: form.elements['jam']?.value || '',
                jumlah_malam: form.elements['jumlah_malam']?.value || document.getElementById('lama_menginap')?.value || '',
                jumlah_pengunjung: form.elements['jumlah_pengunjung']?.value || '',
                statePackages: statePackages || {},
                stateFasilitas: stateFasilitas || {},
                activeFasCategory: activeFasCategory || {},
                targetHariAktif: targetHariAktif || 0
            };

            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }
        catch(e) {
            console && console.warn && console.warn('saveDraft error', e);
        }
    }

    function loadDraft()
    {
        try {
            if(isEditing) return;

            const draft = buildServerDraft();

            if (draft) {
                const form = document.getElementById('form-reservasi');

                if (draft.nama_pemesan) form.elements['nama_pemesan'].value = draft.nama_pemesan;
                if (draft.no_hp) form.elements['no_hp'].value = draft.no_hp;
                if (draft.tanggal_kunjungan) document.getElementById('tanggal_checkin').value = draft.tanggal_kunjungan;
                if (draft.tanggal_checkout) document.getElementById('tanggal_checkout').value = draft.tanggal_checkout;
                if (draft.jam) form.elements['jam'].value = draft.jam;
                if (draft.jumlah_malam) document.getElementById('lama_menginap').value = draft.jumlah_malam;
                if (draft.jumlah_pengunjung) form.elements['jumlah_pengunjung'].value = draft.jumlah_pengunjung;

                applyDraftToState(draft);
                saveDraft();
                return;
            }

            const s = localStorage.getItem(STORAGE_KEY);

            if(!s) return;

            const d = JSON.parse(s);
            const form = document.getElementById('form-reservasi');

            if(d.nama_pemesan) form.elements['nama_pemesan'].value = d.nama_pemesan;
            if(d.no_hp) form.elements['no_hp'].value = d.no_hp;
            if(d.tanggal_kunjungan) document.getElementById('tanggal_checkin').value = d.tanggal_kunjungan;
            if(d.tanggal_checkout) document.getElementById('tanggal_checkout').value = d.tanggal_checkout;
            if(d.jam) form.elements['jam'].value = d.jam;
            if(d.jumlah_malam) document.getElementById('lama_menginap').value = d.jumlah_malam;
            if(d.jumlah_pengunjung) form.elements['jumlah_pengunjung'].value = d.jumlah_pengunjung;

            if(d.statePackages) statePackages = d.statePackages;
            if(d.stateFasilitas) stateFasilitas = d.stateFasilitas;
            if(d.activeFasCategory) activeFasCategory = d.activeFasCategory;
            if(typeof d.targetHariAktif !== 'undefined') targetHariAktif = d.targetHariAktif;

        }
        catch(e){ console && console.warn && console.warn('loadDraft error', e); }
    }

    function hitungMalam()
    {
        const checkin = document.getElementById('tanggal_checkin').value;
        const checkout = document.getElementById('tanggal_checkout').value;

        if(checkin && checkout)
        {
            const tgl1 = new Date(checkin);
            const tgl2 = new Date(checkout);

            let selisih = Math.ceil(
                (tgl2 - tgl1) / (1000 * 60 * 60 * 24)
            );

            if(selisih < 0)
            {
                selisih = 0;
            }

            document.getElementById('lama_menginap').value = selisih;

            renderTabs();
        }
    }

    function updateCheckout()
    {
        const checkin = document.getElementById('tanggal_checkin').value;
        const malam = parseInt(
            document.getElementById('lama_menginap').value
        );

        if(checkin !== '' && !isNaN(malam))
        {
            let tanggal = new Date(checkin);

            tanggal.setDate(tanggal.getDate() + malam);

            document.getElementById('tanggal_checkout').value =
                tanggal.toISOString().split('T')[0];
        }
    }

    function renderTabs()
    {
        let jmlMalam = parseInt(
            document.getElementById('lama_menginap').value
        );

        if(isNaN(jmlMalam) || jmlMalam < 0)
        {
            jmlMalam = 0;
        }

        const totalHari = jmlMalam === 0 ? 1 : jmlMalam;
        const tanggalCheckin = document.getElementById('tanggal_checkin').value;
        const containerTab = document.getElementById('container-tab');
        const containerKonten = document.getElementById('container-konten-hari');
        const hiddenInputs = document.getElementById('hidden-inputs');

        containerTab.innerHTML = '';
        containerKonten.innerHTML = '';

        if(targetHariAktif >= totalHari)
        {
            targetHariAktif = totalHari - 1;
        }

        for(let i = 0; i < totalHari; i++)
        {
            let labelTgl = '';

            if(tanggalCheckin)
            {
                let d = new Date(tanggalCheckin);

                d.setDate(d.getDate() + i);

                labelTgl = d.toLocaleDateString('id-ID', {
                    day:'numeric',
                    month:'short'
                });
            }

            containerTab.innerHTML += `
                <button
                    type="button"
                    onclick="gantiHari(${i})"
                    class="px-6 py-4 rounded-2xl border-2 min-w-[190px] shrink-0 transition
                    ${
                        i === targetHariAktif
                        ? 'border-blue-600 bg-blue-50 text-blue-700'
                        : 'border-slate-200 bg-white text-slate-500'
                    }"
                >

                    <div class="text-xs font-black uppercase">
                        Hari ${i + 1}
                    </div>

                    <div class="text-sm font-bold mt-1">
                        ${labelTgl}
                    </div>

                    <div class="text-xs mt-2 opacity-70">
                        ${
                            jmlMalam === 0
                            ? 'Tidak Menginap'
                            : 'Sampai malam ke-' + (i + 1)
                        }
                    </div>

                </button>
            `;
        }

        let html = `
            <div class="mb-10">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-bold text-xl text-slate-800">
                            Paket Hari ${targetHariAktif + 1}
                        </h3>

                        <p class="text-sm text-slate-400 mt-1">
                            Pilih paket yang digunakan pada hari ini
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="bukaModal(${targetHariAktif})"
                        class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold"
                    >
                        Tambah Paket
                    </button>
                </div>
        `;

        if(statePackages[targetHariAktif]?.length > 0)
        {
            statePackages[targetHariAktif].forEach((p, idx) => {

                html += `
                    <div class="border border-slate-200 rounded-3xl p-5 mb-5">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                            <div>
                                <h4 class="font-bold text-slate-800 text-lg mb-2">
                                    ${p.nama}
                                </h4>

                                <div class="flex flex-wrap gap-3">
                                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-2 rounded-xl">
                                        Rp ${Number(p.harga).toLocaleString('id-ID')}
                                    </span>

                                    <span class="bg-slate-100 text-slate-700 text-xs font-bold px-3 py-2 rounded-xl">
                                        Kapasitas ${p.kapasitas} Orang
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        onclick="ubahQtyPaket(${targetHariAktif}, ${idx}, -1)"
                                        class="w-10 h-10 rounded-xl border"
                                    >
                                        -
                                    </button>

                                    <span class="font-bold w-8 text-center">
                                        ${p.qty}
                                    </span>

                                    <button
                                        type="button"
                                        onclick="ubahQtyPaket(${targetHariAktif}, ${idx}, 1)"
                                        class="w-10 h-10 rounded-xl border"
                                    >
                                        +
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    onclick="hapusPaket(${targetHariAktif}, ${idx})"
                                    class="px-4 py-2 rounded-xl bg-red-50 text-red-600 font-bold text-sm"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        else
        {
            html += `
                <div class="border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center text-slate-400">
                    Belum ada paket dipilih
                </div>
            `;
        }

        let groupedFas = {};

        allFasilitas.forEach(f => {
            let cat = f.kategori?.nama_kategori || 'Lainnya';
            if(!groupedFas[cat])
            {
                groupedFas[cat] = [];
            }
            groupedFas[cat].push(f);
        });

        let cats = Object.keys(groupedFas);

        html += `
            <div class="mt-14 pt-10 border-t border-slate-100">
                <div class="mb-6">
                    <h3 class="font-bold text-xl text-slate-800">
                        Fasilitas Tambahan
                    </h3>

                    <p class="text-sm text-slate-400 mt-2">
                        Fasilitas dihitung per malam menginap
                    </p>
                </div>
        `;

        if(cats.length > 0)
        {
            let curIdx = activeFasCategory[targetHariAktif] || 0;

            html += `
                <div class="flex gap-3 overflow-x-auto hide-scrollbar mb-8 pb-2">
            `;

            cats.forEach((c, idx) => {
                html += `
                    <button
                        type="button"
                        onclick="changeFasCat(${targetHariAktif}, ${idx})"
                        class="px-5 py-3 rounded-2xl text-sm font-bold border shrink-0 transition
                        ${
                            idx === curIdx
                            ? 'bg-yellow-400 border-yellow-400 text-slate-900'
                            : 'bg-white border-slate-200 text-slate-600'
                        }"
                    >
                        ${c}
                    </button>
                `;
            });

            html += `</div>`;

            html += `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            `;

            groupedFas[cats[curIdx]].forEach(f => {
                let qty = stateFasilitas[targetHariAktif]?.[f.id] || 0;

                html += `
                    <div class="border rounded-3xl p-5 flex justify-between items-center gap-5
                    ${
                        qty > 0
                        ? 'border-blue-200 bg-blue-50'
                        : 'border-slate-200 bg-white'
                    }">

                        <div>
                            <h4 class="font-bold text-slate-800 mb-2">
                                ${f.nama_fasilitas}
                            </h4>

                            <p class="text-sm text-slate-500">
                                Rp ${Number(f.harga).toLocaleString('id-ID')} / malam
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                onclick="ubahQtyFas(${targetHariAktif}, ${f.id}, -1)"
                                class="w-10 h-10 rounded-xl border"
                            >
                                -
                            </button>

                            <span class="font-bold w-8 text-center">
                                ${qty}
                            </span>

                            <button
                                type="button"
                                onclick="ubahQtyFas(${targetHariAktif}, ${f.id}, 1)"
                                class="w-10 h-10 rounded-xl border"
                            >
                                +
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
        }

        html += `</div>`;

        containerKonten.innerHTML = html;

        let hiddenHtml = '';

        for(let h in statePackages)
        {
            statePackages[h].forEach(p => {
                hiddenHtml += `
                    <input type="hidden" name="paket[${h}][]" value="${p.id}">
                    <input type="hidden" name="paket_qty[${h}][${p.id}]" value="${p.qty}">
                `;
            });
        }

        for(let h in stateFasilitas)
        {
            for(let fId in stateFasilitas[h])
            {
                hiddenHtml += `
                    <input type="hidden" name="fasilitas[${h}][${fId}]" value="${stateFasilitas[h][fId]}">
                `;
            }
        }

        hiddenInputs.innerHTML = hiddenHtml;
        saveDraft();
    }

    function gantiHari(i)
    {
        targetHariAktif = i;
        renderTabs();
    }

    function changeFasCat(h, i)
    {
        activeFasCategory[h] = i;
        renderTabs();
    }

    function ubahQtyFas(h, id, v)
    {
        if(!stateFasilitas[h])
        {
            stateFasilitas[h] = {};
        }

        stateFasilitas[h][id] = Math.max(
            0,
            (stateFasilitas[h][id] || 0) + v
        );

        renderTabs();
    }

    function ubahQtyPaket(h, i, v)
    {
        statePackages[h][i].qty = Math.max(
            1,
            statePackages[h][i].qty + v
        );

        renderTabs();
    }

    function hapusPaket(h, i)
    {
        statePackages[h].splice(i, 1);
        renderTabs();
    }

    function bukaModal(h)
    {
        targetHariAktif = h;
        document.getElementById('modal-pilih-paket').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function tutupModal()
    {
        document.getElementById('modal-pilih-paket').classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('btn-close-modal').onclick = tutupModal;

    document.querySelectorAll('.item-paket-modal').forEach(el => {
        el.onclick = () => {
            if(!statePackages[targetHariAktif])
            {
                statePackages[targetHariAktif] = [];
            }

            statePackages[targetHariAktif].push({
                id: el.dataset.id,
                nama: el.dataset.nama,
                qty: 1,
                harga: el.dataset.harga,
                kapasitas: el.dataset.kapasitas
            });

            tutupModal();
            renderTabs();
        };
    });

    document.getElementById('btn-tambah-p').onclick = () => {
        document.getElementById('jumlah_pengunjung').value++;
    };

    document.getElementById('btn-kurang-p').onclick = () => {
        let i = document.getElementById('jumlah_pengunjung');
        if(i.value > 1)
        {
            i.value--;
        }
    };

    document.getElementById('btn-tambah-malam').onclick = () => {
        let input = document.getElementById('lama_menginap');
        input.value = parseInt(input.value || 0) + 1;
        updateCheckout();
        renderTabs();
    };

    document.getElementById('btn-kurang-malam').onclick = () => {
        let input = document.getElementById('lama_menginap');
        if(parseInt(input.value) > 0)
        {
            input.value = parseInt(input.value) - 1;
            updateCheckout();
            renderTabs();
        }
    };

    document.getElementById('tanggal_checkin').addEventListener('change', hitungMalam);
    document.getElementById('tanggal_checkout').addEventListener('change', hitungMalam);

    loadDraft();
    renderTabs();

    document.querySelectorAll('#form-reservasi input, #form-reservasi select, #form-reservasi textarea')
        .forEach(el => {
            el.addEventListener('input', saveDraft);
            el.addEventListener('change', saveDraft);
        });

    document.getElementById('form-reservasi').addEventListener('submit', saveDraft);

</script>

@endsection
