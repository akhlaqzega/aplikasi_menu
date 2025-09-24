@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-6">Kelola Menu</h1>
                    
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                        <h2 class="text-xl font-bold mb-4">Tambah Menu Baru</h2>
                        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Menu</label>
                                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label for="price" class="block text-gray-700 font-semibold mb-2">Harga</label>
                                    <input type="number" name="price" id="price" step="0.01" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="block text-gray-700 font-semibold mb-2">Gambar</label>
                                <input type="file" name="image" id="image" class="w-full">
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition">
                                Simpan Menu
                            </button>
                        </form>
                    </div>

                    <h2 class="text-xl font-bold mb-4">Daftar Menu</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="py-3 px-6 text-left">Nama</th>
                                    <th class="py-3 px-6 text-left">Harga</th>
                                    <th class="py-3 px-6 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menus as $menu)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-4 px-6 font-semibold">{{ $menu->name }}</td>
                                        <td class="py-4 px-6">Rp{{ number_format($menu->price, 2, ',', '.') }}</td>
                                        <td class="py-4 px-6">
                                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                                            <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection