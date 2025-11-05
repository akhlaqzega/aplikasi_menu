@extends('layouts.admin')

@section('title', 'Manajemen Menu & Produk')

@section('content')

    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="flex justify-between items-center mb-6 border-b-2 border-purple-200 pb-3">
                        {{-- Header diubah ke Purple-600 --}}
                        <h1 class="text-3xl font-bold text-purple-600">Daftar Menu & Produk</h1>
                        {{-- Tombol diubah ke Indigo-600 --}}
                        <a href="{{ route('admin.menus.create') }}"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg flex items-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Menu Baru</span>
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Tabel Daftar Menu --}}
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Menu Saat Ini ({{ $menus->count() }})</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-2xl">
                        <table class="min-w-full bg-white">
                            {{-- Header tabel diubah ke Purple-50/100 --}}
                            <thead class="bg-purple-100">
                                <tr>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-1/12">
                                        Gambar</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-4/12">
                                        Nama Menu & Deskripsi</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-2/12">
                                        Kategori</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-purple-800 uppercase tracking-wider w-2/12">
                                        Harga</th>
                                    <th
                                        class="py-3 px-6 text-center text-xs font-medium text-purple-800 uppercase tracking-wider w-3/12">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($menus as $item)
                                    {{-- Hover diubah ke Purple-50 --}}
                                    <tr class="hover:bg-purple-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <img src="{{ str_contains($item->image, 'images/menus/') ? asset($item->image) : asset('storage/' . $item->image) }}"
                                                class="w-16 h-16 object-cover rounded-lg shadow-md border border-gray-100" alt="{{ $item->name }}">
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-gray-800">{{ $item->name }}</p>
                                            <p class="text-sm text-gray-500 line-clamp-2 mt-1">{{ $item->description }}</p>
                                        </td>
                                        {{-- Warna kategori disamakan dengan sidebar --}}
                                        <td class="py-4 px-6 text-sm text-purple-600 font-medium">
                                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded-full">
                                                {{ $item->category->name ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-bold text-green-600">
                                            Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap space-x-3">
                                            <a href="{{ route('admin.menus.edit', $item->id) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-semibold transition flex-initial inline-flex items-center space-x-1">
                                                <i class="fas fa-edit text-sm"></i> <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus menu {{ $item->name }}? Aksi ini tidak bisa dibatalkan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 font-semibold transition flex-initial inline-flex items-center space-x-1">
                                                    <i class="fas fa-trash-alt text-sm"></i> <span>Hapus</span>
                                                </button>
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