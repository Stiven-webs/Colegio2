<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if admin ID is provided
if (!isset($_GET['id_admin']) || empty($_GET['id_admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de administrador no proporcionado']);
    exit();
}

$id_admin = (int)$_GET['id_admin'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get administrator data
    $stmt = $conn->prepare("SELECT id_admin, nombre, apellido, usuario, email, telefono, activo FROM administradores WHERE id_admin = :id_admin");
    $stmt->bindParam(':id_admin', $id_admin);
    $stmt->execute();
    
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'admin' => $admin]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Administrador no encontrado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>