CREATE DATABASE IF NOT EXISTS el_molde_errante
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE el_molde_errante;

CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    icono VARCHAR(60) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

INSERT INTO servicios (nombre, descripcion, icono)
SELECT 'Impresión 3D',
       'Fabricación de piezas personalizadas con distintos materiales.',
       'bi-box'
WHERE NOT EXISTS (
    SELECT 1 FROM servicios WHERE nombre = 'Impresión 3D'
);

INSERT INTO servicios (nombre, descripcion, icono)
SELECT 'Diseño 3D',
       'Modelado de piezas listas para impresión.',
       'bi-pencil-square'
WHERE NOT EXISTS (
    SELECT 1 FROM servicios WHERE nombre = 'Diseño 3D'
);

INSERT INTO servicios (nombre, descripcion, icono)
SELECT 'Prototipos',
       'Desarrollo de prototipos para proyectos personales y comerciales.',
       'bi-gear'
WHERE NOT EXISTS (
    SELECT 1 FROM servicios WHERE nombre = 'Prototipos'
);
