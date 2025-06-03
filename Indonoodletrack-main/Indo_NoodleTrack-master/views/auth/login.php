<?php
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    // Destroy session and redirect to login
    session_destroy();
    header("Location: login.php?logout=success");
    exit();
}

// Include database connection
require_once __DIR__ . '/../../config/database.php';

// Get fresh connection
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize error and success messages
$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    
    // Basic input validation
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Harap isi semua field!";
    } else {
        // Query to check user credentials
        $stmt = $conn->prepare("SELECT id, username, sandi, role FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Clean up
        $stmt->close();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['sandi'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Record login activity
                $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description) 
                               VALUES (?, 'login', 'User logged in')";
                $activity_stmt = $conn->prepare($activity_sql);
                $activity_stmt->bind_param("i", $user['id']);
                $activity_stmt->execute();
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'gudang':
                        header("Location: ../../views/auth/Gudang/dashboardgudang.php");
                        break;
                    case 'manager':
                        header("Location: ../../views/auth/Manager/dashboardmanager.php");
                        break;
                    case 'produksi':
                        header("Location: ../../views/auth/Produksi/dashboardproduksi.php");
                        break;
                    default:
                        header("Location: ../../index.php");
                }
                exit();
            } else {
                $error = "Kata sandi salah!";
            }
        } else {
            $error = "Username atau role tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IndoNoodle Track</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .main-content {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin: 20px 0;
        }

        .card h1 {
            margin: 0;
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #00bcd4;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #00bcd4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0097a7;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .password-field {
            position: relative;
        }

        .password-field i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .password-field i:hover {
            color: #00bcd4;
        }

        .text-center {
            text-align: center;
            margin-top: 20px;
        }

        .text-center a {
            color: #00bcd4;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <script src="../../../assets/js/main.js"></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</head>
<body>
    <div class="main-content">
        <div class="card">
            <h1>Login</h1>
            
            <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Anda telah berhasil logout!
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="">Pilih Role</option>
                        <option value="gudang">Gudang</option>
                        <option value="manager">Manager</option>
                        <option value="produksi">Produksi</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password" required>
            </h>
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="text-center">
                <p>Belum punya akun? <a href="signup.php">Daftar</a></p>
                <p><a href="forgot-password.php">Lupa Kata Sandi?</a></p>
            </div>
        </div>
    </div>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
