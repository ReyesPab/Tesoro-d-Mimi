-- Script para crear el procedimiento almacenado SP_CREAR_PRODUCTO
-- Crea un nuevo producto en tbl_producto
-- Uso: CALL SP_CREAR_PRODUCTO(nombre, id_unidad_medida, precio, cantidad_minima, cantidad_maxima, estado, descripcion, creado_por, @resultado, @id_producto);

-- INSTRUCCIONES:
-- - Si vas a pegar esto en phpMyAdmin o MySQL Workbench, pega todo y ejecuta tal cual.
-- - Si usas el cliente mysql desde la terminal, ejecuta:
--     mysql -u root -p rosquilleria < "C:/xampp/htdocs/sistema/db/sp_scripts/create_SP_CREAR_PRODUCTO.sql"
-- - Si prefieres copiar/pegar manualmente en la consola, asegúrate de que el cliente soporte la directiva DELIMITER.

DROP PROCEDURE IF EXISTS `SP_CREAR_PRODUCTO`;
DELIMITER $$
CREATE PROCEDURE `SP_CREAR_PRODUCTO`(
    IN p_nombre VARCHAR(100),
    IN p_id_unidad_medida INT,
    IN p_precio DECIMAL(10,2),
    IN p_cantidad_minima DECIMAL(10,2),
    IN p_cantidad_maxima DECIMAL(10,2),
    IN p_estado VARCHAR(20),
    IN p_descripcion VARCHAR(255),
    IN p_id_usuario INT,
    IN p_creado_por VARCHAR(50),
    OUT p_resultado VARCHAR(255),
    OUT p_id_producto INT
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;
    DECLARE v_unidad_existe INT DEFAULT 0;

    -- Validar que la unidad de medida existe
    SELECT COUNT(*) INTO v_unidad_existe FROM tbl_unidad_medida WHERE ID_UNIDAD_MEDIDA = p_id_unidad_medida;
    IF v_unidad_existe = 0 THEN
        SET p_resultado = CONCAT('ERROR: La unidad de medida con ID ', p_id_unidad_medida, ' no existe');
        SET p_id_producto = 0;
    ELSE
        -- Validar que el usuario existe (tbl_ms_usuarios)
        IF p_id_usuario IS NULL OR p_id_usuario = 0 THEN
            SET p_resultado = 'ERROR: p_id_usuario no puede ser NULL o 0';
            SET p_id_producto = 0;
            RETURN;
        END IF;
        DECLARE v_usuario_existe INT DEFAULT 0;
        SELECT COUNT(*) INTO v_usuario_existe FROM tbl_ms_usuarios WHERE ID_USUARIO = p_id_usuario;
        IF v_usuario_existe = 0 THEN
            SET p_resultado = CONCAT('ERROR: El usuario con ID ', p_id_usuario, ' no existe');
            SET p_id_producto = 0;
            RETURN;
        END IF;
        -- Validar nombre único
        SELECT COUNT(*) INTO v_existe FROM tbl_producto WHERE NOMBRE = p_nombre;
        IF v_existe > 0 THEN
            SET p_resultado = CONCAT('ERROR: Ya existe un producto con el nombre "', p_nombre, '"');
            SET p_id_producto = 0;
        ELSEIF p_cantidad_minima >= p_cantidad_maxima THEN
            SET p_resultado = 'ERROR: La cantidad mínima debe ser menor que la cantidad máxima';
            SET p_id_producto = 0;
        ELSE
            -- Insertar producto
            INSERT INTO tbl_producto (
                NOMBRE,
                ID_UNIDAD_MEDIDA,
                PRECIO,
                MINIMO,
                MAXIMO,
                ESTADO,
                DESCRIPCION,
                CREADO_POR,
                FECHA_CREACION,
                CANTIDAD
            ) VALUES (
                p_nombre,
                p_id_unidad_medida,
                p_precio,
                p_cantidad_minima,
                p_cantidad_maxima,
                p_estado,
                p_descripcion,
                p_creado_por,
                NOW(),
                0
            );

            -- Obtener ID del producto insertado
            SET p_id_producto = LAST_INSERT_ID();

            -- Crear registro inicial en cardex (stock 0) usando el usuario provisto
            INSERT INTO tbl_cardex_producto (
                ID_PRODUCTO,
                CANTIDAD,
                TIPO_MOVIMIENTO,
                ID_USUARIO,
                DESCRIPCION,
                FECHA_MOVIMIENTO,
                CREADO_POR
            ) VALUES (
                p_id_producto,
                0,
                'INICIAL',
                p_id_usuario,
                'Creación de producto',
                NOW(),
                p_creado_por
            );

            SET p_resultado = CONCAT('OK: Producto creado con ID ', p_id_producto);
        END IF;
    END IF;
END$$
DELIMITER ;