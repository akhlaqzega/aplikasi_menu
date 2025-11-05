<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Tampilkan daftar menu (List View).
     */
    public function index()
    {
        $menus = Menu::with('category')->get();
        return view('admin.menus.index', compact('menus')); // Mengarah ke index.blade.php
    }

    /**
     * Tampilkan form untuk membuat menu baru (Create View).
     */
    public function create()
    {
        $categories = Category::all(); // Ambil semua kategori untuk dropdown
        return view('admin.menus.create', compact('categories')); // Mengarah ke create.blade.php
    }

    /**
     * Simpan menu baru ke database.
     */
    public function store(Request $request)
    {
        // ... (Logika validasi dan store sama seperti sebelumnya) ...
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_images', 'public');
        }

        Menu::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Tampilkan form untuk mengedit menu (Edit View).
     */
    public function edit(Menu $menu)
    {
        $categories = Category::all(); // Ambil semua kategori
        return view('admin.menus.edit', compact('menu', 'categories')); // Mengarah ke edit.blade.php
    }

    /**
     * Perbarui menu di database.
     */
    public function update(Request $request, Menu $menu)
    {
        // ... (Logika validasi dan update sama seperti sebelumnya) ...
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menu_images', 'public');
        } else {
            $imagePath = $menu->image;
        }

        $menu->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => $request->input('category_id'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        // Hapus gambar dari storage
        if ($menu->image) {
            // Asumsi path gambar di storage/app/public/...
            \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->image);
        }

        // Hapus record menu
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus!');
    }
}
