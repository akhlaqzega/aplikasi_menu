@extends('layouts.customer')

@section('content')
    <div class="container mx-auto px-4 py-16"> {{-- Menambah padding atas/bawah --}}
        
        {{-- Card utama diubah ke bg-zinc-800 dengan border amber --}}
        <div class="max-w-xl mx-auto bg-zinc-800 p-8 rounded-xl shadow-2xl border border-amber-400/20">
            
            {{-- Judul dan Teks diubah ke warna tema --}}
            <h1 class="text-3xl font-extrabold text-amber-400 mb-6 text-center">Lacak Pesanan Anda 🔍</h1>
            <p class="text-zinc-400 mb-6 text-center">Masukkan nomor telepon yang Anda gunakan saat pemesanan.</p>

            {{-- Form Pencarian --}}
            <form action="{{ route('customer.track.order') }}" method="POST" class="space-y-4">
                @csrf
                {{-- Input field disesuaikan --}}
                <input type="tel" name="phone_number" placeholder="Contoh: 081234567890" required 
                    class="w-full px-4 py-3 border border-zinc-700 bg-zinc-900 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 @error('phone_number') border-red-500 @enderror">
                
                @error('phone_number')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror

                {{-- Tombol submit diubah ke amber --}}
                <button type="submit" class="w-full bg-amber-400 text-zinc-900 px-4 py-3 rounded-lg font-bold hover:bg-amber-300 transition shadow-lg shadow-amber-500/30">
                    Lacak Pesanan
                </button>
            </form>

            @if(isset($order))
                {{-- Hasil Pelacakan --}}
                <div class="mt-8 pt-6 border-t border-zinc-700">
                    <h2 class="text-xl font-bold mb-4 text-white">Status Pesanan Terakhir</h2>
                    
                    @if($order)
                        {{-- Logika warna untuk status --}}
                        @php
                            $status_color = 'amber';
                            $status_bg = 'amber-900/30';
                            $status_text = 'amber-400';

                            if($order->status == 'completed') {
                                $status_color = 'green';
                                $status_bg = 'green-900/30';
                                $status_text = 'green-400';
                            } elseif($order->status == 'canceled') {
                                $status_color = 'red';
                                $status_bg = 'red-900/30';
                                $status_text = 'red-400';
                            }
                        @endphp

                        {{-- Card status disesuaikan --}}
                        <div class="p-4 rounded-lg border-2 border-{{ $status_color }}-500 bg-{{ $status_bg }}">
                            
                            <p class="text-sm font-medium text-zinc-400">Nomor Pesanan: <span class="font-bold text-white">#{{ $order->order_number }}</span></p>
                            
                            <p class="text-lg font-bold mt-2 text-{{ $status_text }}">
                                {{ strtoupper($order->status) }}
                            </p>
                            
                            <p class="text-xs text-zinc-500 mt-1">Dipesan pada: {{ $order->created_at->format('d M H:i') }}</p>
                        </div>
                    @else
                        {{-- Pesan tidak ditemukan disesuaikan --}}
                        <div class="p-4 bg-zinc-900 rounded-lg text-center text-zinc-400 border border-zinc-700">
                            Nomor telepon tidak ditemukan atau belum ada pesanan aktif.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection