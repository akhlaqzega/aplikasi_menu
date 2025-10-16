@extends('layouts.admin')

@section('title', 'Ringkasan Operasional')

@section('content')
    <div class="space-y-8">
        <h1 class="text-3xl font-bold text-gray-800 border-b pb-4">Dashboard Utama</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Card Pesanan Menunggu --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500 flex justify-between items-center">
                    Pesanan Menunggu <i class="fas fa-clock text-xl text-yellow-500"></i>
                </p>
                <h2 class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $pendingCount }}</h2>
                <span class="text-xs text-gray-400">Menunggu Pembayaran</span>
            </div>

            {{-- Card Sedang Diproses --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500 flex justify-between items-center">
                    Sedang Diproses <i class="fas fa-concierge-bell text-xl text-blue-500"></i>
                </p>
                <h2 class="text-3xl font-extrabold text-blue-600 mt-1">{{ $inProgressCount }}</h2>
                <span class="text-xs text-gray-400">Order di Dapur/Diantar</span>
            </div>

            {{-- Card Selesai --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500 flex justify-between items-center">
                    Selesai Total <i class="fas fa-check-circle text-xl text-green-500"></i>
                </p>
                <h2 class="text-3xl font-extrabold text-green-600 mt-1">{{ $completedCount }}</h2>
                <span class="text-xs text-gray-400">Pesanan Telah Diterima</span>
            </div>

            {{-- Card Dibatalkan --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500 flex justify-between items-center">
                    Total Dibatalkan <i class="fas fa-times-circle text-xl text-red-500"></i>
                </p>
                <h2 class="text-3xl font-extrabold text-red-600 mt-1">{{ $canceledCount }}</h2>
                <span class="text-xs text-gray-400">Otomatis / Manual</span>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center bg-purple-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:bg-purple-700 transition duration-150">
                <i class="fas fa-clipboard-list mr-2"></i> KELOLA SEMUA PESANAN MASUK
            </a>
        </div>
    </div>
@endsection