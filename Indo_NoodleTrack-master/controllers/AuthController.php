<?php
session_start();
require_once '../config/database.php';

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Log activity
                    $this->logActivity($user['id'], 'login', 'User logged in');
                    
                    header("Location: dashboard.php");
                    exit();
                }
            }
            
            $_SESSION['error'] = "Invalid username or password";
        }
        
        include '../views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];
            
            $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $password, $role);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "Registration failed";
            }
        }
        
        include '../views/auth/register.php';
    }
    
    public function logout() {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    
    private function logActivity($user_id, $activity_type, $description) {
        $sql = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $activity_type, $description);
        $stmt->execute();
    }
}
