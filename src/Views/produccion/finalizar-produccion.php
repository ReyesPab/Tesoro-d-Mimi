<?php 
// Obtener ID de producción desde GET
$id_produccion = $_GET['id'] ?? 0;
if (!$id_produccion) {
    header('Location: /sistema/public/gestion-produccion');
    exit;
}

// Cargar layout común
require_once dirname(__DIR__) . '/partials/header.php';
require_once dirname(__DIR__) . '/partials/sidebar.php';
?>

<main id="main" class="main">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2><i class="fas fa-check-circle"></i> Finalizar Producción</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/sistema/public/inicio">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="/sistema/public/gestion-produccion">Producción</a></li>
                        <li class="breadcrumb-item active">Finalizar</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-clipboard-check"></i> Registrar Producción Terminada</h4>
                    </div>
                    <div class="card-body">
                        <form id="formFinalizarProduccion">
                            <input type="hidden" id="id_produccion" name="id_produccion" value="<?php echo $id_produccion; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Producto</label>
                                        <p class="form-control-plaintext" id="infoProducto">Cargando...</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Cantidad Planificada</label>
                                        <p class="form-control-plaintext" id="infoCantidadPlanificada">Cargando...</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Cantidad Acumulada</label>
                                        <p class="form-control-plaintext" id="infoCantidadAcumulada">0.00 unidades</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cantidad_real" class="font-weight-bold">Cantidad a Registrar *</label>
                                        <input type="number" class="form-control" id="cantidad_real" 
                                               name="cantidad_real" step="1" min="1" required 
                                               placeholder="Cantidad a añadir">
                                        <small class="form-text text-muted">Unidades en buen estado</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Diferencia vs Planificado</label>
                                        <p class="form-control-plaintext" id="infoDiferencia">-</p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones_finalizacion" class="font-weight-bold">Observaciones de la Producción</label>
                                <textarea class="form-control" id="observaciones_finalizacion" 
                                          name="observaciones_finalizacion" rows="3"
                                          placeholder="Notas sobre la producción, calidad, problemas encontrados, etc..."></textarea>
                            </div>

                            <!-- Descripción de pérdida (aparece sólo si hay déficit) -->
                            <div class="form-group" id="divDescripcionPerdida" style="display:none;">
                                <label for="descripcion_perdida" class="font-weight-bold">Descripción de la Pérdida</label>
                                <textarea class="form-control" id="descripcion_perdida" name="descripcion_perdida" rows="3" placeholder="Describa la causa o detalles de la pérdida (requerido cuando hay déficit)"></textarea>
                                <small class="form-text text-muted">Este campo se solicita cuando la producción final es menor a la planificada. Será guardado junto a la pérdida.</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success" id="btnFinalizar">
                                    <i class="fas fa-check"></i> Finalizar Producción
                                </button>
                                <a href="/sistema/public/gestion-produccion" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Orden</h4>
                    </div>
                    <div class="card-body">
                        <div id="infoOrden">
                            <p><i class="fas fa-spinner fa-spin"></i> Cargando información...</p>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Materias Primas Utilizadas</h4>
                    </div>
                    <div class="card-body">
                        <div id="infoMateriasPrimas">
                            <p><i class="fas fa-spinner fa-spin"></i> Cargando materiales...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Esperar a que jQuery esté disponible aunque el footer lo cargue después
    (function waitForJQ(){
        if (window.jQuery) {
            jQuery(function($){
                initFinalizarProduccion($);
            });
        } else {
            setTimeout(waitForJQ, 50);
        }
    })();

    // Variables globales
    let cantidadPlanificadaGlobal = 0;
    let cantidadProducidaActual = 0;

    function initFinalizarProduccion($){
        cargarDetalleProduccion();

        // Calcular diferencia al cambiar cantidad real
        $('#cantidad_real').on('input', function() {
            calcularDiferencia();
        });

        // Enviar formulario
        $('#formFinalizarProduccion').on('submit', function(e) {
            e.preventDefault();
            finalizarProduccion();
        });
    }

    function cargarDetalleProduccion() {
        $.ajax({
            url: '/sistema/public/produccion?caso=obtenerDetalleProduccion&id_produccion=<?php echo $id_produccion; ?>',
            type: 'GET',
            success: function(response) {
                if (response.status === 200) {
                    var produccion = response.data.produccion;
                    var materiasPrimas = response.data.materias_primas;
                    
                    // Actualizar información básica
                    $('#infoProducto').text(produccion.PRODUCTO);
                    cantidadPlanificadaGlobal = parseFloat(produccion.CANTIDAD_PLANIFICADA) || 0;
                    cantidadProducidaActual = parseFloat(produccion.CANTIDAD_PRODUCIDA) || 0;
                    $('#infoCantidadPlanificada').text(cantidadPlanificadaGlobal.toFixed(2) + ' unidades');
                    $('#infoCantidadAcumulada').text(cantidadProducidaActual.toFixed(2) + ' unidades');
                    
                    // Calcular diferencia inicial
                    calcularDiferencia();

                    // Actualizar información de la orden
                    var infoHtml = '<p><strong>Estado:</strong> <span class="badge badge-warning">' + produccion.ESTADO + '</span></p>';
                    infoHtml += '<p><strong>Fecha Inicio:</strong> ' + (produccion.FECHA_INICIO ? new Date(produccion.FECHA_INICIO).toLocaleString() : '-') + '</p>';
                    infoHtml += '<p><strong>Responsable:</strong> ' + (produccion.NOMBRE_USUARIO || 'Sistema') + '</p>';
                    if (produccion.OBSERVACION) {
                        infoHtml += '<p><strong>Observación:</strong> ' + produccion.OBSERVACION + '</p>';
                    }
                    $('#infoOrden').html(infoHtml);
                    
                    // Actualizar información de materias primas
                    if (materiasPrimas.length > 0) {
                        var mpHtml = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                        mpHtml += '<thead><tr><th>Material</th><th>Cantidad</th><th>Costo</th></tr></thead><tbody>';
                        
                        var costoTotal = 0;
                        materiasPrimas.forEach(function(mp) {
                            mpHtml += '<tr>';
                            mpHtml += '<td>' + mp.MATERIA_PRIMA + '</td>';
                            mpHtml += '<td class="text-right">' + parseFloat(mp.CANTIDAD_USADA).toFixed(2) + ' ' + mp.UNIDAD + '</td>';
                            mpHtml += '<td class="text-right">L. ' + parseFloat(mp.SUBTOTAL).toFixed(2) + '</td>';
                            mpHtml += '</tr>';
                            costoTotal += parseFloat(mp.SUBTOTAL);
                        });
                        
                        mpHtml += '</tbody></table>';
                        mpHtml += '<p class="font-weight-bold">Costo Total: L. ' + costoTotal.toFixed(2) + '</p></div>';
                        $('#infoMateriasPrimas').html(mpHtml);
                    } else {
                        $('#infoMateriasPrimas').html('<p class="text-muted">No se encontraron materiales utilizados</p>');
                    }
                    
                } else {
                    Swal.fire('Error', 'No se pudo cargar la información de la producción', 'error');
                    window.location.href = '/sistema/public/gestion-produccion';
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión al cargar información', 'error');
            }
        });
    }

    function calcularDiferencia() {
        const cantidadPlanificada = cantidadPlanificadaGlobal || 0;
        const cantidadRegistrar = parseFloat($('#cantidad_real').val()) || 0;
        const cantidadTotalProducida = cantidadProducidaActual + cantidadRegistrar;
        const diferencia = cantidadTotalProducida - cantidadPlanificada;
        
        var diferenciaHtml = '';
        // Mostrar campo de descripción sólo si hay déficit
        if (diferencia > 0) {
            diferenciaHtml = '<span class="text-success">+' + diferencia.toFixed(2) + ' unidades (Excedente)</span>';
            $('#divDescripcionPerdida').hide();
            $('#descripcion_perdida').prop('required', false);
        } else if (diferencia < 0) {
            diferenciaHtml = '<span class="text-danger">' + diferencia.toFixed(2) + ' unidades (Déficit)</span>';
            $('#divDescripcionPerdida').show();
            $('#descripcion_perdida').prop('required', true);
        } else {
            diferenciaHtml = '<span class="text-info">0 unidades (Exacto)</span>';
            $('#divDescripcionPerdida').hide();
            $('#descripcion_perdida').prop('required', false);
        }

        $('#infoDiferencia').html(diferenciaHtml);
    }

    function finalizarProduccion() {
        const cantidadReal = $('#cantidad_real').val();
        
        if (!cantidadReal || cantidadReal <= 0) {
            Swal.fire('Advertencia', 'Ingrese una cantidad real válida', 'warning');
            return;
        }

        $('#btnFinalizar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        // Preparar payload. Si hay déficit, incluir 'perdidas' JSON con la descripción
        var payload = {
            id_produccion: <?php echo $id_produccion; ?>,
            cantidad_buena: cantidadReal,
            observaciones: $('#observaciones_finalizacion').val() || ''
        };

        // Calcular déficit (positivo si hay pérdida)
        const cantidadRegistrar = parseFloat($('#cantidad_real').val()) || 0;
        const cantidadTotalProducida = cantidadProducidaActual + cantidadRegistrar;
        const deficit = (cantidadPlanificadaGlobal || 0) - cantidadTotalProducida;
        if (deficit > 0) {
            var descripcion = $('#descripcion_perdida').val() || '';
            if (descripcion.trim().length === 0) {
                Swal.fire('Advertencia', 'Por favor describa la pérdida antes de finalizar la producción', 'warning');
                $('#btnFinalizar').prop('disabled', false).html('<i class="fas fa-check"></i> Finalizar Producción');
                return;
            }

            var perdidas = [
                {
                    motivo: 'OTRO',
                    cantidad: parseFloat(deficit.toFixed(2)),
                    descripcion: descripcion
                }
            ];

            payload.perdidas = JSON.stringify(perdidas);
        }

        $.ajax({
            url: '/sistema/public/produccion?caso=finalizarProduccion',
            type: 'POST',
            data: payload,
            success: function(response) {
                if (response && response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Producción Finalizada!',
                        text: response.message,
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/sistema/public/gestion-produccion';
                        }
                    });
                } else {
                    const msg = (response && response.message) ? response.message : 'Error inesperado al finalizar producción';
                    Swal.fire('Error', msg, 'error');
                    $('#btnFinalizar').prop('disabled', false).html('<i class="fas fa-check"></i> Finalizar Producción');
                }
            },
            error: function(xhr) {
                let msg = 'Error de conexión al finalizar producción';
                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json && json.message) msg = json.message;
                } catch(e) {}
                Swal.fire('Error', msg, 'error');
                $('#btnFinalizar').prop('disabled', false).html('<i class="fas fa-check"></i> Finalizar Producción');
            }
        });
    }
    </script>

</main>

<?php 
// Pie de página estándar del layout (incluye scripts y cierre de body/html)
require_once dirname(__DIR__) . '/partials/footer.php'; 
?>