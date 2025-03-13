<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
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
    
    // Query to get all teachers
    $stmt = $conn->prepare("SELECT id_profesor, nombre, apellido, dni, fecha_nacimiento, 
                           direccion, telefono, email, fecha_contratacion, especialidad, activo
                           FROM profesores");
    $stmt->execute();
    
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate HTML table
    $html = '<div class="profesor-container">';
    $html .= '<h2>Profesores del Sistema</h2>';
    $html .= '<div class="profesor-actions"><button id="add-profesor-btn" class="btn-add"><i class="fas fa-plus"></i> Agregar Profesor</button></div>';
    $html .= '<table class="profesor-table">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Apellido</th>';
    $html .= '<th>DNI</th>';
    $html .= '<th>Fecha Nacimiento</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Especialidad</th>';
    $html .= '<th>Fecha Contratación</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($profesores as $profesor) {
        $html .= '<tr>';
        $html .= '<td>' . $profesor['id_profesor'] . '</td>';
        $html .= '<td>' . $profesor['nombre'] . '</td>';
        $html .= '<td>' . $profesor['apellido'] . '</td>';
        $html .= '<td>' . ($profesor['dni'] ? $profesor['dni'] : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['fecha_nacimiento'] ? date('d/m/Y', strtotime($profesor['fecha_nacimiento'])) : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['telefono'] ? $profesor['telefono'] : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['email'] ? $profesor['email'] : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['especialidad'] ? $profesor['especialidad'] : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['fecha_contratacion'] ? date('d/m/Y', strtotime($profesor['fecha_contratacion'])) : 'N/A') . '</td>';
        $html .= '<td>' . ($profesor['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</td>';
        $html .= '<td class="action-buttons">';
        $html .= '<button class="btn-edit" data-id="' . $profesor['id_profesor'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $profesor['id_profesor'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    // Mobile cards view
    $html .= '<div class="profesor-cards">';
    foreach ($profesores as $profesor) {
        $html .= '<div class="profesor-card">';
        $html .= '<div class="card-header">';
        $html .= '<div class="card-title">' . $profesor['nombre'] . ' ' . $profesor['apellido'] . '</div>';
        $html .= '<div class="card-actions">';
        $html .= '<button class="btn-edit" data-id="' . $profesor['id_profesor'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $profesor['id_profesor'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<div class="card-item"><span class="item-label">ID:</span> <span class="item-value">' . $profesor['id_profesor'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">DNI:</span> <span class="item-value">' . ($profesor['dni'] ? $profesor['dni'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Fecha Nacimiento:</span> <span class="item-value">' . ($profesor['fecha_nacimiento'] ? date('d/m/Y', strtotime($profesor['fecha_nacimiento'])) : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Teléfono:</span> <span class="item-value">' . ($profesor['telefono'] ? $profesor['telefono'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Email:</span> <span class="item-value">' . ($profesor['email'] ? $profesor['email'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Especialidad:</span> <span class="item-value">' . ($profesor['especialidad'] ? $profesor['especialidad'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Fecha Contratación:</span> <span class="item-value">' . ($profesor['fecha_contratacion'] ? date('d/m/Y', strtotime($profesor['fecha_contratacion'])) : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Estado:</span> <span class="item-value">' . ($profesor['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</span></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    // Add CSS styles for the teacher table
    $html .= '<style>
        .profesor-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .profesor-container h2 {
            color: #002147;
            margin-bottom: 20px;
        }
        .profesor-actions {
            margin-bottom: 15px;
            text-align: right;
        }
        .btn-add {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .profesor-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        .profesor-table th, .profesor-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .profesor-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .profesor-table tr:hover {
            background-color: #f5f5f5;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .btn-edit, .btn-delete {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 14px;
        }
        .btn-edit {
            color: #2196F3;
        }
        .btn-delete {
            color: #F44336;
        }
        .status-active {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-inactive {
            color: #F44336;
            font-weight: bold;
        }
        
        /* Mobile cards styling */
        .profesor-cards {
            display: none;
        }
        .profesor-card {
            background-color: #002147;
            color: white;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background-color: #002147;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card-title {
            font-weight: bold;
            font-size: 16px;
        }
        .card-actions {
            display: flex;
        }
        .card-actions .btn-edit,
        .card-actions .btn-delete {
            color: white;
            margin-left: 8px;
        }
        .card-body {
            padding: 15px;
            background-color: white;
            color: #333;
        }
        .card-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .card-item:last-child {
            border-bottom: none;
        }
        .item-label {
            font-weight: bold;
            color: #002147;
        }
        
        /* Media queries for responsive design */
        @media screen and (max-width: 768px) {
            .profesor-table {
                display: none;
            }
            .profesor-cards {
                display: block;
            }
        }
    </style>';
    
    echo $html;
    
} catch(PDOException $e) {
    echo '<div style="color: red; text-align: center; padding: 20px;">Error de base de datos: ' . $e->getMessage() . '</div>';
}
?>
