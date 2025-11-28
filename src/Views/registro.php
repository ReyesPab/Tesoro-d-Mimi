<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Registro de Usuario - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: rgba(8, 88, 126, 1);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .card {
            border-radius: 25px;
            border: none;
        }
        
        .card-body {
            padding: 2.5rem;
        }
        
        .form-title {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        .required-label::after {
            content: " *";
            color: #1550bdff;
        }
        
        .btn-primary {
            background-color: #3b71ca;
            border-color: #3b71ca;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #386bc0;
            border-color: #386bc0;
        }
        
        .form-control:focus {
            border-color: #3b71ca;
            box-shadow: 0 0 0 0.2rem rgba(59, 113, 202, 0.25);
        }
        
        .form-outline {
            position: relative;
            width: 100%;
        }
        
        .icon-container {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .icon-container i {
            font-size: 1.25rem;
            color: #3b71ca;
            width: 2.5rem;
        }
        
        .password-strength .progress {
            background-color: rgba(15, 185, 9, 1);
            height: 5px;
        }
        
        .password-strength .progress-bar {
            transition: width 0.3s ease;
        }
        
        .form-text {
            font-size: 0.875rem;
        }
        
        .invalid-feedback {
            display: block;
            color: rgba(0, 0, 0, 1);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .valid-feedback {
            display: block;
            color: #198754;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .illustration-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .illustration-container img {
            max-width: 80%;
            height: auto;
            border-radius: 15px;
        }
        
        @media (max-width: 991.98px) {
            .illustration-container {
                order: -1;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
                <div class="card text-black" style="border-radius: 25px;">
                    <div class="card-body p-md-5">
                        <div class="row justify-content-center">
                            <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                                <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Registro de Usuario</p>

                                <form id="formRegistro" class="needs-validation" novalidate>
                                    <!-- Número de Identidad -->
                                    <div class="icon-container">
                                        <i class="fas fa-id-card fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="numero_identidad" class="form-label">Número de Identidad</label>
                                            <input type="text" class="form-control" id="numero_identidad" name="numero_identidad" 
                                                   maxlength="20" pattern="[0-9]+" 
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            <div class="invalid-feedback">
                                                Solo se permiten números (máximo 20 caracteres)
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Usuario -->
                                    <div class="icon-container">
                                        <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="usuario" class="form-label required-label">Nombre de Usuario</label>
                                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                                   maxlength="15" pattern="[A-Za-z]+" required
                                                   oninput="this.value = this.value.replace(/[^A-Za-z]/g, '').toUpperCase()">
                                            <div class="invalid-feedback">
                                                Solo se permiten letras (máximo 15 caracteres)
                                            </div>
                                            <div class="form-text" id="usuario-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Nombre Completo -->
                                    <div class="icon-container">
                                        <i class="fas fa-user-tag fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="nombre_usuario" class="form-label required-label">Nombre Completo</label>
                                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                                   maxlength="100" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" required
                                                   oninput="this.value = this.value.toUpperCase()">
                                            <div class="invalid-feedback">
                                                Solo se permiten letras y espacios (máximo 100 caracteres)
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Correo Electrónico -->
                                    <div class="icon-container">
                                        <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" 
                                                   maxlength="50">
                                            <div class="invalid-feedback">
                                                Por favor ingrese un correo electrónico válido
                                            </div>
                                            <div class="form-text" id="correo-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Contraseña -->
                                    <div class="icon-container">
                                        <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="contrasena" class="form-label required-label">Contraseña</label>
                                            <input type="password" class="form-control" id="contrasena" name="contrasena" 
                                                   minlength="5" maxlength="10" required
                                                   pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{5,10}$">
                                            <div class="invalid-feedback">
                                                La contraseña debe tener entre 5-10 caracteres e incluir: minúscula, mayúscula, número y carácter especial (@$!%*?&_)
                                            </div>
                                            <div class="form-text">
                                                <small>Requisitos: 5-10 caracteres, 1 minúscula, 1 mayúscula, 1 número, 1 carácter especial (@$!%*?&_)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confirmar Contraseña -->
                                    <div class="icon-container">
                                        <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                                        <div class="form-outline flex-fill mb-0">
                                            <label for="confirmar_contrasena" class="form-label required-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" id="confirmar_contrasena" name="confirmar_contrasena" 
                                                   minlength="5" maxlength="10" required>
                                            <div class="invalid-feedback">
                                                Las contraseñas deben coincidir
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Indicadores de fortaleza de contraseña -->
                                    <div class="mb-4">
                                        <div class="password-strength">
                                            <div class="progress mb-2" style="height: 5px;">
                                                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <small id="password-strength-text" class="text-muted">Fortaleza de la contraseña</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                        <button type="submit" class="btn btn-primary btn-lg" id="btnRegistrar">
                                            <i class="fas fa-user-plus me-2"></i> Registrarse
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <a href="/sistema/public/login" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i> Volver al Login
                                        </a>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2 illustration-container">
                                <!-- Imagen personalizada -->
                                <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" 
                                    class="img-fluid" alt="Tesoro de mimi" 
                                    onerror="this.src='https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/draw1.webp'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="modalExito" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success">¡Registro Exitoso!</h5>
                </div>
                <div class="modal-body">
                    <p id="mensaje-exito"></p>
                </div>
                <div class="modal-footer">
                    <a href="/sistema/public/login" class="btn btn-primary">Ir al Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    class RegistroUsuario {
        constructor() {
            this.init();
        }

        init() {
            this.configurarEventos();
            this.configurarValidaciones();
        }

        configurarEventos() {
            document.getElementById('formRegistro').addEventListener('submit', (e) => this.registrarUsuario(e));
            
            // Validación en tiempo real del usuario
            document.getElementById('usuario').addEventListener('blur', () => this.verificarUsuario());
            
            // Validación en tiempo real del correo
            document.getElementById('correo_electronico').addEventListener('blur', () => this.verificarCorreo());
            
            // Validación de fortaleza de contraseña
            document.getElementById('contrasena').addEventListener('input', () => this.validarFortalezaPassword());
            document.getElementById('confirmar_contrasena').addEventListener('input', () => this.validarConfirmacionPassword());
        }

        configurarValidaciones() {
            // Bootstrap validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }

        async verificarUsuario() {
            const usuario = document.getElementById('usuario').value;
            const feedback = document.getElementById('usuario-feedback');
            
            if (usuario.length < 3) {
                feedback.innerHTML = '<small class="text-warning">El usuario debe tener al menos 3 caracteres</small>';
                return;
            }
            
            if (!/^[A-Za-z]+$/.test(usuario)) {
                feedback.innerHTML = '<small class="text-danger">Solo se permiten letras</small>';
                return;
            }
            
            try {
                const response = await fetch('index.php?route=user&caso=verificar-usuario', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ usuario: usuario })
                });
                
                const result = await response.json();
                
                if (result.status === 200) {
                    feedback.innerHTML = '<small class="text-success">✓ Usuario disponible</small>';
                } else {
                    feedback.innerHTML = '<small class="text-danger">✗ Este usuario ya está registrado</small>';
                }
            } catch (error) {
                console.error('Error verificando usuario:', error);
                feedback.innerHTML = '<small class="text-warning">Error al verificar usuario</small>';
            }
        }

        async verificarCorreo() {
            const correo = document.getElementById('correo_electronico').value;
            const feedback = document.getElementById('correo-feedback');
            
            if (!correo) return;
            
            if (!this.validarEmail(correo)) {
                feedback.innerHTML = '<small class="text-danger">Formato de correo inválido</small>';
                return;
            }
            
            try {
                const response = await fetch('index.php?route=user&caso=verificar-correo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ correo: correo })
                });
                
                const result = await response.json();
                
                if (result.status === 200) {
                    feedback.innerHTML = '<small class="text-success">✓ Correo disponible</small>';
                } else {
                    feedback.innerHTML = '<small class="text-danger">✗ Este correo ya está registrado</small>';
                }
            } catch (error) {
                console.error('Error verificando correo:', error);
                feedback.innerHTML = '<small class="text-warning">Error al verificar correo</small>';
            }
        }

        validarEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        validarFortalezaPassword() {
            const password = document.getElementById('contrasena').value;
            const bar = document.getElementById('password-strength-bar');
            const text = document.getElementById('password-strength-text');
            
            let strength = 0;
            let feedback = '';
            
            // Longitud
            if (password.length >= 5) strength += 25;
            if (password.length >= 8) strength += 25;
            
            // Complejidad
            if (/[a-z]/.test(password)) strength += 15;
            if (/[A-Z]/.test(password)) strength += 15;
            if (/[0-9]/.test(password)) strength += 10;
            if (/[@$!%*?&_]/.test(password)) strength += 10;
            
            // Actualizar barra y texto
            bar.style.width = strength + '%';
            
            if (strength < 30) {
                bar.className = 'progress-bar bg-danger';
                feedback = 'Débil';
            } else if (strength < 70) {
                bar.className = 'progress-bar bg-warning';
                feedback = 'Media';
            } else {
                bar.className = 'progress-bar bg-success';
                feedback = 'Fuerte';
            }
            
            text.textContent = `Fortaleza: ${feedback}`;
        }

        validarConfirmacionPassword() {
            const password = document.getElementById('contrasena').value;
            const confirmacion = document.getElementById('confirmar_contrasena').value;
            const input = document.getElementById('confirmar_contrasena');
            
            if (confirmacion && password !== confirmacion) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }

        async registrarUsuario(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const form = document.getElementById('formRegistro');
            const btnRegistrar = document.getElementById('btnRegistrar');
            
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            // Validar que las contraseñas coincidan
            const password = document.getElementById('contrasena').value;
            const confirmacion = document.getElementById('confirmar_contrasena').value;
            
            if (password !== confirmacion) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            // Preparar datos
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            try {
                btnRegistrar.innerHTML = '<i class="fas fa-hourglass-half me-2"></i> Registrando...';
                btnRegistrar.disabled = true;
                
                const response = await fetch('index.php?route=user&caso=registro', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.status === 201) {
                    this.mostrarExito(result.message);
                } else {
                    throw new Error(result.message);
                }
                
            } catch (error) {
                console.error('Error en registro:', error);
                alert('Error al registrar: ' + error.message);
            } finally {
                btnRegistrar.innerHTML = '<i class="fas fa-user-plus me-2"></i> Registrarse';
                btnRegistrar.disabled = false;
            }
        }

        mostrarExito(mensaje) {
            document.getElementById('mensaje-exito').textContent = mensaje;
            const modal = new bootstrap.Modal(document.getElementById('modalExito'));
            modal.show();
            
            // Limpiar formulario después de éxito
            setTimeout(() => {
                document.getElementById('formRegistro').reset();
                document.getElementById('formRegistro').classList.remove('was-validated');
                document.getElementById('password-strength-bar').style.width = '0%';
                document.getElementById('password-strength-text').textContent = 'Fortaleza de la contraseña';
            }, 1000);
        }
    }

    // Inicializar
    const registro = new RegistroUsuario();
    </script>
</body>
</html>