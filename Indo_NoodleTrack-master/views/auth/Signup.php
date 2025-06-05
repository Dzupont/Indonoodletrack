<?php
session_start();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Include database connection
require_once __DIR__ . '/../../config/database.php';

// Get fresh connection
$conn = getDBConnection();
if (!$conn || !is_object($conn)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize error and success messages
$error = '';
$success = '';

// Function to safely escape strings
function escape($string) {
    global $conn;
    if (!$conn || !is_object($conn)) {
        $conn = getDBConnection();
    }
    if (!$conn || !is_object($conn)) {
        die("Database connection lost: " . mysqli_connect_error());
    }
    return $conn->real_escape_string($string);
}

// Function to safely prepare and execute statement
function safeQuery($sql, $params) {
    global $conn;
    if (!$conn || !is_object($conn)) {
        $conn = getDBConnection();
    }
    if (!$conn || !is_object($conn)) {
        die("Database connection lost: " . mysqli_connect_error());
    }
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param(...$params);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    return $stmt;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get fresh connection before form submission
    $conn = getDBConnection();
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_tlpn = trim($_POST['no_tlpn'] ?? '');
    $sandi = trim($_POST['sandi'] ?? '');
    $confirm_sandi = trim($_POST['confirm_sandi'] ?? '');
    $role = trim($_POST['role'] ?? '');
    
    // Basic input validation
    if (empty($username) || empty($email) || empty($no_tlpn) || empty($sandi) || empty($confirm_sandi) || empty($role)) {
        $error = "Harap isi semua field!";
    } else if ($sandi !== $confirm_sandi) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else if (!in_array($role, ['gudang', 'manager', 'produksi'])) {
        $error = "Role tidak valid!";
    } else {
        // Prevent SQL injection
        $username = escape($username);
        $email = escape($email);
        $no_tlpn = escape($no_tlpn);
        
        // Check if username already exists
        $stmt = safeQuery("SELECT id FROM users WHERE username = ?", ["s", $username]);
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Check if email already exists
            $stmt = safeQuery("SELECT id FROM users WHERE email = ?", ["s", $email]);
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Email sudah digunakan!";
            } else {
                // Hash password
                $hashed_password = password_hash($sandi, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = safeQuery("INSERT INTO users (username, email, no_tlpn, sandi, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())", 
                    ["sssss", $username, $email, $no_tlpn, $hashed_password, $role]);
                
                $success = "Akun berhasil dibuat! Silakan login.";
                
                // Record registration activity
                $user_id = $conn->insert_id;
                $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description) 
                               VALUES (?, 'registration', 'User registered')";
                $activity_stmt = $conn->prepare($activity_sql);
                $activity_stmt->bind_param("i", $user_id);
                $activity_stmt->execute();
                
                header("Location: login.php?success=1");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IndoNoodle Track - Register</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ecf0f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .main-content {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }

        .card {
            background: #ecf0f3;
            border-radius: 15px;
            box-shadow: 8px 8px 20px #d1d9e6, -8px -8px 20px #ffffff;
            padding: 30px 25px;
        }

        .card h1 {
            font-size: 18px;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: none;
            border-radius: 10px;
            background: #f7f9fb;
            box-shadow: inset 2px 2px 4px #d1d9e6, inset -2px -2px 4px #ffffff;
            font-size: 14px;
            box-sizing: border-box;
            max-width: 100%;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            background-color: #fff;
            box-shadow: 0 0 0 2px #00bcd4;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #00bcd4;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 3px 3px 10px #d1d9e6, -3px -3px 10px #ffffff;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0097a7;
        }

        .alert {
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            font-size: 15px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d0f0e0;
            color: #2e7d32;
            border: 1px solid #b2dfdb;
        }

        .alert-danger {
            background-color: #fce4e4;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .password-field {
            position: relative;
        }

        .password-field i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 18px;
        }

        .password-field i:hover {
            color: #00bcd4;
        }

        .text-center {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="main-content">
        <div class="card">
            <h1>Daftar Akun</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="no_tlpn">Nomor Telepon</label>
                    <input type="tel" name="no_tlpn" id="no_tlpn" required>
                </div>
                
                <div class="form-group">
                    <label for="sandi">Kata Sandi</label>
                    <div class="password-field">
                        <input type="password" name="sandi" id="sandi" required>
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_sandi">Konfirmasi Kata Sandi</label>
                    <div class="password-field">
                        <input type="password" name="confirm_sandi" id="confirm_sandi" required>
                        <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                    </div>
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
                    <button type="submit" class="btn">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                </div>
            </form>
            
            <div class="text-center">
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('sandi');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_sandi');
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>