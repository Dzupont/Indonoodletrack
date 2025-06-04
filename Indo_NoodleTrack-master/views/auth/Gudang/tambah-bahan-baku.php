@extends('layouts.app-layout')

@section('header', 'Tambah Bahan Baku')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-primary mb-6">Tambah Bahan Baku Baru</h2>
    
    <form action="{{ route('gudang.bahan-baku.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Bahan Baku -->
            <div>
                <label for="nama_bahanbaku" class="block text-sm font-medium text-gray-700 mb-1">Nama Bahan Baku <span class="text-red-500">*</span></label>
                <input type="text" name="nama_bahanbaku" id="nama_bahanbaku" value="{{ old('nama_bahanbaku') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            
            <!-- Jenis Bahan Baku -->
            <div>
                <label for="jenis_bahanbaku" class="block text-sm font-medium text-gray-700 mb-1">Jenis Bahan Baku <span class="text-red-500">*</span></label>
                <select name="jenis_bahanbaku" id="jenis_bahanbaku" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Pilih Jenis</option>
                    @foreach($jenisOptions as $jenis)
                    <option value="{{ $jenis }}" {{ old('jenis_bahanbaku') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Jumlah Stok Barang -->
            <div>
                <label for="stok_bahanbaku" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Stok Barang <span class="text-red-500">*</span></label>
                <input type="number" name="stok_bahanbaku" id="stok_bahanbaku" value="{{ old('stok_bahanbaku', 0) }}" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            
            <!-- Satuan (hidden) -->
            <div class="hidden">
                <input type="hidden" name="satuan" id="satuan" value="item" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            
            <!-- Harga -->
            <div>
                <label for="harga" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500">Rp</span>
                    </div>
                    <input type="number" name="harga" id="harga" value="{{ old('harga') }}" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="0">
                </div>
            </div>
            
            <!-- Tanggal Expired -->
            <div>
                <label for="tanggal_expired" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Expired</label>
                <input type="date" name="tanggal_expired" id="tanggal_expired" value="{{ old('tanggal_expired') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            
            <!-- Gambar -->
            <div class="md:col-span-2">
                <label for="gambar" class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                <input type="file" name="gambar" id="gambar" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Jika tidak diisi, gambar default akan digunakan sesuai kategori</p>
            </div>
            
            <!-- Deskripsi -->
            <div class="md:col-span-2">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('deskripsi') }}</textarea>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4 mt-8">
            <a href="{{ route('gudang.stok-bahan-baku') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
