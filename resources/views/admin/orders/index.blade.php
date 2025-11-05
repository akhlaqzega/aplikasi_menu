@extends('layouts.admin')

@section('title', 'Kelola Pesanan Masuk (Aktif)')

@section('content')

    @php
        // Fungsi untuk mengembalikan kelas CSS berdasarkan status
        function getStatusColorClass($status)
        {
            return match ($status) {
                'pending' => 'bg-amber-100 text-amber-800 border-amber-400',
                'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
                'delivering' => 'bg-blue-100 text-blue-800 border-blue-400',
                'completed' => 'bg-green-100 text-green-800 border-green-400',
                'canceled' => 'bg-red-100 text-red-800 border-red-400',
                default => 'bg-gray-100 text-gray-800 border-gray-400',
            };
        }
    @endphp

    <div class="py-2">

        {{-- Header & Total Aktif --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-receipt text-purple-600 mr-2"></i>
                Pesanan Aktif ({{ $activeOrders->count() }})
            </h1>
            <p class="text-sm text-gray-500 mt-1">Daftar semua pesanan yang sedang menunggu atau diproses.</p>
        </div>

        {{-- Pesan Sukses/Error --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-base"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-base"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($activeOrders->isEmpty())
            <div class="bg-purple-50 p-10 rounded-lg shadow-inner mt-4 border border-purple-200">
                <p class="text-gray-600 text-center text-xl">🎉 Tidak ada pesanan aktif saat ini. Semuanya beres!</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-xl rounded-lg border border-gray-200">
                <table
                    class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg overflow-hidden shadow-sm">
                    <thead class="bg-purple-100">
                        <tr class="text-purple-800 uppercase tracking-wider text-xs text-center">
                            <th class="py-3 px-4 font-bold w-1/12">No</th>
                            <th class="py-3 px-4 font-bold w-1/12">ID Pesanan</th>
                            <th class="py-3 px-4 font-bold w-3/12">Pelanggan</th>
                            <th class="py-3 px-4 font-bold w-1/12">Meja</th>
                            <th class="py-3 px-4 font-bold w-2/12">Total</th>
                            <th class="py-3 px-4 font-bold w-4/12">Status & Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200 text-center">
                        @foreach ($activeOrders as $order)
                            <tr class="hover:bg-purple-50 transition-colors duration-150">
                                {{-- No --}}
                                <td class="py-3 px-4 align-middle text-purple-700 font-extrabold text-base">
                                    #{{ $loop->iteration }}
                                </td>

                                {{-- ID Pesanan --}}
                                <td class="py-3 px-4 align-middle">
                                    <p class="text-gray-600 font-semibold text-sm tracking-wide">
                                        {{ $order->order_number }}
                                    </p>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="py-3 px-4 align-middle text-base font-bold text-gray-800">
                                    {{ $order->customer_name ?? 'Anonim' }}
                                    <p class="text-xs text-gray-500 font-normal mt-1">
                                    </p>
                                </td>

                                {{-- Meja --}}
                                <td class="py-3 px-4 align-middle text-base font-bold text-purple-600">
                                    {{ $order->table->name ?? '-' }}
                                </td>

                                {{-- Total --}}
                                <td class="py-3 px-4 align-middle">
                                    <p class="font-extrabold text-lg text-green-700">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-gray-500 font-normal mt-1">
                                        {{ ucfirst($order->payment?->method ?? 'Belum Bayar') }}
                                    </p>
                                </td>

                                {{-- Status & Aksi --}}
                                <td class="py-3 px-4 align-middle space-y-3">
                                    {{-- Baris 1: Tombol aksi utama --}}
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        {{-- Tombol Detail --}}
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition duration-150 shadow-md">
                                            <i class="fas fa-search-plus mr-1"></i> Detail
                                        </a>

                                        {{-- Tombol Cetak --}}
                                        @if (in_array($order->status, ['processing', 'delivering', 'completed']))
                                            <a href="{{ route('admin.order.print', $order->id) }}" target="_blank"
                                                onclick="window.open(this.href, 'Struk', 'width=350,height=600').print(); return false;"
                                                class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-1.5 px-3 rounded-lg text-sm transition duration-150 shadow-md">
                                                <i class="fas fa-print mr-1"></i> Cetak
                                            </a>
                                        @endif

                                        {{-- Konfirmasi Tunai --}}
                                        @if ($order->status === 'pending' && $order->payment?->method === 'cash')
                                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('KONFIRMASI: Pesanan #{{ $order->id }} dibayar tunai dan siap diproses?')"
                                                class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-3 rounded-lg text-sm transition duration-150 shadow-md">
                                                    <i class="fas fa-check mr-1"></i> Proses
                                                </button>
                                            </form>

                                            {{-- Dropdown Ubah Status --}}
                                        @elseif (!in_array($order->status, ['completed', 'canceled']))
                                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                                method="POST" onchange="this.submit()" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status"
                                                    class="block py-1.5 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-semibold">
                                                    <option value="" disabled selected>Ubah Status...</option>

                                                    @if ($order->status === 'processing')
                                                        <option value="delivering">🍽️ Diantar/Siap</option>
                                                        <option value="completed">✅ Selesai</option>
                                                        <option value="canceled">❌ Batalkan</option>
                                                    @elseif ($order->status === 'delivering')
                                                        <option value="completed">✅ Selesai</option>
                                                        <option value="processing">⚙️ Kembali ke Proses</option>
                                                        <option value="canceled">❌ Batalkan</option>
                                                    @endif
                                                </select>
                                            </form>

                                            {{-- Final Status --}}
                                        @else
                                            <span
                                                class="text-sm font-bold py-1.5 px-3 rounded-lg border {{ getStatusColorClass($order->status) }}">
                                                TRANSAKSI FINAL
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Baris 2: Status Aktif --}}
                                    <div class="flex justify-center items-center">
                                        <span
                                            class="text-xs font-bold px-3 py-1 rounded-full border {{ getStatusColorClass($order->status) }}">
                                            Status: {{ strtoupper($order->status) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
