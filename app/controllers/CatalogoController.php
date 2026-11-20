<?php

class CatalogoController {

    public function mostrar(string $whatsapp): void {

        // Sesión y datos que necesita el catálogo público.
        session_start();
        $_SESSION['token_catalogo'] ??= bin2hex(random_bytes(32));
        $modelo = new SolicitudCatalogo(Conexion::abrir());
        $errorCatalogo = '';
        $datos = [];

        // Validar los datos del cliente y guardar los productos de su solicitud.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $token = $_POST['token'] ?? '';
                if (!is_string($token) || !hash_equals($_SESSION['token_catalogo'], $token)) {
                    throw new InvalidArgumentException('El formulario venció. Actualiza la página y vuelve a intentarlo.');
                }

                foreach (['nombre' => 100, 'colegio' => 150, 'grado' => 80] as $campo => $limite) {
                    $valor = $_POST[$campo] ?? '';
                    $datos[$campo] = is_string($valor) ? trim($valor) : '';

                    if (mb_strlen($datos[$campo]) > $limite || ($campo !== 'grado' && $datos[$campo] === '')) {
                        throw new InvalidArgumentException('Revisa tu nombre, el colegio y el grado o sección.');
                    }
                }

                $cantidades = $_POST['cantidad'] ?? [];
                if (!is_array($cantidades) || count($cantidades) > 500) {
                    throw new InvalidArgumentException('Revisa las cantidades del carrito.');
                }

                $registro = $modelo->registrar($datos['nombre'], $datos['colegio'], $datos['grado'], $cantidades);

                // Preparar el aviso de WhatsApp con la solicitud que ya quedó guardada.
                $codigo = 'COT-' . str_pad((string)$registro['id'], 5, '0', STR_PAD_LEFT);
                $mensaje = ["Hola, envié la solicitud {$codigo}.", 'Nombre: ' . $datos['nombre'], 'Colegio: ' . $datos['colegio']];

                if ($datos['grado'] !== '') {
                    $mensaje[] = 'Grado y sección: ' . $datos['grado'];
                }

                foreach ($registro['lineas'] as [$producto, $cantidad]) {
                    $mensaje[] = "• {$cantidad} × {$producto}";
                }

                $_SESSION['solicitud_catalogo'] = ['codigo' => $codigo, 'mensaje' => implode("\n", $mensaje)];
                unset($_SESSION['token_catalogo']);

                // Redirigir para que recargar la página no vuelva a enviar el formulario.
                header('Location: index.php?enviada=1', true, 303);
                exit;
            } catch (InvalidArgumentException $error) {
                $errorCatalogo = $error->getMessage();
            } catch (Throwable $error) {
                error_log($error->getMessage());
                $errorCatalogo = 'No se pudo guardar la solicitud. Inténtalo nuevamente.';
            }
        }

        // Mostrar el catálogo o la confirmación después del envío.
        $solicitud = ($_GET['enviada'] ?? '') === '1' ? ($_SESSION['solicitud_catalogo'] ?? null) : null;
        $productos = $solicitud ? [] : $modelo->productos();
        $enlaceWhatsApp = '';
        if ($solicitud && preg_match('/^[1-9][0-9]{9,14}$/D', $whatsapp)) {
            $enlaceWhatsApp = 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode($solicitud['mensaje']);
        }
        require __DIR__ . '/../views/catalogoClientes.php';
    }
}
