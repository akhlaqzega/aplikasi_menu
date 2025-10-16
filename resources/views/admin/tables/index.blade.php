@extends('layouts.admin')

@section('title', 'Kelola Data Meja')

@section('content')
    <div class="space-y-8">
        <h1 class="text-3xl font-bold text-gray-800 border-b pb-4">Kelola Data Meja</h1>

        {{-- Pesan Notifikasi Sukses/Error --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <p class="font-bold">Kesalahan Validasi:</p>
                <ul>
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

        <div class="p-6 bg-white rounded-xl shadow-lg border-l-4 border-blue-500">
            <h2 class="text-xl font-bold text-blue-700 mb-4">{{ $title }}</h2>
            <form action="{{ $route }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                @method($method)
                
                {{-- Field 1: Nama Meja --}}
                <div class="md:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kode Meja (Contoh: M06)</label>
                    <input type="text" name="name" placeholder="Contoh: M01" value="{{ old('name', $table->name ?? '') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                </div>

                {{-- Field 2: Status (Hanya saat Edit) --}}
                @if($isEdit)
                    <div class="md:col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status Meja</label>
                        <select name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
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
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition mr-2">
                        {{ $isEdit ? 'Perbarui Meja' : 'Simpan Meja Baru' }}
                    </button>
                    @if($isEdit)
                        <a href="{{ route('admin.tables.index') }}" class="bg-gray-400 text-white py-2 px-4 rounded-md hover:bg-gray-500 transition">Batal Edit</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Meja --}}
        <div class="bg-white p-6 rounded-xl shadow-lg mt-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Meja ({{ $tables->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Meja</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Pada</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tables as $item)
                            <tr class="@if(isset($table) && $table->id === $item->id) bg-yellow-50 @endif">
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-blue-600">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = match($item->status) {
                                            'available' => 'bg-green-100 text-green-800',
                                            'occupied' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                {{-- PERBAIKAN: Menggunakan Null-safe Operator (?->) untuk mencegah error pada created_at yang null --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $item->created_at?->format('d M Y') ?? '-' }}
                                </td>
                                {{-- END PERBAIKAN --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.tables.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('admin.tables.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus meja {{ $item->name }}? Pastikan meja tidak digunakan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada meja yang dicatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection