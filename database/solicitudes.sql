-- Ejecutar sobre la misma base que usa el panel de ISHUME.

-- Datos del cliente y estado de la solicitud.
CREATE TABLE IF NOT EXISTS SolicitudesCatalogo (
    idSolicitud INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Colegio VARCHAR(150) NOT NULL,
    GradoSeccion VARCHAR(80) NULL,
    Fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Estado ENUM('PENDIENTE', 'APROBADA', 'DESCARTADA') NOT NULL DEFAULT 'PENDIENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Productos y cantidades elegidos desde el catalogo.
CREATE TABLE IF NOT EXISTS DetalleSolicitudCatalogo (
    idSolicitud INT UNSIGNED NOT NULL,
    idModelo CHAR(7) NOT NULL,
    NombreProducto VARCHAR(100) NOT NULL,
    Cantidad INT UNSIGNED NOT NULL CHECK (Cantidad BETWEEN 1 AND 999),
    PRIMARY KEY (idSolicitud, idModelo),
    FOREIGN KEY (idSolicitud) REFERENCES SolicitudesCatalogo(idSolicitud),
    FOREIGN KEY (idModelo) REFERENCES modelos(idModelo)
) ENGINE=InnoDB;
