@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 py-12">

        {{-- BAGIAN HEADER DENGAN TEMA GELAP --}}
        <div class="text-center mb-16 relative">
            <div
                class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full">
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">
                Menu Pilihan Kami
            </h1>
            <p class="text-lg text-zinc-400 max-w-2xl mx-auto">Nikmati mahakarya dari dapur kami, dibuat dengan
                bahan-bahan terbaik dan penuh cinta.</p>

            <div class="flex justify-center mt-6 space-x-2">
                <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
            </div>
        </div>
        
        {{-- ====================================================================== --}}
        {{-- BAGIAN BEST SELLER BARU (Horizontal Scroll & Placeholder) --}}
        <div class="mb-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-amber-400 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 fill-amber-400" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zM8 12c0 2.21 1.79 4 4 4s4-1.79 4-4-1.79-4-4-4-4 1.79-4 4zm3-4.47l-1.04 2.11L8 12.5h2.5L12 15.5l1.5-3h2.5l-2.96-1.86L13 7.53z"/></svg>
                    Menu Paling Laris (Best Seller)
                </h2>
                <span class="text-sm text-zinc-400 hidden sm:block">Lihat 10 menu terlaris bulan ini</span>
            </div>

            {{-- Container untuk Horizontal Scrolling --}}
            <div class="flex space-x-6 overflow-x-scroll pb-4 custom-scrollbar">
                
                @if(isset($bestSellers) && $bestSellers->count() > 0)
                    {{-- Loop data Best Seller jika ada --}}
                    @foreach ($bestSellers as $menu)
                        {{-- KARTU BEST SELLER --}}
                        <div 
                            class="flex-shrink-0 w-64 bg-zinc-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-amber-500/30 hover:scale-[1.02] border border-zinc-700 hover:border-amber-500"
                            data-menu-id="{{ $menu->id }}">
                            
                            <div class="relative h-32 overflow-hidden">
                                <img 
                                    src="{{ strpos($menu->image, '/') !== false && !\Illuminate\Support\Str::contains($menu->image, 'menu_images/') ? asset($menu->image) : ($menu->image ? asset('storage/' . $menu->image) : 'https://via.placeholder.com/400x250.png?text=No+Image') }}"
                                    alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover">
                                
                                <div class="absolute bottom-2 left-2 bg-amber-400 text-zinc-900 text-xs font-bold px-2 py-0.5 rounded-full shadow-lg">
                                    Terjual: {{ $menu->total_sold }}
                                </div>
                            </div>

                            <div class="p-3">
                                <h3 class="text-lg font-bold text-zinc-100 truncate">{{ $menu->name }}</h3>
                                <p class="text-amber-400 text-sm font-extrabold mt-1">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                                <p class="text-zinc-400 text-xs line-clamp-1 mt-1">{{ $menu->description }}</p>

                                <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form mt-3">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                    <button type="submit"
                                        class="w-full bg-amber-400 text-zinc-900 text-sm px-4 py-2 rounded-lg font-bold hover:bg-amber-300 transition-all">
                                        Pesan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- KARTU PLACEHOLDER KETIKA DATA KOSONG --}}
                    <div class="flex flex-col items-center justify-center w-full bg-zinc-900/50 p-10 rounded-2xl border-2 border-dashed border-amber-400/50 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-amber-500 mb-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.376ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-lg font-semibold text-amber-300">Belum Ada Best Seller</p>
                        <p class="text-sm text-zinc-400 text-center mt-1">Lariskan pesanan Anda! Menu terlaris akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Sisipkan custom CSS untuk menghilangkan scrollbar default --}}
        <style>
        /* ... (CSS custom-scrollbar tetap sama) ... */
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px; /* Tinggi scrollbar horizontal */
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #f59e0b; /* Warna amber-500 */
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #4b5563; /* Warna zinc-600 */
        }
        </style>
        {{-- ====================================================================== --}}


        {{-- FILTER KATEGORI DENGAN TEMA GELAP (Dibuat Sticky) --}}
        <div class="mb-10 flex flex-wrap justify-center gap-3 p-3 bg-zinc-900/90 backdrop-blur-sm rounded-xl sticky top-[68px] z-30 border border-amber-400/20 shadow-xl"
            id="category-tabs">
            
            <button data-category="all"
                class="category-tab px-4 py-2 text-sm font-semibold rounded-full transition-colors duration-300 text-zinc-300 hover:bg-zinc-700">
                Semua Menu
            </button>
            @foreach ($groupedMenus as $categoryName => $menus)
                <button data-category="{{ \Illuminate\Support\Str::slug($categoryName) }}"
                    class="category-tab px-4 py-2 text-sm font-semibold rounded-full transition-colors duration-300 text-zinc-300 hover:bg-zinc-700">
                    {{ $categoryName }}
                </button>
            @endforeach
        </div>

        {{-- Loop utama untuk KATEGORI --}}
        @foreach ($groupedMenus as $categoryName => $menus)
            <div class="mb-16 category-section" id="section-{{ \Illuminate\Support\Str::slug($categoryName) }}">
                <h2 class="text-3xl font-extrabold text-white mb-8 border-b-2 border-amber-500/50 pb-3">
                    {{ $categoryName }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    {{-- Loop kedua untuk MENU di dalam kategori --}}
                    @foreach ($menus as $menu)
                        {{-- KARTU MENU DENGAN TEMA GELAP --}}
                        <div
                            class="group bg-zinc-800 rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-amber-500/30 hover:-translate-y-2 border border-zinc-700 hover:border-amber-500">
                            <div class="relative overflow-hidden h-56">
                                <img 
                                    src="{{ strpos($menu->image, '/') !== false && !\Illuminate\Support\Str::contains($menu->image, 'menu_images/') ? asset($menu->image) : ($menu->image ? asset('storage/' . $menu->image) : 'https://via.placeholder.com/400x250.png?text=No+Image') }}"
                                    alt="{{ $menu->name }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                {{-- Harga diubah ke amber --}}
                                <div class="absolute top-4 right-4 bg-amber-400 text-zinc-900 text-sm font-bold px-3 py-1 rounded-full shadow-lg transform group-hover:scale-110 transition-transform">
                                    Rp{{ number_format($menu->price, 0, ',', '.') }}
                                </div>

                                {{-- Kategori diubah ke tema gelap --}}
                                <div class="absolute top-4 left-4 bg-zinc-900/50 backdrop-blur-sm text-zinc-200 text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $menu->category->name ?? 'Lain-lain' }}
                                </div>
                            </div>

                            <div class="p-5 relative">
                                {{-- Tombol Favorite diubah ke tema gelap --}}
                                <button
                                    class="absolute -top-5 right-6 bg-zinc-700 p-2 rounded-full shadow-md text-zinc-400 hover:text-red-500 transition-all duration-300 hover:scale-110 hover:bg-zinc-600 group-hover:shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>

                                <div class="mb-4 h-24">
                                    {{-- Nama Menu diubah ke amber saat hover --}}
                                    <h2 class="text-xl font-bold text-zinc-100 mb-2 group-hover:text-amber-400 transition-colors truncate">
                                        {{ $menu->name }}</h2>
                                    <p class="text-zinc-400 text-sm line-clamp-3 leading-relaxed">{{ $menu->description }}</p>
                                </div>

                                <form action="{{ route('customer.cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                    {{-- Tombol Pesan diubah ke amber --}}
                                    <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 bg-amber-400 text-zinc-900 px-6 py-3 rounded-xl font-bold hover:bg-amber-300 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:translate-y-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Pesan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if (count($groupedMenus) === 0)
            <div class="text-center py-16">
                <h3 class="text-2xl font-bold text-white">Oops! Menu belum tersedia.</h3>
                <p class="text-zinc-400 mt-2">Silakan kembali lagi nanti untuk melihat hidangan lezat kami.</p>
            </div>
        @endif

    </div>

    {{-- SCRIPT DENGAN JAVASCRIPT ANDA (TIDAK ADA PERUBAHAN FUNGSI) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const getCsrfToken = () => {
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            };

            const tabs = document.querySelectorAll('.category-tab');
            const sections = document.querySelectorAll('.category-section');

            const filterMenus = (categorySlug) => {
                // Reset semua tab ke style TIDAK AKTIF (tema gelap)
                tabs.forEach(t => {
                    t.classList.remove('bg-amber-400', 'text-zinc-900', 'shadow-lg'); 
                    t.classList.add('text-zinc-300', 'hover:bg-zinc-700'); 
                });

                sections.forEach(s => s.style.display = 'none');

                const setActiveTab = (selector) => {
                    const activeTab = document.querySelector(selector);
                    if (activeTab) {
                        // Style AKTIF diubah ke amber-400/zinc-900
                        activeTab.classList.remove('text-zinc-300', 'hover:bg-zinc-700');
                        activeTab.classList.add('bg-amber-400', 'text-zinc-900', 'shadow-lg');
                    }
                };

                if (categorySlug === 'all') {
                    sections.forEach(s => s.style.display = 'block');
                    setActiveTab('[data-category="all"]');
                } else {
                    const targetSection = document.getElementById(`section-${categorySlug}`);
                    if (targetSection) {
                        targetSection.style.display = 'block';
                        setActiveTab(`[data-category="${categorySlug}"]`);
                    }
                }
            };

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    filterMenus(tab.dataset.category);
                });
            });

            // Inisialisasi filter
            filterMenus('all');

            // --- Logika AJAX Add to Cart (TIDAK ADA PERUBAHAN FUNGSI) ---
            document.querySelectorAll('.add-to-cart-form').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const csrfToken = getCsrfToken();
                    const menuId = form.querySelector('input[name="menu_id"]').value;

                    if (!csrfToken) {
                        alert("Token keamanan hilang/kadaluarsa. Harap muat ulang halaman.");
                        return;
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ menu_id: menuId }),
                            credentials: 'same-origin'
                        });

                        if (response.status === 419) {
                            alert("Sesi keamanan telah kadaluarsa. Halaman akan dimuat ulang.");
                            window.location.reload();
                            return;
                        }

                        if (!response.ok) {
                            throw new Error('Permintaan gagal dengan status ' + response.status);
                        }

                        const data = await response.json();

                        if (data.success) {
                            const cartCountElement = document.getElementById('cart-count');
                            if (cartCountElement) {
                                cartCountElement.textContent = data.cart_count;
                                cartCountElement.classList.add('scale-150');
                                setTimeout(() => {
                                    cartCountElement.classList.remove('scale-150');
                                }, 300);
                            }
                            console.log(data.message);
                        } else {
                            alert(data.message || 'Gagal menambahkan ke keranjang.');
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        if (error instanceof SyntaxError) {
                            alert("Terjadi kesalahan sistem (respons tidak terduga). Harap hubungi administrator.");
                        } else {
                            alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                        }
                    }
                });
            });
        });
    </script>
@endsection