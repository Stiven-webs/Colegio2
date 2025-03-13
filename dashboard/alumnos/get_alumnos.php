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
    
    // Query to get all students with their grade information
    $stmt = $conn->prepare("SELECT a.id_alumno, a.nombre, a.apellido, a.dni, a.fecha_nacimiento, 
                           a.direccion, a.telefono, a.email, a.fecha_registro, a.activo,
                           g.nombre AS grado, g.nivel
                           FROM alumnos a
                           LEFT JOIN grados g ON a.id_grado = g.id_grado");
    $stmt->execute();
    
    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate HTML table
    $html = '<div class="alumno-container">';
    $html .= '<h2>Alumnos del Sistema</h2>';
    $html .= '<div class="alumno-actions"><button id="add-alumno-btn" class="btn-add"><i class="fas fa-plus"></i> Agregar Alumno</button></div>';
    $html .= '<table class="alumno-table">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Apellido</th>';
    $html .= '<th>DNI</th>';
    $html .= '<th>Fecha Nacimiento</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Grado</th>';
    $html .= '<th>Fecha Registro</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($alumnos as $alumno) {
        $html .= '<tr>';
        $html .= '<td>' . $alumno['id_alumno'] . '</td>';
        $html .= '<td>' . $alumno['nombre'] . '</td>';
        $html .= '<td>' . $alumno['apellido'] . '</td>';
        $html .= '<td>' . ($alumno['dni'] ? $alumno['dni'] : 'N/A') . '</td>';
        $html .= '<td>' . ($alumno['fecha_nacimiento'] ? date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) : 'N/A') . '</td>';
        $html .= '<td>' . ($alumno['telefono'] ? $alumno['telefono'] : 'N/A') . '</td>';
        $html .= '<td>' . ($alumno['email'] ? $alumno['email'] : 'N/A') . '</td>';
        $html .= '<td>' . ($alumno['grado'] ? $alumno['grado'] . ' - ' . $alumno['nivel'] : 'No asignado') . '</td>';
        $html .= '<td>' . date('d/m/Y', strtotime($alumno['fecha_registro'])) . '</td>';
        $html .= '<td>' . ($alumno['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</td>';
        $html .= '<td class="action-buttons">';
        $html .= '<button class="btn-edit" data-id="' . $alumno['id_alumno'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $alumno['id_alumno'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    // Mobile cards view
    $html .= '<div class="alumno-cards">';
    foreach ($alumnos as $alumno) {
        $html .= '<div class="alumno-card">';
        $html .= '<div class="card-header">';
        $html .= '<div class="card-title">' . $alumno['nombre'] . ' ' . $alumno['apellido'] . '</div>';
        $html .= '<div class="card-actions">';
        $html .= '<button class="btn-edit" data-id="' . $alumno['id_alumno'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $alumno['id_alumno'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<div class="card-item"><span class="item-label">ID:</span> <span class="item-value">' . $alumno['id_alumno'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">DNI:</span> <span class="item-value">' . ($alumno['dni'] ? $alumno['dni'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Fecha Nacimiento:</span> <span class="item-value">' . ($alumno['fecha_nacimiento'] ? date('d/m/Y', strtotime($alumno['fecha_nacimiento'])) : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Teléfono:</span> <span class="item-value">' . ($alumno['telefono'] ? $alumno['telefono'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Email:</span> <span class="item-value">' . ($alumno['email'] ? $alumno['email'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Grado:</span> <span class="item-value">' . ($alumno['grado'] ? $alumno['grado'] . ' - ' . $alumno['nivel'] : 'No asignado') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Fecha Registro:</span> <span class="item-value">' . date('d/m/Y', strtotime($alumno['fecha_registro'])) . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Estado:</span> <span class="item-value">' . ($alumno['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</span></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    // Add CSS styles for the student table
    $html .= '<style>
        .alumno-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .alumno-container h2 {
            color: #002147;
            margin-bottom: 20px;
        }
        .alumno-actions {
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
        .alumno-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        .alumno-table th, .alumno-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .alumno-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .alumno-table tr:hover {
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
        .alumno-cards {
            display: none;
        }
        .alumno-card {
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
            .alumno-table {
                display: none;
            }
            .alumno-cards {
                display: block;
            }
        }
    </style>';
    
    echo $html;
    
} catch(PDOException $e) {
    echo '<div style="color: red; text-align: center; padding: 20px;">Error de base de datos: ' . $e->getMessage() . '</div>';
}
?>