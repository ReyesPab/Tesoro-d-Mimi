<?php
// Al inicio del archivo, después de tu código de sesión
session_start();
$usuario_nombre = $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? 'Invitado';

// ========== VERIFICACIÓN DE PERMISOS ==========
require_once __DIR__ . '/../../config/SessionHelper.php';
require_once __DIR__ . '/../../config/PermisosHelper.php';
require_once __DIR__ . '/../../models/permisosModel.php';
use App\config\SessionHelper;
use App\config\PermisosHelper;

PermisosHelper::requirePermission('CREAR_PRODUCCION', 'CREAR');
?>

<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-fluid {
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a4cb3 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .page-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
        }
        
        .breadcrumb {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            padding: 8px 15px;
            margin-top: 10px;
            margin-bottom: 0;
        }
        
        .breadcrumb-item a {
            color: rgba(255,255,255,0.9);
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            padding: 18px 25px;
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a4cb3 100%) !important;
        }
        
        .card-header.bg-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #258391 100%) !important;
        }
        
        .card-header.bg-success {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #17a673 100%) !important;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .btn {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #17a673 100%);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #17a673 0%, #148a61 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(28, 200, 138, 0.3);
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #258391 100%);
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #258391 0%, #1e6d7a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(54, 185, 204, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }
        
        .alert-info {
            background-color: rgba(54, 185, 204, 0.1);
            color: #117a8b;
            border-left: 4px solid var(--info-color);
        }
        
        .alert-warning {
            background-color: rgba(246, 194, 62, 0.1);
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }
        
        .alert-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: #155724;
            border-left: 4px solid var(--secondary-color);
        }
        
        .alert-danger {
            background-color: rgba(231, 74, 59, 0.1);
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }
        
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        
        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 15px;
            font-weight: 600;
        }
        
        .table td {
            padding: 12px 15px;
            border-color: #e3e6f0;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: rgba(0,0,0,0.02);
        }
        
        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: rgba(28, 200, 138, 0.2);
            color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>

<body>
<main id="main" class="main">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Datos de Producción</h4>
                    </div>
                    <div class="card-body">
                        <form id="formProduccion">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_producto" class="font-weight-bold">Producto a Producir *</label>
                                        <select class="form-control select2" id="id_producto" name="id_producto" required>
                                            <option value="">Seleccionar producto...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cantidad_planificada" class="font-weight-bold">Cantidad a Producir *</label>
                                        <input type="number" class="form-control" id="cantidad_planificada" 
                                               name="cantidad_planificada" step="1" min="1" required 
                                               placeholder="Ej: 100">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observacion" class="font-weight-bold">Observaciones</label>
                                <textarea class="form-control" id="observacion" name="observacion" 
                                          rows="3" placeholder="Descripción de la producción, instrucciones especiales, etc..."></textarea>
                            </div>

                            <div class="form-group">
                                <button type="button" id="btnVerificarStock" class="btn btn-info">
                                    <i class="fas fa-search"></i> Verificar Stock Disponible
                                </button>
                                <button type="submit" class="btn btn-success" id="btnCrearOrden">
                                    <i class="fas fa-save"></i> Crear Orden de Producción
                                </button>
                                <a href="/sistema/public/gestion-produccion" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-list-alt"></i> Información de Receta</h4>
                    </div>
                    <div class="card-body">
                        <div id="infoReceta" class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Seleccione un producto para ver la receta
                        </div>
                        <div id="infoStock" class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Verifique el stock disponible antes de crear la orden
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-calculator"></i> Cálculo de Costos</h4>
                    </div>
                    <div class="card-body">
                        <div id="infoCostos">
                            <p class="text-muted">Los costos se calcularán automáticamente al verificar el stock</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </main>

    <!-- jQuery desde CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
    // Validación en tiempo real
$(document).ready(function() {
    console.log('✅ jQuery cargado correctamente');
    cargarProductos();
    
    // Cargar receta al seleccionar producto
    $('#id_producto').change(function() {
        var idProducto = $(this).val();
        console.log('🔄 Producto seleccionado:', idProducto);
        if (idProducto) {
            cargarReceta(idProducto);
            $('#infoStock').html('<i class="fas fa-exclamation-triangle"></i> Verifique el stock disponible');
            $('#infoStock').removeClass('alert-success alert-danger').addClass('alert-warning');
            validarFormulario();
        }
    });

    // Validar cuando cambia la cantidad
    $('#cantidad_planificada').on('input', function() {
        validarFormulario();
    });

    // Verificar stock
    $('#btnVerificarStock').click(function() {
        verificarStock();
    });

    // Enviar formulario
    $('#formProduccion').submit(function(e) {
        e.preventDefault();
        crearProduccion();
    });
    
    // Validación inicial
    validarFormulario();
});

function validarFormulario() {
    var idProducto = $('#id_producto').val();
    var cantidad = $('#cantidad_planificada').val();
    
    var formularioValido = idProducto && idProducto !== "" && cantidad && cantidad > 0;
    
    $('#btnVerificarStock').prop('disabled', !formularioValido);
    $('#btnCrearOrden').prop('disabled', true); // Solo se habilita después de verificar stock
    
    if (!formularioValido) {
        $('#infoStock').html('<i class="fas fa-info-circle"></i> Complete los datos requeridos para verificar stock');
        $('#infoStock').removeClass('alert-success alert-danger').addClass('alert-warning');
    }
}

   function cargarProductos() {
    console.log('🔍 Cargando productos...');
    console.log('📋 URL completa:', '/sistema/public/produccion?caso=obtenerProductosProduccion');
    
    // Mostrar loading
    $('#id_producto').html('<option value="">Cargando productos...</option>');
    
    $.ajax({
        url: '/sistema/public/produccion?caso=obtenerProductosProduccion',
        type: 'GET',
        dataType: 'json',
        timeout: 10000, // 10 segundos timeout
        success: function(response) {
            console.log('📦 Respuesta productos completa:', response);
            console.log('📦 Status:', response.status);
            console.log('📦 Data:', response.data);
            
            if (response.status === 200 && response.data) {
                var select = $('#id_producto');
                select.empty().append('<option value="">Seleccionar producto...</option>');
                
                response.data.forEach(function(producto) {
                    select.append('<option value="' + producto.ID_PRODUCTO + '">' + 
                                 producto.NOMBRE + ' (Stock: ' + (producto.CANTIDAD || 0) + ')</option>');
                });
                
                console.log('✅ Productos cargados exitosamente:', response.data.length);
                
                if (response.data.length === 0) {
                    select.append('<option value="">No hay productos disponibles</option>');
                    console.warn('⚠️ No hay productos disponibles');
                }
            } else {
                console.error('❌ Error en respuesta:', response);
                $('#id_producto').html('<option value="">Error al cargar productos</option>');
                Swal.fire('Error', 'No se pudieron cargar los productos: ' + (response.message || 'Error desconocido'), 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('💥 Error AJAX:', {
                status: status,
                error: error,
                xhr: xhr
            });
            $('#id_producto').html('<option value="">Error de conexión</option>');
            Swal.fire('Error', 'Error de conexión al cargar productos: ' + error, 'error');
        },
        complete: function() {
            console.log('✅ Petición de productos completada');
        }
    });
}

    function cargarReceta(idProducto) {
        console.log('🔍 Cargando receta para producto:', idProducto);
        
        $.ajax({
            url: '/sistema/public/produccion?caso=obtenerReceta&id_producto=' + idProducto,
            type: 'GET',
            success: function(response) {
                console.log('📦 Respuesta receta:', response);
                
                if (response.status === 200 && response.data.length > 0) {
                    var html = '<h6 class="font-weight-bold">Materias Primas Requeridas:</h6>';
                    html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
                    html += '<thead><tr><th>Material</th><th>Cantidad</th><th>Unidad</th></tr></thead><tbody>';
                    
                    response.data.forEach(function(item) {
                        html += '<tr>';
                        html += '<td>' + item.NOMBRE_MP + '</td>';
                        html += '<td class="text-right">' + parseFloat(item.CANTIDAD_NECESARIA).toFixed(2) + '</td>';
                        html += '<td>' + item.UNIDAD + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    $('#infoReceta').html(html);
                    console.log('✅ Receta cargada exitosamente');
                } else {
                    $('#infoReceta').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No se encontró receta para este producto</div>');
                    console.warn('⚠️ No se encontró receta para el producto');
                }
            },
            error: function(xhr, status, error) {
                console.error('💥 Error cargando receta:', error);
                $('#infoReceta').html('<div class="alert alert-danger"><i class="fas fa-times"></i> Error al cargar la receta</div>');
            }
        });
    }

    function verificarStock() {
    var idProducto = $('#id_producto').val();
    var cantidad = $('#cantidad_planificada').val();

    console.log('🔍 Verificando stock para:', {
        idProducto: idProducto,
        cantidad: cantidad
    });

    if (!idProducto || idProducto === "") {
        Swal.fire('Advertencia', 'Seleccione un producto válido', 'warning');
        return;
    }

    if (!cantidad || cantidad <= 0) {
        Swal.fire('Advertencia', 'Ingrese una cantidad válida mayor a cero', 'warning');
        return;
    }

    $('#btnVerificarStock').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

    // MÉTODO SIMPLE CON JQUERY
    $.post('/sistema/public/produccion?caso=verificarStock', {
        id_producto: idProducto,
        cantidad_planificada: cantidad
    })
    .done(function(response) {
        console.log('✅ Respuesta exitosa:', response);
        
        $('#btnVerificarStock').prop('disabled', false).html('<i class="fas fa-search"></i> Verificar Stock Disponible');
        
        if (response.status === 200) {
            if (response.stock_suficiente) {
                $('#infoStock').removeClass('alert-warning alert-danger').addClass('alert-success');
                $('#infoStock').html('<i class="fas fa-check-circle"></i> ' + response.message.replace(/\\n/g, '<br>'));
                $('#btnCrearOrden').prop('disabled', false);
                mostrarInfoCostos(idProducto, cantidad);
            } else {
                $('#infoStock').removeClass('alert-warning alert-success').addClass('alert-danger');
                $('#infoStock').html('<i class="fas fa-times-circle"></i> ' + response.message.replace(/\\n/g, '<br>'));
                $('#btnCrearOrden').prop('disabled', true);
            }
        } else {
            Swal.fire('Error', response.message, 'error');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('💥 Error:', error);
        $('#btnVerificarStock').prop('disabled', false).html('<i class="fas fa-search"></i> Verificar Stock Disponible');
        Swal.fire('Error', 'Error de conexión: ' + error, 'error');
    });
}

    function mostrarInfoCostos(idProducto, cantidad) {
        console.log('💰 Mostrando costos para:', idProducto, 'Cantidad:', cantidad);
        
        $('#infoCostos').html(`
            <div class="row">
                <div class="col-12">
                    <p><strong>Cantidad planificada:</strong> ${cantidad} unidades</p>
                    <p><strong>Estado:</strong> <span class="badge badge-success">Stock suficiente</span></p>
                    <p class="text-success"><strong>Puede proceder con la creación de la orden</strong></p>
                </div>
            </div>
        `);
    }

    function crearProduccion() {
    var idProducto = $('#id_producto').val();
    var cantidad = $('#cantidad_planificada').val();
    var observacion = $('#observacion').val();
    
    console.log('🚀 Creando orden de producción:', {
        idProducto: idProducto,
        cantidad: cantidad,
        observacion: observacion
    });

    // Validar que el stock fue verificado y es suficiente
    if ($('#btnCrearOrden').prop('disabled')) {
        Swal.fire('Advertencia', 'Debe verificar el stock primero y asegurarse de que es suficiente', 'warning');
        return;
    }

    // Validar campos requeridos
    if (!idProducto || idProducto === "") {
        Swal.fire('Error', 'Debe seleccionar un producto', 'error');
        return;
    }

    if (!cantidad || cantidad <= 0) {
        Swal.fire('Error', 'La cantidad debe ser mayor a 0', 'error');
        return;
    }

    // Mostrar confirmación
    Swal.fire({
        title: '¿Crear Orden de Producción?',
        html: `<p>¿Está seguro de crear la orden de producción?</p>
              <p><strong>Producto:</strong> ${$('#id_producto option:selected').text()}</p>
              <p><strong>Cantidad:</strong> ${cantidad} unidades</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1cc88a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, Crear Orden',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Deshabilitar botón y mostrar loading
            $('#btnCrearOrden').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');
            
            // ENVIAR DATOS DE FORMA SIMPLE - como application/x-www-form-urlencoded
            var datos = {
                id_producto: idProducto,
                cantidad_planificada: cantidad,
                observacion: observacion
            };
            
            console.log('📤 Enviando datos:', datos);
            
            $.ajax({
                url: '/sistema/public/produccion?caso=crearOrdenProduccion',
                type: 'POST',
                data: datos, // jQuery automáticamente convierte a application/x-www-form-urlencoded
                success: function(response) {
                    console.log('📦 Respuesta creación orden:', response);
                    
                    if (response.success) {
                        // Éxito
                        Swal.fire({
                            title: '¡Orden Creada!',
                            html: `<p>${response.message}</p>
                                  <p><strong>Número de Orden:</strong> #${response.id_produccion}</p>`,
                            icon: 'success',
                            confirmButtonColor: '#1cc88a',
                            confirmButtonText: '<i class="fas fa-check"></i> Aceptar'
                        }).then(() => {
                            // Redirigir a la página de gestión de producción
                            window.location.href = '/sistema/public/gestion-produccion';
                        });
                    } else {
                        // Error
                        Swal.fire('Error', response.message, 'error');
                        $('#btnCrearOrden').prop('disabled', false).html('<i class="fas fa-save"></i> Crear Orden de Producción');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('💥 Error creando orden:', error);
                    console.error('💥 Detalles:', xhr.responseText);
                    
                    let errorMsg = 'Error de conexión al crear la orden';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        errorMsg = error;
                    }
                    
                    Swal.fire('Error', errorMsg, 'error');
                    $('#btnCrearOrden').prop('disabled', false).html('<i class="fas fa-save"></i> Crear Orden de Producción');
                }
            });
        }
    });
}

    </script>
