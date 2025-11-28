<?php
// Al inicio del archivo, después de tu código de sesión
session_start();
$usuario_nombre = $_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? 'Invitado';
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
            /* Orange to dark orange gradient with black text */
            background: linear-gradient(135deg, #FFA500 0%, #CC5500 100%) !important;
            color: #000 !important;
        }

        .card-header.bg-info {
            /* Orange to dark orange gradient with black text */
            background: linear-gradient(135deg, #FFA500 0%, #CC5500 100%) !important;
            color: #000 !important;
        }

        .card-header.bg-success {
            /* Orange to dark orange gradient with black text */
            background: linear-gradient(135deg, #FFA500 0%, #CC5500 100%) !important;
            color: #000 !important;
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
    <div class="container-fluid">
        <div class="page-header">
            <h2><i class="fas fa-plus-circle"></i> Crear Nuevo Producto</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/sistema/public/inicio">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/sistema/public/gestion-productos">Productos</a></li>
                    <li class="breadcrumb-item active">Crear Producto</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Datos del Producto</h4>
                    </div>
                    <div class="card-body">
                        <form id="formCrearProducto">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="font-weight-bold">Nombre del Producto *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required
                                               placeholder="Ej: Pan Integral">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unidad" class="font-weight-bold">Unidad de Medida *</label>
                                        <select class="form-control" id="unidad" name="unidad" required>
                                            <option value="">Seleccionar unidad...</option>
                                            <option value="kg">Kilogramos (kg)</option>
                                            <option value="g">Gramos (g)</option>
                                            <option value="l">Litros (l)</option>
                                            <option value="ml">Mililitros (ml)</option>
                                            <option value="unidades">Unidades</option>
                                            <option value="paquetes">Paquetes</option>
                                            <option value="cajas">Cajas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="precio" class="font-weight-bold">Precio por Unidad *</label>
                                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0.01" required
                                               placeholder="Ej: 25.50">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cantidad_minima" class="font-weight-bold">Cantidad Mínima *</label>
                                        <input type="number" class="form-control" id="cantidad_minima" name="cantidad_minima" step="0.01" min="0" required
                                               placeholder="Ej: 10">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cantidad_maxima" class="font-weight-bold">Cantidad Máxima *</label>
                                        <input type="number" class="form-control" id="cantidad_maxima" name="cantidad_maxima" step="0.01" min="0" required
                                               placeholder="Ej: 100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado" class="font-weight-bold">Estado *</label>
                                        <select class="form-control" id="estado" name="estado" required>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descripcion" class="font-weight-bold">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                          placeholder="Descripción detallada del producto..."></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success" id="btnCrearProducto">
                                    <i class="fas fa-save"></i> Crear Producto
                                </button>
                                <a href="/sistema/public/gestion-productos" class="btn btn-secondary">
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
                        <h4 class="mb-0"><i class="fas fa-info-circle"></i> Información</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb"></i> Consejos para crear productos:</h6>
                            <ul class="mb-0">
                                <li>El nombre debe ser descriptivo y único</li>
                                <li>Define correctamente la unidad de medida</li>
                                <li>Establece límites mínimos y máximos realistas</li>
                                <li>El precio debe incluir todos los costos</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-calculator"></i> Resumen</h4>
                    </div>
                    <div class="card-body">
                        <div id="resumenProducto">
                            <p class="text-muted">Complete el formulario para ver el resumen</p>
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
    $(document).ready(function() {
        console.log('✅ jQuery cargado correctamente');

        // Actualizar resumen en tiempo real
        $('#formCrearProducto input, #formCrearProducto select').on('input change', actualizarResumen);

        // Enviar formulario
        $('#formCrearProducto').submit(function(e) {
            e.preventDefault();
            crearProducto();
        });

        // Validación inicial
        actualizarResumen();
    });

    function actualizarResumen() {
        var nombre = $('#nombre').val();
        var precio = parseFloat($('#precio').val()) || 0;
        var unidad = $('#unidad').val();
        var min = parseFloat($('#cantidad_minima').val()) || 0;
        var max = parseFloat($('#cantidad_maxima').val()) || 0;

        if (nombre && precio > 0 && unidad && min >= 0 && max > 0) {
            $('#resumenProducto').html(`
                <div class="row">
                    <div class="col-12">
                        <p><strong>Producto:</strong> ${nombre}</p>
                        <p><strong>Precio:</strong> L ${precio.toFixed(2)} por ${unidad}</p>
                        <p><strong>Inventario:</strong> ${min} - ${max} ${unidad}</p>
                        <p class="text-success"><strong>Estado:</strong> Listo para crear</p>
                    </div>
                </div>
            `);
        } else {
            $('#resumenProducto').html('<p class="text-muted">Complete el formulario para ver el resumen</p>');
        }
    }

    function crearProducto() {
        var nombre = $('#nombre').val().trim();
        var unidad = $('#unidad').val();
        var precio = parseFloat($('#precio').val());
        var cantidad_minima = parseFloat($('#cantidad_minima').val());
        var cantidad_maxima = parseFloat($('#cantidad_maxima').val());
        var estado = $('#estado').val();
        var descripcion = $('#descripcion').val().trim();

        // Validaciones
        if (!nombre) {
            Swal.fire('Error', 'El nombre del producto es obligatorio', 'error');
            return;
        }

        if (!unidad) {
            Swal.fire('Error', 'Debe seleccionar una unidad de medida', 'error');
            return;
        }

        if (!precio || precio <= 0) {
            Swal.fire('Error', 'El precio debe ser mayor a 0', 'error');
            return;
        }

        if (cantidad_minima < 0) {
            Swal.fire('Error', 'La cantidad mínima no puede ser negativa', 'error');
            return;
        }

        if (cantidad_maxima <= 0) {
            Swal.fire('Error', 'La cantidad máxima debe ser mayor a 0', 'error');
            return;
        }

        if (cantidad_minima >= cantidad_maxima) {
            Swal.fire('Error', 'La cantidad mínima debe ser menor que la máxima', 'error');
            return;
        }

        // Preparar datos
        var datos = {
            nombre: nombre,
            unidad: unidad,
            precio: precio,
            cantidad_minima: cantidad_minima,
            cantidad_maxima: cantidad_maxima,
            estado: estado,
            descripcion: descripcion
        };

        console.log('🚀 Creando producto:', datos);

        // Mostrar loading
        $('#btnCrearProducto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

        // Enviar petición
        $.ajax({
            url: '/sistema/public/index.php?route=produccion&caso=crearProducto',
            type: 'POST',
            data: datos,
            success: function(response) {
                console.log('📦 Respuesta creación producto:', response);

                if (response.success) {
                    Swal.fire({
                        title: '¡Producto Creado!',
                        html: `<p>${response.message}</p>
                              <p><strong>ID del Producto:</strong> #${response.id_producto}</p>`,
                        icon: 'success',
                        confirmButtonColor: '#1cc88a',
                        confirmButtonText: '<i class="fas fa-check"></i> Aceptar'
                    }).then(() => {
                        // Redirigir a la página de gestión de productos
                        window.location.href = '/sistema/public/gestion-productos';
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#btnCrearProducto').prop('disabled', false).html('<i class="fas fa-save"></i> Crear Producto');
                }
            },
            error: function(xhr, status, error) {
                console.error('💥 Error creando producto:', error);
                console.error('💥 Detalles:', xhr.responseText);

                let errorMsg = 'Error de conexión al crear el producto';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg = error;
                }

                Swal.fire('Error', errorMsg, 'error');
                $('#btnCrearProducto').prop('disabled', false).html('<i class="fas fa-save"></i> Crear Producto');
            }
        });
    }
    </script>
</body>
</html>
