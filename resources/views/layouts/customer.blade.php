<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CafeKU</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome CDN --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Custom Tailwind Config --}}
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                        accent: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        bounceSubtle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-3px)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .nav-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
        }
        
        .nav-shadow {
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.15);
        }
        
        .cart-pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen font-sans antialiased">
    {{-- Navbar --}}
    <nav class="nav-gradient p-4 nav-shadow sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            {{-- Logo --}}
            <a href="/" class="flex items-center space-x-3 group">
                <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-white tracking-tight">CafeKU</span>
            </a>

            {{-- Navigation Links --}}
            <div class="flex items-center space-x-8">
                {{-- Menu --}}
                <a href="{{ route('customer.menu.index') }}" 
                   class="text-white/90 hover:text-white font-medium px-3 py-2 rounded-lg transition-all duration-300 hover:bg-white/10 hover-lift flex items-center space-x-2">
                    <i class="fas fa-utensils text-sm"></i>
                    <span>Menu</span>
                </a>

                {{-- Pesanan Saya (muncul jika ada session pending_order_id) --}}
                @if (Session::has('pending_order_id'))
                    <a href="{{ route('customer.payment.cash', ['order' => Session::get('pending_order_id')]) }}"
                       class="text-white/90 hover:text-white font-medium px-3 py-2 rounded-lg transition-all duration-300 hover:bg-white/10 hover-lift flex items-center space-x-2">
                        <i class="fas fa-receipt text-sm"></i>
                        <span>Pesanan Saya</span>
                    </a>
                @endif

                {{-- Keranjang --}}
                <a href="{{ route('customer.cart') }}"
                   class="relative group flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-white/20 transition-all duration-300 hover-lift">
                    <div class="relative">
                        <i class="fas fa-shopping-cart text-white text-lg"></i>
                        <span id="cart-count"
                            class="absolute -top-3 -right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full cart-pulse">
                            {{ count(session('cart') ?? []) }}
                        </span>
                    </div>
                    <span class="text-white font-medium hidden sm:block">Keranjang</span>
                </a>
            </div>
        </div>
        
        {{-- Decorative Wave --}}
        <div class="absolute bottom-0 left-0 w-full overflow-hidden">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative w-full h-6">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" 
                      class="fill-white/20"></path>
            </svg>
        </div>
    </nav>

    {{-- Content --}}
    <main class="py-8 animate-fade-in">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gradient-to-r from-primary-600 to-accent-600 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-mug-hot text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">CafeKU</span>
                </div>
                <div class="text-white/80 text-sm">
                    &copy; 2023 CafeKU. Crafted with <i class="fas fa-heart text-red-300 mx-1"></i> for coffee lovers
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Periksa apakah ada pesan notifikasi dari server
        @if(Session::has('customer_notification'))
            // Create a modern notification instead of alert
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 z-50 bg-white rounded-xl shadow-2xl border-l-4 border-green-500 p-4 max-w-sm animate-slide-down';
            notification.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="bg-green-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">Notifikasi</h4>
                        <p class="text-gray-600 text-sm mt-1">{{ Session::get('customer_notification') }}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        @endif
        
        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 10) {
                nav.classList.add('shadow-lg');
                nav.style.background = 'linear-gradient(135deg, #0284c7 0%, #7c3aed 100%)';
            } else {
                nav.classList.remove('shadow-lg');
                nav.style.background = 'linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%)';
            }
        });
    </script>
</body>

</html>