<?php

require_once __DIR__ . '/../config/database.php';

class PermintaanMasukController
{
    /**
     * Menampilkan daftar permintaan masuk
     *
     * @return void
     */
    public function index()
    {
        session_start();
        
        // Check if user is logged in and has produksi role
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
            header('Location: ../login.php');
            exit();
        }

        $conn = getDBConnection();
        
        // Get user data
        $user_id = $_SESSION['user_id'];
        
        // Fetch permintaan masuk
        $sql = "SELECT r.*, m.nama as nama_bahan, m.satuan, u.username as requested_by
                FROM requests r
                LEFT JOIN raw_materials m ON r.material_id = m.id
                LEFT JOIN users u ON r.requested_by = u.id
                WHERE r.status != 'rejected'
                ORDER BY r.created_at DESC";
        
        $result = $conn->query($sql);
        $permintaan = array();
        
        while ($row = $result->fetch_assoc()) {
            $permintaan[] = $row;
        }
        
        require_once __DIR__ . '/../views/auth/Produksi/permintaanmasuk.php';
    }
    
    /**
     * Approve permintaan masuk
     *
     * @param int $request_id
     * @return void
     */
    public function approve($request_id)
    {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
            header('Location: ../login.php');
            exit();
        }

        $conn = getDBConnection();
        
        // Update request status
        $sql = "UPDATE requests SET 
                status = 'approved',
                approved_by = ?,
                approved_at = NOW()
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $_SESSION['user_id'], $request_id);
        
        if ($stmt->execute()) {
            // Log activity
            $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description) 
                            VALUES (?, 'approve_request', ?)";
            
            $activity_stmt = $conn->prepare($activity_sql);
            $description = "Menyetujui permintaan bahan baku dengan ID $request_id";
            $activity_stmt->bind_param("is", $_SESSION['user_id'], $description);
            $activity_stmt->execute();
            
            header('Location: permintaanmasuk.php?status=success');
            exit();
        } else {
            header('Location: permintaanmasuk.php?status=error');
            exit();
        }
    }
    
    /**
     * Reject permintaan masuk
     *
     * @param int $request_id
     * @return void
     */
    public function reject($request_id)
    {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
            header('Location: ../login.php');
            exit();
        }

        $conn = getDBConnection();
        
        // Update request status
        $sql = "UPDATE requests SET 
                status = 'rejected',
                rejected_by = ?,
                rejected_at = NOW(),
                rejection_reason = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        
        $rejection_reason = $_POST['rejection_reason'] ?? '';
        $stmt->bind_param("iss", $_SESSION['user_id'], $rejection_reason, $request_id);
        
        if ($stmt->execute()) {
            // Log activity
            $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description) 
                            VALUES (?, 'reject_request', ?)";
            
            $activity_stmt = $conn->prepare($activity_sql);
            $description = "Menolak permintaan bahan baku dengan ID $request_id: $rejection_reason";
            $activity_stmt->bind_param("is", $_SESSION['user_id'], $description);
            $activity_stmt->execute();
            
            header('Location: permintaanmasuk.php?status=success');
            exit();
        } else {
            header('Location: permintaanmasuk.php?status=error');
            exit();
        }
    }
    
    /**
     * Show monitoring page for specific request
     *
     * @param int $request_id
     * @return void
     */
    public function monitoring($request_id)
    {
        session_start();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'produksi') {
            header('Location: ../login.php');
            exit();
        }

        $conn = getDBConnection();
        
        // Fetch request details
        $sql = "SELECT r.*, m.nama as nama_bahan, m.satuan, u.username as requested_by
                FROM requests r
                LEFT JOIN raw_materials m ON r.material_id = m.id
                LEFT JOIN users u ON r.requested_by = u.id
                WHERE r.id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $request = $result->fetch_assoc();
        
        if (!$request) {
            header('Location: permintaanmasuk.php?status=not_found');
            exit();
        }
        
        require_once __DIR__ . '/../views/auth/Produksi/monitoring.php';
    }
        

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
