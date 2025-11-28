<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\authModel;
use App\db\connectionDB; // <-- AGREGAR ESTA LÍNEA

use PDO;

class authController {
    
    private $method;
    private $data;
    
     public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
        
    }
    
public function login() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['usuario']) || empty($this->data['password'])) {
        echo json_encode(responseHTTP::status400('Usuario y contraseña son obligatorios'));
        return;
    }
    
    $erroresUsuario = Security::validarUsuario($this->data['usuario']);
    if (!empty($erroresUsuario)) {
        echo json_encode(responseHTTP::status400(implode(', ', $erroresUsuario)));
        return;
    }
    
    $password = $this->data['password'];
    if (empty($password) || preg_match('/\s/', $password)) {
        echo json_encode(responseHTTP::status400('La contraseña no puede estar vacía o contener espacios'));
        return;
    }
    
    $result = authModel::verificarUsuario($this->data['usuario'], $password);
    
    if ($result['success'] && $result['user']) {
        $fotoPerfil = authModel::obtenerFotoPerfil($result['user']['ID_USUARIO']);
        // INICIAR SESIÓN CORRECTAMENTE
        session_start();
        $_SESSION['logged_in'] = true; // <- ESTA LÍNEA FALTA
        $_SESSION['user_id'] = $result['user']['ID_USUARIO'];
        
        $_SESSION['id_usuario'] = $result['user']['ID_USUARIO'];
        $_SESSION['user_name'] = $result['user']['NOMBRE_USUARIO'];
        $_SESSION['usuario_nombre'] = $result['user']['NOMBRE_USUARIO'];
        $_SESSION['user_usuario'] = $result['user']['USUARIO'];
        $_SESSION['primer_ingreso'] = $result['user']['PRIMER_INGRESO'];
        $_SESSION['estado_usuario'] = $result['user']['ESTADO_USUARIO'];
        $_SESSION['id_rol'] = $result['user']['ID_ROL']; // ← LÍNEA AGREGADA
        $_SESSION['rol'] = $result['user']['ROL']; // ← LÍNEA AGREGADA (opcional)

        $_SESSION['foto_perfil'] = $fotoPerfil;
        
        $response = [
            'status' => 'success',
            'message' => 'Login exitoso',
            'data' => [
                'user' => $result['user'],
                'primer_ingreso' => $result['user']['PRIMER_INGRESO'],
                'id_usuario' => $result['user']['ID_USUARIO'],
                'id_rol' => $result['user']['ID_ROL'], // ← LÍNEA AGREGADA
                'requiere_2fa' => $result['requiere_2fa'],
                'session_data' => [
                    'user_id' => $result['user']['ID_USUARIO'],
                    'user_name' => $result['user']['NOMBRE_USUARIO'],
                    'primer_ingreso' => $result['user']['PRIMER_INGRESO'],
                    'estado_usuario' => $result['user']['ESTADO_USUARIO'],
                    'id_rol' => $result['user']['ID_ROL'], // ← LÍNEA AGREGADA
                    'rol' => $result['user']['ROL'], // ← LÍNEA AGREGADA (opcional)
                    'foto_perfil' => $fotoPerfil
                ]
            ]
        ];
        
        echo json_encode(responseHTTP::status200($response['message'], $response));
        
    } else {
        echo json_encode(responseHTTP::status401($result['message']));
    }
}

// Método para actualizar estado después de cambiar contraseña
public static function actualizarEstadoUsuario($idUsuario) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "UPDATE TBL_MS_USUARIOS 
                SET ESTADO_USUARIO = 'ACTIVO', 
                    PRIMER_INGRESO = 1,
                    FECHA_MODIFICACION = NOW(),
                    MODIFICADO_POR = 'SISTEMA'
                WHERE ID_USUARIO = :id_usuario";
        
        $query = $con->prepare($sql);
        $result = $query->execute(['id_usuario' => $idUsuario]);
        
        error_log(" Estado actualizado para usuario $idUsuario: ACTIVO, PRIMER_INGRESO=1");
        
        return $result;
        
    } catch (\PDOException $e) {
        error_log("Error en actualizarEstadoUsuario: " . $e->getMessage());
        return false;
    }
}
    
    public function recuperarPassword() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar usuario
        if (empty($this->data['usuario'])) {
            echo json_encode(responseHTTP::status400('El usuario es obligatorio'));
            return;
        }
        
        $usuario = strtoupper(trim($this->data['usuario']));
        
        // Verificar si el usuario existe usando procedimiento almacenado
        $usuarioExiste = authModel::verificarUsuarioExiste($usuario);
        
        if (!$usuarioExiste) {
            echo json_encode(responseHTTP::status404('Usuario no encontrado'));
            return;
        }
        
        $passwordTemporal = Security::generarPasswordRobusta(8);
        
        // Obtener ID del usuario para cambiar la contraseña
        $userData = authModel::obtenerDatosUsuario($usuario);
        
        if ($userData) {
            // Cambiar contraseña a la temporal (sin contraseña actual para reset)
            $result = authModel::cambiarPassword($userData['ID_USUARIO'], $passwordTemporal, null, 'SISTEMA_RECUPERACION');
            
            if ($result['success']) {
                echo json_encode(responseHTTP::status200('Se ha enviado una contraseña temporal a su correo electrónico'));
            } else {
                echo json_encode(responseHTTP::status500('Error al generar contraseña temporal'));
            }
        } else {
            echo json_encode(responseHTTP::status404('Usuario no encontrado'));
        }
    }
    
    public function verificarPreguntas() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar datos
        if (empty($this->data['id_usuario']) || empty($this->data['respuestas'])) {
            echo json_encode(responseHTTP::status400('Datos incompletos'));
            return;
        }
        
        $idUsuario = $this->data['id_usuario'];
        $respuestas = $this->data['respuestas'];
        
        // Verificar respuestas usando procedimiento almacenado
        $result = authModel::verificarRespuestas($idUsuario, $respuestas);
        
        if ($result) {
            echo json_encode(responseHTTP::status200('Respuestas verificadas correctamente'));
        } else {
            echo json_encode(responseHTTP::status401('Respuestas incorrectas - Usuario bloqueado'));
        }
    }
    
    public function cambiarPassword() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos
    if (empty($this->data['id_usuario']) || empty($this->data['nueva_password'])) {
        echo json_encode(responseHTTP::status400('Datos incompletos'));
        return;
    }
    
    $idUsuario = $this->data['id_usuario'];
    $nuevaPassword = $this->data['nueva_password'];
    $passwordActual = $this->data['password_actual'] ?? null;
    
    // Validar contraseña
    $errores = Security::validarPassword($nuevaPassword);
    if (!empty($errores)) {
        echo json_encode(responseHTTP::status400(implode(', ', $errores)));
        return;
    }
    
    // Cambiar contraseña usando procedimiento almacenado
    $result = authModel::cambiarPassword($idUsuario, $nuevaPassword, $passwordActual);
    
    if ($result['success']) {
        // NUEVO: Si el usuario era "Nuevo", actualizar su estado a "ACTIVO"
        $userData = authModel::obtenerDatosUsuarioCompletosPorId($idUsuario);
        
        if ($userData && $userData['ESTADO_USUARIO'] == 'Nuevo' && $userData['PRIMER_INGRESO'] == 0) {
            $actualizado = authModel::actualizarEstadoUsuario($idUsuario);
            
            if ($actualizado) {
                error_log(" ✅ Estado actualizado para usuario $idUsuario: ACTIVO, PRIMER_INGRESO=1");
                
                // Registrar en bitácora
                authModel::registrarBitacora(
                    $idUsuario, 
                    'PRIMER_INGRESO_COMPLETADO', 
                    'Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO'
                );
                
                // Modificar el mensaje para indicar el cambio de estado
                $result['message'] .= '. Estado actualizado a ACTIVO.';
            }
        }
        
        echo json_encode(responseHTTP::status200($result['message']));
    } else {
        echo json_encode(responseHTTP::status400($result['message']));
    }
}

   // MÉTODO PARA CAMBIAR CONTRASEÑA DESDE DASHBOARD
public function cambiarPasswordDashboard() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos
    if (empty($this->data['id_usuario']) || empty($this->data['nueva_password']) || empty($this->data['password_actual'])) {
        echo json_encode(responseHTTP::status400('Todos los campos son obligatorios'));
        return;
    }
    
    $idUsuario = $this->data['id_usuario'];
    $nuevaPassword = $this->data['nueva_password'];
    $passwordActual = $this->data['password_actual'];
    
    // NUEVA VALIDACIÓN: Longitud entre 5 y 10 caracteres
    if (strlen($nuevaPassword) < 5 || strlen($nuevaPassword) > 10) {
        echo json_encode(responseHTTP::status400('La contraseña debe tener entre 5 y 10 caracteres'));
        return;
    }
    
    // Validar contraseña (las demás reglas existentes)
    $errores = Security::validarPassword($nuevaPassword);
    if (!empty($errores)) {
        echo json_encode(responseHTTP::status400(implode(', ', $errores)));
        return;
    }
    
    // El resto de tu código permanece igual...
    try {
        // Cambiar contraseña usando el método específico para dashboard
        $result = authModel::cambiarPasswordDashboard($idUsuario, $nuevaPassword, $passwordActual);
        
        if ($result['success']) {
            // NUEVO: Si el usuario era "Nuevo", actualizar su estado a "ACTIVO"
            $userData = authModel::obtenerDatosUsuarioCompletosPorId($idUsuario);
            
            if ($userData) {
                error_log(" 📊 Datos del usuario obtenidos: Estado={$userData['ESTADO_USUARIO']}, PrimerIngreso={$userData['PRIMER_INGRESO']}");
                
                if ($userData['ESTADO_USUARIO'] == 'Nuevo' && $userData['PRIMER_INGRESO'] == 0) {
                    $actualizado = authModel::actualizarEstadoUsuario($idUsuario);
                    
                    if ($actualizado) {
                        error_log(" ✅ Estado actualizado para usuario $idUsuario: ACTIVO, PRIMER_INGRESO=1");
                        
                        // Registrar en bitácora
                        authModel::registrarBitacora(
                            $idUsuario, 
                            'PRIMER_INGRESO_COMPLETADO', 
                            'Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO'
                        );
                        
                        // Modificar el mensaje para indicar el cambio de estado
                        $result['message'] .= '. Estado actualizado a ACTIVO.';
                    } else {
                        error_log(" ❌ No se pudo actualizar el estado del usuario");
                    }
                } else {
                    error_log(" ℹ️ Usuario ya está ACTIVO o no requiere cambio de estado");
                }
            } else {
                error_log(" ❌ No se pudieron obtener los datos del usuario");
            }
            
            echo json_encode(responseHTTP::status200($result['message']));
        } else {
            echo json_encode(responseHTTP::status400($result['message']));
        }
        
    } catch (\Exception $e) {
        error_log(" 💥 Error en cambiarPasswordDashboard: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error interno del servidor: ' . $e->getMessage()));
    }
}
  
    // MÉTODO PARA RECUPERACIÓN DE CONTRASEÑA CON SELECCIÓN DE MÉTODO
    public function recuperarPasswordAvanzado() {
        if ($this->method != 'post') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        // Validar datos básicos
        if (empty($this->data['usuario'])) {
            echo json_encode(responseHTTP::status400('El usuario es obligatorio'));
            return;
        }
        
        if (empty($this->data['metodo'])) {
            echo json_encode(responseHTTP::status400('El método de recuperación es obligatorio'));
            return;
        }
        
        $usuario = strtoupper(trim($this->data['usuario']));
        $metodo = $this->data['metodo'];
        
        error_log(" SOLICITUD RECUPERACIÓN - Usuario: $usuario, Método: $metodo");
        
        // Verificar si el usuario existe
        $usuarioExiste = authModel::verificarUsuarioRecuperacion($usuario);
        
        error_log("RESULTADO verificarUsuarioRecuperacion: " . print_r($usuarioExiste, true));
        
        if (!$usuarioExiste['success']) {
            echo json_encode(responseHTTP::status404($usuarioExiste['message']));
            return;
        }
        
        $userData = $usuarioExiste['user'];
        
        // Procesar según el método seleccionado
        switch ($metodo) {
            case 'correo':
                $this->recuperarPorCorreo($usuario, $userData);
                break;
                
            default:
                echo json_encode(responseHTTP::status400('Método de recuperación no válido'));
                break;
        }
    }

    // RECUPERACIÓN POR CORREO ELECTRÓNICO - MÉTODO CORREGIDO
    private function recuperarPorCorreo($usuario, $userData) {
    try {
        // Verificar que tenga correo electrónico
        if (empty($userData['CORREO_ELECTRONICO'])) {
            echo json_encode(responseHTTP::status400('El usuario no tiene correo electrónico registrado'));
            return;
        }
        
        // Generar contraseña temporal
        $contraseñaTemporal = authModel::generarContraseñaTemporal();
        
        error_log("🔐 CONTRASEÑA TEMPORAL PARA $usuario: $contraseñaTemporal");
        
        // Solicitar recuperación por correo
        $result = authModel::solicitarRecuperacionCorreo($usuario, $contraseñaTemporal);
        
        if ($result['success']) {
            // 🔥 USAR EmailService para enviar el correo
            $correoEnviado = \App\config\EmailService::enviarCorreoRecuperacion(
                $result['correo'],
                $result['nombre_usuario'], 
                $usuario,
                $result['password_temporal'],
                $result['fecha_expiracion']
            );
            
            if ($correoEnviado) {
                // 🔥 NUEVO: Registrar en bitácora con ID_OBJETO = 5
                $this->registrarBitacoraConObjeto(
                    $userData['ID_USUARIO'], 
                    'RECUPERACION_CORREO_ENVIADA', 
                    'Contraseña temporal enviada a: ' . $result['correo'],
                    5
                );
                
                $responseData = [
                    'usuario' => $usuario,
                    'password_temporal' => $result['password_temporal'],
                    'fecha_expiracion' => $result['fecha_expiracion'],
                    'correo' => $result['correo'],
                    'nombre_usuario' => $result['nombre_usuario'],
                    'correo_enviado' => true
                ];
                
                // 🔥 NUEVO: Registrar en bitácora con ID_OBJETO = 5
                $this->registrarBitacoraConObjeto(
                    $userData['ID_USUARIO'], 
                    'RECUPERACION_CORREO_ENVIADA', 
                    'Contraseña temporal enviada por correo a: ' . $userData['CORREO_ELECTRONICO'],
                    5
                );
        
                $mensaje = 'Se ha enviado una contraseña temporal a su correo electrónico: ' . $result['correo'];
                
                echo json_encode(responseHTTP::status200($mensaje, $responseData));
            } else {
                // Si falla el envío del correo, mostrar la contraseña en la respuesta
                $responseData = [
                    'usuario' => $usuario,
                    'password_temporal' => $result['password_temporal'],
                    'fecha_expiracion' => $result['fecha_expiracion'],
                    'correo' => $result['correo'],
                    'nombre_usuario' => $result['nombre_usuario'],
                    'correo_enviado' => false,
                    'nota' => 'El correo no pudo ser enviado. Use la contraseña temporal mostrada.'
                ];
                
                $mensaje = 'Error al enviar correo. Contraseña temporal: ' . $result['password_temporal'];
                
                echo json_encode(responseHTTP::status200($mensaje, $responseData));
            }
            
        } else {
            error_log(" ERROR EN recuperarPorCorreo: " . $result['message']);
            echo json_encode(responseHTTP::status500($result['message']));
        }
        
    } catch (\Exception $e) {
        error_log(" EXCEPCIÓN EN recuperarPorCorreo: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error interno del servidor: ' . $e->getMessage()));
    }
}

//  NUEVO MÉTODO: Registrar en bitácora con ID_OBJETO específico
private function registrarBitacoraConObjeto($idUsuario, $accion, $descripcion, $idObjeto) {
    try {
        $con = \App\db\connectionDB::getConnection();
        
        $sql = "INSERT INTO TBL_MS_BITACORA (FECHA, ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR) 
                VALUES (NOW(), :id_usuario, :id_objeto, :accion, :descripcion, :creado_por)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'id_usuario' => $idUsuario,
            'id_objeto' => $idObjeto,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'creado_por' => 'SISTEMA'
        ]);
        
        error_log(" REGISTRADO EN BITÁCORA - Usuario: $idUsuario, Objeto: $idObjeto, Acción: $accion");
        
    } catch (\PDOException $e) {
        error_log(" ERROR en registrarBitacoraConObjeto: " . $e->getMessage());
    }
}



// Agrega estos métodos a tu clase authController existente

public function iniciar2FA() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['usuario']) || empty($this->data['password'])) {
        echo json_encode(responseHTTP::status400('Usuario y contraseña son obligatorios'));
        return;
    }
    
    // Primera verificación de credenciales
    $result = authModel::verificarUsuario($this->data['usuario'], $this->data['password']);
    
    if ($result['success'] && $result['user']) {
        // Generar código 2FA
        $codigo2FA = \App\config\TwoFactorAuth::generarCodigo();
        
        // Guardar código en sesión (no en base de datos como solicitaste)
        session_start();
        $_SESSION['2fa_usuario'] = $this->data['usuario'];
        $_SESSION['2fa_codigo'] = $codigo2FA;
        $_SESSION['2fa_timestamp'] = time();
        $_SESSION['2fa_user_data'] = $result['user'];
        
        // Enviar código por correo
        $correoEnviado = \App\config\TwoFactorAuth::enviarCodigoCorreo(
            $result['user']['CORREO_ELECTRONICO'],
            $result['user']['NOMBRE_USUARIO'],
            $codigo2FA
        );
        
        if ($correoEnviado) {
            // Registrar en bitácora
            authModel::registrarBitacora(
                $result['user']['ID_USUARIO'], 
                '2FA_INICIADO', 
                'Código 2FA enviado al correo'
            );
            
            $response = [
                'status' => '2fa_required',
                'message' => 'Se ha enviado un código de verificación a tu correo electrónico',
                'data' => [
                    'usuario' => $this->data['usuario'],
                    'correo' => substr($result['user']['CORREO_ELECTRONICO'], 0, 3) . '***' . substr($result['user']['CORREO_ELECTRONICO'], strpos($result['user']['CORREO_ELECTRONICO'], '@'))
                ]
            ];
            
            echo json_encode(responseHTTP::status200($response['message'], $response));
        } else {
            echo json_encode(responseHTTP::status500('Error al enviar el código de verificación'));
        }
        
    } else {
        echo json_encode(responseHTTP::status401($result['message']));
    }
}

public function verificar2FA() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['codigo'])) {
        echo json_encode(responseHTTP::status400('El código de verificación es obligatorio'));
        return;
    }
    
    session_start();
    
    // Verificar que exista una sesión 2FA activa
    if (!isset($_SESSION['2fa_usuario']) || !isset($_SESSION['2fa_codigo']) || !isset($_SESSION['2fa_timestamp'])) {
        echo json_encode(responseHTTP::status400('Sesión de verificación no encontrada'));
        return;
    }
    
    // Verificar expiración
    if (!\App\config\TwoFactorAuth::validarExpiracion($_SESSION['2fa_timestamp'])) {
        // Limpiar sesión
        unset($_SESSION['2fa_usuario'], $_SESSION['2fa_codigo'], $_SESSION['2fa_timestamp'], $_SESSION['2fa_user_data']);
        echo json_encode(responseHTTP::status400('El código ha expirado. Por favor, inicie sesión nuevamente.'));
        return;
    }
    
    // Verificar código
    if ($this->data['codigo'] !== $_SESSION['2fa_codigo']) {
        echo json_encode(responseHTTP::status401('Código de verificación incorrecto'));
        return;
    }
    
    // Código correcto - Login exitoso
    $userData = $_SESSION['2fa_user_data'];
    
    // Registrar en bitácora
    authModel::registrarBitacora(
        $userData['ID_USUARIO'], 
        '2FA_VERIFICADO', 
        'Autenticación en dos pasos completada exitosamente'
    );
    
    // Preparar respuesta de login exitoso
    $response = [
        'status' => 'success',
        'message' => 'Autenticación en dos pasos completada exitosamente',
        'data' => [
            'user' => $userData,
            'primer_ingreso' => $userData['PRIMER_INGRESO'] == 1,
            'id_usuario' => $userData['ID_USUARIO']
        ]
    ];
    
    // Limpiar datos 2FA de la sesión
    unset($_SESSION['2fa_usuario'], $_SESSION['2fa_codigo'], $_SESSION['2fa_timestamp'], $_SESSION['2fa_user_data']);
    
    // Mantener sesión de usuario
    $_SESSION['user_id'] = $userData['ID_USUARIO'];
    $_SESSION['user_name'] = $userData['NOMBRE_USUARIO'];
    $_SESSION['user_usuario'] = $userData['USUARIO'];
    
    echo json_encode(responseHTTP::status200($response['message'], $response));
}

public function reenviarCodigo2FA() {
    if ($this->method != 'post') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    session_start();
    
    if (!isset($_SESSION['2fa_usuario']) || !isset($_SESSION['2fa_user_data'])) {
        echo json_encode(responseHTTP::status400('No hay sesión de verificación activa'));
        return;
    }
    
    // Generar nuevo código
    $nuevoCodigo = \App\config\TwoFactorAuth::generarCodigo();
    
    // Actualizar sesión
    $_SESSION['2fa_codigo'] = $nuevoCodigo;
    $_SESSION['2fa_timestamp'] = time();
    
    $userData = $_SESSION['2fa_user_data'];
    
    // Enviar nuevo código
    $correoEnviado = \App\config\TwoFactorAuth::enviarCodigoCorreo(
        $userData['CORREO_ELECTRONICO'],
        $userData['NOMBRE_USUARIO'],
        $nuevoCodigo
    );
    
    if ($correoEnviado) {
        // Registrar en bitácora
        authModel::registrarBitacora(
            $userData['ID_USUARIO'], 
            '2FA_REENVIADO', 
            'Código 2FA reenviado al correo'
        );
        
        echo json_encode(responseHTTP::status200('Se ha enviado un nuevo código de verificación a tu correo'));
    } else {
        echo json_encode(responseHTTP::status500('Error al reenviar el código de verificación'));
    }
}






}
