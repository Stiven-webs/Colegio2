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

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get form data
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = !empty($_POST['dni']) ? trim($_POST['dni']) : null;
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $direccion = !empty($_POST['direccion']) ? trim($_POST['direccion']) : null;
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $id_grado = !empty($_POST['id_grado']) ? (int)$_POST['id_grado'] : null;
    $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
    
    // Validate data
    $errors = [];
    
    if (empty($nombre)) {
        $errors[] = 'El nombre es requerido';
    }
    
    if (empty($apellido)) {
        $errors[] = 'El apellido es requerido';
    }
    
    if ($dni !== null) {
        // Check if DNI already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM alumnos WHERE dni = :dni");
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'El DNI ya está registrado';
        }
    }
    
    if ($email !== null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del email es inválido';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM alumnos WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'El email ya está registrado';
            }
        }
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
    
    // Insert new student
    $stmt = $conn->prepare("INSERT INTO alumnos (nombre, apellido, dni, fecha_nacimiento, direccion, telefono, email, id_grado, fecha_registro, activo) 
                          VALUES (:nombre, :apellido, :dni, :fecha_nacimiento, :direccion, :telefono, :email, :id_grado, CURRENT_DATE, :activo)");
    
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id_grado', $id_grado);
    $stmt->bindParam(':activo', $activo);
    
    $stmt->execute();
    
    // Get the ID of the newly inserted student
    $id_alumno = $conn->lastInsertId();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Alumno agregado correctamente', 'id_alumno' => $id_alumno]);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>