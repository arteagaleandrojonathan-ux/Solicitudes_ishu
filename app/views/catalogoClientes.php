<?php $esc = static fn($valor) => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo de ISHUME</title>
    <link rel="stylesheet" href="css/CatalogoSolicitudes.css">
    <script src="js/catalogoClientes.js" defer></script>
</head>
<body class="pagina-catalogo-clientes">
    <main class="solicitudes-catalogo catalogo-clientes">
        <header class="marca-catalogo-clientes">
            <img src="img/logo_ishume03.jpg" alt="ISHUME" width="40" height="53">
            <div><h1>Catálogo ISHUME</h1><p>Cuadros y anuarios · Solicita una cotización</p></div>
        </header>

        <!-- Confirmación con el código y el acceso opcional a WhatsApp. -->
        <?php if ($solicitud): ?>
            <section class="tarjeta-solicitud">
                <h2>Solicitud guardada</h2>
                <p>Tu código es <strong><?= $esc($solicitud['codigo']) ?></strong>.</p>
                <?php if ($enlaceWhatsApp !== ''): ?>
                    <a class="accion-catalogo" href="<?= $esc($enlaceWhatsApp) ?>" target="_blank" rel="noopener noreferrer">Abrir mensaje en WhatsApp</a>
                <?php endif; ?>
                <a href="index.php">Crear otra solicitud</a>
            </section>
        <?php else: ?>
            <?php if ($errorCatalogo !== ''): ?><p class="tarjeta-solicitud error-catalogo" role="alert"><?= $esc($errorCatalogo) ?></p><?php endif; ?>
            <form method="post" action="index.php" id="formCatalogoClientes">
                <input type="hidden" name="token" value="<?= $esc($_SESSION['token_catalogo']) ?>">
                <!-- Filtros para encontrar productos en el catálogo. -->
                <div class="filtros-catalogo-clientes">
                    <label>Buscar producto <input type="search" id="buscarCatalogoCliente" placeholder="Nombre, color o código"></label>
                    <label>Categoría <select id="categoriaCatalogoCliente"><option value="">Todas</option><option>Cuadros</option><option>Anuarios</option></select></label>
                </div>

                <!-- Cada ficha conserva su cantidad al cambiar de filtro. -->
                <div class="grid-catalogo-clientes">
                    <?php foreach ($productos as $producto): ?>
                        <?php $codigo = $producto['idModelo']; $cantidad = $_POST['cantidad'][$codigo] ?? ''; ?>
                        <article class="tarjeta-solicitud ficha-catalogo-cliente" data-categoria="<?= $esc($producto['NombreCategoria']) ?>" data-busqueda="<?= $esc(implode(' ', [$producto['NombreMaterial'], $producto['Color'], $producto['NivelEducativo'], $codigo])) ?>">
                            <?php if (preg_match('~^img/modelo/[\w/-]+\.(?:webp|png|jpe?g)$~iD', $producto['Imagen'] ?? '') && is_file(__DIR__ . '/../../public/' . $producto['Imagen'])): ?>
                                <img src="<?= $esc($producto['Imagen']) ?>" alt="" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <h2><?= $esc($producto['NombreMaterial']) ?></h2>
                            <p><?= $esc($producto['Color']) ?> · <?= $esc($codigo) ?></p>
                            <label>Cantidad <input type="number" name="cantidad[<?= $esc($codigo) ?>]" value="<?= is_scalar($cantidad) ? $esc($cantidad) : '' ?>" min="0" max="999" step="1" inputmode="numeric" placeholder="0"></label>
                        </article>
                    <?php endforeach; ?>
                </div>
                <p id="catalogoSinResultados" hidden>No se encontraron productos.</p>

                <!-- Resumen del carrito y datos del cliente para la cotización. -->
                <section id="carritoCliente" class="tarjeta-solicitud">
                    <h2>Tu carrito</h2>
                    <p id="resumenCarritoCliente" aria-live="polite">Ingresa cantidades para agregar productos.</p>
                    <ul id="productosCarritoCliente"></ul>
                    <div class="filtros-catalogo-clientes">
                        <label>Nombre <input name="nombre" maxlength="100" value="<?= $esc($datos['nombre'] ?? '') ?>" required></label>
                        <label>Colegio <input name="colegio" maxlength="150" value="<?= $esc($datos['colegio'] ?? '') ?>" required></label>
                        <label>Grado y sección <input name="grado" maxlength="80" value="<?= $esc($datos['grado'] ?? '') ?>" placeholder="Opcional"></label>
                    </div>
                    <button class="accion-catalogo" type="submit">Enviar solicitud</button>
                </section>

                <a class="accion-catalogo ir-carrito-cliente" href="#carritoCliente" id="irCarritoCliente">Ver carrito</a>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
