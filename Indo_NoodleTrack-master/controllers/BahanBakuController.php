<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahanBakuUtama = BahanBaku::where('jenis_bahanbaku', 'Bahan Baku Utama')->get();
        $bahanTambahan = BahanBaku::where('jenis_bahanbaku', 'Bahan Tambahan')->get();
        $bumbuPerisa = BahanBaku::where('jenis_bahanbaku', 'Bumbu & Perisa')->get();
        $pelengkapKemasan = BahanBaku::where('jenis_bahanbaku', 'Pelengkap Kemasan')->get();
        $bahanPelengkapLain = BahanBaku::where('jenis_bahanbaku', 'Bahan Pelengkap Lain')->get();
        
        return view('gudang.stok-bahan-baku', compact('bahanBakuUtama', 'bahanTambahan', 'bumbuPerisa', 'pelengkapKemasan', 'bahanPelengkapLain'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        return view('gudang.detail-bahan-baku', compact('bahanBaku'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisOptions = [
            'Bahan Baku Utama',
            'Bahan Tambahan',
            'Bumbu & Perisa',
            'Pelengkap Kemasan',
            'Bahan Pelengkap Lain'
        ];
        
        return view('gudang.tambah-bahan-baku', compact('jenisOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahanbaku' => 'required|string|max:255',
            'stok_bahanbaku' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'harga' => 'nullable|numeric|min:0',
            'jenis_bahanbaku' => 'required|in:Bahan Baku Utama,Bahan Tambahan,Bumbu & Perisa,Pelengkap Kemasan,Bahan Pelengkap Lain',
            'deskripsi' => 'nullable|string',
            'tanggal_expired' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate kode unik
        $prefix = '';
        switch($request->jenis_bahanbaku) {
            case 'Bahan Baku Utama': $prefix = 'BBU'; break;
            case 'Bahan Tambahan': $prefix = 'BBT'; break;
            case 'Bumbu & Perisa': $prefix = 'BPR'; break;
            case 'Pelengkap Kemasan': $prefix = 'PKM'; break;
            case 'Bahan Pelengkap Lain': $prefix = 'BPL'; break;
        }
        
        $kode = $prefix . '-' . strtoupper(Str::random(5));
        
        // Handle file upload
        $gambarPath = 'default.png';
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/item-images', $gambarName);
            // Pastikan symlink sudah ada agar gambar bisa diakses
            if (!file_exists(public_path('storage'))) {
                symlink(storage_path('app/public'), public_path('storage'));
            }
            $gambarPath = $gambarName;
        } else {
            // Set default image based on category
            switch($request->jenis_bahanbaku) {
                case 'Bahan Baku Utama': $gambarPath = 'terigu.png'; break;
                case 'Bahan Tambahan': $gambarPath = 'carboxymethyl-cellulose.png'; break;
                case 'Bumbu & Perisa': $gambarPath = 'msg.png'; break;
                case 'Pelengkap Kemasan': $gambarPath = 'dus.png'; break;
                case 'Bahan Pelengkap Lain': $gambarPath = 'cabai.png'; break;
            }
        }
        
        // Prepare additional attributes if any
        $atributTambahan = [];
        if ($request->has('atribut_tambahan')) {
            $atributTambahan = $request->atribut_tambahan;
        }
        
        // Create new bahan baku
        BahanBaku::create([
            'nama_bahanbaku' => $request->nama_bahanbaku,
            'stok_bahanbaku' => $request->stok_bahanbaku,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
            'kode' => $kode,
            'jenis_bahanbaku' => $request->jenis_bahanbaku,
            'deskripsi' => $request->deskripsi,
            'tanggal_expired' => $request->tanggal_expired,
            'gambar' => $gambarPath,
            'atribut_tambahan' => $atributTambahan,
        ]);
        
        return redirect()->route('gudang.stok-bahan-baku')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $jenisOptions = [
            'Bahan Baku Utama',
            'Bahan Tambahan',
            'Bumbu & Perisa',
            'Pelengkap Kemasan',
            'Bahan Pelengkap Lain'
        ];
        
        return view('gudang.edit-bahan-baku', compact('bahanBaku', 'jenisOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahanbaku' => 'required|string|max:255',
            'stok_bahanbaku' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'harga' => 'nullable|numeric|min:0',
            'jenis_bahanbaku' => 'required|in:Bahan Baku Utama,Bahan Tambahan,Bumbu & Perisa,Pelengkap Kemasan,Bahan Pelengkap Lain',
            'deskripsi' => 'nullable|string',
            'tanggal_expired' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $bahanBaku = BahanBaku::findOrFail($id);
        
        // Handle file upload if new image is provided
        if ($request->hasFile('gambar')) {
            // Delete old image if it's not a default image
            if ($bahanBaku->gambar != 'default.png' && 
                $bahanBaku->gambar != 'terigu.png' && 
                $bahanBaku->gambar != 'carboxymethyl-cellulose.png' && 
                $bahanBaku->gambar != 'msg.png' && 
                $bahanBaku->gambar != 'dus.png' && 
                $bahanBaku->gambar != 'cabai.png') {
                Storage::delete('public/item-images/' . $bahanBaku->gambar);
            }
            
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . $gambar->getClientOriginalName();
            $gambar->storeAs('public/item-images', $gambarName);
            $bahanBaku->gambar = $gambarName;
        }
        
        // Prepare additional attributes if any
        if ($request->has('atribut_tambahan')) {
            $bahanBaku->atribut_tambahan = $request->atribut_tambahan;
        }
        
        // Update bahan baku
        $bahanBaku->nama_bahanbaku = $request->nama_bahanbaku;
        $bahanBaku->stok_bahanbaku = $request->stok_bahanbaku;
        $bahanBaku->satuan = $request->satuan;
        $bahanBaku->harga = $request->harga;
        $bahanBaku->jenis_bahanbaku = $request->jenis_bahanbaku;
        $bahanBaku->deskripsi = $request->deskripsi;
        $bahanBaku->tanggal_expired = $request->tanggal_expired;
        $bahanBaku->save();
        
        return redirect()->route('gudang.stok-bahan-baku')
            ->with('success', 'Bahan baku berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        
        // Delete image if it's not a default image
        if ($bahanBaku->gambar != 'default.png' && 
            $bahanBaku->gambar != 'terigu.png' && 
            $bahanBaku->gambar != 'carboxymethyl-cellulose.png' && 
            $bahanBaku->gambar != 'msg.png' && 
            $bahanBaku->gambar != 'dus.png' && 
            $bahanBaku->gambar != 'cabai.png') {
            Storage::delete('public/item-images/' . $bahanBaku->gambar);
        }
        
        $bahanBaku->delete();
        
        return redirect()->route('gudang.stok-bahan-baku')
            ->with('success', 'Bahan baku berhasil dihapus!');
    }
    
    /**
     * Increment stock of the specified resource.
     */
    public function incrementStock(Request $request, string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $amount = $request->input('amount', 1);
        $notes = $request->input('notes', '');
        
        // Increment stock
        $bahanBaku->stok_bahanbaku += $amount;
        $bahanBaku->save();
        
        // Record in stock history
        StockHistory::create([
            'bahan_baku_id' => $bahanBaku->id_bahanbaku,
            'activity_type' => 'increment',
            'amount' => $amount,
            'stock_after' => $bahanBaku->stok_bahanbaku,
            'operator_name' => auth()->user()->name ?? 'System',
            'notes' => $notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil ditambahkan',
            'new_stock' => $bahanBaku->stok_bahanbaku
        ]);
    }
    
    /**
     * Decrement stock of the specified resource.
     */
    public function decrementStock(Request $request, string $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);
        $amount = $request->input('amount', 1);
        $notes = $request->input('notes', '');
        
        if ($bahanBaku->stok_bahanbaku < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi',
                'current_stock' => $bahanBaku->stok_bahanbaku
            ], 400);
        }
        
        // Decrement stock
        $bahanBaku->stok_bahanbaku -= $amount;
        $bahanBaku->save();
        
        // Record in stock history
        StockHistory::create([
            'bahan_baku_id' => $bahanBaku->id_bahanbaku,
            'activity_type' => 'decrement',
            'amount' => $amount,
            'stock_after' => $bahanBaku->stok_bahanbaku,
            'operator_name' => auth()->user()->name ?? 'System',
            'notes' => $notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil dikurangi',
            'new_stock' => $bahanBaku->stok_bahanbaku
        ]);
    }
}
