-- Database creation script for 'colegio'

-- Drop database if exists to avoid conflicts
DROP DATABASE IF EXISTS colegio;

-- Create database
CREATE DATABASE colegio;

-- Use the database
USE colegio;

-- Create administrators table
CREATE TABLE administradores (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME,
    activo BOOLEAN DEFAULT TRUE
);

-- Create grades table (grados)
CREATE TABLE grados (
    id_grado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    nivel VARCHAR(50) NOT NULL -- primaria, secundaria, etc.
);

-- Create classrooms table (aulas)
CREATE TABLE aulas (
    id_aula INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL,
    ubicacion VARCHAR(100),
    descripcion TEXT
);

-- Create teachers table (profesores)
CREATE TABLE profesores (
    id_profesor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    direccion VARCHAR(255),
    fecha_nacimiento DATE,
    fecha_contratacion DATE,
    especialidad VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE
);

-- Create courses table (cursos)
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_grado INT,
    id_profesor INT,
    FOREIGN KEY (id_grado) REFERENCES grados(id_grado) ON DELETE SET NULL,
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor) ON DELETE SET NULL
);

-- Create students table (alumnos)
CREATE TABLE alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    fecha_nacimiento DATE,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    id_grado INT,
    fecha_registro DATE DEFAULT (CURRENT_DATE),
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_grado) REFERENCES grados(id_grado) ON DELETE SET NULL
);

-- Create monthly payments table (mensualidades)
CREATE TABLE mensualidades (
    id_mensualidad INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT,
    mes INT NOT NULL,
    anio INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    fecha_pago DATE,
    estado ENUM('pendiente', 'pagado', 'atrasado') DEFAULT 'pendiente',
    metodo_pago VARCHAR(50),
    referencia_pago VARCHAR(100),
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE
);

-- Create schedules table (horarios)
CREATE TABLE horarios (
    id_horario INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT,
    id_aula INT,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE CASCADE,
    FOREIGN KEY (id_aula) REFERENCES aulas(id_aula) ON DELETE SET NULL
);

-- Create student observations table (observaciones)
CREATE TABLE observaciones (
    id_observacion INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT,
    id_profesor INT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('academica', 'disciplinaria', 'asistencia', 'otro') DEFAULT 'otro',
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor) ON DELETE SET NULL
);

-- Create enrollment table (matricula) to connect students with courses
CREATE TABLE matricula (
    id_matricula INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT,
    id_curso INT,
    fecha_matricula DATE DEFAULT (CURRENT_DATE),
    estado ENUM('activo', 'retirado', 'suspendido') DEFAULT 'activo',
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno) ON DELETE CASCADE,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO administradores (nombre, apellido, usuario, password, email) 
VALUES ('Admin', 'System', 'admin', '123456789', 'admin@colegio.com');

-- Insert some example data for testing

-- Insert grades
INSERT INTO grados (nombre, descripcion, nivel) VALUES
('Primero', 'Primer grado de primaria', 'Primaria'),
('Segundo', 'Segundo grado de primaria', 'Primaria'),
('Tercero', 'Tercer grado de primaria', 'Primaria'),
('Primero', 'Primer grado de secundaria', 'Secundaria'),
('Segundo', 'Segundo grado de secundaria', 'Secundaria');

-- Insert classrooms
INSERT INTO aulas (nombre, capacidad, ubicacion) VALUES
('Aula 101', 30, 'Edificio A, Planta 1'),
('Aula 102', 25, 'Edificio A, Planta 1'),
('Aula 201', 35, 'Edificio A, Planta 2'),
('Laboratorio', 20, 'Edificio B, Planta 1'),
('Sala de Computación', 25, 'Edificio B, Planta 2');

-- Insert teachers
INSERT INTO profesores (nombre, apellido, dni, email, telefono, especialidad) VALUES
('Juan', 'Pérez', '12345678A', 'juan.perez@colegio.com', '123456789', 'Matemáticas'),
('María', 'López', '87654321B', 'maria.lopez@colegio.com', '987654321', 'Lengua'),
('Carlos', 'Gómez', '11223344C', 'carlos.gomez@colegio.com', '456789123', 'Ciencias'),
('Ana', 'Martínez', '55667788D', 'ana.martinez@colegio.com', '789123456', 'Historia'),
('Pedro', 'Sánchez', '99887766E', 'pedro.sanchez@colegio.com', '321654987', 'Educación Física');

-- Insert additional administrators
INSERT INTO administradores (nombre, apellido, usuario, password, email, telefono) VALUES
('Laura', 'García', 'laura.garcia', 'password123', 'laura.garcia@colegio.com', '555666777'),
('Roberto', 'Fernández', 'roberto.fernandez', 'password456', 'roberto.fernandez@colegio.com', '888999000'),
('Carmen', 'Rodríguez', 'carmen.rodriguez', 'secure789', 'carmen.rodriguez@colegio.com', '777888999'),
('Javier', 'Moreno', 'javier.moreno', 'secure321', 'javier.moreno@colegio.com', '666777888');

-- Insert students
INSERT INTO alumnos (nombre, apellido, dni, fecha_nacimiento, direccion, telefono, email, id_grado) VALUES
('Miguel', 'Torres', '11111111A', '2010-05-15', 'Calle Principal 123', '111222333', 'miguel.torres@email.com', 1),
('Sofia', 'Ruiz', '22222222B', '2011-03-20', 'Avenida Central 456', '444555666', 'sofia.ruiz@email.com', 2),
('Lucas', 'Fernández', '33333333C', '2010-08-10', 'Plaza Mayor 789', '222333444', 'lucas.fernandez@email.com', 3),
('Valentina', 'García', '44444444D', '2011-11-25', 'Calle Secundaria 321', '555666777', 'valentina.garcia@email.com', 4);

-- Insert courses
INSERT INTO cursos (nombre, descripcion, id_grado, id_profesor) VALUES
('Matemáticas Básicas', 'Curso fundamental de matemáticas', 1, 1),
('Lengua y Literatura', 'Curso de comprensión y expresión', 2, 2),
('Ciencias Naturales', 'Estudio de la naturaleza y sus fenómenos', 3, 3),
('Historia Universal', 'Estudio de eventos históricos mundiales', 4, 4);

-- Insert monthly payments
INSERT INTO mensualidades (id_alumno, mes, anio, monto, fecha_vencimiento, estado) VALUES
(1, 9, 2023, 300.00, '2023-09-10', 'pendiente'),
(2, 9, 2023, 300.00, '2023-09-10', 'pagado'),
(3, 9, 2023, 300.00, '2023-09-10', 'atrasado'),
(4, 9, 2023, 300.00, '2023-09-10', 'pendiente');

-- Insert schedules
INSERT INTO horarios (id_curso, id_aula, dia_semana, hora_inicio, hora_fin) VALUES
(1, 1, 'lunes', '08:00:00', '09:30:00'),
(2, 2, 'martes', '10:00:00', '11:30:00'),
(3, 3, 'miercoles', '08:00:00', '09:30:00'),
(4, 4, 'jueves', '10:00:00', '11:30:00');

-- Insert observations
INSERT INTO observaciones (id_alumno, id_profesor, titulo, descripcion, tipo) VALUES
(1, 1, 'Excelente participación', 'El alumno muestra gran interés en clase', 'academica'),
(2, 2, 'Falta de asistencia', 'No asistió a clase por enfermedad', 'asistencia'),
(3, 3, 'Buen desempeño', 'Muestra mejora significativa en el curso', 'academica'),
(4, 4, 'Participación destacada', 'Contribuye activamente en las discusiones', 'academica');

-- Insert enrollments
INSERT INTO matricula (id_alumno, id_curso, estado) VALUES
(1, 1, 'activo'),
(2, 2, 'activo'),
(3, 3, 'activo'),
(4, 4, 'activo');