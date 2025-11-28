<?php
// Vista: Gestión de Clientes
// Siguiendo el ejemplo de las demás vistas en modulo_ventas
// Puedes agregar aquí el contenido HTML y PHP necesario para la gestión de clientes
// ...
?>
<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';
use App\config\SessionHelper;
use App\config\PermisosHelper;

if (!SessionHelper::isLoggedIn()) {
    header('Location: ../../login.php');
    exit;
}

if (!PermisosHelper::checkPermission('modulo_ventas/gestion-clientes.php', 'CONSULTAR')) {
    echo "<div class='alert alert-danger'>No tiene permisos para acceder a esta vista.</div>";
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="/sistema/public/css/productos.css">
    <style>
        .modal-bg { display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: #fff; margin: 10vh auto; padding: 20px; border-radius: 8px; width: 400px; max-width: 90vw; }
    </style>
</head>
<body>
<main id="main" class="main">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h2 mb-0">Gestión de Clientes</h1>
                <div class="d-flex gap-2">
                    <button id="btn-agregar-cliente" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Agregar Cliente
                    </button>
                    <button id="btn-pdf-clientes" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Guardar como PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <div class="card-body">
                <!-- Filtros Centrados -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-light p-3 rounded">
                            <div class="row g-3 align-items-end justify-content-center">
                                <div class="col-md-4">
                                    <label for="filtroBusqueda" class="form-label">Buscar cliente:</label>
                                    <input type="text" id="filtroBusqueda" class="form-control" placeholder="Nombre, Apellido o DNI">
                                </div>
                                <div class="col-md-2">
                                    <button id="btnRefrescar" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-arrow-clockwise"></i> Refrescar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading y Mensajes -->
                <div id="loadingMessage" class="alert alert-info text-center" style="display:none;">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando clientes...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar los clientes. Verifica la consola para más detalles.
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table id="tablaClientes" class="table table-hover" style="display: none;">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>DNI</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientes-table-body">
                            <!-- Los clientes se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- No Results -->
                <div class="text-center mt-4" id="sinResultados" style="display: none;">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No se encontraron clientes registrados en el sistema.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal simple para agregar cliente -->
<div class="modal-bg" id="modalCliente">
    <div class="modal-content" style="max-width:560px;padding:0;">
        <div style="background:linear-gradient(90deg,#0d6efd,#20c997);color:#fff;padding:14px;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:18px;">Agregar Cliente</h4>
            <button id="cerrarModal" class="btn btn-sm btn-light" style="background:transparent;border:0;color:#fff;font-weight:600;">✕</button>
        </div>
        <div style="padding:18px;background:#fff;border-radius:0 0 8px 8px;">
            <form id="formCliente" novalidate>
                <div class="mb-2">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" maxlength="50" required>
                </div>
                <div class="mb-2">
                    <label for="apellido" class="form-label">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Apellido" maxlength="50" required>
                </div>
                <div class="mb-2">
                    <label for="dni" class="form-label">DNI *</label>
                    <input type="text" id="dni" name="dni" class="form-control" placeholder="DNI" inputmode="numeric" maxlength="17" required>
                    <div class="form-text">Máx. 13 números. Se agrupa automáticamente cada 4 dígitos (últimos 5 juntos).</div>
                </div>
                <div class="mb-2">
                    <label for="telefono" class="form-label">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" placeholder="0000-0000" inputmode="numeric" maxlength="9" required>
                    <div class="form-text">8 dígitos. Se formatea como <code>0000-0000</code>.</div>
                </div>
                <div class="mb-2">
                    <label for="email" class="form-label">Email (opcional)</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección">
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" id="cancelarModalAgregar" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
                <div id="msgCliente" style="margin-top:10px;"></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para previsualizar PDF -->
<div class="modal-bg" id="modalPdfPreview" style="display:none;">
    <div class="modal-content" style="max-width:900px;width:90vw;">
        <h3>Previsualización PDF de Clientes</h3>
        <div id="pdf-preview-content" style="max-height:60vh;overflow:auto;background:#f8f9fa;padding:10px;border-radius:6px;"></div>
        <div class="mt-3 d-flex justify-content-end gap-2">
            <button id="btn-descargar-pdf" class="btn btn-danger"><i class="bi bi-download"></i> Descargar PDF</button>
            <button id="btn-cerrar-pdf-modal" class="btn btn-secondary">Cerrar</button>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const modal = document.getElementById('modalCliente');
    document.getElementById('btn-agregar-cliente').onclick = () => { modal.style.display = 'block'; };
    document.getElementById('cerrarModal').onclick = () => { modal.style.display = 'none'; document.getElementById('msgCliente').innerHTML = ''; };
    window.onclick = e => { if (e.target === modal) modal.style.display = 'none'; };

    function mostrarLoading(show) {
        document.getElementById('loadingMessage').style.display = show ? '' : 'none';
    }
    function mostrarError(msg) {
        const e = document.getElementById('errorMessage');
        e.innerText = msg;
        e.style.display = '';
    }
    function ocultarError() {
        document.getElementById('errorMessage').style.display = 'none';
    }

    function cargarClientes(filtro = '') {
        mostrarLoading(true);
        ocultarError();
        // Si no hay filtro, usamos el endpoint listarClientes para devolver TODOS los clientes
        const endpoint = (filtro === '') ? '/sistema/public/index.php?route=clientes&caso=listarClientes' : '/sistema/public/index.php?route=clientes&caso=buscarClientes';
        const options = (filtro === '') ? { method: 'GET' } : {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ busqueda: filtro })
        };

        fetch(endpoint, options)
        .then(r => r.json())
        .then(res => {
            mostrarLoading(false);
            const tbody = document.getElementById('clientes-table-body');
            const tabla = document.getElementById('tablaClientes');
            const sinResultados = document.getElementById('sinResultados');
            tbody.innerHTML = '';
                if (res.status === 200 && res.data.length) {
                tabla.style.display = '';
                sinResultados.style.display = 'none';

                // Helper para mostrar teléfono con guión y DNI con espacios
                const formatTelefonoDisplay = (val) => {
                    if (!val && val !== 0) return '';
                    const s = val.toString().trim();
                    // Si ya contiene guión o espacio, devolver tal cual
                    if (/[\s\-\.]/.test(s)) return s;
                    const digits = s.replace(/\D/g, '').slice(0,8);
                    if (digits.length > 4) return digits.slice(0,4) + '-' + digits.slice(4);
                    return digits;
                };
                const formatDNIDisplay = (val) => {
                    if (!val && val !== 0) return '';
                    const s = val.toString().trim();
                    // Si ya contiene separadores, devolver tal cual
                    if (/[\s\-\.]/.test(s)) return s;
                    const digits = s.replace(/\D/g, '').slice(0,13);
                    const parts = [];
                    let i = 0;
                    let remaining = digits.length;
                    while (remaining > 5) {
                        parts.push(digits.substr(i,4));
                        i += 4;
                        remaining -= 4;
                    }
                    if (i < digits.length) parts.push(digits.substr(i));
                    return parts.join(' ');
                };

                res.data.forEach(c => {
                    const dniDisplay = formatDNIDisplay(c.DNI || '');
                    const telDisplay = formatTelefonoDisplay(c.TELEFONO || '');
                    tbody.innerHTML += `<tr>
                        <td>${c.ID_CLIENTE}</td>
                        <td>${c.NOMBRE || ''}</td>
                        <td>${c.APELLIDO || ''}</td>
                        <td>${dniDisplay}</td>
                        <td>${telDisplay}</td>
                        <td>${c.CORREO || ''}</td>
                        <td>${c.DIRECCION || ''}</td>
                        <td>${c.ESTADO || ''}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-editar-cliente" data-id="${c.ID_CLIENTE}" data-cliente='${JSON.stringify(c)}'>
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar-cliente" data-id="${c.ID_CLIENTE}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>`;
                // Modal para editar cliente (insertar solo una vez)
                if (!document.getElementById('modalEditarCliente')) {
                    document.body.insertAdjacentHTML('beforeend', `
                    <div class="modal-bg" id="modalEditarCliente" style="display:none;">
                        <div class="modal-content" style="max-width:560px;padding:0;">
                            <div style="background:linear-gradient(90deg,#6f42c1,#20c997);color:#fff;padding:16px;border-radius:8px 8px 0 0;display:flex;justify-content:space-between;align-items:center;">
                                <h4 style="margin:0;font-size:18px;">Editar Cliente</h4>
                                <button id="cerrarModalEditar" class="btn btn-sm btn-light" style="background:transparent;border:0;color:#fff;font-weight:600;">✕</button>
                            </div>
                            <div style="padding:18px;background:#fff;border-radius:0 0 8px 8px;">
                                <form id="formEditarCliente" novalidate>
                                    <input type="hidden" name="id_cliente" id="edit-id-cliente">
                                    <div class="mb-2">
                                        <label class="form-label">Nombre *</label>
                                        <input type="text" id="edit-nombre" name="nombre" class="form-control" placeholder="Nombre" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Apellido *</label>
                                        <input type="text" id="edit-apellido" name="apellido" class="form-control" placeholder="Apellido" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">DNI *</label>
                                        <input type="text" id="edit-dni" name="dni" class="form-control" placeholder="DNI" inputmode="numeric" maxlength="17" required>
                                        <div class="form-text">Máx. 13 números; se formatea automáticamente.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Teléfono *</label>
                                        <input type="text" id="edit-telefono" name="telefono" class="form-control" placeholder="0000-0000" inputmode="numeric" maxlength="9" required>
                                        <div class="form-text">8 dígitos. Se formatea como <code>0000-0000</code>.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Email (opcional)</label>
                                        <input type="email" id="edit-email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" id="edit-direccion" name="direccion" class="form-control" placeholder="Dirección">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Estado</label>
                                        <select id="edit-estado" name="estado" class="form-control">
                                            <option value="ACTIVO">ACTIVO</option>
                                            <option value="INACTIVO">INACTIVO</option>
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" id="cancelarEditarCliente" class="btn btn-secondary">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                    </div>
                                    <div id="msgEditarCliente" style="margin-top:10px;"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    `);

                    // Masking & input sanitation
                    document.addEventListener('input', function(e) {
                        const el = e.target;
                        if (!el) return;
                        // Nombre / Apellido: permitir letras con acentos y espacios
                        if (el.id === 'edit-nombre' || el.id === 'edit-apellido') {
                            el.value = el.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0,50);
                        }
                        // Teléfono: solo dígitos, máximo 8, formatear 4-4 con '-'
                        if (el.id === 'edit-telefono') {
                            let digits = el.value.replace(/\D/g, '').slice(0,8);
                            if (digits.length > 4) el.value = digits.slice(0,4) + '-' + digits.slice(4);
                            else el.value = digits;
                        }
                        // DNI: solo dígitos, máximo 13, agrupar de 4 hasta dejar 5 finales juntos
                        if (el.id === 'edit-dni') {
                            let digits = el.value.replace(/\D/g, '').slice(0,13);
                            let parts = [];
                            let i = 0;
                            let remaining = digits.length;
                            while (remaining > 5) {
                                parts.push(digits.substr(i,4));
                                i += 4;
                                remaining -= 4;
                            }
                            if (i < digits.length) parts.push(digits.substr(i));
                            el.value = parts.join(' ');
                        }
                    });

                    // Abrir/cerrar modal
                    document.addEventListener('click', function(e) {
                        if (e.target.closest('.btn-editar-cliente')) {
                            const btn = e.target.closest('.btn-editar-cliente');
                            const cliente = JSON.parse(btn.getAttribute('data-cliente'));
                            document.getElementById('edit-id-cliente').value = cliente.ID_CLIENTE;
                            document.getElementById('edit-nombre').value = cliente.NOMBRE || '';
                            document.getElementById('edit-apellido').value = cliente.APELLIDO || '';
                            document.getElementById('edit-dni').value = (cliente.DNI || '').toString().replace(/\D/g,'').slice(0,13);
                            // Aplicar formato DNI inmediatamente
                            const dniField = document.getElementById('edit-dni');
                            dniField.dispatchEvent(new Event('input'));
                            document.getElementById('edit-telefono').value = cliente.TELEFONO ? (cliente.TELEFONO.replace(/\D/g,'').slice(0,8)) : '';
                            // aplicar formato telefono
                            const telField = document.getElementById('edit-telefono');
                            telField.dispatchEvent(new Event('input'));
                            document.getElementById('edit-email').value = cliente.CORREO || '';
                            document.getElementById('edit-direccion').value = cliente.DIRECCION || '';
                            document.getElementById('modalEditarCliente').style.display = 'block';
                            document.getElementById('msgEditarCliente').innerHTML = '';
                        }
                        if (e.target.id === 'cerrarModalEditar' || e.target.id === 'cancelarEditarCliente') {
                            const modal = document.getElementById('modalEditarCliente');
                            if (modal) modal.style.display = 'none';
                            const msg = document.getElementById('msgEditarCliente'); if (msg) msg.innerHTML = '';
                        }
                    });

                    // Validación y envío del formulario de edición
                    document.getElementById('formEditarCliente').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const msg = document.getElementById('msgEditarCliente');
                        msg.innerHTML = '';

                        const nombre = document.getElementById('edit-nombre').value.trim();
                        const apellido = document.getElementById('edit-apellido').value.trim();
                        const dniRaw = document.getElementById('edit-dni').value.replace(/\s/g,'');
                        const telefonoRaw = document.getElementById('edit-telefono').value.replace(/\D/g,'');
                        const email = document.getElementById('edit-email').value.trim();
                        const direccion = document.getElementById('edit-direccion').value.trim();
                        const id_cliente = document.getElementById('edit-id-cliente').value;

                        let errors = [];
                        const nameRegex = /^[A-Za-zÀ-ÿ\s]{2,50}$/u;
                        if (!nameRegex.test(nombre)) errors.push('Nombre inválido (solo letras y espacios, 2-50 caracteres)');
                        if (!nameRegex.test(apellido)) errors.push('Apellido inválido (solo letras y espacios, 2-50 caracteres)');
                        if (!(dniRaw.length >= 4 && dniRaw.length <= 13)) errors.push('DNI debe tener entre 4 y 13 dígitos');
                        if (!(telefonoRaw.length === 8)) errors.push('Teléfono debe tener exactamente 8 dígitos');
                        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Email inválido');

                        if (errors.length) {
                            msg.innerHTML = '<div class="alert alert-danger">' + errors.join('<br>') + '</div>';
                            return;
                        }

                        const datos = {
                            id_cliente: id_cliente,
                            nombre: nombre,
                            apellido: apellido,
                            dni: dniRaw,
                            telefono: telefonoRaw,
                            email: email,
                            direccion: direccion,
                            estado: document.getElementById('edit-estado').value || 'ACTIVO'
                        };

                        fetch('/sistema/public/index.php?route=clientes&caso=editarCliente', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(datos)
                        })
                        .then(async r => {
                            if (!r.ok) {
                                const txt = await r.text(); throw new Error(txt || ('HTTP ' + r.status));
                            }
                            const ct = r.headers.get('content-type') || '';
                            if (ct.includes('application/json')) return r.json();
                            const txt = await r.text(); try { return JSON.parse(txt); } catch(e) { throw new Error(txt || 'Respuesta no JSON'); }
                        })
                        .then(res => {
                            if (res && res.status === 200) {
                                msg.innerHTML = '<div class="alert alert-success">Cliente actualizado correctamente</div>';
                                cargarClientes();
                                setTimeout(() => { document.getElementById('modalEditarCliente').style.display = 'none'; msg.innerHTML = ''; }, 1200);
                            } else {
                                msg.innerHTML = '<div class="alert alert-danger">' + (res && res.message ? res.message : 'Error al editar cliente') + '</div>';
                            }
                        })
                        .catch(err => {
                            msg.innerHTML = '<div class="alert alert-danger">Error: ' + (err.message || 'Error de red') + '</div>';
                        });
                    });
                }

                // Eliminar cliente
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-eliminar-cliente')) {
                        const btn = e.target.closest('.btn-eliminar-cliente');
                        const id = btn.getAttribute('data-id');
                        mostrarConfirmacionEliminar(id);
                    }
                });

                function mostrarConfirmacionEliminar(id) {
                    // Si ya existe el modal, elimínalo primero
                    const oldModal = document.getElementById('modalConfirmEliminar');
                    if (oldModal) oldModal.remove();
                    // Crear modal de confirmación
                    const modalHtml = `
                        <div class="modal-bg" id="modalConfirmEliminar" style="display:block;">
                            <div class="modal-content">
                                <h4>¿Seguro que deseas eliminar este cliente?</h4>
                                <div style="margin-top:20px; display:flex; gap:10px; justify-content:center;">
                                    <button class="btn btn-danger" id="btnConfirmEliminar">Eliminar</button>
                                    <button class="btn btn-secondary" id="btnCancelarEliminar">Cancelar</button>
                                </div>
                                <div id="msgEliminarCliente" style="margin-top:10px;"></div>
                            </div>
                        </div>`;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    document.getElementById('btnCancelarEliminar').onclick = function() {
                        document.getElementById('modalConfirmEliminar').remove();
                    };
                    document.getElementById('btnConfirmEliminar').onclick = function() {
                        fetch('/sistema/public/index.php?route=clientes&caso=eliminarCliente', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_cliente: id })
                        })
                        .then(async r => {
                            // Si el status no es OK, leer texto y lanzar error con contenido
                            if (!r.ok) {
                                const txt = await r.text();
                                throw new Error(txt || ('HTTP ' + r.status));
                            }
                            const ct = r.headers.get('content-type') || '';
                            if (ct.includes('application/json')) {
                                return r.json();
                            }
                            // Intentar parsear texto aunque no tenga cabecera JSON
                            const txt = await r.text();
                            try { return JSON.parse(txt); } catch (e) { throw new Error(txt || 'Respuesta no JSON'); }
                        })
                        .then(res => {
                            const msg = document.getElementById('msgEliminarCliente');
                            if (res && res.status === 200) {
                                msg.innerHTML = '<span style="color:green">Cliente eliminado correctamente</span>';
                                cargarClientes();
                                setTimeout(() => { document.getElementById('modalConfirmEliminar').remove(); }, 1200);
                            } else {
                                msg.innerHTML = '<span style="color:red">' + (res && res.message ? res.message : 'Error al eliminar cliente') + '</span>';
                            }
                        })
                        .catch(err => {
                            const msgEl = document.getElementById('msgEliminarCliente');
                            const text = (err && err.message) ? err.message : 'Error de red';
                            msgEl.innerHTML = '<span style="color:red">Error: ' + text.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span>';
                        });
                    };
                }
                });
            } else {
                tabla.style.display = 'none';
                sinResultados.style.display = '';
            }
        })
        .catch(err => {
            mostrarLoading(false);
            mostrarError('Error al cargar los clientes.');
            console.error(err);
        });
    }
    cargarClientes();

    document.getElementById('btnRefrescar').onclick = () => cargarClientes(document.getElementById('filtroBusqueda').value);
    document.getElementById('filtroBusqueda').oninput = function() {
        cargarClientes(this.value);
    };

    // El manejo del envío de 'formCliente' se realiza más abajo con validaciones y máscaras.
    </script>
<script>
    // Patrones y utilidades compartidas
    const namePattern = /^[A-Za-zÀ-ÿ\s]{2,50}$/u; // acepta acentos y espacios
    const dniDigitsMin = 4;
    const dniDigitsMax = 13;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Enmascarado y saneamiento de inputs para agregar/editar
    document.addEventListener('input', function(e) {
        const el = e.target;
        if (!el) return;
        // Nombre / Apellido: permitir letras con acentos y espacios
        if (el.id === 'nombre' || el.id === 'apellido' || el.id === 'edit-nombre' || el.id === 'edit-apellido') {
            el.value = el.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '').slice(0,50);
        }
        // Teléfono: solo dígitos, máximo 8, formatear 4-4 con '-'
        if (el.id === 'telefono' || el.id === 'edit-telefono') {
            let digits = el.value.replace(/\D/g, '').slice(0,8);
            if (digits.length > 4) el.value = digits.slice(0,4) + '-' + digits.slice(4);
            else el.value = digits;
        }
        // DNI: solo dígitos, máximo 13, agrupar de 4 hasta dejar 5 finales juntos
        if (el.id === 'dni' || el.id === 'edit-dni') {
            let digits = el.value.replace(/\D/g, '').slice(0,dniDigitsMax);
            let parts = [];
            let i = 0;
            let remaining = digits.length;
            while (remaining > 5) {
                parts.push(digits.substr(i,4));
                i += 4;
                remaining -= 4;
            }
            if (i < digits.length) parts.push(digits.substr(i));
            el.value = parts.join(' ');
        }
    });

    // Cancelar modal agregar
    document.getElementById('cancelarModalAgregar').onclick = function() {
        document.getElementById('modalCliente').style.display = 'none';
        const msg = document.getElementById('msgCliente'); if (msg) msg.innerHTML = '';
    };

    // Envío del formulario de agregar cliente con validaciones
    document.getElementById('formCliente').onsubmit = function(e) {
        e.preventDefault();
        const form = e.target;
        const msgCliente = document.getElementById('msgCliente');
        msgCliente.innerHTML = '';

        const nombre = form.nombre.value.trim();
        const apellido = form.apellido.value.trim();
        const dniRaw = form.dni.value.replace(/\s/g,'');
        const telefonoRaw = form.telefono.value.replace(/\D/g,'');
        const email = form.email.value.trim();
        const direccion = form.direccion.value.trim();

        let errors = [];
        if (!nombre || !namePattern.test(nombre)) errors.push('Nombre inválido (solo letras y espacios, 2-50 caracteres)');
        if (!apellido || !namePattern.test(apellido)) errors.push('Apellido inválido (solo letras y espacios, 2-50 caracteres)');
        if (!(dniRaw.length >= dniDigitsMin && dniRaw.length <= dniDigitsMax)) errors.push(`DNI inválido (entre ${dniDigitsMin} y ${dniDigitsMax} dígitos)`);
        if (!(telefonoRaw.length === 8)) errors.push('Teléfono debe tener exactamente 8 dígitos');
        if (email && !emailPattern.test(email)) errors.push('Email inválido');

        if (errors.length) {
            msgCliente.innerHTML = '<div class="alert alert-danger">' + errors.join('<br>') + '</div>';
            return;
        }

        const datos = {
            nombre: nombre,
            apellido: apellido,
            dni: dniRaw,
            telefono: telefonoRaw,
            email: email,
            direccion: direccion
        };

        fetch('/sistema/public/index.php?route=clientes&caso=crearCliente', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(async r => {
            if (!r.ok) {
                const txt = await r.text(); throw new Error(txt || ('HTTP ' + r.status));
            }
            return r.json();
        })
        .then(res => {
            if (res.status === 201) {
                msgCliente.innerHTML = '<div class="alert alert-success">Cliente creado correctamente</div>';
                form.reset();
                cargarClientes();
                setTimeout(() => { document.getElementById('modalCliente').style.display = 'none'; msgCliente.innerHTML = ''; }, 1200);
            } else {
                msgCliente.innerHTML = '<div class="alert alert-danger">' + (res.message || 'Error al crear cliente') + '</div>';
            }
        })
        .catch(err => {
            msgCliente.innerHTML = '<div class="alert alert-danger">Error: ' + (err.message || 'Error de red') + '</div>';
        });
    };

    // Update edit modal validation (usa mismos patrones)
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'formEditarCliente') {
            e.preventDefault();
            const form = e.target;
            const msgEditar = document.getElementById('msgEditarCliente');
            msgEditar.innerHTML = '';
            let errors = [];

            const nombre = form.nombre.value.trim();
            const apellido = form.apellido.value.trim();
            const dniRaw = form.dni.value.replace(/\s/g,'');
            const telefonoRaw = form.telefono.value.replace(/\D/g,'');
            const email = form.email.value.trim();

            if (!nombre || !namePattern.test(nombre)) errors.push('Nombre inválido (solo letras y espacios, 2-50 caracteres)');
            if (!apellido || !namePattern.test(apellido)) errors.push('Apellido inválido (solo letras y espacios, 2-50 caracteres)');
            if (!(dniRaw.length >= dniDigitsMin && dniRaw.length <= dniDigitsMax)) errors.push(`DNI inválido (entre ${dniDigitsMin} y ${dniDigitsMax} dígitos)`);
            if (!(telefonoRaw.length === 8)) errors.push('Teléfono debe tener exactamente 8 dígitos');
            if (email && !emailPattern.test(email)) errors.push('Email inválido');

            if (errors.length) {
                msgEditar.innerHTML = '<div class="alert alert-danger">' + errors.join('<br>') + '</div>';
                return false;
            }

            const datos = {
                id_cliente: form.id_cliente.value,
                nombre: nombre,
                apellido: apellido,
                dni: dniRaw,
                telefono: telefonoRaw,
                email: email,
                direccion: form.direccion.value.trim(),
                estado: form.estado.value
            };
            fetch('/sistema/public/index.php?route=clientes&caso=editarCliente', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
            .then(async r => {
                if (!r.ok) {
                    const txt = await r.text(); throw new Error(txt || ('HTTP ' + r.status));
                }
                return r.json();
            })
            .then(res => {
                if (res.status === 200) {
                    msgEditar.innerHTML = '<div class="alert alert-success">Cliente actualizado correctamente</div>';
                    cargarClientes();
                    setTimeout(() => { document.getElementById('modalEditarCliente').style.display = 'none'; msgEditar.innerHTML = ''; }, 1200);
                } else {
                    msgEditar.innerHTML = '<div class="alert alert-danger">' + (res.message || 'Error al editar cliente') + '</div>';
                }
            })
            .catch(err => {
                msgEditar.innerHTML = '<div class="alert alert-danger">Error: ' + (err.message || 'Error de red') + '</div>';
            });
        }
    });

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

    // PDF Preview & Download
    document.getElementById('btn-pdf-clientes').onclick = function() {
        // Copia la tabla de clientes para previsualizar
        const tabla = document.getElementById('tablaClientes');
        const preview = document.getElementById('pdf-preview-content');
        if (tabla && tabla.style.display !== 'none') {
            preview.innerHTML = '<table class="table table-bordered">' + tabla.innerHTML + '</table>';
        } else {
            preview.innerHTML = '<div class="alert alert-info">No hay clientes para exportar.</div>';
        }
        document.getElementById('modalPdfPreview').style.display = 'block';
        registrarBitacora('EXPORTAR_PDF', 'Exportó clientes a PDF');
    };
    document.getElementById('btn-cerrar-pdf-modal').onclick = function() {
        document.getElementById('modalPdfPreview').style.display = 'none';
    };
    // Descargar PDF usando html2pdf.js
    document.getElementById('btn-descargar-pdf').onclick = function() {
        const tabla = document.getElementById('tablaClientes');
        const filas = Array.from(tabla.querySelectorAll('tbody tr'));
        const columnas = ['ID', 'Nombre', 'Apellido', 'DNI', 'Teléfono', 'Correo', 'Dirección', 'Estado'];
        const datos = filas.map(tr => {
            const tds = Array.from(tr.querySelectorAll('td'));
            if (tds.length >= 8) {
                return [
                    tds[0].textContent,
                    tds[1].textContent,
                    tds[2].textContent,
                    tds[3].textContent,
                    tds[4].textContent,
                    tds[5].textContent,
                    tds[6].textContent,
                    tds[7].textContent
                ];
            }
            return null;
        }).filter(Boolean);
        generarPDFCustom({
            titulo: 'Reporte de Clientes',
            subtitulo: "Tesoro D' MIMI",
            columnas,
            datos,
            filename: `clientes_${new Date().toISOString().split('T')[0]}.pdf`
        });
    };

    function generarPDFCustom({titulo, subtitulo, columnas, datos, filename}) {
        const fechaActual = new Date().toLocaleDateString('es-ES', {
            year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
        const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
        const element = document.createElement('div');
        element.innerHTML = `
        <div class="container" id="contenido-pdf" style="max-width:1100px;margin:0 auto;background:#fff;border:1px solid #ddd;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
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
            <div class="section" style="padding:18px 24px;">
                <div class="resumen" style="margin-bottom:15px;font-size:11px;padding:8px;background-color:#f8f9fa;border-left:4px solid #E38B29;">
                    <strong>Total de registros: ${datos.length}</strong>
                </div>
                <table style="width:100%;border-collapse:collapse;margin-top:15px;font-size:9px;page-break-inside:auto;">
                    <thead>
                        <tr>
                            ${columnas.map(col => `<th style='background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;padding:10px 8px;text-align:left;border:1px solid #B97222;'>${col}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${datos.map((fila, idx) => `<tr>${fila.map((celda, i) => `<td style='border:1px solid #dee2e6;padding:9px 8px;vertical-align:top;${i===0?'text-align:center;':''}'>${celda}</td>`).join('')}</tr>`).join('')}
                        ${datos.length === 0 ? `<tr><td colspan='${columnas.length}' style='text-align:center;padding:14px;'>No hay registros.</td></tr>` : ''}
                    </tbody>
                </table>
            </div>
            <div class="footer" style="text-align:center;padding:16px 24px;color:#6c757d;font-size:12px;border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>`;
        const opt = {
            margin: [8,8,8,8],
            filename: filename || `reporte_${new Date().toISOString().split('T')[0]}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
<!-- html2pdf.js para exportar PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
