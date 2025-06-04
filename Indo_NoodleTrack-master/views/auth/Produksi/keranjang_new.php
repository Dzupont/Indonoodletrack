@extends('layouts.app-layout')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold text-primary">Keranjang Permintaan</h1>
</div>

<!-- Main content with clean design matching image 2 -->
<div>    
    <!-- Success notification (initially hidden) -->
    <div id="pageNotification" class="hidden mx-auto my-8 p-8 rounded-lg bg-primary text-white text-center max-w-2xl">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-full border-2 border-white flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>
        <p class="text-lg font-medium">Permintaan Berhasil Diajukan</p>
    </div>
        
    <!-- Cart Items Table -->
    <div id="cartContent">
        <table class="w-full border-collapse">
            <thead>
                <tr class="text-left border-b border-t border-gray-200">
                    <th class="py-2 px-4 bg-primary text-white font-medium">ID</th>
                    <th class="py-2 px-4 bg-primary text-white font-medium">Item</th>
                    <th class="py-2 px-4 bg-primary text-white font-medium">Kuantitas</th>
                    <th class="py-2 px-4 bg-primary text-white font-medium">Ketersediaan</th>
                    <th class="py-2 px-4 bg-primary text-white font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody id="cartItemsList">
                <!-- Cart items will be added here dynamically -->
            </tbody>
        </table>
    </div>
    
    <!-- Notes section (shown if there are notes) -->
    <div id="cartNotes" class="mt-4 text-sm text-gray-600 hidden">
        <p>Catatan: <span id="notesContent">Minta dalam packaging kering</span></p>
    </div>
    
    <!-- Submit button at the bottom right -->
    <div class="mt-8 flex justify-end">
        <button id="checkoutBtn" class="bg-gray-400 text-white px-6 py-2 rounded-md hover:bg-gray-500 transition-colors">
            Ajukan
        </button>
    </div>
</div>

@push('scripts')
<!-- Include cart-page.js -->
<script src="{{ asset('js/cart-page.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.renderCart) {
            window.renderCart();
        }
    });
</script>
@endpush
@endsection
