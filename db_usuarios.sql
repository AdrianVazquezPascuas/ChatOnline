CREATE DATABASE db_usuarios;

USE db_usuarios;

-- Tabla de usuarios
CREATE TABLE tbl_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabla de amigos
CREATE TABLE tbl_amigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES tbl_usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES tbl_usuarios(id) ON DELETE CASCADE
);

-- Tabla de solicitudes de amistad
CREATE TABLE tbl_solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (sender_id) REFERENCES tbl_usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES tbl_usuarios(id) ON DELETE CASCADE
);


drop database db_usuarios;