<?php
session_start();

// Database connection
$host = 'localhost';
$dbname = 'colegio';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    
    // Validate input
    if (empty($usuario) || empty($clave)) {
        header("Location: ../index.php?error=empty_fields");
        exit();
    }
    
    // Query to check user credentials
    $stmt = $conn->prepare("SELECT * FROM administradores WHERE usuario = :usuario AND password = :clave AND activo = 1");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':clave', $clave);
    $stmt->execute();
    
    // Check if user exists
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set session variables
        $_SESSION['user_id'] = $user['id_admin'];
        $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_role'] = 'admin';
        $_SESSION['logged_in'] = true;
        
        // Update last access time
        $updateStmt = $conn->prepare("UPDATE administradores SET ultimo_acceso = NOW() WHERE id_admin = :id");
        $updateStmt->bindParam(':id', $user['id_admin']);
        $updateStmt->execute();
        
        // Redirect to dashboard
        header("Location: ../dashboard/index.php");
        exit();
    } else {
        // Check if it's a teacher
        $stmt = $conn->prepare("SELECT * FROM profesores WHERE email = :usuario AND dni = :clave AND activo = 1");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':clave', $clave);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id_profesor'];
            $_SESSION['user_name'] = $user['nombre'] . ' ' . $user['apellido'];
            $_SESSION['user_role'] = 'profesor';
            $_SESSION['logged_in'] = true;
            
            // Redirect to dashboard
            header("Location: ../dashboard/index.php");
            exit();
        } else {
            // Invalid credentials
            header("Location: ../index.php?error=invalid_credentials");
            exit();
        }
    }
} else {
    // If not POST request, redirect to login page
    header("Location: ../index.php");
    exit();
}