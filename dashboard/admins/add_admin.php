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
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
    
    // Validate data
    $errors = [];
    
    if (empty($nombre)) {
        $errors[] = 'El nombre es requerido';
    }
    
    if (empty($apellido)) {
        $errors[] = 'El apellido es requerido';
    }
    
    if (empty($usuario)) {
        $errors[] = 'El usuario es requerido';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM administradores WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'El nombre de usuario ya está en uso';
        }
    }
    
    if (empty($email)) {
        $errors[] = 'El email es requerido';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El formato del email es inválido';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM administradores WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'El email ya está registrado';
        }
    }
    
    if (empty($password)) {
        $errors[] = 'La contraseña es requerida';
    } elseif (strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Las contraseñas no coinciden';
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new administrator
    $stmt = $conn->prepare("INSERT INTO administradores (nombre, apellido, usuario, email, telefono, password, fecha_registro, activo) VALUES (:nombre, :apellido, :usuario, :email, :telefono, :password, NOW(), :activo)");
    
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':activo', $activo);
    
    $stmt->execute();
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Administrador agregado correctamente']);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>