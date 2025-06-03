<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Semua field harus diisi";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username sudah terdaftar";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            
            if ($stmt->execute()) {
                header('Location: login.php?success=1');
                exit();
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Indo Noodle Track</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: hsl(189, 64.70%, 43.30%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: rgb(9, 176, 205);
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success {
            color: green;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background-color: rgb(9, 176, 205);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: rgb(21, 191, 197);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: rgb(9, 176, 205);
            text-decoration: none;
        }

        .back-link:hover {
            color: rgb(21, 191, 197);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="gudang">Gudang</option>
                    <option value="manager">Manager</option>
                    <option value="produksi">Produksi</option>
                </select>
            </div>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <a href="login.php" class="back-link">Kembali ke Login</a>
    </div>
</body>
</html>
