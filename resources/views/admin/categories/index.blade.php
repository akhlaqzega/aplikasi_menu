@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content')

    <div class="py-">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    {{-- Header diubah ke Purple-600 --}}
                    <h1 class="text-3xl font-bold text-purple-600 mb-6 border-b-2 border-purple-200 pb-2">
                        <i class="fas fa-tags mr-2"></i> Daftar Kategori Menu
                    </h1>
                    
                    {{-- Tombol Tambah Baru diubah ke Indigo-600 --}}
                    <div class="mb-6">
                        <a href="{{ route('admin.categories.create') }}" 
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg flex-initial inline-flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Kategori Baru</span>
                        </a>
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Tabel Daftar Kategori --}}
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Kategori Saat Ini ({{ $categories->count() }})</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-2xl">
                        <table class="min-w-full bg-white">
                            {{-- Thead diubah ke Purple-100 --}}
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-3/12">Nama Kategori</th>
                                    <th class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-5/12">Deskripsi</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-purple-800 uppercase tracking-wider w-2/12">Jumlah Menu</th>
                                    <th class="py-3 px-6 text-center text-xs font-medium text-purple-800 uppercase tracking-wider w-2/12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($categories as $item)
                                    {{-- Hover diubah ke Purple-50 --}}
                                    <tr class="hover:bg-purple-50 transition-colors">
                                        <td class="py-4 px-6 font-bold text-purple-700">{{ $item->name }}</td>
                                        <td class="py-4 px-6 text-gray-600 text-sm">{{ $item->description ?? '— Tidak ada deskripsi —' }}</td>
                                        <td class="py-4 px-6 text-center text-lg font-extrabold text-indigo-600">
                                            <span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full text-xs font-semibold">
                                                {{ $item->menus_count }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap space-x-3">
                                            {{-- Link Edit diubah ke Indigo-600 --}}
                                            <a href="{{ route('admin.categories.edit', $item->id) }}" 
                                                class="text-indigo-600 hover:text-indigo-800 font-semibold flex-initial inline-flex items-center space-x-1">
                                                <i class="fas fa-edit text-sm"></i> <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $item->name }}? Semua menu di dalamnya akan menjadi tanpa kategori.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold flex-initial inline-flex items-center space-x-1">
                                                    <i class="fas fa-trash-alt text-sm"></i> <span>Hapus</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-500 bg-gray-50">
                                            Tidak ada kategori menu yang dicatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection