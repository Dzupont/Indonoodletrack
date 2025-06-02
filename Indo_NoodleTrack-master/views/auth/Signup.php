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
            background-color: #f0f4f8;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 350px; /* Increased width for better layout */
            text-align: center;
        }

        h2 {
            color: #00bcd4; /* Main color */
            margin-bottom: 25px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #00bcd4; /* Focus color */
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #00bcd4; /* Button color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0097a7; /* Darker on hover */
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            display: none; /* Hidden by default */
        }

        .alert-danger {
            background-color: #ffebee; /* Alert background */
            color: #c62828; /* Alert text color */
            border: 1px solid #ffcdd2;
        }

        .text-center {
            margin-top: 20px;
        }

        .password-field {
            position: relative;
            width: 100%;
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

        a {
            color: #00bcd4; /* Link color */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline; /* Underline on hover */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Daftar Akun</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" style="display: block;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="display: block;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form action="signup.php" method="POST">
            <div>
                <label for="username">Nama Pengguna</label>
                <input type="text" name="username" id="username" required>
            </div>
            
            <div>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div>
                <label for="no_tlpn">Nomor Telepon</label>
                <input type="tel" name="no_tlpn" id="no_tlpn" required>
            </div>
            
            <div>
                <label for="sandi">Kata Sandi</label>
                <div class="password-field">
                    <input type="password" name="sandi" id="sandi" required>
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
            </div>
            
            <div>
                <label for="confirm_sandi">Konfirmasi Kata Sandi</label>
                <div class="password-field">
                    <input type="password" name="confirm_sandi" id="confirm_sandi" required>
                    <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                </div>
            </div>
            
            <div>
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="">Pilih Role</option>
                    <option value="gudang">Gudang</option>
                    <option value="manager">Manager</option>
                    <option value="produksi">Produksi</option>
                </select>
            </div>
            
            <button type="submit">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>
        
        <div class="text-center">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
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
    </div>
</body>
</html>