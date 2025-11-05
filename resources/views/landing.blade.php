<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di CafeKU - Kopi & Kenangan</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Custom animation classes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Applying delays for staggered effect */
        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.4s;
        }

        .delay-3 {
            animation-delay: 0.6s;
        }

        .delay-4 {
            animation-delay: 0.8s;
        }

        /* Make animation invisible until it starts */
        [class*="animate-"] {
            opacity: 0;
        }
    </style>
</head>

<body class="bg-zinc-900 text-white">

    <!-- Main Container -->
    <div class="relative min-h-screen w-full flex flex-col">

        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?q=80&w=2071&auto=format&fit=crop"
                alt="Suasana interior CafeKU yang hangat dan nyaman" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900 via-zinc-900/80 to-transparent"></div>
        </div>

        <!-- Navigation -->
        <header class="relative z-10 p-6 md:p-8">
            <div class="container mx-auto flex justify-between items-center">
                <a href="#" class="text-2xl font-bold tracking-wider animate-fade-in-up delay-1">CafeKU</a>

            </div>
        </header>

        <!-- Hero Section -->
        <main class="relative z-10 flex-grow flex items-center">
            <div class="container mx-auto text-center px-6">
                <h1
                    class="text-5xl md:text-7xl lg:text-8xl font-extrabold leading-tight mb-4 animate-fade-in-up delay-1">
                    Secangkir Cerita, <br class="hidden sm:block"> Sejuta <span class="text-amber-400">Rasa.</span>
                </h1>
                <p class="text-xl md:text-2xl text-zinc-300 max-w-2xl mx-auto mb-10 animate-fade-in-up delay-2">
                    Nikmati sajian kopi pilihan dan hidangan lezat yang dibuat dengan hati, langsung dari meja Anda.
                </p>
                <a href="/menu"
                    class="inline-flex items-center justify-center px-10 py-4 text-lg font-bold text-zinc-900 
                          bg-amber-400 rounded-full transition duration-300 
                          hover:bg-amber-300 transform hover:scale-105 shadow-lg shadow-amber-500/20
                          animate-fade-in-up delay-3">
                    <i class="fas fa-book-open mr-3"></i>
                    Lihat Menu
                </a>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 p-6 md:p-8">
            <div class="container mx-auto text-center text-zinc-400">
                <div class="flex justify-center space-x-6 mb-4 animate-fade-in-up delay-4">
                    <a href="#" aria-label="Instagram"
                        class="text-2xl hover:text-amber-400 transition duration-300"><i
                            class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"
                        class="text-2xl hover:text-amber-400 transition duration-300"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"
                        class="text-2xl hover:text-amber-400 transition duration-300"><i class="fab fa-twitter"></i></a>
                </div>
                <p class="animate-fade-in-up delay-4">&copy; 2024 CafeKU. Semua Hak Cipta Dilindungi.</p>
            </div>
        </footer>

    </div>

</body>

</html>
