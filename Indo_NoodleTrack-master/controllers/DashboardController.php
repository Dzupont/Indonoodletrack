<?php
session_start();
require_once '../config/database.php';

class DashboardController {
    private $conn;
    
    public function __construct() {
        $this->conn = $conn;
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }
    }
    
    public function index() {
        $role = $_SESSION['role'];
        
        // Get statistics based on role
        $stats = $this->getStatistics();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        include '../views/dashboard/index.php';
    }
    
    private function getStatistics() {
        $stats = [];
        
        // Get total raw materials
        $sql = "SELECT COUNT(*) as total FROM raw_materials";
        $result = $this->conn->query($sql);
        $stats['total_materials'] = $result->fetch_assoc()['total'];
        
        // Get pending requests
        $sql = "SELECT COUNT(*) as total FROM requests WHERE status = 'pending'";
        $result = $this->conn->query($sql);
        $stats['pending_requests'] = $result->fetch_assoc()['total'];
        
        // Get low stock materials
        $sql = "SELECT COUNT(*) as total FROM raw_materials rm 
                JOIN stock s ON rm.id = s.material_id 
                WHERE s.quantity < rm.minimum_stock";
        $result = $this->conn->query($sql);
        $stats['low_stock'] = $result->fetch_assoc()['total'];
        
        return $stats;
    }
    
    private function getRecentActivities() {
        $sql = "SELECT al.*, u.username 
                FROM activity_logs al 
                JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT 10";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function manageMaterials() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add':
                        $this->addMaterial();
                        break;
                    case 'update':
                        $this->updateMaterial();
                        break;
                    case 'delete':
                        $this->deleteMaterial();
                        break;
                }
            }
        }
        
        $materials = $this->getMaterials();
        include '../views/dashboard/materials.php';
    }
    
    private function addMaterial() {
        $name = $_POST['name'];
        $unit = $_POST['unit'];
        $minimum_stock = $_POST['minimum_stock'];
        
        $sql = "INSERT INTO raw_materials (name, unit, minimum_stock) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $unit, $minimum_stock);
        $stmt->execute();
        
        $this->logActivity('material', 'add', "Added new material: $name");
        
        header("Location: materials.php");
        exit();
    }
    
    private function getMaterials() {
        $sql = "SELECT rm.*, s.quantity 
                FROM raw_materials rm 
                LEFT JOIN stock s ON rm.id = s.material_id";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    private function logActivity($type, $action, $description) {
        $sql = "INSERT INTO activity_logs (user_id, activity_type, description) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $_SESSION['user_id'], $type, $description);
        $stmt->execute();
    }
}
