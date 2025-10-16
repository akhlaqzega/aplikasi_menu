@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold text-blue-800 mb-6">Daftar Kategori Menu</h1>
                    
                    {{-- Tombol Tambah Baru --}}
                    <div class="mb-6">
                        <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                            + Tambah Kategori Baru
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Tabel Daftar Kategori --}}
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Kategori Saat Ini ({{ $categories->count() }})</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Menu</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($categories as $item)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="py-4 px-6 font-semibold text-gray-800">{{ $item->name }}</td>
                                        <td class="py-4 px-6 text-gray-600">{{ $item->description ?? '-' }}</td>
                                        <td class="py-4 px-6 text-center text-gray-600">{{ $item->menus_count }}</td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap space-x-2">
                                            <a href="{{ route('admin.categories.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                            <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item->name }}? Semua menu di dalamnya akan menjadi tanpa kategori.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
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