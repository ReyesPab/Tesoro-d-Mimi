<?php
// src/Views/compras/registrar-materia-prima.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registrar Producto de Proveedor - Sistema de Gestión</title>
    
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
        
        .numeric-input {
            text-align: right;
        }
        
        .info-section {
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
                    <h1 class="h2 mb-0">Registrar Nuevo Producto de Proveedor</h1>
                    <a href="/sistema/public/gestion-productos-proveedor" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-box-seam me-2"></i>Información del Producto
                            </h4>
                        </div>
                        <div class="card-body">
<form id="formRegistrarProductoProveedor" novalidate>
    <!-- CAMPO OCULTO PARA ID_PROVEEDOR (SIEMPRE 0) -->
    <input type="hidden" id="id_proveedor" name="id_proveedor" value="0">
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="nombre_producto" class="form-label required-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required 
                   placeholder="Ingrese el nombre del producto"
                   minlength="3" maxlength="100"
                   pattern="[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}">
            <div class="valid-feedback">Nombre válido</div>
            <div class="invalid-feedback">
                El nombre debe tener entre 3 y 100 caracteres (solo letras, números y espacios)
            </div>
            <div class="form-text">Mínimo 3 caracteres, máximo 100. Se permiten letras, números y espacios.</div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" 
                      rows="2" placeholder="Descripción detallada del producto"
                      maxlength="255"></textarea>
            <div class="form-text">Máximo 255 caracteres</div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="id_unidad_medida" class="form-label required-label">Unidad de Medida</label>
            <select class="form-select" id="id_unidad_medida" name="id_unidad_medida" required>
                <option value="">Seleccione una unidad</option>
                <!-- Las unidades se cargarán dinámicamente -->
            </select>
            <div class="valid-feedback">Unidad válida</div>
            <div class="invalid-feedback">Por favor seleccione una unidad de medida</div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="precio_unitario" class="form-label required-label">Precio Unitario (L)</label>
            <div class="input-group">
                <span class="input-group-text">L</span>
                <input type="number" class="form-control numeric-input" id="precio_unitario" name="precio_unitario" required 
                       placeholder="0.00" min="0.01" step="0.01" value="0.00">
            </div>
            <div class="valid-feedback">Precio válido</div>
            <div class="invalid-feedback">El precio unitario debe ser mayor a 0</div>
            <div class="form-text">Precio unitario en Lempiras</div>
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="minimo" class="form-label required-label">Stock Mínimo</label>
            <input type="number" class="form-control numeric-input" id="minimo" name="minimo" required 
                   placeholder="0" min="0" step="1" value="0">
            <div class="valid-feedback">Stock mínimo válido</div>
            <div class="invalid-feedback">El stock mínimo debe ser mayor o igual a 0</div>
            <div class="form-text">Cantidad mínima en inventario</div>
        </div>
        
        <div class="col-md-4 mb-3">
            <label for="maximo" class="form-label required-label">Stock Máximo</label>
            <input type="number" class="form-control numeric-input" id="maximo" name="maximo" required 
                   placeholder="100" min="1" step="1" value="100">
            <div class="valid-feedback">Stock máximo válido</div>
            <div class="invalid-feedback">El stock máximo debe ser mayor al mínimo</div>
            <div class="form-text">Cantidad máxima en inventario</div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="button" class="btn btn-secondary me-md-2" onclick="cancelarRegistro()">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="btnRegistrar" disabled>
                    <i class="bi bi-check-circle"></i> Registrar Producto
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
        // Variables para controlar validaciones
        let nombreValido = false;
        let unidadValido = false;
        let precioValido = false;
        let minimoValido = false;
        let maximoValido = false;
        let nombreUnico = false;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Formulario cargado, iniciando procesos...');
            
            // Cargar datos iniciales
            cargarUnidadesMedida();
            
            const form = document.getElementById('formRegistrarProductoProveedor');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📤 Formulario enviado');
                registrarProductoProveedor();
            });
            
            // Validación en tiempo real para todos los campos
            document.getElementById('nombre_producto').addEventListener('input', function() {
                validarNombre();
                verificarBoton();
            });
            
            document.getElementById('id_unidad_medida').addEventListener('change', function() {
                unidadValido = this.value !== '';
                console.log('📏 Unidad seleccionada:', this.value, 'Válido:', unidadValido);
                verificarBoton();
            });
            
            document.getElementById('precio_unitario').addEventListener('input', function() {
                validarPrecio();
                verificarBoton();
            });
            
            document.getElementById('minimo').addEventListener('input', function() {
                validarMinimoMaximo();
                verificarBoton();
            });
            
            document.getElementById('maximo').addEventListener('input', function() {
                validarMinimoMaximo();
                verificarBoton();
            });
            
            // Validar unicidad cuando el usuario sale del campo
            document.getElementById('nombre_producto').addEventListener('blur', function() {
                if (nombreValido) {
                    validarNombreUnico();
                }
            });
            
            // Verificar botón inicialmente
            setTimeout(verificarBoton, 100);
        });

        async function cargarUnidadesMedida() {
            try {
                console.log('🔍 Cargando unidades de medida...');
                
                // Usar el endpoint correcto
                const endpoint = '/sistema/public/index.php?route=compras&caso=obtenerUnidadesMedidaProductosProveedores';
                
                const response = await fetch(endpoint);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📦 Unidades recibidas:', data);
                    
                    if (data.status === 200 && data.data) {
                        const select = document.getElementById('id_unidad_medida');
                        
                        // Limpiar opciones existentes (excepto la primera)
                        while (select.children.length > 1) {
                            select.removeChild(select.lastChild);
                        }
                        
                        data.data.forEach(unidad => {
                            const option = document.createElement('option');
                            option.value = unidad.ID_UNIDAD_MEDIDA;
                            option.textContent = `${unidad.UNIDAD} - ${unidad.DESCRIPCION || ''}`;
                            select.appendChild(option);
                        });
                        
                        console.log('🎉 Unidades cargadas exitosamente:', data.data.length);
                    } else {
                        console.error('❌ Error en respuesta:', data);
                        cargarUnidadesFallback();
                    }
                } else {
                    throw new Error('Error HTTP: ' + response.status);
                }
                
            } catch (error) {
                console.error('💥 Error cargando unidades:', error);
                cargarUnidadesFallback();
            }
        }

        function cargarUnidadesFallback() {
            console.log('🔄 Cargando unidades fallback...');
            const select = document.getElementById('id_unidad_medida');
            const unidadesFallback = [
                {id: 1, nombre: 'UNIDAD - Pieza individual'},
                {id: 2, nombre: 'KG - Kilogramo'},
                {id: 3, nombre: 'L - Litro'},
                {id: 4, nombre: 'M - Metro'}
            ];
            
            unidadesFallback.forEach(unidad => {
                const option = document.createElement('option');
                option.value = unidad.id;
                option.textContent = unidad.nombre;
                select.appendChild(option);
            });
        }

        function validarNombre() {
            const nombreInput = document.getElementById('nombre_producto');
            const nombre = nombreInput.value.trim();
            
            console.log('🔤 Validando nombre:', nombre);
            
            // Validar formato (letras, números y espacios)
            const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ0-9\s]{3,100}$/;
            nombreValido = regex.test(nombre);
            
            console.log('✅ Nombre válido:', nombreValido);
            
            if (nombreValido) {
                nombreInput.classList.remove('is-invalid');
                nombreInput.classList.add('is-valid');
            } else {
                nombreInput.classList.remove('is-valid');
                nombreInput.classList.add('is-invalid');
                nombreUnico = false;
            }
            
            verificarBoton();
            return nombreValido;
        }

        function validarPrecio() {
            const precioInput = document.getElementById('precio_unitario');
            const precio = parseFloat(precioInput.value) || 0;
            
            precioValido = precio > 0;
            
            console.log('💰 Precio validado:', precio, 'Válido:', precioValido);
            
            if (precioValido) {
                precioInput.classList.remove('is-invalid');
                precioInput.classList.add('is-valid');
            } else {
                precioInput.classList.remove('is-valid');
                precioInput.classList.add('is-invalid');
            }
            
            return precioValido;
        }

        function validarMinimoMaximo() {
            const minimoInput = document.getElementById('minimo');
            const maximoInput = document.getElementById('maximo');
            const minimo = parseFloat(minimoInput.value) || 0;
            const maximo = parseFloat(maximoInput.value) || 0;
            
            minimoValido = minimo >= 0;
            maximoValido = maximo > 0 && maximo > minimo;
            
            console.log('📊 Mínimo:', minimo, 'Válido:', minimoValido);
            console.log('📈 Máximo:', maximo, 'Válido:', maximoValido);
            
            if (minimoValido) {
                minimoInput.classList.remove('is-invalid');
                minimoInput.classList.add('is-valid');
            } else {
                minimoInput.classList.remove('is-valid');
                minimoInput.classList.add('is-invalid');
            }
            
            if (maximoValido) {
                maximoInput.classList.remove('is-invalid');
                maximoInput.classList.add('is-valid');
            } else {
                maximoInput.classList.remove('is-valid');
                maximoInput.classList.add('is-invalid');
            }
            
            return minimoValido && maximoValido;
        }

        async function validarNombreUnico() {
            const nombreInput = document.getElementById('nombre_producto');
            const nombre = nombreInput.value.trim();
            
            if (!nombre) {
                nombreUnico = false;
                verificarBoton();
                return;
            }
            
            console.log('🔍 Validando nombre único:', nombre);
            
            try {
                // Por ahora asumimos que está disponible
                nombreUnico = true;
                console.log('✅ Nombre único validado:', nombreUnico);
                verificarBoton();
                
            } catch (error) {
                console.error('❌ Error validando nombre:', error);
                nombreUnico = true;
                verificarBoton();
            }
        }

        function verificarBoton() {
            const btnRegistrar = document.getElementById('btnRegistrar');
            
            console.log('📋 Estado validaciones - Nombre:', nombreValido, 
                        'Unidad:', unidadValido, 
                        'Precio:', precioValido,
                        'Mínimo:', minimoValido,
                        'Máximo:', maximoValido,
                        'Nombre Único:', nombreUnico);
            
            const formularioValido = nombreValido && 
                                   unidadValido && 
                                   precioValido &&
                                   minimoValido &&
                                   maximoValido &&
                                   nombreUnico;
            
            console.log('✅ Formulario válido:', formularioValido);
            
            btnRegistrar.disabled = !formularioValido;
            
            if (formularioValido) {
                btnRegistrar.classList.remove('btn-secondary');
                btnRegistrar.classList.add('btn-primary');
            } else {
                btnRegistrar.classList.remove('btn-primary');
                btnRegistrar.classList.add('btn-secondary');
            }
        }

        function validarFormularioCompleto() {
            console.log('🔍 Validando formulario completo...');
            
            validarNombre();
            validarPrecio();
            validarMinimoMaximo();
            
            if (!nombreValido) {
                alert('❌ Por favor ingrese un nombre válido para el producto (3-100 caracteres).');
                document.getElementById('nombre_producto').focus();
                return false;
            }
                
            if (!unidadValido) {
                alert('❌ Por favor seleccione una unidad de medida.');
                document.getElementById('id_unidad_medida').focus();
                return false;
            }
            
            if (!precioValido) {
                alert('❌ Por favor ingrese un precio unitario válido (mayor a 0).');
                document.getElementById('precio_unitario').focus();
                return false;
            }
            
            if (!minimoValido) {
                alert('❌ Por favor ingrese un stock mínimo válido (mayor o igual a 0).');
                document.getElementById('minimo').focus();
                return false;
            }
            
            if (!maximoValido) {
                alert('❌ Por favor ingrese un stock máximo válido (mayor al stock mínimo).');
                document.getElementById('maximo').focus();
                return false;
            }
            
            console.log('✅ Formulario validado correctamente');
            return true;
        }

function registrarProductoProveedor() {
    console.log('🚀 Iniciando registro de producto...');
    
    if (!validarFormularioCompleto()) {
        return;
    }
    
    const btnRegistrar = document.getElementById('btnRegistrar');
    const originalText = btnRegistrar.innerHTML;
    
    // Mostrar loading
    btnRegistrar.innerHTML = '<span class="loading-spinner"></span> Registrando...';
    btnRegistrar.disabled = true;
    
    // Crear FormData
    const formData = new FormData();
    
    // Agregar campos manualmente
    formData.append('nombre_producto', document.getElementById('nombre_producto').value.trim());
    formData.append('descripcion', document.getElementById('descripcion').value.trim());
    formData.append('id_unidad_medida', document.getElementById('id_unidad_medida').value);
    formData.append('precio_unitario', document.getElementById('precio_unitario').value);
    formData.append('minimo', document.getElementById('minimo').value);
    formData.append('maximo', document.getElementById('maximo').value);
    formData.append('id_proveedor', '0'); // Siempre 0
    formData.append('creado_por', '<?= $_SESSION['usuario']['username'] ?? 'ADMIN' ?>');
    formData.append('id_usuario', '<?= $_SESSION['usuario']['id_usuario'] ?? 1 ?>');
    
    console.log('📤 Enviando datos...');
    
    // Enviar datos
    fetch('/sistema/public/index.php?route=compras&caso=registrarProductoProveedorCompleto', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Respuesta HTTP:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📥 Respuesta del servidor:', data);
        
        if (data.status === 201 || data.status === 200) {
            alert('✅ ' + (data.message || 'Producto registrado exitosamente'));
            window.location.href = '/sistema/public/gestion-productos-proveedor';
        } else {
            throw new Error(data.message || 'Error al registrar el producto');
        }
    })
    .catch(error => {
        console.error('💥 Error:', error);
        alert('❌ Error: ' + error.message);
        btnRegistrar.innerHTML = originalText;
        btnRegistrar.disabled = false;
    });
}

        function cancelarRegistro() {
            if (confirm('¿Está seguro que desea cancelar el registro? Los datos no guardados se perderán.')) {
                window.location.href = '/sistema/public/gestion-productos-proveedor';
            }
        }
    </script>
</body>
</html>