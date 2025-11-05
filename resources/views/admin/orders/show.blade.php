@extends('layouts.admin')

@section('title', 'Detail Pesanan' )

@section('content')

    <div class="py-2">
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-file-invoice text-indigo-600 mr-2"></i>
                    Detail Pesanan #{{ $order->order_number }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">Audit lengkap dan rincian transaksi.</p>
            </div>


            <div class="flex items-center gap-3">
                @if ($order->status !== 'canceled' && $order->status !== 'pending')
                    <a href="{{ route('admin.order.print', $order->id) }}" target="_blank"
                        class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm shadow-md"
                        onclick="window.open(this.href, 'Struk', 'width=350,height=600').print(); return false;">
                        <i class="fas fa-print mr-2"></i> Cetak Struk
                    </a>
                @endif 
                <a href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition text-sm shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Pesanan Aktif
                </a>
            </div>
        </div>

        {{-- STATUS & ACTION CARD --}}
        @php
            // Fungsi getStatusColorClass dari index tidak tersedia di sini, didefinisikan ulang untuk keamanan
            $status = $order->status;
            $statusColorClass = match ($status) {
                'pending' => 'bg-amber-100 text-amber-800 border-amber-400',
                'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
                'delivering' => 'bg-blue-100 text-blue-800 border-blue-400',
                'completed' => 'bg-green-100 text-green-800 border-green-400',
                'canceled' => 'bg-red-100 text-red-800 border-red-400',
                default => 'bg-gray-100 text-gray-800 border-gray-400',
            };
        @endphp

        <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">

                {{-- STATUS SAAT INI & WAKTU RELATIF --}}
                <div class="md:col-span-1">
                    <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Status Pesanan</p>
                    <span
                        class="text-base font-bold px-4 py-2 rounded-xl border inline-block w-full text-center {{ $statusColorClass }}">
                        {{ strtoupper($status) }}
                    </span>
                    <p class="text-xs text-gray-500 mt-2 text-center">Dibuat: {{ $order->created_at->diffForHumans() }}</p>
                    {{-- WAKTU RELATIF DITAMBAHKAN --}}
                </div>

                {{-- TOTAL HARGA --}}
                <div class="md:col-span-1">
                    <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Total Pembayaran</p>
                    <div class="text-3xl font-extrabold text-green-700">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </div>
                </div>

                {{-- AKSI STATUS --}}
                <div class="md:col-span-1">
                    @if (!in_array($order->status, ['completed', 'canceled']))
                        <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Ubah Status</p>
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                            onchange="this.submit()">
                            @csrf @method('PATCH')
                            <select name="status"
                                class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold">

                                <option value="{{ $order->status }}" selected>► {{ strtoupper($order->status) }} (Sekarang)
                                </option>

                                @if ($order->status === 'pending')
                                    <option value="processing">⚙️ Proses (Bayar Tunai Diterima)</option>
                                    <option value="canceled">❌ Batalkan</option>
                                @elseif ($order->status === 'processing')
                                    <option value="delivering">🍽️ Siap Diantar/Ambil</option>
                                    <option value="completed">✅ Selesai</option>
                                @elseif ($order->status === 'delivering')
                                    <option value="completed">✅ Selesai</option>
                                @endif
                                @if (!in_array($order->status, ['pending', 'completed', 'canceled']))
                                    <option value="canceled">❌ Batalkan</option>
                                @endif
                            </select>
                        </form>
                    @else
                        <div class="text-center py-2.5 px-3 rounded-lg border {{ $statusColorClass }}">
                            <span class="font-bold text-sm uppercase">Pesanan Sudah Final</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- DETAIL UTAMA: 2 Kolom --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Detail Pesanan (Mengambil 2/3 lebar) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Detail Item Pesanan --}}
                <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200">
                    <h3 class="text-xl font-bold text-indigo-600 mb-4 border-b pb-2">Rincian Item Pesanan
                        ({{ $order->orderItems->count() }} Item)</h3>
                    <ul class="space-y-3">
                        @foreach ($order->orderItems as $item)
                            <li class="flex justify-between items-start border-b pb-2 last:border-b-0">
                                <div class="flex items-center">
                                    <span
                                        class="inline-block w-6 h-6 rounded-full bg-purple-600 text-white text-xs font-bold mr-3 flex items-center justify-center flex-shrink-0">
                                        {{ $item->quantity }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-800 text-base">
                                            {{ $item->menu?->name ?? 'Menu Hilang' }}</p>
                                        @if ($item->notes)
                                            <p class="text-xs text-gray-500 italic mt-0.5">Catatan: {{ $item->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-700">
                                        Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">@ Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Catatan Khusus --}}
                @if ($order->notes)
                    <div class="bg-amber-50 p-4 rounded-lg border-l-4 border-amber-500 shadow-md">
                        <div class="flex items-center">
                            <i class="fas fa-sticky-note text-amber-600 text-xl mr-3"></i>
                            <div>
                                <p class="font-bold text-amber-800">Catatan Khusus Pelanggan:</p>
                                <p class="text-sm text-amber-900 mt-1">{{ $order->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Info Pelanggan & Audit (Mengambil 1/3 lebar) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Info Pelanggan & Meja --}}
                <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200">
                    <h3 class="text-xl font-bold text-indigo-600 mb-4 border-b pb-2">Informasi Pelanggan</h3>
                    <div class="space-y-3 text-gray-700">
                        <p><strong>Nama:</strong> {{ $order->customer_name ?? 'Anonim' }}</p>
                        <p><strong>Meja:</strong> <span
                                class="text-purple-600 font-bold">{{ $order->table->name ?? '-' }}</span></p>
                        @if ($order->phone_number)
                            <p><strong>Telepon:</strong> <span class="text-green-600">{{ $order->phone_number }}</span></p>
                        @endif
                    </div>
                </div>

                {{-- Detail Pembayaran --}}
                <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200">
                    <h3 class="text-xl font-bold text-indigo-600 mb-4 border-b pb-2">Detail Pembayaran</h3>
                    <div class="space-y-3 text-gray-700">
                        <p><strong>Metode:</strong> <span
                                class="font-bold">{{ ucfirst($order->payment->method ?? 'Tunai') }}</span></p>
                        <p><strong>Status Bayar:</strong>
                            {{-- Menggunakan status ASLI dari tabel payments --}}
                            @php
                                $paymentStatus = $order->payment->status ?? 'unpaid';
                            @endphp
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-bold 
                {{ $paymentStatus === 'paid' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                {{ strtoupper($paymentStatus) }}
                            </span>
                        </p>
                    </div>
                </div>

                {{-- Audit Waktu --}}
                <div class="bg-white p-6 rounded-lg shadow-xl border border-gray-200">
                    <h3 class="text-xl font-bold text-indigo-600 mb-4 border-b pb-2">Audit Waktu</h3>
                    <div class="space-y-2 text-sm text-gray-700">
                        <p><strong>Dibuat:</strong> {{ $order->created_at->format('d F Y H:i:s') }}</p>
                        <p><strong>Diperbarui:</strong> {{ $order->updated_at->format('d F Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@php
    $paymentStatus =
        $order->status === 'pending' && $order->payment?->status !== 'paid'
            ? 'unpaid'
            : $order->payment?->status ?? 'paid';
@endphp
