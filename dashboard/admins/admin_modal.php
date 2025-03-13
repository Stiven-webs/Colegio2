<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}
?>

<!-- Modal HTML Structure -->
<div id="admin-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Agregar Administrador</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="admin-form">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
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
        font-size: 20px;
    }

    /* Close Button */
    .close {
        color: white;
        font-size: 28px;
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
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #002147;
    }

    /* Form Actions */
    .form-actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-save {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-cancel {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    /* Error Message */
    .error-message {
        color: #f44336;
        margin-top: 5px;
        font-size: 12px;
    }

    /* Success Message */
    .success-message {
        color: #4CAF50;
        margin-top: 5px;
        font-size: 12px;
    }

    /* Password Note */
    .password-note {
        color: #666;
        margin-top: 5px;
        font-size: 12px;
        font-style: italic;
    }

    /* Responsive Adjustments */
    @media (max-width: 600px) {
        .modal-content {
            width: 95%;
            margin: 10% auto;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-save, .btn-cancel {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>