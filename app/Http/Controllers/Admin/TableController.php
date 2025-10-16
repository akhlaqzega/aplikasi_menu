<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Tampilkan daftar meja dan form input (index).
     */
    public function index()
    {
        $tables = Table::orderBy('name')->get(); // Ambil semua data meja
        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Simpan data meja baru (Create).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name', // Pastikan nama unik
        ], [
            'name.unique' => 'Nama meja ini sudah digunakan.'
        ]);

        // Status default akan otomatis menjadi 'available' (dari migration)
        Table::create([
            'name' => $request->name,
            'status' => 'available', 
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Meja baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit meja (Edit).
     */
    public function edit(Table $table)
    {
        $tables = Table::orderBy('name')->get();
        // Melewatkan data $table untuk mode edit, dan semua meja untuk tabel list
        return view('admin.tables.index', compact('tables', 'table')); 
    }

    /**
     * Perbarui data meja (Update).
     */
    public function update(Request $request, Table $table)
    {
        $request->validate([
            // Abaikan nama meja saat ini saat memeriksa keunikan
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'status' => 'required|string|in:available,occupied', 
        ], [
            'name.unique' => 'Nama meja ini sudah digunakan.'
        ]);

        $table->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil diperbarui!');
    }

    /**
     * Hapus data meja (Delete).
     */
    public function destroy(Table $table)
    {
        // Pengecekan keamanan: cegah penghapusan jika meja sedang digunakan (optional)
        // if ($table->status === 'occupied') {
        //     return redirect()->back()->with('error', 'Meja sedang ditempati dan tidak bisa dihapus.');
        // }
        
        // Catatan: Pastikan di Migration, relasi table_id di tabel orders memiliki onDelete('set null') atau sejenisnya
        
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dihapus!');
    }
}