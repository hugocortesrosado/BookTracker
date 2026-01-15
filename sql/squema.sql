-- Crear base de datos
CREATE DATABASE IF NOT EXISTS booktracker_db;
USE booktracker_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de libros
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    due_date DATE,
    status ENUM('Pendiente', 'Leyendo', 'Finalizado') DEFAULT 'Pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_user_id ON books(user_id);
CREATE INDEX idx_email ON users(email);

-- Crear usuario para la conexión
CREATE USER 'booktracker_admin'@'localhost' IDENTIFIED BY 'BookTracker@2024';

-- Dar permisos al usuario
GRANT ALL PRIVILEGES ON booktracker_db.* TO 'booktracker_admin'@'localhost';
FLUSH PRIVILEGES;
