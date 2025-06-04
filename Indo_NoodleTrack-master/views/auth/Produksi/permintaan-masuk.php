@extends('layouts.app-layout')

@section('header', 'Permintaan Bahan Baku')

@section('content')
    <div x-data="{ activeTab: 'Bahan Baku Utama' }" class="pb-10">
    <!-- Category Tabs + Search + Cart (in one row) -->
    <div class="flex justify-between items-center px-2 py-3 gap-4">
        <!-- Tabs -->
        <div class="flex space-x-1.5 overflow-x-auto pb-1 scrollbar-hide">
            @foreach($categories as $category)
                <button @click="activeTab = '{{ $category }}'" :class="activeTab === '{{ $category }}' ? 'bg-primary text-white' : 'bg-white text-primary border border-primary'" class="px-4 py-2 text-sm rounded-full hover:bg-primary hover:text-white transition-colors duration-200 whitespace-nowrap flex-shrink-0">
                    {{ $category }}
                </button>
            @endforeach
        </div>
        <style>
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
        <!-- Search & Cart -->
        <div class="flex items-center space-x-2 mt-2 sm:mt-0">
            <a href="{{ route('keranjang') }}" class="bg-primary-light p-2 rounded-full text-white hover:bg-primary-dark transition relative">
                <img src="{{ asset('item-images/keranjang.svg') }}" alt="Keranjang Icon" class="h-5 w-5">
                <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" style="display: none;">0</span>
            </a>
            <div class="relative">
                <input type="text" placeholder="Cari bahan baku..." class="rounded-full border border-primary py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-primary text-sm min-w-[180px]" />
                <span class="absolute left-3 top-2.5 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4-4m0 0A7 7 0 104 4a7 7 0 0013 13z" />
                    </svg>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Category Tab Contents -->
    @foreach($categories as $category)
    <div x-show="activeTab === '{{ $category }}'" class="mt-4">
        <!-- Bahan Baku Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 px-2">
            @forelse($bahanBakuByCategory[$category] ?? [] as $item)
                <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <!-- Image Section -->
                    <div class="p-4 bg-white flex justify-center items-center h-[180px]">
                        <img src="{{ asset('item-images/' . basename($item['gambar'])) }}" alt="{{ $item['nama'] }}" class="h-32 object-contain" onerror="this.src='{{ asset('item-images/default-product.png') }}'; this.onerror=null;">
                    </div>
                    
                    <div class="bg-cyan-50 p-4 flex flex-col">
                        <!-- Product Name and Cart Button -->
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="font-bold text-lg text-primary truncate mr-2" style="max-width: 85%;">{{ $item['nama'] }}</h3>
                            <button class="text-primary hover:text-primary-dark add-to-cart" 
                                data-id="{{ $item['id'] }}" 
                                data-name="{{ $item['nama'] }}" 
                                data-image="{{ basename($item['gambar']) }}" 
                                data-stok="{{ $item['stok'] }}">
                                <img src="{{ asset('item-images/item.svg') }}" alt="Cart Icon" class="h-5 w-5 cursor-pointer">
                            </button>
                        </div>

                        <!-- Category -->
                        <p class="text-gray-600 text-xs mb-2">{{ $item['kategori'] }}</p>
                                
                        <!-- Specifications in horizontal layout to save space -->
                        <div class="grid grid-cols-3 gap-2 mt-auto">
                            <!-- Row 1: Kode -->
                            <div class="text-xs font-medium text-gray-700">
                                <p>{{ $item['kode'] }}</p>
                                <p>Kode</p>
                            </div>
                            
                            <!-- Row 2: Expired -->
                            <div class="text-xs font-medium text-gray-700">
                                <p class="truncate" title="{{ $item['expired'] }}">{{ $item['expired'] }}</p>
                                <p>Expired</p>
                            </div>
                            
                            <!-- Row 3: Kadar Protein -->
                            <div class="text-xs font-medium text-gray-700">
                                <p>{{ $item['protein'] }}</p>
                                <p>Kadar Protein</p>
                            </div>
                            
                            <!-- Row 4: Tekstur -->
                            <div class="text-xs font-medium text-gray-700">
                                <p>{{ $item['tekstur'] }}</p>
                                <p>Tekstur</p>
                            </div>
                            
                            <!-- Row 5: Stok -->
                            <div class="text-xs font-medium text-gray-700">
                                <p>{{ $item['stok'] }} {{ $item['satuan'] }}</p>
                                <p>Stok</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 text-center py-8">
                    <p class="text-gray-500">Belum ada data {{ $category }}</p>
                </div>
            @endforelse
        </div>
    </div>
    @endforeach
    </div>
    
    <!-- Only using the tambahKeKeranjangModal below -->
    
    <!-- Notification system is now working properly -->
    
    <!-- Cart Drawer -->
    <div id="cartDrawer" class="fixed top-0 right-0 w-full sm:w-96 h-full bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="h-full flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Keranjang Saya</h3>
                <button id="closeCartBtn" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Cart Items -->
            <div id="cartItems" class="flex-1 overflow-y-auto p-4">
                <!-- Items will be dynamically added here -->
                <div class="text-center text-gray-500 py-10">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="mt-2">Keranjang Anda masih kosong</p>
                </div>
            </div>
            
            <!-- Cart Footer -->
            <div class="border-t p-4 bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">Total</span>
                    <span id="cartTotal" class="font-semibold text-lg">Rp 0</span>
                </div>
                <button id="checkoutBtn" class="w-full bg-primary text-white py-2 rounded-md hover:bg-primary-dark transition-colors">
                    Checkout
                </button>
            </div>
        </div>
    </div>
    
    <!-- Cart Overlay -->
    <div id="cartOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
    
    <!-- Cart JavaScript - using inline scripts only to avoid conflicts -->
    <script>
        // Inisialisasi Alpine.js
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized');
        });
        
        // Fungsi untuk memperbarui jumlah item di ikon keranjang
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
            const totalItems = cart.reduce((total, item) => total + (parseInt(item.jumlah) || 0), 0);
            const cartCount = document.getElementById('cart-count');
            
            if (cartCount) {
                if (totalItems > 0) {
                    cartCount.textContent = totalItems;
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }
            }
        }
        
        // Make updateCartCount available globally
        window.updateCartCount = updateCartCount;
        
        // Inisialisasi jumlah item di ikon keranjang saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
        
        // Format currency helper function
        window.formatCurrency = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount || 0);
        };
        
        // Initialize cart functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM fully loaded');
            
            // Handle close modal button
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const modalBackdrop = document.getElementById('modalBackdrop');
            
            function closeModal() {
                const modal = document.getElementById('cartModal');
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
            
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeModal);
            }
            
            if (modalBackdrop) {
                modalBackdrop.addEventListener('click', closeModal);
            }
            
            // Handle quantity input changes
            const quantityInput = document.getElementById('quantityInput');
            const incrementBtn = document.getElementById('incrementBtn');
            const decrementBtn = document.getElementById('decrementBtn');
            
            if (quantityInput && incrementBtn && decrementBtn) {
                // Handle increment button
                incrementBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let value = parseInt(quantityInput.value) || 0;
                    quantityInput.value = value + 1;
                });
                
                // Handle decrement button
                decrementBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let value = parseInt(quantityInput.value) || 1;
                    if (value > 1) {
                        quantityInput.value = value - 1;
                    }
                });
                
                // Handle manual input
                quantityInput.addEventListener('change', function() {
                    let value = parseInt(this.value) || 1;
                    if (value < 1) {
                        this.value = 1;
                    } else {
                        this.value = value;
                    }
                });
            }
            
            // Handle add to cart button in modal
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const modal = document.getElementById('cartModal');
                    const itemId = modal.getAttribute('data-item-id');
                    const itemName = document.getElementById('modalItemName').textContent;
                    const itemPrice = parseFloat(document.getElementById('modalItemPrice').textContent.replace(/[^0-9.-]+/g,''));
                    const itemImage = document.getElementById('modalItemImage').src.split('/').pop();
                    const quantity = parseInt(document.getElementById('quantityInput').value);
                    const note = document.getElementById('noteInput').value;
                    
                    // Add item to cart
                    addToCart({
                        id: itemId,
                        nama: itemName,
                        harga: itemPrice,
                        jumlah: quantity,
                        gambar: itemImage,
                        catatan: note
                    });
                    
                    // Close modal
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                });
            }
            
            // Initialize cart count on page load
            if (window.updateCartCount) {
                window.updateCartCount();
            }
            
            // Handle quantity input changes
            const quantityInput = document.getElementById('quantityInput');
            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    let value = parseInt(this.value) || 0;
                    const max = parseInt(this.getAttribute('max')) || 999;
                    const min = parseInt(this.getAttribute('min')) || 1;
                    
                    if (value > max) {
                        this.value = max;
                    } else if (value < min) {
                        this.value = min;
                    }
                });
            }
            
            // Initialize increment/decrement buttons
            document.addEventListener('click', function(e) {
                const incrementBtn = e.target.closest('.increment-quantity');
                const decrementBtn = e.target.closest('.decrement-quantity');
                const quantityInput = document.getElementById('quantityInput');
                
                if (!quantityInput) return;
                
                let quantity = parseInt(quantityInput.value) || 1;
                const max = parseInt(quantityInput.getAttribute('max')) || 999;
                
                if (incrementBtn) {
                    e.preventDefault();
                    if (quantity < max) {
                        quantityInput.value = quantity + 1;
                    }
                }
                
                if (decrementBtn) {
                    e.preventDefault();
                    if (quantity > 1) {
                        quantityInput.value = quantity - 1;
                    }
                }
            });
        });
    </script>

<!-- Cart Overlay -->
<div id="cartOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Cart JavaScript -->
<script src="{{ asset('js/cart.js') }}"></script>

<!-- Initialize Cart -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize cart functionality
        if (window.initializeCart) {
            window.initializeCart();
        }
        
        // Initialize quantity input
        const quantityInput = document.getElementById('quantityInput');
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                const max = parseInt(this.getAttribute('max')) || 999;
                const min = parseInt(this.getAttribute('min')) || 1;
                
                if (value > max) {
                    this.value = max;
                } else if (value < min) {
                    this.value = min;
                }
            });
        }
        
        // Initialize increment/decrement buttons
        document.addEventListener('click', function(e) {
            const incrementBtn = e.target.closest('.increment-quantity');
            const decrementBtn = e.target.closest('.decrement-quantity');
            const quantityInput = document.getElementById('quantityInput');
            
            if (!quantityInput) return;
            
            let quantity = parseInt(quantityInput.value) || 1;
            const max = parseInt(quantityInput.getAttribute('max')) || 999;
            
            if (incrementBtn) {
                e.preventDefault();
                if (quantity < max) {
                    quantityInput.value = quantity + 1;
                }
            }
            
            if (decrementBtn) {
                e.preventDefault();
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                }
            }
        });
    });
</script>

<!-- Initialize Event Listeners -->
<script>
    // Format mata uang
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount || 0);
    }
    
    // Fungsi untuk menambahkan item ke keranjang
    function addToCart(item) {
        // Dapatkan keranjang dari localStorage
        let cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
        
        // Cek apakah item sudah ada di keranjang
        const existingItemIndex = cart.findIndex(cartItem => cartItem.id === item.id);
        
        if (existingItemIndex >= 0) {
            // Jika item sudah ada, tambahkan jumlahnya
            cart[existingItemIndex].jumlah += item.jumlah;
        } else {
            // Jika item belum ada, tambahkan ke keranjang
            cart.push(item);
        }
        
        // Simpan kembali ke localStorage
        localStorage.setItem('indoNoodleCart', JSON.stringify(cart));
        
        // Perbarui jumlah di ikon keranjang
        updateCartCount();
        
        // Tampilkan notifikasi
        showNotification(`${item.jumlah} ${item.nama} ditambahkan ke keranjang`);
    }
    
    // Fungsi untuk memperbarui jumlah item di ikon keranjang
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
        const totalItems = cart.reduce((total, item) => total + (item.jumlah || 0), 0);
        const cartCount = document.getElementById('cart-count');
        
        if (cartCount) {
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.style.display = 'flex';
            } else {
                cartCount.style.display = 'none';
            }
        }
    }
    
    // Update total harga di modal
    function updateTotalPrice(modal, price, quantity) {
        const totalPrice = price * quantity;
        const totalElement = modal.querySelector('#totalPrice');
        if (totalElement) {
            totalElement.textContent = formatCurrency(totalPrice);
        }
    }
    
    // Tampilkan notifikasi
    function showNotification(message, type = 'success') {
        const notification = document.getElementById('successNotification');
        const messageEl = document.getElementById('notificationMessage');
        
        if (notification && messageEl) {
            // Update pesan notifikasi
            messageEl.textContent = message;
            
            // Tampilkan notifikasi
            notification.classList.remove('hidden');
            notification.classList.add('flex');
            
            // Sembunyikan notifikasi setelah 3 detik
            setTimeout(() => {
                notification.classList.remove('flex');
                notification.classList.add('hidden');
            }, 3000);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Handle add to cart button clicks
        // Event listener untuk tombol 'Tambah ke Keranjang' di modal
        document.body.addEventListener('click', function(e) {
            const addToCartBtn = e.target.closest('.add-to-cart');
            
            if (addToCartBtn) {
                e.preventDefault();
                
                // Dapatkan data dari atribut data
                const item = {
                    id: addToCartBtn.dataset.id,
                    nama: addToCartBtn.dataset.name,
                    gambar: addToCartBtn.dataset.image,
                    harga: parseInt(addToCartBtn.dataset.price),
                    stok: parseInt(addToCartBtn.dataset.stock),
                    satuan: addToCartBtn.dataset.unit,
                    kategori: addToCartBtn.dataset.category || 'Bahan Baku',
                    jumlah: 1 // Default jumlah 1
                };
                
                // Tampilkan modal untuk memilih jumlah
                const modal = document.getElementById('addToCartModal');
                const modalInstance = new bootstrap.Modal(modal);
                
                // Set data item ke modal
                modal.querySelector('#itemName').textContent = item.nama;
                modal.querySelector('#itemStock').textContent = `Stok: ${item.stok} ${item.satuan}`;
                modal.querySelector('#itemPrice').textContent = formatCurrency(item.harga);
                
                // Reset quantity
                const quantityInput = modal.querySelector('#quantityInput');
                quantityInput.value = 1;
                
                // Update total harga
                updateTotalPrice(modal, item.harga, 1);
                
                // Handle perubahan quantity
                modal.querySelector('.increment-quantity').onclick = () => {
                    const newValue = parseInt(quantityInput.value) + 1;
                    if (newValue <= item.stok) {
                        quantityInput.value = newValue;
                        updateTotalPrice(modal, item.harga, newValue);
                    }
                };
                
                modal.querySelector('.decrement-quantity').onclick = () => {
                    const newValue = Math.max(1, parseInt(quantityInput.value) - 1);
                    quantityInput.value = newValue;
                    updateTotalPrice(modal, item.harga, newValue);
                };
                
                // Handle input manual
                quantityInput.oninput = () => {
                    let value = parseInt(quantityInput.value) || 1;
                    if (value > item.stok) {
                        value = item.stok;
                        quantityInput.value = value;
                    } else if (value < 1) {
                        value = 1;
                        quantityInput.value = value;
                    }
                    updateTotalPrice(modal, item.harga, value);
                };
                
                // Handle tombol tambah ke keranjang
                const addToCartBtn = modal.querySelector('#confirmAddToCart');
                addToCartBtn.onclick = () => {
                    const quantity = parseInt(quantityInput.value);
                    if (quantity > 0 && quantity <= item.stok) {
                        // Update jumlah item
                        item.jumlah = quantity;
                        
                        // Tambahkan ke keranjang
                        addToCart(item);
                        
                        // Tutup modal
                        modalInstance.hide();
                    }
                };
                
                // Tampilkan modal
                modalInstance.show();
            }
            if (addToCartBtn) {
                e.preventDefault();
                const modal = document.getElementById('cartModal');
                if (modal) {
                    // Set item data to modal
                    const itemId = addToCartBtn.getAttribute('data-id');
                    const itemName = addToCartBtn.getAttribute('data-name');
                    const itemPrice = addToCartBtn.getAttribute('data-price');
                    const itemImage = addToCartBtn.getAttribute('data-image') || 'default-product.png';
                    const itemUnit = addToCartBtn.getAttribute('data-unit') || 'pcs';
                    
                    // Set data ke modal
                    modal.setAttribute('data-item-id', itemId);
                    document.getElementById('modalItemName').textContent = itemName;
                    document.getElementById('modalItemPrice').textContent = formatCurrency(parseFloat(itemPrice));
                    document.getElementById('modalItemImage').src = `{{ asset('item-images/') }}/${itemImage}`;
                    document.getElementById('modalItemStock').textContent = `Satuan: ${itemUnit}`;
                    document.getElementById('quantityInput').value = 1;
                    document.getElementById('noteInput').value = '';
                    
                    // Tampilkan modal
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
                
                return;
            }
            
            // Handle tombol keranjang
            if (e.target.closest('#cartButton')) {
                e.preventDefault();
                console.log('Cart button clicked');
                // Toggle cart drawer
                const cartDrawer = document.getElementById('cartDrawer');
                const overlay = document.getElementById('cartOverlay');
                
                if (cartDrawer && overlay) {
                    cartDrawer.classList.toggle('translate-x-full');
                    overlay.classList.toggle('hidden');
                    document.body.classList.toggle('overflow-hidden');
                }
                return;
            }
        });
        
        // Handle checkout button
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Tambahkan logika checkout di sini
                showNotification('Fitur checkout sedang dalam pengembangan', 'info');
            });
        }
        
        // Initialize cart count on page load
        if (window.updateCartCount) {
            window.updateCartCount();
        }
    });
    
    // Helper function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        } shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Trigger reflow
        void notification.offsetWidth;
        
        // Slide in
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            
            // Remove from DOM after animation
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Make showNotification available globally
    window.showNotification = showNotification;
    
    // Format currency helper function (available globally)
    window.formatCurrency = (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount || 0);
    };
</script>

<!-- Cart Drawer -->
<div id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50">
    <div class="flex flex-col h-full">
        <!-- Cart Header -->
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold">Keranjang Belanja</h3>
            <button class="close-cart text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Cart Items -->
        <div id="cartItems" class="flex-1 overflow-y-auto p-4">
            <!-- Items will be dynamically added here by cart.js -->
            <div class="text-center text-gray-500 py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="mt-2">Keranjang Anda masih kosong</p>
            </div>
        </div>
        
        <!-- Cart Footer -->
        <div class="border-t p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Total</span>
                <span id="cartTotal" class="font-semibold text-lg">Rp 0</span>
            </div>
            <button id="checkoutBtn" class="w-full bg-primary text-white py-2 rounded-md hover:bg-primary-dark transition-colors">
                Checkout
            </button>
        </div>
    </div>
</div>

<!-- Cart Overlay -->
<div id="cartOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Cart Modal for quantity selection -->
<form id="cartForm">
    <input type="hidden" id="itemId" name="itemId" value="">
    <div id="tambahKeKeranjangModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Modal backdrop -->
            <div class="fixed inset-0 bg-black opacity-50 modal-overlay" id="modalBackdrop"></div>
            
            <!-- Modal content -->
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md relative z-10">
                <!-- Modal header -->
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Tambah Tepung Terigu ke Keranjang</h3>
                    <button type="button" id="closeModalBtn" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Modal body -->
                <div class="px-6 py-4">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-100 rounded-md overflow-hidden">
                            <img id="modalItemImage" src="" alt="" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h4 id="modalItemName" class="font-medium text-gray-900"></h4>
                            <p id="modalItemStock" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="flex items-center">
                            <button type="button" id="decrementBtn" class="bg-gray-100 text-gray-700 hover:bg-gray-200 h-10 w-10 rounded-l-md border border-gray-300 flex items-center justify-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number" id="quantityInput" name="quantity" min="1" value="1" class="h-10 w-16 border-t border-b border-gray-300 text-center focus:ring-primary focus:border-primary" />
                            <button type="button" id="incrementBtn" class="bg-gray-100 text-gray-700 hover:bg-gray-200 h-10 w-10 rounded-r-md border border-gray-300 flex items-center justify-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea id="noteInput" name="note" rows="2" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md border p-2" placeholder="Contoh: Pedas, Tanpa Bawang, dll."></textarea>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3 border-t">
                    <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="confirmAddToCartBtn" class="px-4 py-2 bg-primary border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-dark transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<!-- Including the simple notification script -->
<script src="{{ asset('js/simple-notification.js') }}"></script>
<script>
// Put this at the very top - we don't want any conflicts
const showNotificationTeal = function(message) {
    console.log('Showing notification:', message); // Debug log
    
    // First, remove any existing notifications
    document.querySelectorAll('.notification-popup-teal').forEach(el => el.remove());
    
    // Create the notification with the teal design
    const toast = document.createElement('div');
    toast.className = 'notification-popup-teal';
    toast.style.position = 'fixed';
    toast.style.top = '50%';
    toast.style.left = '50%';
    toast.style.transform = 'translate(-50%, -50%)';
    toast.style.backgroundColor = '#00BCD4';
    toast.style.color = 'white';
    toast.style.padding = '20px';
    toast.style.borderRadius = '4px';
    toast.style.textAlign = 'center';
    toast.style.zIndex = '999999'; // Super high z-index
    toast.style.width = '260px';
    toast.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    
    // Add checkmark icon in circle
    toast.innerHTML = `
        <div style="display: flex; justify-content: center; margin-bottom: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        <div style="font-size: 16px; font-weight: 500;">${message || 'Produk Telah Ditambahkan Ke Keranjang'}</div>
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

// Make it available globally
window.showNotification = showNotificationTeal;

// Add event listener for test notification button
document.addEventListener('DOMContentLoaded', function() {
    // Notification system is now working with the dedicated script
    
    // Regular initialization
    // Initialize cart count display
    updateCartCount();
    
    // Close cart drawer when clicking overlay or close button
    const cartOverlay = document.getElementById('cartOverlay');
    const cartDrawer = document.getElementById('cartDrawer');
    const closeCartBtn = document.querySelector('.close-cart');
    
    if (cartOverlay && cartDrawer && closeCartBtn) {
        cartOverlay.addEventListener('click', function() {
            cartDrawer.classList.add('translate-x-full');
            cartOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
        
        closeCartBtn.addEventListener('click', function() {
            cartDrawer.classList.add('translate-x-full');
            cartOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    }
    
    // Function to update cart count in the header
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
        const totalItems = cart.reduce((total, item) => total + (parseInt(item.jumlah) || 0), 0);
        const cartCount = document.getElementById('cart-count');
        
        if (cartCount) {
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.style.display = 'flex';
            } else {
                cartCount.style.display = 'none';
            }
        }
    }
    
    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('tambahKeKeranjangModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }
    
    // Handle add to cart button clicks
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get item data from button attributes
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');
            const itemImage = this.getAttribute('data-image') || 'default-product.png';
            const itemStok = this.getAttribute('data-stok') || '100';
            const itemCategory = this.closest('.bg-white').querySelector('.text-gray-600.text-xs').textContent;
            
            // Get default image based on category
            let defaultImage = 'default-product.png';
            if (itemCategory.includes('Bahan Baku Utama')) {
                defaultImage = 'terigu.png';
            } else if (itemCategory.includes('Bahan Tambahan')) {
                defaultImage = 'carboxymethyl-cellulose.png';
            } else if (itemCategory.includes('Bumbu & Perisa')) {
                defaultImage = 'msg.png';
            } else if (itemCategory.includes('Pelengkap Kemasan')) {
                defaultImage = 'dus.png';
            } else if (itemCategory.includes('Bahan Pelengkap Lain')) {
                defaultImage = 'cabai.png';
            }
            
            // Set up the modal with item data
            const modal = document.getElementById('tambahKeKeranjangModal');
            if (modal) {
                // Store item ID in hidden form field
                document.getElementById('itemId').value = itemId;
                
                // Update modal content
                document.getElementById('modalTitle').textContent = `Tambah ${itemName} ke Keranjang`;
                document.getElementById('modalItemName').textContent = itemName;
                document.getElementById('modalItemStock').textContent = `Stok: ${itemStok}`;
                
                // Set image with proper path and fallback
                const imgElement = document.getElementById('modalItemImage');
                imgElement.src = `{{ asset('item-images/') }}/${itemImage}`;
                imgElement.alt = itemName;
                imgElement.onerror = function() {
                    this.src = `{{ asset('item-images/') }}/${defaultImage}`;
                    this.onerror = null;
                };
                
                // Reset form fields
                document.getElementById('quantityInput').value = 1;
                document.getElementById('noteInput').value = '';
                
                // Show modal
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    // Handle modal close buttons
    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('cancelBtn').addEventListener('click', closeModal);
    document.getElementById('modalBackdrop').addEventListener('click', closeModal);
    
    // Handle quantity increment/decrement
    const quantityInput = document.getElementById('quantityInput');
    
    document.getElementById('incrementBtn').addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent event bubbling
        const currentValue = parseInt(quantityInput.value) || 1;
        quantityInput.value = currentValue + 1;
    });
    
    document.getElementById('decrementBtn').addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent event bubbling
        const currentValue = parseInt(quantityInput.value) || 2;
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });
    
    // Handle form submission for adding to cart
    document.getElementById('cartForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent actual form submission
        
        // Get form data
        const itemId = document.getElementById('itemId').value;
        const itemName = document.getElementById('modalItemName').textContent;
        const itemImage = document.getElementById('modalItemImage').getAttribute('src').split('/').pop();
        const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
        const note = document.getElementById('noteInput').value || '';
        
        // Get current cart or initialize empty array
        let cart = JSON.parse(localStorage.getItem('indoNoodleCart')) || [];
        
        // Check if item already exists in cart
        const existingItemIndex = cart.findIndex(cartItem => cartItem.id === itemId);
        
        if (existingItemIndex !== -1) {
            // Update quantity if item exists
            cart[existingItemIndex].jumlah = parseInt(cart[existingItemIndex].jumlah) + parseInt(quantity);
        } else {
            // Add new item to cart
            cart.push({
                id: itemId,
                nama: itemName,
                gambar: itemImage,
                jumlah: quantity,
                catatan: note
            });
        }
        
        // Save updated cart to localStorage
        localStorage.setItem('indoNoodleCart', JSON.stringify(cart));
        
        // Update cart count
        updateCartCount();
        
        // Close modal
        closeModal();
        
        // Show success message using our new simple notification function
        createTealNotification('Produk Telah Ditambahkan Ke Keranjang');
    });
});
</script>
@endpush

@endsection
