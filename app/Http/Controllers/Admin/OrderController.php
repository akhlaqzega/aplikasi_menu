<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 30;


    public function dashboard(Request $request)
    {
        // 1. Tentukan Filter dan Periode Waktu
        // Filter kustom: 'today', 'yesterday', 'this_week', 'this_month', 'this_year', 'custom'
        $timeFilter = $request->input('time_filter', 'this_month'); // Default: Bulan Ini

        $revenueStatuses = ['completed', 'processing', 'delivering'];

        // Tentukan Rentang Waktu Awal/Akhir untuk Filter Dinamis
        $startDate = null;
        $endDate = Carbon::now();
        $filteredLabel = '';
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        switch ($timeFilter) {
            case 'today':
                $startDate = Carbon::today();
                $filteredLabel = 'Hari Ini';
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                $filteredLabel = 'Kemarin';
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $filteredLabel = 'Minggu Ini';
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $filteredLabel = 'Bulan Ini';
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $filteredLabel = 'Tahun Ini';
                break;
            case 'custom':
                if ($customStartDate && $customEndDate) {
                    $startDate = Carbon::parse($customStartDate)->startOfDay();
                    $endDate = Carbon::parse($customEndDate)->endOfDay();
                    $filteredLabel = 'Rentang Kustom';
                } else {
                    $startDate = Carbon::today();
                    $filteredLabel = 'Hari Ini (Default)';
                }
                break;
            default:
                $startDate = Carbon::today();
                $filteredLabel = 'Hari Ini (Default)';
                break;
        }

        // ===============================================
        // 2. STATISTIK PESANAN & METRIK STATIS
        // ===============================================
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['processing', 'delivering'])->count();
        $completedCount = Order::where('status', 'completed')->count();
        $canceledCount = Order::where('status', 'canceled')->count();

        // Total Kerugian Statis (TOTAL KESELURUHAN - Tidak difilter)
        $totalLossAccumulated = Order::where('status', 'canceled')->sum('total_price');

        // ===============================================
        // 3. METRIK FILTERED (untuk Kartu Utama)
        // ===============================================

        $queryRevenue = Order::whereIn('status', $revenueStatuses);
        $queryLoss = Order::where('status', 'canceled'); // Query Kerugian

        if ($startDate) {
            $queryRevenue->whereBetween('created_at', [$startDate, $endDate]);
            $queryLoss->whereBetween('created_at', [$startDate, $endDate]); // KRITIS: Filter diterapkan pada Kerugian
        }

        $totalRevenue = $queryRevenue->sum('total_price');
        $filteredLoss = $queryLoss->sum('total_price'); // <-- Kerugian yang sudah difilter

        // 4. DATA UNTUK GRAFIK (7 hari terakhir, statis)
        $chartData = $this->getWeeklyRevenueData();

        // Kirim tanggal kustom kembali ke view
        $customDate = [
            'start' => $customStartDate,
            'end' => $customEndDate
        ];

        return view('admin.dashboard', compact(
            'pendingCount',
            'inProgressCount',
            'completedCount',
            'canceledCount',
            'totalRevenue',
            'filteredLoss',    // <-- Kerugian yang SUDAH difilter
            'filteredLabel',
            'timeFilter',
            'chartData',
            'customDate'
        ));
    }

    /**
     * Fungsi Helper untuk mengambil data pendapatan harian selama 7 hari terakhir (statis).
     */
    protected function getWeeklyRevenueData()
    {
        $days = [];
        $revenues = [];
        $today = Carbon::today();

        $revenueStatuses = ['completed', 'processing', 'delivering'];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $days[] = $date->isoFormat('dd');

            $revenue = Order::whereDate('created_at', $date)
                ->whereIn('status', $revenueStatuses)
                ->sum('total_price');

            $revenues[] = (float) $revenue;
        }

        return [
            'labels' => $days,
            'data' => $revenues
        ];
    }


    public function index()
    {
        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;

        // PERBAIKAN: Hapus with('table') di sini. Kita akan pakai Table::find() di loop.
        $pendingOrders = Order::where('status', 'pending')->get();

        foreach ($pendingOrders as $order) {
            $createdAt = Carbon::parse($order->created_at);
            $now = Carbon::now();

            if ($createdAt->diffInSeconds($now) >= $timeoutSeconds) {

                $order->status = 'canceled';
                $order->save();

                // Logika Pembebasan Meja karena Canceled/Timeout
                // Table::find() menjamin kita mengambil data TERBARU dari DB
                $table = Table::find($order->table_id);
                if ($table) {
                    if ($table->status === 'occupied') {
                        $table->update(['status' => 'available']);
                    }
                }
            }
        }

        $activeOrders = Order::whereIn('status', ['pending', 'processing', 'delivering'])
            ->with('table', 'payment', 'orderItems.menu')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.orders.index', compact('activeOrders'));
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.menu', 'table', 'payment']);
        return view('admin.orders.show', compact('order'));
    }


    public function updateStatus(Request $request, Order $order)
    {
        // Validasi status baru
        $request->validate([
            'status' => 'required|string|in:pending,processing,delivering,completed,canceled',
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        // Guard: Mencegah perubahan jika order sudah dibatalkan (kecuali status baru juga 'canceled', 
        // meskipun ini tidak logis, kita biarkan logic Anda yang mengurusnya)
        if ($oldStatus === 'canceled' && $newStatus !== 'canceled') {
            return redirect()->back()->with('error', "Pesanan #{$order->id} telah dibatalkan dan tidak bisa diproses.");
        }

        // 1. Perbarui status pesanan utama
        $order->status = $newStatus;
        $order->save();

        // 2. LOGIKA UTAMA PEMBAYARAN (KRITIS)
        if ($order->payment) {

            // KASUS A: Konfirmasi Pembayaran Tunai (Pending -> Processing)
            // Jika status baru adalah 'processing' dan sebelumnya 'pending'
            if ($newStatus === 'processing' && $oldStatus === 'pending') {
                // Asumsi: Pembayaran cash BARU DITERIMA. Set status pembayaran menjadi 'paid'.
                $order->payment->update(['status' => 'paid']);
            }

            // KASUS B: Pesanan Selesai (Pastikan status bayar final adalah PAID)
            // Ini penting untuk pembayaran non-tunai atau memastikan data final konsisten
            if ($newStatus === 'completed' && $order->payment->status !== 'paid') {
                $order->payment->update(['status' => 'paid']);
            }
        }

        // 3. LOGIKA MEJA (Melepaskan/Mengubah status meja)
        $table = Table::find($order->table_id);

        if ($table) {
            $activeStatuses = ['pending', 'processing', 'delivering'];

            if ($newStatus === 'completed' || $newStatus === 'canceled') {
                // Pembebasan Meja: Selesai atau Dibatalkan
                if ($table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }
            } elseif (in_array($newStatus, $activeStatuses) && !in_array($oldStatus, $activeStatuses)) {
                // Menduduki Meja Kembali (Jika diubah dari completed/canceled/initial ke aktif)
                if ($table->status === 'available') {
                    $table->update(['status' => 'occupied']);
                }
            }
        }

        return redirect()->back()->with('success', "Status Pesanan #{$order->id} berhasil diperbarui menjadi " . strtoupper($newStatus));
    }


    public function history()
    {
        $eagerLoads = ['table', 'payment', 'orderItems.menu'];

        $canceledOrders = Order::where('status', 'canceled')
            ->with($eagerLoads)
            ->orderBy('created_at', 'asc')->get();

        $completedOrders = Order::where('status', 'completed')
            ->with($eagerLoads)
            ->orderBy('updated_at', 'asc')->get();

        return view('admin.orders.history', compact('canceledOrders', 'completedOrders'));
    }


    public function checkNewOrders()
    {
        // Hitung pesanan HANYA jika statusnya 'pending' (yaitu cash order)
        $newOrderCount = Order::whereIn('status', ['pending', 'processing'])->count();
        // ABAIKAN status 'initial'

        return response()->json([
            // Ganti nama variabel agar lebih sesuai (new_count)
            'new_count' => $newOrderCount,
            'has_new' => $newOrderCount > 0
        ]);
    }

    public function getNotificationDetails()
    {
        $newOrders = Order::whereIn('status', ['pending', 'processing'])
            ->with('table') // Ambil data meja untuk tampilan
            ->orderBy('created_at', 'desc')
            ->take(5) // Batasi maksimal 5 notifikasi terbaru
            ->get();
            
        $showRouteBaseUrl = route('admin.orders.show', ['order' => 'BASE_ID'], false);
        // Ganti placeholder BASE_ID dengan string kosong untuk digunakan di JS
        $showRouteBaseUrl = str_replace('BASE_ID', '', $showRouteBaseUrl);
        // Mapping data agar mudah digunakan di JavaScript
        $notifications = $newOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'table_name' => $order->table->name ?? 'Ambil',
                'status' => $order->status,
                'time_ago' => $order->created_at->diffForHumans(), // Waktu relatif
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }
}
