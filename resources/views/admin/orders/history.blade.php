@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')

    @php
        // Fungsi untuk mengembalikan kelas CSS berdasarkan status
        function getStatusColorClass($status)
        {
            return match ($status) {
                'completed' => 'bg-green-100 text-green-800 border-green-400',
                'canceled' => 'bg-red-100 text-red-800 border-red-400',
                default => 'bg-gray-100 text-gray-800 border-gray-400',
            };
        }
    @endphp

    <div class="py-2">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-history text-purple-600 mr-2"></i>
                Riwayat Pesanan
            </h1>
            <p class="text-sm text-gray-500 mt-1">Arsip semua pesanan yang sudah selesai dan dibatalkan.</p>
        </div>

        {{-- =========================================== --}}
        {{-- BAGIAN 1: PESANAN SELESAI (COMPLETED) --}}
        {{-- =========================================== --}}
        <h2 class="text-2xl font-bold text-green-600 mb-4 border-b pb-2">
            <i class="fas fa-check-circle mr-2"></i> Pesanan Berhasil Diselesaikan ({{ $completedOrders->count() }})
        </h2>

        @if ($completedOrders->isEmpty())
            <div class="bg-green-50 p-6 rounded-lg shadow-inner border border-green-200 mb-8">
                <p class="text-gray-600 text-center">Belum ada pesanan yang berhasil diselesaikan.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-xl rounded-lg border border-gray-200 mb-8">
                <table
                    class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                    <thead class="bg-green-100">
                        <tr class="text-green-800 uppercase tracking-wider text-xs text-center">
                            <th class="py-3 px-4 font-bold w-1/12">No</th>
                            <th class="py-3 px-4 font-bold w-2/12">Waktu Selesai</th>
                            <th class="py-3 px-4 font-bold w-2/12">Pelanggan/Meja</th>
                            <th class="py-3 px-4 font-bold w-2/12">Pembayaran</th>
                            <th class="py-3 px-4 font-bold w-2/12">Total</th>
                            <th class="py-3 px-4 font-bold w-3/12">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 text-center">
                        @foreach ($completedOrders as $order)
                            <tr class="hover:bg-green-50 transition-colors duration-150">
                                {{-- ID --}}
                                <td class="py-3 px-4 align-middle text-green-700 font-extrabold text-base">
                                    #{{ $order->id }}
                                </td>

                                {{-- Waktu Selesai --}}
                                <td class="py-3 px-4 align-middle text-sm text-gray-600">
                                    {{ $order->updated_at->format('d M Y H:i') }}
                                </td>

                                {{-- Pelanggan/Meja --}}
                                <td class="py-3 px-4 align-middle text-sm">
                                    <p class="font-bold text-gray-800">{{ $order->customer_name ?? 'Anonim' }}</p>
                                    <span class="text-xs text-purple-600 font-semibold">
                                        Meja: {{ $order->table->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Pembayaran --}}
                                <td class="py-3 px-4 align-middle">
                                    <span
                                        class="text-xs font-bold px-3 py-1 rounded-full border bg-green-100 text-green-700 border-green-400">
                                        {{ strtoupper($order->payment->status ?? 'PAID') }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ ucfirst($order->payment?->method ?? 'Tunai') }}
                                    </p>
                                </td>

                                {{-- Total --}}
                                <td class="py-3 px-4 align-middle font-extrabold text-lg text-green-700">
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3 px-4 align-middle">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition duration-150 shadow-md">
                                        <i class="fas fa-search-plus mr-1"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        @endif


        {{-- =========================================== --}}
        {{-- BAGIAN 2: PESANAN DIBATALKAN (CANCELED) --}}
        {{-- =========================================== --}}
        <h2 class="text-2xl font-bold text-red-600 mb-4 border-b pb-2 mt-8">
            <i class="fas fa-times-circle mr-2"></i> Pesanan Dibatalkan ({{ $canceledOrders->count() }})
        </h2>

        @if ($canceledOrders->isEmpty())
            <div class="bg-red-50 p-6 rounded-lg shadow-inner border border-red-200">
                <p class="text-gray-600 text-center">Tidak ada pesanan yang dibatalkan pada riwayat.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-xl rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
    <thead class="bg-red-100">
        <tr class="text-red-800 uppercase tracking-wider text-xs text-center">
            <th class="py-3 px-4 font-bold w-1/12">No</th>
            <th class="py-3 px-4 font-bold w-2/12">Waktu Dibatalkan</th>
            <th class="py-3 px-4 font-bold w-2/12">Pelanggan/Meja</th>
            <th class="py-3 px-4 font-bold w-2/12">Metode Bayar</th>
            <th class="py-3 px-4 font-bold w-2/12">Nilai Kerugian</th>
            <th class="py-3 px-4 font-bold w-3/12">Aksi</th>
        </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-200 text-center">
        @foreach ($canceledOrders as $order)
            <tr class="hover:bg-red-50 transition-colors duration-150">
                {{-- ID --}}
                <td class="py-3 px-4 align-middle text-red-700 font-extrabold text-base">
                    #{{ $order->id }}
                </td>

                {{-- Waktu Dibatalkan --}}
                <td class="py-3 px-4 align-middle text-sm text-gray-600">
                    {{ $order->updated_at->format('d M Y H:i') }}
                </td>

                {{-- Pelanggan/Meja --}}
                <td class="py-3 px-4 align-middle text-sm">
                    <p class="font-bold text-gray-800">{{ $order->customer_name ?? 'Anonim' }}</p>
                    <span class="text-xs text-purple-600 font-semibold">
                        Meja: {{ $order->table->name ?? '-' }}
                    </span>
                </td>

                {{-- Metode Bayar --}}
                <td class="py-3 px-4 align-middle">
                    <span class="text-xs font-bold px-3 py-1 rounded-full border bg-red-100 text-red-700 border-red-400">
                        {{ ucfirst($order->payment->method ?? 'Tunai') }}
                    </span>
                </td>

                {{-- Nilai Kerugian --}}
                <td class="py-3 px-4 align-middle font-extrabold text-lg text-red-700">
                    Rp{{ number_format($order->total_price, 0, ',', '.') }}
                </td>

                {{-- Aksi --}}
                <td class="py-3 px-4 align-middle">
                    <a href="{{ route('admin.orders.show', $order->id) }}"
                       class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition duration-150 shadow-md">
                        <i class="fas fa-search-plus mr-1"></i> Lihat Detail
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

            </div>
        @endif
    </div>
@endsection
