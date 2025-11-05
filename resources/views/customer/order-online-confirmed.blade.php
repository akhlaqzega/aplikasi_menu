@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 py-20 text-center">
        {{-- Card disesuaikan dengan tema gelap (bg-zinc-800) dan border amber --}}
        <div class="bg-zinc-800 rounded-xl shadow-2xl p-10 max-w-xl mx-auto border border-amber-400/20">

            {{-- Ikon Sukses disesuaikan dengan warna amber --}}
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-400/20 mb-6">
                {{-- Menggunakan Font Awesome untuk konsistensi ikon --}}
                <i class="fas fa-check-circle h-10 w-10 text-amber-400"></i>
            </div>

            {{-- Judul dan Teks disesuaikan --}}
            <h1 class="text-4xl font-extrabold text-amber-400 mb-4">Pembayaran Berhasil!</h1>

            <p class="text-zinc-300 mb-6 text-lg">
                Pesanan Anda dengan nomor <strong class="text-white">#{{ $order->order_number }}</strong> telah dikonfirmasi.
            </p>

            <p class="text-xl text-zinc-200 mb-8">
                Pesanan Anda sedang kami siapkan. Terima kasih!
            </p>

            {{-- Tombol disesuaikan ke amber-400 dengan teks zinc-900 --}}
            <a href="{{ route('customer.menu.index') }}"
                class="inline-block bg-amber-400 text-zinc-900 px-8 py-3 rounded-full font-bold hover:bg-amber-300 transition-transform transform hover:scale-105 shadow-lg shadow-amber-500/30">
                Kembali ke Menu
            </a>

        </div>
    </div>
@endsection