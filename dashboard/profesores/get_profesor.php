<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if teacher ID is provided
if (!isset($_GET['id_profesor']) || empty($_GET['id_profesor'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de profesor no proporcionado']);
    exit();
}

$id_profesor = (int)$_GET['id_profesor'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get teacher data
    $stmt = $conn->prepare("SELECT id_profesor, nombre, apellido, dni, fecha_nacimiento, direccion, telefono, email, fecha_contratacion, especialidad, activo FROM profesores WHERE id_profesor = :id_profesor");
    $stmt->bindParam(':id_profesor', $id_profesor);
    $stmt->execute();
    
    $profesor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($profesor) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'profesor' => $profesor]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Profesor no encontrado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>