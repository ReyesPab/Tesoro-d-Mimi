<?php

namespace App\models\modulo_ventas;

use App\db\connectionDB;

class clienteModel
{
    /**
     * Obtener cliente por DNI
     * @param string $dni - DNI del cliente
     * @return array|null - Datos del cliente o null
     */
    public static function obtenerPorDNI($dni)
    {
        try {
            // Normalizar búsqueda: obtener sólo dígitos de la entrada y comparar
            $dni_digits = preg_replace('/\D/', '', $dni);
            $con = connectionDB::getConnection();
            // En la BD puede haber formatos (espacios/guiones/puntos). Usamos REPLACE para igualar.
            $sql = "SELECT * FROM tbl_cliente WHERE REPLACE(REPLACE(REPLACE(DNI, ' ', ''), '-', ''), '.', '') = :dni AND ESTADO = 'ACTIVO' LIMIT 1";
            $query = $con->prepare($sql);
            $query->execute([':dni' => $dni_digits]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::obtenerPorDNI: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener cliente por ID
     * @param int $id_cliente - ID del cliente
     * @return array|null
     */
    public static function obtenerPorID($id_cliente)
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT * FROM tbl_cliente WHERE ID_CLIENTE = :id_cliente AND ESTADO = 'ACTIVO'";
            $query = $con->prepare($sql);
            $query->execute([':id_cliente' => $id_cliente]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::obtenerPorID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar todos los clientes activos
     * @return array
     */
    public static function listar()
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT * FROM tbl_cliente WHERE ESTADO = 'ACTIVO' ORDER BY NOMBRE ASC";
            $query = $con->prepare($sql);
            $query->execute();
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::listar: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar todos los clientes (activos e inactivos) ordenados por fecha de creación descendente
     * @return array
     */
    public static function listarTodos()
    {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT * FROM tbl_cliente ORDER BY FECHA_CREACION DESC, ID_CLIENTE DESC";
            $query = $con->prepare($sql);
            $query->execute();
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::listarTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crear cliente nuevo (API simplificada)
     * @param string $nombre
     * @param string $dni
     * @param string $telefono
     * @param string $email
     * @return array|null - Datos del cliente creado o null
     */
    public static function crearCliente($nombre, $dni, $telefono = '', $email = '')
    {
        try {
            // Normalizar para comprobaciones (no sobreescribimos $dni ni $telefono; queremos guardar tal cual)
            $dni_digits = preg_replace('/\D/', '', $dni);
            $telefono_digits = preg_replace('/\D/', '', $telefono);

            // Validar que el cliente no exista (compara versión normalizada)
            $existe = self::obtenerPorDNI($dni);
            if ($existe) {
                error_log("Cliente con DNI $dni ya existe");
                return null;
            }

            $con = connectionDB::getConnection();
                $sql = "INSERT INTO tbl_cliente (NOMBRE, DNI, TELEFONO, CORREO, ESTADO, CREADO_POR, FECHA_CREACION)
                    VALUES (:nombre, :dni, :telefono, :email, 'ACTIVO', 'SISTEMA', NOW())";
            
            $query = $con->prepare($sql);
            // Insertar tal cual se recibe en el formulario (dni y telefono conservan formato)
            $resultado = $query->execute([
                ':nombre' => $nombre,
                ':dni' => $dni,
                ':telefono' => $telefono,
                ':email' => $email
            ]);

            if ($resultado) {
                $id_cliente = $con->lastInsertId();
                return self::obtenerPorID($id_cliente);
            }
            return null;
        } catch (\PDOException $e) {
            error_log("Error clienteModel::crearCliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Registrar nuevo cliente
     * @param array $datos - Datos del cliente: NOMBRE, APELLIDO, TELEFONO, DNI, CORREO, DIRECCION
     * @param int $id_usuario - ID del usuario que registra
     * @return array ['success' => bool, 'id_cliente' => int|null, 'message' => string]
     */
    public static function crear($datos, $id_usuario)
    {
        try {
            // Preparar versiones normalizadas para comprobaciones (no sobreescribimos los valores originales que se guardarán)
            $dni_digits = preg_replace('/\D/', '', $datos['DNI'] ?? '');
            $telefono_digits = preg_replace('/\D/', '', $datos['TELEFONO'] ?? '');

            // Validar que el cliente no exista (compara versión normalizada)
            $existe = self::obtenerPorDNI($datos['DNI'] ?? '');
            if ($existe) {
                return ['success' => false, 'message' => 'Cliente con este DNI ya existe'];
            }

            $con = connectionDB::getConnection();
            $sql = "INSERT INTO tbl_cliente (NOMBRE, APELLIDO, TELEFONO, DNI, CORREO, DIRECCION, ESTADO, CREADO_POR, FECHA_CREACION)
                    VALUES (:nombre, :apellido, :telefono, :dni, :correo, :direccion, 'ACTIVO', :id_usuario, NOW())";
            
            $query = $con->prepare($sql);
            // Guardar los valores tal como llegaron en el formulario
            $resultado = $query->execute([
                ':nombre' => $datos['NOMBRE'],
                ':apellido' => $datos['APELLIDO'],
                ':telefono' => $datos['TELEFONO'] ?? '',
                ':dni' => $datos['DNI'],
                ':correo' => $datos['CORREO'] ?? '',
                ':direccion' => $datos['DIRECCION'] ?? '',
                ':id_usuario' => $id_usuario
            ]);

            if ($resultado) {
                return [
                    'success' => true,
                    'id_cliente' => $con->lastInsertId(),
                    'message' => 'Cliente creado exitosamente'
                ];
            }
            return ['success' => false, 'message' => 'Error al crear cliente'];

        } catch (\PDOException $e) {
            error_log("Error clienteModel::crear: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }

    /**
     * Buscar clientes por nombre o apellido (activos e inactivos) ordenados por fecha de creación descendente
     * @param string $busqueda
     * @return array
     */
    public static function buscar($busqueda)
    {
        try {
            $con = connectionDB::getConnection();
            $busqueda = "%{$busqueda}%";
            $sql = "SELECT * FROM tbl_cliente 
                    WHERE (NOMBRE LIKE :busqueda OR APELLIDO LIKE :busqueda OR DNI LIKE :busqueda)
                    ORDER BY FECHA_CREACION DESC, ID_CLIENTE DESC
                    LIMIT 20";

            $query = $con->prepare($sql);
            $query->execute([':busqueda' => $busqueda]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::buscar: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar clientes activos por nombre o apellido, filtrando por ESTADO = 'ACTIVO'
     * @param string $busqueda
     * @return array
     */
    public static function buscarActivos($busqueda)
    {
        try {
            $con = connectionDB::getConnection();
            $busqueda = "%{$busqueda}%";
            $sql = "SELECT * FROM tbl_cliente 
                    WHERE ESTADO = 'ACTIVO' AND (NOMBRE LIKE :busqueda OR APELLIDO LIKE :busqueda OR DNI LIKE :busqueda)
                    ORDER BY FECHA_CREACION DESC, ID_CLIENTE DESC
                    LIMIT 20";

            $query = $con->prepare($sql);
            $query->execute([':busqueda' => $busqueda]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error clienteModel::buscarActivos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registrar acción en bitácora del sistema
     * Integración con tbl_ms_bitacora para auditoría completa
     * @param int $idUsuario - ID del usuario que realiza la acción
     * @param string $accion - Tipo de acción (REGISTRAR_CLIENTE, ACTUALIZAR_CLIENTE, etc.)
     * @param string $descripcion - Descripción detallada de la acción
     * @param int $idObjeto - ID_OBJETO de tbl_ms_objetos (VENTAS = correspondiente ID)
     * @param string $creadoPor - Usuario o sistema que registra la acción
     * @return bool - true si se registró exitosamente
     */
    public static function registrarBitacora($idUsuario, $accion, $descripcion, $idObjeto = null, $creadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            // Si no se especifica ID_OBJETO, obtener el de VENTAS
            if (!$idObjeto) {
                $sql_objeto = "SELECT ID_OBJETO FROM tbl_ms_objetos WHERE OBJETO = 'VENTAS' LIMIT 1";
                $query_objeto = $con->prepare($sql_objeto);
                $query_objeto->execute();
                $resultado_objeto = $query_objeto->fetch(\PDO::FETCH_ASSOC);
                $idObjeto = $resultado_objeto['ID_OBJETO'] ?? null;
            }
            // Registrar en bitácora
            $sql = "INSERT INTO TBL_MS_BITACORA (FECHA, ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR, FECHA_CREACION) 
                    VALUES (NOW(), :id_usuario, :id_objeto, :accion, :descripcion, :creado_por, NOW())";
            $query = $con->prepare($sql);
            $query->execute([
                ':id_usuario' => $idUsuario,
                ':id_objeto' => $idObjeto,
                ':accion' => $accion,
                ':descripcion' => $descripcion,
                ':creado_por' => $creadoPor
            ]);
            return true;
        } catch (\PDOException $e) {
            error_log("clienteModel::registrarBitacora -> " . $e->getMessage());
            // No lanzar excepción, solo registrar el error. La bitácora no debe afectar el flujo principal
            return false;
        }
    }

    /**
     * Eliminar cliente (soft delete): cambiar estado a INACTIVO
     * @param int $id_cliente
     * @return array ['success' => bool, 'message' => string]
     */
    public static function eliminar($id_cliente, $id_usuario = null)
    {
        try {
            $con = connectionDB::getConnection();
            error_log("clienteModel::eliminar - entrada para id_cliente=" . $id_cliente . ", id_usuario=" . var_export($id_usuario, true));

            // Borrado físico solicitado: intentar DELETE
            try {
                $sql = "DELETE FROM tbl_cliente WHERE ID_CLIENTE = :id_cliente";
                $query = $con->prepare($sql);
                $resultado = $query->execute([':id_cliente' => $id_cliente]);
            } catch (\PDOException $e) {
                // Devolver error con detalle (por ejemplo, FK constraint)
                error_log("clienteModel::eliminar -> error ejecutando DELETE: " . $e->getMessage());
                return ['success' => false, 'message' => 'Error al eliminar cliente: ' . $e->getMessage()];
            }

            if ($resultado) {
                // Registrar bitácora si se proporcionó usuario
                if (!empty($id_usuario)) {
                    try {
                        self::registrarBitacora($id_usuario, 'ELIMINAR_CLIENTE', "Cliente ID {$id_cliente} eliminado", null, 'SISTEMA');
                    } catch (\Throwable $t) {
                        error_log("clienteModel::eliminar -> error registrando bitacora: " . $t->getMessage());
                    }
                }
                error_log("clienteModel::eliminar - DELETE exitoso para id=" . $id_cliente);
                return ['success' => true, 'message' => 'Cliente eliminado correctamente'];
            }
            return ['success' => false, 'message' => 'Error al eliminar cliente'];
        } catch (\PDOException $e) {
            error_log("clienteModel::eliminar -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }

    /**
     * Actualizar cliente existente
     * @param array $datos - Datos del cliente que incluye ID_CLIENTE y campos a actualizar
     * @param int $id_usuario - ID del usuario que realiza la actualización
     * @return array ['success' => bool, 'message' => string]
     */
    public static function actualizar($datos, $id_usuario)
    {
        if (empty($datos['ID_CLIENTE'])) {
            return ['success' => false, 'message' => 'ID_CLIENTE es requerido para actualizar'];
        }
        try {
            // Preparar versiones normalizadas para comprobaciones (no sobreescribimos los valores originales que se guardarán)
            $dni_digits = !empty($datos['DNI']) ? preg_replace('/\D/', '', $datos['DNI']) : '';
            $telefono_digits = !empty($datos['TELEFONO']) ? preg_replace('/\D/', '', $datos['TELEFONO']) : '';
            $con = connectionDB::getConnection();
            // Evitar duplicado de DNI en otro registro
            if (!empty($dni_digits)) {
                // Comparar contra la versión normalizada almacenada (el campo DNI puede contener espacios/guiones)
                $sql_dup = "SELECT ID_CLIENTE FROM tbl_cliente WHERE REPLACE(REPLACE(REPLACE(DNI, ' ', ''), '-', ''), '.', '') = :dni AND ID_CLIENTE != :id_cliente LIMIT 1";
                $qdup = $con->prepare($sql_dup);
                $qdup->execute([':dni' => $dni_digits, ':id_cliente' => $datos['ID_CLIENTE']]);
                $exists = $qdup->fetch(\PDO::FETCH_ASSOC);
                if ($exists) {
                    return ['success' => false, 'message' => 'El DNI ya está asignado a otro cliente'];
                }
            }
            $sql = "UPDATE tbl_cliente SET 
                        NOMBRE = :nombre, 
                        APELLIDO = :apellido, 
                        DNI = :dni, 
                        TELEFONO = :telefono, 
                        CORREO = :correo, 
                        DIRECCION = :direccion, 
                        ESTADO = :estado, 
                        MODIFICADO_POR = :id_usuario,
                        FECHA_MODIFICACION = NOW()
                    WHERE ID_CLIENTE = :id_cliente";
            $query = $con->prepare($sql);
            $resultado = $query->execute([
                ':nombre' => $datos['NOMBRE'] ?? '',
                ':apellido' => $datos['APELLIDO'] ?? '',
                ':dni' => $datos['DNI'] ?? '',
                ':telefono' => $datos['TELEFONO'] ?? '',
                ':correo' => $datos['CORREO'] ?? '',
                ':direccion' => $datos['DIRECCION'] ?? '',
                ':estado' => $datos['ESTADO'] ?? 'ACTIVO',
                ':id_usuario' => $id_usuario,
                ':id_cliente' => $datos['ID_CLIENTE']
            ]);
            if ($resultado) {
                // Registrar acción en bitácora
                self::registrarBitacora(
                    $id_usuario,
                    'ACTUALIZAR_CLIENTE',
                    "Cliente ID {$datos['ID_CLIENTE']} actualizado",
                    null,
                    'SISTEMA'
                );
                return ['success' => true, 'message' => 'Cliente actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al actualizar cliente'];
            }
        } catch (\PDOException $e) {
            error_log("clienteModel::actualizar -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos'];
        }
    }
}
