@extends('layouts.app-layout')

@section('header', 'Stok Bahan Baku')

@section('content')
    <div x-data="{ activeTab: 'bahan-baku-utama' }" class="pb-10">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
    @endif
        <!-- Category Tabs + Add Button (in one row) -->
        <div class="flex justify-between items-center px-2 py-3 gap-4">
            <!-- Tabs -->
            <div class="flex space-x-2 overflow-x-auto">
                <button @click="activeTab = 'bahan-baku-utama'" :class="activeTab === 'bahan-baku-utama' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap">
                    Bahan Baku Utama
                </button>
                <button @click="activeTab = 'bahan-tambahan'" :class="activeTab === 'bahan-tambahan' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap">
                    Bahan Tambahan
                </button>
                <button @click="activeTab = 'bumbu-perisa'" :class="activeTab === 'bumbu-perisa' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap">
                    Bumbu & Perisa
                </button>
                <button @click="activeTab = 'pelengkap-kemasan'" :class="activeTab === 'pelengkap-kemasan' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap">
                    Pelengkap Kemasan
                </button>
                <button @click="activeTab = 'bahan-pelengkap-lain'" :class="activeTab === 'bahan-pelengkap-lain' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap">
                    Bahan Pelengkap Lain
                </button>
            </div>
            <!-- Add Button -->
            <a href="{{ route('gudang.tambah-bahan-baku') }}" class="bg-primary text-white px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Bahan Baku
            </a>
        </div>
        
        <!-- Bahan Baku Utama Tab Content -->
        <div x-show="activeTab === 'bahan-baku-utama'" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
                @forelse($bahanBakuUtama as $item)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                        <!-- Image Section -->
                        <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                            <img src="{{ asset('item-images/terigu.png') }}" alt="{{ $item->nama_bahanbaku }}" class="h-32 object-contain">
                        </div>
                        
                        <!-- Product Info Section with Teal Background -->
                        <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                            <!-- Product Name -->
                            <div class="mb-2">
                                <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                            </div>

                            <!-- Description -->
                            <div class="flex-1">
                                <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                            </div>
                            
                            <!-- Stock Management Controls -->
                            <div class="flex justify-between items-center mt-auto">
                                <!-- Decrease Button -->
                                <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                
                                <!-- Current Stock -->
                                <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                    <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}" data-stock="{{ $item->stok_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                                </div>
                                
                                <!-- Increase Button -->
                                <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Belum ada data Bahan Baku Utama</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Bahan Tambahan Tab Content -->
        <div x-show="activeTab === 'bahan-tambahan'" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
                @forelse($bahanTambahan as $item)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                        <!-- Image Section -->
                        <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                            <img src="{{ asset('item-images/carboxymethyl-cellulose.png') }}" alt="{{ $item->nama_bahanbaku }}" class="h-32 object-contain">
                        </div>
                        
                        <!-- Product Info Section with Teal Background -->
                        <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                            <!-- Product Name -->
                            <div class="mb-2">
                                <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                            </div>

                            <!-- Description -->
                            <div class="flex-1">
                                <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                            </div>
                            
                            <!-- Stock Management Controls -->
                            <div class="flex justify-between items-center mt-auto">
                                <!-- Decrease Button -->
                                <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                
                                <!-- Current Stock -->
                                <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                    <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}" data-stock="{{ $item->stok_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                                </div>
                                
                                <!-- Increase Button -->
                                <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Belum ada data Bahan Tambahan</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Bumbu & Perisa Tab Content -->
        <div x-show="activeTab === 'bumbu-perisa'" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
                @forelse($bumbuPerisa as $item)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                        <!-- Image Section -->
                        <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                            <img src="{{ asset('item-images/msg.png') }}" alt="{{ $item->nama_bahanbaku }}" class="h-32 object-contain">
                        </div>
                        
                        <!-- Product Info Section with Teal Background -->
                        <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                            <!-- Product Name -->
                            <div class="mb-2">
                                <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                            </div>

                            <!-- Description -->
                            <div class="flex-1">
                                <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                            </div>
                            
                            <!-- Stock Management Controls -->
                            <div class="flex justify-between items-center mt-auto">
                                <!-- Decrease Button -->
                                <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                
                                <!-- Current Stock -->
                                <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                    <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}" data-stock="{{ $item->stok_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                                </div>
                                
                                <!-- Increase Button -->
                                <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Belum ada data Bumbu & Perisa</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <div x-show="activeTab === 'pelengkap-kemasan'" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
                @forelse($pelengkapKemasan as $item)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                        <!-- Image Section -->
                        <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                            <img src="{{ asset('item-images/dus.png') }}" alt="{{ $item->nama_bahanbaku }}" class="h-32 object-contain">
                        </div>
                        
                        <!-- Product Info Section with Teal Background -->
                        <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                            <!-- Product Name -->
                            <div class="mb-2">
                                <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                            </div>

                            <!-- Description -->
                            <div class="flex-1">
                                <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                            </div>
                            
                            <!-- Stock Management Controls -->
                            <div class="flex justify-between items-center mt-auto">
                                <!-- Decrease Button -->
                                <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                
                                <!-- Current Stock -->
                                <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                    <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}" data-stock="{{ $item->stok_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                                </div>
                                
                                <!-- Increase Button -->
                                <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Belum ada data Pelengkap Kemasan</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <div x-show="activeTab === 'bahan-pelengkap-lain'" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
                @forelse($bahanPelengkapLain as $item)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                        <!-- Image Section -->
                        <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                            <img src="{{ asset('item-images/cabai.png') }}" alt="{{ $item->nama_bahanbaku }}" class="h-32 object-contain">
                        </div>
                    
                    <!-- Product Info Section with Teal Background -->
                    <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                        <!-- Product Name -->
                        <div class="mb-2">
                            <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                        </div>

                        <!-- Description -->
                        <div class="flex-1">
                            <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                        </div>
                        
                        <!-- Stock Management Controls -->
                        <div class="flex justify-between items-center pt-4">
                            <!-- Decrease Button -->
                            <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            
                            <!-- Current Stock -->
                            <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                            </div>
                            
                            <!-- Increase Button -->
                            <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Bahan Pelengkap Lain Item 2 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-gray-100 h-[380px] flex flex-col">
                    <!-- Image Section -->
                    <div class="p-6 bg-white flex justify-center items-center h-[160px]">
                        <img src="{{ asset('item-images/cabai.png') }}" alt="Bubuk Cabai" class="h-32 object-contain">
                    </div>
                        
                        <!-- Product Info Section with Teal Background -->
                        <div class="bg-cyan-50 p-4 flex-1 flex flex-col">
                            <!-- Product Name -->
                            <div class="mb-2">
                                <h3 class="font-bold text-xl text-primary truncate" title="{{ $item->nama_bahanbaku }}">{{ $item->nama_bahanbaku }}</h3>
                            </div>

                            <!-- Description -->
                            <div class="flex-1">
                                <p class="text-gray-600 text-sm overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 3; line-clamp: 3; max-height: 4.5em; -webkit-box-orient: vertical;" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                            </div>
                            
                            <!-- Stock Management Controls -->
                            <div class="flex justify-between items-center mt-auto">
                                <!-- Decrease Button -->
                                <button type="button" class="decrement-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                
                                <!-- Current Stock -->
                                <div class="bg-white px-4 py-1 rounded-full border border-gray-300 text-center min-w-[80px]">
                                    <span class="font-medium stock-value" id="stock-{{ $item->id_bahanbaku }}" data-stock="{{ $item->stok_bahanbaku }}">{{ $item->stok_bahanbaku }}</span>
                                </div>
                                
                                <!-- Increase Button -->
                                <button type="button" class="increment-stock bg-white p-1 rounded-full border border-gray-300 hover:bg-gray-100 transition" data-id="{{ $item->id_bahanbaku }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-8">
                        <p class="text-gray-500">Belum ada data Bahan Pelengkap Lain</p>
                    </div>
                @endforelse
            </div>
        </div>
    
    <!-- Modal increment/decrement stock -->
    <div x-data="{showModal: false, action: 'increment', itemId: null, currentStock: 0, amount: 1, notes: ''}" x-show="showModal" class="relative z-10" aria-labelledby="modal-title" x-ref="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="action === 'increment' ? 'bg-green-100' : 'bg-red-100'">
                                <svg class="h-6 w-6" :class="action === 'increment' ? 'text-green-600' : 'text-red-600'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="action === 'increment' ? 'M12 6v6m0 0v6m0-6h6m-6 0H6' : 'M18 12H6'" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title" x-text="action === 'increment' ? 'Tambah Stok' : 'Kurangi Stok'"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" x-text="action === 'increment' ? 'Masukkan jumlah stok yang ingin ditambahkan.' : 'Masukkan jumlah stok yang ingin dikurangi.'"></p>
                                    <div class="mt-4">
                                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah</label>
                                        <input type="number" name="amount" id="amount" x-model="amount" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                    </div>
                                    <div class="mt-4">
                                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                                        <textarea name="notes" id="notes" x-model="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" placeholder="Masukkan catatan (opsional)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm sm:ml-3 sm:w-auto" :class="action === 'increment' ? 'bg-primary text-white hover:bg-primary-dark' : 'bg-red-600 text-white hover:bg-red-700'" @click="processStockChange()">
                            Konfirmasi
                        </button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="showModal = false">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk menangani increment dan decrement stock -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Implementasi sederhana untuk langsung menambah/kurang stok tanpa modal
            // Tombol increment stock
            document.querySelectorAll('.increment-stock').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const stockElement = document.getElementById(`stock-${id}`);
                    if (!stockElement) {
                        console.error(`Element dengan id stock-${id} tidak ditemukan`);
                        return;
                    }
                    
                    let currentStock = parseInt(stockElement.textContent);
                    // Tambah stok langsung
                    currentStock += 1;
                    stockElement.textContent = currentStock;
                    
                    // Kirim perubahan ke server di background
                    updateStockOnServer(id, 'increment', 1);
                });
            });
            
            // Tombol decrement stock
            document.querySelectorAll('.decrement-stock').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const stockElement = document.getElementById(`stock-${id}`);
                    if (!stockElement) {
                        console.error(`Element dengan id stock-${id} tidak ditemukan`);
                        return;
                    }
                    
                    let currentStock = parseInt(stockElement.textContent);
                    // Pastikan stok tidak menjadi negatif
                    if (currentStock > 0) {
                        // Kurangi stok langsung
                        currentStock -= 1;
                        stockElement.textContent = currentStock;
                        
                        // Kirim perubahan ke server di background
                        updateStockOnServer(id, 'decrement', 1);
                    }
                });
            });
            
            // Fungsi untuk memperbarui stok di server
            function updateStockOnServer(id, action, amount) {
                // URL endpoint berdasarkan aksi
                const url = action === 'increment' 
                    ? `{{ url('/gudang/bahan-baku/increment') }}/${id}` 
                    : `{{ url('/gudang/bahan-baku/decrement') }}/${id}`;
                
                // Data untuk dikirim
                const data = {
                    amount: amount,
                    notes: `Perubahan stok via tombol ${action === 'increment' ? 'tambah' : 'kurang'}`,
                    _token: '{{ csrf_token() }}'
                };
                
                // Kirim request AJAX
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Terjadi kesalahan saat memproses permintaan');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Stok berhasil diperbarui:', data);
                    
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                });
            }
        });
        
        // Fungsi untuk modal (jika masih dibutuhkan)
        function processStockChange() {
            const modalElement = document.querySelector('[x-data*="showModal"]');
            if (!modalElement || !modalElement.__x) {
                console.error('Modal tidak ditemukan');
                return;
            }
            
            const modal = modalElement.__x.$data;
            
            const id = modal.itemId;
            const amount = parseInt(modal.amount);
            const action = modal.action;
            const stockElement = document.getElementById(`stock-${id}`);
            
            if (amount <= 0) {
                alert('Jumlah harus lebih dari 0');
                return;
            }
            
            // Update UI langsung untuk respons yang cepat
            let currentStock = parseInt(stockElement.textContent);
            if (action === 'increment') {
                currentStock += amount;
            } else {
                currentStock = Math.max(0, currentStock - amount);
            }
            stockElement.textContent = currentStock;
            
            // Tutup modal
            modal.showModal = false;
            
            // URL endpoint berdasarkan aksi
            const url = action === 'increment' 
                ? `{{ url('/gudang/bahan-baku/increment') }}/${id}` 
                : `{{ url('/gudang/bahan-baku/decrement') }}/${id}`;
            
            // Data untuk dikirim
            const data = {
                amount: amount,
                notes: modal.notes,
                _token: '{{ csrf_token() }}'
            };
            
            // Kirim request AJAX
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Terjadi kesalahan saat memproses permintaan');
                }
                return response.json();
            })
            .then(data => {
                console.log('Stok berhasil diperbarui:', data);
            })
            .catch(error => {
                console.error('Error:', error);
                // Jika terjadi error, kembalikan nilai stok ke nilai sebelumnya
                alert('Terjadi kesalahan saat memperbarui stok. Halaman akan dimuat ulang.');
                location.reload();
            });
        }
    </script>
@endsection
