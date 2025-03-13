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
    $id_profesor = (int)$_POST['id_profesor'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = !empty($_POST['dni']) ? trim($_POST['dni']) : null;
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;
    $direccion = !empty($_POST['direccion']) ? trim($_POST['direccion']) : null;
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    $fecha_contratacion = !empty($_POST['fecha_contratacion']) ? $_POST['fecha_contratacion'] : null;
    $especialidad = !empty($_POST['especialidad']) ? trim($_POST['especialidad']) : null;
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
        // Check if DNI already exists (excluding current teacher)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM profesores WHERE dni = :dni AND id_profesor != :id_profesor");
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':id_profesor', $id_profesor);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'El DNI ya está registrado';
        }
    }
    
    if ($email !== null) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del email es inválido';
        } else {
            // Check if email already exists (excluding current teacher)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM profesores WHERE email = :email AND id_profesor != :id_profesor");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id_profesor', $id_profesor);
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
    
    // Update teacher
    $stmt = $conn->prepare("UPDATE profesores SET 
                          nombre = :nombre, 
                          apellido = :apellido, 
                          dni = :dni, 
                          fecha_nacimiento = :fecha_nacimiento, 
                          direccion = :direccion, 
                          telefono = :telefono, 
                          email = :email, 
                          fecha_contratacion = :fecha_contratacion, 
                          especialidad = :especialidad, 
                          activo = :activo 
                          WHERE id_profesor = :id_profesor");
    
    $stmt->bindParam(':id_profesor', $id_profesor);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':dni', $dni);
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':fecha_contratacion', $fecha_contratacion);
    $stmt->bindParam(':especialidad', $especialidad);
    $stmt->bindParam(':activo', $activo);
    
    $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Profesor actualizado correctamente']);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>