@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 text-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-xl mx-auto">
            <h1 class="text-4xl font-bold text-green-600 mb-4">Pembayaran Berhasil!</h1>
            <p class="text-gray-700 mb-6">
                Pesanan Anda dengan nomor **#{{ $order->id }}** telah dikonfirmasi.
            </p>
            <p class="text-xl font-semibold text-gray-700 mb-4">
                Pesanan Anda akan segera diproses dan diantarkan. Harap menunggu.
            </p>
            <a href="{{ route('customer.menu.index') }}"
               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition">
                Kembali ke Menu
            </a>
        </div>
    </div>
@endsection