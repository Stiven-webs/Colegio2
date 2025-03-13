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
    $id_admin = (int)$_POST['id_admin'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
    $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    
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
        // Check if username already exists (excluding current admin)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM administradores WHERE usuario = :usuario AND id_admin != :id_admin");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':id_admin', $id_admin);
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
        // Check if email already exists (excluding current admin)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM administradores WHERE email = :email AND id_admin != :id_admin");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_admin', $id_admin);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'El email ya está registrado';
        }
    }
    
    // Password validation only if a new password is provided
    if ($password !== null) {
        if (strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Las contraseñas no coinciden';
        }
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }
    
    // Prepare update query
    if ($password !== null) {
        // Update with new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE administradores SET nombre = :nombre, apellido = :apellido, usuario = :usuario, email = :email, telefono = :telefono, password = :password, activo = :activo WHERE id_admin = :id_admin");
        $stmt->bindParam(':password', $hashed_password);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE administradores SET nombre = :nombre, apellido = :apellido, usuario = :usuario, email = :email, telefono = :telefono, activo = :activo WHERE id_admin = :id_admin");
    }
    
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':activo', $activo);
    $stmt->bindParam(':id_admin', $id_admin);
    
    $stmt->execute();
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Administrador actualizado correctamente']);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>