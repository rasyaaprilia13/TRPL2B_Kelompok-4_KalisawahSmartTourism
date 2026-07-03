@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 pt-32 pb-40">
    <div class="max-w-4xl mx-auto">

        {{-- NOTIF --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 p-4 rounded-2xl font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800">
                Pembayaran Berhasil
            </h1>

            <p class="text-slate-500 mt-2">
                Silakan simpan kode booking dan upload bukti pembayaran Anda
            </p>
        </div>

        {{-- CARD INFO --}}
        <div class="bg-white border rounded-3xl p-8 shadow-sm mb-8">

            <h2 class="font-bold text-xl mb-6">Detail Booking</h2>

            <div class="space-y-4 text-sm">

                <div class="flex justify-between">
                    <span>Kode Booking</span>
                    <span class="font-bold">{{ $booking->kode_booking }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Nama</span>
                    <span class="font-bold">{{ $booking->nama_pemesan }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Status</span>
                    <span class="font-bold text-amber-500 uppercase">
                        {{ $booking->status_booking }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Total Pembayaran</span>
                    <span class="font-extrabold text-blue-600">
                        Rp {{ number_format($booking->total_harga_final,0,',','.') }}
                    </span>
                </div>

            </div>
        </div>

        {{-- UPLOAD BUKTI --}}
        <div class="bg-white border rounded-3xl p-8 shadow-sm">

            <h2 class="font-bold text-xl mb-6">Upload Bukti Pembayaran</h2>

            <form action="{{ route('booking.upload-bukti', $booking->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- FILE --}}
                <div class="mb-5">
                    <label class="font-semibold text-sm">Foto Bukti Transfer</label>

                    <input type="file"
                           name="bukti_pembayaran"
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full mt-2 border rounded-xl p-3"
                           required>

                    <p class="text-xs text-slate-500 mt-2">
                        Format: JPG, PNG. Maksimal 2MB.
                    </p>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-2xl">
                    Upload Bukti
                </button>

            </form>

        </div>

    </div>
</div>

<script>
    try {
        localStorage.removeItem('booking_form_draft_v1');
    } catch (e) {
        console && console.warn && console.warn('clear booking draft error', e);
    }
</script>

@endsection