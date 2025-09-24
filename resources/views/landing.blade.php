<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang di CafeKU</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="relative min-h-screen flex items-center justify-center bg-blue-600">
        <div class="relative z-10 text-center p-8 bg-white rounded-xl shadow-2xl max-w-lg mx-auto">
            <h1 class="text-5xl font-extrabold text-blue-800 mb-4 animate-fade-in">CafeKU</h1>
            <p class="text-xl text-gray-700 mb-6">
                Nikmati hidangan lezat dan minuman terbaik kami.
                Silakan jelajahi menu untuk memulai pesanan Anda.
            </p>
            <a href="{{ route('customer.menu.index') }}" 
               class="inline-block px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-full 
                      hover:bg-blue-700 transition transform hover:scale-105 shadow-md">
                Lihat Menu
            </a>
        </div>
    </div>
</body>
</html>