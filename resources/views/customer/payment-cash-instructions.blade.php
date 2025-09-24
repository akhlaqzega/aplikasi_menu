@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 text-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-xl mx-auto">
            @php
                $orderCreatedAt = new DateTime($order->created_at);
                $expiryTime = (clone $orderCreatedAt)->add(new DateInterval('PT15S')); // 10 detik
                $now = new DateTime();
                $isCanceled = $now > $expiryTime;
            @endphp

            <div id="pending-message-section" class="{{ ($order->status === 'canceled' || $isCanceled) ? 'hidden' : '' }}">
                <h1 class="text-4xl font-bold text-blue-800 mb-4">Pembayaran Tunai</h1>
                <p class="text-gray-700 mb-6">
                    Pesanan Anda telah berhasil dibuat dengan nomor <strong>#{{ $order->id }}</strong>.
                </p>
                <p class="text-gray-700 mb-6">
                    Total pembayaran: <strong>Rp{{ number_format($order->total_price, 2, ',', '.') }}</strong>.
                </p>
                <p class="text-xl font-semibold text-blue-600 mb-4">
                    Mohon segera menuju kasir dan sebutkan nomor pesanan Anda untuk menyelesaikan pembayaran.
                </p>

                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Waktu Tersisa</p>
                    <p id="countdown" class="text-2xl font-bold"></p>
                    <p class="text-sm mt-1">Pesanan Anda akan dibatalkan secara otomatis jika waktu habis.</p>
                </div>

                <a href="{{ route('customer.menu.index') }}"
                   class="inline-block bg-gray-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-600 transition">
                    Kembali ke Menu
                </a>
            </div>

            <div id="canceled-message-section" class="{{ ($order->status === 'canceled' || $isCanceled) ? '' : 'hidden' }}">
                <h1 class="text-4xl font-bold text-red-600 mb-4">Pesanan Dibatalkan</h1>
                <p class="text-gray-700 mb-6">
                    Waktu pembayaran untuk pesanan <strong>#{{ $order->id }}</strong> telah habis.
                </p>
                <p class="text-xl font-semibold text-gray-700 mb-4">
                    Silakan kembali ke menu untuk memesan lagi.
                </p>
                <a href="{{ route('customer.order.clear-session') }}"
                   class="inline-block bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition">
                    Pesan Lagi
                </a>
            </div>
            
            @if ($order->status !== 'canceled' && !$isCanceled)
                <script>
                    const orderCreatedAt = new Date("{{ $order->created_at->toIso8601String() }}").getTime();
                    const tenSecondsInMs = 15 * 1000;
                    const pendingSection = document.getElementById('pending-message-section');
                    const canceledSection = document.getElementById('canceled-message-section');
                    const countdownDisplay = document.getElementById('countdown');

                    const countdownInterval = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = (orderCreatedAt + tenSecondsInMs) - now;

                        if (distance <= 0) {
                            clearInterval(countdownInterval);
                            pendingSection.classList.add('hidden');
                            canceledSection.classList.remove('hidden');
                        } else {
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            countdownDisplay.textContent = `${minutes} menit ${seconds} detik`;
                        }
                    }, 1000);
                </script>
            @endif
        </div>
    </div>
@endsection