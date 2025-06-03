<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #e3f2fd; /* Warna biru telur asin untuk seluruh halaman */
    }

    .sidebar {
      width: 230px;
      height: 100vh;
      background-color: #036b91; /* Warna sidebar */
      color: white;
      padding: 30px 20px;
      position: fixed;
      text-align: center;
    }

    .sidebar img {
      width: 100%; /* Atur agar gambar memenuhi lebar sidebar */
      height: auto;
      margin-bottom: 20px; /* Jarak bawah gambar */
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 12px;
      margin: 8px 0;
      border-radius: 8px;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background-color: #024e6c;
    }

    .navbar {
      display: flex;
      flex-direction: column; /* Mengubah navbar menjadi vertikal */
      margin-top: 20px; /* Jarak di atas navbar */
    }

    .content {
      margin-left: 250px; /* Jarak untuk memisahkan dari sidebar */
      padding: 30px;
      background-color: #ffffff; /* Warna putih untuk konten */
      height: 100vh; /* Memastikan konten memenuhi tinggi halaman */
      overflow: auto; /* Menambahkan scroll jika konten melebihi tinggi */
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    .header h1 {
      font-size: 26px;
      color: #036b91;
      margin: 0;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    .logout-btn {
      background-color: #e74c3c;
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .logout-btn:hover {
      background-color: #c0392b;
    }

    .notification {
      background-color: #e0f7fa;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      display: none; /* Menyembunyikan notifikasi secara default */
    }

    .card-container {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      padding: 25px;
      flex: 1 1 200px; /* Flexible width */
      min-width: 200px;
    }

    .card-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 18px;
      color: #036b91;
    }

    .status {
      display: flex;
      align-items: center;
      font-size: 16px;
      font-weight: 600;
      gap: 10px;
    }

    .status.green {
      color: #2ecc71;
    }

    .status.red {
      color: #e74c3c;
    }
  </style>
</head>
<body>
  <div class="sidebar">
  <img src="assets/images/logoindonoodletrack2.png" alt="Logo">
    <div class="navbar">
      <a href="#">Dashboard</a>
      <a href="#">Permintaan Masuk</a>
      <a href="#">Retur Masuk</a>
      <a href="#">Monitoring</a>
      <a href="#">Stok</a>
    </div>
  </div>

  <div class="content">
    <div class="header">
      <h1>Dashboard</h1>
      <div class="user-info">
        <img src="assets/images/logoindonoodletrack2.png" alt="Logo IndoNoodleTrack" />
        <span>Divisi Gudang</span>
        <a href="../../../views/auth/logout.php" class="logout-btn">Logout</a>
      </div>
    </div>

    <div class="notification" id="welcome-notification">
      <p>Selamat datang di dashboard Anda!</p>
    </div>

    <div class="card-container">
      <div class="card">
        <div class="card-title">Permintaan</div>
        <div class="status green">✔ Completed</div>
      </div>
      <div class="card">
        <div class="card-title">Stok</div>
        <div class="status red">✖ Pending</div>
      </div>
    </div>
  </div>

  <script>
    // Menampilkan notifikasi selamat datang saat halaman dimuat
    window.onload = function() {
      document.getElementById('welcome-notification').style.display = 'block';
      
      // Menghilangkan notifikasi setelah 3 detik
      setTimeout(function() {
        document.getElementById('welcome-notification').style.display = 'none';
      }, 3000);
    };
  </script>
</body>
</html>