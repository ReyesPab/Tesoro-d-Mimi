<?php 
// ========== VERIFICACIÓN DE PERMISOS ==========
require_once __DIR__ . '/../../config/SessionHelper.php';
require_once __DIR__ . '/../../config/PermisosHelper.php';
use App\config\SessionHelper;
use App\config\PermisosHelper;
use App\models\permisosModel;

// Iniciar sesión de forma segura
SessionHelper::startSession();

$userId = SessionHelper::getUserId();

PermisosHelper::requirePermission('GESTION_INVENTARIO_PRODUCTOS', 'CONSULTAR');
$puedeEditar = permisosModel::verificarPermiso($userId, 'GESTION_INVENTARIO_PRODUCTOS', 'EDITAR');

require_once __DIR__ . '/../partials/header.php'; 
require_once __DIR__ . '/../partials/sidebar.php'; 
?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestión de Inventario - Productos</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Controles de ordenamiento y filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="ordenarPor" class="form-label">Ordenar por:</label>
                        <select id="ordenarPor" class="form-select" style="max-width: 200px;">
                            <option value="nombre">Nombre (A-Z)</option>
                            <option value="cantidad">Cantidad (Mayor a menor)</option>
                            <option value="estado">Estado</option>
                            <option value="fecha">Última Actualización</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <button id="btnExportarPDF" class="btn btn-danger me-2">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                        <button id="btnRefrescar" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Refrescar
                        </button>
                    </div>
                </div>

                <!-- Filtros adicionales -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="CRITICO">Crítico</option>
                            <option value="BAJO">Bajo</option>
                            <option value="NORMAL">Normal</option>
                            <option value="EXCESO">Exceso</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroBusqueda" class="form-label">Buscar:</label>
                        <input type="text" id="filtroBusqueda" class="form-control" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltros" class="btn btn-primary me-2">Aplicar Filtros</button>
                        <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">Limpiar</button>
                    </div>
                </div>

                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando inventario de productos...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar el inventario. Verifica la consola para más detalles.
                </div>
                
                <div class="table-responsive">
                    <table id="tablaInventario" class="table table-striped table-bordered" style="display: none; width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Última Actualización</th>
                                <th>Actualizado Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav id="paginacion" style="display: none;">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled" id="btnAnterior">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item" id="btnSiguiente">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Ver Historial -->
    <div class="modal fade" id="modalVerHistorial" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historial de Movimientos - Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="historial_id_producto">
                    <p class="fw-bold" id="historial_nombre_producto"></p>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="historial_fecha_inicio" class="form-label">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="historial_fecha_inicio">
                        </div>
                        <div class="col-md-6">
                            <label for="historial_fecha_fin" class="form-label">Fecha Fin:</label>
                            <input type="date" class="form-control" id="historial_fecha_fin">
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary mb-3" id="btnFiltrarHistorial" style="display: none;">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaHistorial">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Usuario</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoHistorial">
                                <!-- Historial se carga aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnExportarHistorialPDF"><i class="bi bi-file-pdf"></i> Exportar PDF</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Editar Producto -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarProducto">
                    <input type="hidden" id="editar_id_inventario_producto">
                    <input type="hidden" id="editar_id_producto">
                    
                    <div class="mb-3">
                        <label for="editar_nombre" class="form-label">Producto</label>
                        <input type="text" class="form-control" id="editar_nombre" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editar_descripcion" readonly></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_cantidad" class="form-label">Cantidad *</label>
                        <input type="number" class="form-control" id="editar_cantidad" step="0.01" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editar_precio" class="form-label">Precio *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="editar_precio" step="0.01" min="0.01" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEdicion">
                    <i class="bi bi-check-lg"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
</main>

<!-- Solo jQuery y html2pdf -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
 class GestionInventarioProductos {
    constructor() {
        this.inventario = [];
        this.inventarioFiltrado = [];
        this.paginaActual = 1;
        this.itemsPorPagina = 10;
        this.init();
    }

    async init() {
        await this.cargarInventario();
        this.configurarEventos();
    }

    async cargarInventario() {
        try {
            console.log("🔍 Iniciando carga de inventario de productos...");
            
            // Usar el caso corregido
            const response = await fetch('index.php?route=inventario&caso=listarProductos');
            console.log("📦 Respuesta HTTP:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log("📊 Datos JSON recibidos:", result);
            
            if (result && result.status === 200 && result.data) {
                console.log("📦 Productos en inventario:", result.data.length);
                this.inventario = result.data;
                this.inventarioFiltrado = [...this.inventario];
                this.mostrarInventario();
            } else {
                console.error("❌ Estructura de datos inesperada:", result);
                throw new Error(result.message || "Estructura de respuesta inesperada");
            }
            
        } catch (error) {
            console.error('❌ Error cargando inventario de productos:', error);
            this.mostrarError(error.message);
        }
    }

    mostrarError(mensaje) {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        
        loadingMessage.style.display = 'none';
        errorMessage.textContent = `Error: ${mensaje}`;
        errorMessage.style.display = 'block';
    }

    mostrarInventario() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaInventario');
        const tbody = tabla.querySelector('tbody');
        const paginacion = document.getElementById('paginacion');

        // Ocultar mensajes
        loadingMessage.style.display = 'none';
        errorMessage.style.display = 'none';

        if (!this.inventarioFiltrado || this.inventarioFiltrado.length === 0) {
            console.log("📭 No hay productos en el inventario");
            errorMessage.textContent = "No hay registros en el inventario de productos";
            errorMessage.style.display = 'block';
            tabla.style.display = 'none';
            paginacion.style.display = 'none';
            return;
        }

        console.log("📋 Mostrando", this.inventarioFiltrado.length, "productos en la tabla");

        // Calcular paginación
        const totalPaginas = Math.ceil(this.inventarioFiltrado.length / this.itemsPorPagina);
        const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
        const fin = inicio + this.itemsPorPagina;
        const itemsPagina = this.inventarioFiltrado.slice(inicio, fin);

        // Limpiar tabla
        tbody.innerHTML = '';

        // Llenar tabla
        itemsPagina.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.NOMBRE || 'N/A'}</td>
                <td>${item.DESCRIPCION || 'Sin descripción'}</td>
                <td class="text-end">$${parseFloat(item.PRECIO || 0).toFixed(2)}</td>
                <td class="text-end fw-bold">${parseFloat(item.CANTIDAD || 0).toFixed(2)}</td>
                <td>${item.FECHA_ACTUALIZACION_FORMATEADA || (item.FECHA_ACTUALIZACION ? new Date(item.FECHA_ACTUALIZACION).toLocaleString('es-ES') : 'N/A')}</td>
                <td>${item.ACTUALIZADO_POR || 'SISTEMA'}</td>
                <td>
    <div class="btn-group btn-group-sm" role="group">
        <?php if ($puedeEditar): ?>
        <button type="button" class="btn btn-outline-warning" 
                onclick="gestionInventarioProductos.editarProducto(
                    ${item.ID_INVENTARIO_PRODUCTO},
                    ${item.ID_PRODUCTO},
                    '${(item.NOMBRE || '').replace(/'/g, "\\'")}',
                    '${(item.DESCRIPCION || '').replace(/'/g, "\\'")}',
                    ${parseFloat(item.CANTIDAD || 0)},
                    ${parseFloat(item.PRECIO || 0)}
                )" 
                title="Editar">
            <i class="bi bi-pencil"></i>
        </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-info" 
                onclick="gestionInventarioProductos.verHistorial(
                    ${item.ID_PRODUCTO}, 
                    '${(item.NOMBRE || '').replace(/'/g, "\\'")}'
                )" 
                title="Ver Historial">
            <i class="bi bi-clock-history"></i>
        </button>
    </div>
</td>
            `;
            tbody.appendChild(row);
        });

        // Actualizar paginación
        this.actualizarPaginacion(totalPaginas);

        // Mostrar elementos
        tabla.style.display = 'table';
        paginacion.style.display = 'block';

        console.log("✅ Tabla de inventario de productos cargada correctamente");
    }

    actualizarPaginacion(totalPaginas) {
        const paginacion = document.getElementById('paginacion');
        const btnAnterior = document.getElementById('btnAnterior');
        const btnSiguiente = document.getElementById('btnSiguiente');
        
        // Actualizar estado de botones
        btnAnterior.classList.toggle('disabled', this.paginaActual === 1);
        btnSiguiente.classList.toggle('disabled', this.paginaActual === totalPaginas);
        
        // Actualizar números de página
        const paginationList = paginacion.querySelector('.pagination');
        let paginationHTML = `
            <li class="page-item ${this.paginaActual === 1 ? 'disabled' : ''}" id="btnAnterior">
                <a class="page-link" href="#" tabindex="-1">Anterior</a>
            </li>
        `;
        
        // Mostrar máximo 5 páginas
        let inicioPagina = Math.max(1, this.paginaActual - 2);
        let finPagina = Math.min(totalPaginas, inicioPagina + 4);
        
        if (finPagina - inicioPagina < 4) {
            inicioPagina = Math.max(1, finPagina - 4);
        }
        
        for (let i = inicioPagina; i <= finPagina; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                </li>
            `;
        }
        
        paginationHTML += `
            <li class="page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}" id="btnSiguiente">
                <a class="page-link" href="#">Siguiente</a>
            </li>
        `;
        
        paginationList.innerHTML = paginationHTML;
        
        // Re-asignar eventos
        this.configurarEventosPaginacion();
    }

    configurarEventosPaginacion() {
        document.getElementById('btnAnterior').addEventListener('click', (e) => {
            e.preventDefault();
            if (this.paginaActual > 1) {
                this.paginaActual--;
                this.mostrarInventario();
            }
        });
        
        document.getElementById('btnSiguiente').addEventListener('click', (e) => {
            e.preventDefault();
            const totalPaginas = Math.ceil(this.inventarioFiltrado.length / this.itemsPorPagina);
            if (this.paginaActual < totalPaginas) {
                this.paginaActual++;
                this.mostrarInventario();
            }
        });
        
        // Eventos para números de página
        document.querySelectorAll('.pagination .page-link[data-pagina]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.paginaActual = parseInt(e.target.getAttribute('data-pagina'));
                this.mostrarInventario();
            });
        });
    }

    configurarEventos() {
        // Ordenamiento
        document.getElementById('ordenarPor').addEventListener('change', (e) => this.ordenarInventario(e.target.value));
        document.getElementById('btnRefrescar').addEventListener('click', () => this.recargarInventario());
        
        // Exportación
        document.getElementById('btnExportarPDF').addEventListener('click', () => this.exportarPDF());
        
        // Filtros
        document.getElementById('btnAplicarFiltros').addEventListener('click', () => this.aplicarFiltros());
        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => this.limpiarFiltros());
        
        // Historial
        document.getElementById('btnFiltrarHistorial').addEventListener('click', () => this.filtrarHistorial());
        document.getElementById('btnExportarHistorialPDF').addEventListener('click', () => this.exportarHistorialPDF());
// Evento para guardar edición
document.getElementById('btnGuardarEdicion').addEventListener('click', () => this.guardarEdicion());
        // Filtrado automático de historial por fecha
        const fechaInicioHistorial = document.getElementById('historial_fecha_inicio');
        const fechaFinHistorial = document.getElementById('historial_fecha_fin');

        const filtrarSiAmbasFechas = () => {
            if (fechaInicioHistorial.value && fechaFinHistorial.value) {
                this.filtrarHistorial();
            }
        };

        fechaInicioHistorial.addEventListener('change', filtrarSiAmbasFechas);
        fechaFinHistorial.addEventListener('change', filtrarSiAmbasFechas);
        
        // Evento para Enter en búsqueda
        document.getElementById('filtroBusqueda').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.aplicarFiltros();
            }
        });
    }

    ordenarInventario(criterio) {
        switch(criterio) {
            case 'nombre':
                this.inventarioFiltrado.sort((a, b) => (a.NOMBRE || '').localeCompare(b.NOMBRE || ''));
                break;
            case 'cantidad':
                this.inventarioFiltrado.sort((a, b) => (b.CANTIDAD || 0) - (a.CANTIDAD || 0));
                break;
            case 'estado':
                this.inventarioFiltrado.sort((a, b) => (a.ESTADO_INVENTARIO || '').localeCompare(b.ESTADO_INVENTARIO || ''));
                break;
            case 'fecha':
                this.inventarioFiltrado.sort((a, b) => new Date(b.FECHA_ACTUALIZACION || 0) - new Date(a.FECHA_ACTUALIZACION || 0));
                break;
        }
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    aplicarFiltros() {
        const filtroEstado = document.getElementById('filtroEstado').value;
        const filtroBusqueda = document.getElementById('filtroBusqueda').value.toLowerCase();
        
        this.inventarioFiltrado = this.inventario.filter(item => {
            const estadoMatch = !filtroEstado || (item.ESTADO_INVENTARIO || '').toLowerCase() === filtroEstado.toLowerCase();
            const busquedaMatch = !filtroBusqueda || (item.NOMBRE || '').toLowerCase().includes(filtroBusqueda);
            
            return estadoMatch && busquedaMatch;
        });
        
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    limpiarFiltros() {
        document.getElementById('filtroEstado').value = '';
        document.getElementById('filtroBusqueda').value = '';
        this.inventarioFiltrado = [...this.inventario];
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    mostrarAlerta(mensaje, tipo = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[tipo] || 'alert-info';

        // Crear alerta temporal
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insertar al inicio del contenido principal
        const main = document.querySelector('main');
        main.insertBefore(alertDiv, main.firstChild);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    async exportarPDF() {
        try {
            // Usar el caso corregido para exportar
          const response = await fetch('index.php?route=inventario&caso=exportarPdfProductos');
            const result = await response.json();
            
            if (result.status === 200) {
                this.generarPDF(result.data.inventario || result.data);
            } else {
                this.mostrarAlerta('Error al exportar el inventario', 'error');
            }
        } catch (error) {
            console.error('Error exportando PDF:', error);
            this.mostrarAlerta('Error de conexión al exportar', 'error');
        }
    }

generarPDF(inventario) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    const getEstadoClass = (estado) => {
        const estadoUpper = estado ? estado.toUpperCase() : '';
        switch(estadoUpper) {
            case 'CRITICO': return 'bg-danger';
            case 'BAJO': return 'bg-warning';
            case 'NORMAL': return 'bg-success';
            case 'EXCESO': return 'bg-info';
            default: return 'bg-success';
        }
    };

    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;

    const element = document.createElement('div');
    element.innerHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Inventario - Productos</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 20px; font-size: 10px; color: #333; }
            .container { max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
            .header { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #ffffff; 
                padding: 18px 22px; 
                border-radius: 8px 8px 0 0; 
            }
            .brand { display: flex; align-items: center; gap: 14px; }
            .brand img { width: 54px; height: 54px; border-radius: 8px; object-fit: cover; background: #fff; }
            .brand-text { display: flex; flex-direction: column; }
            .header h1 { margin: 0; font-size: 24px; letter-spacing: .5px; }
            .header h2 { margin: 2px 0 4px; font-size: 14px; font-weight: normal; opacity: .9; }
            .header .fecha { font-size: 12px; opacity: .9; }

            .section { padding: 18px 24px; }
            .resumen { margin-bottom: 15px; font-size: 11px; padding: 8px; background-color: #f8f9fa; border-left: 4px solid #E38B29; }
            
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; page-break-inside: auto; }
            th { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #fff; 
                padding: 10px 8px; 
                text-align: left; 
                border: 1px solid #B97222; 
            }
            td { border: 1px solid #dee2e6; padding: 9px 8px; vertical-align: top; }
            tr:nth-child(even) { background-color: #fdf8f2; }

            .badge { color: white; padding: 3px 6px; border-radius: 12px; font-size: 8px; font-weight: bold; }
            .bg-success { background-color: #28a745; }
            .bg-danger { background-color: #dc3545; }
            .bg-warning { background-color: #ffc107; }
            .bg-info { background-color: #0dcaf0; }

            .footer { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 10px 24px; 
                color: #6c757d; 
                font-size: 9px; 
                border-top: 1px solid #dee2e6;
                background-color: #f8f9fa;
                flex-wrap: nowrap;
            }
            .footer-company { 
                font-style: italic; 
                margin-right: 15px;
            }
            .footer-download { 
                margin-right: 15px;
            }
            .footer-pagination { 
                font-weight: bold; 
                white-space: nowrap;
            }
            </style>
        </head>
         <body>
        <div class="container" id="contenido-pdf">
          <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Reporte de Inventario - Productos</h1>
                    <h2>Tesoro D' MIMI</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="resumen">
                <strong>Total de productos en inventario: ${inventario.length}</strong>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Producto</th>
                        <th width="30%">Descripción</th>
                        <th width="15%" class="text-end">Precio</th>
                        <th width="15%" class="text-end">Cantidad</th>
                        <th width="15%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    ${inventario.map((item, index) => {
                        const estadoClass = getEstadoClass(item.ESTADO_INVENTARIO);
                        const estadoTexto = item.ESTADO_INVENTARIO || 'NORMAL';
                        
                        return `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td><strong>${item.NOMBRE || 'N/A'}</strong></td>
                            <td>${item.DESCRIPCION || 'Sin descripción'}</td>
                            <td class="text-end">$${parseFloat(item.PRECIO || 0).toFixed(2)}</td>
                            <td class="text-end">${parseFloat(item.CANTIDAD || 0).toFixed(2)}</td>
                            <td class="text-center"><span class="badge ${estadoClass}">${estadoTexto}</span></td>
                        </tr>
                        `;
                    }).join('')}
                    ${inventario.length === 0 ? '<tr><td colspan="6" style="text-align:center; padding:14px;">No hay productos en el inventario.</td></tr>' : ''}
                </tbody>
            </table>
        </div>
        <div class="footer">
           <div class="footer" style="text-align:center;padding:12px 12px;color:#6c757d;font-size:12px;border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
        </div>
        </body>
        </html>
        `;

     const opt = {
        margin: [8, 8, 15, 8],
        filename: `inventario_productos_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'letter', orientation: 'landscape' }
     };

  
        // Configurar numeración de páginas automática
    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function(pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        
        // Agregar número de página a cada página
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(8);
            pdf.setTextColor(100);
            // Posición: esquina inferior derecha
            pdf.text(`Página ${i} de ${totalPages}`, pdf.internal.pageSize.getWidth() - 25, pdf.internal.pageSize.getHeight() - 10);
        }
    } ).save();
}

    async exportarHistorialPDF() {
        const idProducto = document.getElementById('historial_id_producto').value;
        const nombreProducto = document.getElementById('historial_nombre_producto').textContent;
        const fechaInicio = document.getElementById('historial_fecha_inicio').value;
        const fechaFin = document.getElementById('historial_fecha_fin').value;

        try {
            let params = `id_producto=${idProducto}`;
            if (fechaInicio) params += `&fecha_inicio=${fechaInicio}`;
            if (fechaFin) params += `&fecha_fin=${fechaFin}`;

            const response = await fetch(`index.php?route=inventario&caso=historialProducto&${params}`);
            const result = await response.json();

            if (result.status === 200 && result.data) {
                this.generarHistorialPDF(result.data, nombreProducto, fechaInicio, fechaFin);
            } else {
                this.mostrarAlerta('No se pudo obtener el historial para exportar.', 'error');
            }
        } catch (error) {
            console.error('Error exportando historial PDF:', error);
            this.mostrarAlerta('Error de conexión al exportar el historial.', 'error');
        }
    }
editarProducto(idInventario, idProducto, nombre, descripcion, cantidad, precio) {
    // Llenar el formulario del modal
    document.getElementById('editar_id_inventario_producto').value = idInventario;
    document.getElementById('editar_id_producto').value = idProducto;
    document.getElementById('editar_nombre').value = nombre;
    document.getElementById('editar_descripcion').value = descripcion;
    document.getElementById('editar_cantidad').value = cantidad;
    document.getElementById('editar_precio').value = precio;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
    modal.show();
}

async guardarEdicion() {
    const idInventario = document.getElementById('editar_id_inventario_producto').value;
    const cantidad = document.getElementById('editar_cantidad').value;
    const precio = document.getElementById('editar_precio').value;
    
    // Validaciones
    if (!cantidad || cantidad < 0) {
        this.mostrarAlerta('La cantidad no puede ser negativa', 'error');
        return;
    }
    
    if (!precio || precio <= 0) {
        this.mostrarAlerta('El precio debe ser mayor a 0', 'error');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id_inventario_producto', idInventario);
        formData.append('cantidad', cantidad);
        formData.append('precio', precio);
        
        const response = await fetch('index.php?route=inventario&caso=editarInventarioProducto', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            this.mostrarAlerta(result.message, 'success');
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
            // Recargar inventario
            await this.recargarInventario();
        } else {
            this.mostrarAlerta(result.message, 'error');
        }
    } catch (error) {
        console.error('Error guardando edición:', error);
        this.mostrarAlerta('Error de conexión al guardar cambios', 'error');
    }
}
    generarHistorialPDF(historial, nombreProducto, fechaInicio, fechaFin) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });

    // Ordenar por fecha más reciente primero
    historial.sort((a, b) => new Date(b.FECHA_MOVIMIENTO) - new Date(a.FECHA_MOVIMIENTO));

    // Obtener la URL base del sitio
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;

    const element = document.createElement('div');
    element.innerHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Historial de Movimientos - ${nombreProducto}</title>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                background-color: #f5f7fa; 
                margin: 0; 
                padding: 15px; 
                font-size: 10px; 
                color: #333; 
            }
            .container { 
                max-width: 100%; 
                margin: 0 auto; 
                background: #fff; 
                border: 1px solid #ddd; 
                border-radius: 8px; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            }
            .header { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #ffffff; 
                padding: 15px 20px; 
                border-radius: 8px 8px 0 0; 
            }
            .brand { 
                display: flex; 
                align-items: center; 
                gap: 12px; 
            }
            .brand img { 
                width: 50px; 
                height: 50px; 
                border-radius: 8px; 
                object-fit: cover; 
                background: #fff; 
            }
            .brand-text { 
                display: flex; 
                flex-direction: column; 
            }
            .header h1 { 
                margin: 0; 
                font-size: 20px; 
                letter-spacing: .5px; 
            }
            .header h2 { 
                margin: 2px 0 4px; 
                font-size: 12px; 
                font-weight: normal; 
                opacity: .9; 
            }
            .header .fecha { 
                font-size: 10px; 
                opacity: .9; 
            }

            .section { 
                padding: 15px 20px; 
            }
            .info-producto { 
                margin-bottom: 12px; 
                font-size: 10px; 
                padding: 8px; 
                background-color: #f8f9fa; 
                border-left: 4px solid #E38B29; 
                border-radius: 4px;
            }
            
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 12px; 
                font-size: 8px; 
                page-break-inside: auto; 
            }
            th { 
                background: linear-gradient(90deg, #D7A86E, #E38B29);
                color: #fff; 
                padding: 8px 6px; 
                text-align: left; 
                border: 1px solid #B97222; 
                font-weight: bold;
            }
            td { 
                border: 1px solid #dee2e6; 
                padding: 7px 6px; 
                vertical-align: top; 
                word-wrap: break-word;
            }
            tr:nth-child(even) { 
                background-color: #fdf8f2; 
            }

            .badge { 
                color: white; 
                padding: 2px 5px; 
                border-radius: 10px; 
                font-size: 7px; 
                font-weight: bold; 
                display: inline-block;
                text-align: center;
                min-width: 50px;
            }
            .bg-success { background-color: #28a745; }
            .bg-danger { background-color: #dc3545; }
            .bg-info { background-color: #0dcaf0; }

            .footer { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 8px 20px; 
                color: #6c757d; 
                font-size: 8px; 
                border-top: 1px solid #dee2e6;
                background-color: #f8f9fa;
                flex-wrap: nowrap;
            }
            .footer-company { 
                font-style: italic; 
                margin-right: 10px;
            }
            .footer-download { 
                margin-right: 10px;
            }
            .footer-pagination { 
                font-weight: bold; 
                white-space: nowrap;
            }
            .text-end { text-align: right; }
            .text-center { text-align: center; }
        </style>
    </head>
    <body>
    <div class="container" id="contenido-pdf">
        <div class="header">
            <div class="brand">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous">
                <div class="brand-text">
                    <h1>Historial de Movimientos</h1>
                    <h2>Tesoro D' MIMI</h2>
                    <div class="fecha">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="info-producto">
                <strong>Producto:</strong> ${nombreProducto}<br>
                <strong>Período del reporte:</strong> ${fechaInicio || 'Todo el historial'} - ${fechaFin || 'Hasta actualidad'}
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="15%">Fecha</th>
                        <th width="8%">Tipo</th>
                        <th width="10%" class="text-end">Cantidad</th>
                        <th width="15%">Usuario</th>
                        <th width="52%">Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    ${historial.map(mov => `
                        <tr>
                            <td>${mov.FECHA_MOVIMIENTO_FORMATEADA || (mov.FECHA_MOVIMIENTO ? new Date(mov.FECHA_MOVIMIENTO).toLocaleDateString('es-ES') : 'N/A')}</td>
                            <td class="text-center">
                                <span class="badge ${mov.TIPO_MOVIMIENTO === 'ENTRADA' ? 'bg-success' : mov.TIPO_MOVIMIENTO === 'SALIDA' ? 'bg-danger' : 'bg-info'}">
                                    ${mov.TIPO_MOVIMIENTO || 'N/A'}
                                </span>
                            </td>
                            <td class="text-end">${parseFloat(mov.CANTIDAD || 0).toFixed(2)}</td>
                            <td>${mov.USUARIO || mov.CREADO_POR || 'SISTEMA'}</td>
                            <td>${mov.DESCRIPCION || 'Sin descripción'}</td>
                        </tr>
                    `).join('')}
                    ${historial.length === 0 ? 
                        '<tr><td colspan="5" class="text-center" style="padding:12px; font-style:italic;">No hay movimientos en el período seleccionado.</td></tr>' : 
                        ''}
                </tbody>
            </table>
        </div>
        
        <div class="footer">
           <div class="footer" style="text-align:center;padding:12px 12px;color:#6c757d;font-size:12px;border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
    </div>
    </body>
    </html>
    `;

    const opt = {
        margin: [10, 10, 15, 10],
        filename: `historial_${nombreProducto.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true,
            logging: false
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'letter', 
            orientation: 'landscape' 
        }
    };

    // Configurar numeración de páginas automática
     html2pdf().set(opt).from(element).toPdf().get('pdf').then(function(pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        
        // Agregar número de página a cada página
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(8);
            pdf.setTextColor(100);
            // Posición: esquina inferior derecha
            pdf.text(`Página ${i} de ${totalPages}`, pdf.internal.pageSize.getWidth() - 25, pdf.internal.pageSize.getHeight() - 10);
        }
    }).save();
}

    async recargarInventario() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaInventario');
        const paginacion = document.getElementById('paginacion');
        
        loadingMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        tabla.style.display = 'none';
        paginacion.style.display = 'none';
        
        await this.cargarInventario();
    }

    async verHistorial(idProducto, nombre) {
        document.getElementById('historial_id_producto').value = idProducto;
        document.getElementById('historial_nombre_producto').textContent = nombre;
        
        // Limpiar fechas para que no se aplique filtro de fecha inicialmente
        document.getElementById('historial_fecha_inicio').value = '';
        document.getElementById('historial_fecha_fin').value = '';
        
        // Cargar todo el historial sin filtro de fecha
        await this.filtrarHistorial();
        
        const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
        modal.show();
    }

    async filtrarHistorial() {
        const idProducto = document.getElementById('historial_id_producto').value;
        const fechaInicio = document.getElementById('historial_fecha_inicio').value;
        const fechaFin = document.getElementById('historial_fecha_fin').value;
        
        console.log("🔍 Filtrando historial de producto:", { idProducto, fechaInicio, fechaFin });
        
        try {
            // Construir parámetros de consulta
            let params = `id_producto=${idProducto}`;
            
            if (fechaInicio) {
                params += `&fecha_inicio=${fechaInicio}`;
            }
            
            if (fechaFin) {
                params += `&fecha_fin=${fechaFin}`;
            }
            
            console.log("📤 Parámetros de consulta:", params);
            
            // Usar el caso corregido para historial
            const response = await fetch(`index.php?route=inventario&caso=historialProducto&${params}`);
            console.log("📦 Respuesta HTTP:", response.status);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const result = await response.json();
            console.log("📊 Resultado historial:", result);
            
            if (result.status === 200 && result.data) {
                this.mostrarHistorial(result.data);
                this.mostrarAlerta(`Se encontraron ${result.data.length} movimientos`, 'success');
            } else {
                console.warn("⚠️ Estructura de respuesta inesperada:", result);
                this.mostrarAlerta(result.message || 'No se encontraron movimientos', 'info');
                this.mostrarHistorial([]);
            }
        } catch (error) {
            console.error('❌ Error cargando historial:', error);
            this.mostrarAlerta('Error de conexión al cargar historial: ' + error.message, 'error');
            this.mostrarHistorial([]);
        }
    }

    mostrarHistorial(historial) {
        const cuerpoHistorial = document.getElementById('cuerpoHistorial');
        
        if (!historial || historial.length === 0) {
            cuerpoHistorial.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        <i class="bi bi-inbox"></i><br>
                        No hay movimientos registrados en el período seleccionado
                    </td>
                </tr>
            `;
            return;
        }
        
        // Ordenar por fecha más reciente primero
        historial.sort((a, b) => new Date(b.FECHA_MOVIMIENTO) - new Date(a.FECHA_MOVIMIENTO));
        
        cuerpoHistorial.innerHTML = historial.map(movimiento => `
            <tr>
                <td>${movimiento.FECHA_MOVIMIENTO_FORMATEADA || (movimiento.FECHA_MOVIMIENTO ? new Date(movimiento.FECHA_MOVIMIENTO).toLocaleString('es-ES') : 'N/A')}</td>
                <td>
                    <span class="badge ${
                        movimiento.TIPO_MOVIMIENTO === 'ENTRADA' ? 'bg-success' : 
                        movimiento.TIPO_MOVIMIENTO === 'SALIDA' ? 'bg-danger' : 'bg-info'
                    }">
                        ${movimiento.TIPO_MOVIMIENTO || 'N/A'}
                    </span>
                </td>
                <td class="text-end">${parseFloat(movimiento.CANTIDAD || 0).toFixed(2)}</td>
                <td>${movimiento.USUARIO || movimiento.CREADO_POR || 'SISTEMA'}</td>
                <td>${movimiento.DESCRIPCION || 'Sin descripción'}</td>
            </tr>
        `).join('');
        
        console.log("✅ Historial mostrado:", historial.length, "movimientos");
    }
}

// Inicializar la gestión de inventario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.gestionInventarioProductos = new GestionInventarioProductos();
});

</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>