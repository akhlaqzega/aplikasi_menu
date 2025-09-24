<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-primary-500 to-primary-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Dashboard Kasir</h1>
                    <p class="text-primary-100">Kelola pesanan dengan mudah</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-2 rounded-full">
                        <i class="fas fa-cash-register text-xl"></i>
                    </div>
                    <div>
                        <p class="font-semibold">Admin Kasir</p>
                        <p class="text-xs text-primary-100">Terakhir login: Hari ini</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-primary-100">Pesanan Menunggu</p>
                        <h2 class="text-3xl font-bold mt-2">{{ $pendingOrders->count() }}</h2>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-danger-500 to-danger-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-danger-100">Pesanan Dibatalkan</p>
                        <h2 class="text-3xl font-bold mt-2">{{ $canceledOrders->count() }}</h2>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-success-500 to-success-600 text-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-success-100">Pesanan Selesai</p>
                        <h2 class="text-3xl font-bold mt-2">{{ $completedOrders->count() }}</h2>
                    </div>
                    <div class="bg-white/20 p-4 rounded-full">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pesanan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Pesanan Menunggu -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-warning-500">
                <div class="bg-gradient-to-r from-warning-50 to-white p-4 border-b">
                    <div class="flex items-center">
                        <div class="bg-warning-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-clock text-warning-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-warning-800">Pesanan Menunggu</h2>
                    </div>
                </div>

                <div class="p-4">
                    @if ($pendingOrders->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada pesanan yang menunggu saat ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-gray-500 text-sm border-b">
                                        <th class="py-3 px-2">ID Pesanan</th>
                                        <th class="py-3 px-2">Pelanggan</th>
                                        <th class="py-3 px-2">Total</th>
                                        <th class="py-3 px-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingOrders as $order)
                                        <tr class="border-b hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-2 font-medium">#{{ $order->id }}</td>
                                            <td class="py-3 px-2">{{ $order->customer_name }}</td>
                                            <td class="py-3 px-2 font-semibold">
                                                Rp{{ number_format($order->total_price, 2, ',', '.') }}</td>
                                            <td class="py-3 px-2 text-center">
                                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit"
                                                        class="bg-success-500 text-white px-3 py-1 rounded-lg hover:bg-success-600 transition flex items-center justify-center mx-auto">
                                                        <i class="fas fa-check mr-1"></i> Selesai
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pesanan Dibatalkan -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-danger-500">
                <div class="bg-gradient-to-r from-danger-50 to-white p-4 border-b">
                    <div class="flex items-center">
                        <div class="bg-danger-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-times-circle text-danger-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-danger-800">Pesanan Dibatalkan</h2>
                    </div>
                </div>

                <div class="p-4">
                    @if ($canceledOrders->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-ban text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada pesanan yang dibatalkan saat ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-gray-500 text-sm border-b">
                                        <th class="py-3 px-2">ID Pesanan</th>
                                        <th class="py-3 px-2">Pelanggan</th>
                                        <th class="py-3 px-2">Total</th>
                                        <th class="py-3 px-2">Waktu Dibatalkan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($canceledOrders as $order)
                                        <tr class="border-b text-gray-500">
                                            <td class="py-3 px-2">#{{ $order->id }}</td>
                                            <td class="py-3 px-2">{{ $order->customer_name }}</td>
                                            <td class="py-3 px-2">
                                                Rp{{ number_format($order->total_price, 2, ',', '.') }}</td>
                                            <td class="py-3 px-2 text-sm">{{ $order->updated_at->format('d M H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pesanan Selesai -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-success-500">
                <div class="bg-gradient-to-r from-success-50 to-white p-4 border-b">
                    <div class="flex items-center">
                        <div class="bg-success-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-check-circle text-success-600"></i>
                        </div>
                        <h2 class="text-xl font-bold text-success-800">Pesanan Selesai</h2>
                    </div>
                </div>

                <div class="p-4">
                    @if ($completedOrders->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-check text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada pesanan yang selesai.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-gray-500 text-sm border-b">
                                        <th class="py-3 px-2">ID Pesanan</th>
                                        <th class="py-3 px-2">Pelanggan</th>
                                        <th class="py-3 px-2">Total</th>
                                        <th class="py-3 px-2">Waktu Selesai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($completedOrders as $order)
                                        <tr class="border-b hover:bg-gray-50 transition-colors">
                                            <td class="py-3 px-2 font-medium">#{{ $order->id }}</td>
                                            <td class="py-3 px-2">{{ $order->customer_name }}</td>
                                            <td class="py-3 px-2 font-semibold">
                                                Rp{{ number_format($order->total_price, 2, ',', '.') }}</td>
                                            <td class="py-3 px-2 text-sm">{{ $order->updated_at->format('d M H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 mb-4 md:mb-0">
                    <p>&copy; {{ date('Y') }} Dashboard Kasir. Semua hak dilindungi.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-primary-600 transition">
                        <i class="fas fa-question-circle"></i> Bantuan
                    </a>
                    <a href="#" class="text-gray-500 hover:text-primary-600 transition">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
