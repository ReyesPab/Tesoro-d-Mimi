// Función para aplicar filtros
function aplicarFiltros() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const idPaciente = document.getElementById('id_paciente').value;
    const idMedico = document.getElementById('id_medico').value;
    const fechaDesde = document.getElementById('fecha_desde').value;
    const fechaHasta = document.getElementById('fecha_hasta').value;
    const table = document.getElementById('informesTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;

    // Empezamos desde 1 para saltar el encabezado
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const pacienteNombre = row.cells[1].textContent.toLowerCase();
        const medicoNombre = row.cells[2].textContent.toLowerCase();
        const fechaInforme = row.cells[4].textContent;
        
        const matchSearch = searchTerm === '' || 
                          pacienteNombre.includes(searchTerm) || 
                          medicoNombre.includes(searchTerm);
        
        const matchIdPaciente = idPaciente === '' || row.cells[1].getAttribute('data-id-paciente')?.includes(idPaciente);
        const matchIdMedico = idMedico === '' || row.cells[2].getAttribute('data-id-medico')?.includes(idMedico);
        const matchFechaDesde = fechaDesde === '' || fechaInforme >= fechaDesde;
        const matchFechaHasta = fechaHasta === '' || fechaInforme <= fechaHasta;
        
        if (matchSearch && matchIdPaciente && matchIdMedico && matchFechaDesde && matchFechaHasta) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
}

// Función para mostrar detalles en modal
function mostrarDetalles(informe) {
    const modal = document.getElementById('detallesModal');
    const modalContent = document.getElementById('modalContent');
    
    // Construir el contenido del modal
    let contenido = `
        <div class="info-paciente">
            <h3>Paciente</h3>
            <p><strong>Nombre:</strong> ${informe.Paciente}</p>
            <p><strong>ID Paciente:</strong> ${informe.ID_Paciente}</p>
        </div>
        
        <div class="info-medico">
            <h3>Médico</h3>
            <p><strong>Nombre:</strong> ${informe.Medico}</p>
            <p><strong>Especialidad:</strong> ${informe.Especialidad}</p>
        </div>
        
        <div class="informe-box">
            <h3>Fecha de Emisión</h3>
            <p>${informe.Fecha_Emision}</p>
        </div>
        
        <div class="informe-box">
            <h3>Detalles</h3>
            <p>${informe.Detalles || 'No se especificaron detalles'}</p>
        </div>
    `;
    
    modalContent.innerHTML = contenido;
    modal.style.display = 'block';
    
    // Cerrar modal al hacer clic en la X
    document.querySelector('.close').onclick = function() {
        modal.style.display = 'none';
    }
    
    // Cerrar modal al hacer clic fuera del contenido
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}

// Función para editar informe
function editarInforme(id) {
    window.location.href = `editar_informe.php?id=${id}`;
}

// Función para confirmar eliminación de informe
function confirmarEliminar(id) {
if (confirm('¿Está seguro que desea eliminar este informe médico?')) {
eliminarInforme(id);
}
}

// Función para eliminar informe
function eliminarInforme(id) {
if (!id || isNaN(id) || id <= 0) {
alert('ID de informe inválido');
return;
}

const deleteBtn = document.querySelector(`button[onclick="confirmarEliminar(${id})"]`);
const originalText = deleteBtn.textContent;
deleteBtn.textContent = 'Eliminando...';
deleteBtn.disabled = true;

fetch(`/gestion/public/informemedico/eliminar?idinforme=${id}`, {
method: 'DELETE',
headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
}
})
.then(async response => {
if (!response.ok) {
    const error = await response.json().catch(() => null);
    throw new Error(error?.message || 'Error en la solicitud');
}
return response.json();
})
.then(data => {
if (data.status === 200) {
    alert(data.message || 'Informe médico eliminado correctamente');
    location.reload();
} else {
    throw new Error(data.message || 'Error al eliminar el informe');
}
})
.catch(error => {
console.error('Error:', error);
alert(error.message || 'Error al eliminar el informe médico');
deleteBtn.textContent = originalText;
deleteBtn.disabled = false;
});
}
// Inicializar los filtros desde la URL al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('id_paciente')) {
        document.getElementById('id_paciente').value = params.get('id_paciente');
    }
    if (params.has('id_medico')) {
        document.getElementById('id_medico').value = params.get('id_medico');
    }
    if (params.has('fecha_desde')) {
        document.getElementById('fecha_desde').value = params.get('fecha_desde');
    }
    if (params.has('fecha_hasta')) {
        document.getElementById('fecha_hasta').value = params.get('fecha_hasta');
    }
    aplicarFiltros();
});

// Función para mostrar formulario de edición
function mostrarFormularioEdicion(informe) {
// Llenar el formulario con los datos del informe
document.getElementById('edit_ID_Informe').value = informe.ID_Informe || '';
document.getElementById('edit_ID_Paciente').value = informe.ID_Paciente || '';
document.getElementById('edit_ID_Medico').value = informe.ID_Medico || '';
document.getElementById('edit_Detalles').value = informe.Detalles || '';

// Mostrar la sección de edición
document.getElementById('editSection').style.display = 'block';

// Desplazarse hasta la sección de edición
document.getElementById('editSection').scrollIntoView({ behavior: 'smooth' });
}

// Función para ocultar el formulario de edición
function ocultarFormularioEdicion() {
document.getElementById('editSection').style.display = 'none';
}

// Función para actualizar el informe médico
function actualizarInforme() {
const form = document.getElementById('editForm');
const formData = new FormData(form);
const informeData = Object.fromEntries(formData.entries());

// Validación básica
if (!informeData.ID_Informe || isNaN(informeData.ID_Informe)) {
alert('ID de informe inválido');
return;
}

const updateBtn = document.querySelector('#editSection .btn-update');
const originalText = updateBtn.textContent;
updateBtn.textContent = 'Actualizando...';
updateBtn.disabled = true;

fetch(`/gestion/public/informemedico/actualizar`, {
method: 'PUT',
headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
},
body: JSON.stringify(informeData)
})
.then(async response => {
const data = await response.json();
if (!response.ok) {
    throw new Error(data.message || `Error HTTP: ${response.status}`);
}
return data;
})
.then(data => {
console.log('Respuesta del servidor:', data);
alert(data.message || 'Informe médico actualizado correctamente');
location.reload();
})
.catch(error => {
console.error('Error:', error);
alert(error.message || 'Error al actualizar el informe médico');
})
.finally(() => {
updateBtn.textContent = originalText;
updateBtn.disabled = false;
});
}