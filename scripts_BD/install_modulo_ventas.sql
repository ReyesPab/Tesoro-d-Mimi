-- ============================================================
-- SCRIPT DE INTEGRACIÓN DEL MÓDULO DE VENTAS
-- Agregar permisos y objetos necesarios
-- ============================================================

-- 1. AGREGAR OBJETO DE VENTAS (si no existe)
INSERT INTO tbl_ms_objetos (OBJETO, DESCRIPCION, TIPO_OBJETO, ESTADO) 
SELECT 'VENTAS', 'Módulo de Ventas - Registrar y consultar transacciones', 'MODULO', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_ms_objetos WHERE OBJETO = 'VENTAS');

-- 2. AGREGAR PERMISOS GRANULARES DE VENTAS (si no existen)
-- Permiso general de acceso al módulo
INSERT INTO tbl_ms_permisos (PERMISO, DESCRIPCION, ESTADO) 
SELECT 'VENTAS', 'Acceso al módulo de ventas', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_ms_permisos WHERE PERMISO = 'VENTAS');

-- Permiso para crear ventas
INSERT INTO tbl_ms_permisos (PERMISO, DESCRIPCION, ESTADO) 
SELECT 'CREAR_VENTA', 'Crear nueva venta y generar facturas', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_ms_permisos WHERE PERMISO = 'CREAR_VENTA');

-- Permiso para consultar ventas
INSERT INTO tbl_ms_permisos (PERMISO, DESCRIPCION, ESTADO) 
SELECT 'CONSULTAR_VENTAS', 'Consultar y ver historial de ventas realizadas', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_ms_permisos WHERE PERMISO = 'CONSULTAR_VENTAS');

-- Permiso para registrar cliente
INSERT INTO tbl_ms_permisos (PERMISO, DESCRIPCION, ESTADO) 
SELECT 'REGISTRAR_CLIENTE', 'Registrar nuevos clientes en el sistema', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_ms_permisos WHERE PERMISO = 'REGISTRAR_CLIENTE');

-- 3. ASIGNAR PERMISOS AL ROL ADMIN (ID_ROL = 1)
-- Buscar ID de admin y agregar permisos
INSERT INTO tbl_ms_rol_permiso (ID_ROL, ID_PERMISO) 
SELECT 1, tp.ID_PERMISO
FROM tbl_ms_permisos tp
WHERE tp.PERMISO IN ('VENTAS', 'CREAR_VENTA', 'CONSULTAR_VENTAS', 'REGISTRAR_CLIENTE')
AND NOT EXISTS (
    SELECT 1 FROM tbl_ms_rol_permiso trp 
    WHERE trp.ID_ROL = 1 AND trp.ID_PERMISO = tp.ID_PERMISO
);

-- 4. VERIFICAR MÉTODOS DE PAGO (insertar si no existen)
INSERT INTO tbl_metodo_pago (METODO_PAGO, DESCRIPCION, ESTADO)
SELECT 'Efectivo', 'Pago en efectivo', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_metodo_pago WHERE METODO_PAGO = 'Efectivo');

INSERT INTO tbl_metodo_pago (METODO_PAGO, DESCRIPCION, ESTADO)
SELECT 'Tarjeta', 'Pago con tarjeta de crédito/débito', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_metodo_pago WHERE METODO_PAGO = 'Tarjeta');

INSERT INTO tbl_metodo_pago (METODO_PAGO, DESCRIPCION, ESTADO)
SELECT 'Transferencia', 'Pago por transferencia bancaria', 'ACTIVO'
WHERE NOT EXISTS (SELECT 1 FROM tbl_metodo_pago WHERE METODO_PAGO = 'Transferencia');

-- 5. VERIFICAR QUE EXISTAN CLIENTES (crear cliente por defecto si es necesario)
INSERT INTO tbl_cliente (NOMBRE, APELLIDO, DNI, TELEFONO, CORREO, DIRECCION, ESTADO, CREADO_POR, FECHA_CREACION)
SELECT 'Cliente', 'General', '00000000', '', '', '', 'ACTIVO', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM tbl_cliente WHERE DNI = '00000000');

-- 6. VERIFICAR PRODUCTOS CON INVENTARIO (son necesarios para vender)
-- Esta es una verificación informativa, los productos ya deben existir

-- ============================================================
-- CONSULTAS DE VERIFICACIÓN
-- ============================================================

-- Ver permisos de ventas creados:
-- SELECT * FROM tbl_ms_permisos WHERE PERMISO LIKE '%VENTA%' OR PERMISO = 'VENTAS';

-- Ver objeto de ventas:
-- SELECT * FROM tbl_ms_objetos WHERE OBJETO = 'VENTAS';

-- Ver métodos de pago:
-- SELECT * FROM tbl_metodo_pago WHERE ESTADO = 'ACTIVO';

-- Ver clientes:
-- SELECT * FROM tbl_cliente WHERE ESTADO = 'ACTIVO' LIMIT 5;

-- Ver productos con stock:
-- SELECT p.*, ip.CANTIDAD 
-- FROM tbl_producto p
-- LEFT JOIN tbl_inventario_producto ip ON p.ID_PRODUCTO = ip.ID_PRODUCTO
-- WHERE p.ESTADO = 'ACTIVO' AND ip.CANTIDAD > 0
-- LIMIT 10;

-- Ver bitácora de ventas (después de usar el módulo):
-- SELECT b.* FROM TBL_MS_BITACORA b
-- INNER JOIN tbl_ms_objetos o ON b.ID_OBJETO = o.ID_OBJETO
-- WHERE o.OBJETO = 'VENTAS'
-- ORDER BY b.FECHA DESC LIMIT 50;

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
