<?php 
require_once 'partials/header.php'; 
require_once 'partials/sidebar.php'; 
?>
<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Bitácora del Sistema</h1>
            <div class="d-flex gap-2">
                <button id="btnExportar" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                </button>
                <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Limpiar Filtros
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filtroUsuario" class="form-label">Usuario</label>
                        <select id="filtroUsuario" class="form-select">
                            <option value="">Todos los usuarios</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroAccion" class="form-label">Acción</label>
                        <select id="filtroAccion" class="form-select">
                            <option value="">Todas las acciones</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
                        <input type="date" id="filtroFechaInicio" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
                        <input type="date" id="filtroFechaFin" class="form-control">
                    </div>
                </div>

                <!-- Buscador -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar en bitácora...">
                            <button class="btn btn-outline-primary" type="button" id="btnBuscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
<select id="registrosPorPagina" class="form-select">
    <option value="25">25 registros</option>
    <option value="50">50 registros</option>
    <option value="100" selected>100 registros</option>
    <option value="250">250 registros</option>
    <option value="500">500 registros</option>
    <option value="1000">1000 registros</option>
    <option value="0">Todos los registros</option>
</select>
                    </div>
                </div>

                <!-- Mensajes -->
                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando bitácora...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar la bitácora. Verifica la consola para más detalles.
                </div>
                
                <!-- Tabla -->
                <table id="tablaBitacora" class="table table-striped table-bordered" style="display: none;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Objeto</th>
                            <th>Creado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargan via JavaScript -->
                    </tbody>
                </table>

                <!-- Paginación -->
                <div id="paginacion" class="d-flex justify-content-between align-items-center mt-3" style="display: none;">
                    <div id="infoPaginacion" class="text-muted"></div>
                    <nav>
                        <ul id="paginacionLista" class="pagination mb-0">
                            <!-- Los números de página se generan dinámicamente -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- html2pdf.js para exportar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Función de depuración para ver la respuesta real
    async function debugBitacora() {
        try {
            console.log("🔍 [DEBUG] Probando endpoint de bitácora...");
            const response = await fetch('index.php?route=bitacora&caso=obtener');
            const text = await response.text();
            console.log("📄 [DEBUG] Respuesta cruda:", text);
            
            try {
                const data = JSON.parse(text);
                console.log("📊 [DEBUG] Estructura JSON:", data);
                console.log("🔍 [DEBUG] Keys del objeto:", Object.keys(data));
                if (data.data) {
                    console.log("📋 [DEBUG] Keys de data:", Object.keys(data.data));
                    console.log("📝 [DEBUG] Tipo de data.bitacora:", typeof data.data.bitacora);
                    if (data.data.bitacora) {
                        console.log("✅ [DEBUG] data.bitacora es array:", Array.isArray(data.data.bitacora));
                        console.log("📊 [DEBUG] Número de registros:", data.data.bitacora.length);
                    }
                }
            } catch (e) {
                console.error("❌ [DEBUG] Error parseando JSON:", e);
            }
        } catch (error) {
            console.error("❌ [DEBUG] Error en fetch:", error);
        }
    }

    // Ejecutar depuración al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        debugBitacora();
    });

    class BitacoraSistema {
    constructor() {
        this.datosBitacora = [];
        this.paginaActual = 1;
        this.registrosPorPagina = 100;
        this.totalRegistros = 0;
        this.filtros = {
            usuario: '',
            accion: '',
            fechaInicio: '',
            fechaFin: '',
            busqueda: ''
        };
        this.init();
    }

    async init() {
        await this.cargarFiltros();
        await this.cargarBitacora();
        this.configurarEventos();
    }

    // Función para cargar TODOS los registros sin límite
    async cargarBitacoraCompleta() {
        try {
            console.log("🔍 Cargando bitácora completa...");
            
            const response = await fetch('index.php?route=bitacora&caso=obtener&limite=10000');
            const text = await response.text();
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("Respuesta no es JSON válido");
            }
            
            if (data.status === 200 && data.data) {
                if (data.data.bitacora && Array.isArray(data.data.bitacora)) {
                    return data.data.bitacora;
                } else if (Array.isArray(data.data)) {
                    return data.data;
                } else {
                    const posiblesDatos = Object.values(data.data).find(item => Array.isArray(item));
                    return posiblesDatos || [];
                }
            } else {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error cargando bitácora completa:', error);
            return [];
        }
    }

    async cargarFiltros() {
        try {
            // Cargar usuarios para filtro
            const responseUsuarios = await fetch('index.php?route=user&caso=listar');
            const dataUsuarios = await responseUsuarios.json();
            
            if (dataUsuarios.status === 200 && dataUsuarios.data && dataUsuarios.data.usuarios) {
                const selectUsuario = document.getElementById('filtroUsuario');
                dataUsuarios.data.usuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.USUARIO;
                    option.textContent = `${usuario.USUARIO} - ${usuario.NOMBRE_USUARIO}`;
                    selectUsuario.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error cargando filtros:', error);
        }
    }

    async cargarBitacora() {
        try {
            console.log("🔍 Iniciando carga de bitácora...");
            
            const loadingMessage = document.getElementById('loadingMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            loadingMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            
            // Construir URL con parámetros
            let url = `index.php?route=bitacora&caso=obtener&limite=${this.registrosPorPagina}&pagina=${this.paginaActual}`;
            
            // Agregar filtros a la URL si existen
            if (this.filtros.usuario) url += `&usuario=${this.filtros.usuario}`;
            if (this.filtros.accion) url += `&accion=${this.filtros.accion}`;
            if (this.filtros.fechaInicio) url += `&fecha_inicio=${this.filtros.fechaInicio}`;
            if (this.filtros.fechaFin) url += `&fecha_fin=${this.filtros.fechaFin}`;
            if (this.filtros.busqueda) url += `&busqueda=${this.filtros.busqueda}`;
            
            const response = await fetch(url);
            
            console.log("📦 Respuesta HTTP:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            
            const text = await response.text();
            console.log("📄 Respuesta CRUDA completa:", text);
            
            // Verificar si es HTML
            if (text.includes('<html') || text.includes('<!DOCTYPE') || text.includes('<head>')) {
                console.error("❌ El servidor devolvió HTML en lugar de JSON");
                throw new Error("El servidor devolvió una página HTML. Posible error PHP o redirección.");
            }
            
            // Verificar errores PHP
            if (text.includes('Fatal error') || text.includes('Parse error') || text.includes('Warning') || text.includes('Notice')) {
                console.error("❌ Error PHP detectado en la respuesta");
                throw new Error("Error en el servidor: " + text.substring(0, 200));
            }
            
            let data;
            try {
                data = JSON.parse(text);
                console.log("✅ JSON parseado correctamente");
                console.log("📊 Estructura completa:", data);
            } catch (e) {
                console.error("❌ Error parseando JSON");
                throw new Error("Respuesta no es JSON válido: " + text.substring(0, 100));
            }
            
            if (data.status === 200 && data.data) {
                console.log("🔍 Estructura de data.data:", data.data);
                
                // Guardar el TOTAL de registros desde la paginación
                if (data.data.paginacion && data.data.paginacion.total_registros) {
                    this.totalRegistros = data.data.paginacion.total_registros;
                    console.log(`📊 Total de registros en BD: ${this.totalRegistros}`);
                }
                
                if (data.data.bitacora && Array.isArray(data.data.bitacora)) {
                    this.datosBitacora = data.data.bitacora;
                    console.log(`✅ Bitácora cargada: ${this.datosBitacora.length} registros de ${this.totalRegistros} totales`);
                } else if (Array.isArray(data.data)) {
                    this.datosBitacora = data.data;
                    console.log(`✅ Bitácora cargada: ${this.datosBitacora.length} registros de ${this.totalRegistros} totales`);
                } else {
                    console.warn("⚠️ Estructura de datos inesperada, intentando adaptar...");
                    const posiblesDatos = Object.values(data.data).find(item => Array.isArray(item));
                    if (posiblesDatos) {
                        this.datosBitacora = posiblesDatos;
                        console.log(`✅ Bitácora cargada (estructura adaptada): ${this.datosBitacora.length} registros de ${this.totalRegistros} totales`);
                    } else {
                        this.datosBitacora = [];
                        console.warn("⚠️ No se encontraron datos de bitácora en la respuesta");
                    }
                }
                
                this.cargarFiltroAcciones();
                this.mostrarBitacora();
                
            } else {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error cargando bitácora:', error);
            this.mostrarError(error.message);
        }
    }

    cargarFiltroAcciones() {
        const accionesUnicas = [...new Set(this.datosBitacora.map(item => item.ACCION))].sort();
        const selectAccion = document.getElementById('filtroAccion');
        
        // Limpiar opciones existentes (excepto la primera)
        while (selectAccion.children.length > 1) {
            selectAccion.removeChild(selectAccion.lastChild);
        }
        
        accionesUnicas.forEach(accion => {
            const option = document.createElement('option');
            option.value = accion;
            option.textContent = accion;
            selectAccion.appendChild(option);
        });
    }

    mostrarBitacora() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaBitacora');
        const paginacion = document.getElementById('paginacion');

        loadingMessage.style.display = 'none';
        errorMessage.style.display = 'none';

        if (!this.datosBitacora || this.datosBitacora.length === 0) {
            console.log("📭 No hay registros en la bitácora");
            errorMessage.textContent = "No hay registros en la bitácora";
            errorMessage.style.display = 'block';
            tabla.style.display = 'none';
            paginacion.style.display = 'none';
            return;
        }

        console.log("🔄 Mostrando", this.datosBitacora.length, "registros de", this.totalRegistros, "totales");

        // Si estamos en modo "Todos los registros", mostrar todos sin paginación
        if (this.registrosPorPagina === 0) {
            console.log("📊 Modo: Todos los registros - Mostrando", this.datosBitacora.length, "registros");
            
            // Mostrar tabla
            tabla.style.display = 'table';
            this.actualizarTabla(this.datosBitacora);
            
            // Ocultar paginación y mostrar info especial
            paginacion.style.display = 'flex';
            this.actualizarPaginacionTodos(this.datosBitacora.length);
        } else {
            // Paginación normal
            const totalPaginas = Math.ceil(this.totalRegistros / this.registrosPorPagina);

            // Mostrar tabla
            tabla.style.display = 'table';
            this.actualizarTabla(this.datosBitacora);
            
            // Mostrar y actualizar paginación con TOTAL REAL
            paginacion.style.display = 'flex';
            this.actualizarPaginacion(this.totalRegistros, totalPaginas);
        }
    }

    // Nueva función para paginación en modo "Todos"
    actualizarPaginacionTodos(totalRegistrosFiltrados) {
        const infoPaginacion = document.getElementById('infoPaginacion');
        const paginacionLista = document.getElementById('paginacionLista');
        
        infoPaginacion.textContent = `Mostrando TODOS los ${totalRegistrosFiltrados} registros (sin paginación)`;
        paginacionLista.innerHTML = ''; // Limpiar paginación
    }

    actualizarTabla(datos) {
        const tbody = document.querySelector('#tablaBitacora tbody');
        tbody.innerHTML = '';

        datos.forEach(item => {
            const fila = document.createElement('tr');
            
            // Formatear fecha
            const fecha = new Date(item.FECHA);
            const fechaFormateada = fecha.toLocaleString('es-ES');
            
            fila.innerHTML = `
                <td>${item.ID_BITACORA}</td>
                <td>${fechaFormateada}</td>
                <td>${item.USUARIO || 'N/A'}</td>
                <td><span class="badge bg-primary">${item.ACCION}</span></td>
                <td>${item.DESCRIPCION || 'N/A'}</td>
                <td>${item.OBJETO || 'N/A'}</td>
                <td>${item.CREADO_POR || 'SISTEMA'}</td>
            `;
            
            tbody.appendChild(fila);
        });
    }

    actualizarPaginacion(totalRegistros, totalPaginas) {
        const infoPaginacion = document.getElementById('infoPaginacion');
        const paginacionLista = document.getElementById('paginacionLista');
        
        // Calcular rango actual
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina + 1;
        const fin = Math.min(inicio + this.registrosPorPagina - 1, totalRegistros);
        
        infoPaginacion.textContent = `Mostrando ${inicio} - ${fin} de ${totalRegistros} registros`;
        
        console.log(`📊 Paginación: ${inicio}-${fin} de ${totalRegistros}`);
        
        // Generar números de página
        paginacionLista.innerHTML = '';
        
        // Botón anterior
        const liAnterior = document.createElement('li');
        liAnterior.className = `page-item ${this.paginaActual === 1 ? 'disabled' : ''}`;
        liAnterior.innerHTML = `
            <a class="page-link" href="#" data-pagina="${this.paginaActual - 1}">
                <i class="bi bi-chevron-left"></i>
            </a>
        `;
        paginacionLista.appendChild(liAnterior);
        
        // Números de página
        const paginasMostrar = this.generarNumerosPagina(totalPaginas);
        
        paginasMostrar.forEach(pagina => {
            const li = document.createElement('li');
            li.className = `page-item ${pagina === this.paginaActual ? 'active' : ''}`;
            
            if (pagina === '...') {
                li.innerHTML = '<span class="page-link">...</span>';
            } else {
                li.innerHTML = `
                    <a class="page-link" href="#" data-pagina="${pagina}">${pagina}</a>
                `;
            }
            
            paginacionLista.appendChild(li);
        });
        
        // Botón siguiente
        const liSiguiente = document.createElement('li');
        liSiguiente.className = `page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}`;
        liSiguiente.innerHTML = `
            <a class="page-link" href="#" data-pagina="${this.paginaActual + 1}">
                <i class="bi bi-chevron-right"></i>
            </a>
        `;
        paginacionLista.appendChild(liSiguiente);
    }

    generarNumerosPagina(totalPaginas) {
        const paginas = [];
        const paginasALaVista = 5;
        
        if (totalPaginas <= paginasALaVista) {
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }
        } else {
            if (this.paginaActual <= 3) {
                paginas.push(1, 2, 3, 4, '...', totalPaginas);
            } else if (this.paginaActual >= totalPaginas - 2) {
                paginas.push(1, '...', totalPaginas - 3, totalPaginas - 2, totalPaginas - 1, totalPaginas);
            } else {
                paginas.push(1, '...', this.paginaActual - 1, this.paginaActual, this.paginaActual + 1, '...', totalPaginas);
            }
        }
        
        return paginas;
    }

    cambiarPagina(nuevaPagina) {
        this.paginaActual = nuevaPagina;
        this.cargarBitacora();
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    configurarEventos() {
        // Filtros - ahora recargan desde servidor
        document.getElementById('filtroUsuario').addEventListener('change', (e) => {
            this.filtros.usuario = e.target.value;
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        document.getElementById('filtroAccion').addEventListener('change', (e) => {
            this.filtros.accion = e.target.value;
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        document.getElementById('filtroFechaInicio').addEventListener('change', (e) => {
            this.filtros.fechaInicio = e.target.value;
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        document.getElementById('filtroFechaFin').addEventListener('change', (e) => {
            this.filtros.fechaFin = e.target.value;
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        // Buscador
        document.getElementById('btnBuscar').addEventListener('click', () => {
            this.filtros.busqueda = document.getElementById('buscadorGlobal').value;
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        document.getElementById('buscadorGlobal').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.filtros.busqueda = e.target.value;
                this.paginaActual = 1;
                this.cargarBitacora();
            }
        });

        // Registros por página
        document.getElementById('registrosPorPagina').addEventListener('change', (e) => {
            this.registrosPorPagina = parseInt(e.target.value);
            this.paginaActual = 1;
            this.cargarBitacora();
        });

        // Paginación (event delegation)
        document.getElementById('paginacionLista').addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.closest('.page-link') && e.target.closest('.page-link').dataset.pagina) {
                const nuevaPagina = parseInt(e.target.closest('.page-link').dataset.pagina);
                if (!isNaN(nuevaPagina)) {
                    this.cambiarPagina(nuevaPagina);
                }
            }
        });

        // Limpiar filtros
        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => {
            this.limpiarFiltros();
        });

        // Exportar
        document.getElementById('btnExportar').addEventListener('click', () => {
            this.mostrarOpcionesExportacion();
        });
    }

    mostrarOpcionesExportacion() {
        Swal.fire({
            title: 'Exportar Bitácora',
            text: 'Selecciona qué datos exportar',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Página Actual',
            cancelButtonText: 'Todos los Registros',
            showDenyButton: true,
            denyButtonText: 'Con Filtros Aplicados',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.exportarPaginaActual();
            } else if (result.isDenied) {
                this.exportarConFiltros();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                this.exportarTodosLosRegistros();
            }
        });
    }

    exportarPaginaActual() {
        this.generarPDF(this.datosBitacora, 'Página Actual');
    }

    exportarConFiltros() {
        // Para exportar con filtros, necesitamos recargar los datos con los filtros aplicados
        const datosFiltrados = this.datosBitacora.filter(item => this.aplicarFiltroIndividual(item));
        this.generarPDF(datosFiltrados, 'Con Filtros Aplicados');
    }

    aplicarFiltroIndividual(item) {
        // Filtro por usuario
        if (this.filtros.usuario && item.USUARIO !== this.filtros.usuario) {
            return false;
        }
        
        // Filtro por acción
        if (this.filtros.accion && item.ACCION !== this.filtros.accion) {
            return false;
        }
        
        // Filtro por fecha
        if (this.filtros.fechaInicio || this.filtros.fechaFin) {
            const fechaRegistro = new Date(item.FECHA).toISOString().split('T')[0];
            
            if (this.filtros.fechaInicio && fechaRegistro < this.filtros.fechaInicio) {
                return false;
            }
            
            if (this.filtros.fechaFin && fechaRegistro > this.filtros.fechaFin) {
                return false;
            }
        }
        
        // Búsqueda global
        if (this.filtros.busqueda) {
            const busqueda = this.filtros.busqueda.toLowerCase();
            const camposBusqueda = [
                item.USUARIO,
                item.ACCION,
                item.DESCRIPCION,
                item.OBJETO,
                item.CREADO_POR
            ].join(' ').toLowerCase();
            
            if (!camposBusqueda.includes(busqueda)) {
                return false;
            }
        }
        
        return true;
    }

    async exportarTodosLosRegistros() {
        try {
            Swal.fire({
                title: 'Cargando todos los registros...',
                text: 'Esto puede tomar unos momentos',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const todosLosRegistros = await this.cargarBitacoraCompleta();
            Swal.close();
            
            if (todosLosRegistros.length === 0) {
                Swal.fire('Info', 'No hay registros para exportar', 'info');
                return;
            }

            this.generarPDF(todosLosRegistros, 'Todos los Registros');
            
        } catch (error) {
            Swal.close();
            Swal.fire('Error', 'No se pudieron cargar todos los registros: ' + error.message, 'error');
        }
    }

    limpiarFiltros() {
        document.getElementById('filtroUsuario').value = '';
        document.getElementById('filtroAccion').value = '';
        document.getElementById('filtroFechaInicio').value = '';
        document.getElementById('filtroFechaFin').value = '';
        document.getElementById('buscadorGlobal').value = '';
        
        this.filtros = {
            usuario: '',
            accion: '',
            fechaInicio: '',
            fechaFin: '',
            busqueda: ''
        };
        
        this.paginaActual = 1;
        this.cargarBitacora();
    }

            generarPDF(datosExportar, tipoExportacion = 'Página Actual') {
                try {
                    Swal.fire({
                        title: 'Generando PDF...',
                        text: 'Por favor espere',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const contenidoPDF = this.crearContenidoPDF(datosExportar, tipoExportacion);
                    
                    // Crear elemento temporal para html2pdf
                    const element = document.createElement('div');
                    element.innerHTML = contenidoPDF;
                    
                    // Configuración de html2pdf
                    const opt = {
                        margin: [10, 10, 10, 10],
                        filename: `bitacora_${tipoExportacion.replace(/\s+/g, '_').toLowerCase()}_${new Date().toISOString().split('T')[0]}.pdf`,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { 
                            scale: 2, 
                            useCORS: true,
                            logging: false
                        },
                        jsPDF: { 
                            unit: 'mm', 
                            format: 'a4', 
                            orientation: 'portrait' 
                        }
                    };

                    // Generar y descargar PDF
                    html2pdf().set(opt).from(element).save().then(() => {
                        Swal.close();
                        Swal.fire('Éxito', 'PDF generado y descargado correctamente', 'success');
                    });
                    
                } catch (error) {
                    Swal.close();
                    console.error('Error generando PDF:', error);
                    Swal.fire('Error', 'No se pudo generar el PDF: ' + error.message, 'error');
                }
            }

                crearContenidoPDF(datosExportar, tipoExportacion) {
                    const fecha = new Date().toLocaleDateString('es-ES');
                    const hora = new Date().toLocaleTimeString('es-ES');
                    
                    const filtros = this.obtenerFiltrosAplicados();
                    
                    return `
                        <div style="font-family: Arial, sans-serif; margin: 0; padding: 0; color: #333;">
                            <div style="text-align: center; border-bottom: 2px solid #D7A86E; padding-bottom: 10px; margin-bottom: 15px;">
                                <h1 style="color: #4B2E05; margin: 5px 0 3px 0; font-size: 20px;">Tesoro D' MIMI</h1>
                                <h2 style="color: #666; margin-bottom: 5px; font-size: 16px;">Reporte de Bitácora del Sistema</h2>
                                <div style="background: #e9ecef; padding: 5px 10px; border-radius: 4px; font-size: 12px; display: inline-block; margin: 5px 0;">
                                    <strong>Tipo de exportación:</strong> ${tipoExportacion}
                                </div>
                                <div style="color: #888; font-size: 11px;">
                                    Generado el: ${fecha} a las ${hora}
                                </div>
                            </div>
                            
                            ${filtros}
                            
                            <div style="background: #e8f4fd; padding: 8px; border-radius: 4px; margin: 8px 0; font-size: 11px;">
                                <strong>Resumen:</strong> ${datosExportar.length} registros exportados
                            </div>
                            
                            <table style="width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 9px;">
                                <thead>
                                    <tr>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">No.</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Fecha y Hora</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Usuario</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Acción</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Descripción</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Objeto</th>
                                        <th style="background-color: #D7A86E; color: white; padding: 6px 4px; text-align: left; border: 1px solid #B97222;">Creado Por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${this.generarFilasPDF(datosExportar)}
                                </tbody>
                            </table>
                            
                            <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 8px;">
                                <strong>Sistema de Gestión Tesoro D' MIMI</strong><br>
                                Documento generado automáticamente - ${tipoExportacion}<br>
                                ${fecha} ${hora} | Usuario: ${this.obtenerUsuarioActual()}
                            </div>
                        </div>
                    `;
                }

                generarFilasPDF(datosExportar) {
                    if (datosExportar.length === 0) {
                        return '<tr><td colspan="7" style="text-align: center; padding: 15px;">No hay registros para mostrar</td></tr>';
                    }
                    
                    return datosExportar.map(item => {
                        const fecha = new Date(item.FECHA);
                        const fechaFormateada = fecha.toLocaleString('es-ES');
                        
                        return `
                            <tr>
                                <td>${item.ID_BITACORA || 'N/A'}</td>
                                <td>${fechaFormateada}</td>
                                <td>${item.USUARIO || 'N/A'}</td>
                                <td>${item.ACCION || 'N/A'}</td>
                                <td>${item.DESCRIPCION || 'N/A'}</td>
                                <td>${item.OBJETO || 'N/A'}</td>
                                <td>${item.CREADO_POR || 'SISTEMA'}</td>
                            </tr>
                        `;
                    }).join('');
                }

                    obtenerFiltrosAplicados() {
                        const filtros = [];
                        
                        if (this.filtros.usuario) filtros.push(`Usuario: ${this.filtros.usuario}`);
                        if (this.filtros.accion) filtros.push(`Acción: ${this.filtros.accion}`);
                        if (this.filtros.fechaInicio) filtros.push(`Desde: ${this.filtros.fechaInicio}`);
                        if (this.filtros.fechaFin) filtros.push(`Hasta: ${this.filtros.fechaFin}`);
                        if (this.filtros.busqueda) filtros.push(`Búsqueda: ${this.filtros.busqueda}`);
                        
                        if (filtros.length === 0) {
                            return '<div class="filtros">Filtros aplicados: Ninguno</div>';
                        }
                        
                        return `
                            <div class="filtros">
                                <strong>Filtros aplicados:</strong><br>
                                ${filtros.map(filtro => `<div class="filtro-item">• ${filtro}</div>`).join('')}
                            </div>
                        `;
                    }   

                        obtenerUsuarioActual() {
                            return '<?= $_SESSION["user_usuario"] ?? "Sistema" ?>';
                        }

                        mostrarError(mensaje) {
                            const loadingMessage = document.getElementById('loadingMessage');
                            const errorMessage = document.getElementById('errorMessage');
                            
                            loadingMessage.style.display = 'none';
                            errorMessage.textContent = `Error: ${mensaje}`;
                            errorMessage.style.display = 'block';
                        }
}

// Instancia global
const bitacoraSistema = new BitacoraSistema();

</script>

<style>
/* Estilos para la tabla de bitácora */
#tablaBitacora {
    font-size: 0.85rem !important;
    width: 100% !important;
}

#tablaBitacora th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.9rem;
    padding: 10px 8px;
}

#tablaBitacora td {
    vertical-align: middle;
    word-wrap: break-word;
    word-break: break-word;
    padding: 8px 6px;
    line-height: 1.2;
}

.badge {
    font-size: 0.7em;
    padding: 4px 6px;
}

/* Columnas específicas */
#tablaBitacora td:nth-child(1) { /* ID */
    min-width: 60px;
    max-width: 80px;
    text-align: center;
}

#tablaBitacora td:nth-child(2) { /* Fecha */
    min-width: 140px;
    max-width: 160px;
    white-space: nowrap;
}

#tablaBitacora td:nth-child(3) { /* Usuario */
    min-width: 120px;
    max-width: 150px;
}

#tablaBitacora td:nth-child(4) { /* Acción */
    min-width: 100px;
    max-width: 120px;
}

#tablaBitacora td:nth-child(5) { /* Descripción */
    min-width: 200px;
    max-width: 300px;
}

#tablaBitacora td:nth-child(6) { /* Objeto */
    min-width: 120px;
    max-width: 150px;
}

#tablaBitacora td:nth-child(7) { /* Creado Por */
    min-width: 120px;
    max-width: 150px;
}

/* Paginación */
.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.page-link {
    color: #0d6efd;
}

/* Filtros */
.card-header {
    background-color: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}

/* Responsive */
@media (max-width: 768px) {
    #tablaBitacora {
        font-size: 0.8rem !important;
    }
    
    #tablaBitacora th,
    #tablaBitacora td {
        padding: 6px 4px;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
}
</style>