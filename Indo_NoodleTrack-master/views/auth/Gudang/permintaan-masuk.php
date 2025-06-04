@extends('layouts.app-layout')

@section('header', 'Permintaan Masuk')

@section('content')
<div class="container mx-auto py-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Permintaan Masuk</h1>
        
        <!-- Search Bar -->
        <div class="relative">
            <input type="text" placeholder="Search..." class="bg-gray-100 rounded-lg py-2 px-4 pl-10 w-64 focus:outline-none focus:ring-2 focus:ring-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-6">
        <button class="px-4 py-2 text-sm rounded-full bg-primary text-white hover:bg-primary-dark transition-colors duration-200">
            Semua
        </button>
        <button class="px-4 py-2 text-sm rounded-full bg-white text-primary border border-primary hover:bg-primary hover:text-white transition-colors duration-200">
            Menunggu
        </button>
        <button class="px-4 py-2 text-sm rounded-full bg-white text-primary border border-primary hover:bg-primary hover:text-white transition-colors duration-200">
            Diproses
        </button>
        <button class="px-4 py-2 text-sm rounded-full bg-white text-primary border border-primary hover:bg-primary hover:text-white transition-colors duration-200">
            Selesai
        </button>
    </div>

    <!-- Request Items Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Bahan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="requestItemsContainer">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data permintaan...</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div>
            <p class="text-sm text-gray-500">Showing <span class="font-medium">1</span> to <span class="font-medium">2</span> of <span class="font-medium">2</span> entries</p>
        </div>
        <div class="flex">
            <button class="mx-1 px-2 py-1 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">1</button>
            <button class="mx-1 px-2 py-1 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">2</button>
            <button class="mx-1 px-2 py-1 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">3</button>
            <button class="mx-1 px-2 py-1 text-sm rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">Next</button>
        </div>
    </div>
    
    <!-- Action Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Tindakan</h3>
            <p id="confirmModalText" class="text-gray-600 mb-6">Apakah Anda yakin ingin menerima permintaan ini?</p>
            <div class="flex justify-end space-x-3">
                <button id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button id="confirmBtn" class="px-4 py-2 bg-primary border border-transparent rounded-md text-white hover:bg-primary-dark">
                    Ya, Terima
                </button>
            </div>
        </div>
    </div>
    
    <!-- Request Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detail Permintaan</h3>
                <button id="closeDetailsBtn" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">ID Permintaan</p>
                    <p id="requestId" class="font-medium">REQ-001</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tanggal</p>
                    <p id="requestDate" class="font-medium">15 Mei 2023</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p id="requestStatus" class="font-medium">Menunggu</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Divisi</p>
                    <p id="requestDivision" class="font-medium">Produksi</p>
                </div>
            </div>
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Item yang Diminta</h4>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="requestItemsTableBody" class="divide-y divide-gray-200">
                        <!-- Item rows will be populated dynamically -->
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button id="closeDetailsBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Tutup
                </button>
                <button id="detailsActionBtn" class="px-4 py-2 bg-primary border border-transparent rounded-md text-white hover:bg-primary-dark">
                    Terima Permintaan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Include request handler script -->
<script src="{{ asset('js/request-handler.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal Handling
        const confirmModal = document.getElementById('confirmModal');
        const detailsModal = document.getElementById('detailsModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const closeDetailsBtn = document.getElementById('closeDetailsBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        let currentRequestId = null;
        let currentActionType = null; // 'accept' or 'reject'
        
        // Load and display requests
        loadRequests();
        
        function loadRequests() {
            if (!window.requestHandler) {
                console.error('Request handler not found');
                return;
            }
            
            const requests = window.requestHandler.getRequests();
            const requestItemsContainer = document.getElementById('requestItemsContainer');
            
            if (!requestItemsContainer) {
                console.error('Request items container not found');
                return;
            }
            
            // Clear current items
            requestItemsContainer.innerHTML = '';
            
            if (requests.length === 0) {
                requestItemsContainer.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada permintaan saat ini</td>
                    </tr>
                `;
                return;
            }
            
            // Generate table rows for each request
            requests.forEach(request => {
                // For each request, get the first item to display
                const firstItem = request.items && request.items.length > 0 ? request.items[0] : null;
                if (!firstItem) return;
                
                // Create row for this item
                const row = document.createElement('tr');
                
                // Determine status class
                let statusClass = '';
                if (request.status === 'Menunggu') {
                    statusClass = 'bg-yellow-100 text-yellow-800';
                } else if (request.status === 'Diterima') {
                    statusClass = 'bg-green-100 text-green-800';
                } else if (request.status === 'Ditolak') {
                    statusClass = 'bg-red-100 text-red-800';
                } else if (request.status === 'Diproses') {
                    statusClass = 'bg-blue-100 text-blue-800';
                }
                
                // Create row with appropriate actions based on status
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${request.items.indexOf(firstItem) + 1}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${firstItem.kode}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${firstItem.nama}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${firstItem.jumlah} ${firstItem.satuan || 'KG'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${request.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        ${request.status === 'Menunggu' ? `
                            <button class="accept-btn bg-primary text-white py-1 px-3 rounded hover:bg-primary-dark transition-colors" data-id="${request.id}">Terima</button>
                            <button class="reject-btn bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 transition-colors" data-id="${request.id}">Tolak</button>
                        ` : ''}
                        <button class="delete-btn text-red-500 hover:text-red-700" data-id="${request.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button class="view-btn text-primary hover:text-primary-dark" data-id="${request.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;
                
                requestItemsContainer.appendChild(row);
            });
            
            // Add event listeners
            setupEventListeners();
        }
        
        function setupEventListeners() {
            // Accept button click
            document.querySelectorAll('.accept-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRequestId = this.getAttribute('data-id');
                    currentActionType = 'accept';
                    document.getElementById('confirmModalText').textContent = 'Apakah Anda yakin ingin menerima permintaan ini?';
                    document.getElementById('confirmBtn').textContent = 'Ya, Terima';
                    document.getElementById('confirmBtn').className = 'px-4 py-2 bg-primary border border-transparent rounded-md text-white hover:bg-primary-dark';
                    confirmModal.classList.remove('hidden');
                    confirmModal.classList.add('flex');
                });
            });
            
            // Reject button click
            document.querySelectorAll('.reject-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRequestId = this.getAttribute('data-id');
                    currentActionType = 'reject';
                    document.getElementById('confirmModalText').textContent = 'Apakah Anda yakin ingin menolak permintaan ini?';
                    document.getElementById('confirmBtn').textContent = 'Ya, Tolak';
                    document.getElementById('confirmBtn').className = 'px-4 py-2 bg-red-500 border border-transparent rounded-md text-white hover:bg-red-600';
                    confirmModal.classList.remove('hidden');
                    confirmModal.classList.add('flex');
                });
            });
            
            // Delete button click
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    currentRequestId = this.getAttribute('data-id');
                    currentActionType = 'delete';
                    document.getElementById('confirmModalText').textContent = 'Apakah Anda yakin ingin menghapus permintaan ini?';
                    document.getElementById('confirmBtn').textContent = 'Ya, Hapus';
                    document.getElementById('confirmBtn').className = 'px-4 py-2 bg-red-500 border border-transparent rounded-md text-white hover:bg-red-600';
                    confirmModal.classList.remove('hidden');
                    confirmModal.classList.add('flex');
                });
            });
            
            // View details button click
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-id');
                    viewRequestDetails(requestId);
                });
            });
        }
        
        // View request details
        function viewRequestDetails(requestId) {
            const request = window.requestHandler.getRequestById(requestId);
            if (!request) return;
            
            // Fill in details modal
            document.getElementById('requestId').textContent = request.id;
            document.getElementById('requestDate').textContent = request.tanggal;
            document.getElementById('requestStatus').textContent = request.status;
            document.getElementById('requestDivision').textContent = request.divisi;
            
            // Clear and fill items table
            const itemsTableBody = document.getElementById('requestItemsTableBody');
            itemsTableBody.innerHTML = '';
            
            request.items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-2 text-sm">${item.kode}</td>
                    <td class="px-4 py-2 text-sm">${item.nama}</td>
                    <td class="px-4 py-2 text-sm">${item.jumlah} ${item.satuan || 'KG'}</td>
                    <td class="px-4 py-2 text-sm">${item.catatan || '-'}</td>
                `;
                itemsTableBody.appendChild(row);
            });
            
            // Show/hide action buttons based on status
            const detailsActionBtn = document.getElementById('detailsActionBtn');
            if (request.status === 'Menunggu') {
                detailsActionBtn.classList.remove('hidden');
                detailsActionBtn.textContent = 'Terima Permintaan';
                detailsActionBtn.addEventListener('click', function() {
                    window.requestHandler.updateRequestStatus(requestId, 'Diterima');
                    detailsModal.classList.add('hidden');
                    detailsModal.classList.remove('flex');
                    loadRequests(); // Refresh the list
                    showNotification('Permintaan berhasil diterima', 'success');
                }, { once: true });
            } else {
                detailsActionBtn.classList.add('hidden');
            }
            
            // Show modal
            detailsModal.classList.remove('hidden');
            detailsModal.classList.add('flex');
        }
        
        // Confirm action button
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (!currentRequestId || !currentActionType) return;
                
                if (currentActionType === 'accept') {
                    window.requestHandler.updateRequestStatus(currentRequestId, 'Diterima');
                    showNotification('Permintaan berhasil diterima', 'success');
                } else if (currentActionType === 'reject') {
                    window.requestHandler.updateRequestStatus(currentRequestId, 'Ditolak');
                    showNotification('Permintaan ditolak', 'info');
                } else if (currentActionType === 'delete') {
                    window.requestHandler.deleteRequest(currentRequestId);
                    showNotification('Permintaan dihapus', 'info');
                }
                
                // Close modal and refresh
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
                loadRequests();
                
                // Reset variables
                currentRequestId = null;
                currentActionType = null;
            });
        }
        
        // Close modals
        cancelBtn.addEventListener('click', function() {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
        });
        
        closeDetailsBtn.addEventListener('click', function() {
            detailsModal.classList.add('hidden');
            detailsModal.classList.remove('flex');
        });
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === confirmModal) {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
            }
            if (event.target === detailsModal) {
                detailsModal.classList.add('hidden');
                detailsModal.classList.remove('flex');
            }
        });
        
        // Simple notification function
        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            toast.innerHTML = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('opacity-0');
                toast.style.transition = 'opacity 0.5s';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    });
</script>
@endpush
@endsection
