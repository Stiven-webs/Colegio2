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

// Get all grades for the dropdown
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT id_grado, nombre, nivel FROM grados ORDER BY nivel, nombre");
    $stmt->execute();
    
    $grados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $grados = [];
}
?>

<!-- Modal HTML Structure -->
<div id="alumno-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Agregar Alumno</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="alumno-form">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input type="text" id="dni" name="dni">
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="id_grado">Grado</label>
                    <select id="id_grado" name="id_grado">
                        <option value="">Seleccionar Grado</option>
                        <?php foreach ($grados as $grado): ?>
                        <option value="<?php echo $grado['id_grado']; ?>"><?php echo $grado['nombre'] . ' - ' . $grado['nivel']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="activo">Estado</label>
                    <select id="activo" name="activo">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" id="cancel-btn" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal CSS Styles -->
<style>
    /* Modal Background */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 500px;
        max-width: 90%;
        animation: modalopen 0.3s;
    }

    @keyframes modalopen {
        from {opacity: 0; transform: translateY(-20px);}
        to {opacity: 1; transform: translateY(0);}
    }

    /* Modal Header */
    .modal-header {
        padding: 15px 20px;
        background-color: #002147;
        color: white;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .close {
        color: white;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    /* Modal Body */
    .modal-body {
        padding: 20px;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #333;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
    }

    .btn-cancel {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .btn-save {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Error Message */
    .error-message {
        color: #f44336;
        font-size: 14px;
        margin-top: 5px;
    }
</style>