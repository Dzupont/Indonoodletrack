<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanBaku;

class PermintaanMasukController extends Controller
{
    /**
     * Menampilkan halaman keranjang
     *
     * @return \Illuminate\View\View
     */
    public function keranjang()
    {
        // Ambil data keranjang dari localStorage (akan diisi oleh JavaScript)
        $keranjang = [];
        
        // Data keranjang akan diisi oleh JavaScript dari localStorage
        // Hitung total harga
        $totalHarga = collect($keranjang)->sum(function($item) {
            return ($item['harga'] ?? 0) * ($item['jumlah'] ?? 0);
        });
        
        return view('produksi.keranjang', [
            'keranjang' => $keranjang,
            'totalHarga' => $totalHarga
        ]);
    }
    public function index()
    {
        // Define categories
        $categories = [
            'Bahan Baku Utama',
            'Bahan Tambahan',
            'Bumbu & Perisa',
            'Pelengkap Kemasan',
            'Bahan Pelengkap Lain'
        ];
        
        // Fetch all bahan baku from database
        $allBahanBaku = BahanBaku::all();
        
        // Organize by category
        $bahanBakuByCategory = [];
        
        foreach ($categories as $category) {
            $bahanBakuByCategory[$category] = [];
        }
        
        foreach ($allBahanBaku as $item) {
            // Extract protein and texture from atribut_tambahan if available
            $atribut = $item->atribut_tambahan ?? [];
            
            // Determine category (default to 'Bahan Baku Utama' if not specified)
            $kategori = $item->jenis_bahanbaku;
            if (!in_array($kategori, $categories)) {
                $kategori = 'Bahan Baku Utama';
            }
            
            // Determine default image based on category
            $defaultImage = 'terigu.png';
            if ($kategori === 'Bahan Tambahan') {
                $defaultImage = 'carboxymethyl-cellulose.png';
            } elseif ($kategori === 'Bumbu & Perisa') {
                $defaultImage = 'msg.png';
            } elseif ($kategori === 'Pelengkap Kemasan') {
                $defaultImage = 'dus.png';
            } elseif ($kategori === 'Bahan Pelengkap Lain') {
                $defaultImage = 'terigu.png';
            }
            
            $formattedItem = [
                'id' => $item->id_bahanbaku,
                'nama' => $item->nama_bahanbaku,
                'stok' => $item->stok_bahanbaku,
                'satuan' => $item->satuan ?? 'kg',
                'harga' => 'Rp ' . number_format($item->harga ?? 0, 0, ',', '.'),
                'kode' => $item->kode ?? 'BB' . str_pad($item->id_bahanbaku, 3, '0', STR_PAD_LEFT),
                'kategori' => $kategori,
                'expired' => $item->tanggal_expired ? $item->tanggal_expired->format('d F Y') : 'Tidak ada kadaluarsa',
                'protein' => $atribut['protein'] ?? '12-14%',
                'tekstur' => $atribut['tekstur'] ?? 'Normal',
                'deskripsi' => $item->deskripsi,
                'gambar' => $item->gambar ? asset('storage/' . $item->gambar) : asset('item-images/' . $defaultImage)
            ];
            
            $bahanBakuByCategory[$kategori][] = $formattedItem;
        }
        
        // For backward compatibility, keep the original $bahanBaku variable
        // with all items (first category will be shown by default)
        $bahanBaku = array_values($bahanBakuByCategory['Bahan Baku Utama'] ?? []);

        return view('produksi.permintaan-masuk', compact('bahanBaku', 'categories', 'bahanBakuByCategory'));
    }
}
