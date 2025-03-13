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
    
    // Query to get all administrators
    $stmt = $conn->prepare("SELECT id_admin, nombre, apellido, usuario, email, telefono, fecha_registro, ultimo_acceso, activo FROM administradores");
    $stmt->execute();
    
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate HTML table
    $html = '<div class="admin-container">';
    $html .= '<h2>Administradores del Sistema</h2>';
    $html .= '<div class="admin-actions"><button id="add-admin-btn" class="btn-add"><i class="fas fa-plus"></i> Agregar Administrador</button></div>';
    $html .= '<table class="admin-table">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Nombre</th>';
    $html .= '<th>Apellido</th>';
    $html .= '<th>Usuario</th>';
    $html .= '<th>Email</th>';
    $html .= '<th>Teléfono</th>';
    $html .= '<th>Fecha Registro</th>';
    $html .= '<th>Último Acceso</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Acciones</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($admins as $admin) {
        $html .= '<tr>';
        $html .= '<td>' . $admin['id_admin'] . '</td>';
        $html .= '<td>' . $admin['nombre'] . '</td>';
        $html .= '<td>' . $admin['apellido'] . '</td>';
        $html .= '<td>' . $admin['usuario'] . '</td>';
        $html .= '<td>' . $admin['email'] . '</td>';
        $html .= '<td>' . ($admin['telefono'] ? $admin['telefono'] : 'N/A') . '</td>';
        $html .= '<td>' . $admin['fecha_registro'] . '</td>';
        $html .= '<td>' . ($admin['ultimo_acceso'] ? $admin['ultimo_acceso'] : 'Nunca') . '</td>';
        $html .= '<td>' . ($admin['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</td>';
        $html .= '<td class="action-buttons">';
        $html .= '<button class="btn-edit" data-id="' . $admin['id_admin'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $admin['id_admin'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    // Mobile cards view
    $html .= '<div class="admin-cards">';
    foreach ($admins as $admin) {
        $html .= '<div class="admin-card">';
        $html .= '<div class="card-header">';
        $html .= '<div class="card-title">' . $admin['nombre'] . ' ' . $admin['apellido'] . '</div>';
        $html .= '<div class="card-actions">';
        $html .= '<button class="btn-edit" data-id="' . $admin['id_admin'] . '"><i class="fas fa-edit"></i></button>';
        $html .= '<button class="btn-delete" data-id="' . $admin['id_admin'] . '"><i class="fas fa-trash"></i></button>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="card-body">';
        $html .= '<div class="card-item"><span class="item-label">ID:</span> <span class="item-value">' . $admin['id_admin'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Usuario:</span> <span class="item-value">' . $admin['usuario'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Email:</span> <span class="item-value">' . $admin['email'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Teléfono:</span> <span class="item-value">' . ($admin['telefono'] ? $admin['telefono'] : 'N/A') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Fecha Registro:</span> <span class="item-value">' . $admin['fecha_registro'] . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Último Acceso:</span> <span class="item-value">' . ($admin['ultimo_acceso'] ? $admin['ultimo_acceso'] : 'Nunca') . '</span></div>';
        $html .= '<div class="card-item"><span class="item-label">Estado:</span> <span class="item-value">' . ($admin['activo'] ? '<span class="status-active">Activo</span>' : '<span class="status-inactive">Inactivo</span>') . '</span></div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    // Add CSS styles for the admin table
    $html .= '<style>
        .admin-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .admin-container h2 {
            color: #002147;
            margin-bottom: 20px;
        }
        .admin-actions {
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
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        .admin-table th, .admin-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .admin-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .admin-table tr:hover {
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
            margin-right: 5px;
        }
        .btn-edit {
            color: #2196F3;
        }
        .btn-delete {
            color: #f44336;
        }
        .status-active {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-inactive {
            color: #f44336;
            font-weight: bold;
        }
        
        /* Mobile cards styling */
        .admin-cards {
            display: none;
        }
        .admin-card {
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
            .admin-table {
                display: none;
            }
            .admin-cards {
                display: block;
            }
        }
    </style>';
    
    echo $html;
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>