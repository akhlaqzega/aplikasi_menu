@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 py-16 text-center">
        
        {{-- Mengubah container dari bg-white menjadi bg-zinc-800 dengan border amber --}}
        <div class="bg-zinc-800 rounded-xl shadow-2xl p-8 max-w-xl mx-auto border border-amber-400/20">
            
            {{-- Kita HAPUS perhitungan PHP ($isCanceled) di sini, karena sudah diurus oleh Controller --}}

            {{-- === BAGIAN: PESANAN MENUNGGU PEMBAYARAN === --}}
            {{-- Hidden jika status di DB sudah canceled --}}
            <div id="pending-message-section" class="{{ $order->status === 'canceled' ? 'hidden' : '' }}">
                
                {{-- Judul diubah ke amber --}}
                <h1 class="text-4xl font-extrabold text-amber-400 mb-6">Pembayaran Tunai</h1>
                
                {{-- Ikon Notifikasi --}}
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-400/20 mb-6">
                    <i class="fas fa-money-bill-wave text-3xl text-amber-400"></i>
                </div>
                
                <p class="text-zinc-300 mb-4">
                    Pesanan Anda telah berhasil dibuat dengan nomor <strong class="text-white">#{{ $order->id }}</strong>.
                </p>
                <p class="text-zinc-300 mb-6">
                    Total pembayaran: <strong class="text-amber-400 text-xl">Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>.
                </p>
                <p class="text-xl font-bold text-white mb-6">
                    Mohon segera menuju kasir dan sebutkan nomor pesanan Anda untuk menyelesaikan pembayaran.
                </p>

                {{-- Kotak Countdown diubah ke tema amber --}}
                <div class="bg-amber-900/30 border-l-4 border-amber-400 text-amber-100 p-4 mb-8" role="alert">
                    <p class="font-bold">Waktu Tersisa</p>
                    <p id="countdown" class="text-3xl font-bold">Menghitung...</p>
                    <p class="text-sm mt-1">Pesanan Anda akan dibatalkan secara otomatis jika waktu habis.</p>
                </div>

                {{-- Tombol Kembali disesuaikan ke tema gelap/zinc --}}
                <a href="{{ route('customer.menu.index') }}"
                    class="inline-block bg-zinc-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-zinc-600 transition transform hover:scale-105">
                    Kembali ke Menu
                </a>
            </div>

            {{-- === BAGIAN: PESANAN DIBATALKAN === --}}
            {{-- Ditampilkan jika status di DB sudah canceled --}}
            <div id="canceled-message-section" class="{{ $order->status === 'canceled' ? '' : 'hidden' }}">
                {{-- Ikon Batal --}}
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-400/20 mb-6">
                    <i class="fas fa-times-circle text-3xl text-red-500"></i>
                </div>
                
                <h1 class="text-4xl font-extrabold text-red-500 mb-4">Pesanan Dibatalkan</h1>
                <p class="text-zinc-300 mb-6">
                    Waktu pembayaran untuk pesanan <strong class="text-white">#{{ $order->id }}</strong> telah habis.
                </p>
                <p class="text-xl font-semibold text-zinc-300 mb-6">
                    Silakan kembali ke menu untuk memesan lagi.
                </p>
                
                {{-- Tombol Pesan Lagi disesuaikan ke amber --}}
                <a href="{{ route('customer.menu.index') }}" {{-- Ubah rute ke customer.menu.index jika rute clear-session tidak ada --}}
                    class="inline-block bg-amber-400 text-zinc-900 px-8 py-3 rounded-full font-bold hover:bg-amber-300 transition transform hover:scale-105 shadow-lg shadow-amber-500/30">
                    Pesan Lagi
                </a>
            </div>
            
            {{-- === SCRIPT COUNTDOWN DENGAN WAKTU DARI SERVER === --}}
            @if ($order->status === 'pending')
                <script>
                    // Ambil waktu kedaluwarsa (ms) dari Controller
                    const EXPIRATION_TIME = {{ $expirationTimestamp ?? 0 }}; 
                    const pendingSection = document.getElementById('pending-message-section');
                    const canceledSection = document.getElementById('canceled-message-section');
                    const countdownDisplay = document.getElementById('countdown');

                    const countdownInterval = setInterval(() => {
                        const now = new Date().getTime();
                        // Hitung selisih antara waktu kedaluwarsa dengan waktu sekarang
                        const distance = EXPIRATION_TIME - now; 

                        if (distance <= 0) {
                            clearInterval(countdownInterval);
                            countdownDisplay.textContent = "Waktu Habis!";
                            
                            // Visualkan pembatalan di sisi client, dan paksa refresh untuk trigger server check
                            pendingSection.classList.add('hidden');
                            canceledSection.classList.remove('hidden');
                            
                            // PENTING: Refresh halaman agar Server side check di Controller ter-trigger
                            // jika user mencoba mengakses halaman ini lagi.
                            setTimeout(() => {
                                window.location.reload(); 
                            }, 3000); 

                        } else {
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            
                            // Format waktu (misalnya: 00 menit 15 detik)
                            const formattedTime = `${String(minutes).padStart(2, '0')} menit ${String(seconds).padStart(2, '0')} detik`;
                            countdownDisplay.textContent = formattedTime;
                        }
                    }, 1000);
                </script>
            @endif
        </div>
    </div>
@endsection