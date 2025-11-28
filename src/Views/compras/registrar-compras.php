
<?php

// Iniciar sesión de manera compatible con tu sistema
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// DEBUG - Ver qué hay en la sesión (quitar después)
error_log("🎯 SESIÓN ACTUAL: " . print_r($_SESSION, true));

// Verificar sesión según tu sistema - probamos diferentes variables
$sesion_activa = false;
$variables_sesion = [
    'logged_in', 'iniciada', 'USUARIO', 'usuario', 
    'ID_USUARIO', 'id_usuario', 'user_name', 'usuario_nombre'
];

foreach ($variables_sesion as $variable) {
    if (isset($_SESSION[$variable])) {
        $sesion_activa = true;
        error_log("✅ Sesión activa con variable: $variable = " . $_SESSION[$variable]);
        break;
    }
}

if (!$sesion_activa) {
    error_log("❌ No hay sesión activa - Redirigiendo al login");
    echo "<script>
        alert('Sesión no encontrada. Serás redirigido al login.');
        window.location.href = '/sistema/public/login';
    </script>";
    exit;
}

// Obtener datos del usuario para mostrar
$nombre_usuario = $_SESSION['NOMBRE_USUARIO'] ?? 
                 $_SESSION['usuario_nombre'] ?? 
                 $_SESSION['user_name'] ?? 
                 $_SESSION['USUARIO'] ?? 
                 'Usuario';

$id_usuario = $_SESSION['ID_USUARIO'] ?? 
              $_SESSION['id_usuario'] ?? 
              $_SESSION['user_id'] ?? 
              0;

// Registrar Compra - Vista corregida
use App\models\comprasModel;

// Obtener datos necesarios para el formulario
try {
    require_once __DIR__ . '/../../models/comprasModel.php';
    $proveedores = comprasModel::obtenerProveedores();
} catch (Exception $e) {
    error_log("Error al cargar datos para compras: " . $e->getMessage());
    $proveedores = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Compra - Sistema de Gestión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        /* Mantén todos tus estilos CSS actuales */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .main {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .pagetitle {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .pagetitle h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 25px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .card-title {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-bg);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table-detalles th {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
            font-weight: 600;
            padding: 15px 12px;
            border: none;
        }
        
        .table-detalles td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: var(--border-color);
        }
        
        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        .btn-agregar {
            background: linear-gradient(135deg, var(--success-color), #219653);
            color: white;
        }
        
        .btn-eliminar {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .total-section {
            background: linear-gradient(135deg, #fff, var(--light-bg));
            padding: 20px;
            border-radius: 10px;
            border: 2px solid var(--border-color);
            text-align: center;
        }
        
        #total-compra {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success-color);
        }
        
        .unidad-display {
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Registrar Compra</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=compras">Compras</a></li>
                <li class="breadcrumb-item active">Registrar</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Información de la Compra</h5>
                        
                        <form id="formCompra">
                            <div class="row mb-3">
                                <div class="col-md-6">
    <label for="id_proveedor" class="form-label">Proveedor *</label>
    <select class="form-select" id="id_proveedor" name="id_proveedor" required>
        <option value="">Seleccionar proveedor...</option>
        <?php foreach ($proveedores as $proveedor): ?>
            <option value="<?php echo $proveedor['ID_PROVEEDOR']; ?>">
                <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <small class="text-muted">Proveedores cargados: <?php echo count($proveedores); ?></small>
</div>
                                <div class="col-md-6">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones adicionales..."></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="card-title">Detalles de la Compra</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-detalles">
                                            <thead>
                                                <tr>
                                                    <th width="40%">Producto</th>
                                                    <th width="15%">Cantidad</th>
                                                    <th width="15%">Precio Unitario (L)</th>
                                                    <th width="15%">Subtotal (L)</th>
                                                    <th width="15%">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalles-body">
                                                <!-- Las filas de detalles se agregarán aquí dinámicamente -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <button type="button" class="btn btn-agregar btn-sm" onclick="agregarDetalle()">
                                                            <i class="bi bi-plus-circle"></i> Agregar Producto
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-6">
                                    <div class="total-section text-center">
                                        <label>Total de la Compra:</label>
                                        <div id="total-compra">L 0.00</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-secondary" onclick="cancelarCompra()">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Registrar Compra
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Variables globales
let contadorDetalles = 0;
let detalles = [];
let productosPorProveedor = {};

document.addEventListener('DOMContentLoaded', function() {
    // Agregar primera fila vacía
    agregarDetalle();
    
    // Configurar formulario
    document.getElementById('formCompra').addEventListener('submit', registrarCompra);
    
    // Event listener para cambio de proveedor
    document.getElementById('id_proveedor').addEventListener('change', function() {
        const idProveedor = this.value;
        cargarProductosPorProveedor(idProveedor);
    });
});

function agregarDetalle() {
    contadorDetalles++;
    const tbody = document.getElementById('detalles-body');
    
    const tr = document.createElement('tr');
    tr.id = `detalle-${contadorDetalles}`;
    
    const proveedorSelect = document.getElementById('id_proveedor');
    const idProveedor = proveedorSelect.value;
    
    let listaProductos = [];
    if (idProveedor && productosPorProveedor[idProveedor]) {
        listaProductos = productosPorProveedor[idProveedor];
    }
    
    tr.innerHTML = `
        <td>
            <select class="form-select producto-select" name="detalles[${contadorDetalles}][id_proveedor_producto]" required onchange="actualizarUnidad(this)">
                <option value="">${listaProductos.length > 0 ? 'Seleccionar producto...' : 'Seleccione un proveedor primero'}</option>
                ${listaProductos.length > 0 ? listaProductos.map(producto => 
                    `<option value="${producto.ID_PROVEEDOR_PRODUCTO}" 
                            data-unidad="${producto.UNIDAD_MEDIDA}"
                            data-precio="${producto.PRECIO_UNITARIO || '0'}">
                        ${producto.NOMBRE} - ${producto.UNIDAD_MEDIDA}
                    </option>`
                ).join('') : ''}
            </select>
            <small class="text-muted unidad-display" id="unidad-${contadorDetalles}"></small>
        </td>
        <td>
            <input type="number" class="form-control cantidad" name="detalles[${contadorDetalles}][cantidad]" 
                   step="1" min="1" required onchange="calcularSubtotal(this)" 
                   oninput="validarEntero(this)">
        </td>
        <td>
            <input type="number" class="form-control precio" name="detalles[${contadorDetalles}][precio_unitario]" 
                   step="0.01" min="0.01" required onchange="calcularSubtotal(this)" readonly>
        </td>
        <td>
            <span class="subtotal fw-bold text-success">L 0.00</span>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-eliminar btn-sm" onclick="eliminarDetalle(${contadorDetalles})" ${contadorDetalles === 1 ? 'disabled' : ''}>
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    
    detalles.push({
        id: contadorDetalles,
        id_proveedor_producto: '',
        cantidad: 0,
        precio_unitario: 0,
        subtotal: 0
    });
    
    console.log('DEBUG: Detalle agregado. Total detalles:', detalles.length);
}

function validarEntero(input) {
    // Remover cualquier caracter que no sea número
    input.value = input.value.replace(/[^0-9]/g, '');
    
    // Asegurar que sea mínimo 1
    if (input.value < 1) {
        input.value = 1;
    }
    
    // Forzar el cálculo del subtotal
    calcularSubtotal(input);
}

function cargarProductosPorProveedor(idProveedor) {
    if (!idProveedor) {
        console.log('DEBUG: No hay proveedor seleccionado');
        actualizarOpcionesProductos([]);
        return;
    }
    
    console.log('DEBUG: Cargando productos para proveedor:', idProveedor);
    
    // Mostrar loading
    const selects = document.querySelectorAll('.producto-select');
    selects.forEach(select => {
        select.innerHTML = '<option value="">Cargando productos...</option>';
        select.disabled = true;
    });
    
    // USAR EL NUEVO ENDPOINT CON LA RELACIÓN
    const url = `/sistema/public/index.php?route=compras&caso=obtenerProductosProveedorRelacion&id_proveedor=${idProveedor}`;
    
    console.log('DEBUG: URL de petición (nueva relación):', url);
    
    fetch(url)
    .then(response => {
        console.log('DEBUG: Response status:', response.status, response.statusText);
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('DEBUG: Respuesta no es JSON:', text.substring(0, 200));
                throw new Error('El servidor devolvió HTML en lugar de JSON. Verifique el endpoint.');
            });
        }
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Datos recibidos del servidor (nueva relación):', data);
        
        if (data.status === 200 && data.data) {
            console.log('DEBUG: Productos cargados exitosamente (nueva relación):', data.data.length);
            productosPorProveedor[idProveedor] = data.data;
            actualizarOpcionesProductos(data.data);
        } else {
            console.log('DEBUG: No hay productos disponibles con la nueva relación');
            actualizarOpcionesProductos([]);
            if (data.message) {
                alert('Info: ' + data.message);
            }
        }
    })
    .catch(error => {
        console.error('DEBUG: Error en fetch (nueva relación):', error);
        actualizarOpcionesProductos([]);
        alert('Error al cargar los productos: ' + error.message);
    })
    .finally(() => {
        const selects = document.querySelectorAll('.producto-select');
        selects.forEach(select => {
            select.disabled = false;
        });
    });
}

function actualizarOpcionesProductos(productosLista) {
    const selects = document.querySelectorAll('.producto-select');
    
    selects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Seleccionar producto...</option>';
        
        if (productosLista && productosLista.length > 0) {
            productosLista.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.ID_PROVEEDOR_PRODUCTO;
                option.textContent = `${producto.NOMBRE} - ${producto.UNIDAD_MEDIDA}`;
                option.setAttribute('data-unidad', producto.UNIDAD_MEDIDA);
                option.setAttribute('data-precio', producto.PRECIO_UNITARIO || '0');
                select.appendChild(option);
            });
            
            if (currentValue) {
                select.value = currentValue;
                const event = new Event('change');
                select.dispatchEvent(event);
            }
        } else {
            select.innerHTML = '<option value="">No hay productos disponibles</option>';
        }
    });
}

function eliminarDetalle(id) {
    const tr = document.getElementById(`detalle-${id}`);
    if (tr) {
        tr.remove();
        
        // CORRECCIÓN: Actualizar correctamente el array de detalles
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles.splice(detalleIndex, 1);
        }
        
        calcularTotal();
        
        // Habilitar botones de eliminar si hay más de una fila
        const deleteButtons = document.querySelectorAll('.btn-eliminar');
        if (deleteButtons.length > 1) {
            deleteButtons.forEach(btn => btn.disabled = false);
        }
        
        console.log('DEBUG: Detalle eliminado. Detalles restantes:', detalles.length);
    }
}

function actualizarUnidad(select) {
    const id = parseInt(select.name.match(/\[(\d+)\]/)[1]);
    const selectedOption = select.options[select.selectedIndex];
    const unidadDisplay = document.getElementById(`unidad-${id}`);
    
    if (selectedOption.value) {
        const precioSugerido = selectedOption.getAttribute('data-precio');
        const unidad = selectedOption.getAttribute('data-unidad');
        unidadDisplay.textContent = `Unidad: ${unidad} | Precio: L ${parseFloat(precioSugerido || 0).toFixed(2)}`;
        
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_proveedor_producto = selectedOption.value;
        }
        
        const row = select.closest('tr');
        const precioInput = row.querySelector('.precio');
        if (precioInput && precioSugerido && precioSugerido !== '0') {
            // Establecer el precio automáticamente y hacerlo readonly
            precioInput.value = parseFloat(precioSugerido).toFixed(2);
            precioInput.readOnly = true; // Hacer el campo de solo lectura
            calcularSubtotal(precioInput);
        }
    } else {
        unidadDisplay.textContent = '';
        
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_proveedor_producto = '';
        }
        
        // Si no hay producto seleccionado, habilitar el campo de precio
        const row = select.closest('tr');
        const precioInput = row.querySelector('.precio');
        if (precioInput) {
            precioInput.readOnly = false;
            precioInput.value = '';
        }
    }
}

function calcularSubtotal(input) {
    const row = input.closest('tr');
    const id = parseInt(row.id.split('-')[1]);
    const cantidadInput = row.querySelector('.cantidad');
    const precioInput = row.querySelector('.precio');
    
    let cantidad = parseInt(cantidadInput.value) || 0;
    const precio = parseFloat(precioInput.value) || 0;
    
    // Validar que la cantidad sea mínimo 1
    if (cantidad < 1) {
        cantidad = 1;
        cantidadInput.value = 1;
    }
    
    const subtotal = cantidad * precio;
    
    row.querySelector('.subtotal').textContent = `L ${subtotal.toFixed(2)}`;
    
    const detalleIndex = detalles.findIndex(d => d.id === id);
    if (detalleIndex !== -1) {
        detalles[detalleIndex].cantidad = cantidad;
        detalles[detalleIndex].precio_unitario = precio;
        detalles[detalleIndex].subtotal = subtotal;
    }
    
    console.log('DEBUG: Subtotal calculado para detalle', id, ':', subtotal);
    console.log('DEBUG: Estado actual de detalles:', detalles);
    
    calcularTotal();
}

function calcularTotal() {
    const total = detalles.reduce((sum, detalle) => sum + detalle.subtotal, 0);
    document.getElementById('total-compra').textContent = `L ${total.toFixed(2)}`;
    console.log('DEBUG: Total calculado:', total);
}

function cancelarCompra() {
    if (confirm('¿Está seguro de que desea cancelar la compra? Se perderán todos los datos ingresados.')) {
        window.location.href = '/sistema/public/consultar-compras';
    }
}

function registrarCompra(event) {
    event.preventDefault();
    
    console.log('DEBUG: Iniciando registro de compra...');
    
    const proveedor = document.getElementById('id_proveedor').value;
    if (!proveedor) {
        alert('Debe seleccionar un proveedor');
        return;
    }
    
    const detallesValidos = [];
    const detallesRows = document.querySelectorAll('#detalles-body tr');
    
    detallesRows.forEach((row) => {
        const productoSelect = row.querySelector('.producto-select');
        const idProveedorProducto = productoSelect.value;
        const cantidad = parseInt(row.querySelector('.cantidad').value) || 0;
        const precioUnitario = parseFloat(row.querySelector('.precio').value) || 0;
        
        // Validar que la cantidad sea entero y mayor o igual a 1
        if (idProveedorProducto && cantidad >= 1 && precioUnitario > 0) {
            detallesValidos.push({
                id_proveedor_producto: parseInt(idProveedorProducto),
                cantidad: cantidad,
                precio_unitario: precioUnitario
            });
        }
    });
    
    console.log('DEBUG: Detalles válidos encontrados:', detallesValidos);
    
    if (detallesValidos.length === 0) {
        alert('Debe agregar al menos un detalle de compra válido');
        return;
    }

    const datosEnvio = {
        id_proveedor: parseInt(proveedor),
        id_usuario: <?php echo $_SESSION['id_usuario'] ?? 1; ?>,
        observaciones: document.getElementById('observaciones').value,
        detalles: detallesValidos,
        creado_por: '<?php echo $_SESSION['usuario'] ?? "SISTEMA"; ?>'
    };

    console.log('DEBUG: Enviando datos:', datosEnvio);
    
    const submitBtn = document.querySelector('#formCompra button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
    submitBtn.disabled = true;
    
    // URL para la API de compras
    const url = '/sistema/public/index.php?route=compras&caso=registrarOrdenCompra';
    
    console.log('DEBUG: URL completa:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datosEnvio)
    })
    .then(response => {
        console.log('DEBUG: Response status:', response.status, response.statusText);
        return response.json();
    })
    .then(data => {
        console.log('DEBUG: Respuesta del servidor:', data);
        
        if (data.status === 201 || data.success) {
            alert('✅ ' + (data.message || 'Compra registrada exitosamente'));
            // CORRECCIÓN: Redirección corregida
            window.location.href = '/sistema/public/consultar-ordenes-pendientes';
        } else {
            alert('❌ Error: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('DEBUG: Error completo:', error);
        alert('Error al registrar la compra: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>
