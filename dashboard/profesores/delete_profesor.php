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

// Check if teacher ID is provided
if (!isset($_POST['id_profesor']) || empty($_POST['id_profesor'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'ID de profesor no proporcionado']);
    exit();
}

$id_profesor = (int)$_POST['id_profesor'];

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Delete the teacher
    $stmt = $conn->prepare("DELETE FROM profesores WHERE id_profesor = :id_profesor");
    $stmt->bindParam(':id_profesor', $id_profesor);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Profesor eliminado correctamente']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'No se encontró el profesor especificado']);
    }
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>