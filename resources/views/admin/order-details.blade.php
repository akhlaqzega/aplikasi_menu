<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Detail Pesanan #{{ $order->id }}</h1>
    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Informasi Umum</h2>
                            <p><strong>Nama Pelanggan:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Tipe Pesanan:</strong> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p>
                            <p><strong>Nomor Meja:</strong> {{ $order->table->name ?? 'Take Away' }}</p>
                            <p><strong>Total Harga:</strong> Rp{{ number_format($order->total_price, 2, ',', '.') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Item Pesanan</h2>
                            <ul class="list-disc list-inside">
                                @foreach($order->orderItems as $item)
                                    <li>{{ $item->menu->name }} x {{ $item->quantity }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-900">
                            &larr; Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>