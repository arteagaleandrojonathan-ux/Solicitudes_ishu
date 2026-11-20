# Solicitudes ISHUME

Catálogo para clientes. El formulario guarda una **solicitud de cotización**, con su código `COT-…`. Después abre WhatsApp con el mensaje preparado para que el cliente lo envíe. El equipo de ISHUME revisa la solicitud en **Compras → Productos por comprar**.

## Estructura MVC

| Carpeta | Qué contiene |
|---|---|
| `app/models/` | Consulta de productos y guardado de la solicitud y sus líneas. |
| `app/controllers/` | Validación del formulario, confirmación y enlace de WhatsApp. |
| `app/views/` | Catálogo, carrito y confirmación. |
| `app/config/` | Conexión a MariaDB. |
| `public/` | Punto de entrada, CSS, JavaScript e imágenes WebP. |
| `database/` | SQL para crear las tablas de solicitudes si faltan. |

## Probar con XAMPP

1. Enciende Apache y MySQL. Dentro de `Solicitudes_ishume`, copia la configuración de ejemplo en PowerShell.

   ```powershell
   Copy-Item app/config/config.ejemplo.php app/config/config.local.php
   ```

2. Completa `usuario` y `clave` con los de tu instalación de MariaDB. El archivo `config.local.php` está excluido de Git. `database/solicitudes.sql` se ejecuta en la base existente de ISHUME si todavía no tiene las tablas de solicitudes.
3. Abre `http://localhost/Solicitudes_ishume/public/`. Envía una solicitud y búscala en **Compras → Productos por comprar** del sistema principal.

## Subir el código y publicar

1. Crea un repositorio vacío en GitHub. Ejecuta los siguientes comandos dentro de esta carpeta independiente para subir únicamente la solicitud.

   ```powershell
   git init -b main
   git add .
   git commit -m "Agregar catálogo de solicitudes ISHUME"
   git remote add origin URL_DEL_REPOSITORIO
   git push -u origin main
   ```

2. Despliega el repositorio en un alojamiento con **PHP 8.1 o superior**, `pdo_mysql`, `mbstring` y HTTPS. Configura **`public/`** como raíz web. GitHub Pages sirve archivos estáticos y el formulario necesita un servidor PHP.
3. Configura `ISHUME_DB_HOST`, `ISHUME_DB_PORT`, `ISHUME_DB_NAME`, `ISHUME_DB_USER` e `ISHUME_DB_PASSWORD` en el servidor. El catálogo y el sistema de administración deben consultar la **misma base de datos** para ver los pedidos. Una base en `localhost` apunta al propio servidor donde se ejecuta PHP. Para usar la base de tu PC hace falta conectividad privada entre servidores o migrar la base a un servidor compartido. Mantén MariaDB y el panel administrativo fuera de la web pública.
4. El usuario de base de datos de la solicitud necesita leer `modelos` y `categorias` e insertar en `SolicitudesCatalogo` y `DetalleSolicitudCatalogo`. Configura `ISHUME_WHATSAPP` si cambias el número de contacto, en formato internacional y sin `+`.
5. Envía una solicitud de prueba desde otra red. Revisa el código en el panel administrativo y abre el mensaje de WhatsApp. Usa la URL HTTPS del alojamiento en el botón del PDF cuando completes esa prueba.
