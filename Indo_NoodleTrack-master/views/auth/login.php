<?php
session_start();

if (isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: login.php?logout=success");
    exit();
}

require_once __DIR__ . '/../../config/database.php';
$conn = getDBConnection();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if (empty($username) || empty($password) || empty($role)) {
        $error = "Harap isi semua field!";
    } else {
        $stmt = $conn->prepare("SELECT id, username, sandi, role FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['sandi'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $activity_sql = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'login', 'User logged in')";
                $activity_stmt = $conn->prepare($activity_sql);
                $activity_stmt->bind_param("i", $user['id']);
                $activity_stmt->execute();

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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - IndoNoodle Track</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
            max-width: 500px;
            padding: 20px;
        }

        .card {
            background: #ecf0f3;
            border-radius: 20px;
            box-shadow: 10px 10px 25px #d1d9e6, -10px -10px 25px #ffffff;
            padding: 40px 30px;
        }

        .card h1 {
            font-size: 30px;
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 12px;
            background: #f7f9fb;
            box-shadow: inset 3px 3px 6px #d1d9e6, inset -3px -3px 6px #ffffff;
            font-size: 16px;
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
            padding: 14px;
            background-color: #00bcd4;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 4px 4px 15px #d1d9e6, -4px -4px 15px #ffffff;
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

        .text-center a {
            color: #00bcd4;
            text-decoration: none;
        }

        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
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
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="text-center">
            <p>Belum punya akun? <a href="Signup.php">Daftar</a></p>
            <p><a href="forgot-password.php">Lupa Kata Sandi?</a></p>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
