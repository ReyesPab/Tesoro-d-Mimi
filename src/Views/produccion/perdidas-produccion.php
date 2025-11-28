<?php
// Opcional: proteger la vista con sesion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>

<style>
/* Alinear el contenido a la par del sidebar */
:root { --sidebar-width: 260px; }
main.content-wrapper {
    margin-left: var(--sidebar-width);
}
@media (max-width: 991.98px) {
    main.content-wrapper {
        margin-left: 0; /* en moviles, sidebar se oculta o se despliega */
    }
}
</style>

<main class="content-wrapper">
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Perdidas de Produccion</h2>
            <button type="button" class="btn btn-danger" id="btn-exportar-pdf">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </button>
        </div>

        <!-- Filtro por produccion -->
        <div class="card mb-4">
            <div class="card-body">
    <form id="form-buscar-perdidas" class="row g-3">
      <!-- ID de producción oculto: no se muestra en el formulario pero se mantiene para uso interno si hace falta -->
      <input type="hidden" id="id_produccion" name="id_produccion" value="">

          <!-- Buscador rápido (búsqueda automática) -->
          <div class="col-md-3">
            <label for="input-buscar-rapido" class="form-label">Buscar</label>
            <input type="search" id="input-buscar-rapido" class="form-control" placeholder="Buscar por producto, motivo, descripción..." autocomplete="off">
          </div>

          <!-- Boton buscar (por compatibilidad, sigue existiendo) -->
          <div class="col-md-2 d-flex align-items-end">
          </div>

          <!-- Boton limpiar / refrescar -->
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" id="btn-limpiar" class="btn btn-outline-secondary w-100">
              Limpiar filtro y mostrar todo
            </button>
          </div>

          <!-- Selector de cantidad de filas -->
          <div class="col-md-3">
            <label for="select-limite" class="form-label">Mostrar registros</label>
            <select id="select-limite" class="form-select">
              <option value="10">10</option>
              <option value="25" selected>25</option>
              <option value="50">50</option>
              <option value="0">Todos</option>
            </select>
          </div>

          <div class="col-12">
            <span id="mensaje-perdidas" class="text-muted"></span>
          </div>
        </form>
            </div>
        </div>

        <!-- Tabla de perdidas -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla-perdidas" class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad perdida</th>
                <th>Motivo</th>
                <th>Descripcion</th>
                <th>Fecha perdida</th>
                <th>Registrado por</th>
                <th>Acciones</th>
              </tr>
            </thead>
                        <tbody>
                            <!-- Se llena por JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>

<?php $baseUrlPerdidas = defined('BASE_URL') ? BASE_URL : '/sistema/public/'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// URL base al endpoint
const URL_PERDIDAS = "<?= $baseUrlPerdidas ?>index.php?route=produccion&caso=obtenerPerdidasPorProduccion";
const buildUrl = (id) =>
  id ? `${URL_PERDIDAS}&id_produccion=${encodeURIComponent(id)}` : URL_PERDIDAS;

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-buscar-perdidas');
  const inputIdProduccion = document.getElementById('id_produccion');
  const inputBuscarRapido = document.getElementById('input-buscar-rapido');
  const btnLimpiar = document.getElementById('btn-limpiar');
  const selectLimite = document.getElementById('select-limite');
  const btnExportar = document.getElementById('btn-exportar-pdf');
  const tbody = document.querySelector('#tabla-perdidas tbody');
  const mensaje = document.getElementById('mensaje-perdidas');

  let perdidasActuales = [];
  let limiteActual = parseInt(selectLimite.value, 10) || 25;
  let searchTerm = '';

  // Util: debounce para evitar llamadas excesivas al tipear
  const debounce = (fn, delay = 250) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), delay);
    };
  };

  // Devuelve el array de perdidas filtrado por searchTerm (si existe)
  const getFilteredPerdidas = () => {
    if (!searchTerm) return perdidasActuales;
    const q = searchTerm.toLowerCase();
    return perdidasActuales.filter(p => {
      const values = [
        (p.nombre ?? p.NOMBRE_PRODUCTO ?? p.PRODUCTO_NOMBRE ?? p.PRODUCTO ?? p.NOMBRE ?? '').toString(),
        (p.MOTIVO_PERDIDA ?? '').toString(),
        (p.DESCRIPCION ?? '').toString(),
        (p.REGISTRADO_POR ?? '').toString(),
        (p.FECHA_PERDIDA ?? '').toString()
      ].join(' ').toLowerCase();
      return values.indexOf(q) !== -1;
    });
  };
  const getProductInfo = (item) => {
    // El nombre real del producto viene de tbl_producto.nombre
    // Priorizar `nombre` y `NOMBRE_PRODUCTO`. Mostrar SOLO el nombre si existe.
    const name = item.nombre ?? item.NOMBRE_PRODUCTO ?? item.PRODUCTO_NOMBRE ?? item.PRODUCTO ?? item.NOMBRE ?? '';
    const id = item.ID_PRODUCTO ?? '';
    if (name) return name; // mostrar el nombre del producto en vez del id
    if (id) return `ID:${id}`; // fallback: mostrar id si no hay nombre
    return '';
  };

  const renderTabla = () => {
    tbody.innerHTML = '';

    if (!perdidasActuales.length) {
      mensaje.textContent = 'No hay perdidas para mostrar.';
      return;
    }

    const filtradas = getFilteredPerdidas();
    const totalFiltradas = filtradas.length;
    const total = perdidasActuales.length;

    const limite = limiteActual === 0
      ? totalFiltradas
      : Math.min(limiteActual, totalFiltradas);

    filtradas.slice(0, limite).forEach((p, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i + 1}</td>
        <td>${getProductInfo(p)}</td>
        <td>${p.CANTIDAD_PERDIDA ?? 0}</td>
        <td>${p.MOTIVO_PERDIDA ?? ''}</td>
        <td>${p.DESCRIPCION ?? ''}</td>
        <td>${p.FECHA_PERDIDA ?? ''}</td>
        <td>${p.REGISTRADO_POR ?? ''}</td>
        <td>
          <button type="button" class="btn btn-sm btn-danger btn-export-individual" data-id="${p.ID_PERDIDA ?? p.ID ?? ''}" data-idx="${perdidasActuales.indexOf(p)}" title="Exportar PDF individual">
            <i class="bi bi-file-earmark-pdf"></i> PDF
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    if (searchTerm) {
      mensaje.textContent = `Mostrando ${limite} de ${totalFiltradas} registro(s) filtrados (total ${total}).`;
    } else {
      mensaje.textContent = `Mostrando ${limite} de ${total} registro(s).`;
    }
  };

  const cargar = async (id) => {
    tbody.innerHTML = '';
    mensaje.textContent = 'Cargando perdidas...';

    try {
      const response = await fetch(buildUrl(id), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const result = await response.json();

      if (result.status !== 200) {
        perdidasActuales = [];
        tbody.innerHTML = '';
        mensaje.textContent = result.message || 'No se pudieron obtener las perdidas.';
        return;
      }

      perdidasActuales = result.data || [];

      if (!perdidasActuales.length) {
        tbody.innerHTML = '';
        mensaje.textContent = 'No hay perdidas registradas.';
        return;
      }

      renderTabla();
    } catch (err) {
      console.error('Error al cargar perdidas:', err);
      perdidasActuales = [];
      tbody.innerHTML = '';
      mensaje.textContent = 'Ocurrio un error al cargar las perdidas.';
    }
  };

  // Buscar (filtrar por ID de produccion)
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const id = inputIdProduccion.value.trim();
    cargar(id || null);
  });

  // Boton limpiar: borra los inputs (id_produccion y busqueda rapida) y recarga todas
  btnLimpiar.addEventListener('click', () => {
    inputIdProduccion.value = '';
    if (inputBuscarRapido) {
      inputBuscarRapido.value = '';
    }
    searchTerm = '';
    cargar(null);
  });

  // Busqueda en vivo (auto) con debounce: filtra las perdidas cargadas en memoria
  if (inputBuscarRapido) {
    inputBuscarRapido.addEventListener('input', debounce((e) => {
      searchTerm = e.target.value.trim();
      renderTabla();
    }, 200));
  }

  // Cambio de cantidad a mostrar
  selectLimite.addEventListener('change', () => {
    limiteActual = parseInt(selectLimite.value, 10) || 0;
    renderTabla();
  });

  const exportarPDF = () => {
    if (!perdidasActuales.length) {
      alert('No hay perdidas para exportar.');
      return;
    }
    const fechaActual = new Date();
    const fechaStr = fechaActual.toLocaleString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });

    const html = `
      <div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px;">
        <div style="max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
          <div style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:18px 22px; border-radius:8px 8px 0 0;">
            <div style="display:flex; align-items:center; gap:14px;">
              <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Logo" style="width:54px; height:54px; border-radius:8px; object-fit:cover; background:#fff;" crossorigin="anonymous">
              <div style="display:flex; flex-direction:column;">
                <h1 style="margin:0; font-size:20px;">Reporte de Pérdidas de Producción</h1>
                <div style="font-size:12px; opacity:.9;">Tesoro D' MIMI</div>
                <div style="font-size:12px; opacity:.9; margin-top:6px;">Generado: ${fechaStr}</div>
              </div>
            </div>
          </div>

          <div style="padding:18px 24px;">
            <div style="font-size:16px; font-weight:bold; color:#2c3e50; margin:10px 0 12px; border-left:4px solid #E38B29; padding-left:10px;">Listado de Pérdidas</div>

            <table style="width:100%; border-collapse: collapse; font-size:12px; margin-top:6px;">
              <thead>
                <tr>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">#</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">Producto</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:right; border:1px solid #B97222;">Cant. Perdida</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">Motivo</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">Descripción</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">Fecha</th>
                  <th style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:10px 8px; text-align:left; border:1px solid #B97222;">Registrado por</th>
                </tr>
              </thead>
              <tbody>
                ${perdidasActuales.map((p, i) => `
                  <tr>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${i + 1}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${getProductInfo(p)}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px; text-align:right;">${p.CANTIDAD_PERDIDA ?? 0}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${p.MOTIVO_PERDIDA ?? ''}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${p.DESCRIPCION ?? ''}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${p.FECHA_PERDIDA ?? ''}</td>
                    <td style="border:1px solid #dee2e6; padding:9px 8px;">${p.REGISTRADO_POR ?? ''}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>

          <div style="text-align:center; padding:16px 24px; color:#6c757d; font-size:12px; border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
        </div>
      </div>
    `;

    const element = document.createElement('div');
    element.innerHTML = html;

    const opt = {
      margin: [8, 8, 8, 8],
      filename: `perdidas_produccion_${fechaActual.toISOString().split('T')[0]}.pdf`,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => console.log('PDF de perdidas generado')).catch(err => { console.error('Error generando PDF de perdidas', err); alert('Error al generar el PDF. Intente nuevamente.'); });
  };
    btnExportar.addEventListener('click', exportarPDF);

    // Generar PDF individual para una sola pérdida
    const exportarPDFIndividual = (p) => {
      if (!p) {
        alert('Registro inválido para exportar.');
        return;
      }

      const fechaActual = new Date();
      const fechaStr = fechaActual.toLocaleString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
      const idIdent = p.ID_PERDIDA ?? p.ID ?? p.ID_PERDIDA_PROD ?? fechaActual.toISOString().split('T')[0];

      const htmlInd = `
        <div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px;">
          <div style="max-width: 1100px; margin: 0 auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <div style="background: linear-gradient(90deg, #D7A86E, #E38B29); color:#fff; padding:18px 22px; border-radius:8px 8px 0 0;">
              <div style="display:flex; align-items:center; gap:14px;">
                <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="Logo" style="width:54px; height:54px; border-radius:8px; object-fit:cover; background:#fff;" crossorigin="anonymous">
                <div style="display:flex; flex-direction:column;">
                  <h1 style="margin:0; font-size:20px;">Detalle de Pérdida de Producción</h1>
                  <div style="font-size:12px; opacity:.9;">Tesoro D' MIMI</div>
                  <div style="font-size:12px; opacity:.9; margin-top:6px;">Generado: ${fechaStr}</div>
                </div>
              </div>
            </div>

            <div style="padding:18px 24px;">
              <div style="font-size:14px; color:#2c3e50; margin-bottom:8px;">Información</div>
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; font-size:13px;">
                <div><strong>Producto:</strong> ${getProductInfo(p)}</div>
                <div><strong>Cantidad perdida:</strong> ${p.CANTIDAD_PERDIDA ?? 0}</div>
                <div><strong>Motivo:</strong> ${p.MOTIVO_PERDIDA ?? '-'}</div>
                <div><strong>Registrado por:</strong> ${p.REGISTRADO_POR ?? '-'}</div>
                <div style="grid-column: 1 / -1; margin-top:6px;"><strong>Descripción:</strong> ${p.DESCRIPCION ?? '-'}</div>
                <div style="grid-column: 1 / -1; margin-top:6px;"><strong>Fecha pérdida:</strong> ${p.FECHA_PERDIDA ?? '-'}</div>
              </div>
            </div>

            <div style="text-align:center; padding:16px 24px; color:#6c757d; font-size:12px; border-top:1px solid #dee2e6;">Documento generado automáticamente por el Sistema de Gestión Tesoro D' MIMI</div>
          </div>
        </div>
      `;

      const elementInd = document.createElement('div');
      elementInd.innerHTML = htmlInd;
      const optInd = {
        margin: [8, 8, 8, 8],
        filename: `perdida_produccion_${idIdent}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      html2pdf().set(optInd).from(elementInd).save().then(() => console.log('PDF individual generado')).catch(err => { console.error('Error generando PDF individual', err); alert('Error al generar el PDF individual.'); });
    };

    // Delegated listener para botones de exportar individual
    tbody.addEventListener('click', (ev) => {
      const btn = ev.target.closest && ev.target.closest('.btn-export-individual');
      if (!btn) return;
      const idx = btn.dataset.idx ? parseInt(btn.dataset.idx, 10) : NaN;
      const id = btn.dataset.id ?? '';
      // Preferir índice si está presente y válido
      if (!Number.isNaN(idx) && perdidasActuales[idx]) {
        exportarPDFIndividual(perdidasActuales[idx]);
        return;
      }
      // Si no hay idx válido, intentar buscar por id
      if (id) {
        const found = perdidasActuales.find(x => String(x.ID_PERDIDA ?? x.ID ?? '') === String(id));
        if (found) {
          exportarPDFIndividual(found);
          return;
        }
      }
      alert('No se pudo localizar el registro para exportar.');
    });

    // Cargar todas las perdidas al abrir la vista
    cargar(null);
});
</script>
