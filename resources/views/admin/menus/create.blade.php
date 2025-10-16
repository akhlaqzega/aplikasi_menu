@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')

@section('content')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @php
                        // Tentukan mode berdasarkan keberadaan $menu
                        $isEdit = isset($menu);
                        $title = $isEdit ? 'Edit Menu: ' . $menu->name : 'Tambah Menu Baru';
                        $route = $isEdit ? route('admin.menus.update', $menu->id) : route('admin.menus.store');
                        $method = $isEdit ? 'PUT' : 'POST';
                    @endphp

                    <h1 class="text-3xl font-bold text-blue-800 mb-6">{{ $title }}</h1>
                    
                    {{-- Form Tambah/Edit Menu --}}
                    <div class="p-6 bg-gray-50 rounded-lg shadow-inner border-l-4 border-blue-500">
                        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method($method)
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                
                                {{-- Field Kategori --}}
                                <div class="mb-4">
                                    <label for="category_id" class="block text-gray-700 font-semibold mb-2">Kategori</label>
                                    <select name="category_id" id="category_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Menu</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $menu->name ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div class="mb-4">
                                    <label for="price" class="block text-gray-700 font-semibold mb-2">Harga</label>
                                    <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $menu->price ?? '') }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $menu->description ?? '') }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label for="image" class="block text-gray-700 font-semibold mb-2">Gambar</label>
                                @if($isEdit && $menu->image)
                                    <p class="text-sm text-gray-500 mb-2">Gambar saat ini: 
                                        <img 
                                            src="{{ strpos($menu->image, '/') !== false ? asset($menu->image) : asset('storage/' . $menu->image) }}" 
                                            class="w-16 h-16 object-cover rounded-md inline-block"
                                            alt="Current Image"
                                        >
                                    </p>
                                @endif
                                <input type="file" name="image" id="image" class="w-full border p-2 rounded-lg bg-white">
                            </div>
                            
                            <div class="flex space-x-3 mt-4">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition shadow-md">
                                    {{ $isEdit ? 'Perbarui Menu' : 'Simpan Menu' }}
                                </button>
                                <a href="{{ route('admin.menus.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-500 transition">Batal</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection