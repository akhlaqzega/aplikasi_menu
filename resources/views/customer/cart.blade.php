@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center text-blue-800 mb-8">Keranjang Pesanan</h1>

        <div class="bg-white rounded-lg shadow-lg p-6">
            @if (session('cart') && count(session('cart')) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach (session('cart') as $id => $details)
                        <li class="flex justify-between items-center py-4 cart-item" data-id="{{ $id }}"
                            data-price="{{ $details['price'] }}">
                            <div class="flex items-center space-x-4 w-1/2">
                                <img src="{{ $details['image'] ? asset($details['image']) : 'https://via.placeholder.com/64x64.png?text=No+Image' }}"
                                    alt="{{ $details['name'] }}" class="w-16 h-16 object-cover rounded">
                                <div>
                                    <h3 class="text-xl font-semibold">{{ $details['name'] }}</h3>
                                    <p class="text-gray-600 item-price">
                                        Rp{{ number_format($details['price'], 2, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Kontrol jumlah dan tombol hapus --}}
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border rounded-lg overflow-hidden">
                                    <button
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 minus-btn">-</button>
                                    <span class="px-4 py-1 quantity-display">{{ $details['quantity'] }}</span>
                                    <button
                                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 plus-btn">+</button>
                                </div>
                                <form action="{{ route('customer.cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $id }}">
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.205 21H7.795a2 2 0 01-1.928-1.858L5 7m5-2h4M9 7v14m5-14v14m-5-14a2 2 0 01-2-2m2 2a2 2 0 00-2 2m0 0a2 2 0 01-2-2M9 7l.867 12.142A2 2 0 0016.205 21H7.795a2 2 0 00-1.928-1.858L5 7" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
                    <span class="text-2xl font-bold">Total:</span>
                    <span id="total-price"
                        class="text-2xl font-bold text-blue-600">Rp{{ number_format(array_sum(array_column(session('cart'), 'price', 'quantity')), 2, ',', '.') }}</span>
                </div>

                <div class="text-right mt-6">
                    <a href="{{ route('customer.checkout') }}"
                        class="bg-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-blue-700 transition">
                        Lanjutkan ke Checkout
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 text-lg">Keranjang Anda kosong.</p>
            @endif
        </div>
    </div>

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
                    `Rp${totalPrice.toLocaleString('id-ID', { minimumFractionDigits: 2 })}`;
            };

            const updateQuantity = async (id, quantity) => {
                const response = await fetch('{{ route('customer.cart.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        // Jika kuantitas 1, hapus item (redirect ke form hapus)
                        item.querySelector('form button[type="submit"]').click();
                    }
                });
            });
        });
    </script>
@endsection
