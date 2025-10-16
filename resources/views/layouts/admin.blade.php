<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CafeKU Admin Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- CSS Khusus Cetak (MENGHILANGKAN SIDEBAR DAN HEADER) --}}
    <style>
        @media print {

            /* Sembunyikan Sidebar Admin (w-64) dan Header Navigasi saat mencetak */
            .sidebar-admin,
            header {
                display: none !important;
            }

            /* Atur Main Content agar mengambil lebar penuh */
            .main-content {
                padding: 0;
                margin: 0;
                width: 100%;
                /* Batalkan efek flex-shrink-0 pada kontainer utama */
                flex-grow: 1;
                overflow: visible !important;
            }

            /* Hapus batasan lebar pada kontainer max-w-7xl */
            .max-w-7xl {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Hilangkan border pada elemen yang tidak perlu dicetak */
            .shadow-xl,
            .shadow-md,
            .rounded-lg,
            .rounded-xl,
            .border,
            .border-l-4 {
                box-shadow: none !important;
                border: none !important;
                border-left: none !important;
            }
        }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div id="app" class="flex h-screen overflow-hidden">

        {{-- SIDEBAR ADMIN (Ditambahkan class 'sidebar-admin' untuk keperluan cetak) --}}
        <div class="flex flex-col w-64 bg-blue-800 text-white shadow-2xl relative z-30 flex-shrink-0 sidebar-admin">
            <div class="flex items-center justify-center h-16 bg-blue-900 shadow-lg">
                <span class="text-xl font-extrabold tracking-wider uppercase">CAFEKU KASIR</span>
            </div>

            <nav class="flex-grow p-4 space-y-1 overflow-y-auto">
                <p class="text-xs text-blue-300 uppercase font-semibold mb-2 mt-2">Umum</p>

                {{-- Tautan 1: DASHBOARD UMUM (Statistik) --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Ringkasan Dashboard</span>
                </a>

                <p class="text-xs text-blue-300 uppercase font-semibold mb-2 mt-4 pt-2 border-t border-blue-700">
                    Manajemen Pesanan</p>

                {{-- Tautan 2: PESANAN AKTIF --}}
                <a href="{{ route('admin.orders.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.orders.index') && !request()->routeIs('admin.orders.history') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span>Pesanan Aktif</span>
                </a>

                {{-- Tautan RIWAYAT PESANAN --}}
                <a href="{{ route('admin.orders.history') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.orders.history') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-history w-5"></i>
                    <span>Riwayat Pesanan</span>
                </a>

                <p class="text-xs text-blue-300 uppercase font-semibold mb-2 mt-4 pt-2 border-t border-blue-700">Data
                    Master</p>

                {{-- Tautan BARU: KELOLA MEJA --}}
                <a href="{{ route('admin.tables.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.tables.*') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-chair w-5"></i>
                    <span>Kelola Meja</span>
                </a>

                {{-- Tautan KELOLA PRODUK --}}
                <a href="{{ route('admin.menus.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.menus.*') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-utensils w-5"></i>
                    <span>Kelola Produk</span>
                </a>

                {{-- Tautan KELOLA KATEGORI --}}
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span>Kelola Kategori</span>
                </a>

                <p class="text-xs text-blue-300 uppercase font-semibold mb-2 mt-4 pt-2 border-t border-blue-700">Laporan
                </p>

                {{-- Tautan LAPORAN KEUANGAN/KINERJA --}}
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                    {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 shadow-md' : 'hover:bg-blue-700' }}">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>Laporan Kinerja</span>
                </a>

            </nav>

            <div class="p-4 border-t border-blue-700">
                <p class="text-sm text-blue-200 mb-2">Login: {{ Auth::user()->name ?? 'Admin' }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left flex items-center space-x-3 p-2 rounded-lg bg-red-700 hover:bg-red-800 transition duration-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- MAIN CONTENT (Ditambahkan class 'main-content' untuk keperluan cetak) --}}
        <div class="flex-grow flex flex-col overflow-y-auto main-content">
            <header class="h-16 bg-white shadow-md flex items-center px-6 justify-between flex-shrink-0">
                <h1 class="text-xl font-semibold text-gray-700">@yield('title', 'Admin Panel')</h1>
            </header>

            <main class="flex-grow p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
</body>

</html>
