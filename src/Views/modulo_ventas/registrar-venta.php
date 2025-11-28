<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

use app\config\SessionHelper;
use app\config\Security;

// Validar que el usuario esté autenticado
if (!SessionHelper::isLoggedIn()) {
    header('Location: /');
    exit;
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Registrar Nueva Venta</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active">Registrar Venta</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-4">

                        <!-- BÚSQUEDA DE CLIENTE -->
                        <h5 class="mb-3">📋 Datos del Cliente</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="buscar-cliente" class="form-label">Buscar Cliente por DNI o Nombre</label>
                                <div class="input-group">
                                        <input type="text" class="form-control" id="buscar-cliente" 
                                            placeholder="DNI o nombre"
                                            title="Solo se permiten letras y números"
                                            onkeypress="return /[A-Za-z0-9\s]/.test(event.key)">
                                    <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente">
                                        <i class="bx bx-search"></i> Buscar
                                    </button>
                                </div>
                                <!-- Resultados de búsqueda -->
                                <div id="lista-clientes-resultados" class="list-group mt-2" style="display:none;"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-warning w-100" id="btn-sin-cliente">
                                    <i class="bx bx-user-x"></i> Sin Cliente
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-secondary w-100" 
                                        data-bs-toggle="modal" data-bs-target="#modal-nuevo-cliente">
                                    <i class="bx bx-plus"></i> Crear Cliente
                                </button>
                            </div>
                        </div>

                        <!-- INFO DEL CLIENTE SELECCIONADO -->
                        <div id="cliente-info-box" class="alert alert-info" style="display:none;">
                            <i class="bx bx-check-circle"></i> <strong>Cliente Seleccionado:</strong>
                            <p class="mb-0" id="cliente-nombre" style="font-size: 16px; font-weight: bold;"></p>
                            <p class="mb-0" id="cliente-dni"></p>
                        </div>

                        <hr>

                        <!-- BÚSQUEDA DE PRODUCTOS -->
                        <h5 class="mt-4 mb-3">🔍 Buscar Productos</h5>
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="buscar-productos" 
                                    placeholder="Ingrese nombre del producto..." 
                                    onkeyup="filtrarProductosPorBusqueda()">
                                <button class="btn btn-outline-secondary" type="button" id="btn-limpiar-busqueda">
                                    <i class="bx bx-x"></i> Limpiar
                                </button>
                            </div>
                        </div>

                        <!-- SELECCIONAR CATEGORÍA -->
                        <h5 class="mt-4 mb-3">🛍️ Seleccionar Productos por Categoría</h5>
                        <div class="mb-4">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary btn-categoria active" 
                                        data-categoria-nombre="Productos de Maíz" style="flex: 1;">
                                    <i class="bx bxs-corn"></i> Maíz
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-categoria" 
                                        data-categoria-nombre="Golosinas" style="flex: 1;">
                                    <i class="bx bxs-candy"></i> Golosinas
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-categoria" 
                                        data-categoria-nombre="Bebidas" style="flex: 1;">
                                    <i class="bx bxs-drink"></i> Bebidas
                                </button>
                            </div>
                        </div>

                        <!-- CONTENEDOR DE PRODUCTOS POR CATEGORÍA -->
                        <div id="contenedor-categorias" class="mb-4">
                            <div id="tabla-productos-contenedor">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Cargando productos...</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- TABLA DE PRODUCTOS SELECCIONADOS -->
                        <h5 class="mt-4 mb-3">📦 Productos Seleccionados</h5>
                        <div class="table-responsive">
                            <table class="table tabla-productos">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%"><i class="bx bx-box"></i> Producto</th>
                                        <th width="15%" class="text-center">Precio Unitario</th>
                                        <th width="15%" class="text-center">Cantidad</th>
                                        <th width="15%" class="text-center">Subtotal</th>
                                        <th width="10%" class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-productos">
                                    <tr id="sin-productos">
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-inbox"></i> No hay productos seleccionados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <hr>

                        <!-- TOTAL CENTRADO -->
                        <div class="text-center mb-4">
                            <h4>Total de la Compra</h4>
                            <div class="total-amount" id="total-compra" style="font-size: 48px; font-weight: bold; color: #28a745;">Lps. 0.00</div>
                        </div>

                        <!-- TIPO DE PAGO -->
                        <div class="row mb-4">
                            <div class="col-md-6 offset-md-3">
                                <label for="metodo-pago" class="form-label"><strong>Tipo de Pago</strong></label>
                                <select class="form-select form-select-lg" id="metodo-pago">
                                    <option value="">-- Seleccionar método de pago --</option>
                                </select>
                            </div>
                        </div>

                        <!-- BOTÓN CONTINUAR -->
                        <div class="d-grid gap-2 col-md-6 offset-md-3 mb-5">
                            <button type="button" class="btn btn-success btn-lg" id="btn-registrar-venta">
                                <i class="bx bx-check-circle"></i> Continuar con la Venta
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- MODAL CREAR CLIENTE (Mejorado, versión ligera) -->
<div class="modal fade" id="modal-nuevo-cliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:640px;">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#0d6efd,#20c997);color:#fff;border-bottom:0;">
                <h5 class="modal-title"><i class="bx bx-user-plus"></i> Crear Cliente Nuevo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" style="padding:18px;">
                <form id="form-nuevo-cliente" novalidate>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="nuevo-cliente-nombre" class="form-label">Nombre *</label>
                            <input type="text" id="nuevo-cliente-nombre" class="form-control" maxlength="50" placeholder="Juan" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo-cliente-apellido" class="form-label">Apellido *</label>
                            <input type="text" id="nuevo-cliente-apellido" class="form-control" maxlength="50" placeholder="Pérez" required>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="nuevo-cliente-dni" class="form-label">DNI *</label>
                            <input type="text" id="nuevo-cliente-dni" class="form-control" inputmode="numeric" maxlength="17" placeholder="1234 5678 90123" required>
                            <div class="form-text">Máx. 13 dígitos. Se agrupa automáticamente.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="nuevo-cliente-telefono" class="form-label">Teléfono *</label>
                            <input type="text" id="nuevo-cliente-telefono" class="form-control" inputmode="numeric" maxlength="9" placeholder="0000-0000" required>
                            <div class="form-text">8 dígitos — formateado como <code>0000-0000</code>.</div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label for="nuevo-cliente-email" class="form-label">Email (opcional)</label>
                        <input type="email" id="nuevo-cliente-email" class="form-control" placeholder="correo@ejemplo.com">
                    </div>

                    <div class="mt-2">
                        <label for="nuevo-cliente-direccion" class="form-label">Dirección</label>
                        <input type="text" id="nuevo-cliente-direccion" class="form-control" placeholder="Dirección">
                    </div>

                    <div id="msg-nuevo-cliente" class="mt-2"></div>
                </form>
            </div>

            <div class="modal-footer" style="background:#f8f9fa;border-top:0;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-guardar-cliente" class="btn btn-primary">
                    <i class="bx bx-save"></i> Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const nameRegex = /^[A-Za-zÀ-ÿ\s]{2,50}$/u;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const dniMin = 4, dniMax = 13;

    document.addEventListener('input', function(e){
        const el = e.target; if(!el) return;
        if (el.id === 'nuevo-cliente-nombre' || el.id === 'nuevo-cliente-apellido') {
            el.value = el.value.replace(/[^A-Za-zÀ-ÿ\s]/g,'').slice(0,50);
        }
        if (el.id === 'nuevo-cliente-telefono') {
            let d = el.value.replace(/\D/g,'').slice(0,8);
            el.value = d.length > 4 ? d.slice(0,4) + '-' + d.slice(4) : d;
        }
        if (el.id === 'nuevo-cliente-dni') {
            let d = el.value.replace(/\D/g,'').slice(0,13);
            const parts = []; let i=0; let rem=d.length; while(rem>5){ parts.push(d.substr(i,4)); i+=4; rem-=4; } if(i<d.length) parts.push(d.substr(i)); el.value = parts.join(' ');
        }
    });

    document.getElementById('btn-guardar-cliente').addEventListener('click', async function(){
        const msg = document.getElementById('msg-nuevo-cliente'); msg.innerHTML = '';
        const nombre = document.getElementById('nuevo-cliente-nombre').value.trim();
        const apellido = document.getElementById('nuevo-cliente-apellido').value.trim();
        const dniRaw = document.getElementById('nuevo-cliente-dni').value.replace(/\s/g,'');
        const telefonoRaw = document.getElementById('nuevo-cliente-telefono').value.replace(/\D/g,'');
        const email = document.getElementById('nuevo-cliente-email').value.trim();
        const direccion = document.getElementById('nuevo-cliente-direccion').value.trim();

        const errors = [];
        if (!nameRegex.test(nombre)) errors.push('Nombre inválido (2-50 letras).');
        if (!nameRegex.test(apellido)) errors.push('Apellido inválido (2-50 letras).');
        if (!(dniRaw.length >= dniMin && dniRaw.length <= dniMax)) errors.push(`DNI inválido (${dniMin}-${dniMax} dígitos).`);
        if (telefonoRaw.length !== 8) errors.push('Teléfono debe tener exactamente 8 dígitos.');
        if (email && !emailRegex.test(email)) errors.push('Email inválido.');
        if (errors.length) { msg.innerHTML = '<div class="alert alert-danger">'+errors.join('<br>')+'</div>'; return; }

        const datos = { nombre, apellido, dni: document.getElementById('nuevo-cliente-dni').value.trim(), telefono: document.getElementById('nuevo-cliente-telefono').value.trim(), email, direccion };

        try {
            const res = await fetch('/sistema/public/index.php?route=clientes&caso=crearCliente', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(datos) });

            // Manejar respuestas JSON y no-JSON (ej. errores SQL que devuelven texto)
            const ct = res.headers.get('content-type') || '';
            let payload = null;
            if (ct.includes('application/json')) {
                try { payload = await res.json(); } catch(e){ payload = null; }
            } else {
                const text = await res.text();
                payload = { success: res.ok, message: text };
            }

            const success = res.ok && (payload && (payload.status === 201 || payload.success === true));
            if (success) {
                msg.innerHTML = '<div class="alert alert-success">Cliente creado correctamente. Recargando...</div>';
                // Intentar actualizar lista dinámica si existe
                if (typeof cargarClientes === 'function') {
                    try { cargarClientes(); } catch(e) { /* noop */ }
                }
                const modalEl = document.getElementById('modal-nuevo-cliente');
                try { const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bsModal.hide(); } catch(e){ modalEl.style.display='none'; }
                // Reset y recarga ligera para que el flujo de venta reconozca el nuevo cliente
                setTimeout(()=>{ document.getElementById('form-nuevo-cliente').reset(); window.location.reload(); },700);
            } else {
                const serverMsg = (payload && payload.message) ? payload.message : 'Error al crear cliente';
                // Mostrar mensaje del servidor (puro texto o JSON.message)
                msg.innerHTML = '<div class="alert alert-danger">'+escapeHtml(String(serverMsg))+'</div>';
            }
        } catch(err) {
            msg.innerHTML = '<div class="alert alert-danger">Error de red: '+escapeHtml(err.message||'')+'</div>';
        }
    });
})();
</script>

<!-- MODAL PAGO EFECTIVO -->
<div id="modal-pago-overlay" class="modal-overlay" style="display:none;"></div>
<div id="modal-pago-efectivo" class="modal-pago" style="display:none;">
    <div class="modal-header-custom">
        <h5><i class="bx bx-money"></i> Procesar Pago en Efectivo</h5>
    </div>

    <h6 class="mt-3 mb-2"><strong>Productos a Vender:</strong></h6>
    <div class="productos-pago-list" id="lista-productos-pago"></div>

    <div class="alert alert-info mt-3">
        <strong><i class="bx bx-calculator"></i> Total a Pagar:</strong>
        <div style="font-size: 24px; font-weight: bold; color: #0066cc;">
            <span id="pago-total">Lps. 0.00</span>
        </div>
    </div>

    <div class="mb-3">
        <label for="dinero-recibido" class="form-label"><strong>Dinero Recibido (Lps)</strong></label>
        <input type="number" class="form-control form-control-lg" id="dinero-recibido" 
               placeholder="Ingrese cantidad recibida" step="0.01" min="0">
    </div>

    <div class="alert alert-success" id="vuelto-info" style="display:none;">
        <i class="bx bx-check-circle"></i> <strong>Vuelto a Entregar (Lps):</strong>
        <div style="font-size: 28px; font-weight: bold; color: #28a745;">
            <span id="vuelto-amount">Lps. 0.00</span>
        </div>
    </div>

    <div class="alert alert-warning" id="dinero-insuficiente" style="display:none;">
        <i class="bx bx-error"></i> ⚠️ Dinero insuficiente
    </div>

    <div class="d-grid gap-2 mt-4">
        <button type="button" class="btn btn-success btn-lg" id="btn-confirmar-pago">
            <i class="bx bx-check"></i> Confirmar Pago y Completar Venta
        </button>
        <button type="button" class="btn btn-secondary" onclick="cerrarModalPago()">
            <i class="bx bx-x"></i> Cancelar
        </button>
    </div>
</div>

<!-- 🔥 CONTENEDOR PARA NOTIFICACIONES TOAST -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<style>
    .toast-notification {
        background: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 250px;
        animation: slideIn 0.3s ease-in-out;
    }

    .toast-success {
        border-left: 4px solid #28a745;
        color: #28a745;
    }

    .toast-error {
        border-left: 4px solid #dc3545;
        color: #dc3545;
    }

    .toast-warning {
        border-left: 4px solid #ffc107;
        color: #ff9800;
    }

    .toast-info {
        border-left: 4px solid #17a2b8;
        color: #17a2b8;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-exit {
        animation: slideOut 0.3s ease-in-out forwards;
    }

<style>
    .btn-categoria {
        border: 2px solid #0066cc;
        color: #0066cc;
        transition: all 0.3s;
    }
    .btn-categoria.active {
        background-color: #0066cc;
        color: white;
    }
    .btn-categoria:hover {
        background-color: #e7f3ff;
    }
    .tabla-productos {
        margin-top: 15px;
    }
    .tabla-productos thead {
        background-color: #f8f9fa;
    }
    .tabla-productos tbody tr {
        vertical-align: middle;
    }
    .cantidad-input {
        width: 100px;
        text-align: center;
    }
    .total-amount {
        padding: 20px;
        background-color: #f0f8ff;
        border-radius: 10px;
        border: 2px solid #28a745;
    }
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1040;
    }
    .modal-pago {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0,0,0,0.3);
        z-index: 1050;
        min-width: 450px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header-custom {
        border-bottom: 2px solid #0066cc;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .productos-pago-list {
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .productos-pago-list > div {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .productos-pago-list > div:last-child {
        border-bottom: none;
    }
</style>

<script>
    // ========================
    // CONFIGURACIÓN INICIAL
    // ========================

    const categoriasMapeadas = {
        'Productos de Maíz': ['Rosquilla', 'Quesadilla', 'Totoposte', 'Tustacas', 'Mixta'],
        'Golosinas': ['Churros', 'Galletas', 'Bonbones', 'Chicles'],
        'Bebidas': ['Agua', 'Refresco', 'Energizante', 'Jugo']
    };

    let clienteSeleccionado = null;
    let productosSeleccionados = {};
    let totalCompra = 0;
    let todosLosProductos = [];
    let categoriaActual = 'Productos de Maíz';
    // Guardar cantidades temporales ingresadas por el usuario para cada producto (persistir entre re-renders)
    let tempQuantities = {};
    let timerRefreshStock = null; // Para sincronizar stock automáticamente
    let esClienteSin = false; // 🔥 Flag para controlar estado Sin Cliente

    // ========================
    // INICIALIZACIÓN
    // ========================

    document.addEventListener('DOMContentLoaded', function() {
        cargarProductosDeBD();
        cargarMetodosPago();
        cargarEventos();
        
        // Iniciar sincronización de stock cada 10 segundos
        iniciarSincronizacionStock();
    });

    /**
     * Sincronizar stock automáticamente cada cierto tiempo
     * Actualiza la CANTIDAD en la tabla de productos visible
     */
    function iniciarSincronizacionStock() {
        if (timerRefreshStock) clearInterval(timerRefreshStock);
        
        timerRefreshStock = setInterval(function() {
            refrescarStockProductos();
        }, 10000); // Cada 10 segundos
    }

    // Helper: normalizar texto (quitar acentos/diacríticos) y pasar a minúsculas
    function normalizeText(str) {
        if (!str) return '';
        try {
            return String(str).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        } catch (e) {
            // Si normalize no existe, aplicar fallback simple
            return String(str).toLowerCase().replace(/[ÁÀÂÄáàâäÉÈÊËéèêëÍÌÎÏíìîïÓÒÔÖóòôöÚÙÛÜúùûüÑñÇç]/g, function(c){
                const map = { 'á':'a','à':'a','â':'a','ä':'a','Á':'a','À':'a','Â':'a','Ä':'a',
                              'é':'e','è':'e','ê':'e','ë':'e','É':'e','È':'e','Ê':'e','Ë':'e',
                              'í':'i','ì':'i','î':'i','ï':'i','Í':'i','Ì':'i','Î':'i','Ï':'i',
                              'ó':'o','ò':'o','ô':'o','ö':'o','Ó':'o','Ò':'o','Ô':'o','Ö':'o',
                              'ú':'u','ù':'u','û':'u','ü':'u','Ú':'u','Ù':'u','Û':'u','Ü':'u',
                              'ñ':'n','Ñ':'n','ç':'c','Ç':'c' };
                return map[c] || c;
            }).toLowerCase();
        }
    }

    /**
     * Refrescar stock de todos los productos desde la BD
     */
    function refrescarStockProductos() {
        fetch('index.php?route=ventas&caso=obtenerTodosLosProductos')
            .then(r => r.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data && data.status === 200 && data.data && Array.isArray(data.data.productos)) {
                        todosLosProductos = data.data.productos;
                        // Re-renderizar la tabla actual para reflejar stock actualizado
                        mostrarProductosPorCategoria();
                    }
                } catch (e) {
                    // Error silencioso en refrescado de fondo
                }
            })
            .catch(e => {
                // Error silencioso en refrescado de fondo
            });
    }

    function cargarProductosDeBD() {
        // Cargar TODOS los productos de la BD
        fetch('index.php?route=ventas&caso=obtenerTodosLosProductos')
            .then(r => r.text())
            .then(text => {
                console.log('Respuesta cruda:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    throw new Error('Respuesta no es JSON válido');
                }
                console.log('Respuesta API:', data);
                if (data && data.status === 200 && data.data && Array.isArray(data.data.productos)) {
                    todosLosProductos = data.data.productos;
                    console.log('Productos cargados de BD:', todosLosProductos);
                    mostrarProductosPorCategoria();
                } else {
                    console.error('Estructura de datos inválida:', data);
                    document.getElementById('tabla-productos-contenedor').innerHTML = 
                        '<div class="alert alert-warning">Error: Estructura de datos inválida</div>';
                }
            })
            .catch(e => {
                console.error('Error cargando productos:', e);
                document.getElementById('tabla-productos-contenedor').innerHTML = 
                    '<div class="alert alert-danger">Error al conectar con la API: ' + e.message + '</div>';
            });
    }

    function mostrarProductosPorCategoria() {
        const contenedor = document.getElementById('tabla-productos-contenedor');
        const productosActuales = filtrarProductosPorCategoria(categoriaActual);

        if (productosActuales.length === 0) {
            contenedor.innerHTML = '<div class="alert alert-info">No hay productos en esta categoría</div>';
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-sm tabla-seleccionar-productos">';
        html += '<thead class="table-light"><tr>';
        html += '<th width="30%">Producto</th>';
        html += '<th width="15%" class="text-center">Precio</th>';
        html += '<th width="15%" class="text-center">Stock 📦</th>';
        html += '<th width="20%" class="text-center">Cantidad</th>';
        html += '<th width="20%" class="text-center">Acción</th>';
        html += '</tr></thead><tbody>';

        productosActuales.forEach(producto => {
            const stock = parseInt(producto.CANTIDAD) || 0;
            const stockClass = stock <= 0 ? 'text-danger fw-bold' : (stock < 5 ? 'text-warning fw-bold' : 'text-success fw-bold');
            
            // Determinar la cantidad a mostrar: si el usuario ya ingresó una cantidad previamente, usarla (pero no exceder stock)
            const savedQty = parseInt(tempQuantities[producto.ID_PRODUCTO]) || 0;
            const qtyToShow = savedQty > 0 ? Math.min(savedQty, stock || savedQty) : 1;

            html += `<tr data-id-producto="${producto.ID_PRODUCTO}" class="producto-row">
                <td><strong>${producto.NOMBRE}</strong><br><small class="text-muted">${producto.DESCRIPCION || ''}</small></td>
                <td class="text-center"><strong>LPS. ${parseFloat(producto.PRECIO).toFixed(2)}</strong></td>
                <td class="text-center ${stockClass} stock-celda" data-id-producto="${producto.ID_PRODUCTO}"><strong>${stock}</strong></td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm cantidad-seleccionar" 
                           value="${qtyToShow}" min="1" max="${stock}" style="width: 80px; margin: 0 auto;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary btn-agregar-producto" 
                            data-id-producto="${producto.ID_PRODUCTO}" 
                            data-nombre="${producto.NOMBRE}"
                            data-precio="${producto.PRECIO}"
                            ${stock <= 0 ? 'disabled' : ''}>
                        <i class="bx bx-plus"></i> Agregar
                    </button>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        contenedor.innerHTML = html;
    }

    function filtrarProductosPorCategoria(nombreCategoria) {
        if (!nombreCategoria || todosLosProductos.length === 0) return [];

        const productosNombres = categoriasMapeadas[nombreCategoria] || [];

        // Filtro insensible a mayúsculas y acentos: normalizar ambos lados
        const productosEnCategoria = todosLosProductos.filter(p => {
            const nombreProducto = normalizeText(p.NOMBRE || '');
            return productosNombres.some(nombre =>
                nombreProducto.includes(normalizeText(nombre))
            );
        });

        console.log(`Productos en ${nombreCategoria}:`, productosEnCategoria);
        return productosEnCategoria;
    }

    function filtrarProductosPorBusqueda() {
        const terminoBusqueda = document.getElementById('buscar-productos').value.trim();
        const terminoBusquedaNorm = normalizeText(terminoBusqueda);
        const contenedor = document.getElementById('tabla-productos-contenedor');

        if (!terminoBusqueda) {
            // Si no hay búsqueda, mostrar productos de la categoría actual
            mostrarProductosPorCategoria();
            return;
        }

        // 🔥 FILTRAR SOLO EN LA CATEGORÍA ACTUAL
        const productosCategoria = filtrarProductosPorCategoria(categoriaActual);
        
        // Filtrar productos de la categoría actual por término de búsqueda
        const productosEncontrados = productosCategoria.filter(p => {
            const nombre = normalizeText(p.NOMBRE || '');
            const descripcion = normalizeText(p.DESCRIPCION || '');
            return nombre.includes(terminoBusquedaNorm) || descripcion.includes(terminoBusquedaNorm);
        });

        if (productosEncontrados.length === 0) {
            contenedor.innerHTML = '<div class="alert alert-warning"><i class="bx bx-search"></i> No se encontraron productos</div>';
            return;
        }

        let html = '<div class="table-responsive"><table class="table table-sm tabla-seleccionar-productos">';
        html += '<thead class="table-light"><tr>';
        html += '<th width="30%">Producto</th>';
        html += '<th width="15%" class="text-center">Precio</th>';
        html += '<th width="15%" class="text-center">Stock 📦</th>';
        html += '<th width="20%" class="text-center">Cantidad</th>';
        html += '<th width="20%" class="text-center">Acción</th>';
        html += '</tr></thead><tbody>';

        productosEncontrados.forEach(producto => {
            const stock = parseInt(producto.CANTIDAD) || 0;
            const stockClass = stock <= 0 ? 'text-danger fw-bold' : (stock < 5 ? 'text-warning fw-bold' : 'text-success fw-bold');
            
            // Determinar la cantidad a mostrar: si el usuario ya ingresó una cantidad previamente, usarla (pero no exceder stock)
            const savedQty = parseInt(tempQuantities[producto.ID_PRODUCTO]) || 0;
            const qtyToShow = savedQty > 0 ? Math.min(savedQty, stock || savedQty) : 1;

            html += `<tr data-id-producto="${producto.ID_PRODUCTO}" class="producto-row">
                <td><strong>${producto.NOMBRE}</strong><br><small class="text-muted">${producto.DESCRIPCION || ''}</small></td>
                <td class="text-center"><strong>LPS. ${parseFloat(producto.PRECIO).toFixed(2)}</strong></td>
                <td class="text-center ${stockClass} stock-celda" data-id-producto="${producto.ID_PRODUCTO}"><strong>${stock}</strong></td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm cantidad-seleccionar" 
                           value="${qtyToShow}" min="1" max="${stock}" style="width: 80px; margin: 0 auto;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary btn-agregar-producto" 
                            data-id-producto="${producto.ID_PRODUCTO}" 
                            data-nombre="${producto.NOMBRE}"
                            data-precio="${producto.PRECIO}"
                            ${stock <= 0 ? 'disabled' : ''}>
                        <i class="bx bx-plus"></i> Agregar
                    </button>
                </td>
            </tr>`;
        });

        html += '</tbody></table></div>';
        contenedor.innerHTML = html;
    }

    function cargarMetodosPago() {
        fetch('index.php?route=ventas&caso=obtenerMetodosPago')
            .then(r => r.text())
            .then(text => {
                console.log('Métodos de pago respuesta cruda:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Error parseando JSON métodos pago:', e);
                    throw new Error('Respuesta no es JSON válido');
                }
                console.log('Métodos de pago:', data);
                if (data && data.status === 200 && Array.isArray(data.data)) {
                    const select = document.getElementById('metodo-pago');
                    data.data.forEach(metodo => {
                        const option = document.createElement('option');
                        option.value = metodo.ID_METODO_PAGO;
                        option.textContent = metodo.METODO_PAGO;
                        select.appendChild(option);
                    });
                } else {
                    console.error('Error en respuesta de métodos de pago:', data);
                }
            })
            .catch(e => console.error('Error cargando métodos de pago:', e));
    }

    function cargarEventos() {
        // Cambio de categoría
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-categoria')) {
                document.querySelectorAll('.btn-categoria').forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');
                categoriaActual = e.target.dataset.categoriaNombre;
                console.log('Categoría seleccionada:', categoriaActual);
                mostrarProductosPorCategoria();
            }
        });

        // Agregar producto
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-agregar-producto')) {
                const idProducto = e.target.dataset.idProducto;
                const nombre = e.target.dataset.nombre;
                const precio = parseFloat(e.target.dataset.precio);
                const fila = e.target.closest('tr');
                const cantidad = parseInt(fila.querySelector('.cantidad-seleccionar').value) || 1;
                const stockDisponible = parseInt(fila.querySelector('.cantidad-seleccionar').max) || 0;

                if (cantidad > stockDisponible) {
                    mostrarNotificacion(`Stock insuficiente. Disponible: ${stockDisponible}`, 'error');
                    return;
                }

                agregarProductoATabla(idProducto, nombre, precio, cantidad);
            }
        });

        // Botón Sin Cliente / Con Cliente Toggle
        const btnSinCliente = document.getElementById('btn-sin-cliente');
        const buscarClienteInput = document.getElementById('buscar-cliente');
        const btnBuscarCliente = document.getElementById('btn-buscar-cliente');
        
        if (btnSinCliente) {
            btnSinCliente.addEventListener('click', function() {
                if (!esClienteSin) {
                    // 🔥 CAMBIAR A SIN CLIENTE: Deshabilitar búsqueda de cliente
                    esClienteSin = true;
                    clienteSeleccionado = { 
                        ID_CLIENTE: null, 
                        NOMBRE: 'Sin Cliente', 
                        APELLIDO: '', 
                        DNI: 'N/A' 
                    };
                    document.getElementById('cliente-nombre').textContent = 'Sin nombre de Cliente';
                    document.getElementById('cliente-dni').textContent = '';
                    document.getElementById('cliente-info-box').style.display = 'block';
                    document.getElementById('buscar-cliente').value = '';
                    document.getElementById('lista-clientes-resultados').style.display = 'none';
                    
                    // 🔴 Deshabilitar búsqueda de cliente
                    buscarClienteInput.disabled = true;
                    buscarClienteInput.style.backgroundColor = '#e9ecef';
                    btnBuscarCliente.disabled = true;
                    
                    // Cambiar texto del botón a "Con Cliente"
                    btnSinCliente.innerHTML = '<i class="bx bx-user-check"></i> Con Cliente';
                    btnSinCliente.classList.remove('btn-warning');
                    btnSinCliente.classList.add('btn-info');
                } else {
                    // 🔥 VOLVER A CON CLIENTE: Habilitar búsqueda de cliente
                    esClienteSin = false;
                    clienteSeleccionado = null;
                    document.getElementById('cliente-nombre').textContent = '';
                    document.getElementById('cliente-dni').textContent = '';
                    document.getElementById('cliente-info-box').style.display = 'none';
                    document.getElementById('buscar-cliente').value = '';
                    document.getElementById('lista-clientes-resultados').style.display = 'none';
                    
                    // 🟢 Habilitar búsqueda de cliente
                    buscarClienteInput.disabled = false;
                    buscarClienteInput.style.backgroundColor = '#fff';
                    btnBuscarCliente.disabled = false;
                    
                    // Cambiar texto del botón a "Sin Cliente"
                    btnSinCliente.innerHTML = '<i class="bx bx-user-x"></i> Sin Cliente';
                    btnSinCliente.classList.remove('btn-info');
                    btnSinCliente.classList.add('btn-warning');
                }
            });
        }

        // Búsqueda de cliente: ahora en vivo mientras escribe + botón LIMPIAR
        // Debounce helper
        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        // Cambiar el botón de buscar por un botón Limpiar
        if (btnBuscarCliente) {
            btnBuscarCliente.innerHTML = '<i class="bx bx-x"></i> Limpiar';
            btnBuscarCliente.classList.remove('btn-outline-primary');
            btnBuscarCliente.classList.add('btn-outline-secondary');
            btnBuscarCliente.addEventListener('click', function() {
                // Limpiar campo y resultados
                buscarClienteInput.value = '';
                const lista = document.getElementById('lista-clientes-resultados');
                if (lista) {
                    lista.innerHTML = '';
                    lista.style.display = 'none';
                }
                // Reset cliente seleccionado
                clienteSeleccionado = null;
                document.getElementById('cliente-info-box').style.display = 'none';
                buscarClienteInput.focus();
            });
        }

        // Live search: buscar mientras se teclea (letras o números)
        if (buscarClienteInput) {
            buscarClienteInput.addEventListener('input', debounce(function(e) {
                const v = this.value.trim();
                const lista = document.getElementById('lista-clientes-resultados');
                if (!v) {
                    if (lista) lista.style.display = 'none';
                    return;
                }
                // Solo caracteres permitidos
                if (!/^[A-Za-z0-9\s]+$/.test(v)) {
                    // No realizar búsqueda si contiene caracteres inválidos
                    return;
                }
                buscarCliente();
            }, 350));

            // Mantener Enter para búsqueda inmediata
            buscarClienteInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') buscarCliente();
            });
        }

        // Botón limpiar búsqueda de productos
        const btnLimpiarBusqueda = document.getElementById('btn-limpiar-busqueda');
        if (btnLimpiarBusqueda) {
            btnLimpiarBusqueda.addEventListener('click', function() {
                document.getElementById('buscar-productos').value = '';
                filtrarProductosPorBusqueda();
            });
        }

        // Selección desde la lista de resultados (delegación)
        document.addEventListener('click', function(e) {
            const item = e.target.closest('.cliente-list-item');
            if (item) {
                const cliente = JSON.parse(item.getAttribute('data-cliente'));
                seleccionarClienteDesdeLista(cliente);
            }
        });

        // Guardar nuevo cliente - usar delegación de eventos en el documento
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'btn-guardar-cliente') {
                e.preventDefault();
                console.log('🔵 Botón guardar cliente clickeado');
                guardarClienteNuevo();
            }
        });

        // Cambios en cantidad de la tabla
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('cantidad-input')) {
                const producto = e.target.dataset.producto;
                let cantidad = parseInt(e.target.value) || 1;
                
                // 🔥 VALIDAR CANTIDAD CONTRA STOCK
                const productoObj = Object.entries(productosSeleccionados).find(([nombre]) => nombre === producto);
                if (productoObj) {
                    const [nombre, datos] = productoObj;
                    const productoData = todosLosProductos.find(p => p.ID_PRODUCTO == datos.id_producto);
                    const stockMaximo = parseInt(productoData?.CANTIDAD) || 0;
                    
                    if (cantidad > stockMaximo) {
                        cantidad = stockMaximo;
                        e.target.value = stockMaximo;
                        mostrarNotificacion(`Stock máximo: ${stockMaximo}`, 'warning', 1500);
                    }
                }
                
                if (cantidad > 0) {
                    productosSeleccionados[producto].cantidad = cantidad;
                    actualizarTablaSeleccionados();
                }
            }
        });

        // Guardar cantidades temporales en los inputs de selección de productos (prevent clearing on re-render)
        document.addEventListener('input', function(e) {
            if (e.target.classList && e.target.classList.contains('cantidad-seleccionar')) {
                const fila = e.target.closest('tr');
                const id = fila ? fila.getAttribute('data-id-producto') : null;
                if (!id) return;
                let val = parseInt(e.target.value) || 0;
                const max = parseInt(e.target.getAttribute('max')) || 0;
                if (val > max && max > 0) {
                    // Si intenta ingresar más que el stock, ajustar al máximo permitido
                    e.target.value = max;
                    val = max;
                    mostrarNotificacion(`Stock máximo: ${max}`, 'warning', 1500);
                }
                if (val <= 0) val = 1;
                tempQuantities[id] = val;
            }
        });

        // Eliminar producto
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-eliminar-producto') || e.target.closest('.btn-eliminar-producto')) {
                const btn = e.target.closest('.btn-eliminar-producto');
                const producto = btn.dataset.producto;
                delete productosSeleccionados[producto];
                actualizarTablaSeleccionados();
            }
        });

        // Cálculo de vuelto
        document.getElementById('dinero-recibido').addEventListener('input', calcularVuelto);

        // Botón registrar venta
        document.getElementById('btn-registrar-venta').addEventListener('click', procesoRegistroVenta);
        document.getElementById('btn-confirmar-pago').addEventListener('click', confirmarPago);
    }

    // ========================
    // SISTEMA DE NOTIFICACIONES
    // ========================
    
    function mostrarNotificacion(mensaje, tipo = 'success', duracion = 2000) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${tipo}`;
        
        let icono = '✓';
        if (tipo === 'error') icono = '✕';
        else if (tipo === 'warning') icono = '⚠';
        else if (tipo === 'info') icono = 'ⓘ';
        
        toast.innerHTML = `<span>${icono}</span><span>${mensaje}</span>`;
        container.appendChild(toast);
        
        // Auto-remover después de la duración
        setTimeout(() => {
            toast.classList.add('toast-exit');
            setTimeout(() => toast.remove(), 300);
        }, duracion);
    }

    // ========================
    // FUNCIONES DE PRODUCTO
    // ========================

    function agregarProductoATabla(idProducto, nombre, precio, cantidad) {
        if (cantidad <= 0) {
            mostrarNotificacion('La cantidad debe ser mayor a 0', 'warning');
            const cantidadInput = document.querySelector(`[data-producto='${nombre}']`);
            if (cantidadInput) {
                cantidadInput.classList.add('is-invalid');
                cantidadInput.focus();
            }
            return;
        }

        // Validar stock actualizado justo antes de agregar
        const producto = todosLosProductos.find(p => p.ID_PRODUCTO == idProducto);
        if (!producto) {
            mostrarNotificacion('Producto no encontrado. Recargue la página e intente de nuevo', 'error');
            return;
        }

        const stockActual = parseInt(producto.CANTIDAD) || 0;
        // Calcular cantidad total si ya existe en seleccionados
        let cantidadTotal = cantidad;
        if (productosSeleccionados[nombre]) {
            cantidadTotal += productosSeleccionados[nombre].cantidad;
        }
        if (cantidadTotal > stockActual) {
            mostrarNotificacion(`No puede agregar más de lo disponible en stock (${stockActual})`, 'warning');
            const cantidadInput = document.querySelector(`[data-producto='${nombre}']`);
            if (cantidadInput) {
                cantidadInput.classList.add('is-invalid');
                cantidadInput.focus();
            }
            return;
        }

        if (!productosSeleccionados[nombre]) {
            productosSeleccionados[nombre] = { 
                id_producto: idProducto,
                precio: precio, 
                cantidad: cantidad 
            };
        } else {
            productosSeleccionados[nombre].cantidad += cantidad;
        }

        actualizarTablaSeleccionados();
        const cantidadInput = document.querySelector(`[data-producto='${nombre}']`);
        if (cantidadInput) cantidadInput.classList.remove('is-invalid');
        mostrarNotificacion(`${nombre} agregado al carrito`, 'success', 1500);
    }

    function actualizarTablaSeleccionados() {
        const tbody = document.getElementById('tbody-productos');
        const sinProductos = document.getElementById('sin-productos');

        if (Object.keys(productosSeleccionados).length === 0) {
            tbody.innerHTML = '<tr id="sin-productos"><td colspan="5" class="text-center text-muted py-4"><i class="bx bx-inbox"></i> No hay productos seleccionados</td></tr>';
            totalCompra = 0;
            document.getElementById('total-compra').textContent = 'Lps0.00';
            return;
        }

        tbody.innerHTML = '';
        totalCompra = 0;

        Object.entries(productosSeleccionados).forEach(([nombre, { id_producto, precio, cantidad }]) => {
            const subtotal = precio * cantidad;
            totalCompra += subtotal;

            const tr = document.createElement('tr');
            // Obtener stock actual del producto para limitar la cantidad (atributo max)
            const prodInfo = todosLosProductos.find(p => p.ID_PRODUCTO == id_producto) || {};
            const stockMax = parseInt(prodInfo.CANTIDAD) || 0;
            tr.innerHTML = `
                <td><strong>${nombre}</strong></td>
                <td class="text-center">Lps. ${precio.toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm cantidad-input" 
                           value="${cantidad}" min="1" max="${stockMax}" data-producto="${nombre}" style="width: 100px; margin: 0 auto;">
                </td>
                <td class="text-center"><strong>Lps${subtotal.toFixed(2)}</strong></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto" data-producto="${nombre}">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('total-compra').textContent = 'Lps.' + totalCompra.toFixed(2);
    }

    // ========================
    // FUNCIONES DE CLIENTE
    // ========================

    function buscarCliente() {
        const valor = document.getElementById('buscar-cliente').value.trim();
        if (!valor) {
            mostrarNotificacion('Ingrese DNI o nombre del cliente', 'warning');
            return;
        }

        // Validar ESTRICTO: solo letras y numeros, SIN ningún carácter especial
        const valorOk = /^[A-Za-z0-9\s]+$/.test(valor);
        if (!valorOk) {
            mostrarNotificacion('Registro exitoso');
            document.getElementById('buscar-cliente').focus();
            return;
        }

        // Usar endpoint de búsqueda general para obtener lista de clientes
        fetch(`index.php?route=ventas&caso=buscarClientesActivos`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ busqueda: valor })
        })
            .then(r => r.text())
            .then(text => {
                console.log('Respuesta búsqueda cliente:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Error parseando respuesta cliente:', e);
                    throw new Error('Respuesta no es JSON válido');
                }
                // Normalizar estado (puede venir como número o string)
                const status = (function(d){
                    if (!d) return 0;
                    if (typeof d.status === 'number') return d.status;
                    if (typeof d.status === 'string' && d.status.match(/^\d+$/)) return parseInt(d.status);
                    if (typeof d.status_str === 'string' && d.status_str.match(/^\d+$/)) return parseInt(d.status_str);
                    return 0;
                })(data);

                if (status === 200 && data.data) {
                    // data.data puede ser un array de clientes
                    if (Array.isArray(data.data) && data.data.length > 0) {
                        renderListaClientes(data.data);
                    } else if (typeof data.data === 'object') {
                        // En caso raro de objeto único
                        renderListaClientes([data.data]);
                    } else {
                        mostrarNotificacion('Cliente no encontrado: ' + (data.message || 'Sin resultados'), 'info');
                    }
                } else {
                    mostrarNotificacion('Cliente no encontrado: ' + (data.message || 'Verifique los datos'), 'info');
                    console.error('Respuesta:', data);
                }
            })
            .catch(e => {
                console.error('Error:', e);
                mostrarNotificacion('Error al buscar cliente: ' + e.message, 'error');
            });
    }

    function renderListaClientes(clientes) {
        const lista = document.getElementById('lista-clientes-resultados');
        lista.innerHTML = '';
        if (!clientes || clientes.length === 0) {
            lista.style.display = 'none';
            return;
        }

        clientes.forEach(c => {
            const nombre = (c.NOMBRE || '') + (c.APELLIDO ? (' ' + c.APELLIDO) : '');
            const dni = c.DNI || c.DOCUMENTO || '';
            const telefono = c.TELEFONO || c.TELEFONO1 || '';

            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action cliente-list-item';
            item.setAttribute('data-cliente', JSON.stringify(c));
            item.innerHTML = `<div class="d-flex w-100 justify-content-between"><h6 class="mb-0">${escapeHtml(nombre)}</h6><small class="text-muted">DNI: ${escapeHtml(dni)}</small></div><p class="mb-0 text-muted">${escapeHtml(telefono)}</p>`;
            lista.appendChild(item);
        });

        lista.style.display = 'block';
    }

    function seleccionarClienteDesdeLista(cliente) {
        // Limpiar lista
        const lista = document.getElementById('lista-clientes-resultados');
        lista.innerHTML = '';
        lista.style.display = 'none';

        // Seleccionar cliente y actualizar UI
        seleccionarCliente(cliente);
        // Enfocar al resto del formulario para seguir interactuando
        document.getElementById('metodo-pago').focus();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function seleccionarCliente(cliente) {
        // 🔥 Si se selecciona un cliente real, resetear el estado de Sin Cliente
        if (esClienteSin) {
            esClienteSin = false;
            const btnSinCliente = document.getElementById('btn-sin-cliente');
            const buscarClienteInput = document.getElementById('buscar-cliente');
            const btnBuscarCliente = document.getElementById('btn-buscar-cliente');
            
            // 🟢 Habilitar búsqueda de cliente
            buscarClienteInput.disabled = false;
            buscarClienteInput.style.backgroundColor = '#fff';
            btnBuscarCliente.disabled = false;
            
            // Cambiar texto del botón a "Sin Cliente"
            btnSinCliente.innerHTML = '<i class="bx bx-user-x"></i> Sin Cliente';
            btnSinCliente.classList.remove('btn-info');
            btnSinCliente.classList.add('btn-warning');
        }
        
        clienteSeleccionado = cliente;
        document.getElementById('cliente-nombre').textContent = cliente.NOMBRE + ' ' + (cliente.APELLIDO || '');
        document.getElementById('cliente-dni').textContent = 'DNI: ' + cliente.DNI;
        document.getElementById('cliente-info-box').style.display = 'block';
    }

    function guardarClienteNuevo() {
        console.log('Iniciando guardarClienteNuevo...');
        const nombreEl = document.getElementById('nuevo-cliente-nombre');
        const apellidoEl = document.getElementById('nuevo-cliente-apellido');
        const dniEl = document.getElementById('nuevo-cliente-dni');
        const telefonoEl = document.getElementById('nuevo-cliente-telefono');
        const emailEl = document.getElementById('nuevo-cliente-email');
        const direccionEl = document.getElementById('nuevo-cliente-direccion');

        const nombre = nombreEl ? (nombreEl.value || '').trim() : '';
        const apellido = apellidoEl ? (apellidoEl.value || '').trim() : '';
        const dni = dniEl ? (dniEl.value || '').trim() : '';
        const telefono = telefonoEl ? (telefonoEl.value || '').trim() : '';
        const email = emailEl ? (emailEl.value || '').trim() : '';
        const direccion = direccionEl ? (direccionEl.value || '').trim() : '';

        console.log('Datos:', { nombre, apellido, dni, telefono, email });

        if (!nombre || !apellido || !dni) {
            mostrarNotificacion('Nombre, Apellido y DNI son requeridos', 'warning');
            return;
        }

        // Client-side sanitization/validation (mirror server) - ESTRICTO
        const nombreOk = /^[A-Za-z\s]+$/.test(nombre);
        const apellidoOk = /^[A-Za-z\s]+$/.test(apellido);
        const dniOk = /^[0-9]{4,15}$/.test(dni);
        const telefonoOk = telefono === '' ? true : /^[0-9]{6,20}$/.test(telefono);
        const emailOk = email === '' ? true : /^\S+@\S+\.\S+$/.test(email);
        const clientErrors = [];
        if (!nombreOk) clientErrors.push('Nombre: solo se permiten letras y espacios');
        if (!apellidoOk) clientErrors.push('Apellido: solo se permiten letras y espacios');
        if (!dniOk) clientErrors.push('DNI inválido (solo números, 4-15 dígitos)');
        if (!telefonoOk) clientErrors.push('Teléfono contiene caracteres inválidos');
        if (!emailOk) clientErrors.push('Email inválido');
        if (clientErrors.length > 0) {
            mostrarNotificacion(clientErrors.join(' | '), 'warning');
            return;
        }

        fetch('index.php?route=clientes&caso=crearCliente', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, apellido, dni, telefono, correo: email, direccion })
        })
        .then(r => r.text())
        .then(text => {
            console.log('Respuesta crear cliente (texto):', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error parseando respuesta crear cliente:', e);
                throw new Error('Respuesta no es JSON válido');
            }
            console.log('Respuesta crear cliente (JSON):', data);
            if (data && (data.status === 200 || data.status === 201)) {
                console.log('Cliente creado exitosamente. Datos:', data.data);
                seleccionarCliente(data.data);
                
                // Cerrar modal de forma segura
                const modalElement = document.getElementById('modal-nuevo-cliente');
                if (modalElement) {
                    try {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        } else {
                            // Si no hay instancia, crear una nueva y esconderla
                            new bootstrap.Modal(modalElement).hide();
                        }
                    } catch (err) {
                        console.error('Error cerrando modal:', err);
                        modalElement.style.display = 'none';
                    }
                }
                
                // Limpiar formulario
                const form = document.getElementById('form-nuevo-cliente');
                if (form) form.reset();
                // Limpiar lista de resultados si estaba visible
                const lista = document.getElementById('lista-clientes-resultados');
                if (lista) {
                    lista.innerHTML = '';
                    lista.style.display = 'none';
                }

                mostrarNotificacion('Cliente creado exitosamente', 'success', 1200);
            } else {
                mostrarNotificacion('Error: ' + (data.message || 'No se pudo crear el cliente'), 'error');
                console.error('Respuesta de error:', data);
            }
        })
        .catch(e => {
            console.error('Error en guardarClienteNuevo:', e);
            mostrarNotificacion('Error al crear cliente: ' + e.message, 'error');
        });
    }

    // ========================
    // FUNCIONES DE PAGO
    // ========================

    function procesoRegistroVenta() {
        if (!clienteSeleccionado) {
            mostrarNotificacion('Por favor seleccione un cliente o presione "Sin Cliente"', 'warning');
            return;
        }
        if (Object.keys(productosSeleccionados).length === 0) {
            mostrarNotificacion('Por favor agregue productos', 'warning');
            return;
        }

        const metodoPago = document.getElementById('metodo-pago').value;
        if (!metodoPago) {
            mostrarNotificacion('Por favor seleccione método de pago', 'warning');
            return;
        }

        const metodoSelect = document.getElementById('metodo-pago');
        const metodoNombre = metodoSelect.options[metodoSelect.selectedIndex].text.toLowerCase();

        if (metodoNombre.includes('efectivo')) {
            abrirModalPagoEfectivo();
        } else {
            // Abrir modal para pago no-efectivo (subir comprobante)
            abrirModalPagoNoEfectivo();
        }
    }

    function abrirModalPagoEfectivo() {
        document.getElementById('modal-pago-overlay').style.display = 'block';
        document.getElementById('modal-pago-efectivo').style.display = 'block';

        const lista = document.getElementById('lista-productos-pago');
        lista.innerHTML = '';

        Object.entries(productosSeleccionados).forEach(([nombre, { precio, cantidad }]) => {
            const subtotal = precio * cantidad;
            lista.innerHTML += `
                <div>
                    <span><strong>${nombre}</strong> × ${cantidad}</span>
                    <span style="color: #28a745; font-weight: bold;">Lps. ${subtotal.toFixed(2)}</span>
                </div>
            `;
        });

        document.getElementById('pago-total').textContent = 'Lps' + totalCompra.toFixed(2);
        document.getElementById('dinero-recibido').value = '';
        document.getElementById('dinero-recibido').focus();
    }

    function cerrarModalPago() {
        document.getElementById('modal-pago-overlay').style.display = 'none';
        document.getElementById('modal-pago-efectivo').style.display = 'none';
    }

    function calcularVuelto() {
        const dineroRecibido = parseFloat(document.getElementById('dinero-recibido').value) || 0;
        const vueltoInfo = document.getElementById('vuelto-info');
        const dineroInsuficiente = document.getElementById('dinero-insuficiente');
        const btnConfirmar = document.getElementById('btn-confirmar-pago');

        if (dineroRecibido >= totalCompra) {
            const vuelto = dineroRecibido - totalCompra;
            document.getElementById('vuelto-amount').textContent = 'Lps' + vuelto.toFixed(2);
            vueltoInfo.style.display = 'block';
            dineroInsuficiente.style.display = 'none';
            btnConfirmar.disabled = false;
        } else {
            vueltoInfo.style.display = 'none';
            dineroInsuficiente.style.display = 'block';
            btnConfirmar.disabled = true;
        }
    }

    function confirmarPago() {
        completarVenta();
        cerrarModalPago();
    }

    // -------------------------
    // Modal pago NO EFECTIVO (tarjeta/transferencia)
    // -------------------------
    function abrirModalPagoNoEfectivo() {
        // Crear modal HTML si no existe
        if (!document.getElementById('modal-pago-noefectivo')) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div id="modal-pago-noefectivo-overlay" class="modal-overlay"></div>
                <div id="modal-pago-noefectivo" class="modal-pago">
                    <div class="modal-header-custom"><h5><i class="bx bx-credit-card"></i> Pago con Tarjeta/Transferencia</h5></div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Seleccione comprobante (jpg, png, pdf)</strong></label>
                        <div class="input-group">
                            <input type="file" id="comprobante-input" class="form-control" accept="image/*,application/pdf">
                            <button class="btn btn-outline-primary" type="button" id="btn-open-camera" title="Abrir cámara">
                                <i class="bx bx-camera"></i> Cámara
                            </button>
                        </div>
                    </div>
                    <div class="mb-3" id="preview-comprobante-wrapper" style="display:none;">
                        <label class="form-label"><strong>Previsualización</strong></label>
                        <div id="preview-comprobante" style="max-height:250px; overflow:auto;"></div>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="button" class="btn btn-success btn-lg" id="btn-confirmar-pago-noefectivo">Confirmar Pago y Subir Comprobante</button>
                        <button type="button" class="btn btn-secondary" id="btn-cancelar-pago-noefectivo">Cancelar</button>
                    </div>
                </div>`;
            // Cámara modal (inline, dinámico)
            wrapper.innerHTML += `
                <div id="modal-camera-overlay" class="modal-overlay" style="display:none"></div>
                <div id="modal-camera" class="modal-pago" style="display:none; min-width:320px; max-width:420px;">
                    <div class="modal-header-custom"><h5><i class="bx bx-camera"></i> Tomar fotografía del comprobante</h5></div>
                    <div class="mb-2 text-center">
                        <video id="camera-video" autoplay playsinline style="width:100%; max-height:360px; background:#000; display:block;"></video>
                        <canvas id="camera-canvas" style="display:none; width:100%;"></canvas>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger w-100" id="btn-close-camera">Cerrar</button>
                        <button type="button" class="btn btn-secondary w-100" id="btn-take-photo">Tomar foto</button>
                        <button type="button" class="btn btn-primary w-100" id="btn-use-photo" disabled>Usar foto</button>
                    </div>
                </div>`;
            document.body.appendChild(wrapper);

            // Attach events
            document.getElementById('comprobante-input').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewWrap = document.getElementById('preview-comprobante-wrapper');
                const preview = document.getElementById('preview-comprobante');
                preview.innerHTML = '';
                if (!file) { previewWrap.style.display = 'none'; return; }
                previewWrap.style.display = 'block';
                const ext = file.name.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    const p = document.createElement('p'); p.textContent = file.name + ' (PDF)'; preview.appendChild(p);
                } else {
                    const img = document.createElement('img');
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '220px';
                    img.src = URL.createObjectURL(file);
                    preview.appendChild(img);
                }
            });

            // Cámara: variables y helpers
            let cameraStream = null;
            const btnOpenCamera = document.getElementById('btn-open-camera');
            const modalCamera = document.getElementById('modal-camera');
            const overlayCamera = document.getElementById('modal-camera-overlay');
            const video = document.getElementById('camera-video');
            const canvas = document.getElementById('camera-canvas');
            const btnTake = document.getElementById('btn-take-photo');
            const btnUse = document.getElementById('btn-use-photo');
            const btnCloseCam = document.getElementById('btn-close-camera');

            function stopCameraStream() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(t => t.stop());
                    cameraStream = null;
                }
                if (video) video.srcObject = null;
            }

            function openCameraModal() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    mostrarNotificacion('Su navegador no soporta cámara', 'error');
                    return;
                }
                // Reset UI
                if (canvas) canvas.style.display = 'none';
                if (video) video.style.display = 'block';
                if (btnUse) btnUse.disabled = true;
                overlayCamera.style.display = 'block';
                modalCamera.style.display = 'block';
                // Request camera
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
                    .then(stream => {
                        cameraStream = stream;
                        if (video) {
                            video.srcObject = stream;
                            video.play();
                        }
                    })
                    .catch(err => {
                        console.error('Error al abrir cámara:', err);
                        mostrarNotificacion('No se pudo acceder a la cámara', 'error');
                        overlayCamera.style.display = 'none';
                        modalCamera.style.display = 'none';
                    });
            }

            function closeCameraModal() {
                stopCameraStream();
                if (overlayCamera) overlayCamera.style.display = 'none';
                if (modalCamera) modalCamera.style.display = 'none';
            }

            if (btnOpenCamera) btnOpenCamera.addEventListener('click', openCameraModal);
            if (btnCloseCam) btnCloseCam.addEventListener('click', closeCameraModal);

            if (btnTake) btnTake.addEventListener('click', function() {
                if (!cameraStream || !video) return;
                const w = video.videoWidth;
                const h = video.videoHeight;
                if (!canvas) return;
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, w, h);
                // Show canvas as preview
                canvas.style.display = 'block';
                video.style.display = 'none';
                if (btnUse) btnUse.disabled = false;
            });

            if (btnUse) btnUse.addEventListener('click', function() {
                if (!canvas) return;
                canvas.toBlob(function(blob) {
                    if (!blob) {
                        mostrarNotificacion('Error al capturar imagen', 'error');
                        return;
                    }
                    const filename = 'comprobante_' + Date.now() + '.jpg';
                    const file = new File([blob], filename, { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    const input = document.getElementById('comprobante-input');
                    input.files = dataTransfer.files;

                    // Trigger change handler to show preview
                    const ev = new Event('change');
                    input.dispatchEvent(ev);

                    // Close camera modal and stop stream
                    closeCameraModal();
                }, 'image/jpeg', 0.92);
            });

            document.getElementById('btn-confirmar-pago-noefectivo').addEventListener('click', function() {
                completarVenta(true);
            });

            document.getElementById('btn-cancelar-pago-noefectivo').addEventListener('click', function() {
                cerrarModalPagoNoEfectivo();
            });
        }

        document.getElementById('modal-pago-noefectivo-overlay').style.display = 'block';
        document.getElementById('modal-pago-noefectivo').style.display = 'block';
    }

    function cerrarModalPagoNoEfectivo() {
        const ov = document.getElementById('modal-pago-noefectivo-overlay');
        const md = document.getElementById('modal-pago-noefectivo');
        if (ov) ov.style.display = 'none';
        if (md) md.style.display = 'none';
    }

    function completarVenta(needsUpload = false) {
        const metodoPago = document.getElementById('metodo-pago').value;
        const detalles = Object.entries(productosSeleccionados).map(([nombre, { id_producto, precio, cantidad }]) => ({
            ID_PRODUCTO: id_producto,
            NOMBRE: nombre,
            CANTIDAD: cantidad,
            PRECIO: precio
        }));
        // Enviar payload con claves esperadas por el backend
        // Detectar si es tarjeta/transferencia y no hay comprobante
        let comprobanteValue = undefined;
        const metodoSelect = document.getElementById('metodo-pago');
        const metodoNombre = metodoSelect.options[metodoSelect.selectedIndex].text.toLowerCase();
        if ((metodoNombre.includes('tarjeta') || metodoNombre.includes('transferencia')) && needsUpload) {
            const input = document.getElementById('comprobante-input');
            if (!input || !input.files || input.files.length === 0) {
                comprobanteValue = null;
            }
        }
        const ventaPayload = {
            ID_CLIENTE: clienteSeleccionado.ID_CLIENTE,
            ID_METODO_PAGO: metodoPago,
            TOTAL: totalCompra,
            ITEMS: detalles
        };
        if (typeof comprobanteValue !== 'undefined') {
            ventaPayload.COMPROBANTE = comprobanteValue;
        }
        fetch('index.php?route=ventas&caso=crearVenta', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(ventaPayload)
        })
        .then(r => r.text())
        .then(text => {
            console.log('Respuesta crear venta:', text);
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error parseando respuesta venta:', e);
                throw new Error('Respuesta no es JSON válido');
            }
            if (data && (data.status === 200 || data.status === 201)) {
                const idFactura = data.data?.id_factura || null;
                // Si se requiere subir comprobante (no-efectivo) y hay un archivo seleccionado
                if (needsUpload && idFactura) {
                    const input = document.getElementById('comprobante-input');
                    if (!input || !input.files || input.files.length === 0) {
                        mostrarNotificacion('Venta registrada pero no se seleccionó comprobante. ID Factura: ' + idFactura, 'info');
                        limpiarFormulario();
                        cerrarModalPagoNoEfectivo();
                        // Redirigir a consultar-ventas
                        setTimeout(() => { window.location.href = 'index.php?route=consultar-ventas'; }, 900);
                        return;
                    }

                    const file = input.files[0];
                    const form = new FormData();
                    form.append('id_factura', idFactura);
                    form.append('id_cliente', clienteSeleccionado.ID_CLIENTE);
                    form.append('comprobante', file);

                    fetch('index.php?route=ventas&caso=guardarComprobantePago', {
                        method: 'POST',
                        body: form
                    })
                    .then(r => r.text())
                    .then(text2 => {
                        let res;
                        try { res = JSON.parse(text2); } catch (err) { throw new Error('Respuesta no es JSON válido: ' + text2); }
                        if (res && res.status === 200) {
                            mostrarNotificacion('Venta y comprobante registrados', 'success');
                        } else {
                            mostrarNotificacion('Venta registrada pero fallo al subir comprobante', 'error');
                            console.error('Respuesta subida:', res);
                        }
                        limpiarFormulario();
                        cerrarModalPagoNoEfectivo();
                        // Redirigir a consultar-ventas
                        setTimeout(() => { window.location.href = 'index.php?route=consultar-ventas'; }, 900);
                    })
                    .catch(err => {
                        console.error('Error subiendo comprobante:', err);
                        mostrarNotificacion('Venta registrada pero error subiendo comprobante', 'error');
                        limpiarFormulario();
                        cerrarModalPagoNoEfectivo();
                        setTimeout(() => { window.location.href = 'index.php?route=consultar-ventas'; }, 900);
                    });

                    return;
                }

                mostrarNotificacion('Venta registrada exitosamente', 'success');
                limpiarFormulario();
                if (needsUpload) cerrarModalPagoNoEfectivo();
                // Redirigir al listado de ventas después de una breve pausa
                setTimeout(() => { window.location.href = 'index.php?route=consultar-ventas'; }, 900);
            } else {
                mostrarNotificacion('Error: ' + (data.message || 'No se pudo registrar la venta'), 'error');
                console.error('Respuesta:', data);
            }
        })
        .catch(e => {
            console.error('Error:', e);
            mostrarNotificacion('Error al registrar venta: ' + e.message, 'error');
        });
    }

    function limpiarFormulario() {
        clienteSeleccionado = null;
        productosSeleccionados = {};
        document.getElementById('cliente-info-box').style.display = 'none';
        document.getElementById('buscar-cliente').value = '';
        document.getElementById('metodo-pago').value = '';
        actualizarTablaSeleccionados();
    }

    function registrarBitacora(accion, descripcion, idObjeto = null) {
        fetch('/sistema/src/routes/bitacoraAPI.php?caso=registrar-navegacion', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                pagina: window.location.pathname,
                accion,
                descripcion,
                id_objeto: idObjeto
            })
        });
    }

    // Ejemplo de uso en exportar PDF
    document.getElementById('btnExportarPDF').addEventListener('click', async function() {
        // ...existing code...
        registrarBitacora('EXPORTAR_PDF', 'Exportó ventas a PDF');
    });

    // Repite el llamado en otras acciones relevantes (crear, editar, eliminar, etc.)
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
