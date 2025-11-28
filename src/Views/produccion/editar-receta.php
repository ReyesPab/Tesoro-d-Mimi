<?php
// Editar Producto con Receta - Vista
use App\models\produccionModel;

// Obtener ID del producto desde la URL
$id_producto = $_GET['id'] ?? null;

if (!$id_producto) {
    echo "<script>alert('ID de producto no especificado'); window.location.href = '/sistema/public/ver-recetas';</script>";
    exit;
}

// Obtener datos necesarios para el formulario
try {
    require_once __DIR__ . '/../../models/produccionModel.php';
    
    // Obtener producto y receta existente
    $productoRecetaData = produccionModel::obtenerProductoRecetaPorId($id_producto);
    $materiasPrimas = produccionModel::obtenerMateriasPrimasParaReceta();
    $unidadesMedida = produccionModel::obtenerUnidadesMedida();
    
    if (!$productoRecetaData['success']) {
        echo "<script>alert('Producto no encontrado'); window.location.href = '/sistema/public/ver-recetas';</script>";
        exit;
    }
    
    $producto = $productoRecetaData['data']['producto'];
    $recetaExistente = $productoRecetaData['data']['receta'];
    
} catch (Exception $e) {
    error_log("Error al cargar datos para editar producto: " . $e->getMessage());
    echo "<script>alert('Error al cargar datos del producto'); window.location.href = '/sistema/public/ver-recetas';</script>";
    exit;
}
?>
<style>
:root {
    --primary-color: #8B4513;
    --secondary-color: #D2691E;
    --success-color: #27ae60;
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --light-bg: #f8f9fa;
    --border-color: #dee2e6;
}

.pagetitle h1 {
    color: var(--primary-color);
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: none;
    margin-bottom: 25px;
}

.card-title {
    color: var(--primary-color);
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--light-bg);
}

.form-control, .form-select {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(210, 105, 30, 0.25);
}

.table-detalles th {
    background: linear-gradient(135deg, var(--primary-color), #A0522D);
    color: white;
    font-weight: 600;
    padding: 15px 12px;
    border: none;
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

.btn-agregar {
    background: linear-gradient(135deg, var(--success-color), #219653);
    color: white;
}

.btn-eliminar {
    background: linear-gradient(135deg, var(--danger-color), #c0392b);
    color: white;
}

.btn-primary {
    background: linear-gradient(135deg, var(--secondary-color), #CD853F);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
}

.unidad-display {
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
    color: #6c757d;
}

.stock-info {
    font-size: 0.75rem;
    color: #28a745;
    font-weight: 500;
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.table-detalles td {
    vertical-align: middle;
}

.input-group-text {
    min-width: 60px;
    justify-content: center;
}

.alert {
    border-radius: 8px;
    border: none;
    margin-bottom: 20px;
}
</style>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Editar Producto con Receta - Rosquilleria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Mismo CSS que el formulario de creación -->
    <style>
        /* Copia todo el CSS del formulario de creación aquí */
    </style>
</head>

<body>
<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Editar Producto con Receta</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=produccion">Producción</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/ver-recetas">Ver Recetas</a></li>
                <li class="breadcrumb-item active">Editar Receta</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Editar Información del Producto</h5>
                        
                        <form id="formProductoReceta">
                            <!-- Campo oculto para el ID del producto -->
                            <input type="hidden" id="id_producto" name="id_producto" value="<?php echo $producto['ID_PRODUCTO']; ?>">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           value="<?php echo htmlspecialchars($producto['NOMBRE']); ?>"
                                           placeholder="Ej: Rosquilla Tradicional">
                                </div>
                                <div class="col-md-6">
                                    <label for="id_unidad_medida" class="form-label">Unidad de Medida *</label>
                                    <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                        <option value="">Seleccionar unidad...</option>
                                        <?php if ($unidadesMedida['success'] && !empty($unidadesMedida['data'])): ?>
                                            <?php foreach ($unidadesMedida['data'] as $unidad): ?>
                                                <option value="<?php echo $unidad['ID_UNIDAD_MEDIDA']; ?>" 
                                                    <?php echo $unidad['ID_UNIDAD_MEDIDA'] == $producto['ID_UNIDAD_MEDIDA'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($unidad['UNIDAD']); ?> - <?php echo htmlspecialchars($unidad['DESCRIPCION']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">No hay unidades de medida disponibles</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="precio" class="form-label">Precio *</label>
                                    <input type="number" class="form-control" id="precio" name="precio" 
                                           step="0.01" min="0.01" required 
                                           value="<?php echo number_format($producto['PRECIO'], 2); ?>"
                                           placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                              placeholder="Descripción opcional del producto..."><?php echo htmlspecialchars($producto['DESCRIPCION']); ?></textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="card-title">Ingredientes de la Receta</h5>
                                    
                                    <!-- Mensaje si no hay materias primas -->
                                    <?php if (!$materiasPrimas['success'] || empty($materiasPrimas['data'])): ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i> No hay materias primas disponibles. 
                                            <a href="/sistema/public/index.php?route=gestion_materias_primas" class="alert-link">
                                                Agregar materias primas primero
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-detalles">
                                            <thead>
                                                <tr>
                                                    <th width="40%">Materia Prima</th>
                                                    <th width="15%">Unidad</th> 
                                                    <th width="20%">Cantidad Necesaria</th>
                                                    <th width="10%">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detalles-body">
                                                <!-- Las filas de detalles se cargarán con JavaScript -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-center">
                                                        <button type="button" class="btn btn-agregar btn-sm" onclick="agregarDetalle()" 
                                                                <?php echo (!$materiasPrimas['success'] || empty($materiasPrimas['data'])) ? 'disabled' : ''; ?>>
                                                            <i class="bi bi-plus-circle"></i> Agregar Ingrediente
                                                        </button>
                                                        <?php if (!$materiasPrimas['success'] || empty($materiasPrimas['data'])): ?>
                                                            <small class="text-muted d-block mt-1">No hay materias primas disponibles</small>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-secondary" onclick="cancelarEdicion()">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary" 
                                            <?php echo (!$materiasPrimas['success'] || empty($materiasPrimas['data'])) ? 'disabled' : ''; ?>>
                                        <i class="bi bi-check-circle"></i> Actualizar Producto y Receta
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
let materiasPrimas = <?php echo json_encode(($materiasPrimas['success'] ? $materiasPrimas['data'] : [])); ?>;
let recetaExistente = <?php echo json_encode($recetaExistente); ?>;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Receta existente cargada:', recetaExistente);
    console.log('Materias primas disponibles:', materiasPrimas);
    
    // Cargar los detalles existentes de la receta
    if (recetaExistente.length > 0) {
        recetaExistente.forEach(ingrediente => {
            agregarDetalleExistente(ingrediente);
        });
    } else {
        // Si no hay receta existente, agregar una fila vacía
        agregarDetalle();
    }
    
    // Configurar formulario para edición
    document.getElementById('formProductoReceta').addEventListener('submit', actualizarProductoConReceta);
});

function agregarDetalleExistente(ingrediente) {
    contadorDetalles++;
    const tbody = document.getElementById('detalles-body');
    
    const tr = document.createElement('tr');
    tr.id = `detalle-${contadorDetalles}`;
    
    // Crear opciones para el select
    let opcionesHTML = '<option value="">Seleccionar materia prima...</option>';
    materiasPrimas.forEach(mp => {
        const selected = mp.ID_MATERIA_PRIMA == ingrediente.ID_MATERIA_PRIMA ? 'selected' : '';
        opcionesHTML += `
            <option value="${mp.ID_MATERIA_PRIMA}" 
                    data-unidad="${mp.UNIDAD || ''}"
                    data-stock="${mp.STOCK_ACTUAL || 0}"
                    data-descripcion="${mp.DESCRIPCION || ''}"
                    ${selected}>
                ${mp.NOMBRE} ${mp.DESCRIPCION ? ' - ' + mp.DESCRIPCION : ''}
            </option>
        `;
    });
    
    tr.innerHTML = `
        <td>
            <select class="form-select materia-prima-select" name="detalles[${contadorDetalles}][id_materia_prima]" required onchange="actualizarUnidad(this)">
                ${opcionesHTML}
            </select>
            <small class="text-muted unidad-display" id="descripcion-${contadorDetalles}">${ingrediente.DESCRIPCION_MATERIA_PRIMA || ''}</small>
        </td>
        <td>
            <span class="form-control-plaintext unidad-texto fw-bold text-center text-success" id="unidad-texto-${contadorDetalles}">${ingrediente.UNIDAD || '-'}</span>
            <small class="stock-info" id="stock-texto-${contadorDetalles}">Stock: ${parseFloat(ingrediente.STOCK_ACTUAL || 0).toFixed(2)}</small>
        </td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control cantidad" name="detalles[${contadorDetalles}][cantidad_necesaria]" 
                       step="0.001" min="0.001" required 
                       value="${parseFloat(ingrediente.CANTIDAD_NECESARIA).toFixed(3)}"
                       placeholder="0.000"
                       oninput="actualizarCantidad(this, ${contadorDetalles})">
                <span class="input-group-text unidad-cantidad text-success" id="unidad-cantidad-${contadorDetalles}">${ingrediente.UNIDAD || '-'}</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-eliminar btn-sm" onclick="eliminarDetalle(${contadorDetalles})" 
                    title="Eliminar ingrediente" ${contadorDetalles === 1 ? 'disabled' : ''}>
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    
    // Inicializar el detalle
    detalles.push({
        id: contadorDetalles,
        id_materia_prima: ingrediente.ID_MATERIA_PRIMA,
        cantidad_necesaria: parseFloat(ingrediente.CANTIDAD_NECESARIA),
        unidad: ingrediente.UNIDAD
    });
    
    console.log(`Detalle existente ${contadorDetalles} cargado:`, detalles[detalles.length-1]);
}

function agregarDetalle() {
    if (materiasPrimas.length === 0) {
        mostrarAlerta('No hay materias primas disponibles para agregar', 'warning');
        return;
    }
    
    contadorDetalles++;
    const tbody = document.getElementById('detalles-body');
    
    const tr = document.createElement('tr');
    tr.id = `detalle-${contadorDetalles}`;
    
    // Crear opciones para el select
    let opcionesHTML = '<option value="">Seleccionar materia prima...</option>';
    materiasPrimas.forEach(mp => {
        const stock = parseFloat(mp.STOCK_ACTUAL || 0).toFixed(2);
        opcionesHTML += `
            <option value="${mp.ID_MATERIA_PRIMA}" 
                    data-unidad="${mp.UNIDAD || ''}"
                    data-stock="${mp.STOCK_ACTUAL || 0}"
                    data-descripcion="${mp.DESCRIPCION || ''}">
                ${mp.NOMBRE} ${mp.DESCRIPCION ? ' - ' + mp.DESCRIPCION : ''}
            </option>
        `;
    });
    
    tr.innerHTML = `
        <td>
            <select class="form-select materia-prima-select" name="detalles[${contadorDetalles}][id_materia_prima]" required onchange="actualizarUnidad(this)">
                ${opcionesHTML}
            </select>
            <small class="text-muted unidad-display" id="descripcion-${contadorDetalles}"></small>
        </td>
        <td>
            <span class="form-control-plaintext unidad-texto fw-bold text-center" id="unidad-texto-${contadorDetalles}">-</span>
            <small class="stock-info" id="stock-texto-${contadorDetalles}"></small>
        </td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control cantidad" name="detalles[${contadorDetalles}][cantidad_necesaria]" 
                       step="0.001" min="0.001" required placeholder="0.000"
                       oninput="actualizarCantidad(this, ${contadorDetalles})">
                <span class="input-group-text unidad-cantidad" id="unidad-cantidad-${contadorDetalles}">-</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-eliminar btn-sm" onclick="eliminarDetalle(${contadorDetalles})" 
                    title="Eliminar ingrediente">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(tr);
    
    // Inicializar el detalle
    detalles.push({
        id: contadorDetalles,
        id_materia_prima: '',
        cantidad_necesaria: 0,
        unidad: ''
    });
    
    console.log(`Nuevo detalle ${contadorDetalles} agregado. Total:`, detalles.length);
    
    // Habilitar todos los botones de eliminar si hay más de una fila
    const deleteButtons = document.querySelectorAll('.btn-eliminar');
    if (deleteButtons.length > 1) {
        deleteButtons.forEach(btn => btn.disabled = false);
    }
}

function eliminarDetalle(id) {
    const tr = document.getElementById(`detalle-${id}`);
    if (tr) {
        tr.remove();
        
        // Actualizar el array de detalles
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles.splice(detalleIndex, 1);
        }
        
        // Habilitar/deshabilitar botones de eliminar según la cantidad de filas
        const deleteButtons = document.querySelectorAll('.btn-eliminar');
        if (deleteButtons.length === 1) {
            deleteButtons[0].disabled = true;
        } else {
            deleteButtons.forEach(btn => btn.disabled = false);
        }
        
        console.log(`Detalle ${id} eliminado. Restantes:`, detalles.length);
    }
}

function actualizarUnidad(select) {
    const id = parseInt(select.name.match(/\[(\d+)\]/)[1]);
    const selectedOption = select.options[select.selectedIndex];
    const unidadTexto = document.getElementById(`unidad-texto-${id}`);
    const stockTexto = document.getElementById(`stock-texto-${id}`);
    const descripcionTexto = document.getElementById(`descripcion-${id}`);
    const unidadCantidad = document.getElementById(`unidad-cantidad-${id}`);
    
    if (selectedOption.value) {
        const unidad = selectedOption.getAttribute('data-unidad');
        const stock = selectedOption.getAttribute('data-stock');
        const descripcion = selectedOption.getAttribute('data-descripcion');
        
        // Actualizar displays
        unidadTexto.textContent = unidad;
        unidadTexto.className = 'form-control-plaintext unidad-texto fw-bold text-center text-success';
        
        stockTexto.textContent = `Stock: ${parseFloat(stock).toFixed(2)}`;
        stockTexto.className = `stock-info ${parseFloat(stock) > 0 ? 'text-success' : 'text-warning'}`;
        
        descripcionTexto.textContent = descripcion;
        
        unidadCantidad.textContent = unidad;
        unidadCantidad.className = 'input-group-text unidad-cantidad text-success';
        
        // Actualizar array de detalles
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_materia_prima = selectedOption.value;
            detalles[detalleIndex].unidad = unidad;
        }
        
        console.log(`Detalle ${id} actualizado:`, detalles[detalleIndex]);
    } else {
        // Resetear si no hay selección
        unidadTexto.textContent = '-';
        unidadTexto.className = 'form-control-plaintext unidad-texto fw-bold text-center';
        
        stockTexto.textContent = '';
        stockTexto.className = 'stock-info';
        
        descripcionTexto.textContent = '';
        
        unidadCantidad.textContent = '-';
        unidadCantidad.className = 'input-group-text unidad-cantidad';
        
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_materia_prima = '';
            detalles[detalleIndex].unidad = '';
        }
    }
}

function actualizarCantidad(input, id) {
    const cantidad = parseFloat(input.value) || 0;
    
    const detalleIndex = detalles.findIndex(d => d.id === id);
    if (detalleIndex !== -1) {
        detalles[detalleIndex].cantidad_necesaria = cantidad;
    }
}

function mostrarAlerta(mensaje, tipo = 'info') {
    // Remover alertas existentes
    const alertasExistentes = document.querySelectorAll('.alert');
    alertasExistentes.forEach(alerta => alerta.remove());
    
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show`;
    alerta.innerHTML = `
        <i class="bi ${tipo === 'success' ? 'bi-check-circle' : tipo === 'warning' ? 'bi-exclamation-triangle' : tipo === 'error' ? 'bi-x-circle' : 'bi-info-circle'} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insertar al inicio del card-body
    const cardBody = document.querySelector('.card-body');
    cardBody.insertBefore(alerta, cardBody.firstChild);
    
    // Auto-eliminar después de 5 segundos (excepto para success)
    if (tipo !== 'success') {
        setTimeout(() => {
            if (alerta.parentNode) {
                alerta.remove();
            }
        }, 5000);
    }
}

function validarMateriasPrimasDuplicadas() {
    const materiasSeleccionadas = new Set();
    const selects = document.querySelectorAll('.materia-prima-select');
    let hayDuplicados = false;
    
    selects.forEach((select, index) => {
        if (select.value) {
            if (materiasSeleccionadas.has(select.value)) {
                const materiaPrimaNombre = select.options[select.selectedIndex].text.split(' - ')[0];
                mostrarAlerta(`La materia prima "${materiaPrimaNombre}" está duplicada en la fila ${index + 1}`, 'warning');
                select.focus();
                hayDuplicados = true;
            }
            materiasSeleccionadas.add(select.value);
        }
    });
    
    return !hayDuplicados;
}

function cancelarEdicion() {
    if (confirm('¿Está seguro de que desea cancelar? Se perderán todos los cambios no guardados.')) {
        window.location.href = '/sistema/public/ver-recetas';
    }
}

function actualizarProductoConReceta(event) {
    event.preventDefault();
    
    console.log('Iniciando actualización de producto con receta...');
    
    // Validaciones
    const idProducto = document.getElementById('id_producto').value;
    const nombre = document.getElementById('nombre').value.trim();
    const precio = document.getElementById('precio').value;
    const idUnidadMedida = document.getElementById('id_unidad_medida').value;
    
    if (!nombre) {
        mostrarAlerta('Debe ingresar el nombre del producto', 'warning');
        document.getElementById('nombre').focus();
        return;
    }
    
    if (!precio || parseFloat(precio) <= 0) {
        mostrarAlerta('Debe ingresar un precio válido mayor a 0', 'warning');
        document.getElementById('precio').focus();
        return;
    }
    
    if (!idUnidadMedida) {
        mostrarAlerta('Debe seleccionar una unidad de medida', 'warning');
        document.getElementById('id_unidad_medida').focus();
        return;
    }
    
    // Validar y recolectar detalles
    const detallesValidos = [];
    const detallesRows = document.querySelectorAll('#detalles-body tr');
    let detallesValidosCount = 0;
    let hayErrores = false;
    
    if (detallesRows.length === 0) {
        mostrarAlerta('Debe agregar al menos un ingrediente a la receta', 'warning');
        return;
    }
    
    detallesRows.forEach((row, index) => {
        const materiaPrimaSelect = row.querySelector('.materia-prima-select');
        const idMateriaPrima = materiaPrimaSelect.value;
        const cantidadInput = row.querySelector('.cantidad');
        const cantidad = parseFloat(cantidadInput.value) || 0;
        
        // Validar materia prima seleccionada
        if (!idMateriaPrima) {
            mostrarAlerta(`Debe seleccionar una materia prima en la fila ${index + 1}`, 'warning');
            materiaPrimaSelect.focus();
            hayErrores = true;
            return;
        }
        
        // Validar cantidad
        if (cantidad <= 0) {
            mostrarAlerta(`La cantidad debe ser mayor a 0 en la fila ${index + 1}`, 'warning');
            cantidadInput.focus();
            hayErrores = true;
            return;
        }
        
        if (idMateriaPrima && cantidad > 0) {
            detallesValidos.push({
                id_materia_prima: parseInt(idMateriaPrima),
                cantidad_necesaria: cantidad
            });
            detallesValidosCount++;
        }
    });
    
    if (hayErrores) return;
    
    console.log('Detalles válidos encontrados:', detallesValidos);
    
    if (detallesValidosCount === 0) {
        mostrarAlerta('Debe agregar al menos un ingrediente válido a la receta', 'warning');
        return;
    }
    
    // Validar duplicados
    if (!validarMateriasPrimasDuplicadas()) {
        return;
    }

    const datosEnvio = {
        id_producto: parseInt(idProducto),
        nombre: nombre,
        descripcion: document.getElementById('descripcion').value.trim(),
        precio: parseFloat(precio),
        id_unidad_medida: parseInt(idUnidadMedida),
        detalles: detallesValidos
    };

    console.log('Enviando datos para actualización:', datosEnvio);
    
    const submitBtn = document.querySelector('#formProductoReceta button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Actualizando...';
    submitBtn.disabled = true;
    
    // Agregar clase de loading al formulario
    document.getElementById('formProductoReceta').classList.add('loading');
    
    const url = '/sistema/public/index.php?route=produccion&caso=editarProductoConRecetaCompleto';
    
    // Usar FormData para enviar los datos
    const formData = new FormData();
    formData.append('id_producto', datosEnvio.id_producto);
    formData.append('nombre', datosEnvio.nombre);
    formData.append('descripcion', datosEnvio.descripcion);
    formData.append('precio', datosEnvio.precio);
    formData.append('id_unidad_medida', datosEnvio.id_unidad_medida);
    formData.append('detalles', JSON.stringify(datosEnvio.detalles));
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        
        if (data.status === 200 || data.success) {
            const mensaje = data.message || 'Producto con receta actualizado exitosamente';
            mostrarAlerta('✅ ' + mensaje, 'success');
            setTimeout(() => {
                window.location.href = '/sistema/public/ver-recetas';
            }, 3000);
        } else {
            const mensajeError = data.message || 'Error desconocido al actualizar';
            mostrarAlerta('❌ ' + mensajeError, 'error');
            console.error('Error del servidor:', mensajeError);
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        mostrarAlerta('Error al actualizar el producto con receta: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        document.getElementById('formProductoReceta').classList.remove('loading');
    });
}
</script>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>