@extends('layouts.admin')

@section('title', 'Tambah Kategori Baru')

@section('content')

    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    {{-- Tentukan Judul dan Route/Method --}}
                    @php
                        // Logika untuk halaman Create
                        $isEdit = false;
                        $title = 'Tambah Kategori Baru';
                        $route = route('admin.categories.store');
                        $method = 'POST';
                        // Inisialisasi variabel kosong agar tidak error jika digunakan pada input old()
                        $category = (object)['name' => '', 'description' => ''];
                    @endphp

                    {{-- Header diubah ke Purple-600 --}}
                    <h1 class="text-3xl font-bold text-purple-600 mb-6 border-b-2 border-purple-100 pb-2">
                        <i class="fas fa-plus-circle mr-2"></i> {{ $title }}
                    </h1>
                    
                    {{-- Card form dengan border diubah ke Purple-500 dan padding diperbesar --}}
                    <div class="p-8 bg-gray-50 rounded-lg shadow-inner border-l-4 border-purple-500">
                        <form action="{{ $route }}" method="POST">
                            @csrf
                            @method($method)
                            
                            <div class="mb-6">
                                <label for="name" class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-heading text-purple-500 mr-1"></i> Nama Kategori
                                </label>
                                {{-- Focus Ring diubah ke Purple-500 --}}
                                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                @error('name')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="mb-8">
                                <label for="description" class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-info-circle text-purple-500 mr-1"></i> Deskripsi (Opsional)
                                </label>
                                {{-- Focus Ring diubah ke Purple-500 --}}
                                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Jelaskan jenis menu yang termasuk dalam kategori ini...">{{ old('description', $category->description) }}</textarea>
                                @error('description')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                            </div>
                            
                            <div class="flex space-x-3 mt-4 pt-4 border-t border-gray-200">
                                {{-- Tombol Submit diubah ke Indigo-600 --}}
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-indigo-700 transition shadow-lg flex items-center">
                                    <i class="fas fa-save mr-2"></i> Simpan Kategori
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-500 transition shadow-md flex items-center">
                                    <i class="fas fa-times mr-2"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection