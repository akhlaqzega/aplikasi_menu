<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function index()
    {
        $pendingOrders = Order::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $canceledOrders = Order::where('status', 'canceled')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('pendingOrders', 'canceledOrders', 'completedOrders'));
    }

    public function show(Order $order)
    {
        // ... kode yang sudah ada
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,completed,canceled'
        ]);

        $order->status = $request->status;
        $order->save();

        if ($request->status === 'completed') {
            // Kita akan menghapus session ini nanti, di halaman pelanggan
            // Session::forget('pending_order_id');
            Session::flash('customer_notification', 'Pesanan Anda telah dikonfirmasi dan sedang diproses!');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
