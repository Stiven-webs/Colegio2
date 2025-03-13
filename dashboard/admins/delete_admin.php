<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

// Check if admin ID is provided
if (!isset($_POST['id_admin']) || empty($_POST['id_admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de administrador no proporcionado']);
    exit();
}

$id_admin = (int)$_POST['id_admin'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if trying to delete the default admin (ID 1)
    if ($id_admin === 1) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No se puede eliminar el administrador predeterminado del sistema']);
        exit();
    }
    
    // Delete the administrator
    $stmt = $conn->prepare("DELETE FROM administradores WHERE id_admin = :id_admin");
    $stmt->bindParam(':id_admin', $id_admin);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Administrador eliminado correctamente']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No se encontró el administrador especificado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>