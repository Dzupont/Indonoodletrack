<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;
use App\Models\StockHistory;

class GudangController extends Controller
{
    public function dashboard()
    {
        return view('gudang.dashboard');
    }
    
    public function stokBahanBaku()
    {
        // Sample data for bahan baku items
        $bahanBaku = [
            // Bahan Baku Utama
            [
                'id' => 1,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan Baku Utama',
                'deskripsi' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal',
                'gambar' => 'terigu.png'
            ],
            // Bahan Tambahan
            [
                'id' => 9,
                'nama' => 'Carboxymethyl Cellulose',
                'stok' => 350,
                'satuan' => 'kg',
                'harga' => 'Rp 45.000',
                'kode' => 'T01',
                'kategori' => 'Bahan Tambahan',
                'deskripsi' => 'Bahan pengental dan stabilizer yang meningkatkan tekstur dan elastisitas mie',
                'expired' => '15 Juni 2025',
                'fungsi' => 'Pengental, Stabilizer',
                'konsentrasi' => '0.5-1.0%',
                'gambar' => 'carboxymethyl-cellulose.png'
            ],
            [
                'id' => 10,
                'nama' => 'Carboxymethyl Cellulose',
                'stok' => 350,
                'satuan' => 'kg',
                'harga' => 'Rp 45.000',
                'kode' => 'T02',
                'kategori' => 'Bahan Tambahan',
                'deskripsi' => 'Bahan pengental dan stabilizer yang meningkatkan tekstur dan elastisitas mie',
                'expired' => '15 Juni 2025',
                'fungsi' => 'Pengental, Stabilizer',
                'konsentrasi' => '0.5-1.0%',
                'gambar' => 'carboxymethyl-cellulose.png'
            ],
            [
                'id' => 11,
                'nama' => 'Carboxymethyl Cellulose',
                'stok' => 350,
                'satuan' => 'kg',
                'harga' => 'Rp 45.000',
                'kode' => 'T03',
                'kategori' => 'Bahan Tambahan',
                'deskripsi' => 'Bahan pengental dan stabilizer yang meningkatkan tekstur dan elastisitas mie',
                'expired' => '15 Juni 2025',
                'fungsi' => 'Pengental, Stabilizer',
                'konsentrasi' => '0.5-1.0%',
                'gambar' => 'carboxymethyl-cellulose.png'
            ],
            [
                'id' => 2,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 3,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 4,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 5,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 6,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 7,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ],
            [
                'id' => 8,
                'nama' => 'Tepung Terigu Protein Tinggi',
                'stok' => 690,
                'satuan' => 'kg',
                'harga' => 'Rp 12.500',
                'kode' => 'U01',
                'kategori' => 'Bahan dasar utama untuk membuat mie, berperan dalam membentuk struktur dan tekstur',
                'expired' => '30 April 2025',
                'protein' => '12-14%',
                'tekstur' => 'Kuat, Elastis, Kenyal'
            ]
        ];

        $categories = [
            'Bahan Baku Utama',
            'Bahan Tambahan',
            'Bumbu & Perisa',
            'Pelengkap Kemasan',
            'Bahan Pelengkap Lain'
        ];

        return view('gudang.stok-bahan-baku', compact('bahanBaku', 'categories'));
    }
    
    public function monitoringStok()
    {
        return view('gudang.monitoring');
    }
    
    public function returBahanBaku()
    {
        return view('gudang.retur-bahan-baku');
    }
    
    public function riwayatStok(Request $request)
    {
        $query = StockHistory::with('bahanBaku')
            ->orderBy('created_at', 'desc');
        
        // Apply filters if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('category')) {
            $query->whereHas('bahanBaku', function($q) use ($request) {
                $q->where('jenis_bahanbaku', $request->category);
            });
        }
        
        if ($request->filled('activity')) {
            $query->where('activity_type', $request->activity);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('bahanBaku', function($q) use ($search) {
                $q->where('nama_bahanbaku', 'like', "%{$search}%");
            });
        }
        
        $stockHistories = $query->paginate(10);
        
        return view('gudang.riwayat-stok', compact('stockHistories'));
    }
    
    public function permintaanMasuk()
    {
        // Sample data for permintaan masuk
        $permintaanList = [
            [
                'id' => 'PM001',
                'tanggal' => '17 Mei 2025',
                'status' => 'Menunggu',
                'divisi' => 'Produksi',
                'jumlah_item' => 5
            ],
            [
                'id' => 'PM002',
                'tanggal' => '16 Mei 2025',
                'status' => 'Diproses',
                'divisi' => 'Produksi',
                'jumlah_item' => 3
            ],
            [
                'id' => 'PM003',
                'tanggal' => '15 Mei 2025',
                'status' => 'Selesai',
                'divisi' => 'Produksi',
                'jumlah_item' => 7
            ]
        ];
        
        return view('gudang.permintaan-masuk', compact('permintaanList'));
    }
}
