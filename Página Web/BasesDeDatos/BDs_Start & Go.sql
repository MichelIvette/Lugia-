CREATE DATABASE IF NOT EXISTS pruebas;
USE pruebas;

CREATE TABLE IF NOT EXISTS usuario (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_nombre VARCHAR(255) NOT NULL,
    usuario_clave CHAR(32) NOT NULL,
    UNIQUE KEY unique_nombre (usuario_nombre)
);

INSERT INTO usuario (usuario_nombre, usuario_clave)
VALUES 
    ('admin', MD5('admin')),
    ('empleado', MD5('empleado'));
