@extends('layouts.admin')

@section('title', 'Riwayat Pesanan')

@section('content')

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h1 class="text-3xl font-bold text-gray-700 mb-6 border-b pb-2">Riwayat Pesanan (Selesai & Dibatalkan)</h1>

                    {{-- Bagian Pesanan Selesai (Completed) --}}
                    <h2 class="text-2xl font-bold text-green-700 mb-4 pt-4 border-t">Pesanan Selesai ({{ $completedOrders->count() }})</h2>
                    
                    @if($completedOrders->isEmpty())
                        <div class="bg-gray-100 p-8 rounded-lg shadow-inner mb-8">
                            <p class="text-gray-600 text-center text-lg">Belum ada pesanan yang berhasil diselesaikan.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto shadow-xl rounded-lg border mb-10">
                            <table class="min-w-full text-base divide-y divide-gray-200">
                                <thead class="bg-green-50">
                                    <tr class="text-left text-gray-700 uppercase tracking-wider text-xs">
                                        <th class="py-3 px-4 font-bold w-1/12">ID/Waktu">ID/Waktu</th>
                                        <th class="py-3 px-4 font-bold w-2/12">Pelanggan/Meja/HP</th>
                                        <th class="py-3 px-4 font-bold w-4/12">Detail Pesanan & Catatan</th>
                                        <th class="py-3 px-4 font-bold text-right w-2/12">Total Bayar</th>
                                        <th class="py-3 px-4 text-center font-bold w-3/12">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedOrders as $order)
                                    <tr class="hover:bg-green-100 border-b">
                                        
                                        <td class="py-3 px-4 align-top">
                                            <p class="text-blue-700 font-extrabold text-lg">#{{ $order->id }}</p>
                                            <span class="text-xs text-gray-500">Selesai: {{ $order->updated_at->format('d M H:i') }}</span>
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
                                                @foreach($order->orderItems as $item)
                                                    <li class="text-gray-700">
                                                        <span class="font-extrabold">{{ $item->quantity }}x</span> 
                                                        {{ $item->menu?->name ?? 'Menu Hilang' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            {{-- MENGGUNAKAN notes --}}
                                            @if($order->notes)
                                                <div class="bg-gray-100 p-2 rounded border border-gray-300 mt-2">
                                                    <p class="text-xs font-bold text-red-600">CATATAN OPSIONAL:</p>
                                                    <p class="text-sm text-red-700 italic">{{ $order->notes }}</p>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top font-extrabold text-right text-green-700 text-lg whitespace-nowrap">
                                            <p>Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            <span class="block text-xs text-gray-500 font-normal">({{ ucfirst($order->payment?->method ?? 'Tunai') }})</span>
                                        </td>

                                        <td class="py-3 px-4 align-top text-center">
                                            <span class="text-sm font-bold px-3 py-1 rounded bg-green-200 text-green-900">SELESAI</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif


                    {{-- Bagian Pesanan Dibatalkan (Canceled) --}}
                    <h2 class="text-2xl font-bold text-red-700 mb-4 pt-4 border-t">Pesanan Dibatalkan ({{ $canceledOrders->count() }})</h2>

                    @if($canceledOrders->isEmpty())
                        <div class="bg-gray-100 p-8 rounded-lg shadow-inner">
                            <p class="text-gray-600 text-center text-lg">Tidak ada pesanan yang dibatalkan pada saat ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto shadow-xl rounded-lg border">
                            <table class="min-w-full text-base divide-y divide-gray-200">
                                <thead class="bg-red-50">
                                    <tr class="text-left text-gray-700 uppercase tracking-wider text-xs">
                                        <th class="py-3 px-4 font-bold w-1/12">ID/Waktu</th>
                                        <th class="py-3 px-4 font-bold w-2/12">Pelanggan/Meja/HP</th>
                                        <th class="py-3 px-4 font-bold w-4/12">Detail Pesanan & Catatan</th>
                                        <th class="py-3 px-4 font-bold text-right w-2/12">Total Harga</th>
                                        <th class="py-3 px-4 text-center font-bold w-3/12">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($canceledOrders as $order)
                                    <tr class="hover:bg-red-100 border-b opacity-75">
                                        
                                        <td class="py-3 px-4 align-top">
                                            <p class="text-gray-700 font-extrabold text-lg">#{{ $order->id }}</p>
                                            <span class="text-xs text-gray-500">Dibatalkan: {{ $order->updated_at->format('d M H:i') }}</span>
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
                                                @foreach($order->orderItems as $item)
                                                    <li class="text-gray-700">
                                                        <span class="font-extrabold">{{ $item->quantity }}x</span> 
                                                        {{ $item->menu?->name ?? 'Menu Hilang' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            {{-- MENGGUNAKAN notes --}}
                                            @if($order->notes)
                                                <div class="bg-gray-100 p-2 rounded border border-gray-300 mt-2">
                                                    <p class="text-xs font-bold text-red-600">CATATAN OPSIONAL:</p>
                                                    <p class="text-sm text-red-700 italic">{{ $order->notes }}</p>
                                                </div>
                                            @endif
                                        </td>
                                        
                                        <td class="py-3 px-4 align-top font-extrabold text-right text-gray-700 text-lg whitespace-nowrap">
                                            <p>Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                            <span class="block text-xs text-gray-500 font-normal">({{ ucfirst($order->payment?->method ?? 'Tunai') }})</span>
                                        </td>

                                        <td class="py-3 px-4 align-top text-center">
                                            <span class="text-sm font-bold px-3 py-1 rounded bg-red-200 text-red-900">DIBATALKAN</span>
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