 // Función para aplicar filtros
 function aplicarFiltros() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const idPaciente = document.getElementById('id_paciente').value;
    const idHistorial = document.getElementById('id_historial').value;
    const table = document.getElementById('recetasTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;

    // Empezamos desde 1 para saltar el encabezado
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const pacienteNombre = row.cells[1].textContent.toLowerCase();
        const medicamento = row.cells[3].textContent.toLowerCase();
        const recetaIdPaciente = row.cells[1].getAttribute('data-id-paciente') || '';
        const recetaIdHistorial = row.cells[2].textContent;
        
        const matchSearch = searchTerm === '' || 
                          pacienteNombre.includes(searchTerm) || 
                          medicamento.includes(searchTerm);
        
        const matchIdPaciente = idPaciente === '' || recetaIdPaciente.includes(idPaciente);
        const matchIdHistorial = idHistorial === '' || recetaIdHistorial.includes(idHistorial);
        
        if (matchSearch && matchIdPaciente && matchIdHistorial) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
}

// Función para mostrar detalles en modal
function mostrarDetalles(receta) {
    const modal = document.getElementById('detallesModal');
    const modalContent = document.getElementById('modalContent');
    
    // Construir el contenido del modal
    let contenido = `
        <div class="info-paciente">
            <h3>Paciente</h3>
            <p><strong>Nombre:</strong> ${receta.Nombre_Paciente} ${receta.Apellido_Paciente}</p>
            <p><strong>ID Paciente:</strong> ${receta.ID_Paciente}</p>
        </div>
        
        <div class="info-historial">
            <h3>Historial Médico</h3>
            <p><strong>ID Historial:</strong> ${receta.ID_Historial}</p>
            <p><strong>Fecha Historial:</strong> ${receta.Fecha_Historial || 'N/A'}</p>
        </div>
        
        <div class="receta-box">
            <h3>Medicamento</h3>
            <p>${receta.Medicamento}</p>
        </div>
        
        <div class="receta-box">
            <h3>Dosis</h3>
            <p>${receta.Dosis}</p>
        </div>
        
        <div class="receta-box">
            <h3>Indicaciones</h3>
            <p>${receta.Indicaciones || 'No se especificaron indicaciones'}</p>
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

// Función para confirmar eliminación de receta
function confirmarEliminarReceta(id) {
if (confirm('¿Está seguro que desea eliminar esta receta médica?')) {
eliminarReceta(id);
}
}

// Función para eliminar receta
function eliminarReceta(id) {
if (!id || isNaN(id) || id <= 0) {
alert('ID de receta inválido');
return;
}

const deleteBtn = document.querySelector(`button[onclick="confirmarEliminarReceta(${id})"]`);
const originalText = deleteBtn.textContent;
deleteBtn.textContent = 'Eliminando...';
deleteBtn.disabled = true;

fetch(`/gestion/public/receta/eliminar?idreceta=${id}`, {
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
    alert(data.message || 'Receta médica eliminada correctamente');
    location.reload();
} else {
    throw new Error(data.message || 'Error al eliminar la receta');
}
})
.catch(error => {
console.error('Error:', error);
alert(error.message || 'Error al eliminar la receta médica');
deleteBtn.textContent = originalText;
deleteBtn.disabled = false;
});
}

// Función para mostrar formulario de edición de receta
function mostrarFormularioEdicionReceta(receta) {
// Llenar el formulario con los datos de la receta
document.getElementById('edit_ID_Receta').value = receta.ID_Receta || '';
document.getElementById('edit_ID_Paciente').value = receta.ID_Paciente || '';
document.getElementById('edit_ID_Historial').value = receta.ID_Historial || '';
document.getElementById('edit_Medicamento').value = receta.Medicamento || '';
document.getElementById('edit_Dosis').value = receta.Dosis || '';
document.getElementById('edit_Indicaciones').value = receta.Indicaciones || '';

// Mostrar la sección de edición
document.getElementById('editSection').style.display = 'block';

// Desplazarse hasta la sección de edición
document.getElementById('editSection').scrollIntoView({ behavior: 'smooth' });
}

// Función para actualizar la receta
function actualizarReceta() {
const form = document.getElementById('editForm');
const formData = new FormData(form);
const recetaData = Object.fromEntries(formData.entries());

// Validación básica
if (!recetaData.ID_Receta || isNaN(recetaData.ID_Receta)) {
alert('ID de receta inválido');
return;
}

const updateBtn = document.querySelector('#editSection .btn-update');
const originalText = updateBtn.textContent;
updateBtn.textContent = 'Actualizando...';
updateBtn.disabled = true;

fetch(`/gestion/public/receta/actualizar`, {
method: 'PUT',
headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
},
body: JSON.stringify(recetaData)
})
.then(async response => {
const data = await response.json();
if (!response.ok) {
    throw new Error(data.message || `Error HTTP: ${response.status}`);
}
return data;
})
.then(data => {
if (data.status === 200) {
    alert(data.message || 'Receta médica actualizada correctamente');
    location.reload();
} else {
    throw new Error(data.message || 'Error al actualizar la receta');
}
})
.catch(error => {
console.error('Error:', error);
alert(error.message || 'Error al actualizar la receta médica');
})
.finally(() => {
updateBtn.textContent = originalText;
updateBtn.disabled = false;
});
}

// Inicializar los filtros desde la URL al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('id_paciente')) {
        document.getElementById('id_paciente').value = params.get('id_paciente');
    }
    if (params.has('id_historial')) {
        document.getElementById('id_historial').value = params.get('id_historial');
    }
    aplicarFiltros();
});