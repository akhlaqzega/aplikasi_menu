@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-extrabold text-center text-amber-400 mb-8">Konfirmasi Pesanan</h1>

        @if (session('error'))
            <div class="bg-red-900/30 border-l-4 border-red-600 text-red-100 p-4 mb-4 max-w-2xl mx-auto rounded-lg" role="alert">
                <p class="font-bold">Gagal Membuat Pesanan!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        @if (session('table_error'))
            <div class="bg-red-900/30 border-l-4 border-red-600 text-red-100 p-4 mb-4 max-w-2xl mx-auto rounded-lg" role="alert">
                <p class="font-bold">PERINGATAN MEJA:</p>
                <p>{{ session('table_error') }}</p>
            </div>
        @endif

        <div class="bg-zinc-800 rounded-xl shadow-2xl p-6 max-w-2xl mx-auto border border-amber-400/20">
            <form action="{{ route('customer.order.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="customer_name" class="block text-zinc-300 font-bold mb-1">Nama Pelanggan</label>
                    <input type="text" name="customer_name" id="customer_name" placeholder="Contoh: Budi Santoso"
                        class="w-full mt-1 px-3 py-2 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400"
                        required value="{{ old('customer_name') }}">
                    @error('customer_name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone_number" class="block text-zinc-300 font-bold mb-1">Nomor Telepon (Pelacakan)</label>
                    <input type="tel" name="phone_number" id="phone_number" placeholder="Contoh: 081234567890"
                        class="w-full mt-1 px-3 py-2 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400"
                        required value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="table_name" class="block text-zinc-300 font-bold mb-1">Pilih Kode Meja Anda</label>
                    
                    <select name="table_name" id="table_name" required
                        class="w-full mt-1 px-3 py-2 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400">
                        
                        <option value="" disabled {{ !old('table_name') ? 'selected' : '' }}>--- Pilih Meja Yang Tersedia ---</option>
                        
                        @forelse($tables as $table)
                            @if ($table->status === 'available')
                                <option 
                                    value="{{ $table->name }}" 
                                    {{ old('table_name') == $table->name ? 'selected' : '' }}>
                                    {{ $table->name }}
                                </option>
                            @endif
                        @empty
                            <option value="" disabled>Tidak ada meja yang tersedia saat ini.</option>
                        @endforelse

                    </select>

                    {{-- KETERANGAN BARU --}}
                    <p class="text-xs text-red-500 mt-1 font-bold">Penting: Pilih **Meja yang Benar-benar Anda Duduki** untuk menghindari kesalahan pesanan.</p>
                    @error('table_name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-zinc-300 font-bold mb-1">Metode Pembayaran</label>
                    <div class="flex items-center space-x-4 mt-1">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method_type" value="cash"
                                class="form-radio text-amber-400" checked>
                            <span class="ml-2 text-zinc-300">Tunai</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method_type" value="online"
                                class="form-radio text-amber-400">
                            <span class="ml-2 text-zinc-300">Online</span>
                        </label>
                    </div>
                    @error('payment_method_type')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div id="online-options-container" class="mb-6 hidden">
                    <label for="payment_method_detail" class="block text-zinc-300 font-bold mb-1">Pilih
                        Bank/E-Wallet</label>
                    <select name="payment_method_detail" id="payment_method_detail"
                        class="w-full mt-1 px-3 py-2 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="DANA">DANA</option>
                        <option value="BRI">BRI Virtual Account</option>
                        <option value="QRIS">QRIS</option>
                        <option value="GOPAY">GOPAY</option>
                    </select>
                </div>

                <div id="cash-info" class="mb-6 p-4 bg-amber-900/30 border-l-4 border-amber-400 text-amber-100 rounded-lg">
                    <p class="font-bold">Penting:</p>
                    <p class="ml-5 mt-1">Untuk pembayaran <strong>Tunai</strong>, Anda akan diarahkan ke halaman instruksi
                        untuk menuju kasir.</p>
                </div>

                <div id="online-info"
                    class="hidden mb-6 p-4 bg-green-900/30 border-l-4 border-green-500 text-green-100 rounded-lg">
                    <p class="font-bold">Penting:</p>
                    <p class="ml-5 mt-1">Untuk pembayaran <strong>Online</strong>, pesanan akan langsung diproses.</p>
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-zinc-300 font-bold mb-1">Catatan Tambahan (opsional)</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Tanpa bawang, level pedas 2"
                        class="w-full mt-1 px-3 py-2 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-right">
                    <button type="submit"
                        class="bg-amber-400 text-zinc-900 px-8 py-3 rounded-full font-bold hover:bg-amber-300 transition shadow-lg shadow-amber-500/30">
                        Konfirmasi Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeRadios = document.querySelectorAll('input[name="payment_method_type"]');
            const cashInfoDiv = document.getElementById('cash-info');
            const onlineInfoDiv = document.getElementById('online-info');
            const onlineOptionsContainer = document.getElementById('online-options-container');

            const togglePaymentOptions = (type) => {
                if (type === 'online') {
                    cashInfoDiv.classList.add('hidden');
                    onlineInfoDiv.classList.remove('hidden');
                    onlineOptionsContainer.classList.remove('hidden');
                } else {
                    cashInfoDiv.classList.remove('hidden');
                    onlineInfoDiv.classList.add('hidden');
                    onlineOptionsContainer.classList.add('hidden');
                }
            };

            typeRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    togglePaymentOptions(radio.value);
                });
            });

            const initialType = document.querySelector('input[name="payment_method_type"]:checked').value;
            togglePaymentOptions(initialType);
        });
    </script>
@endsection