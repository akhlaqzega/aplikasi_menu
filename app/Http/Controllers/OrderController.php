<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OrderController extends Controller
{
    const ORDER_TIMEOUT_SECONDS = 15;

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

        // LOGIKA BARU: Hitung TOTAL KUANTITAS (jumlah semua item)
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalQuantity += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan ke keranjang!',
            'cart_count' => $totalQuantity // Mengirim total kuantitas
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
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong, tidak bisa melanjutkan ke checkout.');
        }

        // REVISI: Mengambil data meja diurutkan berdasarkan nama (M01, M02, dsb.)
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
            'payment_method_detail' => 'required_if:payment_method_type,online|nullable|string',
        ], [
            'table_name.exists' => 'Kode meja tidak valid atau tidak terdaftar.'
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong.');
        }

        $table = Table::where('name', $request->input('table_name'))->first();

        if (!$table) {
            // Secara teknis, validasi di atas sudah mencakup ini, tapi ini adalah pengamanan ekstra
            return redirect()->back()->withInput()->with('error', 'Meja tidak ditemukan.');
        }

        if ($table->status === 'occupied') {
            return redirect()->back()->withInput()->with('table_error', 'Meja ' . $table->name . ' sedang terisi. Harap periksa kode meja Anda atau tunggu meja tersebut selesai digunakan.');
        }

        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        $isCash = $request->input('payment_method_type') === 'cash';

        $orderStatus = $isCash ? 'pending' : 'processing';
        $paymentMethod = $isCash ? 'cash' : $request->input('payment_method_detail');
        $paymentStatus = $isCash ? 'unpaid' : 'paid';

        $order = Order::create([
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

        Session::put('pending_order_id', $order->id);
        Session::forget('cart');

        if ($isCash) {
            return redirect()->route('customer.payment.cash', ['order' => $order->id]);
        } else {
            Session::forget('pending_order_id');
            return redirect()->route('customer.order.confirmed', ['order' => $order->id]);
        }
    }

    public function cashInstructions(Order $order)
    {
        $timeoutSeconds = self::ORDER_TIMEOUT_SECONDS;

        $expirationTime = $order->created_at->copy()->addSeconds($timeoutSeconds);

        if ($order->status === 'pending' && $expirationTime->isPast()) {
            $order->status = 'canceled';
            $order->save();
            \Illuminate\Support\Facades\Session::forget('pending_order_id');
        }

        if ($order->status === 'processing' || $order->status === 'delivering' || $order->status === 'completed') {
            \Illuminate\Support\Facades\Session::forget('pending_order_id');
            return view('customer.order-confirmed', compact('order'));
        }

        if ($order->status === 'pending' || $order->status === 'canceled') {
            $expirationTimestamp = $expirationTime->timestamp * 1000;
            return view('customer.payment-cash-instructions', compact('order', 'expirationTimestamp'));
        }

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
}
