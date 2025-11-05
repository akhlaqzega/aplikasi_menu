@extends('layouts.customer')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    {{-- CONTAINER UTAMA --}}
    <div class="bg-zinc-800 rounded-xl shadow-2xl p-8 max-w-md mx-auto border border-indigo-500/20">
        
        <h1 class="text-3xl font-bold mb-4 text-white">Konfirmasi & Bayar</h1>
        
        <div class="mb-6 p-4 bg-zinc-900 rounded-lg text-left border border-zinc-700 max-h-60 overflow-y-auto custom-scrollbar">
            <h2 class="text-sm font-bold text-indigo-400 mb-2 uppercase">Rincian Pesanan:</h2>
            <ul class="space-y-2">
                @php
                    $orderItems = $order->orderItems ?? [];
                @endphp

                @forelse ($orderItems as $item)
                    <li class="flex justify-between items-center text-zinc-300 border-b border-zinc-700/50 pb-1">
                        <span class="font-medium text-white text-sm">
                            {{ $item->quantity }}x {{ $item->menu?->name ?? 'Item Menu' }}
                        </span>
                        <span class="text-xs text-indigo-300">
                            Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </span>
                    </li>
                @empty
                    <li class="text-red-400 text-sm">Tidak ada item ditemukan.</li>
                @endforelse
            </ul>
        </div>
        <p class="mb-6 text-zinc-300">
            Anda akan membayar:
            <strong class="text-indigo-400 text-2xl">
                Rp{{ number_format($order->total_price, 0, ',', '.') }}
            </strong>
        </p>

        <button id="pay-button"
            class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-indigo-700 transition shadow-lg">
            Bayar Sekarang
        </button>

        {{-- KRITIS: Tombol diubah menjadi "Batalkan Pesanan" dengan warna peringatan --}}
        <a href="{{ route('customer.order.cancel-online', $order) }}"
           class="block mt-6 text-red-400 hover:text-red-500 transition font-semibold">
            <i class="fas fa-times-circle mr-1"></i> Batalkan Pesanan
        </a>
    </div>
</div>
@endsection

@push('scripts')
{{-- (Scripts Midtrans Snap) --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
window.addEventListener('load', function() {
    const snapToken = "{{ $snapToken }}";

    if (!snapToken) {
        console.error("Midtrans Error: Snap Token tidak ditemukan. Periksa Controller.");
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.innerText = "Error: Token Gagal";
            payButton.disabled = true;
        }
        return;
    }

    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            if (typeof snap === 'undefined') {
                 console.error("Midtrans Snap object is not defined yet.");
                 return; 
            }
            
            snap.pay(snapToken, {
                onSuccess: function (result) {
                    console.log("Pembayaran sukses:", result);
                    window.location.href = "{{ route('customer.order.update-success', $order->id) }}";
                },
                onPending: function (result) {
                    console.log("Menunggu pembayaran:", result);
                    alert('Pembayaran sedang diproses. Silakan selesaikan melalui aplikasi Midtrans Anda.');
                },
                onError: function (result) {
                    console.error("Terjadi kesalahan:", result);
                    alert('Terjadi kesalahan saat memproses pembayaran.');
                },
                onClose: function () {
                    // Jika pengguna menutup pop-up, tidak perlu refresh.
                    // Mereka bisa menekan tombol Bayar Sekarang lagi atau Batalkan Pesanan
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran. Klik "Bayar Sekarang" lagi untuk melanjutkan.');
                }
            });
        });
    }
});
</script>
@endpush