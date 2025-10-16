<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 15;

    public function dashboard()
    {
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['processing', 'delivering'])->count();
        $completedCount = Order::where('status', 'completed')->count();
        $canceledCount = Order::where('status', 'canceled')->count();

        return view('admin.dashboard', compact('pendingCount', 'inProgressCount', 'completedCount', 'canceledCount'));
    }

    public function index()
    {
        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;

        // Eager load table untuk menghindari N+1 query pada loop timeout
        $pendingOrders = Order::where('status', 'pending')->with('table')->get();

        foreach ($pendingOrders as $order) {
            $createdAt = Carbon::parse($order->created_at);
            $now = Carbon::now();

            if ($createdAt->diffInSeconds($now) >= $timeoutSeconds) {

                $order->status = 'canceled';
                $order->save();

                // Logika Pembebasan Meja karena Canceled/Timeout
                $table = $order->table;
                if ($table && $table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }
            }
        }

        $activeOrders = Order::whereIn('status', ['pending', 'processing', 'delivering'])
            ->with('table', 'payment', 'orderItems.menu')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.orders.index', compact('activeOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->status === 'canceled' && $request->input('status') !== 'canceled') {
            return redirect()->back()->with('error', "Pesanan #{$order->id} telah dibatalkan dan tidak bisa diproses.");
        }

        $request->validate([
            'status' => 'required|string|in:pending,processing,delivering,completed,canceled',
        ]);

        $newStatus = $request->input('status');
        $oldStatus = $order->status;

        $order->status = $newStatus;
        $order->save();

        // 1. Logika Update Pembayaran (jika selesai)
        if ($newStatus === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid']);
        }

        // 2. Logika Pembebasan Meja saat status diubah ke 'completed'
        if ($newStatus === 'completed' && $order->table) {
            if ($order->table->status === 'occupied') {
                $order->table->update(['status' => 'available']);
            }
        }

        // 3. Logika Pembebasan Meja saat status diubah ke 'canceled' oleh Admin
        if ($newStatus === 'canceled' && $order->table) {
            if ($order->table->status === 'occupied') {
                $order->table->update(['status' => 'available']);
            }
        }

        // Pengecekan: Jika status diubah kembali dari 'completed' atau 'canceled' ke status aktif,
        // maka status meja harus kembali ke 'occupied' (jika meja ditemukan)
        $activeStatuses = ['pending', 'processing', 'delivering'];
        if (in_array($newStatus, $activeStatuses) && !in_array($oldStatus, $activeStatuses) && $order->table) {
            if ($order->table->status === 'available') {
                $order->table->update(['status' => 'occupied']);
            }
        }

        return redirect()->back()->with('success', "Status Pesanan #{$order->id} berhasil diperbarui menjadi " . strtoupper($newStatus));
    }

    public function history()
    {
        $eagerLoads = ['table', 'payment', 'orderItems.menu'];

        $canceledOrders = Order::where('status', 'canceled')
            ->with($eagerLoads)
            ->orderBy('created_at', 'desc')->get();

        $completedOrders = Order::where('status', 'completed')
            ->with($eagerLoads)
            ->orderBy('updated_at', 'desc')->get();

        return view('admin.orders.history', compact('canceledOrders', 'completedOrders'));
    }


    public function checkNewOrders()
    {
        // Cek jumlah pesanan yang statusnya 'pending'
        $newPendingCount = Order::where('status', 'pending')->count();

        return response()->json([
            'new_count' => $newPendingCount,
            'has_new' => $newPendingCount > 0
        ]);
    }
}
