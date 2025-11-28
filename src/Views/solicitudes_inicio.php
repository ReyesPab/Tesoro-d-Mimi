<?php
// Fragmento para mostrar las solicitudes de acceso del usuario en la página de inicio
// Incluir este archivo en `src/Views/inicio.php` donde desees el apartado.
?>
<div id="solicitudes-acceso" style="margin-top:24px;">
    <div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,.05)">
        <h4>Solicitudes de acceso</h4>
        <p id="solicitudes-mensaje">Aquí verás el estado de tus solicitudes de acceso a las áreas.</p>

        <div id="solicitudes-contenido">
            <p>No hay solicitudes recientes.</p>
        </div>

        <div style="margin-top:12px;">
            <button id="btn-refrescar-solicitudes" style="padding:8px 12px;border-radius:6px;border:0;background:#0d6efd;color:#fff;cursor:pointer">Refrescar</button>
        </div>
    </div>
</div>

<script>
    async function cargarSolicitud() {
        try {
            const resp = await fetch('/sistema/public/solicitud_status.php');
            if (!resp.ok) throw new Error('No autenticado o error');
            const j = await resp.json();
            const cont = document.getElementById('solicitudes-contenido');
            const msg = document.getElementById('solicitudes-mensaje');

            if (j.status === 200) {
                msg.innerText = 'Última solicitud:';
                cont.innerHTML = `<div style="padding:10px;border:1px solid #eee;border-radius:6px">
                    <strong>ID:</strong> ${j.id_solicitud} <br>
                    <strong>Área:</strong> ${j.area} <br>
                    <strong>Estado:</strong> ${j.estado} <br>
                </div>`;
            } else if (j.status === 404) {
                msg.innerText = 'No tienes solicitudes pendientes.';
                cont.innerHTML = '<p>No hay solicitudes recientes.</p>';
            } else {
                msg.innerText = 'Error al obtener solicitudes.';
                cont.innerHTML = '<p>Error. Intenta refrescar.</p>';
            }
        } catch (e) {
            console.error('Error cargando solicitud:', e);
            const cont = document.getElementById('solicitudes-contenido');
            document.getElementById('solicitudes-mensaje').innerText = 'Error al cargar solicitudes.';
            cont.innerHTML = '<p>Inicia sesión o verifica la conexión.</p>';
        }
    }

    document.getElementById('btn-refrescar-solicitudes').addEventListener('click', cargarSolicitud);

    // Auto-refrescar cada 8 segundos
    setInterval(cargarSolicitud, 8000);
    // Carga inicial
    cargarSolicitud();
</script>
