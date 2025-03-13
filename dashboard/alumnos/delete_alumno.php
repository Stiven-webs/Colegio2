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

// Check if student ID is provided
if (!isset($_POST['id_alumno']) || empty($_POST['id_alumno'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de alumno no proporcionado']);
    exit();
}

$id_alumno = (int)$_POST['id_alumno'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Delete the student
    $stmt = $conn->prepare("DELETE FROM alumnos WHERE id_alumno = :id_alumno");
    $stmt->bindParam(':id_alumno', $id_alumno);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Alumno eliminado correctamente']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No se encontró el alumno especificado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>