<?php 
// ========== VERIFICACIÓN DE PERMISOS ==========
require_once __DIR__ . '/../../config/SessionHelper.php';
require_once __DIR__ . '/../../config/PermisosHelper.php';
use App\config\SessionHelper;
use App\config\PermisosHelper;

PermisosHelper::requirePermission('GESTION_INVENTARIO', 'CONSULTAR');
$puedeEditar = PermisosHelper::checkPermission('GESTION_INVENTARIO', 'ACTUALIZAR');

require_once __DIR__ . '/../partials/header.php'; 
require_once __DIR__ . '/../partials/sidebar.php'; 
?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestión de Inventario - Materia Prima</h1>
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
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="bg-light p-3 rounded">
                            <div class="row g-3 align-items-end">
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
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="gestionInventario.limpiarFiltros()" title="Limpiar filtros">
                                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando inventario...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar el inventario. Verifica la consola para más detalles.
                </div>
                
                <div class="table-responsive">
                    <table id="tablaInventario" class="table table-hover" style="display: none; width: 100%;">
                        <thead>
                            <tr>
                                <th>Materia Prima</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>Mínimo</th>
                                <th>Máximo</th>
                                <th>Estado</th>
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

    <!-- Modal Ajustar Inventario -->
    <div class="modal fade" id="modalAjustarInventario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajustar Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAjustarInventario">
                        <input type="hidden" id="ajustar_id_materia_prima" name="ID_MATERIA_PRIMA">
                        <input type="hidden" id="ajustar_nombre_materia_prima" name="NOMBRE_MATERIA_PRIMA">
                        
                        <div class="mb-3">
                            <label class="form-label">Materia Prima:</label>
                            <p class="form-control-plaintext fw-bold" id="display_nombre_materia_prima"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stock Actual:</label>
                            <p class="form-control-plaintext" id="display_stock_actual"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_tipo_movimiento" class="form-label">Tipo de Movimiento:</label>
                            <select class="form-select" id="ajustar_tipo_movimiento" name="TIPO_MOVIMIENTO" required>
                                <option value="">Seleccionar...</option>
                                <option value="ENTRADA">Entrada</option>
                                <option value="SALIDA">Salida</option>
                                <option value="AJUSTE">Ajuste</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_cantidad" class="form-label">Cantidad:</label>
                            <input type="number" class="form-control" id="ajustar_cantidad" name="CANTIDAD" 
                                   step="0.01" min="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_descripcion" class="form-label">Descripción:</label>
                            <textarea class="form-control" id="ajustar_descripcion" name="DESCRIPCION" 
                                      rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarAjuste">Ajustar Inventario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Historial -->
    <div class="modal fade" id="modalVerHistorial" tabindex="-1">
         <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historial de Movimientos</h5>
                        <button type="button" class="btn btn-danger btn-sm me-2" id="btnExportarHistorialPDF">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="historial_id_materia_prima">
                    <p class="fw-bold" id="historial_nombre_materia_prima"></p>
                    
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
                    
                    <button type="button" class="btn btn-primary mb-3" id="btnFiltrarHistorial">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    
                    <div class="table-responsive">
                       <table class="table table-sm table-striped" id="tablaHistorial">
                                <thead>
                                    <tr>
                                        <th>Materia Prima</th>
                                        <th>Descripción</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Mínimo</th>
                                        <th>Máximo</th>
                                        <th>Estado</th>
                                        <th>Última Actualización</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpoHistorial">
                                    <!-- Historial se carga aquí -->
                                </tbody>
                            </table>

                    </div>
                  </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Solo jQuery y html2pdf -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
/* (estilos omitidos aquí por brevedad, idénticos a los del original) */
</style>

<script>
// Generador PDF reutilizable
function generarPDFCustom({titulo, subtitulo, columnas, datos, filename}) {
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
    const element = document.createElement('div');
    element.innerHTML = `
    <div class="container" id="contenido-pdf" style="width:100%;max-width:900px;margin:0 auto;background:#fff;border:1px solid #ddd;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        <div class="header" style="background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;padding:18px 22px;border-radius:8px 8px 0 0;">
            <div class="brand" style="display:flex;align-items:center;gap:14px;">
                <img src="${logoUrl}" alt="Logo" crossorigin="anonymous" style="width:54px;height:54px;border-radius:8px;object-fit:cover;background:#fff;">
                <div class="brand-text" style="display:flex;flex-direction:column;">
                    <h1 style="margin:0;font-size:24px;letter-spacing:.5px;">${titulo}</h1>
                    <h2 style="margin:2px 0 4px;font-size:14px;font-weight:normal;opacity:.9;">${subtitulo}</h2>
                    <div class="fecha" style="font-size:12px;opacity:.9;">Generado el: ${fechaActual}</div>
                </div>
            </div>
        </div>
        <div class="section" style="padding:18px 12px;">
            <div class="resumen" style="margin-bottom:15px;font-size:11px;padding:8px;background-color:#f8f9fa;border-left:4px solid #E38B29;">
                <strong>Total de registros: ${datos.length}</strong>
            </div>
            <div style="overflow-x:auto;width:100%;">
            <table style="width:100%;min-width:850px;border-collapse:collapse;margin-top:15px;font-size:9px;page-break-inside:auto;">
                <thead>
                    <tr>
                        ${columnas.map(col => `<th style='background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;padding:8px 4px;text-align:center;border:1px solid #B97222;font-size:10px;'>${col.title}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${datos.map((fila, idx) => `<tr>
                        ${columnas.map(col => {
                            let valor = fila[col.dataKey] ?? '';
                            
                            // Limpiar valores no deseados
                            if (valor === 'N/A' || valor === 'undefined' || valor === 'null') {
                                valor = '';
                            }
                            
                            // Formatear valores numéricos
                            if (['cantidad', 'minimo', 'maximo'].includes(col.dataKey) && !isNaN(parseFloat(valor))) {
                                valor = parseFloat(valor).toFixed(2);
                            }
                            
                            return `<td style='border:1px solid #dee2e6;padding:7px 4px;vertical-align:top;text-align:center;word-break:break-word;'>${valor}</td>`;
                        }).join('')}
                    </tr>`).join('')}
                    ${datos.length === 0 ? `<tr><td colspan='${columnas.length}' style='text-align:center;padding:14px;'>No hay registros.</td></tr>` : ''}
                </tbody>
            </table>
            </div>
        </div>
        <div class="footer" style="text-align:center;padding:12px 12px;color:#6c757d;font-size:12px;border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
    </div>`;
    const opt = {
        margin: [5,5,5,5],
        filename: filename || `reporte_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1.5, useCORS: true, scrollX: 0, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'letter', orientation: 'landscape' }
    };
    
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

class GestionInventario {
    constructor() {
        this.inventario = [];
        this.inventarioFiltrado = [];
        this.paginaActual = 1;
        this.itemsPorPagina = 10;
        this.historialActual = [];
        this.init();
    }

    async init() {
        await this.cargarInventario();
        this.configurarEventos();
    }

    async cargarInventario() {
        try {
            console.log("🔍 Iniciando carga de inventario...");
            
            const response = await fetch('index.php?route=inventario&caso=listar');
            console.log("📦 Respuesta HTTP:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            
            const text = await response.text();
            console.log("📄 Respuesta cruda:", text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("❌ Error parseando JSON:", e);
                throw new Error("Respuesta no es JSON válido");
            }
            
            console.log("📊 Datos JSON recibidos:", data);
            
            if (data && data.status === 200 && data.data && data.data.inventario) {
                console.log("📦 Items en inventario:", data.data.inventario.length);
                this.inventario = data.data.inventario;
                this.inventarioFiltrado = [...this.inventario];
                this.mostrarInventario();
            } else {
                console.error("❌ Estructura de datos inesperada:", data);
                throw new Error("Estructura de respuesta inesperada");
            }
            
        } catch (error) {
            console.error('❌ Error cargando inventario:', error);
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
            console.log("📭 No hay items en el inventario");
            errorMessage.textContent = "No hay registros en el inventario";
            errorMessage.style.display = 'block';
            tabla.style.display = 'none';
            paginacion.style.display = 'none';
            return;
        }

        console.log("📋 Mostrando", this.inventarioFiltrado.length, "items en la tabla");

        // Calcular paginación
        const totalPaginas = Math.ceil(this.inventarioFiltrado.length / this.itemsPorPagina);
        const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
        const fin = inicio + this.itemsPorPagina;
        const itemsPagina = this.inventarioFiltrado.slice(inicio, fin);

        // Limpiar tabla
        tbody.innerHTML = '';

        // Llenar tabla
        itemsPagina.forEach(item => {
            const estado = item.ESTADO_INVENTARIO || 'N/A';
            let badgeClass = 'bg-secondary';
            let texto = estado;
            
            switch((estado || '').toUpperCase()) {
                case 'CRITICO':
                    badgeClass = 'bg-danger';
                    texto = 'Crítico';
                    break;
                case 'BAJO':
                    badgeClass = 'bg-warning';
                    texto = 'Bajo';
                    break;
                case 'NORMAL':
                    badgeClass = 'bg-success';
                    texto = 'Normal';
                    break;
                case 'EXCESO':
                    badgeClass = 'bg-info';
                    texto = 'Exceso';
                    break;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><strong>${item.NOMBRE || 'N/A'}</strong></td>
                <td>${item.DESCRIPCION || 'Sin descripción'}</td>
                <td class="text-center"><span class="badge bg-secondary">${item.UNIDAD || 'N/A'}</span></td>
                <td class="text-end">${parseFloat(item.CANTIDAD || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.MINIMO || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.MAXIMO || 0).toFixed(2)}</td>
                <td class="text-center"><span class="badge ${badgeClass}">${texto}</span></td>
                <td>${item.FECHA_ACTUALIZACION ? new Date(item.FECHA_ACTUALIZACION).toLocaleString('es-ES') : 'N/A'}</td>
                <td>${item.ACTUALIZADO_POR || 'SISTEMA'}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-info btn-sm" 
                            onclick="gestionInventario.verHistorial(${item.ID_MATERIA_PRIMA}, '${(item.NOMBRE || '').replace(/'/g, "\\'")}')" 
                            title="Ver historial">
                        <i class="bi bi-clock-history"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Actualizar paginación
        this.actualizarPaginacion(totalPaginas);

        // Mostrar elementos
        tabla.style.display = 'table';
        paginacion.style.display = 'block';

        console.log("✅ Tabla de inventario cargada correctamente");
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
        
        // Exportación - botón principal
        document.getElementById('btnExportarPDF').addEventListener('click', () => {
            const tabla = document.getElementById('tablaInventario');
            const filas = Array.from(tabla.querySelectorAll('tbody tr'));
            // Definimos columnas como objetos { title, dataKey } para mantener consistencia
            const columnas = [
                { title: 'Materia Prima', dataKey: 'materia' },
                { title: 'Descripción', dataKey: 'descripcion' },
                { title: 'Unidad', dataKey: 'unidad' },
                { title: 'Cantidad', dataKey: 'cantidad' },
                { title: 'Mínimo', dataKey: 'minimo' },
                { title: 'Máximo', dataKey: 'maximo' },
                { title: 'Estado', dataKey: 'estado' },
                { title: 'Última Actualización', dataKey: 'fechaUltima' },
                { title: 'Actualizado Por', dataKey: 'actualizadoPor' }
            ];
            // Construimos datos como array de objetos (clave => valor) usando los tds existentes
            const datos = filas.map(tr => {
                const tds = Array.from(tr.querySelectorAll('td'));
                // Si la fila no tiene suficientes columnas, devolver null (será filtrada)
                if (tds.length < 7) return null;
                return {
                    materia: (tds[0]?.textContent || '').trim(),
                    descripcion: (tds[1]?.textContent || '').trim(),
                    unidad: (tds[2]?.textContent || '').trim(),
                    cantidad: (tds[3]?.textContent || '').trim(),
                    minimo: (tds[4]?.textContent || '').trim(),
                    maximo: (tds[5]?.textContent || '').trim(),
                    estado: (tds[6]?.textContent || '').trim(),
                    fechaUltima: (tds[7]?.textContent || '').trim(),
                    actualizadoPor: (tds[8]?.textContent || '').trim()
                };
            }).filter(Boolean);

            // Llamamos a la función generadora asegurando que columnas y datos tienen el formato esperado
            generarPDFCustom({
                titulo: 'Reporte de Inventario - Materia Prima',
                subtitulo: "Tesoro D' MIMI",
                columnas,
                datos,
                filename: `inventario_materia_prima_${new Date().toISOString().split('T')[0]}.pdf`
            });
        });

        // Filtros automáticos (sin botón aplicar)
        document.getElementById('filtroEstado').addEventListener('change', () => this.aplicarFiltros());
        document.getElementById('filtroBusqueda').addEventListener('input', () => this.aplicarFiltros());
        
        // Ajuste de inventario
        document.getElementById('btnConfirmarAjuste').addEventListener('click', () => this.confirmarAjuste());
        
        // Historial
        document.getElementById('btnFiltrarHistorial').addEventListener('click', () => this.filtrarHistorial());
        
        // Evento para Enter en búsqueda
        document.getElementById('filtroBusqueda').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.aplicarFiltros();
            }
        });
     
        document.getElementById('btnExportarHistorialPDF').addEventListener('click', () => {
            this.exportarHistorialPDF();
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

    ajustarInventario(idMateriaPrima, nombre, stockActual) {
        document.getElementById('ajustar_id_materia_prima').value = idMateriaPrima;
        document.getElementById('ajustar_nombre_materia_prima').value = nombre;
        document.getElementById('display_nombre_materia_prima').textContent = nombre;
        document.getElementById('display_stock_actual').textContent = parseFloat(stockActual).toFixed(2);
        
        // Limpiar formulario
        document.getElementById('ajustar_tipo_movimiento').value = '';
        document.getElementById('ajustar_cantidad').value = '';
        document.getElementById('ajustar_descripcion').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalAjustarInventario'));
        modal.show();
    }

    async confirmarAjuste() {
        const formData = {
            id_materia_prima: document.getElementById('ajustar_id_materia_prima').value,
            cantidad: parseFloat(document.getElementById('ajustar_cantidad').value),
            tipo_movimiento: document.getElementById('ajustar_tipo_movimiento').value,
            descripcion: document.getElementById('ajustar_descripcion').value.trim(),
            id_usuario: 1,
            actualizado_por: 'ADMIN'
        };

        console.log("📤 Enviando datos:", formData);

        // Validaciones
        if (!formData.tipo_movimiento) {
            this.mostrarAlerta('Seleccione un tipo de movimiento', 'warning');
            return;
        }

        if (!formData.cantidad || formData.cantidad <= 0 || isNaN(formData.cantidad)) {
            this.mostrarAlerta('Ingrese una cantidad válida mayor a 0', 'warning');
            return;
        }

        if (!formData.descripcion) {
            this.mostrarAlerta('Ingrese una descripción para el movimiento', 'warning');
            return;
        }

        try {
            const btnConfirmar = document.getElementById('btnConfirmarAjuste');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

            const response = await fetch('index.php?route=inventario&caso=actualizar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            console.log("📨 Status de respuesta:", response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("📊 Resultado JSON:", result);
            
            if (result.status === 200) {
                this.mostrarAlerta(result.message || 'Inventario actualizado correctamente', 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAjustarInventario'));
                modal.hide();
                await this.recargarInventario();
            } else {
                this.mostrarAlerta(result.message || 'Error al actualizar el inventario', 'error');
            }
        } catch (error) {
            console.error('❌ Error completo:', error);
            this.mostrarAlerta('Error de conexión: ' + error.message, 'error');
        } finally {
            const btnConfirmar = document.getElementById('btnConfirmarAjuste');
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Ajustar Inventario';
        }
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
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show alert-flotante`;
        alertDiv.innerHTML = `
            <i class="bi ${tipo === 'success' ? 'bi-check-circle' : tipo === 'error' ? 'bi-exclamation-circle' : tipo === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    async exportarPDF() {
        try {
            const response = await fetch('index.php?route=inventario&caso=exportar-pdf');
            const result = await response.json();
            
            if (result.status === 200) {
                this.generarPDF(result.data.inventario);
            } else {
                this.mostrarAlerta('Error al exportar el inventario', 'error');
            }
        } catch (error) {
            console.error('Error exportando PDF:', error);
            this.mostrarAlerta('Error de conexión al exportar', 'error');
        }
    }

    generarPDF(inventario) {
        // Formatear fecha actual
        const fechaActual = new Date().toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // Función para formatear fechas
        const formatearFecha = (fecha) => {
            if (!fecha) return 'N/A';
            return new Date(fecha).toLocaleDateString('es-ES');
        };

        // Función para determinar clase del estado
        const getEstadoClass = (estado) => {
            const estadoUpper = estado ? estado.toUpperCase() : '';
            switch(estadoUpper) {
                case 'CRITICO': return 'estado-critico';
                case 'BAJO': return 'estado-bajo';
                case 'NORMAL': return 'estado-normal';
                case 'EXCESO': return 'estado-exceso';
                default: return 'estado-normal';
            }
        };

        const element = document.createElement('div');
        element.innerHTML = `...`;
        // Para mantener el ejemplo conciso, usamos el generador custom más completo para exportar (generarPDFCustom)
        generarPDFCustom({
            titulo: 'Reporte de Inventario - Materia Prima',
            subtitulo: "Tesoro D' MIMI",
            columnas: [
                { title: '#', dataKey: 'idx' },
                { title: 'Materia Prima', dataKey: 'materia' },
                { title: 'Descripción', dataKey: 'descripcion' },
                { title: 'Unidad', dataKey: 'unidad' },
                { title: 'Cantidad', dataKey: 'cantidad' },
                { title: 'Mínimo', dataKey: 'minimo' },
                { title: 'Máximo', dataKey: 'maximo' },
                { title: 'Estado', dataKey: 'estado' },
                { title: 'Última Actualización', dataKey: 'fecha' }
            ],
            datos: inventario.map((it, i) => ({
                idx: i+1,
                materia: it.NOMBRE || 'N/A',
                descripcion: it.DESCRIPCION || 'Sin descripción',
                unidad: it.UNIDAD || 'N/A',
                cantidad: (Number(it.CANTIDAD)||0).toFixed(2),
                minimo: (Number(it.MINIMO)||0).toFixed(2),
                maximo: (Number(it.MAXIMO)||0).toFixed(2),
                estado: it.ESTADO_INVENTARIO || 'NORMAL',
                fecha: it.FECHA_ACTUALIZACION ? new Date(it.FECHA_ACTUALIZACION).toLocaleString('es-ES') : 'N/A'
            })),
            filename: `inventario_materia_prima_${new Date().toISOString().split('T')[0]}.pdf`
        });
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

    async verHistorial(idMateriaPrima, nombre) {
        document.getElementById('historial_id_materia_prima').value = idMateriaPrima;
        document.getElementById('historial_nombre_materia_prima').textContent = nombre;
        
        // Limpiar fechas
        const hoy = new Date();
        const haceUnMes = new Date();
        haceUnMes.setMonth(hoy.getMonth() - 1);
        
        document.getElementById('historial_fecha_inicio').value = haceUnMes.toISOString().split('T')[0];
        document.getElementById('historial_fecha_fin').value = hoy.toISOString().split('T')[0];
        
        // Cargar historial inicial (último mes)
        await this.filtrarHistorial();
        
        const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
        modal.show();
    }

async filtrarHistorial() {
    const idMateriaPrima = document.getElementById('historial_id_materia_prima').value;
    const fechaInicio = document.getElementById('historial_fecha_inicio').value;
    const fechaFin = document.getElementById('historial_fecha_fin').value;

    console.log("Filtrando historial:", { idMateriaPrima, fechaInicio, fechaFin });

    try {
        let params = `id_materia_prima=${idMateriaPrima}`;
        if (fechaInicio) params += `&fecha_inicio=${fechaInicio}`;
        if (fechaFin) params += `&fecha_fin=${fechaFin}`;

        const response = await fetch(`index.php?route=inventario&caso=historial&${params}`);
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const result = await response.json();
        console.log("Resultado historial COMPLETO:", result);

        let datosHistorial = [];

        // Detectar estructura (mantenemos tu lógica robusta)
        if (result.status === 200) {
            if (result.data?.historial) datosHistorial = result.data.historial;
            else if (Array.isArray(result.data)) datosHistorial = result.data;
            else if (result.historial) datosHistorial = result.historial;
            else if (Array.isArray(result)) datosHistorial = result;
            else {
                // Búsqueda recursiva (tu código original)
                const buscar = obj => {
                    for (let key in obj) {
                        if (Array.isArray(obj[key]) && obj[key].length > 0) return obj[key];
                        if (typeof obj[key] === 'object' && obj[key]) {
                            const encontrado = buscar(obj[key]);
                            if (encontrado) return encontrado;
                        }
                    }
                    return null;
                };
                datosHistorial = buscar(result) || [];
            }

            // ENRIQUECER CADA REGISTRO DEL HISTORIAL con datos del inventario principal
            const materiaPrimaActual = this.inventario.find(
                item => item.ID_MATERIA_PRIMA == idMateriaPrima
            );

            if (materiaPrimaActual) {
                datosHistorial = datosHistorial.map(mov => {
                    // Añadimos UNIDAD (siempre viene del inventario principal)
                    mov.UNIDAD = materiaPrimaActual.UNIDAD || 'N/A';

                    // Añadimos MÍNIMO y MÁXIMO por si no vienen en el movimiento
                    mov.MINIMO = mov.MINIMO ?? materiaPrimaActual.MINIMO;
                    mov.MAXIMO = mov.MAXIMO ?? materiaPrimaActual.MAXIMO;

                    // Calculamos el ESTADO si no viene explícito
                    const cantidad = parseFloat(mov.CANTIDAD || mov.cantidad_actual || 0);
                    const minimo = parseFloat(mov.MINIMO || 0);
                    const maximo = parseFloat(mov.MAXIMO || 0);

                    let estado = 'NORMAL';
                    if (cantidad <= minimo * 0.5) estado = 'CRITICO';
                    else if (cantidad < minimo) estado = 'BAJO';
                    else if (cantidad > maximo) estado = 'EXCESO';

                    mov.ESTADO_INVENTARIO = mov.ESTADO_INVENTARIO || estado;

                    return mov;
                });
            }

            this.historialActual = datosHistorial;
            this.mostrarHistorial(datosHistorial);
            this.mostrarAlerta(`Se encontraron ${datosHistorial.length} movimientos`, 'success');
        } else {
            this.historialActual = [];
            this.mostrarAlerta(result.message || 'No se encontraron movimientos', 'info');
            this.mostrarHistorial([]);
        }
    } catch (error) {
        console.error('Error cargando historial:', error);
        this.historialActual = [];
        this.mostrarAlerta('Error de conexión: ' + error.message, 'error');
        this.mostrarHistorial([]);
    }
}

mostrarHistorial(historial) {
    console.log("🔍 Datos recibidos para mostrar historial:", historial);
    
    this.historialActual = Array.isArray(historial) ? [...historial] : [];
    const cuerpoHistorial = document.getElementById('cuerpoHistorial');
    
    if (!historial || historial.length === 0) {
        this.historialActual = [];
        cuerpoHistorial.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i><br>
                    No hay registros de historial en el período seleccionado
                </td>
            </tr>
        `;
        return;
    }

    // Ordenar por fecha más reciente primero
    historial.sort((a, b) => {
        const fechaA = a.FECHA_ACTUALIZACION || a.FECHA_MOVIMIENTO || a.fecha_actualizacion || a.fecha_movimiento;
        const fechaB = b.FECHA_ACTUALIZACION || b.FECHA_MOVIMIENTO || b.fecha_actualizacion || b.fecha_movimiento;
        return new Date(fechaB) - new Date(fechaA);
    });

    cuerpoHistorial.innerHTML = historial.map(item => {
        console.log("📦 Procesando item del historial:", item);
        
        // Obtener nombre (probando diferentes posibles nombres de propiedad)
        const nombre = item.NOMBRE || item.MATERIA_PRIMA || item.nombre || item.materia_prima || 'N/A';
        
        // Obtener descripción
        const descripcion = item.DESCRIPCION || item.descripcion || 'Sin descripción';
        
        // Obtener unidad (probando diferentes posibles nombres de propiedad)
        const unidad = item.UNIDAD || item.UNIDAD_MEDIDA || item.unidad || item.unidad_medida || 'N/A';
        
        // Obtener cantidad (probando diferentes posibles nombres de propiedad)
        const cantidad = parseFloat(item.CANTIDAD || item.CANTIDAD_ACTUAL || item.cantidad || item.cantidad_actual || 0).toFixed(2);
        
        // Obtener mínimo y máximo
        const minimo = parseFloat(item.MINIMO || item.minimo || 0).toFixed(2);
        const maximo = parseFloat(item.MAXIMO || item.maximo || 0).toFixed(2);
        
        // Obtener estado (probando diferentes posibles nombres de propiedad)
        const estadoRaw = (item.ESTADO_INVENTARIO || item.ESTADO || item.estado_inventario || item.estado || '').toUpperCase();
        let badgeClass = 'bg-secondary';
        let textoEstado = 'Desconocido';

        switch (estadoRaw) {
            case 'CRITICO':
                badgeClass = 'bg-danger';
                textoEstado = 'Crítico';
                break;
            case 'BAJO':
                badgeClass = 'bg-warning';
                textoEstado = 'Bajo';
                break;
            case 'NORMAL':
                badgeClass = 'bg-success';
                textoEstado = 'Normal';
                break;
            case 'EXCESO':
                badgeClass = 'bg-info';
                textoEstado = 'Exceso';
                break;
            default: badgeClass = 'bg-secondary'; textoEstado = estadoRaw;
        }

        // Obtener fecha (probando diferentes posibles nombres de propiedad)
        const fechaRef = item.FECHA_ACTUALIZACION || item.FECHA_MOVIMIENTO || item.fecha_actualizacion || item.fecha_movimiento;
        const fechaFormateada = fechaRef ? new Date(fechaRef).toLocaleString('es-ES') : 'N/A';

        return `
            <tr>
                <td><strong>${nombre}</strong></td>
                <td>${descripcion}</td>
                <td class="text-center">
                    <span class="badge bg-secondary">${unidad}</span>
                </td>
                <td class="text-end">${cantidad}</td>
                <td class="text-end">${minimo}</td>
                <td class="text-end">${maximo}</td>
                <td class="text-center">
                    <span class="badge ${badgeClass}">${textoEstado}</span>
                </td>
                <td>${fechaFormateada}</td>
            </tr>
        `;
    }).join('');

    console.log("✅ Historial mostrado:", historial.length, "registros");
}

async exportarHistorialPDF() {
    try {
        console.log("🔄 Iniciando exportación de PDF...");
        console.log("📊 Historial actual para exportar:", this.historialActual);
        
        // Verificar que tenemos datos del historial
        if (!this.historialActual || this.historialActual.length === 0) {
            throw new Error('No hay datos de historial para exportar');
        }

        // Mapear columnas
        const columnas = [
            { title: 'Materia Prima', dataKey: 'materia' },
            { title: 'Descripción', dataKey: 'descripcion' },
            { title: 'Unidad', dataKey: 'unidad' },
            { title: 'Cantidad', dataKey: 'cantidad' },
            { title: 'Mínimo', dataKey: 'minimo' },
            { title: 'Máximo', dataKey: 'maximo' },
            { title: 'Estado', dataKey: 'estado' },
            { title: 'Última Actualización', dataKey: 'fechaUltima' }
        ];
        
        // Procesar datos del historialActual
        const datos = this.historialActual.map(item => {
            console.log("📦 Procesando item para PDF:", item);
            
            // Obtener nombre
            const nombre = item.NOMBRE || item.MATERIA_PRIMA || item.nombre || item.materia_prima || 'N/A';
            
            // Obtener descripción
            const descripcion = item.DESCRIPCION || item.descripcion || 'Sin descripción';
            
            // Obtener unidad
            const unidad = item.UNIDAD || item.UNIDAD_MEDIDA || item.unidad || item.unidad_medida || 'N/A';
            
            // Obtener cantidad
            const cantidad = parseFloat(item.CANTIDAD || item.CANTIDAD_ACTUAL || item.cantidad || item.cantidad_actual || 0).toFixed(2);
            
            // Obtener mínimo y máximo
            const minimo = parseFloat(item.MINIMO || item.minimo || 0).toFixed(2);
            const maximo = parseFloat(item.MAXIMO || item.maximo || 0).toFixed(2);
            
            // Obtener estado
            const estadoRaw = (item.ESTADO_INVENTARIO || item.ESTADO || item.estado_inventario || item.estado || '').toUpperCase();
            let textoEstado = 'N/A';

            switch (estadoRaw) {
                case 'CRITICO': textoEstado = 'Crítico'; break;
                case 'BAJO': textoEstado = 'Bajo'; break;
                case 'NORMAL': textoEstado = 'Normal'; break;
                case 'EXCESO': textoEstado = 'Exceso'; break;
                default: textoEstado = estadoRaw || 'N/A';
            }

            // Obtener fecha
            const fechaRef = item.FECHA_ACTUALIZACION || item.FECHA_MOVIMIENTO || item.fecha_actualizacion || item.fecha_movimiento;
            const fechaFormateada = fechaRef ? new Date(fechaRef).toLocaleString('es-ES') : 'N/A';

            return {
                materia: nombre,
                descripcion: descripcion,
                unidad: unidad,
                cantidad: cantidad,
                minimo: minimo,
                maximo: maximo,
                estado: textoEstado,
                fechaUltima: fechaFormateada
            };
        });

        console.log("📋 Datos procesados para PDF:", datos);
        
        if (datos.length === 0) {
            throw new Error('No se pudieron procesar los datos del historial');
        }

        const nombreMateriaPrima = document.getElementById('historial_nombre_materia_prima').textContent || 'Materia Prima';
        const fechaInicio = document.getElementById('historial_fecha_inicio').value;
        const fechaFin = document.getElementById('historial_fecha_fin').value;
        
        let textoPeriodo = 'Todo el historial disponible';
        if (fechaInicio && fechaFin) textoPeriodo = `Del ${fechaInicio} al ${fechaFin}`;
        else if (fechaInicio) textoPeriodo = `Desde el ${fechaInicio}`;
        else if (fechaFin) textoPeriodo = `Hasta el ${fechaFin}`;

        // Exportar PDF
        generarPDFCustom({
            titulo: 'Historial de Inventario - Materia Prima',
            subtitulo: `${nombreMateriaPrima} | ${textoPeriodo}`,
            columnas,
            datos,
            filename: `historial_${this.limpiarNombreArchivo(nombreMateriaPrima)}_${new Date().toISOString().split('T')[0]}.pdf`
        });

        // Registrar acción en bitácora
        if (typeof registrarBitacora === 'function') {
            registrarBitacora('Exportar PDF', `Exportó historial de materia prima: ${nombreMateriaPrima}`);
        }

        this.mostrarAlerta('PDF exportado correctamente', 'success');
        
    } catch (error) {
        console.error('❌ Error en exportarHistorialPDF:', error);
        this.mostrarAlerta(`Error: ${error.message}`, 'error');
    }
}

async exportarHistorialPDF() {
    try {
        console.log("🔄 Iniciando exportación de PDF...");
        
        // Verificar que tenemos datos del historial
        if (!this.historialActual || this.historialActual.length === 0) {
            throw new Error('No hay datos de historial para exportar');
        }

        console.log("📊 Datos del historial actual:", this.historialActual);

        // Mapear columnas y datos DIRECTAMENTE desde this.historialActual
        const columnas = [
            { title: 'Materia Prima', dataKey: 'materia' },
            { title: 'Descripción', dataKey: 'descripcion' },
            { title: 'Unidad', dataKey: 'unidad' },
            { title: 'Cantidad', dataKey: 'cantidad' },
            { title: 'Mínimo', dataKey: 'minimo' },
            { title: 'Máximo', dataKey: 'maximo' },
            { title: 'Estado', dataKey: 'estado' },
            { title: 'Última Actualización', dataKey: 'fechaUltima' }
        ];
        
        // Usar los datos DIRECTAMENTE del historialActual en lugar de extraer de la tabla HTML
        const datos = this.historialActual.map(item => {
            // Determinar el texto del estado
            const estadoRaw = (item.ESTADO_INVENTARIO || item.ESTADO || '').toUpperCase();
            let textoEstado = estadoRaw || 'N/A';
            
            switch (estadoRaw) {
                case 'CRITICO': textoEstado = 'Crítico'; break;
                case 'BAJO': textoEstado = 'Bajo'; break;
                case 'NORMAL': textoEstado = 'Normal'; break;
                case 'EXCESO': textoEstado = 'Exceso'; break;
            }

            return {
                materia: item.NOMBRE || item.MATERIA_PRIMA || 'N/A',
                descripcion: item.DESCRIPCION || 'Sin descripción',
                unidad: item.UNIDAD || item.UNIDAD_MEDIDA || 'N/A', // Usar directamente del objeto
                cantidad: parseFloat(item.CANTIDAD || item.CANTIDAD_ACTUAL || 0).toFixed(2),
                minimo: parseFloat(item.MINIMO || 0).toFixed(2),
                maximo: parseFloat(item.MAXIMO || 0).toFixed(2),
                estado: textoEstado, // Usar el texto formateado
                fechaUltima: item.FECHA_ACTUALIZACION || item.FECHA_MOVIMIENTO ? 
                    new Date(item.FECHA_ACTUALIZACION || item.FECHA_MOVIMIENTO).toLocaleString('es-ES') : 'N/A'
            };
        });

        console.log("📋 Datos procesados para PDF:", datos);
        
        if (datos.length === 0) {
            throw new Error('No se pudieron procesar los datos del historial');
        }

        const nombre = document.getElementById('historial_nombre_materia_prima').textContent || 'Materia Prima';
        const fechaInicio = document.getElementById('historial_fecha_inicio').value;
        const fechaFin = document.getElementById('historial_fecha_fin').value;
        let textoPeriodo = 'Todo el historial disponible';
        if (fechaInicio && fechaFin) textoPeriodo = `Del ${fechaInicio} al ${fechaFin}`;
        else if (fechaInicio) textoPeriodo = `Desde el ${fechaInicio}`;
        else if (fechaFin) textoPeriodo = `Hasta el ${fechaFin}`;

        // Exportar directamente
        generarPDFCustom({
            titulo: 'Historial de Inventario - Materia Prima',
            subtitulo: `${nombre} | ${textoPeriodo}`,
            columnas,
            datos,
            filename: `historial_${this.limpiarNombreArchivo(nombre)}_${new Date().toISOString().split('T')[0]}.pdf`
        });

        // Registrar acción en bitácora
        if (typeof registrarBitacora === 'function') {
            registrarBitacora('Exportar PDF', `Exportó historial de materia prima: ${nombre}`);
        }

        this.mostrarAlerta('PDF exportado correctamente', 'success');
        
    } catch (error) {
        console.error('❌ Error en exportarHistorialPDF:', error);
        this.mostrarAlerta(`Error: ${error.message}`, 'error');
    }
}

    generarHTMLParaPDFMateriaPrima() {
        // (método conservado si lo necesitas en futuro) - Implementación mínima aquí
        return '';
    }

    async verificarDatosHistorial() {
        const maxIntentos = 5;
        
        for (let i = 0; i < maxIntentos; i++) {
            const cuerpoHistorial = document.getElementById('cuerpoHistorial');
            const filas = cuerpoHistorial?.querySelectorAll('tr') || [];
            
            let datosValidos = false;
            filas.forEach(fila => {
                if (!fila.querySelector('.text-muted') && 
                    fila.cells.length >= 5 && 
                    fila.cells[0].textContent.trim() !== 'N/A' &&
                    fila.cells[0].textContent.trim() !== '') {
                    datosValidos = true;
                }
            });
            
            if (datosValidos) {
                console.log(`✅ Datos verificados en intento ${i + 1}`);
                return true;
            }
            
            if (i < maxIntentos - 1) {
                console.log(`⏳ Esperando datos... intento ${i + 1}`);
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
        
        return false;
    }

    async generarPDFConTimeout(element, opt, timeoutMs) {
        const pdfPromise = html2pdf().set(opt).from(element).save();
        const timeoutPromise = new Promise((_, reject) => 
            setTimeout(() => reject(new Error('Timeout generando PDF')), timeoutMs)
        );
        
        await Promise.race([pdfPromise, timeoutPromise]);
    }

    async exportarComoTexto() {
        try {
            const cuerpoHistorial = document.getElementById('cuerpoHistorial');
            const filas = cuerpoHistorial.querySelectorAll('tr');
            const nombre = document.getElementById('historial_nombre_producto')?.textContent || 
                          document.getElementById('historial_nombre_materia_prima')?.textContent || 
                          'Historial';
            
            let contenido = `HISTORIAL DE MOVIMIENTOS - ${nombre.toUpperCase()}\n\n`;
            contenido += "Fecha | Tipo | Cantidad | Usuario | Descripción\n";
            contenido += "------------------------------------------------\n";
            
            let contador = 0;
            filas.forEach(fila => {
                if (fila.querySelector('.text-muted')) return;
                
                const celdas = fila.cells;
                if (celdas.length >= 5) {
                    const fecha = celdas[0]?.textContent || '';
                    const tipo = celdas[1]?.textContent || '';
                    const cantidad = celdas[2]?.textContent || '';
                    const usuario = celdas[3]?.textContent || '';
                    const descripcion = celdas[4]?.textContent || '';
                    
                    contenido += `${fecha} | ${tipo} | ${cantidad} | ${usuario} | ${descripcion}\n`;
                    contador++;
                }
            });
            
            if (contador > 0) {
                const blob = new Blob([contenido], { type: 'text/plain;charset=utf-8' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `historial_${this.limpiarNombreArchivo(nombre)}.txt`;
                a.click();
                window.URL.revokeObjectURL(url);
                
                this.mostrarAlerta(`Se descargó un archivo de texto con ${contador} movimientos`, 'info');
            } else {
                this.mostrarAlerta('No hay datos para exportar', 'warning');
            }
            
        } catch (error) {
            console.error('Error en exportarComoTexto:', error);
            this.mostrarAlerta('No se pudo exportar el historial', 'error');
        }
    }

    limpiarNombreArchivo(nombre) {
        return (nombre || '').replace(/[^a-z0-9]/gi, '_').toLowerCase();
    }
}

// Inicializar la gestión de inventario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.gestionInventario = new GestionInventario();
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
