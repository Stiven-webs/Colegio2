<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit();
}

// Get user information
$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Escolar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #002147;
            color: white;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            background-color: #001a38;
            border-bottom: 1px solid #003366;
        }

        .school-info {
            display: flex;
            align-items: center;
        }

        .school-logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .school-logo img {
            width: 30px;
            height: 30px;
        }

        .school-name {
            font-size: 14px;
            line-height: 1.2;
        }

        .school-name .main-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .school-name .sub-name {
            font-size: 12px;
        }

        .user-info {
            padding: 15px 20px;
            border-bottom: 1px solid #003366;
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: #ff5252;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            font-weight: bold;
        }

        .user-name {
            font-size: 14px;
            font-weight: bold;
        }

        .user-status {
            font-size: 12px;
            display: flex;
            align-items: center;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background-color: #4CAF50;
            border-radius: 50%;
            margin-right: 5px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            border-bottom: 1px solid #003366;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: #003366;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background-color: #002147;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar {
            flex: 1;
            margin: 0 20px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            font-size: 14px;
        }

        .search-bar i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .search-bar input {
            padding-left: 30px;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
        }

        .action-icon {
            color: white;
            margin-left: 15px;
            font-size: 18px;
            cursor: pointer;
        }

        .year-display {
            font-size: 20px;
            font-weight: bold;
            margin-right: 20px;
        }

        .content-area {
            flex: 1;
            padding: 20px;
            background-color: #e9ecef;
        }

        .welcome-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-card h2 {
            color: #002147;
            margin-bottom: 10px;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
        }

        /* Mobile Card Layout */
        .mobile-cards {
            display: none;
        }

        .card {
            background-color: #002147;
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .card-title {
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Logout Button Styles */
        .logout-btn {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background-color: #001a38;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        .logout-btn i {
            margin-right: 5px;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                display: none;
            }

            .mobile-menu {
                display: block;
                background-color: #002147;
                padding: 15px;
            }

            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: white;
                margin-bottom: 15px;
            }

            .mobile-school-info {
                display: flex;
                align-items: center;
            }

            .mobile-school-logo {
                width: 30px;
                height: 30px;
                background-color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 10px;
            }

            .mobile-school-name {
                font-size: 14px;
                font-weight: bold;
            }

            .mobile-cards {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 20px;
            }

            .top-bar {
                display: none;
            }

            .content-area {
                padding: 0;
            }

            .welcome-card {
                display: none;
            }

            .logout-btn {
                width: 100%;
                padding: 12px;
                background-color: #ff5252;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                margin-top: 15px;
                transition: background-color 0.3s;
            }

            .logout-btn:hover {
                background-color: #ff3333;
            }

            .logout-btn i {
                margin-right: 8px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar for Desktop -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="school-info">
                    <div class="school-logo">
                        <img src="../assets/images/user-icon.png" alt="School Logo">
                    </div>
                    <div class="school-name">
                        <div class="main-name">INSTITUCIÓN EDUCATIVA</div>
                        <div class="sub-name">Jhoan's Schools</div>
                        <div class="sub-name">SEDE SAN MIGUEL</div>
                    </div>
                </div>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <span>M</span>
                </div>
                <div>
                    <div class="user-name"><?php echo $userName; ?></div>
                    <div class="user-status">
                        <span class="status-indicator"></span>
                        <span>Online</span>
                    </div>
                </div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link" id="admin-link">
                        <i class="fas fa-user-shield"></i>
                        <span>Administradores</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>Alumnos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Profesores</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../auth/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <div class="mobile-header">
                <div class="mobile-school-info">
                    <div class="mobile-school-logo">
                        <img src="../assets/images/user-icon.png" alt="School Logo" style="width: 20px; height: 20px;">
                    </div>
                    <div class="mobile-school-name">
                        INSTITUCIÓN EDUCATIVA<br>
                        <small>Jhoan's Schools</small>
                    </div>
                </div>
                <div class="mobile-year">2025</div>
            </div>
            <a href="../auth/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top Bar for Desktop -->
            <div class="top-bar">
                <div class="system-name">SISTEMA</div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
                <div class="top-bar-actions">
                    <div class="year-display">2025</div>
                    <i class="fas fa-plus-circle action-icon"></i>
                    <i class="fas fa-bell action-icon"></i>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <div class="welcome-card">
                    <h2>Bienvenido, <?php echo $userName; ?></h2>
                    <p>Seleccione una opción del menú para comenzar.</p>
                </div>
                
                <!-- Admin Content will be loaded here -->
                <div id="admin-content" style="display: none;"></div>
                
                <!-- Other content sections will be added here -->
            </div>
        </div>
    </div>
    
    <!-- Load Admin Modal -->
    <div id="modal-container"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Admin link click handler
            document.getElementById('admin-link').addEventListener('click', function(e) {
                e.preventDefault();
                loadAdminContent();
            });
            
            // Alumnos link click handler
            const alumnosLink = document.querySelector('.nav-link:has(i.fas.fa-user-graduate)');
            if (alumnosLink) {
                alumnosLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadAlumnosContent();
                });
            }
            
            // Profesores link click handler
            const profesoresLink = document.querySelector('.nav-link:has(i.fas.fa-chalkboard-teacher)');
            if (profesoresLink) {
                profesoresLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadProfesoresContent();
                });
            }
            
            // Add event listeners to all other navigation links
            const navLinks = document.querySelectorAll('.nav-link:not(#admin-link):not([href="../auth/logout.php"]):not(:has(i.fas.fa-user-graduate)):not(:has(i.fas.fa-chalkboard-teacher))');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideWelcomeCard();
                    // Here you would load the specific content for each section
                    // For now, we'll just show a message
                    showContentMessage(this.querySelector('span').textContent);
                });
            });
            
            // Function to hide welcome card
            function hideWelcomeCard() {
                const welcomeCard = document.querySelector('.welcome-card');
                welcomeCard.style.display = 'none';
            }
            
            // Function to load profesores content
            function loadProfesoresContent() {
                const adminContent = document.getElementById('admin-content');
                
                // Hide welcome card
                hideWelcomeCard();
                
                // Show loading state
                adminContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
                adminContent.style.display = 'block';
                
                // Fetch profesores data
                fetch('profesores/get_profesores.php')
                    .then(response => response.text())
                    .then(html => {
                        adminContent.innerHTML = html;
                        
                        // Add event listener to the Add Profesor button
                        const addProfesorBtn = document.getElementById('add-profesor-btn');
                        if (addProfesorBtn) {
                            addProfesorBtn.addEventListener('click', openProfesorModal);
                        }
                        
                        // Add event listeners to edit and delete buttons
                        const editButtons = document.querySelectorAll('.btn-edit');
                        const deleteButtons = document.querySelectorAll('.btn-delete');
                        
                        editButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const profesorId = this.getAttribute('data-id');
                                openEditProfesorModal(profesorId);
                            });
                        });
                        
                        deleteButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const profesorId = this.getAttribute('data-id');
                                confirmDeleteProfesor(profesorId);
                            });
                        });
                    })
                    .catch(error => {
                        adminContent.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Error al cargar los datos. Por favor, intente de nuevo.</div>';
                        console.error('Error loading profesores content:', error);
                    });
            }
            
            // Function to open profesor modal
            function openProfesorModal() {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch modal content
                fetch('profesores/profesor_modal.php')
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        // Show the modal
                        const modal = document.getElementById('profesor-modal');
                        modal.style.display = 'block';
                        
                        // Add event listeners for modal actions
                        const closeBtn = modal.querySelector('.close');
                        const cancelBtn = document.getElementById('cancel-btn');
                        const form = document.getElementById('profesor-form');
                        
                        closeBtn.addEventListener('click', closeModal);
                        cancelBtn.addEventListener('click', closeModal);
                        form.addEventListener('submit', submitProfesorForm);
                        
                        // Close modal when clicking outside
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading modal:', error);
                    });
            }
            
            // Function to open edit profesor modal
            function openEditProfesorModal(profesorId) {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch modal content
                fetch('profesores/profesor_modal.php')
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        // Show the modal
                        const modal = document.getElementById('profesor-modal');
                        modal.style.display = 'block';
                        
                        // Change modal title
                        modal.querySelector('.modal-header h2').textContent = 'Editar Profesor';
                        
                        // Add event listeners for modal actions
                        const closeBtn = modal.querySelector('.close');
                        const cancelBtn = document.getElementById('cancel-btn');
                        const form = document.getElementById('profesor-form');
                        
                        closeBtn.addEventListener('click', closeModal);
                        cancelBtn.addEventListener('click', closeModal);
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitProfesorForm(e, profesorId);
                        });
                        
                        // Close modal when clicking outside
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                        
                        // Fetch profesor data and fill the form
                        fetch(`profesores/get_profesor.php?id_profesor=${profesorId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const profesor = data.profesor;
                                    document.getElementById('nombre').value = profesor.nombre;
                                    document.getElementById('apellido').value = profesor.apellido;
                                    document.getElementById('dni').value = profesor.dni || '';
                                    document.getElementById('fecha_nacimiento').value = profesor.fecha_nacimiento || '';
                                    document.getElementById('direccion').value = profesor.direccion || '';
                                    document.getElementById('telefono').value = profesor.telefono || '';
                                    document.getElementById('email').value = profesor.email || '';
                                    document.getElementById('fecha_contratacion').value = profesor.fecha_contratacion || '';
                                    document.getElementById('especialidad').value = profesor.especialidad || '';
                                    document.getElementById('activo').value = profesor.activo;
                                } else {
                                    alert('Error al cargar los datos del profesor');
                                    closeModal();
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error al cargar los datos del profesor');
                                closeModal();
                            });
                    })
                    .catch(error => {
                        console.error('Error loading modal:', error);
                    });
            }
            
            // Function to submit profesor form
            function submitProfesorForm(e, profesorId = null) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const url = profesorId ? 'profesores/update_profesor.php' : 'profesores/add_profesor.php';
                
                if (profesorId) {
                    formData.append('id_profesor', profesorId);
                }
                
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        loadProfesoresContent();
                    } else {
                        alert(data.errors ? data.errors.join('\n') : 'Error al guardar los datos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar los datos');
                });
            }
            
            // Function to confirm and delete profesor
            function confirmDeleteProfesor(profesorId) {
                if (confirm('¿Está seguro de que desea eliminar este profesor?')) {
                    const formData = new FormData();
                    formData.append('id_profesor', profesorId);
                    
                    fetch('profesores/delete_profesor.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadProfesoresContent();
                        } else {
                            alert(data.error || 'Error al eliminar el profesor');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el profesor');
                    });
                }
            }
            
            // Function to show content message for sections not yet implemented
            function showContentMessage(sectionName) {
                const adminContent = document.getElementById('admin-content');
                adminContent.innerHTML = `<div style="text-align: center; padding: 20px;">
                    <h2>Sección de ${sectionName}</h2>
                    <p>Esta sección está en desarrollo.</p>
                </div>`;
                adminContent.style.display = 'block';
            }
            
            // Function to load admin content
            function loadAdminContent() {
                const adminContent = document.getElementById('admin-content');
                
                // Hide welcome card
                hideWelcomeCard();
                
                // Show loading state
                adminContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
                adminContent.style.display = 'block';
                
                // Fetch admin data
                fetch('admins/get_admins.php')
                    .then(response => response.text())
                    .then(html => {
                        adminContent.innerHTML = html;
                        
                        // Add event listener to the Add Admin button
                        const addAdminBtn = document.getElementById('add-admin-btn');
                        if (addAdminBtn) {
                            addAdminBtn.addEventListener('click', openAdminModal);
                        }
                        
                        // Add event listeners to edit and delete buttons
                        const editButtons = document.querySelectorAll('.btn-edit');
                        const deleteButtons = document.querySelectorAll('.btn-delete');
                        
                        editButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const adminId = this.getAttribute('data-id');
                                openEditModal(adminId);
                            });
                        });
                        
                        deleteButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const adminId = this.getAttribute('data-id');
                                confirmDeleteAdmin(adminId);
                            });
                        });
                    })
                    .catch(error => {
                        adminContent.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Error al cargar los datos. Por favor, intente de nuevo.</div>';
                        console.error('Error loading admin content:', error);
                    });
            }
            
            // Function to load alumnos content
            function loadAlumnosContent() {
                const adminContent = document.getElementById('admin-content');
                
                // Hide welcome card
                hideWelcomeCard();
                
                // Show loading state
                adminContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
                adminContent.style.display = 'block';
                
                // Fetch alumnos data
                fetch('alumnos/get_alumnos.php')
                    .then(response => response.text())
                    .then(html => {
                        adminContent.innerHTML = html;
                        
                        // Add event listener to the Add Alumno button
                        const addAlumnoBtn = document.getElementById('add-alumno-btn');
                        if (addAlumnoBtn) {
                            addAlumnoBtn.addEventListener('click', openAlumnoModal);
                        }
                        
                        // Add event listeners to edit and delete buttons
                        const editButtons = document.querySelectorAll('.btn-edit');
                        const deleteButtons = document.querySelectorAll('.btn-delete');
                        
                        editButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const alumnoId = this.getAttribute('data-id');
                                openEditAlumnoModal(alumnoId);
                            });
                        });
                        
                        deleteButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const alumnoId = this.getAttribute('data-id');
                                confirmDeleteAlumno(alumnoId);
                            });
                        });
                    })
                    .catch(error => {
                        adminContent.innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Error al cargar los datos. Por favor, intente de nuevo.</div>';
                        console.error('Error loading alumnos content:', error);
                    });
            }
            
            // Function to open alumno modal
            function openAlumnoModal() {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch modal content
                fetch('alumnos/alumno_modal.php')
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        // Show the modal
                        const modal = document.getElementById('alumno-modal');
                        modal.style.display = 'block';
                        
                        // Add event listeners for modal actions
                        const closeBtn = modal.querySelector('.close');
                        const cancelBtn = document.getElementById('cancel-btn');
                        const form = document.getElementById('alumno-form');
                        
                        closeBtn.addEventListener('click', closeModal);
                        cancelBtn.addEventListener('click', closeModal);
                        form.addEventListener('submit', submitAlumnoForm);
                        
                        // Close modal when clicking outside
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading modal:', error);
                    });
            }
            
            // Function to open edit alumno modal
            function openEditAlumnoModal(alumnoId) {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch modal content
                fetch('alumnos/alumno_modal.php')
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        // Show the modal
                        const modal = document.getElementById('alumno-modal');
                        modal.style.display = 'block';
                        
                        // Change modal title
                        modal.querySelector('.modal-header h2').textContent = 'Editar Alumno';
                        
                        // Add event listeners for modal actions
                        const closeBtn = modal.querySelector('.close');
                        const cancelBtn = document.getElementById('cancel-btn');
                        const form = document.getElementById('alumno-form');
                        
                        closeBtn.addEventListener('click', closeModal);
                        cancelBtn.addEventListener('click', closeModal);
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            updateAlumno(alumnoId);
                        });
                        
                        // Close modal when clicking outside
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                        
                        // Fetch alumno data and populate form
                        fetch(`alumnos/get_alumno.php?id_alumno=${alumnoId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const alumno = data.alumno;
                                    document.getElementById('nombre').value = alumno.nombre;
                                    document.getElementById('apellido').value = alumno.apellido;
                                    document.getElementById('dni').value = alumno.dni || '';
                                    document.getElementById('fecha_nacimiento').value = alumno.fecha_nacimiento || '';
                                    document.getElementById('direccion').value = alumno.direccion || '';
                                    document.getElementById('telefono').value = alumno.telefono || '';
                                    document.getElementById('email').value = alumno.email || '';
                                    
                                    const gradoSelect = document.getElementById('id_grado');
                                    if (alumno.id_grado) {
                                        gradoSelect.value = alumno.id_grado;
                                    }
                                    
                                    document.getElementById('activo').value = alumno.activo;
                                } else {
                                    alert('Error al cargar los datos del alumno: ' + data.error);
                                    closeModal();
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching alumno data:', error);
                                alert('Error al cargar los datos del alumno');
                                closeModal();
                            });
                    })
                    .catch(error => {
                        console.error('Error loading modal:', error);
                    });
            }
            
            // Function to submit alumno form
            function submitAlumnoForm(e) {
                e.preventDefault();
                
                const form = document.getElementById('alumno-form');
                const formData = new FormData(form);
                
                fetch('alumnos/add_alumno.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        loadAlumnosContent(); // Reload the alumnos table
                    } else {
                        let errorMessage = 'Error al agregar alumno:';
                        if (data.errors && data.errors.length > 0) {
                            errorMessage += '<ul>';
                            data.errors.forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                            errorMessage += '</ul>';
                        }
                        
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message';
                        errorDiv.innerHTML = errorMessage;
                        
                        // Remove any existing error message
                        const existingError = form.querySelector('.error-message');
                        if (existingError) {
                            existingError.remove();
                        }
                        
                        form.insertBefore(errorDiv, form.firstChild);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Error al procesar la solicitud');
                });
            }
            
            // Function to update alumno
            function updateAlumno(alumnoId) {
                const form = document.getElementById('alumno-form');
                const formData = new FormData(form);
                formData.append('id_alumno', alumnoId);
                
                fetch('alumnos/update_alumno.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        loadAlumnosContent(); // Reload the alumnos table
                    } else {
                        let errorMessage = 'Error al actualizar alumno:';
                        if (data.errors && data.errors.length > 0) {
                            errorMessage += '<ul>';
                            data.errors.forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                            errorMessage += '</ul>';
                        }
                        
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message';
                        errorDiv.innerHTML = errorMessage;
                        
                        // Remove any existing error message
                        const existingError = form.querySelector('.error-message');
                        if (existingError) {
                            existingError.remove();
                        }
                        
                        form.insertBefore(errorDiv, form.firstChild);
                    }
                })
                .catch(error => {
                    console.error('Error updating alumno:', error);
                    alert('Error al procesar la solicitud');
                });
            }
            
            // Function to confirm delete alumno
            function confirmDeleteAlumno(alumnoId) {
                if (confirm('¿Está seguro de que desea eliminar este alumno? Esta acción no se puede deshacer.')) {
                    const formData = new FormData();
                    formData.append('id_alumno', alumnoId);
                    
                    fetch('alumnos/delete_alumno.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadAlumnosContent(); // Reload the alumnos table
                        } else {
                            alert('Error al eliminar alumno: ' + (data.error || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting alumno:', error);
                        alert('Error al procesar la solicitud');
                    });
                }
            }
            
            // Function to close modal
            function closeModal() {
                const adminModal = document.getElementById('admin-modal');
                const alumnoModal = document.getElementById('alumno-modal');
                const profesorModal = document.getElementById('profesor-modal');
                
                if (adminModal) {
                    adminModal.style.display = 'none';
                }
                
                if (alumnoModal) {
                    alumnoModal.style.display = 'none';
                }

                if (profesorModal) {
                    profesorModal.style.display = 'none';
                }
            }
            
            // Function to open admin modal
            function openAdminModal() {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch modal content
                fetch('admins/admin_modal.php')
                    .then(response => response.text())
                    .then(html => {
                        modalContainer.innerHTML = html;
                        
                        const modal = document.getElementById('admin-modal');
                        const closeBtn = modal.querySelector('.close');
                        const cancelBtn = document.getElementById('cancel-btn');
                        const adminForm = document.getElementById('admin-form');
                        
                        // Show modal
                        modal.style.display = 'block';
                        
                        // Close modal when clicking the close button
                        closeBtn.addEventListener('click', closeModal);
                        
                        // Close modal when clicking the cancel button
                        cancelBtn.addEventListener('click', closeModal);
                        
                        // Close modal when clicking outside the modal content
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                closeModal();
                            }
                        });
                        
                        // Handle form submission
                        adminForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            submitAdminForm();
                        });
                    })
                    .catch(error => {
                        console.error('Error loading modal:', error);
                    });
            }
            
            // This duplicate closeModal function has been removed and consolidated with the one above
            
            // Function to submit admin form
            function submitAdminForm() {
                const adminForm = document.getElementById('admin-form');
                const formData = new FormData(adminForm);
                
                // Clear previous error messages
                const errorElements = adminForm.querySelectorAll('.error-message');
                errorElements.forEach(element => element.remove());
                
                // Get the form action URL (add_admin.php or update_admin.php)
                const actionUrl = adminForm.getAttribute('data-action') || 'admins/add_admin.php';
                
                // Submit form data via AJAX
                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        
                        // Close modal
                        closeModal();
                        
                        // Reload admin content to show the new/updated admin
                        loadAdminContent();
                    } else {
                        // Show error messages
                        if (data.errors) {
                            data.errors.forEach(error => {
                                // Find the related input field
                                const errorField = error.toLowerCase().includes('nombre') ? 'nombre' :
                                                error.toLowerCase().includes('apellido') ? 'apellido' :
                                                error.toLowerCase().includes('usuario') ? 'usuario' :
                                                error.toLowerCase().includes('email') ? 'email' :
                                                error.toLowerCase().includes('teléfono') ? 'telefono' :
                                                error.toLowerCase().includes('contraseña') ? 'password' : null;
                                
                                if (errorField) {
                                    const inputField = document.getElementById(errorField);
                                    const errorElement = document.createElement('div');
                                    errorElement.className = 'error-message';
                                    errorElement.textContent = error;
                                    inputField.parentNode.appendChild(errorElement);
                                }
                            });
                        } else if (data.error) {
                            alert('Error: ' + data.error);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Error al enviar el formulario. Por favor, intente de nuevo.');
                });
            }
            
            // Function to open edit modal
            function openEditModal(adminId) {
                const modalContainer = document.getElementById('modal-container');
                
                // Fetch admin data
                fetch(`admins/get_admin.php?id_admin=${adminId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fetch modal content
                            fetch('admins/admin_modal.php')
                                .then(response => response.text())
                                .then(html => {
                                    modalContainer.innerHTML = html;
                                    
                                    const modal = document.getElementById('admin-modal');
                                    const modalTitle = modal.querySelector('.modal-header h2');
                                    const adminForm = document.getElementById('admin-form');
                                    const closeBtn = modal.querySelector('.close');
                                    const cancelBtn = document.getElementById('cancel-btn');
                                    
                                    // Change modal title
                                    modalTitle.textContent = 'Editar Administrador';
                                    
                                    // Set form action for update
                                    adminForm.setAttribute('data-action', 'admins/update_admin.php');
                                    
                                    // Add hidden input for admin ID
                                    const idInput = document.createElement('input');
                                    idInput.type = 'hidden';
                                    idInput.name = 'id_admin';
                                    idInput.value = adminId;
                                    adminForm.appendChild(idInput);
                                    
                                    // Fill form with admin data
                                    document.getElementById('nombre').value = data.admin.nombre;
                                    document.getElementById('apellido').value = data.admin.apellido;
                                    document.getElementById('usuario').value = data.admin.usuario;
                                    document.getElementById('email').value = data.admin.email;
                                    document.getElementById('telefono').value = data.admin.telefono || '';
                                    document.getElementById('activo').value = data.admin.activo;
                                    
                                    // Make password fields optional for editing
                                    const passwordField = document.getElementById('password');
                                    const confirmPasswordField = document.getElementById('confirm_password');
                                    passwordField.removeAttribute('required');
                                    confirmPasswordField.removeAttribute('required');
                                    
                                    // Add note about password
                                    const passwordNote = document.createElement('div');
                                    passwordNote.className = 'password-note';
                                    passwordNote.textContent = 'Dejar en blanco para mantener la contraseña actual';
                                    passwordField.parentNode.appendChild(passwordNote);
                                    
                                    // Show modal
                                    modal.style.display = 'block';
                                    
                                    // Close modal when clicking the close button
                                    closeBtn.addEventListener('click', closeModal);
                                    
                                    // Close modal when clicking the cancel button
                                    cancelBtn.addEventListener('click', closeModal);
                                    
                                    // Close modal when clicking outside the modal content
                                    window.addEventListener('click', function(event) {
                                        if (event.target === modal) {
                                            closeModal();
                                        }
                                    });
                                    
                                    // Handle form submission
                                    adminForm.addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        submitAdminForm();
                                    });
                                })
                                .catch(error => {
                                    console.error('Error loading modal:', error);
                                    alert('Error al cargar el formulario. Por favor, intente de nuevo.');
                                });
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching admin data:', error);
                        alert('Error al obtener datos del administrador. Por favor, intente de nuevo.');
                    });
            }
            
            // Function to confirm and delete admin
            function confirmDeleteAdmin(adminId) {
                if (confirm('¿Está seguro de que desea eliminar este administrador?')) {
                    const formData = new FormData();
                    formData.append('id_admin', adminId);
                    
                    fetch('admins/delete_admin.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            loadAdminContent(); // Reload the admin list
                        } else {
                            alert('Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting admin:', error);
                        alert('Error al eliminar el administrador. Por favor, intente de nuevo.');
                    });
                }
            }
        });
    </script>
</body>
</html>

<!-- Footer
            <div class="footer">
        <p>© Todos los derechos reservados al grupo - 05</p>
    </div>
    -->
</body>
</html>
