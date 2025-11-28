-- Script para crear el procedimiento almacenado SP_AJUSTAR_INVENTARIO_PRODUCTO
-- Ajusta stock en tbl_producto y registra movimiento en tbl_cardex_producto.
-- Uso: CALL SP_AJUSTAR_INVENTARIO_PRODUCTO(id_producto, cantidad, tipo_movimiento, descripcion, id_usuario, actualizado_por, @resultado);

DELIMITER $$
CREATE PROCEDURE `SP_AJUSTAR_INVENTARIO_PRODUCTO`(
    IN p_id_producto INT,
    IN p_cantidad DECIMAL(10,2),
    IN p_tipo_movimiento VARCHAR(20),
    IN p_descripcion VARCHAR(255),
    IN p_id_usuario INT,
    IN p_actualizado_por VARCHAR(50),
    OUT p_resultado VARCHAR(255)
)
BEGIN
    DECLARE v_actual DECIMAL(10,2);
    DECLARE v_nuevo DECIMAL(10,2);

    START TRANSACTION;
    -- Obtener stock actual
    SELECT CANTIDAD INTO v_actual FROM tbl_producto WHERE ID_PRODUCTO = p_id_producto FOR UPDATE;

    IF v_actual IS NULL THEN
        SET p_resultado = CONCAT('ERROR: Producto no encontrado (ID=', p_id_producto, ')');
        ROLLBACK;
        LEAVE BEGIN;
    END IF;

    -- Comportamiento según tipo de movimiento
    IF UPPER(p_tipo_movimiento) = 'SALIDA' THEN
        SET v_nuevo = v_actual - p_cantidad;
        IF v_nuevo < 0 THEN
            SET p_resultado = 'ERROR: Stock insuficiente para la salida';
            ROLLBACK;
            LEAVE BEGIN;
        END IF;
    ELSEIF UPPER(p_tipo_movimiento) = 'ENTRADA' THEN
        SET v_nuevo = v_actual + p_cantidad;
    ELSEIF UPPER(p_tipo_movimiento) = 'AJUSTE' THEN
        -- Para AJUSTE, interpretamos p_cantidad como el nuevo stock absoluto
        SET v_nuevo = p_cantidad;
    ELSE
        SET p_resultado = 'ERROR: Tipo de movimiento no reconocido';
        ROLLBACK;
        LEAVE BEGIN;
    END IF;

    -- Actualizar producto
    UPDATE tbl_producto SET CANTIDAD = v_nuevo WHERE ID_PRODUCTO = p_id_producto;

    -- Registrar en cardex
    INSERT INTO tbl_cardex_producto (ID_PRODUCTO, CANTIDAD, TIPO_MOVIMIENTO, ID_USUARIO, DESCRIPCION, CREADO_POR)
    VALUES (p_id_producto, p_cantidad, p_tipo_movimiento, p_id_usuario, p_descripcion, p_actualizado_por);

    COMMIT;

    SET p_resultado = CONCAT('OK: Stock actualizado a ', v_nuevo);
END$$
DELIMITER ;
