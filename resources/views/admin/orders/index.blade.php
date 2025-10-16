@extends('layouts.admin')

@section('title', 'Kelola Pesanan Masuk (Aktif)')

@section('content')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h1 class="text-3xl font-bold text-yellow-700 mb-6 border-b pb-2">Pesanan Aktif (Menunggu, Diproses, Diantar)</h1>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 text-base" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-base" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($activeOrders->isEmpty())
                        <div class="bg-gray-100 p-10 rounded-lg shadow-inner mt-4">
                            <p class="text-gray-600 text-center text-xl">🎉 Tidak ada pesanan aktif saat ini. Semuanya beres!</p>
                        </div>
                    @else
                        <div class="overflow-x-auto shadow-xl rounded-lg border">
                            <table class="min-w-full text-base divide-y divide-gray-200">
                                <thead class="bg-yellow-100">
                                    <tr class="text-left text-gray-700 uppercase tracking-wider text-xs">
                                        <th class="py-3 px-4 font-bold w-1/12">ID/Waktu</th>
                                        <th class="py-3 px-4 font-bold w-2/12">Pelanggan/Meja</th>
                                        <th class="py-3 px-4 font-bold w-4/12">Detail Pesanan & Catatan</th>
                                        <th class="py-3 px-4 font-bold text-right w-2/12">Total</th>
                                        <th class="py-3 px-4 text-center font-bold w-3/12">Aksi Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeOrders as $order)
                                    <tr class="hover:bg-yellow-50 border-b">
                                        
                                        <td class="py-3 px-4 align-top">
                                            <p class="text-blue-700 font-extrabold text-lg">#{{ $order->id }}</p>
                                            <span class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                                            <span class="text-xs text-red-500 block">({{ $order->created_at->diffForHumans() }})</span>
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top">
                                            <p class="text-gray-800 font-extrabold text-base">{{ $order->customer_name ?? 'Anonim' }}</p>
                                            <span class="text-sm text-gray-600">Meja: <span class="font-bold">{{ $order->table->name ?? '-' }}</span></span>
                                            {{-- MENGGUNAKAN phone_number --}}
                                            @if($order->phone_number)
                                                <span class="text-sm text-blue-600 block">📞 {{ $order->phone_number }}</span>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top text-sm">
                                            <ul class="list-disc list-inside space-y-0.5 max-h-24 overflow-y-auto mb-2">
                                                @if ($order->orderItems->isEmpty())
                                                    <li class="text-red-500 font-bold">❌ Item Pesanan Kosong!</li>
                                                @else
                                                    @foreach($order->orderItems as $item)
                                                        <li class="text-gray-700">
                                                            <span class="font-bold">{{ $item->quantity }}x</span> 
                                                            {{ $item->menu?->name ?? 'Menu Hilang' }}
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                            {{-- MENGGUNAKAN notes --}}
                                            @if($order->notes)
                                                <div class="bg-red-50 p-2 rounded border border-red-300 mt-2">
                                                    <p class="text-xs font-bold text-red-600">CATATAN OPSIONAL:</p>
                                                    <p class="text-sm text-red-700 italic">{{ $order->notes }}</p>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top font-extrabold text-right text-green-700 text-lg whitespace-nowrap">
                                            <p>Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            <span class="block text-xs text-gray-500 font-normal">({{ ucfirst($order->payment?->method ?? 'Belum Bayar') }})</span>
                                        </td>

                                        <td class="py-3 px-4 align-top text-center space-y-2">
                                            
                                            @if ($order->status === 'pending' && $order->payment?->method === 'cash')
                                                <p class="text-sm font-semibold text-red-500 mb-2">Konfirmasi Pembayaran?</p>
                                                
                                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline-block mr-1" onsubmit="return confirm('KONFIRMASI: Pesanan #{{ $order->id }} DIBAYAR Tunai & siap diproses?')">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="processing"> 
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-3 rounded text-sm transition duration-150">
                                                        ✅ Bayar & Proses
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin MEMBATALKAN pesanan #{{ $order->id }}?')">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="canceled"> 
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1.5 px-3 rounded text-sm transition duration-150">
                                                        ❌ Batalkan
                                                    </button>
                                                </form>

                                            @else
                                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" onchange="this.submit()">
                                                    @csrf @method('PATCH')

                                                    @php
                                                        $availableStatuses = ['pending', 'processing', 'delivering', 'completed', 'canceled'];
                                                        
                                                        if (in_array($order->status, ['processing', 'delivering', 'completed'])) {
                                                            $availableStatuses = array_diff($availableStatuses, ['pending']);
                                                        }
                                                        if (in_array($order->status, ['completed', 'canceled'])) {
                                                            $availableStatuses = [$order->status];
                                                        }
                                                    @endphp

                                                    <select name="status" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold"
                                                        @if(in_array($order->status, ['completed', 'canceled'])) disabled @endif>
                                                        
                                                        @foreach($availableStatuses as $status)
                                                            @php
                                                                $isSelected = $order->status === $status;
                                                                $statusColor = match ($status) {
                                                                    'pending' => 'text-yellow-700', 'processing' => 'text-blue-700', 
                                                                    'delivering' => 'text-indigo-700', 'completed' => 'text-green-700', 
                                                                    'canceled' => 'text-red-700', default => 'text-gray-700',
                                                                };
                                                            @endphp
                                                            <option value="{{ $status }}" {{ $isSelected ? 'selected' : '' }} class="{{ $statusColor }}">
                                                                {{ strtoupper($status) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    <span class="text-xs font-bold px-3 py-1 rounded w-full mt-2 inline-block 
                                                        @if($order->status == 'pending') bg-yellow-400 text-yellow-900 
                                                        @elseif($order->status == 'processing') bg-blue-100 text-blue-800 
                                                        @elseif($order->status == 'delivering') bg-indigo-100 text-indigo-800 
                                                        @elseif($order->status == 'completed') bg-green-100 text-green-800 
                                                        @elseif($order->status == 'canceled') bg-red-100 text-red-800 
                                                        @endif">
                                                        STATUS: {{ strtoupper($order->status) }}
                                                    </span>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const checkUrl = "{{ route('admin.orders.checkNew') }}";
    const titleElement = document.querySelector('h1');
    // Dapatkan hitungan awal pesanan pending (kasus tunai) saat halaman dimuat
    // Kita menggunakan $activeOrders yang sudah ada di Controller
    let initialPendingCount = {{ $activeOrders->where('status', 'pending')->count() }};

    function startPolling() {
        setInterval(async () => {
            try {
                // Lakukan AJAX request (Fetch)
                const response = await fetch(checkUrl);
                const data = await response.json();
                
                const currentPendingCount = data.new_count;

                // LOGIKA UTAMA: Cek apakah ada pesanan pending baru yang masuk
                if (currentPendingCount > initialPendingCount) {
                    
                    const newOrdersCount = currentPendingCount - initialPendingCount;
                    
                    // 1. Beri notifikasi ke Admin
                    alert(`🔔 PESANAN BARU MASUK! ${newOrdersCount} pesanan menunggu konfirmasi.`);
                    
                    // 2. Update hitungan awal dan paksa refresh
                    initialPendingCount = currentPendingCount;
                    window.location.reload(); 

                } else if (currentPendingCount < initialPendingCount) {
                    // Jika pesanan berkurang (mungkin diproses/dibatalkan admin lain), refresh juga.
                    initialPendingCount = currentPendingCount;
                    window.location.reload(); 
                }

            } catch (error) {
                console.error("Gagal melakukan polling:", error);
            }
        }, 8000); // Polling setiap 8 detik
    }

    // Pastikan skrip dimulai setelah semua elemen dimuat
    document.addEventListener('DOMContentLoaded', startPolling);
</script>
@endsection