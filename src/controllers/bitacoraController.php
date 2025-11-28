<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\models\bitacoraModel;

class bitacoraController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = $data;
    }
    
    /**
     * Obtener bitácora con filtros
     */
public function obtenerBitacora() {
    if ($this->method != 'get') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $filtros = [
            'usuario' => $_GET['usuario'] ?? '',
            'accion' => $_GET['accion'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? '',
            'pagina' => $_GET['pagina'] ?? 1,
            'limite' => $_GET['limite'] ?? 100
        ];
        
        $resultado = bitacoraModel::obtenerBitacoraFiltrada($filtros);
        
        // 🔥 Asegurar que la estructura sea consistente
        echo json_encode([
            'status' => 200,
            'data' => [
                'bitacora' => $resultado['bitacora'] ?? [],
                'paginacion' => $resultado['paginacion'] ?? []
            ],
            'message' => 'Bitácora obtenida correctamente'
        ], JSON_PRETTY_PRINT); // Agregar JSON_PRETTY_PRINT para debugging
        
    } catch (\Exception $e) {
        error_log("Error en bitacoraController: " . $e->getMessage());
        echo json_encode(responseHTTP::status500('Error al obtener la bitácora'));
    }
}
    /**
 * Registrar navegación automática
 */
public static function registrarNavegacion($idUsuario, $pagina, $accion = 'NAVEGACION') {
    try {
        // Mapeo de páginas a ID_OBJETO (actualiza según tus objetos)
        $mapaObjetos = [
            'inicio' => 1,
            'dashboard' => 1,
            'gestion-usuarios' => 2,
            'crear-usuario' => 2,
            'editar-usuario' => 2,
            'resetear-contrasena' => 2,
            'cambiar-password' => 6,
            'bitacora' => 4,
            'perfil' => 6,
            'configurar-2fa' => 6,
            'gestion-proveedores' => 7,
            'registrar-proveedor' => 7,
            'editar-proveedor' => 7,
            'gestion-productos-proveedor' => 7,
            'consultar-ordenes-pendientes' => 8,
            'consultar-compras' => 9,
            'registrar-compras' => 9,
            'detalle-compra' => 9,
            'gestion-materia-prima' => 10,
            'registrar-materia-prima' => 10,
            'editar-materia-prima' => 10,
            'crear-produccion' => 11,
            'gestion-produccion' => 12,
            'finalizar-produccion' => 12,
            'detalle-produccion' => 12,
            'ver-recetas' => 13,
            'crear-receta' => 13,
            'gestion-inventario' => 14,
            'gestion-inventario-productos' => 14,
            'gestion-productos' => 15,
            'editar-producto' => 15,
            'registrar-venta' => 17,
            'consultar-ventas' => 17,
            'ventas' => 17,
            'gestion-backups' => 16,
            'restaurar-backup' => 16
        ];
        
        $idObjeto = $mapaObjetos[$pagina] ?? 1; // Default a 1 si no se encuentra
        $descripcion = self::obtenerDescripcionPagina($pagina);
        
        bitacoraModel::registrarAccion($idUsuario, $idObjeto, $accion, $descripcion);
        
    } catch (\Exception $e) {
        error_log("Error en registrarNavegacion: " . $e->getMessage());
    }
}

private static function obtenerDescripcionPagina($pagina) {
    $descripciones = [
        'inicio' => 'Accedió a la página de Inicio',
        'dashboard' => 'Accedió al Dashboard principal',
        'gestion-usuarios' => 'Accedió a la gestión de usuarios',
        'crear-usuario' => 'Accedió a crear nuevo usuario',
        'editar-usuario' => 'Accedió a editar usuario',
        'resetear-contrasena' => 'Accedió a resetear contraseña',
        'cambiar-password' => 'Accedió a cambiar contraseña',
        'bitacora' => 'Consultó la bitácora del sistema',
        'perfil' => 'Consultó su perfil de usuario',
        'configurar-2fa' => 'Accedió a configurar autenticación en dos pasos',
        'gestion-proveedores' => 'Accedió a gestión de proveedores',
        'registrar-proveedor' => 'Accedió a registrar proveedor',
        'editar-proveedor' => 'Accedió a editar proveedor',
        'gestion-productos-proveedor' => 'Accedió a gestión de productos por proveedor',
        'consultar-ordenes-pendientes' => 'Consultó órdenes de compra pendientes',
        'consultar-compras' => 'Consultó compras realizadas',
        'registrar-compras' => 'Accedió a registrar nueva compra',
        'detalle-compra' => 'Consultó detalle de compra',
        'gestion-materia-prima' => 'Accedió a gestión de materia prima',
        'registrar-materia-prima' => 'Accedió a registrar materia prima',
        'editar-materia-prima' => 'Accedió a editar materia prima',
        'crear-produccion' => 'Accedió a crear orden de producción',
        'gestion-produccion' => 'Accedió a gestión de producción',
        'finalizar-produccion' => 'Accedió a finalizar producción',
        'detalle-produccion' => 'Consultó detalle de producción',
        'ver-recetas' => 'Consultó recetas de producción',
        'crear-receta' => 'Accedió a crear nueva receta',
        'gestion-inventario' => 'Accedió a gestión de inventario',
        'gestion-inventario-productos' => 'Accedió a gestión de inventario de productos',
        'gestion-productos' => 'Accedió a gestión de productos',
        'editar-producto' => 'Accedió a editar producto',
        'registrar-venta' => 'Accedió a registrar nueva venta',
        'consultar-ventas' => 'Consultó historial de ventas',
        'ventas' => 'Accedió a módulo de ventas',
        'gestion-backups' => 'Accedió a gestión de respaldos',
        'restaurar-backup' => 'Accedió a restaurar respaldo'
    ];
    
    return $descripciones[$pagina] ?? "Navegó a: $pagina";
}
  
    
    /**
     * Obtener estadísticas de bitácora
     */
    public function obtenerEstadisticas() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $estadisticas = bitacoraModel::obtenerEstadisticas();
            
            echo json_encode([
                'status' => 200,
                'data' => $estadisticas,
                'message' => 'Estadísticas obtenidas correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener estadísticas'));
        }
    }
}