USE appdb;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insertar admin (password: 12345)
INSERT INTO usuarios (username, password, rol) VALUES
('admin',  '$2y$10$0wkl2VjqQFNw2X5e.f3Lj.fliI7Etwk8r7bNW.OkqtGi31E8M6nRC', 'admin');

-- Insertar usuario de prueba (password: 12345)
INSERT INTO usuarios (username, password, rol) VALUES
('usuario1',  '$2y$10$0wkl2VjqQFNw2X5e.f3Lj.fliI7Etwk8r7bNW.OkqtGi31E8M6nRC', 'usuario');

-- Tabla de talleres
CREATE TABLE talleres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cupo_maximo INT NOT NULL,
    cupo_disponible INT NOT NULL
);

INSERT INTO talleres (nombre, descripcion, cupo_maximo, cupo_disponible) VALUES
('Taller de PHP', 'Aprende PHP desde cero con MVC', 5, 5),
('Taller de jQuery', 'DOM, eventos y AJAX', 3, 3),
('Taller de MySQL', 'Bases de datos relacionales', 4, 4);


CREATE TABLE solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    taller_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (taller_id) REFERENCES talleres(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);