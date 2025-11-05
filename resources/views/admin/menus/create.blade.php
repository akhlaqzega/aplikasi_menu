@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')

@section('content')

    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    @php
                        // Logika untuk menentukan mode Edit atau Create
                        $isEdit = isset($menu);
                        $title = $isEdit ? 'Edit Menu: ' . ($menu->name ?? '...') : 'Tambah Menu Baru';
                        $route = $isEdit ? route('admin.menus.update', $menu->id) : route('admin.menus.store');
                        $method = $isEdit ? 'PUT' : 'POST';
                    @endphp

                    {{-- Header diubah ke Purple-600 --}}
                    <h1 class="text-3xl font-bold text-purple-600 mb-6 border-b-2 border-purple-100 pb-2">
                        <i class="fas fa-plus-circle mr-2"></i> {{ $title }}
                    </h1>
                    
                    {{-- Form Tambah/Edit Menu --}}
                    {{-- Border diubah ke Purple-500 dan padding diperbesar --}}
                    <div class="p-8 bg-gray-50 rounded-lg shadow-inner border-l-4 border-purple-500">
                        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method($method)
                            
                            {{-- Layout grid diubah untuk gap yang lebih besar --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                
                                {{-- Field Kategori --}}
                                <div class="col-span-1 mb-4">
                                    <label for="category_id" class="block text-gray-700 font-semibold mb-2">
                                        <i class="fas fa-tags text-purple-500 mr-1"></i> Kategori
                                    </label>
                                    {{-- Focus Ring diubah ke Purple-500 --}}
                                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $menu->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                                </div>
                                
                                <div class="col-span-1 mb-4">
                                    <label for="name" class="block text-gray-700 font-semibold mb-2">
                                        <i class="fas fa-feather-alt text-purple-500 mr-1"></i> Nama Menu
                                    </label>
                                    {{-- Focus Ring diubah ke Purple-500 --}}
                                    <input type="text" name="name" id="name" value="{{ old('name', $menu->name ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                </div>

                                <div class="col-span-1 mb-4">
                                    <label for="price" class="block text-gray-700 font-semibold mb-2">
                                        <i class="fas fa-dollar-sign text-purple-500 mr-1"></i> Harga (Rp)
                                    </label>
                                    {{-- Focus Ring diubah ke Purple-500 --}}
                                    <input type="number" name="price" id="price" step="1" value="{{ old('price', $menu->price ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                                </div>
                            </div>

                            {{-- Deskripsi menggunakan lebar penuh (col-span-full) --}}
                            <div class="mb-6 col-span-full">
                                <label for="description" class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-info-circle text-purple-500 mr-1"></i> Deskripsi
                                </label>
                                {{-- Focus Ring diubah ke Purple-500 --}}
                                <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Deskripsi singkat menu/produk...">{{ old('description', $menu->description ?? '') }}</textarea>
                            </div>
                            
                            {{-- Gambar --}}
                            <div class="mb-6">
                                <label for="image" class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-image text-purple-500 mr-1"></i> Gambar
                                </label>
                                {{-- Bagian ini hanya relevan di Edit, tapi tidak menimbulkan error di Create --}}
                                @if($isEdit && $menu->image)
                                    <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                                    <img 
                                        src="{{ strpos($menu->image, '/') !== false ? asset($menu->image) : asset('storage/' . $menu->image) }}" 
                                        class="w-20 h-20 object-cover rounded-lg inline-block border-2 border-purple-200 shadow-md mb-2"
                                        alt="Current Image"
                                    >
                                @endif
                                <input type="file" name="image" id="image" class="w-full border p-2 rounded-lg bg-white">
                                <p class="text-xs text-gray-500 mt-1">Pilih gambar untuk menu ini.</p>
                            </div>
                            
                            <div class="flex space-x-3 mt-8 pt-4 border-t border-gray-200">
                                {{-- Tombol Simpan/Perbarui diubah ke Indigo-600 --}}
                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-indigo-700 transition shadow-lg flex items-center">
                                    <i class="fas fa-save mr-2"></i> {{ $isEdit ? 'Perbarui Menu' : 'Simpan Menu' }}
                                </button>
                                <a href="{{ route('admin.menus.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-500 transition shadow-md flex items-center">
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