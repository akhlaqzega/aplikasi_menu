@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center text-blue-800 mb-8">Checkout Pesanan</h1>

        <div class="bg-white rounded-lg shadow-lg p-6 max-w-2xl mx-auto">
            <form action="{{ route('customer.order.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="customer_name" class="block text-gray-700 font-bold mb-2">Nama Pelanggan</label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('customer_name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="table_id" class="block text-gray-700 font-bold mb-2">Nomor Meja</label>
                    <input type="text" name="table_id" id="table_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('table_id')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">Metode Pembayaran</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method" value="cash" class="form-radio text-blue-600" checked>
                            <span class="ml-2 text-gray-700">Tunai</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method" value="online" class="form-radio text-blue-600">
                            <span class="ml-2 text-gray-700">Online</span>
                        </label>
                    </div>
                    @error('payment_method')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Pesan yang akan muncul secara dinamis --}}
                <div id="cash-info" class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg">
                    <p class="font-bold">Penting:</p>
                    <p class="ml-5 mt-1">Untuk pembayaran **Tunai**, Anda akan diarahkan ke halaman instruksi untuk menuju kasir.</p>
                </div>
                
                <div id="online-info" class="hidden mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
                    <p class="font-bold">Penting:</p>
                    <p class="ml-5 mt-1">Untuk pembayaran **Online**, pesanan akan langsung diproses.</p>
                </div>
                
                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 font-bold mb-2">Catatan Tambahan (opsional)</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-ring-blue-500"></textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-blue-700 transition">
                        Konfirmasi Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Script untuk mengendalikan tampilan pesan --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
            const cashInfoDiv = document.getElementById('cash-info');
            const onlineInfoDiv = document.getElementById('online-info');
            
            paymentMethodRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.value === 'cash') {
                        cashInfoDiv.classList.remove('hidden');
                        onlineInfoDiv.classList.add('hidden');
                    } else if (radio.value === 'online') {
                        cashInfoDiv.classList.add('hidden');
                        onlineInfoDiv.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
@endsection