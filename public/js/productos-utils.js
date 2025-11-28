// Funciones auxiliares para el manejo de productos
function getNumericField(obj, keys) {
    for (let k of keys) {
        if (obj[k] !== undefined && obj[k] !== null) return parseFloat(obj[k]);
    }
    return 0;
}

function formatNumber(v) {
    const n = parseFloat(v);
    return isNaN(n) ? '0' : n.toFixed(2);
}

function formatMoney(v) { return '$' + parseFloat(v||0).toFixed(2); }

function escapeHtml(s) {
    return String(s||'').replace(/[&<>"']/g, function(c) {
        return ({
            '&': '&amp;',
            '<': '<',
            '>': '>',
            '"': '"',
            "'": '&#39;'
        })[c]||c;
    });
}

// Función para renderizar un producto individual
function renderProducto(prod) {
    const id = prod.ID_PRODUCTO || prod.id_producto || prod.ID || prod.id;
    const nombre = prod.NOMBRE || prod.nombre || prod.nombre_producto || 'Sin nombre';
    const precio = getNumericField(prod, ['PRECIO', 'precio', 'PRECIO_VENTA', 'precio_venta', 'precio']);
    const stock = getNumericField(prod, ['CANTIDAD_REAL', 'cantidad_real', 'CANTIDAD_PLANIFICADA', 'cantidad_planificada', 'CANTIDAD', 'cantidad']);
    const imgSrc = prod.IMAGEN || prod.imagen || ('imagenes_productos/' + id + '.jpg');
    const idProduccion = prod.ID_PRODUCCION || prod.id_produccion || null;

    const col = document.createElement('div');
    col.className = 'col-md-4 col-lg-3';
    col.innerHTML = `
        <div class="card product-card p-2">
            <div class="d-flex align-items-center gap-2">
                <img src="${escapeHtml(imgSrc)}"
                     onerror="this.src='https://via.placeholder.com/64?text=IMG'"
                     class="product-img rounded"
                     alt="${escapeHtml(nombre)}">
                <div class="flex-grow-1">
                    <div class="fw-bold">${escapeHtml(nombre)}</div>
                    <div class="text-muted small">Precio: ${formatMoney(precio)}</div>
                </div>
            </div>
            <div class="mt-2">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="small text-muted">Stock disponible:</div>
                    <div class="stock-badge">${formatNumber(stock)}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number"
                           data-id="${id}"
                           ${idProduccion ? `data-id-produccion="${idProduccion}"` : ''}
                           data-precio="${precio}"
                           class="form-control input-cantidad"
                           min="0"
                           max="${stock}"
                           step="1"
                           value="0"
                           style="width:100px">
                    <div class="ms-auto subtotal" id="subtotal-${id}">${formatMoney(0)}</div>
                </div>
            </div>
        </div>`;

    return col;
}

// Función mejorada para renderizar lista de productos
function renderProductos(lista) {
    productosActuales = lista;
    const container = document.getElementById('productosContainer');
    container.innerHTML = '';

    if (!lista || lista.length === 0) {
        container.innerHTML = '<div class="alert alert-secondary">No hay productos para esta categoría</div>';
        return;
    }

    lista.forEach(prod => {
        container.appendChild(renderProducto(prod));
    });

    // Adjuntar listeners
    document.querySelectorAll('.input-cantidad').forEach(input => {
        input.addEventListener('input', onCantidadChange);
        // Validación de stock
        input.addEventListener('change', function(e) {
            const max = parseFloat(this.max);
            const val = parseFloat(this.value);
            if (val > max) {
                mostrarAlerta(`Solo hay ${max} unidades disponibles`, 'warning');
                this.value = max;
                // Trigger change para actualizar subtotal
                this.dispatchEvent(new Event('input'));
            }
        });
    });
}
