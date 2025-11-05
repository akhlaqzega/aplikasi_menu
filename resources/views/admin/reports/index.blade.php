@extends('layouts.admin')

@section('title', 'Laporan Kinerja & Pendapatan')

@section('content')

    <div class="py-6">
        <div class="max-w-8x1 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    {{-- Header Laporan --}}
                    <h1 class="text-3xl font-bold text-purple-600 mb-6 border-b-2 border-purple-200 pb-2">
                        <i class="fas fa-chart-bar mr-2"></i> Laporan Detail Transaksi
                    </h1>

                    {{-- Filter Section --}}
                    <div class="mb-6 p-6 bg-purple-50 rounded-lg shadow-inner border border-purple-200 print:hidden">
                        <form id="filter-form" action="{{ route('admin.reports.index') }}" method="GET"
                            class="flex items-end space-x-4 mb-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal
                                    Mulai</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            </div>
                            {{-- Tombol Filter --}}
                            <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <i class="fas fa-filter mr-2"></i> Filter Laporan
                            </button>
                        </form>

                        {{-- Tombol Unduh PDF BARU --}}
                        <button type="button" id="download-pdf-btn"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2">
                            <i class="fas fa-file-pdf mr-2 text-red-600"></i> Unduh Laporan PDF
                        </button>

                        {{-- Tombol Cetak Biasa --}}
                        <button type="button" onclick="window.print()"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2">
                            <i class="fas fa-print mr-2"></i> Cetak Laporan (Printer)
                        </button>

                        <p class="mt-4 text-sm text-purple-800 font-semibold">Laporan untuk periode:
                            **{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}** hingga
                            **{{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}**</p>
                    </div>


                    <h2 class="text-2xl font-bold text-gray-800 pt-4 border-t mt-6">Detail Semua Pesanan Periode</h2>
                    <div class="bg-white p-0 rounded-xl shadow-lg border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                {{-- HEADER --}}
                                <thead class="bg-purple-100">
                                    <tr class="text-left text-purple-800 uppercase tracking-wider text-xs">
                                        <th class="px-6 py-3 font-bold w-1/12">No</th>
                                        <th class="px-6 py-3 font-bold w-2/12">ID Pesanan</th>
                                        <th class="px-6 py-3 font-bold w-2/12">Waktu Pesanan</th>
                                        <th class="px-6 py-3 font-bold w-2/12">Status Akhir</th>
                                        <th class="px-6 py-3 font-bold w-2/12">Pembayaran</th>
                                        <th class="px-6 py-3 font-bold w-3/12">Pelanggan</th>
                                        <th class="px-6 py-3 font-bold w-2/12">Meja</th>
                                        <th class="px-6 py-3 font-bold w-2/12 text-right">Total Harga</th>
                                    </tr>
                                </thead>

                                {{-- BODY --}}
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($detailedTransactions ?? [] as $order)
                                        <tr class="hover:bg-purple-50 transition-colors duration-150">
                                            {{-- ID --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-500">
                                                #{{ $order->id }}
                                            </td>
                                            {{-- Order Number --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold">
                                                {{ $order->order_number }}

                                                {{-- Waktu --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $order->created_at->format('d M Y H:i') }}
                                            </td>

                                            {{-- Status --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs font-semibold rounded-full border
                        @if ($order->status == 'completed') bg-green-100 text-green-800 border-green-300
                        @elseif($order->status == 'canceled')
                            bg-red-100 text-red-800 border-red-300
                        @else
                            bg-yellow-100 text-yellow-800 border-yellow-300 @endif">
                                                    {{ strtoupper($order->status) }}
                                                </span>
                                            </td>

                                            {{-- Pembayaran --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ ucfirst($order->payment->method ?? 'Tunai') }}
                                            </td>

                                            {{-- Pelanggan / Meja --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $order->customer_name ?? 'Anonim' }}
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700">
                                                {{ $order->table->name ?? '-' }}
                                            </td>

                                            {{-- Total --}}
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-extrabold
                    @if ($order->status == 'completed') text-green-700
                    @elseif($order->status == 'canceled')
                        text-red-700
                    @else
                        text-gray-700 @endif">
                                                Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-8 bg-gray-50 text-gray-500 text-base">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                Tidak ada transaksi pada periode ini.
                                            </td>
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

    {{-- SCRIPT BARU UNTUK MENGIRIM FILTER TANGGAL KE ROUTE PDF --}}
    <script>
        document.getElementById('download-pdf-btn').addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            let url = "{{ route('admin.reports.export.pdf') }}";
            let params = [];

            if (startDate) {
                params.push(`start_date=${startDate}`);
            }
            if (endDate) {
                params.push(`end_date=${endDate}`);
            }

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.open(url, '_blank');
        });
    </script>
@endsection
