@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Judul dan Tujuan Form --}}
                    <h1 class="text-3xl font-bold text-blue-800 mb-6">Edit Kategori: {{ $category->name }}</h1>
                    
                    <div class="p-6 bg-gray-50 rounded-lg shadow-inner border-l-4 border-blue-500">
                        
                        {{-- PERUBAHAN UTAMA DI SINI: Action dan Method --}}
                        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label for="name" class="block text-gray-700 font-semibold mb-2">Nama Kategori</label>
                                {{-- PENGISIAN DATA: Menggunakan $category->name --}}
                                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                @error('name')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="mb-6">
                                <label for="description" class="block text-gray-700 font-semibold mb-2">Deskripsi (Opsional)</label>
                                {{-- PENGISIAN DATA: Menggunakan $category->description --}}
                                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $category->description) }}</textarea>
                            </div>
                            
                            <div class="flex space-x-3 mt-4">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition shadow-md">
                                    Perbarui Kategori
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="bg-gray-400 text-white px-6 py-2 rounded-full font-semibold hover:bg-gray-500 transition">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection