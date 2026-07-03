@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 pt-32 pb-40">
    <div class="max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800">
                Pembayaran Booking
            </h1>

            <p class="text-slate-500 mt-2">
                Silakan pilih metode dan tipe pembayaran untuk menyelesaikan booking Anda
            </p>
        </div>

        {{-- CARD RINGKASAN --}}
        <div class="bg-white border border-slate-100 rounded-3xl p-8 mb-8 shadow-sm">

            <h2 class="font-bold text-xl mb-6 text-slate-800">
                Ringkasan Pesanan
            </h2>

            <div class="space-y-4 text-sm">

                <div class="flex justify-between">
                    <span class="text-slate-500">Kode Booking</span>
                    <span class="font-bold">{{ $booking->kode_booking }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Pemesan</span>
                    <span class="font-bold">{{ $booking->nama_pemesan }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal Check-in</span>
                    <span class="font-bold">{{ $booking->tanggal_kunjungan }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal Check-out</span>
                    <span class="font-bold">{{ $booking->tanggal_selesai }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Jumlah Orang</span>
                    <span class="font-bold">{{ $booking->jumlah_pengunjung }}</span>
                </div>

            </div>

            <hr class="my-6">

            <div class="flex justify-between text-sm">
                <span class="text-slate-500">Subtotal</span>
                <span class="font-bold text-slate-800">
                    Rp {{ number_format($booking->total_harga ?? 0,0,',','.') }}
                </span>
            </div>

            <div class="flex justify-between text-lg mt-4">
                <span class="font-bold text-slate-700">Total</span>
                <span class="font-extrabold text-blue-600">
                    Rp {{ number_format($booking->total_harga_final ?? 0,0,',','.') }}
                </span>
            </div>

        </div>

        {{-- FORM PEMBAYARAN --}}
        <form method="POST" action="{{ route('pengunjung.booking.update-payment', $booking->id) }}">
            @csrf

            <div class="bg-white border rounded-3xl p-8 shadow-sm space-y-8">

                {{-- METODE PEMBAYARAN --}}
                <div>
                    <h3 class="font-bold text-lg mb-4 text-slate-800">
                        Metode Pembayaran
                    </h3>

                    <div class="border rounded-2xl p-4 bg-blue-50 border-blue-200">
                        <label class="flex items-center gap-3 cursor-pointer">

                            <input type="radio" name="metode_pembayaran" value="transfer"
                                checked class="w-5 h-5">

                            <div>
                                <p class="font-bold text-slate-800">
                                    Transfer Bank
                                </p>
                                <p class="text-xs text-slate-500">
                                    Pembayaran hanya melalui transfer bank
                                </p>
                            </div>

                        </label>
                    </div>
                </div>

                {{-- TIPE PEMBAYARAN --}}
                <div>
                    <h3 class="font-bold text-lg mb-4 text-slate-800">
                        Tipe Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <label class="border rounded-2xl p-5 cursor-pointer hover:border-blue-500">
                            <input type="radio" name="tipe_pembayaran" value="dp" class="mr-2">
                            <span class="font-bold">DP (Down Payment)</span>

                            <p class="text-xs text-slate-500 mt-2">
                                Minimal 10% atau Rp 100.000
                            </p>
                        </label>

                        <label class="border rounded-2xl p-5 cursor-pointer hover:border-blue-500">
                            <input type="radio" name="tipe_pembayaran" value="lunas" class="mr-2" checked>
                            <span class="font-bold">Lunas</span>

                            <p class="text-xs text-slate-500 mt-2">
                                Pembayaran penuh 100%
                            </p>
                        </label>

                    </div>
                </div>

                {{-- INFO DP --}}
                <div id="info-dp" class="hidden bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
                    <p class="text-sm text-yellow-800">
                        <span class="font-bold">Info DP:</span>
                        Minimal pembayaran DP adalah <b>10%</b> dari total atau minimal <b>Rp 100.000</b>.
                    </p>
                </div>

                {{-- INFO REKENING (BARU) --}}
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                    <h4 class="font-bold text-green-800 mb-3">
                        Informasi Rekening Pembayaran
                    </h4>

                    <div class="text-sm text-green-900 space-y-1">
                        <p><span class="font-semibold">Bank:</span> BCA</p>
                        <p><span class="font-semibold">Nomor Rekening:</span> 1234567890</p>
                        <p><span class="font-semibold">Atas Nama:</span> Kalisawah Adventure</p>
                    </div>

                    <div class="mt-3 text-xs text-green-700 font-medium">
                        ⚠ Mohon dicatat: Pastikan transfer sesuai nominal dan sertakan kode booking pada keterangan transfer.
                    </div>
                </div>

                {{-- TOTAL BAYAR --}}
                <div class="bg-slate-50 border rounded-2xl p-5">

                    <div class="flex justify-between text-sm mb-2">
                        <span>Subtotal</span>
                        <span>
                            Rp {{ number_format($booking->total_harga ?? 0,0,',','.') }}
                        </span>
                    </div>

                    <div class="flex justify-between text-sm mb-2">
                        <span>DP (jika dipilih)</span>
                        <span id="dp-value">-</span>
                    </div>

                    <hr class="my-3">

                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Bayar</span>
                        <span id="total-bayar">
                            Rp {{ number_format($booking->total_harga_final ?? 0,0,',','.') }}
                        </span>
                    </div>

                </div>

                {{-- BUTTON --}}
                <button type="submit"
                    class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl">
                    Lanjutkan Pembayaran
                </button>

            </div>

        </form>

    </div>
</div>

{{-- SCRIPT --}}
<script>

const total = {{ $booking->total_harga_final ?? 0 }};

const radios = document.querySelectorAll('input[name="tipe_pembayaran"]');
const infoDp = document.getElementById('info-dp');
const dpValue = document.getElementById('dp-value');
const totalBayar = document.getElementById('total-bayar');

function formatRupiah(val)
{
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
}

radios.forEach(r => {

    r.addEventListener('change', function () {

        if(this.value === 'dp')
        {
            infoDp.classList.remove('hidden');

            let dp = Math.max(total * 0.10, 100000);

            dpValue.innerText = formatRupiah(dp);
            totalBayar.innerText = formatRupiah(dp);
        }
        else
        {
            infoDp.classList.add('hidden');

            dpValue.innerText = '-';
            totalBayar.innerText = formatRupiah(total);
        }

    });

});

</script>

@endsection