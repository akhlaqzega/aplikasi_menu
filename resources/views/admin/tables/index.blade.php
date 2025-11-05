@extends('layouts.admin')

@section('title', 'Kelola Data Meja')

@section('content')
    <div class="py-6">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-8">
                {{-- Header diubah ke Purple-600 --}}
                <h1 class="text-3xl font-bold text-purple-600 border-b-2 border-purple-200 pb-4">
                    <i class="fas fa-chair mr-2"></i> Kelola Data Meja
                </h1>

                {{-- Pesan Notifikasi Sukses/Error (Warna tetap) --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <p class="font-bold">Kesalahan Validasi:</p>
                        <ul class="list-disc list-inside ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Form Tambah/Edit Meja --}}
                @php
                    $isEdit = isset($table);
                    $route = $isEdit ? route('admin.tables.update', $table->id) : route('admin.tables.store');
                    $method = $isEdit ? 'PUT' : 'POST';
                    $title = $isEdit ? 'Edit Meja: ' . $table->name : 'Tambahkan Meja Baru';
                @endphp

                {{-- Card form dengan border diubah ke Purple-500 --}}
                <div class="p-6 bg-white rounded-xl shadow-lg border-l-4 border-purple-500">
                    <h2 class="text-xl font-bold text-indigo-700 mb-4">{{ $title }}</h2>
                    <form action="{{ $route }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        @method($method)
                        
                        {{-- Field 1: Nama Meja --}}
                        <div class="md:col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kode Meja (Contoh: M06)</label>
                            {{-- Focus ring disesuaikan ke ungu --}}
                            <input type="text" name="name" placeholder="Contoh: M01" value="{{ old('name', $table->name ?? '') }}" required 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        {{-- Field 2: Status (Hanya saat Edit) --}}
                        @if($isEdit)
                            <div class="md:col-span-1">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status Meja</label>
                                {{-- Focus ring disesuaikan ke ungu --}}
                                <select name="status" required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:border-purple-500 focus:ring-purple-500">
                                    @foreach(['available', 'occupied'] as $status)
                                        <option value="{{ $status }}" {{ old('status', $table->status ?? '') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else 
                            {{-- Hidden field untuk status default saat create --}}
                            <input type="hidden" name="status" value="available">
                        @endif
                        
                        <div class="md:col-span-2 flex justify-start">
                            {{-- Tombol Submit diubah ke Indigo-600 --}}
                            <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition shadow-md mr-2 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                {{ $isEdit ? 'Perbarui Meja' : 'Simpan Meja Baru' }}
                            </button>
                            @if($isEdit)
                                <a href="{{ route('admin.tables.index') }}" class="bg-gray-400 text-white py-2 px-4 rounded-md hover:bg-gray-500 transition shadow-md flex items-center">
                                    <i class="fas fa-times mr-2"></i> Batal Edit
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Tabel Daftar Meja --}}
                <div class="bg-white p-6 rounded-xl shadow-lg mt-6">
                    <h2 class="text-2xl font-bold text-purple-700 mb-4">Daftar Meja ({{ $tables->count() }})</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- Thead diubah ke Purple-100 --}}
                            <thead class="bg-purple-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Kode Meja</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-800 uppercase tracking-wider">Dibuat Pada</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-purple-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($tables as $item)
                                    {{-- Baris yang sedang diedit diberi highlight Purple-50 --}}
                                    <tr class="@if(isset($table) && $table->id === $item->id) bg-purple-50 @else hover:bg-gray-50 transition-colors @endif">
                                        {{-- Kode Meja diubah ke Purple-600 --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-purple-600">{{ $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = match($item->status) {
                                                    'available' => 'bg-green-100 text-green-800 border border-green-300',
                                                    'occupied' => 'bg-red-100 text-red-800 border border-red-300',
                                                    default => 'bg-gray-100 text-gray-800 border border-gray-300',
                                                };
                                            @endphp
                                            {{-- Status dijadikan badge dengan border --}}
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $statusClass }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ $item->created_at?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                                            {{-- Tombol Edit diubah ke Indigo-600 --}}
                                            <a href="{{ route('admin.tables.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex-initial inline-flex items-center space-x-1">
                                                <i class="fas fa-pen text-xs"></i> <span>Edit</span>
                                            </a>
                                            <form action="{{ route('admin.tables.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus meja {{ $item->name }}? Pastikan meja tidak digunakan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold flex-initial inline-flex items-center space-x-1">
                                                    <i class="fas fa-trash-alt text-xs"></i> <span>Hapus</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-8 text-gray-500 bg-gray-50">Belum ada meja yang dicatat. Gunakan form di atas untuk menambah.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection