<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification; // Dipertahankan untuk handle Webhook, meskipun Anda mengabaikannya
use Illuminate\Support\Str;


class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 30;

    public function addToCart(Request $request)
    {
        $menu = Menu::find($request->input('menu_id'));
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan.'], 404);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity']++;
        } else {
            $cart[$menu->id] = [
                "id" => $menu->id,
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image,
            ];
        }

        Session::put('cart', $cart);

        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan ke keranjang!',
            'cart_count' => $totalQuantity
        ]);
    }

    public function cart()
    {
        $cart = Session::get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = Session::get('cart', []);
        $menuId = $request->input('menu_id');
        $quantity = $request->input('quantity');

        if ($quantity <= 0) {
            unset($cart[$menuId]);
        } else {
            if (isset($cart[$menuId])) {
                $cart[$menuId]['quantity'] = $quantity;
            }
        }
        Session::put('cart', $cart);

        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_price' => $total_price,
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $menu_id = $request->input('menu_id');
        $cart = Session::get('cart', []);

        if (isset($cart[$menu_id])) {
            unset($cart[$menu_id]);
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Menu berhasil dihapus dari keranjang.');
        }
        return redirect()->back()->with('error', 'Menu tidak ditemukan di keranjang.');
    }

    public function checkout()
    {
        $cart = Session::get('cart');

        if (empty($cart) && Session::has('online_pending_order_id')) {
            return view('customer.checkout', compact('tables')); // Tetap lanjut ke view checkout
        }
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong, tidak bisa melanjutkan ke checkout.');
        }

        $tables = Table::orderBy('name')->get();
        return view('customer.checkout', compact('tables'));
    }

  public function store(Request $request)
{
    $request->validate([
        'customer_name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:15',
        'table_name' => 'required|string|exists:tables,name',
        'notes' => 'nullable|string',
        'payment_method_type' => 'required|string|in:cash,online',
    ], [
        'table_name.exists' => 'Kode meja tidak valid atau tidak terdaftar.'
    ]);

    $cart = Session::get('cart', []);
    if (empty($cart)) {
        return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong.');
    }

    $table = Table::where('name', $request->input('table_name'))->first();
    if (!$table) {
        return redirect()->back()->withInput()->with('error', 'Meja tidak ditemukan.');
    }

    if ($table->status === 'occupied') {
        return redirect()->back()->withInput()->with('table_error', 'Meja ' . $table->name . ' sedang terisi. Harap periksa kode meja Anda atau tunggu meja tersebut selesai digunakan.');
    }

    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // ===============================================
    // KRITIS: GENERATE DAN PASTIKAN NOMOR UNIK
    // ===============================================
    do {
        // Format: CAFE + 6 karakter alfanumerik acak
        $uniqueOrderNumber = 'CAFE' . Str::upper(Str::random(6));
        $exists = Order::where('order_number', $uniqueOrderNumber)->exists();
    } while ($exists);
    // ===============================================

    $isCash = $request->input('payment_method_type') === 'cash';

    // Logika Status: Online = initial (sementara) agar tidak memicu notifikasi admin
    $orderStatus = $isCash ? 'pending' : 'initial';
    $paymentMethod = $isCash ? 'cash' : 'online';
    $paymentStatus = 'unpaid';

    $order = Order::create([
        'order_number' => $uniqueOrderNumber, // <-- SIMPAN NOMOR UNIK
        'customer_name' => $request->input('customer_name'),
        'phone_number' => $request->input('phone_number'),
        'table_id' => $table->id,
        'order_type' => 'dine_in',
        'total_price' => $total_price,
        'status' => $orderStatus,
        'notes' => $request->input('notes'),
    ]);

    foreach ($cart as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
        ]);
    }

    Payment::create([
        'order_id' => $order->id,
        'amount' => $order->total_price,
        'method' => $paymentMethod,
        'status' => $paymentStatus
    ]);

    $table->update(['status' => 'occupied']);

    Session::forget('cart');

    if ($isCash) {
        // Cash: set session ID agar tombol "Pesanan Saya" muncul
        Session::put('pending_order_id', $order->id);
        return redirect()->route('customer.payment.cash', ['order' => $order->id]);
    } else {
        // Online: tidak perlu set session ID pending, langsung ke Snap
        Session::forget('pending_order_id');
        return $this->onlineCheckout($request, $order);
    }
}
    // METHOD BARU: Digunakan oleh Client-side (Snap onSuccess) untuk konfirmasi status
    public function updateStatusSuccess(Order $order)
    {
        // Cek apakah order masih dalam status awal ('initial') atau belum terbayar ('pending').
        if ($order->status === 'initial' || $order->status === 'pending' || $order->payment->status === 'unpaid') {

            // KRITIS: Pindah ke 'processing' dan 'paid'
            $order->status = 'processing'; // <-- Admin kini akan melihat notifikasi ini!
            $order->payment->status = 'paid';
            $order->payment->save();
            $order->save();
        }

        // Redirect ke halaman konfirmasi final
        return redirect()->route('customer.order.online-confirmed', $order);
    }

    public function cashInstructions(Order $order)
    {
        // Guard 1: Untuk pesanan Online (Jika terdeteksi status non-cash/non-pending)
        if ($order->payment && $order->payment->method !== 'cash') {
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;
        $expirationTime = $order->created_at->copy()->addSeconds($timeoutSeconds);
        $isTimedOut = $order->status === 'pending' && $expirationTime->isPast();

        // KASUS 1: Timeout terjadi saat ini (HANYA CASH) - JIKA USER ME-REFRESH SETELAH WAKTU HABIS
        if ($isTimedOut) {
            // Lakukan pembatalan dan bebaskan meja
            DB::transaction(function () use ($order) {
                $order->status = 'canceled';
                $order->save();

                $table = Table::find($order->table_id);
                if ($table && $table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }
                \Illuminate\Support\Facades\Session::forget('pending_order_id');
            });
            // Setelah dibatalkan, kita biarkan kode mengalir ke KASUS 3
            $order->refresh(); // Penting: Muat ulang objek $order setelah transaksi
        }

        // KASUS 2: Pesanan sudah selesai atau diproses (dikonfirmasi oleh Admin)
        if ($order->status === 'processing' || $order->status === 'delivering' || $order->status === 'completed') {
            \Illuminate\Support\Facades\Session::forget('pending_order_id');
            // Redirect ke halaman konfirmasi
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        // KASUS 3: Pesanan sudah dibatalkan (termasuk hasil dari KASUS 1)
        if ($order->status === 'canceled') {
            \Illuminate\Support\Facades\Session::forget('pending_order_id');
            // KRITIS: Muat View yang sama agar Blade menampilkan bagian CANCELED
            return view('customer.payment-cash-instructions', compact('order'));
        }

        // KASUS 4: PENDING dan masih dalam waktu
        if ($order->status === 'pending') {
            $expirationTimestamp = $expirationTime->timestamp * 1000;
            return view('customer.payment-cash-instructions', compact('order', 'expirationTimestamp'));
        }

        // Default fallback
        return redirect()->route('customer.menu.index')->with('error', 'Pesanan tidak valid atau tidak ditemukan.');
    }

    public function showTrackForm()
    {
        return view('customer.track');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:15',
        ]);

        $order = Order::where('phone_number', $request->phone_number)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('customer.track', compact('order'));
    }

    public function checkOrderStatus(Order $order)
    {
        // Hanya kembalikan status order dan status pembayaran
        return response()->json([
            'order_status' => $order->status,
            'payment_status' => $order->payment?->status ?? 'unpaid'
        ]);
    }

    public function onlineConfirmed(Order $order)
    {
        // Hapus session order pending karena order sudah sukses (processing/paid)
        \Illuminate\Support\Facades\Session::forget('pending_order_id');

        // View yang akan menggunakan order-online-confirmed.blade.php
        return view('customer.order-online-confirmed', compact('order'));
    }

    public function onlineCheckout(Request $request, Order $order)
    {
        $isPaid = $order->payment && $order->payment->status === 'paid';
        $isActiveOrCompleted = in_array($order->status, ['processing', 'delivering', 'completed']);

        // Jika pesanan sudah dibayar DAN sudah diproses, langsung ke halaman konfirmasi final.
        if ($isPaid && $isActiveOrCompleted) {
            return redirect()->route('customer.order.online-confirmed', $order);
        }

        // Jika masih 'initial' atau 'pending' (belum dibayar), LANJUTKAN untuk membuat Snap Token.

        // A. Konfigurasi Midtrans (Tidak diubah)
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');


        // B. Siapkan Detail Transaksi (Pastikan order_id Midtrans menggunakan ID saat ini)
        $transaction_details = [
            'order_id' => $order->id . '-' . time(),
            'gross_amount' => $order->total_price,
        ];

        // C. Detail Pelanggan
        $customer_details = [
            'first_name' => $order->customer_name ?? 'Pelanggan',
            'email' => 'customer-' . $order->id . '@example.com',
            'phone' => $order->phone_number ?? '08123456789',
        ];

        // D. Buat Parameter Snap
        $snap_params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
        ];

        try {
            // E. Buat Snap Token
            $snapToken = Snap::getSnapToken($snap_params);

            // F. Kirim Token ke View Checkout
            return view('customer.payment.online_checkout', compact('snapToken', 'order'));
        } catch (\Exception $e) {
            return redirect()->route('customer.menu.index')->with('error', 'Gagal membuat token pembayaran: ' . $e->getMessage());
        }
    }

    // Webhook Handler (Disertakan tapi tidak digunakan jika Anda tidak mengkonfigurasi Ngrok)
    public function handleMidtransNotification(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $notification = new Notification();
        $transaction = $notification->transaction_status;
        $orderIdMidtrans = $notification->order_id;

        $orderId = explode('-', $orderIdMidtrans)[0];
        $order = Order::with('payment')->find($orderId);
        $table = Table::find($order->table_id);

        if (!$order) {
            return response('Order not found', 404);
        }

        if ($transaction == 'capture' || $transaction == 'settlement') {
            if ($order->status !== 'completed' && $order->status !== 'processing') {
                $order->status = 'processing';
                $order->save();

                if ($order->payment) {
                    $order->payment->status = 'paid';
                    $order->payment->save();
                }
            }
        } elseif ($transaction == 'cancel' || $transaction == 'expire' || $transaction == 'deny') {
            if ($order->status !== 'canceled') {
                $order->status = 'canceled';
                $order->save();

                if ($table && $table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }
            }
        }

        return response('Notification handled successfully', 200);
    }

    public function cancelOnlineOrder(Request $request, Order $order)
    {
        // Izinkan pembatalan jika statusnya 'pending' ATAU 'initial'
        if ($order->status === 'pending' || $order->status === 'initial') {

            // 1. Batalkan Pesanan (di dalam DB::transaction untuk keamanan)
            DB::transaction(function () use ($order, $request) {
                $order->status = 'canceled';
                $order->save();

                // Bebaskan Meja
                $table = Table::find($order->table_id);
                if ($table && $table->status === 'occupied') {
                    $table->update(['status' => 'available']);
                }

                // Hapus session ID pesanan yang tertunda
                $request->session()->forget('online_pending_order_id');
            });

            // 2. Redirect ke Menu dengan SweetAlert Success
            return redirect()->route('customer.menu.index')->with('sweet_success', 'Pesanan Anda berhasil dibatalkan');
        } else {
            // Jika statusnya sudah processing/completed/canceled
            return redirect()->route('customer.menu.index')->with('error', 'Pesanan tidak dapat dibatalkan karena statusnya sudah ' . strtoupper($order->status));
        }
    }

    public function printStruk(Order $order)
    {
        // Pastikan relasi orderItems sudah dimuat
        $order->load('orderItems.menu');
        
        // Render view cetak
        return view('admin.orders.struk_print', compact('order'));
    }
}
