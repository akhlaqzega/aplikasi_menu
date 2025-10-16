<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tampilkan laporan kinerja dan pendapatan.
     */
    public function index(Request $request)
    {
        // 1. Tentukan Rentang Tanggal
        // Default: Hari ini
        $today = Carbon::today();
        
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : $today->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $today->endOfDay();

        // Jika start_date dan end_date sama, atur rentang ke satu hari penuh
        if ($startDate->toDateString() == $endDate->toDateString()) {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        // 2. Query Data Pesanan Berdasarkan Rentang Tanggal
        $ordersInPeriod = Order::whereBetween('created_at', [$startDate, $endDate]);

        // 3. Perhitungan Ringkasan (Summary)
        
        // Pesanan Selesai
        $completedOrders = (clone $ordersInPeriod)->where('status', 'completed');
        $totalCompleted = $completedOrders->count();
        $totalRevenue = $completedOrders->sum('total_price');

        // Pesanan Dibatalkan
        $canceledOrders = (clone $ordersInPeriod)->where('status', 'canceled');
        $totalCanceled = $canceledOrders->count();
        $totalLossFromCanceled = $canceledOrders->sum('total_price');
        
        // Total Pesanan Masuk
        $totalOrders = $ordersInPeriod->count();

        // Data Laporan Ringkas
        $summary = [
            'total_revenue' => $totalRevenue,
            'total_completed' => $totalCompleted,
            'total_canceled' => $totalCanceled,
            'total_orders' => $totalOrders,
            'total_loss_canceled' => $totalLossFromCanceled,
        ];
        
        // 4. Query Detail Transaksi (Untuk Tabel Audit di bawah)
        // Ambil SEMUA pesanan dalam periode tersebut
        $detailedTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('admin.reports.index', [
            'summary' => $summary,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'detailedTransactions' => $detailedTransactions, // <--- Data Detail Transaksi
        ]);
    }
}