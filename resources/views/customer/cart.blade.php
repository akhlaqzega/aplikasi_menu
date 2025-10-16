@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-extrabold text-center text-amber-400 mb-8">Keranjang Pesanan</h1>

        <div class="bg-zinc-800 rounded-xl shadow-2xl p-6 border border-zinc-700">
            @if (session('cart') && count(session('cart')) > 0)
                <ul class="divide-y divide-zinc-700">
                    @foreach (session('cart') as $id => $details)
                        <li class="flex justify-between items-center py-4 cart-item" data-id="{{ $id }}"
                            data-price="{{ $details['price'] }}">
                            <div class="flex items-center space-x-4 w-1/2">
                                <img src="{{ strpos($details['image'], 'menu_images/') !== false
                                    ? asset('storage/' . $details['image'])
                                    : asset($details['image']) }}"
                                    alt="{{ $details['name'] }}" class="w-16 h-16 object-cover rounded-lg shadow-md">
                                <div>
                                    <h3 class="text-xl font-semibold text-white">{{ $details['name'] }}</h3>
                                    <p class="text-zinc-400 item-price">
                                        Rp{{ number_format($details['price'], 2, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Kontrol jumlah dan tombol hapus --}}
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border border-zinc-600 rounded-lg overflow-hidden">
                                    <button
                                        class="bg-zinc-700 hover:bg-zinc-600 text-white px-3 py-1 minus-btn transition duration-200">-</button>
                                    <span class="px-4 py-1 quantity-display bg-zinc-900 text-white">{{ $details['quantity'] }}</span>
                                    <button
                                        class="bg-zinc-700 hover:bg-zinc-600 text-white px-3 py-1 plus-btn transition duration-200">+</button>
                                </div>
                                <form action="{{ route('customer.cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $id }}">
                                    {{-- PERUBAHAN DI SINI: Mengganti SVG dengan ikon Font Awesome --}}
                                    <button type="submit" class="text-red-500 hover:text-red-400 transition">
                                        <i class="fas fa-trash-alt text-xl"></i> {{-- Menggunakan Font Awesome --}}
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-between items-center mt-6 pt-6 border-t border-zinc-700">
                    <span class="text-2xl font-bold text-white">Total:</span>
                    <span id="total-price"
                        class="text-3xl font-extrabold text-amber-400">Rp{{ number_format(array_sum(array_column(session('cart'), 'price', 'quantity')), 2, ',', '.') }}</span>
                </div>

                <div class="text-right mt-8">
                    <a href="{{ route('customer.checkout') }}"
                        class="bg-amber-400 text-zinc-900 px-10 py-3 rounded-full font-bold text-lg hover:bg-amber-300 transition transform hover:scale-105 shadow-lg shadow-amber-500/30">
                        Lanjutkan ke Checkout <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @else
                <p class="text-center text-zinc-400 text-lg py-10">
                    <i class="fas fa-shopping-basket text-4xl mb-4 text-amber-400"></i><br>
                    Keranjang Anda kosong. Yuk, <a href="{{ route('customer.menu.index') }}" class="text-amber-400 hover:underline">lihat menu</a>!
                </p>
            @endif
        </div>
    </div>

    {{-- Script untuk mengendalikan tampilan pesan dan dropdown (TIDAK ADA PERUBAHAN FUNGSI) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const plusButtons = document.querySelectorAll('.plus-btn');
            const minusButtons = document.querySelectorAll('.minus-btn');

            const updateTotalPrice = () => {
                let totalPrice = 0;
                document.querySelectorAll('.cart-item').forEach(item => {
                    const quantity = parseInt(item.querySelector('.quantity-display').textContent);
                    const price = parseFloat(item.dataset.price); 
                    totalPrice += quantity * price;
                });
                document.getElementById('total-price').textContent = 
                    `Rp${totalPrice.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
            };

            const updateQuantity = async (id, quantity) => {
                const response = await fetch('{{ route('customer.cart.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        menu_id: id,
                        quantity: quantity
                    })
                });

                if (!response.ok) {
                    console.error('Failed to update cart.');
                }
            };

            plusButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const item = button.closest('.cart-item');
                    const id = item.dataset.id;
                    const quantityDisplay = item.querySelector('.quantity-display');
                    let quantity = parseInt(quantityDisplay.textContent);
                    quantity++;
                    quantityDisplay.textContent = quantity;
                    updateQuantity(id, quantity);
                    updateTotalPrice();
                });
            });

            minusButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const item = button.closest('.cart-item');
                    const id = item.dataset.id;
                    const quantityDisplay = item.querySelector('.quantity-display');
                    let quantity = parseInt(quantityDisplay.textContent);
                    if (quantity > 1) {
                        quantity--;
                        quantityDisplay.textContent = quantity;
                        updateQuantity(id, quantity);
                        updateTotalPrice();
                    } else {
                        item.querySelector('form button[type="submit"]').click();
                    }
                });
            });
            
            updateTotalPrice(); 
        });
    </script>
@endsection