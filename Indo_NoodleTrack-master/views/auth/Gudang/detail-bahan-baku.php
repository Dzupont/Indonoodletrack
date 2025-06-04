@extends('layouts.app-layout')

@section('header', 'Detail Bahan Baku')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-start mb-6">
        <h2 class="text-2xl font-bold text-primary">Detail Bahan Baku</h2>
        <div class="flex space-x-2">
            <a href="{{ route('gudang.bahan-baku.edit', $bahanBaku->id_bahanbaku) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            <form action="{{ route('gudang.bahan-baku.destroy', $bahanBaku->id_bahanbaku) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Gambar Bahan Baku -->
        <div class="bg-gray-50 p-4 rounded-lg flex justify-center items-center">
            <img src="{{ asset('item-images/' . $bahanBaku->gambar) }}" alt="{{ $bahanBaku->nama_bahanbaku }}" class="max-h-64 object-contain">
        </div>
        
        <!-- Informasi Bahan Baku -->
        <div class="md:col-span-2 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Nama Bahan Baku</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->nama_bahanbaku }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Kode</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->kode }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Jenis</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->jenis_bahanbaku }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Stok Saat Ini</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->stok_bahanbaku }} {{ $bahanBaku->satuan }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Harga</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->harga ? 'Rp ' . number_format($bahanBaku->harga, 0, ',', '.') : '-' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Tanggal Expired</h3>
                    <p class="text-lg font-semibold">{{ $bahanBaku->tanggal_expired ? $bahanBaku->tanggal_expired->format('d F Y') : '-' }}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Deskripsi</h3>
                    <p class="text-base">{{ $bahanBaku->deskripsi ?? '-' }}</p>
                </div>
                
                @if($bahanBaku->atribut_tambahan)
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Atribut Tambahan</h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($bahanBaku->atribut_tambahan as $key => $value)
                        <div>
                            <span class="text-xs font-medium text-gray-500">{{ ucfirst($key) }}</span>
                            <p class="text-sm">{{ $value }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="mt-8 pt-6 border-t border-gray-200">
        <h3 class="text-lg font-semibold text-primary mb-4">Kelola Stok</h3>
        <div class="flex items-center space-x-4">
            <form action="{{ route('gudang.bahan-baku.decrement', $bahanBaku->id_bahanbaku) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <input type="number" name="amount" min="1" value="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                    Kurangi Stok
                </button>
            </form>
            
            <form action="{{ route('gudang.bahan-baku.increment', $bahanBaku->id_bahanbaku) }}" method="POST" class="flex items-center space-x-2">
                @csrf
                <input type="number" name="amount" min="1" value="1" class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <button type="submit" class="px-4 py-2 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Stok
                </button>
            </form>
        </div>
    </div>
    
    <div class="flex justify-start mt-8">
        <a href="{{ route('gudang.stok-bahan-baku') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
</div>
@endsection
