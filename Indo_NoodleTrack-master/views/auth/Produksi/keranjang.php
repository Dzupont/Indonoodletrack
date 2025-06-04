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
<!-- Include request-handler.js -->
<script src="{{ asset('js/request-handler.js') }}"></script>
<!-- Include cart-page.js -->
<script src="{{ asset('js/cart-page.js') }}"></script>

<script>
// EXACT notification to match the image - with no black background
window.showNotification = function(message) {
    // First, remove any existing notifications
    document.querySelectorAll('.notification-popup').forEach(el => el.remove());
    
    // Remove ALL modal backdrops and overlays that might cause a black background
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.querySelectorAll('div[style*="background-color: rgba"]').forEach(el => el.remove());
    
    // Create the notification - EXACT match to the second image
    const toast = document.createElement('div');
    toast.className = 'notification-popup';
    toast.style.position = 'fixed';
    toast.style.top = '50%';
    toast.style.left = '50%';
    toast.style.transform = 'translate(-50%, -50%)';
    toast.style.backgroundColor = '#00BCD4'; // Exact teal color from the image
    toast.style.color = 'white';
    toast.style.padding = '20px';
    toast.style.borderRadius = '4px';
    toast.style.textAlign = 'center';
    toast.style.zIndex = '99999'; // Extremely high z-index
    toast.style.width = '260px';
    toast.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    
    // Add checkmark icon in circle - EXACT match to image
    toast.innerHTML = `
        <div style="display: flex; justify-content: center; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <div style="font-size: 16px; font-weight: 500;">Permintaan Berhasil Diajukan</div>
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 2 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 2000);
};

// Display cart items directly
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded');
    
    // Get cart contents
    const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
    console.log('Cart contents:', cart);
    
    // Display cart items
    const cartItemsList = document.getElementById('cartItemsList');
    if (cartItemsList) {
        cartItemsList.innerHTML = '';
        
        // Check if cart is empty
        if (cart.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="5" class="text-center py-8">
                    <div class="flex flex-col items-center">
                        <p class="text-gray-500">Keranjang Permintaan Kosong</p>
                    </div>
                </td>
            `;
            cartItemsList.appendChild(emptyRow);
            return;
        }
        
        // Display each item
        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-100';
            row.innerHTML = `
                <td class="py-3 px-4 text-sm">
                    <div class="flex items-center">
                        <span class="text-primary">${item.id || '-'}</span>
                    </div>
                </td>
                <td class="py-3 px-4 text-sm">${item.nama || 'Tidak ada nama'}</td>
                <td class="py-3 px-4 text-sm">${item.jumlah || 1} kg</td>
                <td class="py-3 px-4 text-sm">${item.stok || '0'} kg</td>
                <td class="py-3 px-4 text-sm">
                    <div class="flex space-x-2">
                        <button class="text-primary hover:text-primary-dark" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button class="remove-item text-red-500 hover:text-red-700" data-index="${index}" onclick="removeItem(${index})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            `;
            cartItemsList.appendChild(row);
        });
        
        // Show notes if any
        const cartNotes = document.getElementById('cartNotes');
        const notesContent = document.getElementById('notesContent');
        
        if (cartNotes && notesContent) {
            let hasNotes = false;
            let note = '';
            
            cart.forEach(item => {
                if (item.catatan && item.catatan.trim() !== '') {
                    hasNotes = true;
                    note = item.catatan;
                }
            });
            
            if (hasNotes) {
                cartNotes.classList.remove('hidden');
                notesContent.textContent = note;
            }
        }
    }
});

// Function to remove item from cart
function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
    if (index >= 0 && index < cart.length) {
        cart.splice(index, 1);
        localStorage.setItem('indoNoodleCart', JSON.stringify(cart));
        
        // Reload cart display
        const cartItemsList = document.getElementById('cartItemsList');
        if (cartItemsList) {
            cartItemsList.innerHTML = '';
            // Re-render cart
            document.dispatchEvent(new Event('DOMContentLoaded'));
            
            // Show notification
            showNotification('Item berhasil dihapus');
        }
    }
}
</script>
@endpush
@endsection
