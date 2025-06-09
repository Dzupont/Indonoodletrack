<?php
require_once __DIR__ . '/database.php';

$conn = getDBConnection();

// Periksa apakah tabel raw_materials ada
echo "<h3>Status Tabel raw_materials:</h3>";

$sql = "SHOW TABLES LIKE 'raw_materials'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<div class='alert alert-success'>Tabel raw_materials ditemukan!</div>";
    
    // Periksa struktur tabel
    $sql = "DESCRIBE raw_materials";
    $result = $conn->query($sql);
    echo "<h4>Struktur Tabel:</h4>";
    echo "<table class='table'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Periksa data di tabel
    $sql = "SELECT * FROM raw_materials";
    $result = $conn->query($sql);
    echo "<h4>Data di Tabel:</h4>";
    echo "<table class='table'>";
    echo "<tr><th>ID</th><th>Name</th><th>Unit</th><th>Stock</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='alert alert-danger'>Tabel raw_materials tidak ditemukan!</div>";
}
