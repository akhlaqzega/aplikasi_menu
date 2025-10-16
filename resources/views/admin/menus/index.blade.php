@extends('layouts.admin')

@section('title', 'Manajemen Menu')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-blue-800">Daftar Menu</h1>
                        <a href="{{ route('admin.menus.create') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                            + Tambah Menu Baru
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Tabel Daftar Menu --}}
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Menu Saat Ini ({{ $menus->count() }})</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gambar</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Menu</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori</th>
                                    <th
                                        class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga</th>
                                    <th
                                        class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($menus as $item)
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="py-4 px-6">
                                            <img src="{{ str_contains($item->image, 'images/menus/') ? asset($item->image) : asset('storage/' . $item->image) }}"
                                                class="w-12 h-12 object-cover rounded-md" alt="{{ $item->name }}">
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-gray-800">{{ $item->name }}</td>
                                        <td class="py-4 px-6 text-sm text-indigo-600 font-medium">
                                            {{ $item->category->name ?? '—' }}</td>
                                        <td class="py-4 px-6 font-semibold">
                                            Rp{{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap space-x-2">
                                            <a href="{{ route('admin.menus.edit', $item->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                            <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus menu {{ $item->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
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
