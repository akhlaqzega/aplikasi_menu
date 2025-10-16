@extends('layouts.admin')

@section('title', 'Laporan Kinerja & Pendapatan')

@section('content')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h1 class="text-3xl font-bold text-blue-800 mb-6 border-b pb-2">Laporan Pendapatan & Kinerja Pesanan</h1>

                    {{-- Form Filter Tanggal --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-inner">
                        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-end space-x-4 mb-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Filter Laporan
                            </button>
                        </form>

                        <button type="button" onclick="window.print()"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Cetak Laporan
                        </button>
                        <p class="mt-2 text-xs text-gray-500">Laporan untuk periode:
                            **{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}** hingga
                            **{{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}**</p>
                    </div>

                    {{-- Ringkasan Kinerja (Cards) --}}
                    
                    {{-- BARIS 1: METRIK KEUANGAN UTAMA (Lebih Besar) --}}
                    <div class="grid grid-cols-1 mb-8">
                        {{-- CARD 1: Total Pendapatan --}}
                        <div class="bg-white p-6 rounded-xl shadow-2xl border-l-4 border-green-600">
                            <p class="text-lg font-medium text-gray-500">TOTAL PENDAPATAN BERSIH</p>
                            <p class="text-5xl font-extrabold text-green-700 mt-2">
                                Rp{{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 mt-2">*Dari semua pesanan yang berstatus 'completed'</p>
                        </div>
                    </div>

                    {{-- BARIS 2: METRIK KINERJA PESANAN (Grid 3 Kolom) --}}
                    <h2 class="text-xl font-bold text-gray-800 mb-4 pt-4 border-t">Ringkasan Kinerja Pesanan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                        
                        {{-- CARD 2: Total Pesanan Masuk --}}
                        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-yellow-500">
                            <p class="text-sm font-medium text-gray-500">Total Pesanan Masuk</p>
                            <p class="text-3xl font-extrabold text-yellow-600 mt-1">
                                {{ number_format($summary['total_orders'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- CARD 3: Pesanan Berhasil --}}
                        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-blue-500">
                            <p class="text-sm font-medium text-gray-500">Pesanan Berhasil Diselesaikan</p>
                            <p class="text-3xl font-extrabold text-blue-600 mt-1">
                                {{ number_format($summary['total_completed'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- CARD 4: Pesanan Dibatalkan --}}
                        <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-red-500">
                            <p class="text-sm font-medium text-gray-500">Pesanan Dibatalkan</p>
                            <p class="text-3xl font-extrabold text-red-600 mt-1">
                                {{ number_format($summary['total_canceled'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Detail Loss dari Pembatalan --}}
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-8">
                        <p class="text-lg font-semibold text-red-700">Potensi Kerugian dari Pembatalan:</p>
                        <p class="text-2xl font-extrabold text-red-800 mt-1">
                            Rp{{ number_format($summary['total_loss_canceled'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-red-500 mt-1">*Angka ini adalah total harga dari semua pesanan yang
                            dibatalkan pada periode ini.</p>
                    </div>

                    {{-- DETAIL SEMUA TRANSAKSI (AUDIT) --}}
                    <h2 class="text-2xl font-bold text-gray-800 pt-4 border-t mt-6">Detail Semua Pesanan Periode</h2>
                    <div class="bg-white p-0 rounded-xl shadow-lg border">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                            ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">
                                            Waktu Pesanan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">
                                            Status Akhir</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">
                                            Pelanggan / Meja</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">
                                            Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($detailedTransactions ?? [] as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">
                                                #{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $order->created_at->format('d M H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if ($order->status == 'completed') bg-green-100 text-green-800 
                                                @elseif($order->status == 'canceled') bg-red-100 text-red-800 
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ strtoupper($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $order->customer_name ?? 'Anonim' }} <span
                                                    class="text-xs text-gray-500">(Meja:
                                                    {{ $order->table->name ?? '-' }})</span>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-right font-extrabold 
                                                @if ($order->status == 'completed') text-green-700 @else text-gray-700 @endif">
                                                Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-gray-500 text-base">Tidak ada
                                                transaksi yang tercatat pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection