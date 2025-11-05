@extends('layouts.admin')

@section('title', 'Ringkasan Operasional & Kinerja')

@section('content')
    
    <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Operasional</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Pesanan Menunggu --}}
        <a href="{{ route('admin.orders.index') }}" class="stat-card pending bg-white p-6 rounded-2xl shadow-lg border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pesanan Menunggu</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-1 pulse-animation">{{ $pendingCount ?? 0 }}</h2>
                    <span class="text-xs text-gray-400">Menunggu Pembayaran</span>
                </div>
                <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white p-4 rounded-full shadow-md">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </a>

        {{-- Sedang Diproses --}}
        <a href="{{ route('admin.orders.index') }}" class="stat-card in-progress bg-white p-6 rounded-2xl shadow-lg border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-1">{{ $inProgressCount ?? 0 }}</h2>
                    <span class="text-xs text-gray-400">Order di Dapur/Diantar</span>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-indigo-500 text-white p-4 rounded-full shadow-md">
                    <i class="fas fa-concierge-bell fa-2x"></i>
                </div>
            </div>
        </a>

        {{-- Selesai --}}
        <a href="{{ route('admin.orders.history') }}" class="stat-card completed bg-white p-6 rounded-2xl shadow-lg border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-1">{{ $completedCount ?? 0 }}</h2>
                    <span class="text-xs text-gray-400">Pesanan Telah Diterima</span>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-teal-500 text-white p-4 rounded-full shadow-md">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </a>

        {{-- Dibatalkan --}}
        <a href="{{ route('admin.orders.history') }}" class="stat-card canceled bg-white p-6 rounded-2xl shadow-lg border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dibatalkan</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-1">{{ $canceledCount ?? 0 }}</h2>
                    <span class="text-xs text-gray-400">Otomatis / Manual</span>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-pink-500 text-white p-4 rounded-full shadow-md">
                    <i class="fas fa-times-circle fa-2x"></i>
                </div>
            </div>
        </a>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mb-4 pt-4 border-t">Kinerja Keuangan Utama</h2>
    
    {{-- DROPDOWN FILTER WAKTU KUSTOM & INPUT TANGGAL --}}
    <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow-inner border border-gray-200">
        <form id="time-filter-form" action="{{ route('admin.dashboard') }}" method="GET">
            <div class="flex items-end justify-between gap-4 flex-wrap">
                <h3 class="text-lg font-semibold text-gray-700 flex-shrink-0">Pilih Periode Kinerja:</h3>
                
                <div class="flex items-center gap-3 flex-wrap">
                    
                    {{-- 1. DROPDOWN UTAMA --}}
                    <select name="time_filter" id="time-filter" onchange="toggleCustomDates();"
                        class="block py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-semibold w-40">
                        
                        <option value="today" {{ $timeFilter == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="yesterday" {{ $timeFilter == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="this_week" {{ $timeFilter == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_month" {{ $timeFilter == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="this_year" {{ $timeFilter == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $timeFilter == 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                        
                    </select>

                    {{-- 2. INPUT TANGGAL KUSTOM (Hanya Muncul saat 'custom' dipilih) --}}
                    <div id="custom-dates" class="flex gap-3">
                        <input type="date" name="start_date" placeholder="Dari Tanggal" value="{{ $customDate['start'] ?? '' }}"
                            class="block py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm text-sm focus:ring-purple-500 w-36">
                        <input type="date" name="end_date" placeholder="Sampai Tanggal" value="{{ $customDate['end'] ?? '' }}"
                            class="block py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm text-sm focus:ring-purple-500 w-36">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md text-sm">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-teal-600 card-hover">
            <p class="text-sm font-medium text-gray-500">TOTAL PENDAPATAN UNTUK PERIODE: <span class="font-bold text-teal-700">{{ $filteredLabel }}</span></p>
            <h2 class="text-3xl font-extrabold text-teal-700 mt-1">
                Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
            </h2>
            <span class="text-sm text-gray-500 mt-2">*Termasuk status Processing, Delivering, dan Completed.</span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-red-600 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">TOTAL KERUGIAN PEMBATALAN: <span class="font-bold text-red-700">{{ $filteredLabel }}</span></p>
                    <h2 class="text-3xl font-extrabold text-red-700 mt-1">
                        Rp{{ number_format($filteredLoss ?? 0, 0, ',', '.') }} {{-- Menggunakan variabel yang sudah difilter --}}
                    </h2>
                    <span class="text-xs text-gray-400">Nilai pesanan dibatalkan pada periode yang dipilih.</span>
                </div>
                <div class="bg-red-500/20 text-red-600 p-3 rounded-full">
                    <i class="fas fa-times fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="text-xl font-bold text-gray-800 mb-4 pt-4 border-t">Visualisasi Pendapatan 7 Hari Terakhir</h2>
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 mb-8">
        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // === FLAG KRITIS UNTUK MENCEGAH REFRESH LOOP ===
    let isInitializing = true; 

    // FUNGSI TOGGLE INPUT TANGGAL KUSTOM
    function toggleCustomDates() {
        const filter = document.getElementById('time-filter').value;
        const customDatesDiv = document.getElementById('custom-dates');
        
        if (filter === 'custom') {
            customDatesDiv.classList.remove('hidden');
        } else {
            customDatesDiv.classList.add('hidden');
            
            // KRITIS: HANYA SUBMIT JIKA SUDAH SELESAI INISIALISASI
            if (!isInitializing) {
                 document.getElementById('time-filter-form').submit();
            }
        }
    }

    // --- KODE CHART.JS ---
    function initializeRevenueChart() {
        const canvasElement = document.getElementById('revenueChart');
        if (!canvasElement) return;

        const ctx = canvasElement.getContext('2d');
        
        if (Chart.getChart(canvasElement)) {
            Chart.getChart(canvasElement).destroy();
        }

        // Data chart di-parse di sini
        const defaultChartData = {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            data: [0, 0, 0, 0, 0, 0, 0]
        };
        
        // Gunakan JSON.parse dengan unescaped string
        const chartDataRaw = '{!! json_encode($chartData ?? $defaultChartData) !!}'; 

        let chartData;
        try {
            chartData = JSON.parse(chartDataRaw);
        } catch (e) {
            console.error("Gagal mem-parsing chart data dari server:", e);
            chartData = defaultChartData; 
        }

        // --- WARNA DAN GRADIENT ---
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(129, 140, 248, 0.9)'); // Indigo-400 Awal
        gradient.addColorStop(1, 'rgba(129, 140, 248, 0.05)'); // Transparan Akhir

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels, 
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartData.data, 
                    backgroundColor: gradient,
                    borderColor: '#4338CA', // Indigo-700
                    borderWidth: 4, 
                    pointBackgroundColor: '#6366F1', // Indigo-500
                    pointBorderColor: '#FFFFFF',
                    pointHoverRadius: 8, 
                    pointRadius: 6, 
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeInOutQuart' },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Pendapatan Bersih (Rp)' },
                        grid: { color: 'rgba(129, 140, 248, 0.2)', borderDash: [5, 5] },
                        ticks: {
                            precision: 0,
                            callback: function(value) {
                                // Format Rupiah
                                if (value >= 1000000) { return 'Rp' + (value / 1000000).toFixed(1) + ' Jt'; } 
                                else if (value >= 1000) { return 'Rp' + (value / 1000) + 'K'; }
                                return 'Rp' + value;
                            }
                        }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1F2937',
                        titleFont: { size: 16 },
                        bodyFont: { size: 14 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Panggil saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Panggil toggle agar state awal (old input) tetap terlihat
        toggleCustomDates(); 
        
        // Setelah 200ms, set flag agar submit otomatis diizinkan.
        setTimeout(() => {
            isInitializing = false;
        }, 200); 

        // Panggil inisialisasi chart
        setTimeout(initializeRevenueChart, 100); 
    });
</script>
@endpush