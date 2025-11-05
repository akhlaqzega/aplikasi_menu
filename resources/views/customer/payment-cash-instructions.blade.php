@extends('layouts.customer')

@section('content')
    
    <style>
        /* CSS Tambahan untuk Animasi Pop-up / Fade In */
        .animated-box {
            animation: fadeInScale 0.6s ease-out forwards;
            opacity: 0;
            transform: scale(0.95);
        }

        @keyframes fadeInScale {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <div class="container mx-auto px-4 py-16 text-center">
        
        {{-- CONTAINER UTAMA --}}
        <div class="bg-zinc-800 rounded-xl shadow-2xl p-8 max-w-xl mx-auto border border-amber-400/20 animated-box">
            
            {{-- Menggunakan if/elseif untuk memastikan hanya satu blok yang dimuat --}}
            @if ($order->status === 'pending')
                
                {{-- === KONDISI 1: PENDING (Tunai) === --}}
                <div id="pending-message-section">
                    
                    <h1 class="text-4xl font-extrabold text-amber-400 mb-6">Pembayaran Tunai</h1>
                    
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-400/20 mb-6 pulse-animation">
                        <i class="fas fa-money-bill-wave text-3xl text-amber-400"></i>
                    </div>
                    
                    <p class="text-zinc-300 mb-4">
                        Pesanan Anda telah berhasil dibuat dengan nomor <strong class="text-white">#{{ $order->order_number }}</strong>.
                    </p>
                    <p class="text-zinc-300 mb-6">
                        Total pembayaran: <strong class="text-amber-400 text-xl">Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong>.
                    </p>
                    <p class="text-xl font-bold text-white mb-6">
                        Mohon segera menuju kasir dan sebutkan nomor pesanan Anda untuk menyelesaikan pembayaran.
                    </p>

                    {{-- Kotak Countdown --}}
                    <div class="bg-amber-900/30 border-l-4 border-amber-400 text-amber-100 p-4 mb-8" role="alert">
                        <p class="font-bold">Waktu Tersisa</p>
                        <p id="countdown" class="text-3xl font-bold">Menghitung...</p>
                        <p class="text-sm mt-1">Pesanan Anda akan dibatalkan secara otomatis jika waktu habis.</p>
                    </div>

                    {{-- Tombol Kembali --}}
                    <a href="{{ route('customer.menu.index') }}"
                        class="inline-block bg-zinc-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-zinc-600 transition transform hover:scale-105">
                        Kembali ke Menu
                    </a>
                </div>

            @elseif ($order->status === 'processing' || $order->status === 'delivering' || $order->status === 'completed')
                
                {{-- KONDISI 2: SUKSES/DIPROSES (Jika Polling Admin Sukses) --}}
                {{-- Catatan: Script ini akan dijalankan karena ada reload dari Polling di bawah --}}
                <h1 class="text-4xl font-extrabold text-green-500 mb-4">Pesanan Sedang Diproses!</h1>
                <p class="text-zinc-300">Harap tunggu sebentar, kami mengarahkan Anda...</p>
                <script> 
                    // Redirect segera ke halaman konfirmasi final setelah polling berhasil
                    window.location.href = "{{ route('customer.order.online-confirmed', $order) }}"; 
                </script> 

            @elseif ($order->status === 'canceled')

                {{-- === KONDISI 3: DIBATALKAN (TIMEOUT/ADMIN) === --}}
                <div id="canceled-message-section">
                    {{-- Ikon Batal --}}
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-400/20 mb-6">
                        <i class="fas fa-times-circle text-3xl text-red-500"></i>
                    </div>
                    
                    <h1 class="text-4xl font-extrabold text-red-500 mb-4">Pesanan Dibatalkan</h1>
                    <p class="text-zinc-300 mb-6">
                        Pesanan Anda dengan nomor <strong class="text-white">#{{ $order->order_number }}</strong> telah dibatalkan karena batas waktu pembayaran habis.
                    </p>
                    <p class="text-xl font-semibold text-zinc-300 mb-6">
                        Silakan kembali ke menu untuk memesan lagi.
                    </p>
                    
                    {{-- Tombol Pesan Lagi --}}
                    <a href="{{ route('customer.menu.index') }}" 
                        class="inline-block bg-amber-400 text-zinc-900 px-8 py-3 rounded-full font-bold hover:bg-amber-300 transition transform hover:scale-105 shadow-lg shadow-amber-500/30">
                        Pesan Lagi
                    </a>
                </div>

            @endif
            
            
            {{-- === SCRIPT COUNTDOWN & POLLING (FORCED RELOAD) === --}}
            {{-- Script ini hanya berjalan jika status saat ini adalah PENDING --}}
            @if ($order->status === 'pending')
                <script>
                    const EXPIRATION_TIME = {{ $expirationTimestamp ?? 0 }}; 
                    const countdownDisplay = document.getElementById('countdown');
                    
                    const checkUrl = "{{ route('customer.order.status.check', $order) }}"; 
                    const TARGET_STATUS = 'processing'; 

                    let pollingInterval;

                    // --- COUNTDOWN LOGIC (Timeout memicu RELOAD) ---
                    const countdownInterval = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = EXPIRATION_TIME - now; 

                        if (distance <= 0) {
                            clearInterval(countdownInterval); 
                            clearInterval(pollingInterval); 
                            
                            // KRITIS: Forced Reload - Ini akan memaksa Controller PHP membatalkan pesanan.
                            window.location.reload(); 

                        } else {
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            
                            const formattedTime = `${String(minutes).padStart(2, '0')} menit ${String(seconds).padStart(2, '0')} detik`;
                            countdownDisplay.textContent = formattedTime;
                        }
                    }, 1000);


                    // --- POLLING LOGIC (Konfirmasi Admin memicu RELOAD) ---
                    pollingInterval = setInterval(async () => {
                        try {
                            const response = await fetch(checkUrl);
                            const data = await response.json();

                            // Jika Admin sudah konfirmasi, atau membatalkan, reload
                            if (data.order_status === TARGET_STATUS || data.payment_status === 'paid' || data.order_status === 'canceled') {
                                
                                clearInterval(countdownInterval); 
                                clearInterval(pollingInterval);
                                // Trigger refresh ke halaman yang benar (sukses atau batal)
                                window.location.reload(); 
                            }
                            
                        } catch (error) {
                            console.error("Polling gagal:", error);
                        }
                    }, 3000);
                </script>
            @endif
        </div>
    </div>
@endsection