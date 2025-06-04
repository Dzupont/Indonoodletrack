@extends('layouts.app-layout')

@section('header', 'Riwayat')

@section('content')
<div class="p-6">

    <!-- Main content area -->
    <div id="mainContent" class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-primary text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tanggal Aktivitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Aktivitas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Table content will be populated by JavaScript -->
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Memuat data riwayat...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="paginationInfo" class="px-6 py-3 text-sm text-gray-500">
            Menampilkan <span id="currentItems">0</span> dari <span id="totalItems">0</span>
        </div>
    </div>

    <!-- Detail View (initially hidden) -->
    <div id="detailView" class="bg-white overflow-hidden shadow-sm rounded-lg mt-6 hidden">
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input id="detailName" type="text" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor ID</label>
                    <input id="detailId" type="text" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                    <input id="detailDivision" type="text" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aktivitas</label>
                <input id="detailActivity" type="text" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Lampiran</label>
                    <div id="detailAttachment" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50 cursor-pointer text-primary">
                        Klik untuk unduh
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea id="detailNotes" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50 resize-none" readonly rows="1"></textarea>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <input id="detailStatus" type="text" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
            </div>

            <!-- Item Details Table -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item yang Diminta</label>
                <div class="overflow-x-auto border border-gray-300 rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Items will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end">
                <button id="backButton" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include request handler script -->
<script src="{{ asset('js/request-handler.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load request history
        loadRequestHistory();
        
        // Back button event listener
        document.getElementById('backButton').addEventListener('click', function() {
            document.getElementById('detailView').classList.add('hidden');
            document.getElementById('mainContent').classList.remove('hidden');
        });
        
        function loadRequestHistory() {
            if (!window.requestHandler) {
                console.error('Request handler not found');
                return;
            }
            
            // Get all requests
            const requests = window.requestHandler.getRequests();
            const tableBody = document.getElementById('historyTableBody');
            
            // Clear table
            tableBody.innerHTML = '';
            
            if (requests.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data riwayat</td>
                    </tr>
                `;
                updatePaginationInfo(0, 0);
                return;
            }
            
            // Update pagination info
            updatePaginationInfo(requests.length, requests.length);
            
            // Populate table
            requests.forEach(request => {
                const row = document.createElement('tr');
                
                // Format date using the formatDate function from request-handler.js
                const formattedDate = window.requestHandler.formatDate(request.tanggal);
                
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
                
                // Create row
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formattedDate}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Permintaan Bahan ${request.items?.[0]?.nama || ''}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${request.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button class="view-btn text-primary hover:text-primary-dark" data-id="${request.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button class="delete-btn text-red-500 hover:text-red-700" data-id="${request.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
            
            // Add event listeners
            addEventListeners();
        }
        
        function addEventListeners() {
            // View detail buttons
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-id');
                    showRequestDetail(requestId);
                });
            });
            
            // Delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.getAttribute('data-id');
                    if (confirm('Apakah Anda yakin ingin menghapus riwayat ini?')) {
                        window.requestHandler.deleteRequest(requestId);
                        loadRequestHistory(); // Reload the table
                        showNotification('Riwayat berhasil dihapus', 'info');
                    }
                });
            });
        }
        
        function showRequestDetail(requestId) {
            // Get request data
            const request = window.requestHandler.getRequestById(requestId);
            if (!request) return;
            
            // Fill detail fields
            document.getElementById('detailName').value = 'Divisi Produksi';
            document.getElementById('detailId').value = requestId;
            document.getElementById('detailDivision').value = 'Produksi';
            document.getElementById('detailActivity').value = `Permintaan Bahan ${request.items?.[0]?.nama || ''}`;
            document.getElementById('detailNotes').value = request.catatan || '-';
            document.getElementById('detailStatus').value = request.status;
            
            // Add the date to detail view (if it exists)
            if (request.tanggal) {
                const dateField = document.createElement('div');
                dateField.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="text" value="${window.requestHandler.formatDate(request.tanggal)}" class="w-full p-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                `;
                
                // Insert after the status field
                const statusField = document.getElementById('detailStatus').parentNode;
                statusField.parentNode.insertBefore(dateField, statusField.nextSibling);
            }
            
            // Clear and fill items table
            const itemsTable = document.getElementById('detailItemsTable');
            itemsTable.innerHTML = '';
            
            if (request.items && request.items.length > 0) {
                request.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-2 text-sm">${item.kode || '-'}</td>
                        <td class="px-4 py-2 text-sm">${item.nama || '-'}</td>
                        <td class="px-4 py-2 text-sm">${item.jumlah || '0'} ${item.satuan || 'KG'}</td>
                        <td class="px-4 py-2 text-sm">${item.catatan || '-'}</td>
                    `;
                    itemsTable.appendChild(row);
                });
            } else {
                itemsTable.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">Tidak ada item</td>
                    </tr>
                `;
            }
            
            // Show detail view, hide main content
            document.getElementById('mainContent').classList.add('hidden');
            document.getElementById('detailView').classList.remove('hidden');
        }
        
        function updatePaginationInfo(current, total) {
            document.getElementById('currentItems').textContent = current;
            document.getElementById('totalItems').textContent = total;
        }
        
        // No longer needed as we're using the formatDate function from request-handler.js
        
        // Notification function
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
