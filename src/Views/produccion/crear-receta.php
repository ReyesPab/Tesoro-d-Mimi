<?php
// Crear Producto con Receta - Vista
use App\models\produccionModel;

// Obtener datos necesarios para el formulario
try {
    require_once __DIR__ . '/../../models/produccionModel.php';
    $materiasPrimas = produccionModel::obtenerMateriasPrimasParaReceta();
    $unidadesMedida = produccionModel::obtenerUnidadesMedida();
    
    // Debug: Ver qué datos estamos obteniendo
    error_log("Materias primas para receta obtenidas: " . print_r($materiasPrimas, true));
    error_log("Unidades de medida obtenidas: " . print_r($unidadesMedida, true));
    
} catch (Exception $e) {
    error_log("Error al cargar datos para crear producto: " . $e->getMessage());
    $materiasPrimas = ['success' => false, 'data' => []];
    $unidadesMedida = ['success' => false, 'data' => []];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Crear Producto - Rosquilleria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
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
        
        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
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
        
        .seccion-receta {
            display: none;
            transition: all 0.3s ease;
        }
        
        .seccion-receta.mostrar {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?> 
    
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Crear Producto</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=produccion">Producción</a></li>
                <li class="breadcrumb-item active">Crear Producto</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Información del Producto</h5>
                        
                        <form id="formProducto">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre del Producto *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           placeholder="Ej: Rosquilla Tradicional">
                                </div>
                                <div class="col-md-6">
                                    <label for="id_unidad_medida" class="form-label">Unidad de Medida *</label>
                                    <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                        <option value="">Seleccionar unidad...</option>
                                        <?php if ($unidadesMedida['success'] && !empty($unidadesMedida['data'])): ?>
                                            <?php foreach ($unidadesMedida['data'] as $unidad): ?>
                                                <option value="<?php echo $unidad['ID_UNIDAD_MEDIDA']; ?>">
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
                                           step="0.01" min="0.01" required placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                              placeholder="Descripción opcional del producto..."></textarea>
                                </div>
                            </div>

                            <!-- Botón para agregar receta -->
                            <div class="row mb-4" id="seccion-boton-receta">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-info" id="btn-agregar-receta" onclick="mostrarSeccionReceta()">
                                        <i class="bi bi-plus-circle"></i> ¿Desea agregar receta a este producto?
                                    </button>
                                </div>
                            </div>

                            <!-- Sección de receta (oculta inicialmente) -->
                            <div class="seccion-receta" id="seccion-receta">
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
                                                    <!-- Las filas de detalles se agregarán aquí dinámicamente -->
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
                            </div>

                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-secondary" onclick="cancelarCreacion()">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="btn-crear-producto">
                                        <i class="bi bi-check-circle"></i> Crear Producto
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
let tieneReceta = false;

// Debug en consola
console.log('Materias primas para receta cargadas:', materiasPrimas);

document.addEventListener('DOMContentLoaded', function() {
    // Configurar formulario
    document.getElementById('formProducto').addEventListener('submit', guardarProducto);
});

function mostrarSeccionReceta() {
    const seccionReceta = document.getElementById('seccion-receta');
    const btnAgregarReceta = document.getElementById('btn-agregar-receta');
    const btnCrearProducto = document.getElementById('btn-crear-producto');
    
    // Mostrar sección de receta
    seccionReceta.classList.add('mostrar');
    
    // Ocultar botón de agregar receta
    btnAgregarReceta.style.display = 'none';
    
    // Cambiar texto del botón de crear
    btnCrearProducto.innerHTML = '<i class="bi bi-check-circle"></i> Crear Producto con Receta';
    
    // Agregar primera fila vacía solo si hay materias primas disponibles
    if (materiasPrimas.length > 0) {
        agregarDetalle();
    }
    
    tieneReceta = true;
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
        </td>
        <td>
            <span class="form-control-plaintext unidad-texto fw-bold text-center" id="unidad-texto-${contadorDetalles}">-</span>
        </td>
        <td>
            <div class="input-group">
                <input type="number" class="form-control cantidad" name="detalles[${contadorDetalles}][cantidad_necesaria]" 
                       step="0.001" min="0.001" required placeholder="0.000">
                <span class="input-group-text unidad-cantidad" id="unidad-cantidad-${contadorDetalles}">-</span>
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
        id_materia_prima: '',
        cantidad_necesaria: 0,
        unidad: ''
    });
    
    console.log(`Detalle ${contadorDetalles} agregado. Total:`, detalles.length);
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
        
        // Habilitar botones de eliminar si hay más de una fila
        const deleteButtons = document.querySelectorAll('.btn-eliminar');
        if (deleteButtons.length > 1) {
            deleteButtons.forEach(btn => btn.disabled = false);
        }
        
        console.log(`Detalle ${id} eliminado. Restantes:`, detalles.length);
    }
}

function actualizarUnidad(select) {
    const id = parseInt(select.name.match(/\[(\d+)\]/)[1]);
    const selectedOption = select.options[select.selectedIndex];
    const unidadTexto = document.getElementById(`unidad-texto-${id}`);
    const unidadCantidad = document.getElementById(`unidad-cantidad-${id}`);
    
    if (selectedOption.value) {
        const unidad = selectedOption.getAttribute('data-unidad');
        
        // Actualizar displays
        unidadTexto.textContent = unidad;
        unidadTexto.className = 'form-control-plaintext unidad-texto fw-bold text-center text-success';
        
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
        
        unidadCantidad.textContent = '-';
        unidadCantidad.className = 'input-group-text unidad-cantidad';
        
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].id_materia_prima = '';
            detalles[detalleIndex].unidad = '';
        }
    }
}

function cancelarCreacion() {
    if (confirm('¿Está seguro de que desea cancelar? Se perderán todos los datos ingresados.')) {
        window.location.href = '/sistema/public/ver-recetas';
    }
}

function guardarProducto(event) {
    event.preventDefault();
    
    console.log('Iniciando creación de producto...');
    console.log('¿Tiene receta?:', tieneReceta);
    
    // Validar campos básicos del producto
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
    
    // Si tiene receta, validar los detalles
    let detallesValidos = [];
    if (tieneReceta) {
        const detallesRows = document.querySelectorAll('#detalles-body tr');
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
            }
        });
        
        if (hayErrores) return;
        
        console.log('Detalles válidos encontrados:', detallesValidos);
        
        if (detallesValidos.length === 0) {
            mostrarAlerta('Debe agregar al menos un ingrediente válido a la receta', 'warning');
            return;
        }
        
        // Validar duplicados
        if (!validarMateriasPrimasDuplicadas()) {
            return;
        }
    }

    const datosEnvio = {
        nombre: nombre,
        descripcion: document.getElementById('descripcion').value.trim(),
        precio: parseFloat(precio),
        id_unidad_medida: parseInt(idUnidadMedida),
        detalles: tieneReceta ? detallesValidos : []
    };

    console.log('Enviando datos:', datosEnvio);
    
    const submitBtn = document.querySelector('#formProducto button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';
    submitBtn.disabled = true;
    
    // Agregar clase de loading al formulario
    document.getElementById('formProducto').classList.add('loading');
    
    const url = '/sistema/public/index.php?route=produccion&caso=crearProductoConRecetaCompleto';
    
    // Usar FormData para enviar los datos
    const formData = new FormData();
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
        
        if (data.status === 201 || data.success) {
            const mensaje = data.message || (tieneReceta ? 'Producto con receta creado exitosamente' : 'Producto creado exitosamente');
            mostrarAlerta('✅ ' + mensaje, 'success');
            setTimeout(() => {
                window.location.href = '/sistema/public/gestion-inventario-productos';
            }, 3000);
        } else {
            mostrarAlerta('❌ ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        mostrarAlerta('Error al crear el producto: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        document.getElementById('formProducto').classList.remove('loading');
    });
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
                const materiaPrimaNombre = select.options[select.selectedIndex].text;
                mostrarAlerta(`La materia prima "${materiaPrimaNombre}" está duplicada en la fila ${index + 1}`, 'warning');
                select.focus();
                hayDuplicados = true;
            }
            materiasSeleccionadas.add(select.value);
        }
    });
    
    return !hayDuplicados;
}

// Actualizar cantidades en tiempo real
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('cantidad')) {
        const row = e.target.closest('tr');
        const id = parseInt(row.id.split('-')[1]);
        const cantidad = parseFloat(e.target.value) || 0;
        
        const detalleIndex = detalles.findIndex(d => d.id === id);
        if (detalleIndex !== -1) {
            detalles[detalleIndex].cantidad_necesaria = cantidad;
        }
    }
});
</script>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>