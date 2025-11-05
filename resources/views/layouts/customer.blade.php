<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Menu') | CafeKU</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- SweetAlert CDN (Diposisikan di head agar tersedia global) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #1F2937;
            /* Default text color (Dark Gray) */
        }

        /* Perubahan: Gradient warna gelap untuk Navbar */
        .nav-gradient {
            background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
        }

        /* Perubahan: Warna shadow aksen amber */
        .nav-shadow {
            box-shadow: 0 4px 20px rgba(251, 191, 36, 0.15);
        }

        .cart-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }

        /* Animasi Toast Notification */
        @keyframes slide-down {
            0% {
                transform: translateY(-100%);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-slide-down {
            animation: slide-down 0.5s ease-out;
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased relative">

    {{-- Latar Belakang (REVISI KEDUA: Mengembalikan overlay, mengatur opacity gambar ke 100 dan opacity overlay menjadi 50) --}}
    <div class="fixed inset-0 z-[-1]">
        <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?q=80&w=2071&auto=format&fit=crop"
            alt="Suasana cafe" class="w-full h-full object-cover opacity-200 blur-sm"> {{-- Gambar tidak transparan --}}
        <div class="absolute inset-0 bg-zinc-900/50"></div> {{-- Overlay warna hitam 50% opacity --}}
    </div>

    {{-- Navbar --}}
    <nav class="nav-gradient p-4 nav-shadow sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            {{-- Logo --}}
            <a href="{{ route('customer.menu.index') }}" class="flex items-center space-x-3 group">
                <div class="bg-amber-400/20 p-2 rounded-xl backdrop-blur-sm">
                    <svg class="w-8 h-8 text-amber-400" fill="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-white tracking-tight">CafeKU</span>
            </a>

            {{-- Navigation Links --}}
            <div class="flex items-center space-x-8">
                <a href="{{ route('customer.track.index') }}"
                    class="text-zinc-300 hover:text-amber-400 font-medium px-3 py-2 rounded-lg transition-all duration-300 hover-lift flex items-center space-x-2">
                    <i class="fas fa-search text-sm"></i><span>Lacak Pesanan</span>
                </a>
                <a href="{{ route('customer.menu.index') }}"
                    class="text-zinc-300 hover:text-amber-400 font-medium px-3 py-2 rounded-lg transition-all duration-300 hover:bg-white/10 hover-lift flex items-center space-x-2">
                    <i class="fas fa-utensils text-sm"></i><span>Menu</span>
                </a>

                {{-- Pesanan Saya --}}
                @if (Session::has('pending_order_id'))
                    <a href="{{ route('customer.payment.cash', ['order' => Session::get('pending_order_id')]) }}"
                        class="text-zinc-300 hover:text-amber-400 font-medium px-3 py-2 rounded-lg transition-all duration-300 hover:bg-white/10 hover-lift flex items-center space-x-2">
                        <i class="fas fa-receipt text-sm"></i><span>Pesanan Saya</span>
                    </a>
                @endif

                {{-- Keranjang (Aksen Amber) --}}
                <a href="{{ route('customer.cart') }}"
                    class="relative group flex items-center space-x-2 bg-amber-400/20 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-amber-400/30 transition-all duration-300 hover-lift">
                    <div class="relative">
                        <i class="fas fa-shopping-cart text-amber-400 text-lg"></i>
                        <span id="cart-count"
                            class="absolute -top-3 -right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-zinc-900 rounded-full cart-pulse">
                            {{ count(session('cart') ?? []) }}
                        </span>
                    </div>
                    <span class="text-amber-400 font-medium hidden sm:block">Keranjang</span>
                </a>
            </div>
        </div>

        {{-- Decorative Wave --}}
        <div class="absolute bottom-0 left-0 w-full overflow-hidden">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative w-full h-6">
                <path
                    d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                    class="fill-zinc-800"></path>
            </svg>
        </div>
    </nav>

    {{-- Content --}}
    <main class="py-8 animate-fade-in">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-zinc-900 to-zinc-800 text-white mt-16 border-t border-amber-400/20">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="bg-amber-400/20 p-2 rounded-lg">
                        <i class="fas fa-mug-hot text-amber-400 text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-white">CafeKU</span>
                </div>
                <div class="text-zinc-400 text-sm">
                    &copy; 2023 CafeKU. Crafted with <i class="fas fa-heart text-red-500 mx-1"></i> for coffee lovers
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Logika Toast Notification dan Scroll dimasukkan ke dalam DOMContentLoaded listener
        document.addEventListener('DOMContentLoaded', function() {

            // Logika scroll untuk navbar
            const nav = document.querySelector('nav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    nav.classList.add('shadow-xl', 'bg-zinc-800/95', 'backdrop-blur-sm');
                    nav.classList.remove('nav-gradient');
                } else {
                    nav.classList.remove('shadow-xl', 'bg-zinc-800/95', 'backdrop-blur-sm');
                    nav.classList.add('nav-gradient');
                }
            });

            // Logika Toast Notification Anda (Session flash message)
            @if (Session::has('customer_notification'))
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-20 right-4 z-50 bg-white rounded-xl shadow-2xl border-l-4 border-green-500 p-4 max-w-sm animate-slide-down';
                notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">Notifikasi</h4>
                        <p class="text-gray-600 text-sm mt-1">{{ Session::get('customer_notification') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 5000);
            @endif

            // --- LOGIKA SWEETALERT DARI FLASH SESSION (BARU) ---
            @if (session('sweet_success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: '{{ session('sweet_success') }}',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#F59E0B' // Amber-500
                    });
                }
            @endif

            @if (session('error'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Terjadi Kesalahan!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#EF4444' // Red-500
                    });
                }
            @endif

        });
    </script>
</body>

</html>
