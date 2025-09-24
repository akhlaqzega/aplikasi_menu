<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Tampilkan daftar menu untuk pelanggan.
     */
    public function index()
    {
        // Ambil semua menu dari database
        $menus = Menu::all();
        // Tampilkan view 'customer.menu' dengan data menu
        return view('customer.menu', compact('menus'));
    }
}