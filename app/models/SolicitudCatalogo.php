<?php

class SolicitudCatalogo {
    public function __construct(private PDO $db) {}

    // Mostrar los modelos activos de cuadros y anuarios del inventario.
    public function productos() {
        return $this->db->query("SELECT m.idModelo, m.NombreMaterial, m.Imagen, m.Color,
            m.NivelEducativo, c.NombreCategoria
            FROM modelos m JOIN categorias c ON c.idCategoria = m.idCategoria
            WHERE m.Estado = 1 AND c.NombreCategoria IN ('Cuadros', 'Anuarios')
            ORDER BY c.NombreCategoria, m.NombreMaterial, m.idModelo")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar la solicitud y sus productos en una sola transacción.
    public function registrar($nombre, $colegio, $grado, $cantidades) {
        $productos = array_column($this->productos(), null, 'idModelo');
        $lineas = [];

        foreach ($cantidades as $codigo => $cantidad) {
            if ($cantidad === '' || $cantidad === '0') {
                continue;
            }

            $unidades = filter_var($cantidad, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 999]]);
            if ($unidades === false || !isset($productos[$codigo])) {
                throw new InvalidArgumentException('Revisa los productos y las cantidades de tu solicitud.');
            }
            $lineas[$codigo] = [$productos[$codigo]['NombreMaterial'], $unidades];
        }

        if (!$lineas) {
            throw new InvalidArgumentException('Agrega al menos un producto.');
        }

        $this->db->beginTransaction();
        try {
            $cabecera = $this->db->prepare('INSERT INTO SolicitudesCatalogo (Nombre, Colegio, GradoSeccion) VALUES (?, ?, ?)');
            $cabecera->execute([$nombre, $colegio, $grado === '' ? null : $grado]);
            $id = (int)$this->db->lastInsertId();

            $detalle = $this->db->prepare('INSERT INTO DetalleSolicitudCatalogo (idSolicitud, idModelo, NombreProducto, Cantidad) VALUES (?, ?, ?, ?)');
            foreach ($lineas as $codigo => [$producto, $cantidad]) {
                $detalle->execute([$id, $codigo, $producto, $cantidad]);
            }
            $this->db->commit();
            return ['id' => $id, 'lineas' => $lineas];
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $error;
        }
    }
}
