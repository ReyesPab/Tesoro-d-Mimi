<?php
// src/Views/compras/editar-materia-prima.php

// Obtener ID de la materia prima desde la URL
$id_materia_prima = $_GET['id'] ?? null;

if (!$id_materia_prima || !is_numeric($id_materia_prima)) {
    header('Location: /sistema/public/gestion-materia-prima');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Editar Materia Prima - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 25px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .required-label::after {
            content: " *";
            color: #dc3545;
        }
        
        .valid-feedback, .invalid-feedback {
            display: block;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Editar Materia Prima</h1>
                    <a href="/sistema/public/gestion-materia-prima" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Materia Prima
                    </a>
                </div>
            </div>

            <!-- Información del Producto -->
            <div class="info-box">
                <div class="row">
                    <div class="col-md-6">
                        <strong>ID:</strong> <span id="display_id"><?= $id_materia_prima ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong> <span id="display_estado">Cargando...</span>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-box-seam me-2"></i>Información de la Materia Prima
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="formEditarMateriaPrima" novalidate>
                                <input type="hidden" id="id_materia_prima" name="id_materia_prima" value="<?= $id_materia_prima ?>">
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label required-label">Nombre de la Materia Prima</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               placeholder="Ingrese el nombre de la materia prima"
                                               minlength="3" maxlength="100"
                                               pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}">
                                        <div class="valid-feedback">Nombre válido</div>
                                        <div class="invalid-feedback">
                                            El nombre debe tener entre 3 y 100 caracteres (solo letras, números y espacios)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                                  rows="2" placeholder="Descripción detallada de la materia prima"
                                                  maxlength="255"></textarea>
                                        <div class="form-text">Máximo 255 caracteres</div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="id_unidad_medida" class="form-label required-label">Unidad de Medida</label>
                                        <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                                            <option value="">Seleccione una unidad</option>
                                            <!-- Las unidades se cargarán dinámicamente -->
                                        </select>
                                        <div class="valid-feedback">Unidad válida</div>
                                        <div class="invalid-feedback">Por favor seleccione una unidad de medida</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label required-label">Estado</label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="">Seleccione un estado</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                        <div class="invalid-feedback">Por favor seleccione un estado</div>
                                    </div>
                                </div>
                                
                                <!-- SE ELIMINARON LOS CAMPOS DE STOCK MÍNIMO Y MÁXIMO -->
                                
                                <!-- Información de auditoría -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="bg-light p-3 rounded">
                                            <h6 class="mb-3">Información de Auditoría</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small><strong>Creado por:</strong> <span id="display_creado_por">-</span></small><br>
                                                    <small><strong>Fecha creación:</strong> <span id="display_fecha_creacion">-</span></small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small><strong>Modificado por:</strong> <span id="display_modificado_por">-</span></small><br>
                                                    <small><strong>Última modificación:</strong> <span id="display_fecha_modificacion">-</span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarEdicion()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="btnGuardar">
                                                <i class="bi bi-check-circle"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Documento cargado, iniciando procesos...');
            
            // Cargar solo los datos de la materia prima (que ya incluyen la unidad)
            cargarDatosMateriaPrima();
            
            const form = document.getElementById('formEditarMateriaPrima');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                guardarCambios();
            });
        });

        function cargarDatosMateriaPrima() {
            const idMateriaPrima = document.getElementById('id_materia_prima').value;
            
            fetch(`/sistema/public/index.php?route=compras&caso=obtenerMateriaPrimaPorId&id_materia_prima=${idMateriaPrima}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 200 && data.data) {
                    llenarFormulario(data.data);
                } else {
                    alert('Error al cargar los datos de la materia prima: ' + (data.message || 'Producto no encontrado'));
                    window.location.href = '/sistema/public/gestion-materia-prima';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cargar los datos del producto');
                window.location.href = '/sistema/public/gestion-materia-prima';
            });
        }

        function llenarFormulario(materiaPrima) {
            console.log('📦 Datos de materia prima recibidos:', materiaPrima);
            
            // Llenar campos del formulario
            document.getElementById('nombre').value = materiaPrima.NOMBRE || '';
            document.getElementById('descripcion').value = materiaPrima.DESCRIPCION || '';
            document.getElementById('estado').value = materiaPrima.ESTADO || '';
            
            // Llenar el select de unidad de medida con la opción actual
            const selectUnidad = document.getElementById('id_unidad_medida');
            
            // Limpiar opciones existentes
            while (selectUnidad.children.length > 1) {
                selectUnidad.removeChild(selectUnidad.lastChild);
            }
            
            // Crear opción con la unidad actual
            if (materiaPrima.ID_UNIDAD_MEDIDA && materiaPrima.UNIDAD) {
                const option = document.createElement('option');
                option.value = materiaPrima.ID_UNIDAD_MEDIDA;
                option.textContent = `${materiaPrima.UNIDAD} - ${materiaPrima.DESC_UNIDAD || ''}`;
                option.selected = true;
                selectUnidad.appendChild(option);
                console.log('✅ Unidad cargada:', materiaPrima.UNIDAD);
            } else {
                console.warn('⚠️ No se encontró información de unidad de medida');
            }
            
            // Llenar información de display
            document.getElementById('display_estado').textContent = materiaPrima.ESTADO || 'ACTIVO';
            document.getElementById('display_creado_por').textContent = materiaPrima.CREADO_POR || 'SISTEMA';
            document.getElementById('display_fecha_creacion').textContent = materiaPrima.FECHA_CREACION_FORMATEADA || '-';
            document.getElementById('display_modificado_por').textContent = materiaPrima.MODIFICADO_POR || 'No modificado';
            document.getElementById('display_fecha_modificacion').textContent = materiaPrima.FECHA_MODIFICACION ? 
                new Date(materiaPrima.FECHA_MODIFICACION).toLocaleDateString('es-ES') : 'No modificado';
        }

        function guardarCambios() {
            const btnGuardar = document.getElementById('btnGuardar');
            const originalText = btnGuardar.innerHTML;
            
            // Mostrar loading
            btnGuardar.innerHTML = '<span class="loading-spinner"></span> Guardando...';
            btnGuardar.disabled = true;
            
            // Obtener datos del formulario
            const formData = new FormData(document.getElementById('formEditarMateriaPrima'));
            const datos = Object.fromEntries(formData);
            
            // Convertir a números
            datos.id_unidad_medida = parseInt(datos.id_unidad_medida);
            
            // Limpiar espacios en blanco
            Object.keys(datos).forEach(key => {
                if (typeof datos[key] === 'string') {
                    datos[key] = datos[key].trim();
                }
            });
            
            fetch('/sistema/public/index.php?route=compras&caso=editarMateriaPrima', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    alert(data.message);
                    window.location.href = '/sistema/public/gestion-materia-prima';
                } else {
                    alert(data.message || 'Error al actualizar la materia prima');
                    btnGuardar.innerHTML = originalText;
                    btnGuardar.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. Intente nuevamente.');
                btnGuardar.innerHTML = originalText;
                btnGuardar.disabled = false;
            });
        }

        function cancelarEdicion() {
            if (confirm('¿Está seguro que desea cancelar la edición? Los cambios no guardados se perderán.')) {
                window.location.href = '/sistema/public/gestion-materia-prima';
            }
        }
    </script>
</body>
</html>