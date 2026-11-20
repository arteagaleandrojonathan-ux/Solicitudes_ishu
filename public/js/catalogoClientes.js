const formularioCatalogo = document.getElementById('formCatalogoClientes');

if (formularioCatalogo) {
    const fichas = [...formularioCatalogo.querySelectorAll('.ficha-catalogo-cliente')];
    const buscar = document.getElementById('buscarCatalogoCliente');
    const categoria = document.getElementById('categoriaCatalogoCliente');
    const lista = document.getElementById('productosCarritoCliente');
    const resumen = document.getElementById('resumenCarritoCliente');

    // Aplicar los filtros y armar el resumen con las cantidades ingresadas.
    function actualizarCatalogo() {
        const texto = buscar.value.trim().toLocaleLowerCase('es');
        let visibles = 0;
        let unidades = 0;
        lista.replaceChildren();

        fichas.forEach(function (ficha) {
            ficha.hidden = !ficha.dataset.busqueda.toLocaleLowerCase('es').includes(texto) ||
                (categoria.value !== '' && ficha.dataset.categoria !== categoria.value);
            if (!ficha.hidden) {
                visibles++;
            }

            // Los productos ocultos por un filtro también se cuentan en el carrito.
            const cantidad = Number(ficha.querySelector('input').value);
            if (Number.isInteger(cantidad) && cantidad > 0) {
                unidades += cantidad;
                const producto = document.createElement('li');
                producto.textContent = cantidad + ' × ' + ficha.querySelector('h2').textContent;
                lista.append(producto);
            }
        });

        document.getElementById('catalogoSinResultados').hidden = visibles > 0;
        resumen.textContent = unidades ? unidades + ' unidades solicitadas' : 'Ingresa cantidades para agregar productos.';
        document.getElementById('irCarritoCliente').textContent = 'Ver carrito' + (unidades ? ' (' + unidades + ')' : '');
    }

    formularioCatalogo.addEventListener('input', actualizarCatalogo);
    categoria.addEventListener('change', actualizarCatalogo);
    actualizarCatalogo();
}
