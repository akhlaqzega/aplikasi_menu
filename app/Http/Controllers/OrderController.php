<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Jobs\CancelOrderJob;

class OrderController extends Controller
{
    /**
     * Menambahkan item ke keranjang (session).
     */
    public function addToCart(Request $request)
    {
        $menu = Menu::find($request->input('menu_id'));
        if (!$menu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan.');
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
        return redirect()->back()->with('success', 'Menu berhasil ditambahkan ke keranjang!');
    }

    /**
     * Menampilkan halaman keranjang.
     */
    public function cart()
    {
        $cart = Session::get('cart', []);
        return view('customer.cart', compact('cart'));
    }

    /**
     * Memperbarui jumlah item di keranjang melalui AJAX.
     */
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

        // Menghitung ulang total harga
        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        // Mengembalikan respons JSON untuk JavaScript
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total_price' => $total_price,
        ]);
    }

    /**
     * Menghapus item dari keranjang.
     */
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

    /**
     * Menampilkan halaman checkout.
     */
    public function checkout()
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong, tidak bisa melanjutkan ke checkout.');
        }

        // Ambil semua meja yang tersedia atau semua meja (tergantung kebutuhan)
        $tables = Table::all();
        return view('customer.checkout', compact('tables'));
    }

    /**
     * Menyimpan pesanan ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_id' => 'required|exists:tables,id',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string|in:cash,online',
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu.index')->with('error', 'Keranjang Anda kosong.');
        }

        $total_price = 0;
        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        $order = Order::create([
            'customer_name' => $request->input('customer_name'),
            'table_id' => $request->input('table_id'),
            'order_type' => 'dine_in',
            'total_price' => $total_price,
            'status' => 'pending',
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

        // Simpan ID pesanan di session untuk navigasi pelanggan
        Session::put('pending_order_id', $order->id);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total_price,
            'method' => $request->input('payment_method'),
            'status' => 'unpaid'
        ]);

        if ($request->input('payment_method') === 'cash') {
            // Dispatch job pembatalan dengan penundaan 5 menit
            CancelOrderJob::dispatch($order)->delay(now()->addSeconds(15));
            return redirect()->route('customer.payment.cash', ['order' => $order->id]);
        } else {
            // Untuk pembayaran online, kosongkan keranjang dan id pending
            Session::forget('cart');
            Session::forget('pending_order_id');
            return redirect()->route('customer.menu.index')->with('success', 'Silakan lanjutkan pembayaran online Anda.');
        }
    }

    /**
     * Menampilkan halaman instruksi pembayaran tunai.
     */
   public function cashInstructions(Order $order)
    {
        $payment = $order->payment;
        // Periksa jika pesanan sudah selesai
        if ($order->status === 'completed') {
            // Hapus session dan tampilkan halaman konfirmasi
            Session::forget('pending_order_id');
            return view('customer.order-confirmed', compact('order'));
        }
        
        // Periksa jika pesanan dibatalkan atau tidak valid
        if ($order->status !== 'pending' || !$payment || $payment->method !== 'cash') {
            return redirect()->route('customer.menu.index')->with('error', 'Pesanan tidak valid atau sudah diproses.');
        }

        return view('customer.payment-cash-instructions', compact('order'));
    }
}
