<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if student ID is provided
if (!isset($_GET['id_alumno']) || empty($_GET['id_alumno'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de alumno no proporcionado']);
    exit();
}

$id_alumno = (int)$_GET['id_alumno'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get student data
    $stmt = $conn->prepare("SELECT id_alumno, nombre, apellido, dni, fecha_nacimiento, direccion, telefono, email, id_grado, activo FROM alumnos WHERE id_alumno = :id_alumno");
    $stmt->bindParam(':id_alumno', $id_alumno);
    $stmt->execute();
    
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($alumno) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'alumno' => $alumno]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Alumno no encontrado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>