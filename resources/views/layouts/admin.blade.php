<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | CafeKU</title>
    
    {{-- Scripts dan Styles Eksternal --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    {{-- AlpineJS untuk Dropdown Profile --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    
    {{-- SweetAlert2 CDN (Untuk notifikasi pop-up) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        /* Card, Notifikasi, Dropdown CSS */
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Card Border Color Indicator */
        .stat-card { position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; }
        .pending::before { background: linear-gradient(90deg, #f6ad55, #fc8181); }
        .in-progress::before { background: linear-gradient(90deg, #63b3ed, #7f9cf5); }
        .completed::before { background: linear-gradient(90deg, #68d391, #4fd1c7); }
        .canceled::before { background: linear-gradient(90deg, #fc8181, #f687b3); }
        
        /* Sidebar & Main Content Sizing */
        .sidebar {
            width: 16rem; 
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 20;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            margin-left: 16rem; 
            flex: 1;
        }

        /* Footer Logout di sidebar */
        .footer-logout { border-top: 1px solid #e5e7eb; margin-top: auto; }

        /* Notifikasi Dot */
        .notification-dot {
            position: absolute;
            top: 0px;
            right: 0px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #ef4444; /* Red-500 */
        }
        
        /* Animasi Konten */
        @keyframes fadeInSlide { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .content-wrapper { animation: fadeInSlide 0.7s ease-out; }
        .dropdown-menu { transition: opacity 0.15s ease, transform 0.15s ease; transform-origin: top right; }
        .dropdown-menu.hidden { opacity: 0; transform: scale(0.95); pointer-events: none; }
    </style>

</head>

<body class="bg-gray-50">
    <div class="flex">

        {{-- SIDEBAR --}}
        <div id="sidebar" class="sidebar bg-white">
            
            {{-- Logo / Judul --}}
            <div class="p-4 border-b border-gray-200 flex items-center justify-center h-24">
                <h1 class="text-2xl font-extrabold text-purple-600">CafeKU Admin</h1>
            </div>
            
            <div class="flex-grow">
                <nav class="mt-6">

                    <p class="text-xs text-gray-400 uppercase px-6 mb-2">Menu Utama</p>

                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-tachometer-alt {{ request()->routeIs('admin.dashboard') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Dashboard</span>
                    </a>

                    <p class="text-xs text-gray-400 uppercase px-6 mt-4 mb-2">Operasional</p>

                    {{-- Pesanan Aktif --}}
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.history') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-shopping-cart {{ request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.history') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Pesanan Aktif</span>
                    </a>

                    {{-- Riwayat Pesanan --}}
                    <a href="{{ route('admin.orders.history') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.orders.history') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-history {{ request()->routeIs('admin.orders.history') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Riwayat</span>
                    </a>

                    <p class="text-xs text-gray-400 uppercase px-6 mt-4 mb-2">Data Master</p>

                    {{-- Kelola Meja --}}
                    <a href="{{ route('admin.tables.index') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.tables.*') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-chair {{ request()->routeIs('admin.tables.*') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Kelola Meja</span>
                    </a>

                    {{-- Kelola Produk --}}
                    <a href="{{ route('admin.menus.index') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.menus.*') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-utensils {{ request()->routeIs('admin.menus.*') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Menu & Produk</span>
                    </a>

                    {{-- Kelola Kategori --}}
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.categories.*') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-tags {{ request()->routeIs('admin.categories.*') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Kategori</span>
                    </a>

                    <p class="text-xs text-gray-400 uppercase px-6 mt-4 mb-2">Keuangan & Sistem</p>

                    {{-- Laporan --}}
                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-purple-50 hover:border-r-4 hover:border-purple-500 transition-colors duration-200 
                        {{ request()->routeIs('admin.reports.*') ? 'bg-purple-50 border-r-4 border-purple-500 font-semibold text-purple-700' : '' }}">
                        <i class="fas fa-chart-bar {{ request()->routeIs('admin.reports.*') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                        <span class="mx-3 font-medium">Laporan</span>
                    </a>
                </nav>
            </div>


            {{-- Footer Logout (Dihilangkan dari sidebar, tetapi structural div tetap ada) --}}
            <div id="footer-logout" class="footer-logout p-4 hidden">
                 {{-- Konten ini sekarang di Navbar Dropdown --}}
            </div>

        </div>

        {{-- MAIN CONTENT (HEADER & CONTENT WRAPPER) --}}
        <div id="main-content" class="main-content flex-1">
            
            {{-- HEADER/NAVBAR --}}
            <header class="bg-white shadow-sm z-10 sticky top-0">
                <div class="flex items-center justify-between px-6 py-4"> {{-- py-4 untuk navbar yang lebih ringkas --}}
                    
                    <div class="flex items-center">
                         {{-- Judul Konten --}}
                        <h1 class="text-xl font-bold text-gray-800">@yield('title', 'Admin Panel')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        
                        {{-- DROPDOWN NOTIFIKASI PESANAN BARU (BARU) --}}
                        <div class="relative inline-block text-left" x-data="{ notificationOpen: false }" @click.away="notificationOpen = false">
                            <button @click="notificationOpen = !notificationOpen; fetchNotificationDetails();" type="button" class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none relative">
                                <i class="fas fa-bell text-xl"></i>
                                {{-- Notifikasi Dot dan Badge Count --}}
                                <span id="notification-indicator" class="notification-dot hidden"></span>
                                <span id="notification-count-badge" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full hidden">0</span>
                            </button>

                            <div x-show="notificationOpen" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-30" 
                                id="notification-dropdown">

                                <div class="p-3 border-b border-gray-100">
                                    <h4 class="font-bold text-gray-800">Notifikasi Pesanan Baru</h4>
                                </div>
                                
                                {{-- Konten Dinamis Akan Diisi Oleh JS --}}
                                <div id="notification-list" class="max-h-64 overflow-y-auto">
                                    <p class="text-center text-sm text-gray-500 p-4">Memuat...</p>
                                </div>

                                <div class="p-2 border-t border-gray-100 text-center">
                                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat Semua Pesanan Aktif</a>
                                </div>
                            </div>
                        </div>
                        {{-- END DROPDOWN NOTIFIKASI --}}


                        {{-- DROPDOWN PROFILE --}}
                        <div class="relative inline-block text-left" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
                            <button @click="profileOpen = !profileOpen" type="button" class="flex items-center focus:outline-none p-1 rounded-full bg-gray-100 hover:bg-gray-200 transition">
                                <span class="mr-2 text-sm font-medium text-gray-700 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                                <img id="navbar-profile-photo" class="h-12 w-12 rounded-full object-cover shadow-inner" 
                                     src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=FFFFFF&background=6366F1' }}" 
                                     alt="Profile">
                            </button>

                            <div x-show="profileOpen" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-30">
                                <div class="py-1">
                                    
                                    <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                        <p class="font-bold">{{ Auth::user()->name ?? 'Admin' }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'user@cafe.com' }}</p>
                                    </div>

                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <i class="fas fa-user-cog w-5 mr-3"></i> Kelola Profil
                                    </a>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                                            <i class="fas fa-sign-out-alt w-5 mr-3"></i> Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- END DROPDOWN PROFILE --}}
                    </div>
                </div>
            </header>

            {{-- CONTENT SECTION --}}
            <div class="p-6 content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts') 


   <script>
    // ===============================================
    // PROFILE DROPDOWN LOGIC (JavaScript Murni)
    // ===============================================
    const profileDropdownBtn = document.getElementById('profile-dropdown-btn');
    const profileDropdownMenu = document.getElementById('profile-dropdown-menu');

    if (profileDropdownBtn && profileDropdownMenu) {
        profileDropdownBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            profileDropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!profileDropdownMenu.contains(event.target) && !profileDropdownBtn.contains(event.target)) {
                profileDropdownMenu.classList.add('hidden');
            }
        });
    }
    
    // ===============================================
    // FUNGSI BARU: MEMUTAR SUARA NOTIFIKASI
    // ===============================================
    function playNotificationSound() {
        try {
            const audio = new Audio('/audio/order_alert.mp3'); 
            audio.play();
        } catch (e) {
            console.error("Gagal memutar audio:", e);
        }
    }


    // ===============================================
    // ORDER POLLING & NOTIF DETAIL LOGIC
    // ===============================================
    const checkUrl = "{{ route('admin.orders.checkNew') }}";
    const detailUrl = "{{ route('admin.notifications.details') }}";
    const notificationIndicator = document.getElementById('notification-indicator');
    const notificationCountBadge = document.getElementById('notification-count-badge');
    const notificationList = document.getElementById('notification-list');
    
    let lastPendingCount = -1;
    

    // FUNGSI BARU: Mengisi konten dropdown
    async function fetchNotificationDetails() {
        try {
            const response = await fetch(detailUrl);
            const detailData = await response.json();
            updateNotificationDropdown(detailData.notifications);
            return detailData; // Mengembalikan data untuk digunakan oleh Polling utama
        } catch (error) {
            console.error("Gagal memuat detail notifikasi:", error);
            if(notificationList) notificationList.innerHTML = '<p class="text-center text-sm text-gray-500 p-4">Gagal memuat detail.</p>';
            return { count: 0 };
        }
    }

    function updateNotificationDropdown(notifications) {
        if (!notificationList) return;
        notificationList.innerHTML = ''; 

        if (notifications.length === 0) {
            notificationList.innerHTML = '<p class="text-center text-sm text-gray-500 p-4">Tidak ada pesanan baru.</p>';
            return;
        }

        notifications.forEach(notif => {
            const statusClass = notif.status === 'pending' ? 'bg-amber-500' : 'bg-indigo-500';
            
            // Menggunakan URL yang sudah dibuat di Controller (notif.url)
            const itemHtml = `
                <a href="${notif.url}" class="flex items-start p-3 hover:bg-indigo-50 border-b last:border-b-0 transition">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">
                            Pesanan #${notif.order_number || notif.id}
                            <span class="text-xs font-normal text-indigo-500 ml-1">(${notif.table_name})</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-clock mr-1"></i> ${notif.time_ago}
                        </p>
                    </div>
                    <span class="text-xs font-bold text-white px-2 py-0.5 rounded ${statusClass} flex-shrink-0">
                        ${notif.status.toUpperCase()}
                    </span>
                </a>
            `;
            notificationList.innerHTML += itemHtml;
        });
    }
    
    // Polling Utama
    async function startPolling() {
        // Inisialisasi awal (mendapatkan count dan mengisi dropdown)
        const initialDetailData = await fetchNotificationDetails();
        lastPendingCount = initialDetailData.count;
        
        // Update visual count awal
        if (lastPendingCount > 0) {
             if (notificationIndicator) notificationIndicator.classList.remove('hidden');
             if (notificationCountBadge) {
                 notificationCountBadge.innerText = lastPendingCount;
                 notificationCountBadge.classList.remove('hidden');
             }
        }


        setInterval(async () => {
            try {
                // Ambil data notifikasi detail (untuk mengisi dropdown)
                const detailData = await fetchNotificationDetails();
                const currentPendingCount = detailData.count;

                // 1. Logika Notifikasi SweetAlert (Jika Count Bertambah)
                if (currentPendingCount > lastPendingCount) {
                    const newOrdersCount = currentPendingCount - lastPendingCount;

                    // PANGGIL SUARA DAN SWEETALERT
                    playNotificationSound(); 
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '🔔 PESANAN BARU MASUK!',
                            html: `${newOrdersCount} pesanan tunai menunggu konfirmasi.`,
                            icon: 'warning',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            confirmButtonText: 'Lihat Pesanan',
                        }).then((result) => {
                            if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                                 window.location.href = "{{ route('admin.orders.index') }}";
                            }
                        });
                    }
                }
                
                // 2. UPDATE COUNT dan Visual Indicator (setelah SweetAlert)
                lastPendingCount = currentPendingCount;
                
                if (notificationIndicator && notificationCountBadge) {
                    if (currentPendingCount > 0) {
                         notificationIndicator.classList.remove('hidden');
                         notificationCountBadge.innerText = currentPendingCount;
                         notificationCountBadge.classList.remove('hidden');
                    } else {
                         notificationIndicator.classList.add('hidden');
                         notificationCountBadge.classList.add('hidden');
                    }
                }

            } catch (error) {
                console.error("Gagal melakukan polling:", error);
            }
        }, 8000); // Poll setiap 8 detik
    }

    document.addEventListener('DOMContentLoaded', startPolling);
</script>
</body>

</html>