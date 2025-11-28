<!DOCTYPE html>
<!-- Modal para subir comprobante desde consultar ventas -->
<div class="modal fade" id="modalSubirBoucher" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir comprobante de pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="formSubirBoucher">
                    <input type="hidden" id="subirBoucherIdFactura" name="id_factura" />
                    <input type="hidden" id="subirBoucherIdCliente" name="id_cliente" />
                    <div class="mb-3">
                        <label for="subirBoucherFile" class="form-label">Selecciona la foto del comprobante</label>
                        <input type="file" class="form-control" id="subirBoucherFile" name="comprobante" accept="image/*" required />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnEnviarBoucher">Subir</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

use app\config\SessionHelper;

// Validar que el usuario esté autenticado
if (!SessionHelper::isLoggedIn()) {
    header('Location: /');
    exit;
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Consultar Ventas</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/sistema/public/registrar-venta">Módulo de Ventas</a></li>
                <li class="breadcrumb-item active">Consultar Ventas</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Historial de Ventas</h5>
                            </div>
                            <div class="col-auto">
                                <a href="/sistema/public/registrar-venta" class="btn btn-success">
                                    <i class="bx bx-plus"></i> Nueva Venta
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="inputBuscar" 
                                       placeholder="Buscar por cliente o factura...">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="inputFecha">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary" onclick="filtrarVentas()">
                                    <i class="bx bx-search"></i> Filtrar
                                </button>
                                <button class="btn btn-outline-secondary ms-2" id="btnMostrarBouchers" title="Mostrar comprobantes (tarjeta/transferencia)">
                                    <i class="bx bx-image"></i> Mostrar bouchers
                                </button>
                                <button class="btn btn-outline-info ms-2" id="btnVerConteoPorPago" title="Ver conteo por tipo de pago" onclick="showConteoToggle(event)">
                                    <i class="bx bx-bar-chart"></i> Ver conteo por pago
                                </button>
                                <button class="btn btn-danger ms-2" id="btnExportarPDF" title="Exportar todas las ventas en PDF">
                                    <i class="bx bx-file"></i> Exportar todo en PDF
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tablaVentas">
                                    <thead class="table-primary">
                                    <tr>
                                        <th>#Recibo</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Método Pago</th>
                                        <th>Total</th>
                                        <th>Items</th>
                                        <th>Estado</th>
                                        <th>Acciones <span class="badge bg-info ms-2"></span></th>
                                </thead>
                                <tbody id="cuerpoTabla">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="spinner-border" role="status">
                                                <span class="visually-hidden">Cargando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Conteo por tipo de pago (panel superior, oculto por defecto) -->
<div class="container mt-3" id="conteo-por-pago" style="display:none;">
    <div class="conteo-wrap">
        <div class="conteo-row w-100">
            <div class="conteo-cards">
                <div class="conteo-card" data-tipo="EFECTIVO">
                    <div class="conteo-header"><i class="bx bx-wallet"></i> <span>Efectivo</span></div>
                    <div class="conteo-body">
                        <div class="conteo-valor fs-count" data-field="count">0</div>
                        <div class="conteo-sub">Ventas</div>
                        <div class="conteo-total" data-field="total">Lps. 0.00</div>
                    </div>
                </div>

                <div class="conteo-card" data-tipo="TARJETA">
                    <div class="conteo-header"><i class="bx bx-credit-card"></i> <span>Tarjeta</span></div>
                    <div class="conteo-body">
                        <div class="conteo-valor fs-count" data-field="count">0</div>
                        <div class="conteo-sub">Ventas</div>
                        <div class="conteo-total" data-field="total">Lps. 0.00</div>
                    </div>
                </div>

                <div class="conteo-card" data-tipo="TRANSFERENCIA">
                    <div class="conteo-header"><i class="bx bx-transfer-alt"></i> <span>Transferencia</span></div>
                    <div class="conteo-body">
                        <div class="conteo-valor fs-count" data-field="count">0</div>
                        <div class="conteo-sub">Ventas</div>
                        <div class="conteo-total" data-field="total">Lps. 0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                            <!-- Modal Confirmación Guardar/Imprimir PDF -->
                            <div class="modal fade" id="modalConfirmPdf" tabindex="-1" aria-hidden="true">
                              <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Guardar / Imprimir</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                  </div>
                                  <div class="modal-body">
                                    <p id="modalConfirmPdfMessage">¿Desea guardar el Recibo como PDF o imprimirla?</p>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="btnImprimirPdf">Imprimir</button>
                                    <button type="button" class="btn btn-primary" id="btnGuardarPdf">Guardar como PDF</button>
                                  </div>
                                </div>
                              </div>
                            </div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Recibo #<span id="numFactura"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Cliente:</strong> <span id="clienteNombre"></span></p>
                        <p><strong>DNI:</strong> <span id="clienteDNI"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Vendedor:</strong> <span id="vendedorNombre"></span></p>
                        <p><strong>Fecha:</strong> <span id="fechaVenta"></span></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalles">
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-8"></div>
                    <div class="col-4">
                        <div class="bg-light p-3 rounded">
                            <p class="mb-2"><strong>Método Pago:</strong> <span id="metodoPago"></span></p>
                            <p class="mb-0"><strong>Total:</strong> <h5 class="text-success">S/. <span id="totalMonto">0.00</span></h5></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="imprimirFactura()">
                    <i class="bx bx-printer"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para previsualizar comprobante en grande -->
<div class="modal fade" id="modalBoucherPreview" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalBoucherImg" src="" alt="Comprobante" style="max-width:100%; max-height:80vh; object-fit:contain;" />
            </div>
            <div class="modal-footer">
                <a id="modalBoucherLink" href="#" target="_blank" class="btn btn-outline-primary">Abrir en nueva pestaña</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge-estado {
        padding: 0.5rem 0.75rem;
        border-radius: 0.25rem;
    }

    /* Estilos para el panel de conteo por pago (mejorados y centrados) */
    .conteo-wrap { display:flex; justify-content:center; }
    .conteo-row { max-width:1100px; margin:0 auto; }
    .conteo-cards { display:flex; gap:1rem; align-items:stretch; justify-content:center; flex-wrap:wrap; }
    .conteo-card {
        flex: 0 1 260px; /* ancho base, flexible */
        min-width:200px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(16,24,40,0.06);
        overflow: hidden;
        transition: transform .18s ease, box-shadow .18s ease;
        border: 1px solid rgba(16,24,40,0.04);
        display:flex;
        flex-direction:column;
        align-items:center;
        padding:0.6rem 0.6rem 1rem 0.6rem;
    }
    .conteo-card:hover { transform: translateY(-6px); box-shadow: 0 12px 34px rgba(16,24,40,0.10); }
    .conteo-card .conteo-header { width:100%; padding:0.6rem 0.8rem; display:flex; align-items:center; justify-content:center; gap:0.6rem; color: #fff; font-weight:700; text-transform:uppercase; border-top-left-radius:12px; border-top-right-radius:12px; }
    .conteo-card .conteo-header i { font-size:1.15rem; opacity:0.95; }
    .conteo-card .conteo-body { padding:0.8rem 0.6rem; text-align:center; width:100%; }
    .conteo-card .conteo-valor { color: #111827; }
    .conteo-card .conteo-sub { color: #6b7280; font-size:0.85rem; }
    .conteo-card .conteo-total { color:#0f5132; font-weight:700; margin-top:0.6rem; }
    /* Color accents por tipo */
    .conteo-card[data-tipo="EFECTIVO"] .conteo-header { background: linear-gradient(90deg,#2dd4bf,#06b6d4); }
    .conteo-card[data-tipo="TARJETA"] .conteo-header { background: linear-gradient(90deg,#6366f1,#8b5cf6); }
    .conteo-card[data-tipo="TRANSFERENCIA"] .conteo-header { background: linear-gradient(90deg,#f59e0b,#f97316); }
    /* Asegurar que los números luzcan bien */
    .conteo-card .fs-count { font-size:1.5rem; font-weight:800; }
    @media (max-width:720px) {
        .conteo-card { flex: 1 1 100%; }
    }
</style>

<script>
// Notificaciones ligeras: asegura que exista `mostrarNotificacion` en esta página
function mostrarNotificacion(mensaje, tipo = 'success', duracion = 2200) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${tipo}`;
    toast.style.background = '#ffffff';
    toast.style.padding = '10px 12px';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 8px 20px rgba(2,6,23,0.08)';
    toast.style.marginTop = '10px';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '8px';
    toast.style.minWidth = '180px';

    let icono = '✓';
    if (tipo === 'error') icono = '✕';
    else if (tipo === 'warning') icono = '⚠';
    else if (tipo === 'info') icono = 'ⓘ';

    const iconSpan = document.createElement('span');
    iconSpan.textContent = icono;
    iconSpan.style.fontWeight = '700';

    const textSpan = document.createElement('span');
    textSpan.textContent = mensaje;

    toast.appendChild(iconSpan);
    toast.appendChild(textSpan);
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.transition = 'opacity .25s ease, transform .25s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-6px)';
        setTimeout(() => toast.remove(), 300);
    }, duracion);
}

const API_BASE = '/sistema/src/routes/ventas.php';
let showBouchers = false; // toggles filter for tarjeta/transferencia
let lastVentasLoaded = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarVentas();
    // Botón exportar PDF
    document.getElementById('btnExportarPDF').addEventListener('click', async function() {
        const filas = Array.from(document.querySelectorAll('#cuerpoTabla tr'));
        if (filas.length === 0 || filas[0].querySelector('.spinner-border')) {
            alert('No hay ventas para exportar.');
            return;
        }
        // Columnas y datos para el PDF
        const columnas = ['#Recibo', 'Cliente', 'Fecha', 'Método Pago', 'Total', 'Items', 'Estado'];
        const datos = filas.map(tr => {
            const tds = Array.from(tr.querySelectorAll('td'));
            // Solo filas con el número correcto de columnas
            if (tds.length >= 7) {
                return [
                    tds[0].textContent,
                    tds[1].textContent,
                    tds[2].textContent,
                    tds[3].textContent,
                    tds[4].textContent,
                    tds[5].textContent,
                    tds[6].textContent
                ];
            }
            return null;
        }).filter(Boolean);
        await (window.html2pdf ? Promise.resolve() : new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        }));
        generarPDFCustom({
            titulo: 'Reporte de Ventas',
            subtitulo: "Tesoro D' MIMI",
            columnas,
            datos,
            filename: `ventas_${new Date().toISOString().split('T')[0]}.pdf`
        });
        registrarBitacora('EXPORTAR_PDF', 'Exportó ventas a PDF');
    });
    // Handler para abrir modal de subir comprobante
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.subir-boucher-btn');
        if (btn) {
            const idFactura = btn.getAttribute('data-id-factura');
            const idCliente = btn.getAttribute('data-id-cliente');
            document.getElementById('subirBoucherIdFactura').value = idFactura;
            document.getElementById('subirBoucherIdCliente').value = idCliente;
            document.getElementById('subirBoucherFile').value = '';
            const modal = new bootstrap.Modal(document.getElementById('modalSubirBoucher'));
            modal.show();
        }
    });

    // Handler para enviar comprobante
    document.getElementById('btnEnviarBoucher').addEventListener('click', function() {
        const form = document.getElementById('formSubirBoucher');
        const fileInput = document.getElementById('subirBoucherFile');
        if (!fileInput.files.length) {
            alert('Selecciona una imagen de comprobante.');
            return;
        }
        let idFactura = document.getElementById('subirBoucherIdFactura').value;
        let idCliente = document.getElementById('subirBoucherIdCliente').value;
        if (!idFactura) {
            alert('No se detectó el número de Recibo.');
            return;
        }
        if (!idCliente) idCliente = '0';
        const formData = new FormData();
        formData.append('id_factura', idFactura);
        formData.append('id_cliente', idCliente);
        formData.append('comprobante', fileInput.files[0]);

        fetch('/sistema/src/routes/ventas.php?caso=guardarComprobantePago', {
            method: 'POST',
            body: formData
        })
        .then(r => r.text())
        .then(text => {
            console.log('Respuesta subida comprobante (cruda):', text);
            let data;
            try { data = JSON.parse(text); } catch(e) { data = null; }

            if (data && data.status === 200) {
                // Éxito explícito desde el servidor
                mostrarNotificacion('Comprobante subido correctamente', 'success');
                try { bootstrap.Modal.getInstance(document.getElementById('modalSubirBoucher')).hide(); } catch(e) { document.getElementById('modalSubirBoucher').style.display = 'none'; }
                cargarVentas(); // Recarga automáticamente las ventas
            } else if (data && data.status && data.status !== 200) {
                // Respuesta JSON con error
                mostrarNotificacion('Error: ' + (data.message || 'No se pudo subir el comprobante.'), 'error');
                console.error('Error subida comprobante (JSON):', data);
            } else {
                // No vino JSON. En muchos casos el backend ya subió el archivo pero devolvió texto/HTML.
                // Mostrar la respuesta cruda al usuario y refrescar para que vea el comprobante.
                mostrarNotificacion(text || 'Comprobante subido (respuesta no estándar)', 'info');
                try { bootstrap.Modal.getInstance(document.getElementById('modalSubirBoucher')).hide(); } catch(e) { document.getElementById('modalSubirBoucher').style.display = 'none'; }
                cargarVentas();
            }
        })
        .catch(err => {
            console.error('Error enviando comprobante:', err);
            mostrarNotificacion('Error de conexión al subir comprobante', 'error');
        });
    });

    // Recargar ventas automáticamente al cerrar el modal de subir comprobante y limpiar backdrop
    document.getElementById('modalSubirBoucher').addEventListener('hidden.bs.modal', function() {
        cargarVentas();
        // Eliminar manualmente cualquier backdrop que quede
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    });

    document.getElementById('inputBuscar').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') filtrarVentas();
    });

    const btnB = document.getElementById('btnMostrarBouchers');
    if (btnB) {
        btnB.addEventListener('click', function() {
            const mostrar = btnB.classList.toggle('active');
            btnB.classList.toggle('btn-outline-secondary', !mostrar);
            btnB.classList.toggle('btn-secondary', mostrar);
            btnB.textContent = mostrar ? ' Ocultar bouchers' : ' Mostrar bouchers';

            // Solo ocultar/mostrar las fotos de bouchers de TRANSFERENCIA y TARJETA
            document.querySelectorAll('.img-boucher[data-metodo]').forEach(img => {
                const metodo = (img.dataset.metodo || '').toUpperCase();
                if (metodo === 'TRANSFERENCIA' || metodo === 'TARJETA') {
                    img.style.display = mostrar ? '' : 'none';
                }
            });
        });
    }

    // Botón Ver conteo por pago
    const btnConteo = document.getElementById('btnVerConteoPorPago');
    if (btnConteo) {
        btnConteo.addEventListener('click', function() {
            const el = document.getElementById('conteo-por-pago');
            const mostrar = el.style.display === 'none' || el.style.display === '' ? true : false;
            if (mostrar) {
                el.style.display = 'block';
                btnConteo.classList.remove('btn-outline-info');
                btnConteo.classList.add('btn-info');
                btnConteo.textContent = ' Ocultar conteo por pago';
                // Rellenar con los datos actuales
                try { actualizarConteoPorPago(lastVentasLoaded); } catch(e) { console.error(e); }
            } else {
                el.style.display = 'none';
                btnConteo.classList.remove('btn-info');
                btnConteo.classList.add('btn-outline-info');
                btnConteo.textContent = ' Ver conteo por pago';
            }
        });
    }

    // Delegación: abrir modal grande al hacer click en miniatura
    document.body.addEventListener('click', function(e) {
        const tgt = e.target;
        if (tgt && tgt.classList && tgt.classList.contains('img-boucher')) {
            const src = tgt.getAttribute('src');
            if (!src) return;

            // Si la miniatura vino de un recurso no-imagen (por ejemplo PDF), abrir en nueva pestaña
            const ext = (src.split('.').pop() || '').toLowerCase();
            const modalEl = document.getElementById('modalBoucherPreview');
            const imgEl = document.getElementById('modalBoucherImg');
            const linkEl = document.getElementById('modalBoucherLink');

            // Establecer enlace directo siempre
            linkEl.href = src;

            // Intentar mostrar la imagen en el modal
            imgEl.src = src;
            imgEl.style.display = 'block';

            // Mostrar modal
            const bs = new bootstrap.Modal(modalEl);
            bs.show();
        }
    });

    // Asegurar funcionamiento del botón por delegación si el handler directo falla
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('#btnVerConteoPorPago');
        if (!btn) return;
        try {
            const el = document.getElementById('conteo-por-pago');
            const mostrar = el.style.display === 'none' || el.style.display === '' ? true : false;
            if (mostrar) {
                el.style.display = 'block';
                btn.classList.remove('btn-outline-info');
                btn.classList.add('btn-info');
                btn.innerHTML = '<i class="bx bx-bar-chart"></i> Ocultar conteo por pago';
                actualizarConteoPorPago(lastVentasLoaded);
            } else {
                el.style.display = 'none';
                btn.classList.remove('btn-info');
                btn.classList.add('btn-outline-info');
                btn.innerHTML = '<i class="bx bx-bar-chart"></i> Ver conteo por pago';
            }
        } catch (err) {
            console.error('Error manejando btnVerConteoPorPago delegación:', err);
        }
    });
});

function cargarVentas() {
    fetch(`${API_BASE}?caso=listarFacturas&limite=100`)
        .then(response => response.json())
        .then(data => {
            const ok = (data && (data.status === 200 || data.success));
            if (ok) {
                // Guardar las ventas crudas para uso en paneles/resúmenes
                lastVentasLoaded = data.data || [];
                const ventas = applyBoucherFilter(lastVentasLoaded);
                mostrarVentas(ventas);
            } else {
                mostrarError(data.message || 'Error al cargar ventas');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        });
}

function mostrarVentas(ventas) {
    const tbody = document.getElementById('cuerpoTabla');
    
    if (ventas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No hay ventas registradas</td></tr>';
        return;
    }

    tbody.innerHTML = ventas.map(venta => {
        const metodo = (venta.METODO_PAGO || '').toUpperCase().replace(/\s+/g, '');
        const esMetodoBaucher = metodo.includes('TRANSFERENCIA') || metodo.includes('TARJETA');
        let estado = 'PAGADA';
        let estadoColor = 'success';
        let estadoTexto = 'PAGADA';
        let comprobanteUrl = `/sistema/src/routes/ventas.php?caso=servirComprobante&id_factura=${venta.ID_FACTURA}`;
        let mostrarBotonBaucher = false;
        if (esMetodoBaucher) {
            if (venta.COMPROBANTE_SUBIDO) {
                estado = 'PAGADA';
                estadoColor = 'success';
                estadoTexto = 'PAGADA';
                mostrarBotonBaucher = true;
            } else {
                estado = 'ACTIVA';
                estadoColor = 'danger';
                estadoTexto = 'ACTIVA (pendiente comprobante)';
                mostrarBotonBaucher = false;
            }
        }
        return `<tr>
            <td><strong>#${venta.ID_FACTURA}</strong></td>
            <td>${venta.CLIENTE_NOMBRE || 'N/A'}</td>
            <td>${formatearFecha(venta.FECHA_VENTA)}</td>
            <td><small>${venta.METODO_PAGO || 'N/A'}</small></td>
            <td class="text-success"><strong>S/. ${parseFloat(venta.TOTAL_VENTA || 0).toFixed(2)}</strong></td>
            <td><center><span class="badge bg-info">${venta.CANTIDAD_ITEMS || 0}</span></center></td>
            <td>
                <span class="badge badge-estado bg-${estadoColor}">${estadoTexto}</span>
            </td>
            <td class="text-center">
                <div style="display:flex; align-items:center; gap:0.5rem; justify-content:center;">
                    ${esMetodoBaucher && mostrarBotonBaucher ? `
                        <img src="${comprobanteUrl}" alt="comprobante" class="img-boucher" data-metodo="${venta.METODO_PAGO || ''}" style="max-width:80px; max-height:60px; object-fit:cover; border-radius:4px;" />
                    ` : ''}
                    ${esMetodoBaucher && !mostrarBotonBaucher && estado === 'ACTIVA' ? `
                        <button type="button" class="btn btn-sm btn-warning subir-boucher-btn" data-id-factura="${venta.ID_FACTURA}" data-id-cliente="${typeof venta.ID_CLIENTE !== 'undefined' && venta.ID_CLIENTE !== null ? venta.ID_CLIENTE : 0}" data-bs-toggle="modal" data-bs-target="#modalSubirBoucher">
                            <i class="bx bx-upload"></i> Subir comprobante
                        </button>
                    ` : ''}
                    <button type="button" class="badge bg-info text-white btn-badge" data-bs-toggle="modal" data-bs-target="#modalDetalles" onclick="verDetalles(${venta.ID_FACTURA})">
                        <i class="bx bx-show me-1"></i> Ver / Imprimir
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    // Al terminar de renderizar, aplicar el estado del botón a todas las imágenes
    setTimeout(() => {
        const btnB = document.getElementById('btnMostrarBouchers');
        const mostrar = btnB && btnB.classList.contains('active');
        document.querySelectorAll('.img-boucher[data-metodo]').forEach(img => {
            let metodo = (img.dataset.metodo || '').toUpperCase().replace(/\s+/g, '');
            // Cubrir variantes como "TRANSFERENCIA BANCARIA", "TARJETA DE CRÉDITO", etc.
            if (metodo.includes('TRANSFERENCIA') || metodo.includes('TARJETA')) {
                if (mostrar) {
                    img.style.setProperty('display', '', 'important');
                } else {
                    img.style.setProperty('display', 'none', 'important');
                }
            }
        });
    }, 0);
    // Actualizar panel de conteo si está visible
    try { actualizarConteoIfVisible(); } catch(e) { /* noop */ }
}

/**
 * Actualiza el panel de conteo por tipo de pago usando la lista de ventas provista
 */
function actualizarConteoPorPago(ventas) {
    if (!ventas || !Array.isArray(ventas)) ventas = [];

    // Normalizar método y agrupar
    const resumen = {
        EFECTIVO: { count: 0, total: 0 },
        TARJETA: { count: 0, total: 0 },
        TRANSFERENCIA: { count: 0, total: 0 }
    };

    ventas.forEach(v => {
        const metodo = (v.METODO_PAGO || '').toUpperCase();
        const total = parseFloat(v.TOTAL_VENTA || v.TOTAL || 0) || 0;
        if (metodo.indexOf('TARJETA') !== -1) {
            resumen.TARJETA.count += 1; resumen.TARJETA.total += total;
        } else if (metodo.indexOf('TRANSFERENCIA') !== -1) {
            resumen.TRANSFERENCIA.count += 1; resumen.TRANSFERENCIA.total += total;
        } else {
            // Todo lo demás lo consideramos efectivo
            resumen.EFECTIVO.count += 1; resumen.EFECTIVO.total += total;
        }
    });

    // Mapear a tarjetas y actualizar DOM
    document.querySelectorAll('#conteo-por-pago .conteo-card').forEach(card => {
        const tipo = (card.dataset.tipo || '').toUpperCase();
        const data = resumen[tipo] || { count: 0, total: 0 };
        const countEl = card.querySelector('[data-field="count"]');
        const totalEl = card.querySelector('[data-field="total"]');
        if (countEl) countEl.textContent = data.count;
        if (totalEl) totalEl.textContent = 'Lps. ' + data.total.toFixed(2);
    });
}

// Si el panel está visible (por ejemplo al recargar), mantenerlo sincronizado
function actualizarConteoIfVisible() {
    const el = document.getElementById('conteo-por-pago');
    if (el && el.style.display !== 'none' && typeof actualizarConteoPorPago === 'function') {
        actualizarConteoPorPago(lastVentasLoaded);
    }
}

// Función global segura para alternar el panel (se liga también con onclick inline)
function showConteoToggle(e) {
    try {
        const btn = document.getElementById('btnVerConteoPorPago');
        const el = document.getElementById('conteo-por-pago');
        if (!el) return;
        const mostrar = el.style.display === 'none' || el.style.display === '';
        if (mostrar) {
            el.style.display = 'block';
            if (btn) { btn.classList.remove('btn-outline-info'); btn.classList.add('btn-info'); btn.innerHTML = '<i class="bx bx-bar-chart"></i> Ocultar conteo por pago'; }
            actualizarConteoPorPago(lastVentasLoaded);
            // Scroll suave para mostrar el panel si el usuario está abajo
            try { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch(e) {}
        } else {
            el.style.display = 'none';
            if (btn) { btn.classList.remove('btn-info'); btn.classList.add('btn-outline-info'); btn.innerHTML = '<i class="bx bx-bar-chart"></i> Ver conteo por pago'; }
        }
    } catch (err) {
        console.error('showConteoToggle error:', err);
    }
}

function applyBoucherFilter(ventas) {
    if (!showBouchers) return ventas;
    const allowed = ['TARJETA', 'TRANSFERENCIA', 'TRANSFERENCIA BANCARIA'];
    return ventas.filter(v => {
        const m = (v.METODO_PAGO || '').toUpperCase();
        return allowed.includes(m) || allowed.some(a => m.indexOf(a) !== -1);
    });
}

function verDetalles(idFactura) {
    fetch(`${API_BASE}?caso=obtenerDetallesFactura&id_factura=${idFactura}`)
        .then(response => response.json())
        .then(data => {
            const ok = data && (data.status === 200 || data.success);
            if (ok) {
                // Algunos endpoints retornan { status:200, data: {...} }
                // otros pueden devolver { success: true, data: {...} }
                mostrarDetalles(data.data || data);
            } else {
                console.error('Error al obtener detalles', data && data.message ? data.message : data);
                mostrarError('No se pudo obtener detalles del Recibo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión al obtener detalles');
        });
}

function mostrarDetalles(factura) {
    document.getElementById('numFactura').textContent = factura.ID_FACTURA;
    document.getElementById('clienteNombre').textContent = `${factura.CLIENTE_NOMBRE} ${factura.CLIENTE_APELLIDO || ''}`;
    document.getElementById('clienteDNI').textContent = factura.CLIENTE_DNI;
    document.getElementById('vendedorNombre').textContent = factura.USUARIO_NOMBRE;
    document.getElementById('fechaVenta').textContent = formatearFecha(factura.FECHA_VENTA);
    document.getElementById('metodoPago').textContent = factura.METODO_PAGO;
    document.getElementById('totalMonto').textContent = parseFloat(factura.TOTAL_VENTA || 0).toFixed(2);

    const tbody = document.getElementById('tablaDetalles');
    tbody.innerHTML = (factura.DETALLES || []).map(detalle => `
        <tr>
            <td>${detalle.PRODUCTO_NOMBRE}</td>
            <td>${detalle.CANTIDAD}</td>
            <td>S/. ${parseFloat(detalle.PRECIO_VENTA || 0).toFixed(2)}</td>
            <td>S/. ${parseFloat(detalle.SUBTOTAL || 0).toFixed(2)}</td>
        </tr>
    `).join('');
}

function filtrarVentas() {
    const busqueda = document.getElementById('inputBuscar').value.trim();
    const fecha = document.getElementById('inputFecha').value.trim();

    const qs = new URLSearchParams({
        caso: 'listarFacturas',
        limite: 100
    });
    if (busqueda !== '') qs.append('busqueda', busqueda);
    if (fecha !== '') qs.append('fecha', fecha);

    fetch(`${API_BASE}?${qs.toString()}`)
        .then(response => response.json())
        .then(data => {
            const ok = (data && (data.status === 200 || data.success));
            if (ok) {
                // Guardar ventas crudas para el panel de conteo
                lastVentasLoaded = data.data || [];
                const ventas = applyBoucherFilter(lastVentasLoaded);
                mostrarVentas(ventas);
            } else {
                mostrarError(data.message || 'Error al filtrar ventas');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            mostrarError('Error de conexión');
        });
}

function imprimirFactura() {
    const numFactura = document.getElementById('numFactura').textContent.trim();

    // Recolectar datos visibles en el modal
    const cliente = document.getElementById('clienteNombre')?.textContent.trim() || '';
    const clienteDNI = document.getElementById('clienteDNI')?.textContent.trim() || '';
    const vendedor = document.getElementById('vendedorNombre')?.textContent.trim() || '';
    const fecha = document.getElementById('fechaVenta')?.textContent.trim() || '';
    const metodo = document.getElementById('metodoPago')?.textContent.trim() || '';
    const total = document.getElementById('totalMonto')?.textContent.trim() || '';

    // Construir filas de la tabla
    const filas = Array.from(document.querySelectorAll('#tablaDetalles tr')).map(tr => {
        const tds = tr.querySelectorAll('td');
        return {
            producto: tds[0]?.textContent.trim() || '',
            cantidad: tds[1]?.textContent.trim() || '',
            precio: tds[2]?.textContent.trim() || '',
            subtotal: tds[3]?.textContent.trim() || ''
        };
    });

    // Generar HTML imprimible con diseño, logo y nombre empresa
    const baseUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/public') + 1)}`;
    const logoUrl = `${baseUrl}src/Views/assets/img/Tesorodemimi.jpg`;
    const invoiceHtml = `
        <div style="font-family: Arial, Helvetica, sans-serif; padding:20px; max-width:800px; margin:0 auto; color:#111; background:#fff; border-radius:10px; border:1px solid #e3c08b; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
                <img src="${logoUrl}" alt="Logo" style="width:60px;height:60px;border-radius:8px;object-fit:cover;background:#fff;border:2px solid #E38B29;">
                <div>
                    <h1 style="margin:0;font-size:22px;color:#E38B29;letter-spacing:.5px;">Tesoro D' MIMI</h1>
                    <h2 style="margin:2px 0 4px;font-size:14px;font-weight:normal;opacity:.9;color:#B97222;">Recibo de Venta</h2>
                    <div style="font-size:12px;opacity:.9;">Generado el: ${fecha}</div>
                </div>
            </div>
            <hr style="border:1px solid #E38B29;margin-bottom:16px;">
            <h2 style="margin-bottom:0.25rem;color:#B97222;">Recibo #${numFactura}</h2>
            <p style="margin:0.15rem 0;"><strong>Cliente:</strong> ${cliente}</p>
            <p style="margin:0.15rem 0;"><strong>DNI:</strong> ${clienteDNI}</p>
            <p style="margin:0.15rem 0;"><strong>Vendedor:</strong> ${vendedor}</p>
            <p style="margin:0.15rem 0 0.75rem 0;"><strong>Fecha:</strong> ${fecha}</p>
            <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                <thead>
                    <tr>
                        <th style="text-align:left; border-bottom:1px solid #E38B29; padding:8px; background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;">Producto</th>
                        <th style="text-align:right; border-bottom:1px solid #E38B29; padding:8px; background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;">Cantidad</th>
                        <th style="text-align:right; border-bottom:1px solid #E38B29; padding:8px; background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;">Precio Unit.</th>
                        <th style="text-align:right; border-bottom:1px solid #E38B29; padding:8px; background:linear-gradient(90deg,#D7A86E,#E38B29);color:#fff;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    ${filas.map(row => `
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f0f0f0">${row.producto}</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f0f0f0; text-align:right">${row.cantidad}</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f0f0f0; text-align:right">${row.precio}</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f0f0f0; text-align:right">${row.subtotal}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            <div style="margin-top:12px; text-align:right;">
                <h3 style="margin:0;color:#E38B29;">Total: ${total}</h3>
                <p style="margin:0; font-size:0.9rem;"><strong>Método Pago:</strong> ${metodo}</p>
            </div>
            <div style="margin-top:18px;text-align:center;color:#B97222;font-size:13px;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
    `;

    // Cargar html2pdf dinámicamente si se necesita
    function loadHtml2Pdf() {
        return new Promise((resolve, reject) => {
            if (window.html2pdf) return resolve();
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js';
            s.onload = () => resolve();
            s.onerror = () => reject(new Error('No se pudo cargar la librería para generar PDF'));
            document.head.appendChild(s);
        });
    }

    // En lugar de usar confirm(), abrir un modal de confirmación (notificación con acciones)
    // Preparar modal con contenido y datos temporales
    const modalEl = document.getElementById('modalConfirmPdf');
    if (!modalEl) {
        console.error('Modal de confirmación no encontrado');
        return;
    }

    // Guardar datos en dataset para uso por los handlers
    modalEl.dataset.invoiceHtml = invoiceHtml;
    modalEl.dataset.numFactura = numFactura;

    const bsModal = new bootstrap.Modal(modalEl);

    // Limpiar backdrops existentes antes de mostrar el modal para evitar duplicados
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');

    // Cerrar el modal de detalles
    const modalDetalles = document.getElementById('modalDetalles');
    if (modalDetalles) {
        const bsModalDetalles = bootstrap.Modal.getInstance(modalDetalles);
        if (bsModalDetalles) {
            bsModalDetalles.hide();
        }
    }

    bsModal.show();

    // Handlers one-time
    const btnGuardar = modalEl.querySelector('#btnGuardarPdf');
    const btnImprimir = modalEl.querySelector('#btnImprimirPdf');

    function limpiarHandlers() {
        btnGuardar.removeEventListener('click', onGuardar);
        btnImprimir.removeEventListener('click', onImprimir);
        modalEl.removeEventListener('hidden.bs.modal', onHidden);
    }

    function onHidden() {
        limpiarHandlers();
        // Asegurar que no queden backdrops ni la clase modal-open en el body
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
    }

    async function onGuardar() {
        // Registrar en bitácora la acción de imprimir/guardar recibo
        registrarBitacora('IMPRIMIR_RECIBO', `Imprimió/guardó recibo #${numFactura} para cliente ${cliente}`);
        loadHtml2Pdf().then(() => {
            html2pdf().set({
                margin: [8,8,8,8],
                filename: `recibo_${numFactura}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(invoiceHtml).save();
        });
        limpiarHandlers();
        bsModal.hide();
    }
    function onImprimir() {
        // Registrar en bitácora la acción de imprimir recibo
        registrarBitacora('IMPRIMIR_RECIBO', `Imprimió recibo #${numFactura} para cliente ${cliente}`);
        const w = window.open('', '_blank');
        w.document.write(invoiceHtml);
        w.document.close();
        setTimeout(() => { try { w.print(); } catch(e) { console.error(e); } }, 500);
        limpiarHandlers();
        bsModal.hide();
    }
    btnGuardar.addEventListener('click', onGuardar);
    btnImprimir.addEventListener('click', onImprimir);
    function onHidden() { limpiarHandlers(); }
    modalEl.addEventListener('hidden.bs.modal', onHidden);
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

function formatearFecha(fecha) {
    if (!fecha) return '-';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-PE') + ' ' + d.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
}

function mostrarError(mensaje) {
    const tbody = document.getElementById('cuerpoTabla');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">${mensaje}</td></tr>`;
}
</script>

<!-- Botón flotante 'Ir arriba' -->
<style>
    #btn-scroll-top {
        position: fixed;
        right: 20px;
        bottom: 22px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg,#4f46e5,#06b6d4);
        color: #fff;
        box-shadow: 0 8px 22px rgba(2,6,23,0.18);
        border: none;
        cursor: pointer;
        z-index: 1200;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .18s ease, transform .18s ease;
    }
    #btn-scroll-top.visible { opacity: 1; transform: translateY(0); }
    #btn-scroll-top:focus { outline: none; box-shadow: 0 0 0 4px rgba(79,70,229,0.12); }
    @media (max-width:520px) { #btn-scroll-top { right: 14px; bottom: 16px; width:44px; height:44px; } }
</style>

<button id="btn-scroll-top" title="Ir arriba" aria-label="Ir arriba" type="button">
    <i class="bx bx-up-arrow-alt" style="font-size:1.25rem"></i>
</button>

<script>
    (function(){
        const btn = document.getElementById('btn-scroll-top');
        if (!btn) return;

        // Mostrar el botón después de desplazarse cierta distancia
        function toggleBtn(){
            try {
                const y = window.scrollY || document.documentElement.scrollTop;
                if (y > 160) btn.classList.add('visible'); else btn.classList.remove('visible');
            } catch(e) { /* noop */ }
        }

        // Scroll suave al tope
        btn.addEventListener('click', function(){
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch(e){ window.scrollTo(0,0); }
        });

        // Bind events
        window.addEventListener('scroll', toggleBtn, { passive: true });
        window.addEventListener('resize', toggleBtn);

        // Inicial
        setTimeout(toggleBtn, 200);
    })();
</script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>
