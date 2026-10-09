CREATE DATABASE IF NOT EXISTS `utic_tickets_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `utic_tickets_db`;

-- 1. Tabla de Pisos / Ubicaciones
CREATE TABLE IF NOT EXISTS `pisos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pisos` (`id`, `nombre`) VALUES
(1, 'Planta Baja'),
(2, 'Primer Piso'),
(3, 'Tercer Piso');

-- 2. Tabla de Dependencias
CREATE TABLE IF NOT EXISTS `dependencias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `piso_id` INT NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    FOREIGN KEY (`piso_id`) REFERENCES `pisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dependencias` (`piso_id`, `nombre`) VALUES
-- Planta Baja (piso_id = 1)
(1, 'Mesa de Entrada'),
(1, 'Secretaría General'),
(1, 'Administración'),
(1, 'CNE'),
(1, 'R.R.H.H'),
(1, 'Transporte'),
-- 1er Piso (piso_id = 2)
(2, 'Vectoriales'),
(2, 'Planificación'),
(2, 'VENOCD'),
(2, 'DDI'),
(2, 'AULA'),
-- 3er Piso (piso_id = 3)
(3, 'Asesoría Jurídica'),
(3, 'Comunicación'),
(3, 'Control de Fronteras'),
(3, 'Analisis'),
(3, 'Centinela');

-- 3. Tabla de Categorías
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Redes / Internet'),
(2, 'Hardware / Impresoras'),
(3, 'Soporte Sistemas / Software'),
(4, 'Soporte General');

-- 4. Tabla de Usuarios (Panel UTIC)
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nombre_completo` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NULL,
    `activo` TINYINT(1) DEFAULT 1,
    `creado_en` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario admin por defecto (Password: admin123)
INSERT INTO `usuarios` (`username`, `password`, `nombre_completo`, `email`) VALUES
('admin', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.a.c4Y9L9xJ3wX1VqFkKkX7R3S6', 'Administrador UTIC', 'utic@dgvs.gov.py');

-- 5. Tabla de Tickets
CREATE TABLE IF NOT EXISTS `tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_ticket` VARCHAR(20) NOT NULL UNIQUE,
    `solicitante_nombre` VARCHAR(100) NOT NULL,
    `dependencia_id` INT NOT NULL,
    `categoria_id` INT NOT NULL,
    `gravedad` ENUM('Baja', 'Media', 'Alta') DEFAULT 'Media',
    `descripcion` TEXT NOT NULL,
    `estado` ENUM('Nuevo', 'En Proceso', 'Resuelto', 'Cerrado') DEFAULT 'Nuevo',
    `observacion_tecnica` TEXT NULL,
    `tecnico_asignado_id` INT NULL,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `fecha_resolucion` DATETIME NULL,
    FOREIGN KEY (`dependencia_id`) REFERENCES `dependencias`(`id`),
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`),
    FOREIGN KEY (`tecnico_asignado_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_codigo` (`codigo_ticket`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_fecha_creacion` (`fecha_creacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;