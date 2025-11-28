-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: rosquilleria
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_alertas_sistema`
--

DROP TABLE IF EXISTS `tbl_alertas_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_alertas_sistema` (
  `ID_ALERTA` int(11) NOT NULL AUTO_INCREMENT,
  `TIPO_ALERTA` varchar(50) NOT NULL,
  `TITULO` varchar(255) NOT NULL,
  `DESCRIPCION` text DEFAULT NULL,
  `ID_REFERENCIA` int(11) DEFAULT NULL,
  `TABLA_REFERENCIA` varchar(50) DEFAULT NULL,
  `NIVEL_URGENCIA` varchar(20) DEFAULT 'MEDIA',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `FECHA_EXPIRACION` datetime DEFAULT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVA',
  `LEIDA` tinyint(1) DEFAULT 0,
  `CREADO_POR` varchar(50) DEFAULT 'SISTEMA',
  PRIMARY KEY (`ID_ALERTA`),
  KEY `idx_tipo_alerta` (`TIPO_ALERTA`),
  KEY `idx_fecha_expiracion` (`FECHA_EXPIRACION`),
  KEY `idx_estado` (`ESTADO`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_alertas_sistema`
--

LOCK TABLES `tbl_alertas_sistema` WRITE;
/*!40000 ALTER TABLE `tbl_alertas_sistema` DISABLE KEYS */;
INSERT INTO `tbl_alertas_sistema` VALUES (103,'INVENTARIO_MP_BAJO','Stock bajo: Harina para repostería','El stock de Harina para repostería está por debajo del mínimo. Stock actual: 6.48 KG. Mínimo requerido: 10.00 KG',23,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(104,'INVENTARIO_MP_BAJO','Stock bajo: Azúcar glass','El stock de Azúcar glass está por debajo del mínimo. Stock actual: 3.68 KG. Mínimo requerido: 10.00 KG',25,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(105,'INVENTARIO_MP_BAJO','Stock bajo: Leche Láctea','El stock de Leche Láctea está por debajo del mínimo. Stock actual: 3.00 LT. Mínimo requerido: 10.00 LT',20,'tbl_materia_prima','ALTA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(106,'INVENTARIO_MP_BAJO','Stock bajo: Aceite vegetal','El stock de Aceite vegetal está por debajo del mínimo. Stock actual: 5.00 LT. Mínimo requerido: 10.00 LT',29,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(107,'INVENTARIO_MP_BAJO','Stock bajo: Azúcar refinada','El stock de Azúcar refinada está por debajo del mínimo. Stock actual: 4.00 LB. Mínimo requerido: 10.00 LB',19,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(110,'INVENTARIO_MP_EXCESIVO','Stock excesivo: Polvo de hornear','El stock de Polvo de hornear excede el máximo permitido. Stock actual: 1.56 KG. Máximo permitido: 0.00 KG',24,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(111,'INVENTARIO_MP_EXCESIVO','Stock excesivo: Huevos blancos','El stock de Huevos blancos excede el máximo permitido. Stock actual: 3.00 UN. Máximo permitido: 0.00 UN',22,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(112,'INVENTARIO_MP_EXCESIVO','Stock excesivo: Harina de maíz','El stock de Harina de maíz excede el máximo permitido. Stock actual: 2.00 LB. Máximo permitido: 0.00 LB',21,'tbl_materia_prima','MEDIA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(113,'INVENTARIO_PROD_BAJO','Stock bajo: Rosquilla Clásica','El stock de Rosquilla Clásica está por debajo del mínimo. Stock actual: 10.00 UN. Mínimo requerido: 50.00 UN',1,'tbl_producto','ALTA','2025-11-07 23:50:27','2025-11-08 23:50:27','ACTIVA',0,'SISTEMA'),(114,'INICIO_SESION','Inicio de sesión: USER','El usuario Nuevo Usuario del Sistema (USER) ha iniciado sesión',2,'tbl_ms_usuarios','BAJA','2025-11-07 23:50:27','2025-11-08 01:50:27','ACTIVA',0,'SISTEMA');
/*!40000 ALTER TABLE `tbl_alertas_sistema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_cardex_materia_prima`
--

DROP TABLE IF EXISTS `tbl_cardex_materia_prima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cardex_materia_prima` (
  `ID_CARDEX_MP` int(11) NOT NULL AUTO_INCREMENT,
  `ID_MATERIA_PRIMA` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `TIPO_MOVIMIENTO` varchar(20) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `FECHA_MOVIMIENTO` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_CARDEX_MP`),
  KEY `ID_MATERIA_PRIMA` (`ID_MATERIA_PRIMA`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  CONSTRAINT `tbl_cardex_materia_prima_ibfk_1` FOREIGN KEY (`ID_MATERIA_PRIMA`) REFERENCES `tbl_materia_prima` (`ID_MATERIA_PRIMA`),
  CONSTRAINT `tbl_cardex_materia_prima_ibfk_2` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cardex_materia_prima`
--

LOCK TABLES `tbl_cardex_materia_prima` WRITE;
/*!40000 ALTER TABLE `tbl_cardex_materia_prima` DISABLE KEYS */;
INSERT INTO `tbl_cardex_materia_prima` VALUES (29,19,2.00,'ENTRADA',1,'Ingreso al inventario: Ingresó al inventario','2025-10-31 15:08:07','ADMIN'),(30,19,2.00,'ENTRADA',1,'Ingreso al inventario: Ingresó al inventario','2025-10-31 15:10:40','ADMIN'),(31,20,3.00,'ENTRADA',1,'Ingreso al inventario: Ingreso al inventario','2025-10-31 15:20:47','ADMIN'),(32,22,3.00,'ENTRADA',1,'Ingreso al inventario: ..','2025-10-31 15:27:34','ADMIN'),(33,21,2.00,'ENTRADA',1,'Ingreso al inventario: ','2025-10-31 16:05:41','ADMIN'),(34,21,0.00,'EDICION',1,'Edición de información: Descripción modificada; Mínimo: 0.00 → 20.00; Máximo: 0.00 → 25.00; ','2025-10-31 23:34:27','SISTEMA'),(35,29,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 20.00; ','2025-11-01 09:01:02','SISTEMA'),(36,25,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 20.00; ','2025-11-01 09:01:41','SISTEMA'),(37,27,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 19.99; ','2025-11-01 09:02:05','SISTEMA'),(38,28,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 19.99; ','2025-11-01 09:03:07','SISTEMA'),(39,23,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 20.00; ','2025-11-01 09:03:40','SISTEMA'),(40,22,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 30.00; Máximo: 0.00 → 50.00; ','2025-11-01 09:04:12','SISTEMA'),(41,31,0.00,'EDICION',1,'Edición de información: Mínimo: 0.00 → 10.00; Máximo: 0.00 → 20.00; ','2025-11-01 09:04:36','SISTEMA'),(42,23,10.00,'ENTRADA',1,'Ingreso al inventario: ','2025-11-01 09:07:19','ADMIN'),(43,25,5.00,'ENTRADA',1,'Ingreso al inventario: ','2025-11-01 09:07:54','ADMIN'),(44,24,2.00,'ENTRADA',1,'Ingreso al inventario: ','2025-11-01 09:08:27','ADMIN'),(45,23,1.60,'SALIDA',2,'Producción #5 - Rosquilla Clásica','2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(46,24,0.20,'SALIDA',2,'Producción #5 - Rosquilla Clásica','2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(47,25,0.60,'SALIDA',2,'Producción #5 - Rosquilla Clásica','2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(48,29,5.00,'ENTRADA',1,'Ingreso al inventario: ','2025-11-04 03:52:33','ADMIN'),(49,29,0.00,'EDICION',1,'Edición de información: Mínimo: 10.00 → 15.00; ','2025-11-04 03:53:01','SISTEMA'),(50,23,1.76,'SALIDA',28,'Producción #7 - Rosquilla Clásica','2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(51,24,0.22,'SALIDA',28,'Producción #7 - Rosquilla Clásica','2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(52,25,0.66,'SALIDA',28,'Producción #7 - Rosquilla Clásica','2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(53,23,0.16,'SALIDA',28,'Producción #6 - Rosquilla Clásica','2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(54,24,0.02,'SALIDA',28,'Producción #6 - Rosquilla Clásica','2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(55,25,0.06,'SALIDA',28,'Producción #6 - Rosquilla Clásica','2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA');
/*!40000 ALTER TABLE `tbl_cardex_materia_prima` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_cardex_producto`
--

DROP TABLE IF EXISTS `tbl_cardex_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cardex_producto` (
  `ID_CARDEX_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `TIPO_MOVIMIENTO` varchar(20) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `FECHA_MOVIMIENTO` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_CARDEX_PRODUCTO`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  CONSTRAINT `tbl_cardex_producto_ibfk_1` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_cardex_producto_ibfk_2` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cardex_producto`
--

LOCK TABLES `tbl_cardex_producto` WRITE;
/*!40000 ALTER TABLE `tbl_cardex_producto` DISABLE KEYS */;
INSERT INTO `tbl_cardex_producto` VALUES (1,1,10.00,'ENTRADA',28,'Ingreso a inventario - Producto: Rosquilla Clásica - Cantidad: 10.00','2025-11-04 10:32:11','ADMINISTRADOR DEL SISTEMA');
/*!40000 ALTER TABLE `tbl_cardex_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_catalogo_motivos_perdida`
--

DROP TABLE IF EXISTS `tbl_catalogo_motivos_perdida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_catalogo_motivos_perdida` (
  `ID_MOTIVO` int(11) NOT NULL AUTO_INCREMENT,
  `CODIGO` varchar(10) NOT NULL,
  `MOTIVO` varchar(50) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `ESTADO` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_MOTIVO`),
  UNIQUE KEY `CODIGO` (`CODIGO`),
  UNIQUE KEY `MOTIVO` (`MOTIVO`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_catalogo_motivos_perdida`
--

LOCK TABLES `tbl_catalogo_motivos_perdida` WRITE;
/*!40000 ALTER TABLE `tbl_catalogo_motivos_perdida` DISABLE KEYS */;
INSERT INTO `tbl_catalogo_motivos_perdida` VALUES (1,'DEF_CALIDA','Defecto de Calidad','Productos que no cumplen con los estándares de calidad','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(2,'EQUIPO','Falla de Equipo','Pérdida por mal funcionamiento de maquinaria','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(3,'MAT_PRIMA','Materia Prima Defectuosa','Pérdida por materiales de entrada en mal estado','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(4,'PROCESO','Error en Proceso','Error durante el proceso de producción','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(5,'MANIPULACI','Mala Manipulación','Daño por manejo inadecuado del producto','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(6,'ALMACEN','Problema de Almacenamiento','Daño por condiciones inadecuadas de almacen','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(7,'CADUCIDAD','Caducidad','Productos que alcanzaron fecha de vencimiento','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(8,'PRUEBA','Muestras de Prueba','Productos utilizados para pruebas de calidad','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(9,'CLIENTE','Rechazo de Cliente','Productos rechazados por el cliente','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL),(10,'OTRO','Otro Motivo','Otras causas no especificadas','ACTIVO','2025-11-02 09:19:22',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tbl_catalogo_motivos_perdida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_cliente`
--

DROP TABLE IF EXISTS `tbl_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_cliente` (
  `ID_CLIENTE` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) NOT NULL,
  `APELLIDO` varchar(100) NOT NULL,
  `TELEFONO` varchar(20) DEFAULT NULL,
  `DNI` varchar(20) DEFAULT NULL,
  `CORREO` varchar(50) DEFAULT NULL,
  `DIRECCION` varchar(255) DEFAULT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_CLIENTE`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_cliente`
--

LOCK TABLES `tbl_cliente` WRITE;
/*!40000 ALTER TABLE `tbl_cliente` DISABLE KEYS */;
INSERT INTO `tbl_cliente` VALUES (1,'Ana','García','2233-4455','0801199901234','ana.garcia@email.com','Colonia Los Pinos, Tegucigalpa','ACTIVO','2025-10-27 09:47:41','SISTEMA',NULL,NULL),(2,'Carlos','Martínez','2244-5566','0801199905678','carlos.martinez@email.com','Residencial La Esperanza, San Pedro Sula','ACTIVO','2025-10-27 09:47:41','SISTEMA',NULL,NULL),(3,'María','López','2255-6677','0801199909012','maria.lopez@email.com','Barrio El Centro, Comayagua','ACTIVO','2025-10-27 09:47:41','SISTEMA',NULL,NULL),(4,'José','Hernández','2266-7788','0801199903456','jose.hernandez@email.com','Colonia Palmira, Tegucigalpa','ACTIVO','2025-10-27 09:47:41','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_compra`
--

DROP TABLE IF EXISTS `tbl_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_compra` (
  `ID_COMPRA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_RECEPCION` int(11) DEFAULT NULL,
  `ID_PROVEEDOR` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `TOTAL_COMPRA` decimal(10,2) NOT NULL,
  `FECHA_COMPRA` datetime DEFAULT current_timestamp(),
  `ESTADO_COMPRA` varchar(20) DEFAULT 'PENDIENTE',
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_COMPRA`),
  KEY `ID_PROVEEDOR` (`ID_PROVEEDOR`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  KEY `ID_RECEPCION` (`ID_RECEPCION`),
  CONSTRAINT `tbl_compra_ibfk_1` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `tbl_proveedor` (`ID_PROVEEDOR`),
  CONSTRAINT `tbl_compra_ibfk_2` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`),
  CONSTRAINT `tbl_compra_ibfk_3` FOREIGN KEY (`ID_RECEPCION`) REFERENCES `tbl_recepcion_compra` (`ID_RECEPCION`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_compra`
--

LOCK TABLES `tbl_compra` WRITE;
/*!40000 ALTER TABLE `tbl_compra` DISABLE KEYS */;
INSERT INTO `tbl_compra` VALUES (33,1,1,1,26.00,'2025-10-30 23:38:07','COMPLETADA','','2025-10-30 23:38:07','SISTEMA',NULL,'SISTEMA'),(34,2,7,1,59.00,'2025-10-30 23:42:30','COMPLETADA','','2025-10-30 23:42:30','SISTEMA',NULL,'SISTEMA'),(35,3,8,1,54.00,'2025-10-30 23:48:32','COMPLETADA','','2025-10-30 23:48:32','SISTEMA',NULL,'SISTEMA'),(36,4,1,1,26.00,'2025-10-30 23:51:09','COMPLETADA','','2025-10-30 23:51:09','SISTEMA',NULL,'SISTEMA'),(37,7,8,1,37.20,'2025-10-31 01:35:14','COMPLETADA','','2025-10-31 01:35:14','SISTEMA',NULL,'SISTEMA'),(38,8,1,1,26.00,'2025-10-31 01:39:24','COMPLETADA','','2025-10-31 01:39:24','SISTEMA',NULL,'SISTEMA'),(39,11,11,1,370.00,'2025-11-01 08:58:16','COMPLETADA','','2025-11-01 08:58:16','SISTEMA',NULL,'SISTEMA'),(40,12,12,1,160.00,'2025-11-01 08:59:03','COMPLETADA','','2025-11-01 08:59:03','SISTEMA',NULL,'SISTEMA'),(41,13,13,1,353.00,'2025-11-01 08:59:41','COMPLETADA','','2025-11-01 08:59:41','SISTEMA',NULL,'SISTEMA'),(42,14,14,1,592.00,'2025-11-01 08:59:57','COMPLETADA','','2025-11-01 08:59:57','SISTEMA',NULL,'SISTEMA'),(43,9,9,1,150.00,'2025-11-04 00:37:45','COMPLETADA','','2025-11-04 00:37:45','SISTEMA',NULL,'SISTEMA'),(44,15,2,28,100.00,'2025-11-04 03:50:18','COMPLETADA','','2025-11-04 03:50:18','SISTEMA',NULL,'SISTEMA');
/*!40000 ALTER TABLE `tbl_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_detalle_compra`
--

DROP TABLE IF EXISTS `tbl_detalle_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_detalle_compra` (
  `ID_DETALLE_COMPRA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_COMPRA` int(11) NOT NULL,
  `ID_MATERIA_PRIMA` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `PRECIO_UNITARIO` decimal(10,2) NOT NULL,
  `SUBTOTAL` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE_COMPRA`),
  KEY `ID_COMPRA` (`ID_COMPRA`),
  KEY `ID_MATERIA_PRIMA` (`ID_MATERIA_PRIMA`),
  CONSTRAINT `tbl_detalle_compra_ibfk_1` FOREIGN KEY (`ID_COMPRA`) REFERENCES `tbl_compra` (`ID_COMPRA`),
  CONSTRAINT `tbl_detalle_compra_ibfk_2` FOREIGN KEY (`ID_MATERIA_PRIMA`) REFERENCES `tbl_materia_prima` (`ID_MATERIA_PRIMA`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_detalle_compra`
--

LOCK TABLES `tbl_detalle_compra` WRITE;
/*!40000 ALTER TABLE `tbl_detalle_compra` DISABLE KEYS */;
INSERT INTO `tbl_detalle_compra` VALUES (37,33,19,2.00,13.00,26.00,'2025-10-30 23:38:07','SISTEMA'),(38,34,20,5.00,11.80,59.00,'2025-10-30 23:42:30','SISTEMA'),(39,35,21,2.00,13.00,26.00,'2025-10-30 23:48:32','SISTEMA'),(40,35,22,5.00,5.60,28.00,'2025-10-30 23:48:32','SISTEMA'),(41,36,19,2.00,13.00,26.00,'2025-10-30 23:51:09','SISTEMA'),(42,37,21,2.00,13.00,26.00,'2025-10-31 01:35:14','SISTEMA'),(43,37,22,2.00,5.60,11.20,'2025-10-31 01:35:14','SISTEMA'),(44,38,19,2.00,13.00,26.00,'2025-10-31 01:39:24','SISTEMA'),(45,39,23,10.00,28.00,280.00,'2025-11-01 08:58:16','SISTEMA'),(46,39,24,2.00,45.00,90.00,'2025-11-01 08:58:16','SISTEMA'),(47,40,25,5.00,32.00,160.00,'2025-11-01 08:59:03','SISTEMA'),(48,41,26,3.00,65.00,195.00,'2025-11-01 08:59:41','SISTEMA'),(49,41,27,2.00,55.00,110.00,'2025-11-01 08:59:41','SISTEMA'),(50,41,28,1.00,48.00,48.00,'2025-11-01 08:59:41','SISTEMA'),(51,42,29,10.00,35.00,350.00,'2025-11-01 08:59:57','SISTEMA'),(52,42,30,2.00,28.00,56.00,'2025-11-01 08:59:57','SISTEMA'),(53,42,31,30.00,6.20,186.00,'2025-11-01 08:59:57','SISTEMA'),(54,43,32,5.00,15.00,75.00,'2025-11-04 00:37:45','SISTEMA'),(55,43,33,3.00,25.00,75.00,'2025-11-04 00:37:45','SISTEMA'),(56,44,34,2.00,32.00,64.00,'2025-11-04 03:50:18','SISTEMA'),(57,44,35,3.00,12.00,36.00,'2025-11-04 03:50:18','SISTEMA');
/*!40000 ALTER TABLE `tbl_detalle_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_detalle_factura`
--

DROP TABLE IF EXISTS `tbl_detalle_factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_detalle_factura` (
  `ID_DETALLE_FACTURA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_FACTURA` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `PRECIO_VENTA` decimal(10,2) NOT NULL,
  `SUBTOTAL` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE_FACTURA`),
  KEY `ID_FACTURA` (`ID_FACTURA`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_detalle_factura_ibfk_1` FOREIGN KEY (`ID_FACTURA`) REFERENCES `tbl_factura` (`ID_FACTURA`),
  CONSTRAINT `tbl_detalle_factura_ibfk_2` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_detalle_factura`
--

LOCK TABLES `tbl_detalle_factura` WRITE;
/*!40000 ALTER TABLE `tbl_detalle_factura` DISABLE KEYS */;
INSERT INTO `tbl_detalle_factura` VALUES (1,1,1,50.00,5.00,250.00,'2025-10-27 10:15:16','SISTEMA'),(2,1,2,20.00,7.00,140.00,'2025-10-27 10:15:16','SISTEMA'),(3,1,3,10.00,6.00,60.00,'2025-10-27 10:15:16','SISTEMA'),(4,2,2,30.00,7.00,210.00,'2025-10-27 10:15:16','SISTEMA'),(5,2,4,10.00,8.00,80.00,'2025-10-27 10:15:16','SISTEMA'),(6,2,3,5.00,6.00,30.00,'2025-10-27 10:15:16','SISTEMA'),(7,3,1,20.00,5.00,100.00,'2025-10-27 10:15:16','SISTEMA'),(8,3,3,10.00,6.00,60.00,'2025-10-27 10:15:16','SISTEMA'),(9,3,4,2.00,8.00,16.00,'2025-10-27 10:15:16','SISTEMA'),(10,4,2,50.00,7.00,350.00,'2025-10-27 10:15:16','SISTEMA'),(11,4,4,20.00,8.00,160.00,'2025-10-27 10:15:16','SISTEMA'),(12,4,1,10.00,5.00,50.00,'2025-10-27 10:15:16','SISTEMA'),(13,5,1,30.00,5.00,150.00,'2025-10-27 10:15:16','SISTEMA'),(14,5,3,10.00,6.00,60.00,'2025-10-27 10:15:16','SISTEMA'),(15,5,4,3.00,8.00,24.00,'2025-10-27 10:15:16','SISTEMA'),(16,6,2,80.00,7.00,560.00,'2025-10-27 10:15:16','SISTEMA'),(17,6,4,30.00,8.00,240.00,'2025-10-27 10:15:16','SISTEMA'),(18,6,1,15.00,5.00,75.00,'2025-10-27 10:15:16','SISTEMA'),(19,6,3,5.00,6.00,30.00,'2025-10-27 10:15:16','SISTEMA'),(20,7,1,40.00,5.00,200.00,'2025-10-27 10:15:16','SISTEMA'),(21,7,2,15.00,7.00,105.00,'2025-10-27 10:15:16','SISTEMA'),(22,7,3,10.00,6.00,60.00,'2025-10-27 10:15:16','SISTEMA'),(23,7,4,1.00,8.00,8.00,'2025-10-27 10:15:16','SISTEMA'),(24,8,2,40.00,7.00,280.00,'2025-10-27 10:15:16','SISTEMA'),(25,8,4,15.00,8.00,120.00,'2025-10-27 10:15:16','SISTEMA'),(26,8,1,4.00,5.00,20.00,'2025-10-27 10:15:16','SISTEMA'),(27,9,1,80.00,5.00,400.00,'2025-10-27 10:15:16','SISTEMA'),(28,9,2,30.00,7.00,210.00,'2025-10-27 10:15:16','SISTEMA'),(29,9,4,8.00,8.00,64.00,'2025-10-27 10:15:16','SISTEMA'),(30,10,2,25.00,7.00,175.00,'2025-10-27 10:15:16','SISTEMA'),(31,10,3,15.00,6.00,90.00,'2025-10-27 10:15:16','SISTEMA'),(32,10,4,3.00,8.00,24.00,'2025-10-27 10:15:16','SISTEMA');
/*!40000 ALTER TABLE `tbl_detalle_factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_detalle_mp_produccion`
--

DROP TABLE IF EXISTS `tbl_detalle_mp_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_detalle_mp_produccion` (
  `ID_DETALLE_MP` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCCION` int(11) NOT NULL,
  `ID_MATERIA_PRIMA` int(11) NOT NULL,
  `CANTIDAD_USADA` decimal(10,2) NOT NULL,
  `COSTO_UNITARIO` decimal(10,2) NOT NULL,
  `SUBTOTAL` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE_MP`),
  KEY `ID_PRODUCCION` (`ID_PRODUCCION`),
  KEY `ID_MATERIA_PRIMA` (`ID_MATERIA_PRIMA`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_detalle_mp_produccion`
--

LOCK TABLES `tbl_detalle_mp_produccion` WRITE;
/*!40000 ALTER TABLE `tbl_detalle_mp_produccion` DISABLE KEYS */;
INSERT INTO `tbl_detalle_mp_produccion` VALUES (1,5,23,1.60,28.00,44.80,'2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(2,5,24,0.20,45.00,9.00,'2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(3,5,25,0.60,32.00,19.20,'2025-11-02 08:31:46','Nuevo Usuario del Sistema'),(4,7,23,1.76,28.00,49.28,'2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(5,7,24,0.22,45.00,9.90,'2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(6,7,25,0.66,32.00,21.12,'2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA'),(7,6,23,0.16,28.00,4.48,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(8,6,24,0.02,45.00,0.90,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(9,6,25,0.06,32.00,1.92,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA');
/*!40000 ALTER TABLE `tbl_detalle_mp_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_detalle_produccion`
--

DROP TABLE IF EXISTS `tbl_detalle_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_detalle_produccion` (
  `ID_DETALLE_PRODUCCION` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCCION` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE_PRODUCCION`),
  KEY `ID_PRODUCCION` (`ID_PRODUCCION`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_detalle_produccion_ibfk_1` FOREIGN KEY (`ID_PRODUCCION`) REFERENCES `tbl_produccion` (`ID_PRODUCCION`),
  CONSTRAINT `tbl_detalle_produccion_ibfk_2` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_detalle_produccion`
--

LOCK TABLES `tbl_detalle_produccion` WRITE;
/*!40000 ALTER TABLE `tbl_detalle_produccion` DISABLE KEYS */;
INSERT INTO `tbl_detalle_produccion` VALUES (1,1,1,50.00,'2025-10-27 10:30:07','SISTEMA'),(2,2,2,35.00,'2025-10-27 10:30:07','SISTEMA'),(3,3,1,25.00,'2025-10-27 10:30:07','SISTEMA'),(4,3,2,20.00,'2025-10-27 10:30:07','SISTEMA'),(5,3,3,15.00,'2025-10-27 10:30:07','SISTEMA');
/*!40000 ALTER TABLE `tbl_detalle_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_detalle_recepcion`
--

DROP TABLE IF EXISTS `tbl_detalle_recepcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_detalle_recepcion` (
  `ID_DETALLE_RECEPCION` int(11) NOT NULL AUTO_INCREMENT,
  `ID_RECEPCION` int(11) NOT NULL,
  `ID_PROVEEDOR_PRODUCTO` int(11) DEFAULT NULL,
  `CANTIDAD` decimal(10,2) NOT NULL,
  `PRECIO_UNITARIO` decimal(10,2) NOT NULL,
  `SUBTOTAL` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_DETALLE_RECEPCION`),
  KEY `ID_RECEPCION` (`ID_RECEPCION`),
  KEY `ID_PROVEEDOR_PRODUCTO` (`ID_PROVEEDOR_PRODUCTO`),
  CONSTRAINT `tbl_detalle_recepcion_ibfk_1` FOREIGN KEY (`ID_RECEPCION`) REFERENCES `tbl_recepcion_compra` (`ID_RECEPCION`),
  CONSTRAINT `tbl_detalle_recepcion_ibfk_3` FOREIGN KEY (`ID_PROVEEDOR_PRODUCTO`) REFERENCES `tbl_proveedor_productos` (`ID_PROVEEDOR_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_detalle_recepcion`
--

LOCK TABLES `tbl_detalle_recepcion` WRITE;
/*!40000 ALTER TABLE `tbl_detalle_recepcion` DISABLE KEYS */;
INSERT INTO `tbl_detalle_recepcion` VALUES (1,1,2,2.00,13.00,26.00,'2025-10-30 21:54:30','SISTEMA'),(2,2,9,5.00,11.80,59.00,'2025-10-30 22:39:23','SISTEMA'),(3,3,11,2.00,13.00,26.00,'2025-10-30 22:48:47','SISTEMA'),(4,3,12,5.00,5.60,28.00,'2025-10-30 22:48:47','SISTEMA'),(5,4,2,2.00,13.00,26.00,'2025-10-30 23:47:00','SISTEMA'),(6,5,6,2.00,52.00,104.00,'2025-10-30 23:50:05','SISTEMA'),(7,6,7,2.00,13.00,26.00,'2025-10-31 00:17:46','SISTEMA'),(8,7,11,2.00,13.00,26.00,'2025-10-31 01:33:32','SISTEMA'),(9,7,12,2.00,5.60,11.20,'2025-10-31 01:33:32','SISTEMA'),(10,8,2,2.00,13.00,26.00,'2025-10-31 01:37:28','SISTEMA'),(11,9,13,5.00,15.00,75.00,'2025-10-31 18:44:42','SISTEMA'),(12,9,14,3.00,25.00,75.00,'2025-10-31 18:44:42','SISTEMA'),(13,10,2,5.00,13.00,65.00,'2025-10-31 18:46:57','SISTEMA'),(14,11,16,10.00,28.00,280.00,'2025-11-01 08:51:49','SISTEMA'),(15,11,18,2.00,45.00,90.00,'2025-11-01 08:51:49','SISTEMA'),(16,12,19,5.00,32.00,160.00,'2025-11-01 08:52:33','SISTEMA'),(17,13,23,3.00,65.00,195.00,'2025-11-01 08:55:17','SISTEMA'),(18,13,24,2.00,55.00,110.00,'2025-11-01 08:55:17','SISTEMA'),(19,13,25,1.00,48.00,48.00,'2025-11-01 08:55:17','SISTEMA'),(20,14,26,10.00,35.00,350.00,'2025-11-01 08:56:49','SISTEMA'),(21,14,29,2.00,28.00,56.00,'2025-11-01 08:56:49','SISTEMA'),(22,14,28,30.00,6.20,186.00,'2025-11-01 08:56:49','SISTEMA'),(23,15,33,2.00,32.00,64.00,'2025-11-04 00:37:24','SISTEMA'),(24,15,4,3.00,12.00,36.00,'2025-11-04 00:37:24','SISTEMA'),(25,16,19,5.00,32.00,160.00,'2025-11-04 03:49:23','SISTEMA'),(26,16,21,5.00,38.00,190.00,'2025-11-04 03:49:23','SISTEMA'),(27,16,22,5.00,42.00,210.00,'2025-11-04 03:49:23','SISTEMA'),(28,17,33,5.00,32.00,160.00,'2025-11-04 03:50:08','SISTEMA');
/*!40000 ALTER TABLE `tbl_detalle_recepcion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_estado_produccion`
--

DROP TABLE IF EXISTS `tbl_estado_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_estado_produccion` (
  `ID_ESTADO_PRODUCCION` int(11) NOT NULL AUTO_INCREMENT,
  `ESTADO` varchar(30) NOT NULL,
  `DESCRIPCION` varchar(100) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_ESTADO_PRODUCCION`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_estado_produccion`
--

LOCK TABLES `tbl_estado_produccion` WRITE;
/*!40000 ALTER TABLE `tbl_estado_produccion` DISABLE KEYS */;
INSERT INTO `tbl_estado_produccion` VALUES (1,'PLANIFICADO','Producción planificada pero no iniciada','2025-10-27 10:01:56','SISTEMA'),(2,'EN_PROCESO','Producción en ejecución','2025-10-27 10:01:56','SISTEMA'),(3,'FINALIZADO','Producción completada exitosamente','2025-10-27 10:01:56','SISTEMA'),(4,'CANCELADO','Producción cancelada','2025-10-27 10:01:56','SISTEMA'),(9,'SUSPENDIDO','Producción temporalmente suspendida','2025-11-01 06:11:13',NULL);
/*!40000 ALTER TABLE `tbl_estado_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_factura`
--

DROP TABLE IF EXISTS `tbl_factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_factura` (
  `ID_FACTURA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_CLIENTE` int(11) NOT NULL,
  `ID_METODO_PAGO` int(11) NOT NULL,
  `TOTAL_VENTA` decimal(10,2) NOT NULL,
  `FECHA_VENTA` datetime DEFAULT current_timestamp(),
  `ESTADO_FACTURA` varchar(20) DEFAULT 'ACTIVA',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_FACTURA`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  KEY `ID_CLIENTE` (`ID_CLIENTE`),
  KEY `ID_METODO_PAGO` (`ID_METODO_PAGO`),
  CONSTRAINT `tbl_factura_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`),
  CONSTRAINT `tbl_factura_ibfk_2` FOREIGN KEY (`ID_CLIENTE`) REFERENCES `tbl_cliente` (`ID_CLIENTE`),
  CONSTRAINT `tbl_factura_ibfk_3` FOREIGN KEY (`ID_METODO_PAGO`) REFERENCES `tbl_metodo_pago` (`ID_METODO_PAGO`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_factura`
--

LOCK TABLES `tbl_factura` WRITE;
/*!40000 ALTER TABLE `tbl_factura` DISABLE KEYS */;
INSERT INTO `tbl_factura` VALUES (1,1,1,1,450.00,'2024-10-03 10:30:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(2,1,2,2,320.00,'2024-10-04 11:15:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(3,1,3,1,180.00,'2024-10-05 09:45:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(4,1,1,3,560.00,'2024-10-07 14:20:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(5,1,4,1,240.00,'2024-10-08 16:30:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(6,1,2,2,890.00,'2024-10-09 10:00:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(7,1,3,1,375.00,'2024-10-11 12:45:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(8,1,4,3,420.00,'2024-10-12 15:10:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(9,1,1,1,680.00,'2024-10-13 11:30:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL),(10,1,2,2,295.00,'2024-10-14 13:25:00','ACTIVA','2025-10-27 10:15:16','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_inventario_materia_prima`
--

DROP TABLE IF EXISTS `tbl_inventario_materia_prima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_inventario_materia_prima` (
  `ID_INVENTARIO_MP` int(11) NOT NULL AUTO_INCREMENT,
  `ID_MATERIA_PRIMA` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) DEFAULT 0.00,
  `MINIMO` decimal(10,2) DEFAULT 0.00,
  `MAXIMO` decimal(10,2) DEFAULT 0.00,
  `FECHA_ACTUALIZACION` datetime DEFAULT current_timestamp(),
  `ACTUALIZADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_INVENTARIO_MP`),
  KEY `ID_MATERIA_PRIMA` (`ID_MATERIA_PRIMA`),
  CONSTRAINT `tbl_inventario_materia_prima_ibfk_1` FOREIGN KEY (`ID_MATERIA_PRIMA`) REFERENCES `tbl_materia_prima` (`ID_MATERIA_PRIMA`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_inventario_materia_prima`
--

LOCK TABLES `tbl_inventario_materia_prima` WRITE;
/*!40000 ALTER TABLE `tbl_inventario_materia_prima` DISABLE KEYS */;
INSERT INTO `tbl_inventario_materia_prima` VALUES (9,19,4.00,10.00,20.00,'2025-10-31 15:10:40','ADMIN'),(10,20,3.00,10.00,20.00,'2025-10-31 15:20:47','ADMIN'),(11,22,3.00,0.00,0.00,'2025-10-31 15:27:34','ADMIN'),(12,21,2.00,0.00,0.00,'2025-10-31 16:05:41','ADMIN'),(13,23,6.48,10.00,20.00,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(14,25,3.68,10.00,20.00,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(15,24,1.56,0.00,0.00,'2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA'),(16,29,5.00,10.00,20.00,'2025-11-04 03:52:33','ADMIN');
/*!40000 ALTER TABLE `tbl_inventario_materia_prima` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_inventario_producto`
--

DROP TABLE IF EXISTS `tbl_inventario_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_inventario_producto` (
  `ID_INVENTARIO_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCTO` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) DEFAULT 0.00,
  `MINIMO` decimal(10,2) DEFAULT 0.00,
  `MAXIMO` decimal(10,2) DEFAULT 0.00,
  `FECHA_ACTUALIZACION` datetime DEFAULT current_timestamp(),
  `ACTUALIZADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_INVENTARIO_PRODUCTO`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_inventario_producto_ibfk_1` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_inventario_producto`
--

LOCK TABLES `tbl_inventario_producto` WRITE;
/*!40000 ALTER TABLE `tbl_inventario_producto` DISABLE KEYS */;
INSERT INTO `tbl_inventario_producto` VALUES (1,1,10.00,50.00,200.00,'2025-11-04 10:32:11','ADMINISTRADOR DEL SISTEMA');
/*!40000 ALTER TABLE `tbl_inventario_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_materia_prima`
--

DROP TABLE IF EXISTS `tbl_materia_prima`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_materia_prima` (
  `ID_MATERIA_PRIMA` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `ID_UNIDAD_MEDIDA` int(11) NOT NULL,
  `CANTIDAD` decimal(10,2) DEFAULT 0.00,
  `MINIMO` decimal(10,2) DEFAULT 0.00,
  `MAXIMO` decimal(10,2) DEFAULT 0.00,
  `PRECIO_PROMEDIO` decimal(10,2) DEFAULT 0.00,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_MATERIA_PRIMA`),
  KEY `ID_UNIDAD_MEDIDA` (`ID_UNIDAD_MEDIDA`),
  CONSTRAINT `tbl_materia_prima_ibfk_1` FOREIGN KEY (`ID_UNIDAD_MEDIDA`) REFERENCES `tbl_unidad_medida` (`ID_UNIDAD_MEDIDA`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_materia_prima`
--

LOCK TABLES `tbl_materia_prima` WRITE;
/*!40000 ALTER TABLE `tbl_materia_prima` DISABLE KEYS */;
INSERT INTO `tbl_materia_prima` VALUES (19,'Azúcar refinada','Azúcar blanca refinada',5,2.00,10.00,20.00,13.00,'ACTIVO','2025-10-30 23:38:07','SISTEMA','2025-10-31 15:10:40','ADMIN'),(20,'Leche Láctea','Leche ultrapasteurizada',3,2.00,10.00,20.00,11.80,'ACTIVO','2025-10-30 23:42:30','SISTEMA','2025-10-31 15:20:47','ADMIN'),(21,'Harina de maíz','Harina para quesadillas',5,2.00,20.00,25.00,13.00,'ACTIVO','2025-10-30 23:48:32','SISTEMA','2025-10-31 23:34:27','SISTEMA'),(22,'Huevos blancos','Huevos grade A',4,4.00,30.00,50.00,5.60,'ACTIVO','2025-10-30 23:48:32','SISTEMA','2025-11-01 09:04:12','SISTEMA'),(23,'Harina para repostería','Harina especial para donas y rosquillas',1,0.00,10.00,20.00,28.00,'ACTIVO','2025-11-01 08:58:16','SISTEMA','2025-11-01 09:07:19','ADMIN'),(24,'Polvo de hornear','Polvo para hornear de alta calidad',1,0.00,0.00,0.00,45.00,'ACTIVO','2025-11-01 08:58:16','SISTEMA','2025-11-01 09:08:27','ADMIN'),(25,'Azúcar glass','Azúcar pulverizada para glaseados',1,0.00,10.00,20.00,32.00,'ACTIVO','2025-11-01 08:59:03','SISTEMA','2025-11-01 09:07:54','ADMIN'),(26,'Chocolate para cobertura','&#039;Chocolate semiamargo para bañar',1,3.00,0.00,0.00,65.00,'ACTIVO','2025-11-01 08:59:41','SISTEMA',NULL,NULL),(27,'Cacao en polvo','Cacao 100% natural',1,2.00,10.00,19.99,55.00,'ACTIVO','2025-11-01 08:59:41','SISTEMA','2025-11-01 09:02:05','SISTEMA'),(28,'Chispas de chocolate','Chips de chocolate semidulce',1,1.00,10.00,19.99,48.00,'ACTIVO','2025-11-01 08:59:41','SISTEMA','2025-11-01 09:03:07','SISTEMA'),(29,'Aceite vegetal','Aceite para freír rosquillas',3,5.00,15.00,20.00,35.00,'ACTIVO','2025-11-01 08:59:57','SISTEMA','2025-11-04 03:53:01','SISTEMA'),(30,'Leche evaporada','Leche evaporada completa',3,2.00,0.00,0.00,28.00,'ACTIVO','2025-11-01 08:59:57','SISTEMA',NULL,NULL),(31,'Huevos extra grandes','Huevos grade AA',4,30.00,10.00,20.00,6.20,'ACTIVO','2025-11-01 08:59:57','SISTEMA','2025-11-01 09:04:36','SISTEMA'),(32,'Levadura instantánea','Levadura en polvo',2,5.00,0.00,0.00,15.00,'ACTIVO','2025-11-04 00:37:45','SISTEMA',NULL,NULL),(33,'Colorante alimenticio','Colorante rojo',3,3.00,0.00,0.00,25.00,'ACTIVO','2025-11-04 00:37:45','SISTEMA',NULL,NULL),(34,'Mantequilla crema','Mantequilla Olancho',5,2.00,0.00,0.00,32.00,'ACTIVO','2025-11-04 03:50:18','SISTEMA',NULL,NULL),(35,'Leche entera','Leche pasteurizada',3,3.00,0.00,0.00,12.00,'ACTIVO','2025-11-04 03:50:18','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_materia_prima` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_metodo_pago`
--

DROP TABLE IF EXISTS `tbl_metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_metodo_pago` (
  `ID_METODO_PAGO` int(11) NOT NULL AUTO_INCREMENT,
  `METODO_PAGO` varchar(30) NOT NULL,
  `DESCRIPCION` varchar(100) DEFAULT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_METODO_PAGO`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_metodo_pago`
--

LOCK TABLES `tbl_metodo_pago` WRITE;
/*!40000 ALTER TABLE `tbl_metodo_pago` DISABLE KEYS */;
INSERT INTO `tbl_metodo_pago` VALUES (1,'EFECTIVO','Pago en efectivo','ACTIVO','2025-10-27 10:01:56','SISTEMA'),(2,'TARJETA','Pago con tarjeta','ACTIVO','2025-10-27 10:01:56','SISTEMA'),(3,'TRANSFERENCIA','Transferencia bancaria','ACTIVO','2025-10-27 10:01:56','SISTEMA');
/*!40000 ALTER TABLE `tbl_metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_backups`
--

DROP TABLE IF EXISTS `tbl_ms_backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_backups` (
  `ID_BACKUP` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_ARCHIVO` varchar(255) NOT NULL,
  `TIPO_RESPALDO` enum('AUTOMATICO','MANUAL') NOT NULL DEFAULT 'MANUAL',
  `RUTA_ARCHIVO` varchar(500) NOT NULL,
  `FECHA_BACKUP` datetime DEFAULT current_timestamp(),
  `PROGRAMADO_CADA_DIAS` int(11) DEFAULT NULL COMMENT 'Cada cuántos días se ejecuta (si es automático)',
  `HORA_PROGRAMADA` time DEFAULT NULL,
  `DIA_SEMANA` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') DEFAULT NULL,
  `ESTADO` enum('PENDIENTE','EJECUTADO','ERROR') DEFAULT 'EJECUTADO',
  `DETALLE` text DEFAULT NULL,
  `CREADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_BACKUP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_backups`
--

LOCK TABLES `tbl_ms_backups` WRITE;
/*!40000 ALTER TABLE `tbl_ms_backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_ms_backups` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `TRG_BI_VALIDAR_BACKUP` BEFORE INSERT ON `tbl_ms_backups` FOR EACH ROW BEGIN
    DECLARE V_COUNT INT;
    IF NEW.TIPO_RESPALDO = 'MANUAL' THEN
        SELECT COUNT(*) INTO V_COUNT FROM TBL_MS_BACKUPS
        WHERE NOMBRE_ARCHIVO = NEW.NOMBRE_ARCHIVO AND DATE(FECHA_BACKUP) = CURDATE();
        IF V_COUNT > 0 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Ya existe un backup manual con este nombre hoy';
        END IF;
    END IF;
    IF NEW.RUTA_ARCHIVO IS NULL OR LENGTH(TRIM(NEW.RUTA_ARCHIVO)) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La ruta del archivo de backup es requerida';
    END IF;
    IF NEW.FECHA_BACKUP IS NULL THEN
        SET NEW.FECHA_BACKUP = NOW();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `TRG_AI_BITACORA_BACKUP` AFTER INSERT ON `tbl_ms_backups` FOR EACH ROW BEGIN
    IF NEW.TIPO_RESPALDO = 'MANUAL' AND NEW.ESTADO = 'EJECUTADO' THEN
        INSERT INTO TBL_MS_BITACORA (ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR)
        VALUES (
            COALESCE((SELECT ID_USUARIO FROM TBL_MS_USUARIOS WHERE USUARIO = NEW.CREADO_POR LIMIT 1), 1),
            (SELECT ID_OBJETO FROM TBL_MS_OBJETOS WHERE OBJETO = 'RESPALDOS_SISTEMA' LIMIT 1),
            'BACKUP_MANUAL_COMPLETADO',
            CONCAT('Backup manual completado: ', NEW.NOMBRE_ARCHIVO, ' - ', NEW.RUTA_ARCHIVO),
            NEW.CREADO_POR
        );
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `TRG_BU_ACTUALIZAR_ESTADO_BACKUP` BEFORE UPDATE ON `tbl_ms_backups` FOR EACH ROW BEGIN
    IF OLD.ESTADO != 'ERROR' AND NEW.ESTADO = 'ERROR' THEN
        INSERT INTO TBL_MS_BITACORA (ID_USUARIO, ID_OBJETO, ACCION, DESCRIPCION, CREADO_POR)
        VALUES (
            COALESCE((SELECT ID_USUARIO FROM TBL_MS_USUARIOS WHERE USUARIO = NEW.CREADO_POR LIMIT 1), 1),
            (SELECT ID_OBJETO FROM TBL_MS_OBJETOS WHERE OBJETO = 'RESPALDOS_SISTEMA' LIMIT 1),
            'BACKUP_ERROR',
            CONCAT('Error en backup: ', NEW.NOMBRE_ARCHIVO, ' - ', NEW.DETALLE),
            NEW.CREADO_POR
        );
    END IF;
    IF OLD.ESTADO = 'EJECUTADO' AND NEW.ESTADO != 'EJECUTADO' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede modificar un backup ya ejecutado';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tbl_ms_backups_auto`
--

DROP TABLE IF EXISTS `tbl_ms_backups_auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_backups_auto` (
  `ID_BACKUP_AUTO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE_ARCHIVO` varchar(255) NOT NULL,
  `RUTA_ARCHIVO` varchar(500) NOT NULL,
  `FRECUENCIA` enum('DIARIO','SEMANAL','MENSUAL') NOT NULL,
  `HORA_EJECUCION` time NOT NULL,
  `DIAS_SEMANA` varchar(50) DEFAULT NULL,
  `ACTIVO` tinyint(4) DEFAULT 1,
  `CREADO_POR` varchar(100) DEFAULT 'SISTEMA',
  `CREADO_EN` timestamp NOT NULL DEFAULT current_timestamp(),
  `ACTUALIZADO_EN` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID_BACKUP_AUTO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_backups_auto`
--

LOCK TABLES `tbl_ms_backups_auto` WRITE;
/*!40000 ALTER TABLE `tbl_ms_backups_auto` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_ms_backups_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_bitacora`
--

DROP TABLE IF EXISTS `tbl_ms_bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_bitacora` (
  `ID_BITACORA` int(11) NOT NULL AUTO_INCREMENT,
  `FECHA` datetime NOT NULL DEFAULT current_timestamp(),
  `ID_USUARIO` int(11) NOT NULL,
  `ID_OBJETO` int(11) DEFAULT NULL,
  `ACCION` varchar(20) NOT NULL,
  `DESCRIPCION` varchar(100) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_BITACORA`),
  KEY `TBL_MS_BITACORA_IBFK_1` (`ID_USUARIO`),
  KEY `TBL_MS_BITACORA_IBFK_2` (`ID_OBJETO`),
  CONSTRAINT `TBL_MS_BITACORA_IBFK_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`),
  CONSTRAINT `TBL_MS_BITACORA_IBFK_2` FOREIGN KEY (`ID_OBJETO`) REFERENCES `tbl_ms_objetos` (`ID_OBJETO`)
) ENGINE=InnoDB AUTO_INCREMENT=1104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_bitacora`
--

LOCK TABLES `tbl_ms_bitacora` WRITE;
/*!40000 ALTER TABLE `tbl_ms_bitacora` DISABLE KEYS */;
INSERT INTO `tbl_ms_bitacora` VALUES (1,'2025-10-15 19:35:43',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-15 19:35:43','SISTEMA',NULL,NULL),(2,'2025-10-15 19:45:23',1,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-15 19:45:23','SISTEMA',NULL,NULL),(3,'2025-10-15 19:48:10',3,NULL,'CREAR_USUARIO','Usuario creado: DENIS','2025-10-15 19:48:10','ADMIN',NULL,NULL),(4,'2025-10-15 19:49:43',2,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-15 19:49:43','SISTEMA',NULL,NULL),(5,'2025-10-15 19:51:39',2,NULL,'CONFIGURACION_PREGUN','Preguntas de seguridad configuradas','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(6,'2025-10-15 19:51:39',2,NULL,'RESETEAR_CONTRASENA','Contraseña reseteada por administrador','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(7,'2025-10-15 19:54:22',3,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-15 19:54:22','SISTEMA',NULL,NULL),(8,'2025-10-15 20:37:08',2,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-15 20:37:08','SISTEMA',NULL,NULL),(9,'2025-10-15 21:56:20',2,NULL,'VALIDACION_PREGUNTAS','Validación de preguntas de seguridad exitosa','2025-10-15 21:56:20','SISTEMA_RECUPERACION',NULL,NULL),(10,'2025-10-15 21:57:02',2,NULL,'CAMBIO_CONTRASENA_RE','Contraseña cambiada exitosamente mediante recuperación','2025-10-15 21:57:02','SISTEMA_RECUPERACION',NULL,NULL),(11,'2025-10-15 23:45:04',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-16 23:45:04','2025-10-15 23:45:04','SISTEMA_RECUPERACION',NULL,NULL),(12,'2025-10-15 23:48:32',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-16 23:48:32','2025-10-15 23:48:32','SISTEMA_RECUPERACION',NULL,NULL),(13,'2025-10-15 23:48:32',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-15 23:48:32','SISTEMA',NULL,NULL),(14,'2025-10-15 23:51:05',3,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-15 23:51:05','SISTEMA',NULL,NULL),(15,'2025-10-16 01:17:54',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 01:17:54','2025-10-16 01:17:54','SISTEMA_RECUPERACION',NULL,NULL),(16,'2025-10-16 01:17:54',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 01:17:54','SISTEMA',NULL,NULL),(17,'2025-10-16 01:19:52',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 01:19:52','2025-10-16 01:19:52','SISTEMA_RECUPERACION',NULL,NULL),(18,'2025-10-16 01:19:52',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 01:19:52','SISTEMA',NULL,NULL),(19,'2025-10-16 01:20:08',3,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:20:08','SISTEMA',NULL,NULL),(20,'2025-10-16 01:22:47',4,NULL,'CREAR_USUARIO','Usuario creado: CARLOS','2025-10-16 01:22:47','ADMIN',NULL,NULL),(21,'2025-10-16 01:23:13',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:23:13','SISTEMA',NULL,NULL),(22,'2025-10-16 01:24:58',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:24:58','SISTEMA',NULL,NULL),(23,'2025-10-16 01:28:28',4,NULL,'CONFIGURACION_PREGUN','Preguntas de seguridad configuradas','2025-10-16 01:28:28','SISTEMA',NULL,NULL),(24,'2025-10-16 01:28:28',4,NULL,'RESETEAR_CONTRASENA','Contraseña reseteada por administrador','2025-10-16 01:28:28','SISTEMA',NULL,NULL),(25,'2025-10-16 01:29:27',4,NULL,'VALIDACION_PREGUNTAS','Validación de preguntas de seguridad exitosa','2025-10-16 01:29:27','SISTEMA_RECUPERACION',NULL,NULL),(26,'2025-10-16 01:29:59',4,NULL,'CAMBIO_CONTRASENA_RE','Contraseña cambiada exitosamente mediante recuperación','2025-10-16 01:29:59','SISTEMA_RECUPERACION',NULL,NULL),(27,'2025-10-16 01:30:10',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:30:10','SISTEMA',NULL,NULL),(28,'2025-10-16 01:30:44',4,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-16 01:30:44','SISTEMA',NULL,NULL),(29,'2025-10-16 01:31:17',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:31:17','SISTEMA',NULL,NULL),(30,'2025-10-16 01:31:28',4,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 01:31:28','2025-10-16 01:31:28','SISTEMA_RECUPERACION',NULL,NULL),(31,'2025-10-16 01:31:28',4,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: moya@gmail.com','2025-10-16 01:31:28','SISTEMA',NULL,NULL),(32,'2025-10-16 01:31:43',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:31:43','SISTEMA',NULL,NULL),(33,'2025-10-16 01:32:43',4,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-16 01:32:43','SISTEMA',NULL,NULL),(34,'2025-10-16 01:50:00',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 01:50:00','SISTEMA',NULL,NULL),(35,'2025-10-16 02:18:48',5,NULL,'CREAR_USUARIO','Usuario creado: WALESKA','2025-10-16 02:18:48','ADMIN',NULL,NULL),(36,'2025-10-16 02:39:09',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 02:39:09','SISTEMA',NULL,NULL),(37,'2025-10-16 17:55:14',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 17:55:14','SISTEMA',NULL,NULL),(38,'2025-10-16 17:55:51',1,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-16 17:55:51','SISTEMA',NULL,NULL),(39,'2025-10-16 17:56:49',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 17:56:49','2025-10-16 17:56:49','SISTEMA_RECUPERACION',NULL,NULL),(40,'2025-10-16 17:56:49',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 17:56:49','SISTEMA',NULL,NULL),(41,'2025-10-16 17:58:37',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 17:58:37','SISTEMA',NULL,NULL),(42,'2025-10-16 17:59:42',6,NULL,'CREAR_USUARIO','Usuario creado: PEDRO','2025-10-16 17:59:42','ADMIN',NULL,NULL),(43,'2025-10-16 21:09:34',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:09:34','2025-10-16 21:09:34','SISTEMA_RECUPERACION',NULL,NULL),(44,'2025-10-16 21:15:27',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:15:27','2025-10-16 21:15:27','SISTEMA_RECUPERACION',NULL,NULL),(45,'2025-10-16 21:15:30',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 21:15:30','SISTEMA',NULL,NULL),(46,'2025-10-16 21:20:41',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:20:41','2025-10-16 21:20:41','SISTEMA_RECUPERACION',NULL,NULL),(47,'2025-10-16 21:20:41',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 21:20:41','SISTEMA',NULL,NULL),(48,'2025-10-16 21:22:19',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:22:19','2025-10-16 21:22:19','SISTEMA_RECUPERACION',NULL,NULL),(49,'2025-10-16 21:22:19',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 21:22:19','SISTEMA',NULL,NULL),(50,'2025-10-16 21:22:31',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:22:31','2025-10-16 21:22:31','SISTEMA_RECUPERACION',NULL,NULL),(51,'2025-10-16 21:22:31',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-16 21:22:31','SISTEMA',NULL,NULL),(52,'2025-10-16 21:35:58',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:35:58','2025-10-16 21:35:58','SISTEMA_RECUPERACION',NULL,NULL),(53,'2025-10-16 21:35:58',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal generada para: denislopez1206@gmail.com','2025-10-16 21:35:58','SISTEMA',NULL,NULL),(54,'2025-10-16 21:37:06',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:37:06','2025-10-16 21:37:06','SISTEMA_RECUPERACION',NULL,NULL),(55,'2025-10-16 21:37:06',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal generada para: denislopez1206@gmail.com','2025-10-16 21:37:06','SISTEMA',NULL,NULL),(56,'2025-10-16 21:44:37',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:44:37','2025-10-16 21:44:37','SISTEMA_RECUPERACION',NULL,NULL),(57,'2025-10-16 21:44:37',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal generada para: denislopez1206@gmail.com','2025-10-16 21:44:37','SISTEMA',NULL,NULL),(58,'2025-10-16 21:50:47',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 21:50:47','2025-10-16 21:50:47','SISTEMA_RECUPERACION',NULL,NULL),(59,'2025-10-16 21:50:47',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal generada para: denislopez1206@gmail.com','2025-10-16 21:50:47','SISTEMA',NULL,NULL),(60,'2025-10-16 22:24:53',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 22:24:53','2025-10-16 22:24:53','SISTEMA_RECUPERACION',NULL,NULL),(61,'2025-10-16 22:24:55',3,NULL,'RECUPERACION_CORREO_','Error al enviar correo a: denislopez1206@gmail.com','2025-10-16 22:24:55','SISTEMA',NULL,NULL),(62,'2025-10-16 22:32:53',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 22:32:53','2025-10-16 22:32:53','SISTEMA_RECUPERACION',NULL,NULL),(63,'2025-10-16 22:32:55',3,NULL,'RECUPERACION_CORREO_','Error al enviar correo a: denislopez1206@gmail.com','2025-10-16 22:32:55','SISTEMA',NULL,NULL),(64,'2025-10-16 22:48:23',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 22:48:23','2025-10-16 22:48:23','SISTEMA_RECUPERACION',NULL,NULL),(65,'2025-10-16 22:48:25',3,NULL,'RECUPERACION_CORREO_','Error al enviar correo a: denislopez1206@gmail.com','2025-10-16 22:48:25','SISTEMA',NULL,NULL),(66,'2025-10-16 23:10:44',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 23:10:44','2025-10-16 23:10:44','SISTEMA_RECUPERACION',NULL,NULL),(67,'2025-10-16 23:10:45',3,NULL,'RECUPERACION_CORREO_','Error al enviar correo a: denislopez1206@gmail.com','2025-10-16 23:10:45','SISTEMA',NULL,NULL),(68,'2025-10-16 23:29:01',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 23:29:01','2025-10-16 23:29:01','SISTEMA_RECUPERACION',NULL,NULL),(69,'2025-10-16 23:29:02',3,NULL,'RECUPERACION_CORREO_','Error al enviar correo a: denislopez1206@gmail.com','2025-10-16 23:29:02','SISTEMA',NULL,NULL),(70,'2025-10-16 23:36:35',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 23:36:35','2025-10-16 23:36:35','SISTEMA_RECUPERACION',NULL,NULL),(71,'2025-10-16 23:36:37',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-16 23:36:37','SISTEMA',NULL,NULL),(72,'2025-10-16 23:38:00',3,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-16 23:38:00','SISTEMA',NULL,NULL),(73,'2025-10-16 23:50:34',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-17 23:50:34','2025-10-16 23:50:34','SISTEMA_RECUPERACION',NULL,NULL),(74,'2025-10-16 23:50:37',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-16 23:50:37','SISTEMA',NULL,NULL),(75,'2025-10-17 00:00:10',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:00:10','2025-10-17 00:00:10','SISTEMA_RECUPERACION',NULL,NULL),(76,'2025-10-17 00:00:12',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 00:00:12','SISTEMA',NULL,NULL),(77,'2025-10-17 00:20:09',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:20:09','2025-10-17 00:20:09','SISTEMA_RECUPERACION',NULL,NULL),(78,'2025-10-17 00:31:10',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:31:10','2025-10-17 00:31:10','SISTEMA_RECUPERACION',NULL,NULL),(79,'2025-10-17 00:34:45',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:34:45','2025-10-17 00:34:45','SISTEMA_RECUPERACION',NULL,NULL),(80,'2025-10-17 00:48:29',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:48:29','2025-10-17 00:48:29','SISTEMA_RECUPERACION',NULL,NULL),(81,'2025-10-17 00:50:34',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:50:34','2025-10-17 00:50:34','SISTEMA_RECUPERACION',NULL,NULL),(82,'2025-10-17 00:59:43',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 00:59:43','2025-10-17 00:59:43','SISTEMA',NULL,NULL),(83,'2025-10-17 01:12:03',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 01:12:03','2025-10-17 01:12:03','SISTEMA_RECUPERACION',NULL,NULL),(84,'2025-10-17 01:14:24',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 01:14:24','SISTEMA',NULL,NULL),(85,'2025-10-17 01:26:48',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 01:26:48','2025-10-17 01:26:48','SISTEMA_RECUPERACION',NULL,NULL),(86,'2025-10-17 01:26:51',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 01:26:51','SISTEMA',NULL,NULL),(87,'2025-10-17 01:38:29',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 01:38:29','SISTEMA',NULL,NULL),(88,'2025-10-17 01:38:51',1,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-17 01:38:51','SISTEMA',NULL,NULL),(89,'2025-10-17 01:48:04',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 01:48:04','2025-10-17 01:48:04','SISTEMA_RECUPERACION',NULL,NULL),(90,'2025-10-17 01:48:07',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 01:48:07','SISTEMA',NULL,NULL),(91,'2025-10-17 01:48:07',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 01:48:07','SISTEMA',NULL,NULL),(92,'2025-10-17 02:05:42',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 02:05:42','2025-10-17 02:05:42','SISTEMA_RECUPERACION',NULL,NULL),(93,'2025-10-17 02:05:45',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 02:05:45','SISTEMA',NULL,NULL),(94,'2025-10-17 02:05:45',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 02:05:45','SISTEMA',NULL,NULL),(95,'2025-10-17 03:42:07',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 03:42:07','2025-10-17 03:42:07','SISTEMA_RECUPERACION',NULL,NULL),(96,'2025-10-17 03:42:11',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 03:42:11','SISTEMA',NULL,NULL),(97,'2025-10-17 03:42:11',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 03:42:11','SISTEMA',NULL,NULL),(98,'2025-10-17 18:21:20',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 18:21:20','2025-10-17 18:21:20','SISTEMA_RECUPERACION',NULL,NULL),(99,'2025-10-17 18:21:22',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 18:21:22','SISTEMA',NULL,NULL),(100,'2025-10-17 18:21:22',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 18:21:22','SISTEMA',NULL,NULL),(101,'2025-10-17 19:21:14',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 19:21:14','2025-10-17 19:21:14','SISTEMA_RECUPERACION',NULL,NULL),(102,'2025-10-17 19:21:16',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 19:21:16','SISTEMA',NULL,NULL),(103,'2025-10-17 19:21:16',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 19:21:16','SISTEMA',NULL,NULL),(104,'2025-10-17 19:24:17',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 19:24:17','SISTEMA',NULL,NULL),(105,'2025-10-17 19:25:29',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 19:25:29','2025-10-17 19:25:29','SISTEMA_RECUPERACION',NULL,NULL),(106,'2025-10-17 19:25:31',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 19:25:31','SISTEMA',NULL,NULL),(107,'2025-10-17 19:25:31',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 19:25:31','SISTEMA',NULL,NULL),(108,'2025-10-17 19:52:32',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 19:52:32','2025-10-17 19:52:32','SISTEMA_RECUPERACION',NULL,NULL),(109,'2025-10-17 19:52:35',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-17 19:52:35','SISTEMA',NULL,NULL),(110,'2025-10-17 19:52:35',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-17 19:52:35','SISTEMA',NULL,NULL),(111,'2025-10-17 20:29:32',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 20:29:32','SISTEMA',NULL,NULL),(112,'2025-10-17 20:29:33',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 20:29:33','SISTEMA',NULL,NULL),(113,'2025-10-17 22:57:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 22:57:50','SISTEMA',NULL,NULL),(114,'2025-10-17 22:58:34',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 22:58:34','SISTEMA',NULL,NULL),(115,'2025-10-17 22:58:34',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 22:58:34','SISTEMA',NULL,NULL),(116,'2025-10-17 22:58:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 22:58:44','SISTEMA',NULL,NULL),(117,'2025-10-17 22:58:44',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 22:58:44','SISTEMA',NULL,NULL),(118,'2025-10-17 23:00:32',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:00:32','SISTEMA',NULL,NULL),(119,'2025-10-17 23:01:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:01:21','SISTEMA',NULL,NULL),(120,'2025-10-17 23:01:21',6,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 23:01:21','SISTEMA',NULL,NULL),(121,'2025-10-17 23:01:24',6,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-17 23:01:24','SISTEMA',NULL,NULL),(122,'2025-10-17 23:02:09',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:02:09','SISTEMA',NULL,NULL),(123,'2025-10-17 23:02:09',6,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-17 23:02:09','SISTEMA',NULL,NULL),(124,'2025-10-17 23:02:11',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:02:11','SISTEMA',NULL,NULL),(125,'2025-10-17 23:07:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:07:58','SISTEMA',NULL,NULL),(126,'2025-10-17 23:07:58',6,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 23:07:58','SISTEMA',NULL,NULL),(127,'2025-10-17 23:08:01',6,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-17 23:08:01','SISTEMA',NULL,NULL),(128,'2025-10-17 23:08:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:08:21','SISTEMA',NULL,NULL),(129,'2025-10-17 23:08:23',6,NULL,'2FA_REENVIADO','Código 2FA reenviado al correo','2025-10-17 23:08:23','SISTEMA',NULL,NULL),(130,'2025-10-17 23:08:47',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:08:47','SISTEMA',NULL,NULL),(131,'2025-10-17 23:08:47',6,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-17 23:08:47','SISTEMA',NULL,NULL),(132,'2025-10-17 23:09:55',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:09:55','SISTEMA',NULL,NULL),(133,'2025-10-17 23:09:55',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-18 23:09:55','2025-10-17 23:09:55','SISTEMA_RECUPERACION',NULL,NULL),(134,'2025-10-17 23:09:57',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-17 23:09:57','SISTEMA',NULL,NULL),(135,'2025-10-17 23:09:57',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-17 23:09:57','SISTEMA',NULL,NULL),(136,'2025-10-17 23:15:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:15:50','SISTEMA',NULL,NULL),(137,'2025-10-17 23:15:50',6,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-17 23:15:50','SISTEMA',NULL,NULL),(138,'2025-10-17 23:15:53',6,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-17 23:15:53','SISTEMA',NULL,NULL),(139,'2025-10-17 23:16:22',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-17 23:16:22','SISTEMA',NULL,NULL),(140,'2025-10-17 23:16:22',6,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-17 23:16:22','SISTEMA',NULL,NULL),(141,'2025-10-17 23:22:15',7,NULL,'CREAR_USUARIO','Usuario creado: ELMER','2025-10-17 23:22:15','ADMIN',NULL,NULL),(142,'2025-10-18 00:37:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:37:08','SISTEMA',NULL,NULL),(143,'2025-10-18 00:37:08',6,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-18 00:37:08','SISTEMA',NULL,NULL),(144,'2025-10-18 00:37:11',6,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-18 00:37:11','SISTEMA',NULL,NULL),(145,'2025-10-18 00:37:40',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:37:40','SISTEMA',NULL,NULL),(146,'2025-10-18 00:37:40',6,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-18 00:37:40','SISTEMA',NULL,NULL),(147,'2025-10-18 00:38:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:38:21','SISTEMA',NULL,NULL),(148,'2025-10-18 00:38:40',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:38:40','SISTEMA',NULL,NULL),(149,'2025-10-18 00:38:40',2,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 00:38:40','2025-10-18 00:38:40','SISTEMA_RECUPERACION',NULL,NULL),(150,'2025-10-18 00:38:43',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-18 00:38:43','SISTEMA',NULL,NULL),(151,'2025-10-18 00:38:43',2,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-18 00:38:43','SISTEMA',NULL,NULL),(152,'2025-10-18 00:39:29',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:39:29','SISTEMA',NULL,NULL),(153,'2025-10-18 00:40:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:40:00','SISTEMA',NULL,NULL),(154,'2025-10-18 00:40:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:40:08','SISTEMA',NULL,NULL),(155,'2025-10-18 00:40:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:40:53','SISTEMA',NULL,NULL),(156,'2025-10-18 00:40:54',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-18 00:40:54','SISTEMA',NULL,NULL),(157,'2025-10-18 00:41:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:41:21','SISTEMA',NULL,NULL),(158,'2025-10-18 00:41:39',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:41:39','SISTEMA',NULL,NULL),(159,'2025-10-18 00:42:33',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:42:33','SISTEMA',NULL,NULL),(160,'2025-10-18 00:42:33',4,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-18 00:42:33','SISTEMA',NULL,NULL),(161,'2025-10-18 00:42:36',4,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-18 00:42:36','SISTEMA',NULL,NULL),(162,'2025-10-18 00:52:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:52:19','SISTEMA',NULL,NULL),(163,'2025-10-18 00:52:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:52:51','SISTEMA',NULL,NULL),(164,'2025-10-18 00:52:51',3,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 00:52:51','2025-10-18 00:52:51','SISTEMA_RECUPERACION',NULL,NULL),(165,'2025-10-18 00:52:53',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-18 00:52:53','SISTEMA',NULL,NULL),(166,'2025-10-18 00:52:53',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-18 00:52:53','SISTEMA',NULL,NULL),(167,'2025-10-18 00:53:27',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:53:27','SISTEMA',NULL,NULL),(168,'2025-10-18 00:53:27',3,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-18 00:53:27','SISTEMA',NULL,NULL),(169,'2025-10-18 00:53:30',3,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-18 00:53:30','SISTEMA',NULL,NULL),(170,'2025-10-18 00:54:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:54:02','SISTEMA',NULL,NULL),(171,'2025-10-18 00:54:03',3,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-18 00:54:03','SISTEMA',NULL,NULL),(172,'2025-10-18 00:55:08',8,NULL,'CREAR_USUARIO','Usuario creado: DEDE','2025-10-18 00:55:08','ADMIN',NULL,NULL),(173,'2025-10-18 00:57:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:57:58','SISTEMA',NULL,NULL),(174,'2025-10-18 00:57:58',8,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-18 00:57:58','SISTEMA',NULL,NULL),(175,'2025-10-18 00:58:01',8,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-18 00:58:01','SISTEMA',NULL,NULL),(176,'2025-10-18 00:58:22',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:58:22','SISTEMA',NULL,NULL),(177,'2025-10-18 00:58:24',8,NULL,'2FA_REENVIADO','Código 2FA reenviado al correo','2025-10-18 00:58:24','SISTEMA',NULL,NULL),(178,'2025-10-18 00:58:47',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 00:58:47','SISTEMA',NULL,NULL),(179,'2025-10-18 00:58:47',8,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-18 00:58:47','SISTEMA',NULL,NULL),(180,'2025-10-18 01:00:57',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:00:57','SISTEMA',NULL,NULL),(181,'2025-10-18 01:01:18',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:01:18','SISTEMA',NULL,NULL),(182,'2025-10-18 01:20:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:20:58','SISTEMA',NULL,NULL),(183,'2025-10-18 01:20:58',4,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 01:20:58','2025-10-18 01:20:58','SISTEMA_RECUPERACION',NULL,NULL),(184,'2025-10-18 01:21:01',4,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: luisjosen306@gmai.com','2025-10-18 01:21:01','SISTEMA',NULL,NULL),(185,'2025-10-18 01:21:01',4,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: luisjosen306@gmai.com','2025-10-18 01:21:01','SISTEMA',NULL,NULL),(186,'2025-10-18 01:32:28',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:32:28','SISTEMA',NULL,NULL),(187,'2025-10-18 01:32:29',8,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 01:32:29','2025-10-18 01:32:29','SISTEMA_RECUPERACION',NULL,NULL),(188,'2025-10-18 01:32:31',8,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: pjreyess@unah.hn','2025-10-18 01:32:31','SISTEMA',NULL,NULL),(189,'2025-10-18 01:32:31',8,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: pjreyess@unah.hn','2025-10-18 01:32:31','SISTEMA',NULL,NULL),(190,'2025-10-18 01:35:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:35:24','SISTEMA',NULL,NULL),(191,'2025-10-18 01:36:07',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:36:07','SISTEMA',NULL,NULL),(192,'2025-10-18 01:36:07',7,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 01:36:07','2025-10-18 01:36:07','SISTEMA_RECUPERACION',NULL,NULL),(193,'2025-10-18 01:36:10',7,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: fogomezr@unah.hn','2025-10-18 01:36:10','SISTEMA',NULL,NULL),(194,'2025-10-18 01:36:10',7,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: fogomezr@unah.hn','2025-10-18 01:36:10','SISTEMA',NULL,NULL),(195,'2025-10-18 01:38:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-18 01:38:13','SISTEMA',NULL,NULL),(196,'2025-10-18 01:38:13',5,NULL,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-19 01:38:13','2025-10-18 01:38:13','SISTEMA_RECUPERACION',NULL,NULL),(197,'2025-10-18 01:38:15',5,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: damaris.osorio@unah.hn','2025-10-18 01:38:15','SISTEMA',NULL,NULL),(198,'2025-10-18 01:38:15',5,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: damaris.osorio@unah.hn','2025-10-18 01:38:15','SISTEMA',NULL,NULL),(199,'2025-10-19 17:52:54',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 17:52:54','SISTEMA',NULL,NULL),(200,'2025-10-19 17:52:54',1,NULL,'LOGIN','Inicio de sesión exitoso','2025-10-19 17:52:54','SISTEMA',NULL,NULL),(201,'2025-10-19 17:52:58',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-19 17:52:58','SISTEMA',NULL,NULL),(202,'2025-10-19 17:53:17',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 17:53:17','SISTEMA',NULL,NULL),(203,'2025-10-19 17:53:17',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-19 17:53:17','SISTEMA',NULL,NULL),(204,'2025-10-19 17:54:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 17:54:02','SISTEMA',NULL,NULL),(205,'2025-10-19 17:54:02',1,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-19 17:54:02','SISTEMA',NULL,NULL),(206,'2025-10-19 18:29:06',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 18:29:06','SISTEMA',NULL,NULL),(207,'2025-10-19 18:29:06',1,NULL,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-19 18:29:06','SISTEMA',NULL,NULL),(208,'2025-10-19 18:35:55',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 18:35:55','SISTEMA',NULL,NULL),(209,'2025-10-19 18:35:55',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada. Parámetros: MIN=5, MAX=10','2025-10-19 18:35:55','SISTEMA',NULL,NULL),(210,'2025-10-19 18:45:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 18:45:37','SISTEMA',NULL,NULL),(211,'2025-10-19 18:45:38',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada. Parámetros: MIN=5, MAX=10','2025-10-19 18:45:38','SISTEMA',NULL,NULL),(212,'2025-10-19 18:48:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 18:48:31','SISTEMA',NULL,NULL),(213,'2025-10-19 18:48:31',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-19 18:48:31','SISTEMA',NULL,NULL),(214,'2025-10-19 19:04:41',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 19:04:41','SISTEMA',NULL,NULL),(215,'2025-10-19 19:04:48',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 19:04:48','SISTEMA',NULL,NULL),(216,'2025-10-19 19:06:45',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 19:06:45','SISTEMA',NULL,NULL),(217,'2025-10-19 19:13:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 19:13:24','SISTEMA',NULL,NULL),(218,'2025-10-19 19:13:24',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-19 19:13:24','Administrador del Sistema',NULL,NULL),(219,'2025-10-19 19:13:27',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-19 19:13:27','SISTEMA',NULL,NULL),(220,'2025-10-19 19:13:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-19 19:13:49','SISTEMA',NULL,NULL),(221,'2025-10-19 19:13:49',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-19 19:13:49','SISTEMA',NULL,NULL),(222,'2025-10-20 20:17:16',9,2,'CREAR_USUARIO','Usuario creado: DEDE. Días vigencia: 360','2025-10-20 20:17:16','ADMIN',NULL,NULL),(223,'2025-10-20 20:27:07',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 20:27:07','SISTEMA',NULL,NULL),(224,'2025-10-20 20:28:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 20:28:12','SISTEMA',NULL,NULL),(225,'2025-10-20 20:28:13',9,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-21 20:28:13','2025-10-20 20:28:13','SISTEMA_RECUPERACION',NULL,NULL),(226,'2025-10-20 20:28:15',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-20 20:28:15','SISTEMA',NULL,NULL),(227,'2025-10-20 20:28:15',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-20 20:28:15','SISTEMA',NULL,NULL),(228,'2025-10-20 20:30:45',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 20:30:45','SISTEMA',NULL,NULL),(229,'2025-10-20 20:30:45',9,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-21 20:30:45','2025-10-20 20:30:45','SISTEMA_RECUPERACION',NULL,NULL),(230,'2025-10-20 20:30:47',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-20 20:30:47','SISTEMA',NULL,NULL),(231,'2025-10-20 20:30:47',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-20 20:30:47','SISTEMA',NULL,NULL),(232,'2025-10-20 21:31:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:31:31','SISTEMA',NULL,NULL),(233,'2025-10-20 21:31:31',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 21:31:31','DEDE DEDE DEDE',NULL,NULL),(234,'2025-10-20 21:31:33',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 21:31:33','SISTEMA',NULL,NULL),(235,'2025-10-20 21:32:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:32:13','SISTEMA',NULL,NULL),(236,'2025-10-20 21:32:13',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-20 21:32:13','SISTEMA',NULL,NULL),(237,'2025-10-20 21:33:14',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:33:14','SISTEMA',NULL,NULL),(238,'2025-10-20 21:33:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:33:58','SISTEMA',NULL,NULL),(239,'2025-10-20 21:40:34',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:40:34','SISTEMA',NULL,NULL),(240,'2025-10-20 21:41:05',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:41:05','SISTEMA',NULL,NULL),(241,'2025-10-20 21:42:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:42:02','SISTEMA',NULL,NULL),(242,'2025-10-20 21:42:02',3,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-21 21:42:02','2025-10-20 21:42:02','SISTEMA_RECUPERACION',NULL,NULL),(243,'2025-10-20 21:42:04',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-20 21:42:04','SISTEMA',NULL,NULL),(244,'2025-10-20 21:42:04',3,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-20 21:42:04','SISTEMA',NULL,NULL),(245,'2025-10-20 21:42:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:42:30','SISTEMA',NULL,NULL),(246,'2025-10-20 21:42:30',3,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 21:42:30','DENIS IRWIN LOPEZ',NULL,NULL),(247,'2025-10-20 21:42:33',3,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 21:42:33','SISTEMA',NULL,NULL),(248,'2025-10-20 21:43:28',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:43:28','SISTEMA',NULL,NULL),(249,'2025-10-20 21:43:28',3,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-20 21:43:28','SISTEMA',NULL,NULL),(250,'2025-10-20 21:57:38',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:57:38','SISTEMA',NULL,NULL),(251,'2025-10-20 21:57:38',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 21:57:38','DEDE DEDE DEDE',NULL,NULL),(252,'2025-10-20 21:57:41',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 21:57:41','SISTEMA',NULL,NULL),(253,'2025-10-20 21:58:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:58:10','SISTEMA',NULL,NULL),(254,'2025-10-20 21:58:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:58:37','SISTEMA',NULL,NULL),(255,'2025-10-20 21:58:37',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 21:58:37','DEDE DEDE DEDE',NULL,NULL),(256,'2025-10-20 21:58:40',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 21:58:40','SISTEMA',NULL,NULL),(257,'2025-10-20 21:58:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 21:58:53','SISTEMA',NULL,NULL),(258,'2025-10-20 21:58:53',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-20 21:58:53','SISTEMA',NULL,NULL),(259,'2025-10-20 22:55:38',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 22:55:38','SISTEMA',NULL,NULL),(260,'2025-10-20 22:55:38',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 22:55:38','DEDE DEDE DEDE',NULL,NULL),(261,'2025-10-20 22:55:41',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 22:55:41','SISTEMA',NULL,NULL),(262,'2025-10-20 22:56:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 22:56:23','SISTEMA',NULL,NULL),(263,'2025-10-20 22:56:23',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-20 22:56:23','SISTEMA',NULL,NULL),(264,'2025-10-20 22:59:21',10,2,'CREAR_USUARIO','Usuario creado: DENISS. Días vigencia: 360','2025-10-20 22:59:21','ADMIN',NULL,NULL),(265,'2025-10-20 23:00:45',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 23:00:45','SISTEMA',NULL,NULL),(266,'2025-10-20 23:01:55',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 23:01:55','SISTEMA',NULL,NULL),(267,'2025-10-20 23:01:56',9,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-21 23:01:56','2025-10-20 23:01:56','SISTEMA_RECUPERACION',NULL,NULL),(268,'2025-10-20 23:01:59',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-20 23:01:59','SISTEMA',NULL,NULL),(269,'2025-10-20 23:01:59',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-20 23:01:59','SISTEMA',NULL,NULL),(270,'2025-10-20 23:03:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 23:03:12','SISTEMA',NULL,NULL),(271,'2025-10-20 23:03:12',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-20 23:03:12','DEDE DEDE DEDE',NULL,NULL),(272,'2025-10-20 23:03:14',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-20 23:03:14','SISTEMA',NULL,NULL),(273,'2025-10-20 23:03:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-20 23:03:43','SISTEMA',NULL,NULL),(274,'2025-10-20 23:03:43',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-20 23:03:43','SISTEMA',NULL,NULL),(275,'2025-10-21 01:33:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:33:31','SISTEMA',NULL,NULL),(276,'2025-10-21 01:35:41',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:35:41','SISTEMA',NULL,NULL),(277,'2025-10-21 01:35:41',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:35:41','JORGE JORGE',NULL,NULL),(278,'2025-10-21 01:35:44',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 01:35:44','SISTEMA',NULL,NULL),(279,'2025-10-21 01:36:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:36:12','SISTEMA',NULL,NULL),(280,'2025-10-21 01:36:13',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 01:36:13','SISTEMA',NULL,NULL),(281,'2025-10-21 01:39:14',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:39:14','SISTEMA',NULL,NULL),(282,'2025-10-21 01:39:14',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:39:14','JORGE JORGE',NULL,NULL),(283,'2025-10-21 01:39:17',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 01:39:17','SISTEMA',NULL,NULL),(284,'2025-10-21 01:39:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:39:30','SISTEMA',NULL,NULL),(285,'2025-10-21 01:39:30',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 01:39:30','SISTEMA',NULL,NULL),(286,'2025-10-21 01:40:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:40:19','SISTEMA',NULL,NULL),(287,'2025-10-21 01:40:19',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:40:19','JORGE JORGE',NULL,NULL),(288,'2025-10-21 01:40:22',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 01:40:22','SISTEMA',NULL,NULL),(289,'2025-10-21 01:40:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:40:37','SISTEMA',NULL,NULL),(290,'2025-10-21 01:40:37',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 01:40:37','SISTEMA',NULL,NULL),(291,'2025-10-21 01:51:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:51:58','SISTEMA',NULL,NULL),(292,'2025-10-21 01:51:58',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:51:58','JORGE JORGE',NULL,NULL),(293,'2025-10-21 01:52:00',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 01:52:00','SISTEMA',NULL,NULL),(294,'2025-10-21 01:52:20',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:52:20','SISTEMA',NULL,NULL),(295,'2025-10-21 01:52:20',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 01:52:20','SISTEMA',NULL,NULL),(296,'2025-10-21 01:55:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:55:31','SISTEMA',NULL,NULL),(297,'2025-10-21 01:56:27',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:56:27','SISTEMA',NULL,NULL),(298,'2025-10-21 01:56:27',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:56:27','JORGE JORGE',NULL,NULL),(299,'2025-10-21 01:56:30',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 01:56:30','SISTEMA',NULL,NULL),(300,'2025-10-21 01:56:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:56:43','SISTEMA',NULL,NULL),(301,'2025-10-21 01:56:43',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 01:56:43','SISTEMA',NULL,NULL),(302,'2025-10-21 01:59:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 01:59:44','SISTEMA',NULL,NULL),(303,'2025-10-21 01:59:44',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 01:59:44','DEDE DEDE DEDE',NULL,NULL),(304,'2025-10-21 02:00:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:00:10','SISTEMA',NULL,NULL),(305,'2025-10-21 02:00:10',9,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-22 02:00:10','2025-10-21 02:00:10','SISTEMA_RECUPERACION',NULL,NULL),(306,'2025-10-21 02:00:13',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-21 02:00:13','SISTEMA',NULL,NULL),(307,'2025-10-21 02:00:13',9,NULL,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-21 02:00:13','SISTEMA',NULL,NULL),(308,'2025-10-21 02:00:33',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:00:33','SISTEMA',NULL,NULL),(309,'2025-10-21 02:00:33',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 02:00:33','DEDE DEDE DEDE',NULL,NULL),(310,'2025-10-21 02:00:36',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 02:00:36','SISTEMA',NULL,NULL),(311,'2025-10-21 02:00:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:00:44','SISTEMA',NULL,NULL),(312,'2025-10-21 02:00:44',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 02:00:44','SISTEMA',NULL,NULL),(313,'2025-10-21 02:04:39',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:04:39','SISTEMA',NULL,NULL),(314,'2025-10-21 02:04:39',9,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 02:04:39','DEDE DEDE DEDE',NULL,NULL),(315,'2025-10-21 02:04:42',9,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 02:04:42','SISTEMA',NULL,NULL),(316,'2025-10-21 02:04:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:04:51','SISTEMA',NULL,NULL),(317,'2025-10-21 02:04:51',9,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 02:04:51','SISTEMA',NULL,NULL),(318,'2025-10-21 02:10:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:10:43','SISTEMA',NULL,NULL),(319,'2025-10-21 02:10:43',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 02:10:43','JORGE JORGE',NULL,NULL),(320,'2025-10-21 02:10:46',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 02:10:46','SISTEMA',NULL,NULL),(321,'2025-10-21 02:11:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 02:11:00','SISTEMA',NULL,NULL),(322,'2025-10-21 02:11:00',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 02:11:00','SISTEMA',NULL,NULL),(323,'2025-10-21 13:43:32',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 13:43:32','SISTEMA',NULL,NULL),(324,'2025-10-21 13:43:32',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 13:43:32','JORGE JORGE',NULL,NULL),(325,'2025-10-21 13:43:34',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 13:43:34','SISTEMA',NULL,NULL),(326,'2025-10-21 13:43:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 13:43:53','SISTEMA',NULL,NULL),(327,'2025-10-21 13:43:53',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 13:43:53','SISTEMA',NULL,NULL),(328,'2025-10-21 13:53:05',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 13:53:05','SISTEMA',NULL,NULL),(329,'2025-10-21 13:53:06',10,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-21 13:53:06','SISTEMA',NULL,NULL),(330,'2025-10-21 14:17:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 14:17:23','SISTEMA',NULL,NULL),(331,'2025-10-21 14:17:23',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 14:17:23','JORGE JORGE',NULL,NULL),(332,'2025-10-21 14:17:26',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 14:17:26','SISTEMA',NULL,NULL),(333,'2025-10-21 14:17:38',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 14:17:38','SISTEMA',NULL,NULL),(334,'2025-10-21 14:17:38',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 14:17:38','SISTEMA',NULL,NULL),(335,'2025-10-21 14:45:47',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 14:45:47','SISTEMA',NULL,NULL),(336,'2025-10-21 14:45:47',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 14:45:47','JORGE JORGE',NULL,NULL),(337,'2025-10-21 14:45:50',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 14:45:50','SISTEMA',NULL,NULL),(338,'2025-10-21 14:46:20',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 14:46:20','SISTEMA',NULL,NULL),(339,'2025-10-21 14:46:20',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 14:46:20','SISTEMA',NULL,NULL),(340,'2025-10-21 14:46:54',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 14:46:54','SISTEMA',NULL,NULL),(341,'2025-10-21 14:46:54',10,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-21 14:46:54','SISTEMA',NULL,NULL),(342,'2025-10-21 16:23:18',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:23:18','SISTEMA',NULL,NULL),(343,'2025-10-21 16:23:18',10,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-22 16:23:18','2025-10-21 16:23:18','SISTEMA_RECUPERACION',NULL,NULL),(344,'2025-10-21 16:23:20',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-21 16:23:20','SISTEMA',NULL,NULL),(345,'2025-10-21 16:23:20',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-21 16:23:20','SISTEMA',NULL,NULL),(346,'2025-10-21 16:23:52',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:23:52','SISTEMA',NULL,NULL),(347,'2025-10-21 16:23:52',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 16:23:52','JORGE JORGE',NULL,NULL),(348,'2025-10-21 16:23:54',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 16:23:54','SISTEMA',NULL,NULL),(349,'2025-10-21 16:24:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:24:15','SISTEMA',NULL,NULL),(350,'2025-10-21 16:24:15',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 16:24:15','SISTEMA',NULL,NULL),(351,'2025-10-21 16:25:35',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:25:35','SISTEMA',NULL,NULL),(352,'2025-10-21 16:25:35',10,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-21 16:25:35','SISTEMA',NULL,NULL),(353,'2025-10-21 16:27:48',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:27:48','SISTEMA',NULL,NULL),(354,'2025-10-21 16:27:48',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 16:27:48','JORGE JORGE',NULL,NULL),(355,'2025-10-21 16:27:50',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 16:27:50','SISTEMA',NULL,NULL),(356,'2025-10-21 16:28:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:28:08','SISTEMA',NULL,NULL),(357,'2025-10-21 16:28:08',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 16:28:08','SISTEMA',NULL,NULL),(358,'2025-10-21 16:32:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:32:02','SISTEMA',NULL,NULL),(359,'2025-10-21 16:32:02',3,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-22 16:32:02','2025-10-21 16:32:02','SISTEMA_RECUPERACION',NULL,NULL),(360,'2025-10-21 16:32:05',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-21 16:32:05','SISTEMA',NULL,NULL),(361,'2025-10-21 16:32:05',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-21 16:32:05','SISTEMA',NULL,NULL),(362,'2025-10-21 16:32:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:32:44','SISTEMA',NULL,NULL),(363,'2025-10-21 16:32:45',3,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 16:32:45','DENIS IRWIN LOPEZ',NULL,NULL),(364,'2025-10-21 16:32:46',3,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 16:32:46','SISTEMA',NULL,NULL),(365,'2025-10-21 16:33:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:33:00','SISTEMA',NULL,NULL),(366,'2025-10-21 16:33:00',3,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 16:33:00','SISTEMA',NULL,NULL),(367,'2025-10-21 16:57:36',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:57:36','SISTEMA',NULL,NULL),(368,'2025-10-21 16:57:36',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 16:57:36','JORGE JORGE',NULL,NULL),(369,'2025-10-21 16:57:38',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 16:57:38','SISTEMA',NULL,NULL),(370,'2025-10-21 16:58:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 16:58:02','SISTEMA',NULL,NULL),(371,'2025-10-21 16:58:02',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 16:58:02','SISTEMA',NULL,NULL),(372,'2025-10-21 17:03:20',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 17:03:20','SISTEMA',NULL,NULL),(373,'2025-10-21 17:03:20',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 17:03:20','JORGE JORGE',NULL,NULL),(374,'2025-10-21 17:03:22',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 17:03:22','SISTEMA',NULL,NULL),(375,'2025-10-21 17:03:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 17:03:37','SISTEMA',NULL,NULL),(376,'2025-10-21 17:03:37',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 17:03:37','SISTEMA',NULL,NULL),(377,'2025-10-21 17:11:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 17:11:10','SISTEMA',NULL,NULL),(378,'2025-10-21 17:11:10',10,1,'LOGIN','Inicio de sesión exitoso','2025-10-21 17:11:10','JORGE JORGE',NULL,NULL),(379,'2025-10-21 17:11:13',10,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-21 17:11:13','SISTEMA',NULL,NULL),(380,'2025-10-21 17:11:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-21 17:11:24','SISTEMA',NULL,NULL),(381,'2025-10-21 17:11:24',10,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-21 17:11:24','SISTEMA',NULL,NULL),(382,'2025-10-23 21:50:36',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 21:50:36','SISTEMA',NULL,NULL),(383,'2025-10-23 21:50:36',10,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-24 21:50:36','2025-10-23 21:50:36','SISTEMA_RECUPERACION',NULL,NULL),(384,'2025-10-23 21:50:40',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-23 21:50:40','SISTEMA',NULL,NULL),(385,'2025-10-23 21:50:40',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-23 21:50:40','SISTEMA',NULL,NULL),(386,'2025-10-23 21:52:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 21:52:19','SISTEMA',NULL,NULL),(387,'2025-10-23 22:18:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 22:18:13','SISTEMA',NULL,NULL),(388,'2025-10-23 22:36:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 22:36:30','SISTEMA',NULL,NULL),(389,'2025-10-23 22:36:30',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-23 22:36:30','Administrador del Sistema',NULL,NULL),(390,'2025-10-23 22:36:33',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-23 22:36:33','SISTEMA',NULL,NULL),(391,'2025-10-23 22:37:11',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 22:37:11','SISTEMA',NULL,NULL),(392,'2025-10-23 22:37:11',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-23 22:37:11','SISTEMA',NULL,NULL),(393,'2025-10-23 22:37:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-23 22:37:43','SISTEMA',NULL,NULL),(394,'2025-10-23 22:37:43',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-23 22:37:43','SISTEMA',NULL,NULL),(395,'2025-10-23 23:10:30',11,2,'CREAR_USUARIO','Usuario creado: DEDER. Días vigencia: 360','2025-10-23 23:10:30','ADMIN',NULL,NULL),(396,'2025-10-23 23:33:17',12,2,'CREAR_USUARIO','Usuario creado: SEDA. Días vigencia: 360','2025-10-23 23:33:17','ADMIN',NULL,NULL),(397,'2025-10-23 23:39:53',13,2,'CREAR_USUARIO','Usuario creado: ADA. Días vigencia: 360','2025-10-23 23:39:53','ADMIN',NULL,NULL),(398,'2025-10-23 23:41:51',14,2,'CREAR_USUARIO','Usuario creado: EDA. Días vigencia: 360','2025-10-23 23:41:51','ADMIN',NULL,NULL),(399,'2025-10-23 23:49:45',15,2,'CREAR_USUARIO','Usuario creado: EDERD. Días vigencia: 360','2025-10-23 23:49:45','ADMIN',NULL,NULL),(400,'2025-10-23 23:59:15',16,2,'CREAR_USUARIO','Usuario creado: EDRAS. Días vigencia: 360','2025-10-23 23:59:15','ADMIN',NULL,NULL),(401,'2025-10-24 00:06:38',17,2,'CREAR_USUARIO','Usuario creado: PIRLO. Días vigencia: 360','2025-10-24 00:06:38','ADMIN',NULL,NULL),(402,'2025-10-24 00:08:29',18,2,'CREAR_USUARIO','Usuario creado: KAREN. Días vigencia: 360','2025-10-24 00:08:29','ADMIN',NULL,NULL),(403,'2025-10-24 00:14:08',19,2,'CREAR_USUARIO','Usuario creado: JENI. Días vigencia: 360','2025-10-24 00:14:08','ADMIN',NULL,NULL),(404,'2025-10-24 00:15:45',20,2,'CREAR_USUARIO','Usuario creado: FERRERA. Días vigencia: 360','2025-10-24 00:15:45','ADMIN',NULL,NULL),(405,'2025-10-24 00:22:23',21,2,'CREAR_USUARIO','Usuario creado: AFERERA. Días vigencia: 360','2025-10-24 00:22:23','ADMIN',NULL,NULL),(406,'2025-10-24 00:23:17',22,2,'CREAR_USUARIO','Usuario creado: FERRERERE. Días vigencia: 360','2025-10-24 00:23:17','ADMIN',NULL,NULL),(407,'2025-10-24 00:24:16',23,2,'CREAR_USUARIO','Usuario creado: DDD. Días vigencia: 360','2025-10-24 00:24:16','ADMIN',NULL,NULL),(408,'2025-10-24 00:32:11',24,2,'CREAR_USUARIO','Usuario creado: YEYE. Días vigencia: 360','2025-10-24 00:32:11','ADMIN',NULL,NULL),(409,'2025-10-24 01:01:22',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:01:22','SISTEMA',NULL,NULL),(410,'2025-10-24 01:01:22',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:01:22','Administrador del Sistema',NULL,NULL),(411,'2025-10-24 01:01:22',1,NULL,'LOGIN_DIRECTO','Login exitoso sin 2FA - Usuario activo','2025-10-24 01:01:22','SISTEMA',NULL,NULL),(412,'2025-10-24 01:02:04',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:02:04','SISTEMA',NULL,NULL),(413,'2025-10-24 01:02:04',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-24 01:02:04','SISTEMA',NULL,NULL),(414,'2025-10-24 01:03:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:03:44','SISTEMA',NULL,NULL),(415,'2025-10-24 01:05:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:05:43','SISTEMA',NULL,NULL),(416,'2025-10-24 01:05:43',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:05:43','Administrador del Sistema',NULL,NULL),(417,'2025-10-24 01:05:43',1,NULL,'LOGIN_DIRECTO','Login exitoso sin 2FA - Usuario activo','2025-10-24 01:05:43','SISTEMA',NULL,NULL),(418,'2025-10-24 01:34:46',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:34:46','SISTEMA',NULL,NULL),(419,'2025-10-24 01:34:46',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:34:46','Administrador del Sistema',NULL,NULL),(420,'2025-10-24 01:34:49',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 01:34:49','SISTEMA',NULL,NULL),(421,'2025-10-24 01:35:16',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:35:16','SISTEMA',NULL,NULL),(422,'2025-10-24 01:35:16',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 01:35:16','SISTEMA',NULL,NULL),(423,'2025-10-24 01:35:55',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:35:55','SISTEMA',NULL,NULL),(424,'2025-10-24 01:35:55',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-24 01:35:55','SISTEMA',NULL,NULL),(425,'2025-10-24 01:36:34',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:36:34','SISTEMA',NULL,NULL),(426,'2025-10-24 01:36:34',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:36:34','Administrador del Sistema',NULL,NULL),(427,'2025-10-24 01:36:37',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 01:36:37','SISTEMA',NULL,NULL),(428,'2025-10-24 01:42:56',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:42:56','SISTEMA',NULL,NULL),(429,'2025-10-24 01:42:56',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:42:56','Administrador del Sistema',NULL,NULL),(430,'2025-10-24 01:42:59',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 01:42:59','SISTEMA',NULL,NULL),(431,'2025-10-24 01:43:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:43:15','SISTEMA',NULL,NULL),(432,'2025-10-24 01:43:15',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 01:43:15','SISTEMA',NULL,NULL),(433,'2025-10-24 01:44:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:44:37','SISTEMA',NULL,NULL),(434,'2025-10-24 01:44:37',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 01:44:37','Administrador del Sistema',NULL,NULL),(435,'2025-10-24 01:44:40',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 01:44:40','SISTEMA',NULL,NULL),(436,'2025-10-24 01:45:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 01:45:02','SISTEMA',NULL,NULL),(437,'2025-10-24 01:45:02',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 01:45:02','SISTEMA',NULL,NULL),(438,'2025-10-24 02:09:37',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:09:37','ADMIN',NULL,NULL),(439,'2025-10-24 02:09:41',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:09:41','ADMIN',NULL,NULL),(440,'2025-10-24 02:09:54',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:09:54','ADMIN',NULL,NULL),(441,'2025-10-24 02:09:57',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:09:57','ADMIN',NULL,NULL),(442,'2025-10-24 02:09:59',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:09:59','ADMIN',NULL,NULL),(443,'2025-10-24 02:10:15',11,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:10:15','ADMIN',NULL,NULL),(444,'2025-10-24 02:12:27',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:12:27','ADMIN',NULL,NULL),(445,'2025-10-24 02:13:25',14,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:13:25','ADMIN',NULL,NULL),(446,'2025-10-24 02:20:53',10,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 02:20:53','ADMIN',NULL,NULL),(447,'2025-10-24 03:45:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 03:45:08','SISTEMA',NULL,NULL),(448,'2025-10-24 03:45:08',3,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-25 03:45:08','2025-10-24 03:45:08','SISTEMA_RECUPERACION',NULL,NULL),(449,'2025-10-24 03:45:11',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-24 03:45:11','SISTEMA',NULL,NULL),(450,'2025-10-24 03:45:11',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-24 03:45:11','SISTEMA',NULL,NULL),(451,'2025-10-24 03:55:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 03:55:13','SISTEMA',NULL,NULL),(452,'2025-10-24 03:55:13',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 03:55:13','Administrador del Sistema',NULL,NULL),(453,'2025-10-24 03:55:16',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 03:55:16','SISTEMA',NULL,NULL),(454,'2025-10-24 03:55:36',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 03:55:36','SISTEMA',NULL,NULL),(455,'2025-10-24 03:55:36',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 03:55:36','SISTEMA',NULL,NULL),(456,'2025-10-24 03:55:55',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 03:55:55','SISTEMA',NULL,NULL),(457,'2025-10-24 04:05:35',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:05:35','SISTEMA',NULL,NULL),(458,'2025-10-24 04:05:35',3,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-25 04:05:35','2025-10-24 04:05:35','SISTEMA_RECUPERACION',NULL,NULL),(459,'2025-10-24 04:05:37',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-24 04:05:37','SISTEMA',NULL,NULL),(460,'2025-10-24 04:05:37',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-24 04:05:37','SISTEMA',NULL,NULL),(461,'2025-10-24 04:19:36',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:19:36','SISTEMA',NULL,NULL),(462,'2025-10-24 04:19:36',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 04:19:36','Administrador del Sistema',NULL,NULL),(463,'2025-10-24 04:19:38',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 04:19:38','SISTEMA',NULL,NULL),(464,'2025-10-24 04:19:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:19:50','SISTEMA',NULL,NULL),(465,'2025-10-24 04:19:51',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 04:19:51','SISTEMA',NULL,NULL),(466,'2025-10-24 04:20:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:20:49','SISTEMA',NULL,NULL),(467,'2025-10-24 04:21:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:21:13','SISTEMA',NULL,NULL),(468,'2025-10-24 04:28:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:28:58','SISTEMA',NULL,NULL),(469,'2025-10-24 04:32:16',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 04:32:16','SISTEMA',NULL,NULL),(470,'2025-10-24 04:39:06',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-24 04:39:06','ADMIN',NULL,NULL),(471,'2025-10-24 23:40:39',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 23:40:39','SISTEMA',NULL,NULL),(472,'2025-10-24 23:40:39',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 23:40:39','Administrador del Sistema',NULL,NULL),(473,'2025-10-24 23:55:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 23:55:50','SISTEMA',NULL,NULL),(474,'2025-10-24 23:55:50',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 23:55:50','Administrador del Sistema',NULL,NULL),(475,'2025-10-24 23:55:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 23:55:50','SISTEMA',NULL,NULL),(476,'2025-10-24 23:55:50',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-24 23:55:50','Administrador del Sistema',NULL,NULL),(477,'2025-10-24 23:55:53',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-24 23:55:53','SISTEMA',NULL,NULL),(478,'2025-10-24 23:56:14',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-24 23:56:14','SISTEMA',NULL,NULL),(479,'2025-10-24 23:56:14',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-24 23:56:14','SISTEMA',NULL,NULL),(480,'2025-10-25 00:03:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:03:02','SISTEMA',NULL,NULL),(481,'2025-10-25 00:03:02',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:03:02','Administrador del Sistema',NULL,NULL),(482,'2025-10-25 00:03:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:03:02','SISTEMA',NULL,NULL),(483,'2025-10-25 00:03:02',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:03:02','Administrador del Sistema',NULL,NULL),(484,'2025-10-25 00:03:05',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 00:03:05','SISTEMA',NULL,NULL),(485,'2025-10-25 00:03:18',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:03:18','SISTEMA',NULL,NULL),(486,'2025-10-25 00:03:18',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 00:03:18','SISTEMA',NULL,NULL),(487,'2025-10-25 00:03:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:03:58','SISTEMA',NULL,NULL),(488,'2025-10-25 00:03:58',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 00:03:58','SISTEMA',NULL,NULL),(489,'2025-10-25 00:06:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:06:50','SISTEMA',NULL,NULL),(490,'2025-10-25 00:06:50',3,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:06:50','DENIS IRWIN LOPEZ',NULL,NULL),(491,'2025-10-25 00:07:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:07:02','SISTEMA',NULL,NULL),(492,'2025-10-25 00:07:03',3,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-26 00:07:03','2025-10-25 00:07:03','SISTEMA_RECUPERACION',NULL,NULL),(493,'2025-10-25 00:07:05',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislopez1206@gmail.com','2025-10-25 00:07:05','SISTEMA',NULL,NULL),(494,'2025-10-25 00:07:05',3,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislopez1206@gmail.com','2025-10-25 00:07:05','SISTEMA',NULL,NULL),(495,'2025-10-25 00:12:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:12:00','SISTEMA',NULL,NULL),(496,'2025-10-25 00:12:00',6,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:12:00','JORGE JORGE',NULL,NULL),(497,'2025-10-25 00:14:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:14:30','SISTEMA',NULL,NULL),(498,'2025-10-25 00:14:31',6,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:14:31','JORGE JORGE',NULL,NULL),(499,'2025-10-25 00:28:25',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:28:25','SISTEMA',NULL,NULL),(500,'2025-10-25 00:28:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:28:43','SISTEMA',NULL,NULL),(501,'2025-10-25 00:28:43',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:28:43','Administrador del Sistema',NULL,NULL),(502,'2025-10-25 00:28:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:28:44','SISTEMA',NULL,NULL),(503,'2025-10-25 00:28:44',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 00:28:44','Administrador del Sistema',NULL,NULL),(504,'2025-10-25 00:28:46',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 00:28:46','SISTEMA',NULL,NULL),(505,'2025-10-25 00:29:01',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:29:01','SISTEMA',NULL,NULL),(506,'2025-10-25 00:29:01',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 00:29:01','SISTEMA',NULL,NULL),(507,'2025-10-25 00:29:35',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:29:35','SISTEMA',NULL,NULL),(508,'2025-10-25 00:29:35',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 00:29:35','SISTEMA',NULL,NULL),(509,'2025-10-25 00:39:03',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 00:39:03','SISTEMA',NULL,NULL),(510,'2025-10-25 00:39:03',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 00:39:03','SISTEMA',NULL,NULL),(511,'2025-10-25 00:39:03',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 00:39:03','SISTEMA',NULL,NULL),(512,'2025-10-25 01:32:26',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 01:32:26','SISTEMA',NULL,NULL),(513,'2025-10-25 01:32:27',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 01:32:27','Administrador del Sistema',NULL,NULL),(514,'2025-10-25 01:32:27',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 01:32:27','SISTEMA',NULL,NULL),(515,'2025-10-25 01:32:27',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 01:32:27','Administrador del Sistema',NULL,NULL),(516,'2025-10-25 01:32:29',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 01:32:29','SISTEMA',NULL,NULL),(517,'2025-10-25 01:32:39',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 01:32:39','SISTEMA',NULL,NULL),(518,'2025-10-25 01:32:39',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 01:32:39','SISTEMA',NULL,NULL),(519,'2025-10-25 01:33:04',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 01:33:04','SISTEMA',NULL,NULL),(520,'2025-10-25 01:33:04',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 01:33:04','SISTEMA',NULL,NULL),(521,'2025-10-25 01:33:04',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 01:33:04','SISTEMA',NULL,NULL),(522,'2025-10-25 01:34:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 01:34:50','SISTEMA',NULL,NULL),(523,'2025-10-25 01:34:50',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 01:34:50','Administrador del Sistema',NULL,NULL),(524,'2025-10-25 01:57:56',13,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-25 01:57:56','ADMIN',NULL,NULL),(525,'2025-10-25 01:58:05',19,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-25 01:58:05','ADMIN',NULL,NULL),(526,'2025-10-25 02:02:02',25,2,'CREAR_USUARIO','Usuario creado: JONI. Días vigencia: 360','2025-10-25 02:02:02','ADMIN',NULL,NULL),(527,'2025-10-25 03:06:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:06:24','SISTEMA',NULL,NULL),(528,'2025-10-25 03:06:24',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 03:06:24','Administrador del Sistema',NULL,NULL),(529,'2025-10-25 03:06:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:06:24','SISTEMA',NULL,NULL),(530,'2025-10-25 03:06:24',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 03:06:24','Administrador del Sistema',NULL,NULL),(531,'2025-10-25 03:06:27',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 03:06:27','SISTEMA',NULL,NULL),(532,'2025-10-25 03:06:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:06:44','SISTEMA',NULL,NULL),(533,'2025-10-25 03:06:44',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 03:06:44','SISTEMA',NULL,NULL),(534,'2025-10-25 03:07:28',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:07:28','SISTEMA',NULL,NULL),(535,'2025-10-25 03:07:28',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 03:07:28','SISTEMA',NULL,NULL),(536,'2025-10-25 03:07:28',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 03:07:28','SISTEMA',NULL,NULL),(537,'2025-10-25 03:08:35',26,2,'CREAR_USUARIO','Usuario creado: ZZZZ. Días vigencia: 360','2025-10-25 03:08:35','ADMIN',NULL,NULL),(538,'2025-10-25 03:10:03',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:10:03','SISTEMA',NULL,NULL),(539,'2025-10-25 03:10:03',8,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-26 03:10:03','2025-10-25 03:10:03','SISTEMA_RECUPERACION',NULL,NULL),(540,'2025-10-25 03:10:06',8,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: pjreyess@unah.hn','2025-10-25 03:10:06','SISTEMA',NULL,NULL),(541,'2025-10-25 03:10:06',8,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: pjreyess@unah.hn','2025-10-25 03:10:06','SISTEMA',NULL,NULL),(542,'2025-10-25 03:10:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 03:10:43','SISTEMA',NULL,NULL),(543,'2025-10-25 03:10:43',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 03:10:43','Administrador del Sistema',NULL,NULL),(544,'2025-10-25 14:48:43',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 14:48:43','SISTEMA',NULL,NULL),(545,'2025-10-25 14:48:43',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 14:48:43','Administrador del Sistema',NULL,NULL),(546,'2025-10-25 14:51:07',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 14:51:07','SISTEMA',NULL,NULL),(547,'2025-10-25 14:51:07',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 14:51:07','Administrador del Sistema',NULL,NULL),(548,'2025-10-25 14:57:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 14:57:19','SISTEMA',NULL,NULL),(549,'2025-10-25 14:57:38',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 14:57:38','SISTEMA',NULL,NULL),(550,'2025-10-25 14:57:38',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 14:57:38','Administrador del Sistema',NULL,NULL),(551,'2025-10-25 15:00:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:00:37','SISTEMA',NULL,NULL),(552,'2025-10-25 15:00:37',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:00:37','Administrador del Sistema',NULL,NULL),(553,'2025-10-25 15:01:20',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:01:20','SISTEMA',NULL,NULL),(554,'2025-10-25 15:01:20',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:01:20','Administrador del Sistema',NULL,NULL),(555,'2025-10-25 15:06:20',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:06:20','SISTEMA',NULL,NULL),(556,'2025-10-25 15:06:20',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:06:20','Administrador del Sistema',NULL,NULL),(557,'2025-10-25 15:14:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:14:23','SISTEMA',NULL,NULL),(558,'2025-10-25 15:14:23',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:14:23','Administrador del Sistema',NULL,NULL),(559,'2025-10-25 15:21:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:21:51','SISTEMA',NULL,NULL),(560,'2025-10-25 15:21:51',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:21:51','Administrador del Sistema',NULL,NULL),(561,'2025-10-25 15:30:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:30:10','SISTEMA',NULL,NULL),(562,'2025-10-25 15:30:10',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:30:10','Administrador del Sistema',NULL,NULL),(563,'2025-10-25 15:32:32',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:32:32','SISTEMA',NULL,NULL),(564,'2025-10-25 15:33:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:33:13','SISTEMA',NULL,NULL),(565,'2025-10-25 15:34:32',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:34:32','SISTEMA',NULL,NULL),(566,'2025-10-25 15:35:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:35:15','SISTEMA',NULL,NULL),(567,'2025-10-25 15:35:15',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:35:15','Administrador del Sistema',NULL,NULL),(568,'2025-10-25 15:35:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:35:15','SISTEMA',NULL,NULL),(569,'2025-10-25 15:35:15',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:35:15','Administrador del Sistema',NULL,NULL),(570,'2025-10-25 15:35:18',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 15:35:18','SISTEMA',NULL,NULL),(571,'2025-10-25 15:35:40',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:35:40','SISTEMA',NULL,NULL),(572,'2025-10-25 15:35:40',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 15:35:40','SISTEMA',NULL,NULL),(573,'2025-10-25 15:36:22',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:36:22','SISTEMA',NULL,NULL),(574,'2025-10-25 15:36:22',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 15:36:22','SISTEMA',NULL,NULL),(575,'2025-10-25 15:36:22',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 15:36:22','SISTEMA',NULL,NULL),(576,'2025-10-25 15:37:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:37:50','SISTEMA',NULL,NULL),(577,'2025-10-25 15:37:50',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:37:50','Administrador del Sistema',NULL,NULL),(578,'2025-10-25 15:43:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:43:08','SISTEMA',NULL,NULL),(579,'2025-10-25 15:43:08',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:43:08','Administrador del Sistema',NULL,NULL),(580,'2025-10-25 15:44:56',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:44:56','SISTEMA',NULL,NULL),(581,'2025-10-25 15:44:56',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:44:56','Administrador del Sistema',NULL,NULL),(582,'2025-10-25 15:44:56',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:44:56','SISTEMA',NULL,NULL),(583,'2025-10-25 15:44:56',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 15:44:56','Administrador del Sistema',NULL,NULL),(584,'2025-10-25 15:44:58',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 15:44:58','SISTEMA',NULL,NULL),(585,'2025-10-25 15:45:14',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:45:14','SISTEMA',NULL,NULL),(586,'2025-10-25 15:45:14',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 15:45:14','SISTEMA',NULL,NULL),(587,'2025-10-25 15:45:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 15:45:44','SISTEMA',NULL,NULL),(588,'2025-10-25 15:45:44',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 15:45:44','SISTEMA',NULL,NULL),(589,'2025-10-25 15:45:44',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 15:45:44','SISTEMA',NULL,NULL),(590,'2025-10-25 15:47:48',27,2,'CREAR_USUARIO','Usuario creado: HUBER. Días vigencia: 360','2025-10-25 15:47:48','ADMIN',NULL,NULL),(591,'2025-10-25 15:48:07',27,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-25 15:48:07','ADMIN',NULL,NULL),(592,'2025-10-25 16:57:59',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 16:57:59','SISTEMA',NULL,NULL),(593,'2025-10-25 17:01:08',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:01:08','SISTEMA',NULL,NULL),(594,'2025-10-25 17:03:42',24,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:03:42','SISTEMA',NULL,NULL),(595,'2025-10-25 17:05:33',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:05:33','SISTEMA',NULL,NULL),(596,'2025-10-25 17:08:58',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:08:58','SISTEMA',NULL,NULL),(597,'2025-10-25 17:22:44',25,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:22:44','SISTEMA',NULL,NULL),(598,'2025-10-25 17:39:08',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:39:08','SISTEMA',NULL,NULL),(599,'2025-10-25 17:47:53',11,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:47:53','SISTEMA',NULL,NULL),(600,'2025-10-25 17:58:30',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:58:30','SISTEMA',NULL,NULL),(601,'2025-10-25 17:58:58',27,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 17:58:58','SISTEMA',NULL,NULL),(602,'2025-10-25 18:00:07',5,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:00:07','SISTEMA',NULL,NULL),(603,'2025-10-25 18:00:49',11,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:00:49','SISTEMA',NULL,NULL),(604,'2025-10-25 18:01:09',25,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:01:09','SISTEMA',NULL,NULL),(605,'2025-10-25 18:01:23',24,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:01:23','SISTEMA',NULL,NULL),(606,'2025-10-25 18:03:17',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:03:17','SISTEMA',NULL,NULL),(607,'2025-10-25 18:07:41',13,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:07:41','SISTEMA',NULL,NULL),(608,'2025-10-25 18:59:13',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 18:59:13','SISTEMA',NULL,NULL),(609,'2025-10-25 19:29:30',27,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:29:30','ADMIN',NULL,NULL),(610,'2025-10-25 19:30:40',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 19:30:40','SISTEMA',NULL,NULL),(611,'2025-10-25 19:31:09',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:31:09','ADMIN',NULL,NULL),(612,'2025-10-25 19:43:33',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:43:33','ADMIN',NULL,NULL),(613,'2025-10-25 19:45:45',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:45:45','ADMIN',NULL,NULL),(614,'2025-10-25 19:53:16',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:53:16','ADMIN',NULL,NULL),(615,'2025-10-25 19:59:28',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 19:59:28','ADMIN',NULL,NULL),(616,'2025-10-25 20:01:15',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:01:15','ADMIN',NULL,NULL),(617,'2025-10-25 20:09:23',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:09:23','ADMIN',NULL,NULL),(618,'2025-10-25 20:18:15',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:18:15','ADMIN',NULL,NULL),(619,'2025-10-25 20:21:19',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:21:19','ADMIN',NULL,NULL),(620,'2025-10-25 20:31:06',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:31:06','ADMIN',NULL,NULL),(621,'2025-10-25 20:33:28',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:33:28','ADMIN',NULL,NULL),(622,'2025-10-25 20:54:25',23,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 20:54:25','SISTEMA',NULL,NULL),(623,'2025-10-25 20:55:04',23,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 20:55:04','ADMIN',NULL,NULL),(624,'2025-10-25 20:56:13',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-25 20:56:13','ADMIN',NULL,NULL),(625,'2025-10-25 20:56:37',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 20:56:37','SISTEMA',NULL,NULL),(626,'2025-10-25 20:58:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 20:58:15','SISTEMA',NULL,NULL),(627,'2025-10-25 20:58:15',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 20:58:15','Administrador del Sistema',NULL,NULL),(628,'2025-10-25 20:58:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 20:58:15','SISTEMA',NULL,NULL),(629,'2025-10-25 20:58:15',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 20:58:15','Administrador del Sistema',NULL,NULL),(630,'2025-10-25 20:58:18',1,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 20:58:18','SISTEMA',NULL,NULL),(631,'2025-10-25 20:58:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 20:58:37','SISTEMA',NULL,NULL),(632,'2025-10-25 20:58:37',1,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 20:58:37','SISTEMA',NULL,NULL),(633,'2025-10-25 20:59:11',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 20:59:11','SISTEMA',NULL,NULL),(634,'2025-10-25 20:59:11',1,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 20:59:11','SISTEMA',NULL,NULL),(635,'2025-10-25 20:59:11',1,NULL,'PRIMER_INGRESO_COMPL','Usuario completó primer ingreso y cambió contraseña - Estado actualizado a ACTIVO','2025-10-25 20:59:11','SISTEMA',NULL,NULL),(636,'2025-10-25 20:59:58',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 20:59:58','SISTEMA',NULL,NULL),(637,'2025-10-25 20:59:58',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 20:59:58','Administrador del Sistema',NULL,NULL),(638,'2025-10-25 21:59:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 21:59:21','SISTEMA',NULL,NULL),(639,'2025-10-25 21:59:21',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 21:59:21','Administrador del Sistema',NULL,NULL),(640,'2025-10-25 22:15:26',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 22:15:26','SISTEMA',NULL,NULL),(641,'2025-10-25 22:15:26',2,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-26 22:15:26','2025-10-25 22:15:26','SISTEMA_RECUPERACION',NULL,NULL),(642,'2025-10-25 22:15:29',2,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denisunah1206@gmail.com','2025-10-25 22:15:29','SISTEMA',NULL,NULL),(643,'2025-10-25 22:15:29',2,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denisunah1206@gmail.com','2025-10-25 22:15:29','SISTEMA',NULL,NULL),(644,'2025-10-25 22:29:04',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 22:29:04','SISTEMA',NULL,NULL),(645,'2025-10-25 22:31:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 22:31:21','SISTEMA',NULL,NULL),(646,'2025-10-25 22:31:21',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 22:31:21','Administrador del Sistema',NULL,NULL),(647,'2025-10-25 22:32:24',2,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-25 22:32:24','SISTEMA',NULL,NULL),(648,'2025-10-25 22:33:10',2,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-25 22:33:10','ADMIN',NULL,NULL),(649,'2025-10-25 22:34:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 22:34:08','SISTEMA',NULL,NULL),(650,'2025-10-25 22:34:08',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 22:34:08','Nuevo Usuario del Sistema',NULL,NULL),(651,'2025-10-25 23:00:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:00:24','SISTEMA',NULL,NULL),(652,'2025-10-25 23:00:24',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:00:24','Nuevo Usuario del Sistema',NULL,NULL),(653,'2025-10-25 23:02:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:02:12','SISTEMA',NULL,NULL),(654,'2025-10-25 23:02:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:02:51','SISTEMA',NULL,NULL),(655,'2025-10-25 23:02:59',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:02:59','SISTEMA',NULL,NULL),(656,'2025-10-25 23:08:39',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:08:39','SISTEMA',NULL,NULL),(657,'2025-10-25 23:08:40',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 23:08:40','SISTEMA',NULL,NULL),(658,'2025-10-25 23:09:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:09:53','SISTEMA',NULL,NULL),(659,'2025-10-25 23:10:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:10:51','SISTEMA',NULL,NULL),(660,'2025-10-25 23:21:15',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:21:15','SISTEMA',NULL,NULL),(661,'2025-10-25 23:22:03',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:22:03','SISTEMA',NULL,NULL),(662,'2025-10-25 23:22:03',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:22:03','Nuevo Usuario del Sistema',NULL,NULL),(663,'2025-10-25 23:23:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:23:13','SISTEMA',NULL,NULL),(664,'2025-10-25 23:25:24',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:25:24','SISTEMA',NULL,NULL),(665,'2025-10-25 23:27:59',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:27:59','SISTEMA',NULL,NULL),(666,'2025-10-25 23:28:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:28:50','SISTEMA',NULL,NULL),(667,'2025-10-25 23:29:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:29:08','SISTEMA',NULL,NULL),(668,'2025-10-25 23:31:33',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:31:33','SISTEMA',NULL,NULL),(669,'2025-10-25 23:31:33',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 23:31:33','SISTEMA',NULL,NULL),(670,'2025-10-25 23:34:36',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:34:36','SISTEMA',NULL,NULL),(671,'2025-10-25 23:34:36',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:34:36','Nuevo Usuario del Sistema',NULL,NULL),(672,'2025-10-25 23:41:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:41:53','SISTEMA',NULL,NULL),(673,'2025-10-25 23:41:53',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 23:41:53','SISTEMA',NULL,NULL),(674,'2025-10-25 23:42:34',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:42:34','SISTEMA',NULL,NULL),(675,'2025-10-25 23:42:34',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:42:34','Nuevo Usuario del Sistema',NULL,NULL),(676,'2025-10-25 23:44:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:44:12','SISTEMA',NULL,NULL),(677,'2025-10-25 23:44:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:44:49','SISTEMA',NULL,NULL),(678,'2025-10-25 23:47:14',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:47:14','SISTEMA',NULL,NULL),(679,'2025-10-25 23:51:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:51:49','SISTEMA',NULL,NULL),(680,'2025-10-25 23:51:49',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:51:49','Nuevo Usuario del Sistema',NULL,NULL),(681,'2025-10-25 23:51:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:51:49','SISTEMA',NULL,NULL),(682,'2025-10-25 23:51:49',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-25 23:51:49','Nuevo Usuario del Sistema',NULL,NULL),(683,'2025-10-25 23:51:52',2,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-25 23:51:52','SISTEMA',NULL,NULL),(684,'2025-10-25 23:52:13',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:52:13','SISTEMA',NULL,NULL),(685,'2025-10-25 23:52:13',2,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-25 23:52:13','SISTEMA',NULL,NULL),(686,'2025-10-25 23:52:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-25 23:52:50','SISTEMA',NULL,NULL),(687,'2025-10-25 23:52:50',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-25 23:52:50','SISTEMA',NULL,NULL),(688,'2025-10-25 23:59:02',28,2,'CREAR_USUARIO','Usuario creado: ADMINISTRADOR. Días vigencia: 360','2025-10-25 23:59:02','ADMIN',NULL,NULL),(689,'2025-10-26 00:02:46',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:02:46','SISTEMA',NULL,NULL),(690,'2025-10-26 00:04:25',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:04:25','SISTEMA',NULL,NULL),(691,'2025-10-26 00:05:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:05:49','SISTEMA',NULL,NULL),(692,'2025-10-26 00:06:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:06:30','SISTEMA',NULL,NULL),(693,'2025-10-26 00:06:30',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 00:06:30','Nuevo Usuario del Sistema',NULL,NULL),(694,'2025-10-26 00:25:16',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:25:16','SISTEMA',NULL,NULL),(695,'2025-10-26 00:54:52',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:54:52','SISTEMA',NULL,NULL),(696,'2025-10-26 00:54:52',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 00:54:52','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(697,'2025-10-26 00:56:18',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:56:18','SISTEMA',NULL,NULL),(698,'2025-10-26 00:56:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 00:56:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(699,'2025-10-26 00:56:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:56:19','SISTEMA',NULL,NULL),(700,'2025-10-26 00:56:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 00:56:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(701,'2025-10-26 00:56:21',28,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-26 00:56:21','SISTEMA',NULL,NULL),(702,'2025-10-26 00:56:51',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:56:51','SISTEMA',NULL,NULL),(703,'2025-10-26 00:56:51',28,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-26 00:56:51','SISTEMA',NULL,NULL),(704,'2025-10-26 00:57:33',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 00:57:33','SISTEMA',NULL,NULL),(705,'2025-10-26 00:57:33',28,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-26 00:57:33','SISTEMA',NULL,NULL),(706,'2025-10-26 01:01:26',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:01:26','SISTEMA',NULL,NULL),(707,'2025-10-26 01:02:04',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:02:04','SISTEMA',NULL,NULL),(708,'2025-10-26 01:02:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:02:12','SISTEMA',NULL,NULL),(709,'2025-10-26 01:02:12',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:02:12','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(710,'2025-10-26 01:03:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:03:19','SISTEMA',NULL,NULL),(711,'2025-10-26 01:03:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:03:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(712,'2025-10-26 01:03:19',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:03:19','SISTEMA',NULL,NULL),(713,'2025-10-26 01:03:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:03:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(714,'2025-10-26 01:03:21',28,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-26 01:03:21','SISTEMA',NULL,NULL),(715,'2025-10-26 01:03:41',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:03:41','SISTEMA',NULL,NULL),(716,'2025-10-26 01:03:41',28,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-26 01:03:41','SISTEMA',NULL,NULL),(717,'2025-10-26 01:04:22',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:04:22','SISTEMA',NULL,NULL),(718,'2025-10-26 01:04:22',28,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-26 01:04:22','SISTEMA',NULL,NULL),(719,'2025-10-26 01:09:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:09:49','SISTEMA',NULL,NULL),(720,'2025-10-26 01:09:49',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:09:49','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(721,'2025-10-26 01:09:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:09:49','SISTEMA',NULL,NULL),(722,'2025-10-26 01:09:49',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:09:49','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(723,'2025-10-26 01:09:52',28,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-26 01:09:52','SISTEMA',NULL,NULL),(724,'2025-10-26 01:10:08',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:10:08','SISTEMA',NULL,NULL),(725,'2025-10-26 01:10:09',28,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-26 01:10:09','SISTEMA',NULL,NULL),(726,'2025-10-26 01:10:44',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:10:44','SISTEMA',NULL,NULL),(727,'2025-10-26 01:10:44',28,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-26 01:10:44','SISTEMA',NULL,NULL),(728,'2025-10-26 01:11:26',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:11:26','SISTEMA',NULL,NULL),(729,'2025-10-26 01:11:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:11:37','SISTEMA',NULL,NULL),(730,'2025-10-26 01:11:37',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:11:37','Nuevo Usuario del Sistema',NULL,NULL),(731,'2025-10-26 01:19:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:19:10','SISTEMA',NULL,NULL),(732,'2025-10-26 01:19:10',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:19:10','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(733,'2025-10-26 01:20:44',23,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-26 01:20:44','ADMIN',NULL,NULL),(734,'2025-10-26 01:20:58',23,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-26 01:20:58','SISTEMA',NULL,NULL),(735,'2025-10-26 01:22:05',7,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 01:22:05','ADMIN',NULL,NULL),(736,'2025-10-26 01:36:21',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 01:36:21','SISTEMA',NULL,NULL),(737,'2025-10-26 01:36:21',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 01:36:21','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(738,'2025-10-26 01:37:01',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-26 01:37:01','SISTEMA',NULL,NULL),(739,'2025-10-26 01:37:10',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-26 01:37:10','ADMIN',NULL,NULL),(740,'2025-10-26 01:37:41',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 01:37:41','ADMIN',NULL,NULL),(741,'2025-10-26 12:30:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:30:49','SISTEMA',NULL,NULL),(742,'2025-10-26 12:30:49',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:30:49','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(743,'2025-10-26 12:31:41',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:31:41','SISTEMA',NULL,NULL),(744,'2025-10-26 12:31:49',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:31:49','SISTEMA',NULL,NULL),(745,'2025-10-26 12:31:49',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:31:49','Nuevo Usuario del Sistema',NULL,NULL),(746,'2025-10-26 12:34:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:34:31','SISTEMA',NULL,NULL),(747,'2025-10-26 12:34:31',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:34:31','Nuevo Usuario del Sistema',NULL,NULL),(748,'2025-10-26 12:39:53',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:39:53','SISTEMA',NULL,NULL),(749,'2025-10-26 12:39:53',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:39:53','Nuevo Usuario del Sistema',NULL,NULL),(750,'2025-10-26 12:43:17',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:43:17','SISTEMA',NULL,NULL),(751,'2025-10-26 12:43:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:43:23','SISTEMA',NULL,NULL),(752,'2025-10-26 12:43:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:43:30','SISTEMA',NULL,NULL),(753,'2025-10-26 12:45:34',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:45:34','SISTEMA',NULL,NULL),(754,'2025-10-26 12:45:34',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:45:34','Nuevo Usuario del Sistema',NULL,NULL),(755,'2025-10-26 12:52:50',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:52:50','SISTEMA',NULL,NULL),(756,'2025-10-26 12:52:50',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:52:50','Nuevo Usuario del Sistema',NULL,NULL),(757,'2025-10-26 12:55:37',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:55:37','SISTEMA',NULL,NULL),(758,'2025-10-26 12:55:37',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:55:37','Nuevo Usuario del Sistema',NULL,NULL),(759,'2025-10-26 12:57:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:57:00','SISTEMA',NULL,NULL),(760,'2025-10-26 12:57:00',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:57:00','Nuevo Usuario del Sistema',NULL,NULL),(761,'2025-10-26 12:57:47',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:57:47','SISTEMA',NULL,NULL),(762,'2025-10-26 12:57:47',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:57:47','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(763,'2025-10-26 12:58:56',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 12:58:56','SISTEMA',NULL,NULL),(764,'2025-10-26 12:58:56',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 12:58:56','Nuevo Usuario del Sistema',NULL,NULL),(765,'2025-10-26 13:01:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:01:12','SISTEMA',NULL,NULL),(766,'2025-10-26 13:01:12',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:01:12','Nuevo Usuario del Sistema',NULL,NULL),(767,'2025-10-26 13:01:46',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:01:46','SISTEMA',NULL,NULL),(768,'2025-10-26 13:01:46',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:01:46','Nuevo Usuario del Sistema',NULL,NULL),(769,'2025-10-26 13:29:33',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:29:33','SISTEMA',NULL,NULL),(770,'2025-10-26 13:29:40',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:29:40','SISTEMA',NULL,NULL),(771,'2025-10-26 13:32:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:32:23','SISTEMA',NULL,NULL),(772,'2025-10-26 13:32:23',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:32:23','Nuevo Usuario del Sistema',NULL,NULL),(773,'2025-10-26 13:32:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:32:23','SISTEMA',NULL,NULL),(774,'2025-10-26 13:32:23',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:32:23','Nuevo Usuario del Sistema',NULL,NULL),(775,'2025-10-26 13:32:26',2,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-26 13:32:26','SISTEMA',NULL,NULL),(776,'2025-10-26 13:33:18',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:33:18','SISTEMA',NULL,NULL),(777,'2025-10-26 13:33:18',2,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-26 13:33:18','SISTEMA',NULL,NULL),(778,'2025-10-26 13:34:40',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:34:40','SISTEMA',NULL,NULL),(779,'2025-10-26 13:34:40',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-26 13:34:40','SISTEMA',NULL,NULL),(780,'2025-10-26 13:35:25',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:35:25','SISTEMA',NULL,NULL),(781,'2025-10-26 13:35:25',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:35:25','Nuevo Usuario del Sistema',NULL,NULL),(782,'2025-10-26 13:36:17',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 13:36:17','SISTEMA',NULL,NULL),(783,'2025-10-26 13:36:17',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 13:36:17','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(784,'2025-10-26 14:08:36',10,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-26 14:08:36','SISTEMA',NULL,NULL),(785,'2025-10-26 14:10:59',10,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 14:10:59','ADMIN',NULL,NULL),(786,'2025-10-26 14:12:36',10,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 14:12:36','ADMIN',NULL,NULL),(787,'2025-10-26 14:15:12',29,2,'CREAR_USUARIO','Usuario creado: FRANCIS. Días vigencia: 360','2025-10-26 14:15:12','ADMIN',NULL,NULL),(788,'2025-10-26 14:17:00',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 14:17:00','SISTEMA',NULL,NULL),(789,'2025-10-26 14:21:02',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 14:21:02','SISTEMA',NULL,NULL),(790,'2025-10-26 14:21:11',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 14:21:11','SISTEMA',NULL,NULL),(791,'2025-10-26 14:27:36',10,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 14:27:36','ADMIN',NULL,NULL),(792,'2025-10-26 15:17:41',30,2,'CREAR_USUARIO','Usuario creado: MARITZA. Días vigencia: 360','2025-10-26 15:17:41','ADMIN',NULL,NULL),(793,'2025-10-26 15:24:41',31,2,'CREAR_USUARIO','Usuario creado: MURILLO. Días vigencia: 360','2025-10-26 15:24:41','ADMIN',NULL,NULL),(794,'2025-10-26 15:27:31',32,2,'CREAR_USUARIO','Usuario creado: ILMA. Días vigencia: 360','2025-10-26 15:27:31','ADMIN',NULL,NULL),(795,'2025-10-26 15:28:30',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 15:28:30','SISTEMA',NULL,NULL),(796,'2025-10-26 15:28:30',32,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 15:28:30','ILMA ONEYDA',NULL,NULL),(797,'2025-10-26 15:28:31',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 15:28:31','SISTEMA',NULL,NULL),(798,'2025-10-26 15:28:31',32,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 15:28:31','ILMA ONEYDA',NULL,NULL),(799,'2025-10-26 15:28:33',32,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-26 15:28:33','SISTEMA',NULL,NULL),(800,'2025-10-26 15:30:23',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 15:30:23','SISTEMA',NULL,NULL),(801,'2025-10-26 15:30:23',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 15:30:23','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(802,'2025-10-26 15:37:12',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 15:37:12','SISTEMA',NULL,NULL),(803,'2025-10-26 15:37:13',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 15:37:13','Nuevo Usuario del Sistema',NULL,NULL),(804,'2025-10-26 17:07:32',5,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-26 17:07:32','SISTEMA',NULL,NULL),(805,'2025-10-26 17:08:20',5,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-26 17:08:20','ADMIN',NULL,NULL),(806,'2025-10-26 17:59:24',33,7,'REGISTRO_USUARIO','Usuario registrado: ALVARO','2025-10-26 17:59:24','REGISTRO',NULL,NULL),(807,'2025-10-26 18:06:10',1,NULL,'TEST_DIAGNOSTICO','Diagnóstico de bitácora desde authController','2025-10-26 18:06:10','SISTEMA',NULL,NULL),(808,'2025-10-26 18:06:10',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 18:06:10','Nuevo Usuario del Sistema',NULL,NULL),(809,'2025-10-26 19:29:05',1,1,'LOGIN','Inicio de sesión exitoso','2025-10-26 19:29:05','Administrador del Sistema',NULL,NULL),(810,'2025-10-26 19:29:07',1,1,'ACCESO_PAGINA','Ingresó a la página de Inicio','2025-10-26 19:29:07','SISTEMA',NULL,NULL),(811,'2025-10-26 19:29:12',1,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-26 19:29:12','SISTEMA',NULL,NULL),(812,'2025-10-26 19:29:12',1,4,'ACCESO_PAGINA','Consultó la bitácora del sistema','2025-10-26 19:29:12','SISTEMA',NULL,NULL),(813,'2025-10-26 19:29:33',1,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-26 19:29:33','SISTEMA',NULL,NULL),(814,'2025-10-26 19:29:33',1,1,'ACCESO_PAGINA','Ingresó a la página de Inicio','2025-10-26 19:29:33','SISTEMA',NULL,NULL),(815,'2025-10-26 19:29:36',1,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-26 19:29:36','SISTEMA',NULL,NULL),(816,'2025-10-26 19:29:36',1,2,'ACCESO_PAGINA','Accedió a la gestión de usuarios registrados','2025-10-26 19:29:36','SISTEMA',NULL,NULL),(817,'2025-10-26 19:29:39',1,6,'NAVEGACION','Accedió a cambiar contraseña','2025-10-26 19:29:39','SISTEMA',NULL,NULL),(818,'2025-10-26 19:29:42',1,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-26 19:29:42','SISTEMA',NULL,NULL),(819,'2025-10-26 19:29:42',1,4,'ACCESO_PAGINA','Consultó la bitácora del sistema','2025-10-26 19:29:42','SISTEMA',NULL,NULL),(820,'2025-10-26 19:33:26',1,4,'ACCESO_PAGINA','Consultó la bitácora del sistema','2025-10-26 19:33:26','SISTEMA',NULL,NULL),(821,'2025-10-26 19:33:28',1,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-26 19:33:28','SISTEMA',NULL,NULL),(822,'2025-10-26 19:33:28',1,1,'ACCESO_PAGINA','Ingresó a la página de Inicio','2025-10-26 19:33:28','SISTEMA',NULL,NULL),(823,'2025-10-26 19:33:31',1,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-26 19:33:31','SISTEMA',NULL,NULL),(824,'2025-10-26 19:33:31',1,4,'ACCESO_PAGINA','Consultó la bitácora del sistema','2025-10-26 19:33:31','SISTEMA',NULL,NULL),(825,'2025-10-26 19:33:52',1,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-26 19:33:52','SISTEMA',NULL,NULL),(826,'2025-10-26 19:33:54',1,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-26 19:33:54','SISTEMA',NULL,NULL),(827,'2025-10-26 19:34:05',1,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-26 19:34:05','SISTEMA',NULL,NULL),(828,'2025-10-26 19:34:09',1,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-26 19:34:09','SISTEMA',NULL,NULL),(829,'2025-10-27 07:07:51',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 07:07:51','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(830,'2025-10-27 07:08:03',28,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-27 07:08:03','SISTEMA',NULL,NULL),(831,'2025-10-27 07:08:12',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-27 07:08:12','SISTEMA',NULL,NULL),(832,'2025-10-27 07:08:34',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-27 07:08:34','SISTEMA',NULL,NULL),(833,'2025-10-27 07:08:42',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-27 07:08:42','ADMIN',NULL,NULL),(834,'2025-10-27 07:08:50',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-27 07:08:50','SISTEMA',NULL,NULL),(835,'2025-10-27 07:09:00',21,2,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-27 07:09:00','ADMIN',NULL,NULL),(836,'2025-10-27 07:09:08',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 07:09:08','SISTEMA',NULL,NULL),(837,'2025-10-27 07:10:45',32,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 07:10:45','ILMA ONEYDA',NULL,NULL),(838,'2025-10-27 07:10:45',32,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 07:10:45','ILMA ONEYDA',NULL,NULL),(839,'2025-10-27 07:10:47',32,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-27 07:10:47','SISTEMA',NULL,NULL),(840,'2025-10-27 07:11:14',32,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-27 07:11:14','SISTEMA',NULL,NULL),(841,'2025-10-27 07:12:02',32,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-27 07:12:02','SISTEMA',NULL,NULL),(842,'2025-10-27 07:12:31',32,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-27 07:12:31','SISTEMA',NULL,NULL),(843,'2025-10-27 07:13:48',32,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 07:13:48','SISTEMA',NULL,NULL),(844,'2025-10-27 07:13:48',32,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 07:13:48','SISTEMA',NULL,NULL),(845,'2025-10-27 07:14:24',32,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 07:14:24','SISTEMA',NULL,NULL),(846,'2025-10-27 07:14:27',32,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-27 07:14:27','SISTEMA',NULL,NULL),(847,'2025-10-27 07:14:45',32,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-27 07:14:45','SISTEMA',NULL,NULL),(848,'2025-10-27 08:41:35',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 08:41:35','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(849,'2025-10-27 08:42:08',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 08:42:08','SISTEMA',NULL,NULL),(850,'2025-10-27 08:42:19',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 08:42:19','SISTEMA',NULL,NULL),(851,'2025-10-27 08:42:52',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 08:42:52','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(852,'2025-10-27 08:44:08',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 08:44:08','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(853,'2025-10-27 09:27:45',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 09:27:45','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(854,'2025-10-27 15:34:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-27 15:34:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(855,'2025-10-27 15:39:11',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-27 15:39:11','SISTEMA',NULL,NULL),(856,'2025-10-27 15:39:24',28,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-27 15:39:24','SISTEMA',NULL,NULL),(857,'2025-10-27 17:08:54',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-27 17:08:54','SISTEMA',NULL,NULL),(858,'2025-10-27 17:09:03',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-27 17:09:03','SISTEMA',NULL,NULL),(859,'2025-10-27 17:09:08',28,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-27 17:09:08','SISTEMA',NULL,NULL),(860,'2025-10-27 17:11:12',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-27 17:11:12','SISTEMA',NULL,NULL),(861,'2025-10-27 22:06:47',1,20,'INSERCION','Compra registrada #16 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 0.26','2025-10-27 22:06:47','SISTEMA',NULL,NULL),(862,'2025-10-27 22:26:42',1,20,'INSERCION','Compra registrada #17 - Proveedor: Lácteos Honduras - Total: L 22.68','2025-10-27 22:26:42','SISTEMA',NULL,NULL),(863,'2025-10-27 23:56:27',1,20,'INSERCION','Compra registrada #18 - Proveedor: Lácteos Honduras - Total: L 121.90','2025-10-27 23:56:27','SISTEMA',NULL,NULL),(864,'2025-10-28 00:55:12',1,20,'INSERCION','Compra registrada #19 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 22.68','2025-10-28 00:55:12','SISTEMA',NULL,NULL),(865,'2025-10-28 00:59:55',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 00:59:55','SISTEMA',NULL,NULL),(866,'2025-10-28 01:45:15',1,20,'INSERCION','Compra registrada #20 - Proveedor: Dulcería La Esperanza - Total: L 540.00','2025-10-28 01:45:15','SISTEMA',NULL,NULL),(867,'2025-10-28 02:02:56',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 02:02:56','SISTEMA',NULL,NULL),(868,'2025-10-28 02:03:13',28,6,'NAVEGACION','Accedió a cambiar contraseña','2025-10-28 02:03:13','SISTEMA',NULL,NULL),(869,'2025-10-28 03:02:10',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 03:02:10','SISTEMA',NULL,NULL),(870,'2025-10-28 03:02:12',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-28 03:02:12','SISTEMA',NULL,NULL),(871,'2025-10-28 03:02:15',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 03:02:15','SISTEMA',NULL,NULL),(872,'2025-10-28 03:04:16',33,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-28 03:04:16','ADMIN',NULL,NULL),(873,'2025-10-28 03:14:54',1,20,'INSERCION','Compra registrada #21 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 205.00','2025-10-28 03:14:54','SISTEMA',NULL,NULL),(874,'2025-10-28 03:15:11',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 03:15:11','SISTEMA',NULL,NULL),(875,'2025-10-28 03:15:24',33,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-28 03:15:24','SISTEMA',NULL,NULL),(876,'2025-10-28 03:15:36',33,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-28 03:15:36','ADMIN',NULL,NULL),(877,'2025-10-28 03:22:02',28,6,'NAVEGACION','Accedió a cambiar contraseña','2025-10-28 03:22:02','SISTEMA',NULL,NULL),(878,'2025-10-28 03:25:53',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 03:25:53','SISTEMA',NULL,NULL),(879,'2025-10-28 03:43:23',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 03:43:23','SISTEMA',NULL,NULL),(880,'2025-10-28 04:13:46',1,20,'INSERCION','Compra registrada #22 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 77.50','2025-10-28 04:13:46','SISTEMA',NULL,NULL),(881,'2025-10-28 04:38:12',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 04:38:12','SISTEMA',NULL,NULL),(882,'2025-10-28 04:38:43',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 04:38:43','SISTEMA',NULL,NULL),(883,'2025-10-28 06:51:40',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 06:51:40','SISTEMA',NULL,NULL),(884,'2025-10-28 06:54:52',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 06:54:52','SISTEMA',NULL,NULL),(885,'2025-10-28 07:04:37',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 07:04:37','SISTEMA',NULL,NULL),(886,'2025-10-28 07:04:39',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-28 07:04:39','SISTEMA',NULL,NULL),(887,'2025-10-28 07:04:41',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 07:04:41','SISTEMA',NULL,NULL),(888,'2025-10-28 07:07:13',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 07:07:13','SISTEMA',NULL,NULL),(889,'2025-10-28 07:07:21',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-28 07:07:21','SISTEMA',NULL,NULL),(890,'2025-10-28 07:07:31',33,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-28 07:07:31','ADMIN',NULL,NULL),(891,'2025-10-28 07:07:54',5,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-28 07:07:54','ADMIN',NULL,NULL),(892,'2025-10-28 16:46:54',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 16:46:54','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(893,'2025-10-28 17:24:34',1,19,'INSERT','Registró nueva materia prima: Quesillo','2025-10-28 17:24:34','SISTEMA',NULL,NULL),(894,'2025-10-28 17:52:32',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 17:52:32','SISTEMA',NULL,NULL),(895,'2025-10-28 17:52:34',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-28 17:52:34','SISTEMA',NULL,NULL),(896,'2025-10-28 17:52:38',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 17:52:38','SISTEMA',NULL,NULL),(897,'2025-10-28 17:56:24',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 17:56:24','SISTEMA',NULL,NULL),(898,'2025-10-28 18:02:33',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 18:02:33','SISTEMA',NULL,NULL),(899,'2025-10-28 18:03:24',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 18:03:24','SISTEMA',NULL,NULL),(900,'2025-10-28 18:12:54',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 18:12:54','SISTEMA',NULL,NULL),(901,'2025-10-28 18:13:04',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-28 18:13:04','SISTEMA',NULL,NULL),(902,'2025-10-28 18:13:11',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-28 18:13:11','ADMIN',NULL,NULL),(903,'2025-10-28 18:13:18',21,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-28 18:13:18','ADMIN',NULL,NULL),(904,'2025-10-28 20:47:06',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 20:47:06','SISTEMA',NULL,NULL),(905,'2025-10-28 21:20:18',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 21:20:18','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(906,'2025-10-28 21:26:01',1,20,'INSERCION','Compra registrada #23 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 602.60','2025-10-28 21:26:01','SISTEMA',NULL,NULL),(907,'2025-10-28 21:28:25',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 21:28:25','SISTEMA',NULL,NULL),(908,'2025-10-28 21:30:39',34,7,'REGISTRO_USUARIO','Usuario registrado: DENISWA','2025-10-28 21:30:39','REGISTRO',NULL,NULL),(909,'2025-10-28 21:31:59',34,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 21:31:59','DEDE DEDE DEDE',NULL,NULL),(910,'2025-10-28 21:31:59',34,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 21:31:59','DEDE DEDE DEDE',NULL,NULL),(911,'2025-10-28 21:32:03',34,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-28 21:32:03','SISTEMA',NULL,NULL),(912,'2025-10-28 21:32:44',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 21:32:44','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(913,'2025-10-28 21:36:17',28,6,'NAVEGACION','Accedió a cambiar contraseña','2025-10-28 21:36:17','SISTEMA',NULL,NULL),(914,'2025-10-28 21:40:49',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-28 21:40:49','SISTEMA',NULL,NULL),(915,'2025-10-28 21:41:03',33,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-28 21:41:03','SISTEMA',NULL,NULL),(916,'2025-10-28 21:41:12',5,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-28 21:41:12','ADMIN',NULL,NULL),(917,'2025-10-28 21:41:20',5,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-28 21:41:20','ADMIN',NULL,NULL),(918,'2025-10-28 21:41:43',28,6,'NAVEGACION','Accedió a cambiar contraseña','2025-10-28 21:41:43','SISTEMA',NULL,NULL),(919,'2025-10-28 21:53:09',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 21:53:09','SISTEMA',NULL,NULL),(920,'2025-10-28 22:28:26',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 22:28:26','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(921,'2025-10-28 22:34:48',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-28 22:34:48','SISTEMA',NULL,NULL),(922,'2025-10-28 22:45:50',35,7,'REGISTRO_USUARIO','Usuario registrado: DEDEERERS','2025-10-28 22:45:50','REGISTRO',NULL,NULL),(923,'2025-10-28 22:59:40',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 22:59:40','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(924,'2025-10-28 23:23:03',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 23:23:03','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(925,'2025-10-28 23:25:33',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 23:25:33','Nuevo Usuario del Sistema',NULL,NULL),(926,'2025-10-28 23:25:33',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 23:25:33','Nuevo Usuario del Sistema',NULL,NULL),(927,'2025-10-28 23:25:36',2,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-28 23:25:36','SISTEMA',NULL,NULL),(928,'2025-10-28 23:27:47',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-28 23:27:47','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(929,'2025-10-29 00:18:16',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 00:18:16','SISTEMA',NULL,NULL),(930,'2025-10-29 00:19:15',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 00:19:15','SISTEMA',NULL,NULL),(931,'2025-10-29 01:38:14',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 01:38:14','SISTEMA',NULL,NULL),(932,'2025-10-29 01:58:27',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-29 01:58:27','SISTEMA',NULL,NULL),(933,'2025-10-29 03:05:05',1,20,'INSERCION','Compra registrada #24 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 114.40','2025-10-29 03:05:05','SISTEMA',NULL,NULL),(934,'2025-10-29 03:50:19',1,20,'INSERCION','Compra registrada #25 - Proveedor: Lácteos Honduras - Total: L 45.36','2025-10-29 03:50:19','SISTEMA',NULL,NULL),(935,'2025-10-29 04:17:43',1,20,'INSERCION','Compra registrada #28 - Proveedor: Lácteos Honduras - Total: L 56.70','2025-10-29 04:17:43','SISTEMA',NULL,NULL),(936,'2025-10-29 04:19:03',1,20,'INSERCION','Compra registrada #29 - Proveedor: Lácteos Honduras - Total: L 65.20','2025-10-29 04:19:03','SISTEMA',NULL,NULL),(937,'2025-10-29 05:01:44',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 05:01:44','SISTEMA',NULL,NULL),(938,'2025-10-29 05:54:48',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 05:54:48','SISTEMA',NULL,NULL),(939,'2025-10-29 05:54:50',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-29 05:54:50','SISTEMA',NULL,NULL),(940,'2025-10-29 05:54:53',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 05:54:53','SISTEMA',NULL,NULL),(941,'2025-10-29 06:45:19',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 06:45:19','SISTEMA',NULL,NULL),(942,'2025-10-29 06:45:34',28,4,'NAVEGACION','Consultó la bitácora del sistema','2025-10-29 06:45:34','SISTEMA',NULL,NULL),(943,'2025-10-29 06:45:37',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 06:45:37','SISTEMA',NULL,NULL),(944,'2025-10-29 08:41:23',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-29 08:41:23','SISTEMA',NULL,NULL),(945,'2025-10-29 08:41:29',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 08:41:29','SISTEMA',NULL,NULL),(946,'2025-10-29 09:12:18',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 09:12:18','SISTEMA',NULL,NULL),(947,'2025-10-29 09:13:12',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 09:13:12','SISTEMA',NULL,NULL),(948,'2025-10-29 10:41:37',28,1,'NAVEGACION','Ingresó a la página de Inicio','2025-10-29 10:41:37','SISTEMA',NULL,NULL),(949,'2025-10-29 10:59:28',36,7,'REGISTRO_USUARIO','Usuario registrado: MIRNA','2025-10-29 10:59:28','REGISTRO',NULL,NULL),(950,'2025-10-29 11:20:54',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 11:20:54','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(951,'2025-10-29 11:29:21',28,1,'NAVEGACION','Accedió al Dashboard principal','2025-10-29 11:29:21','SISTEMA',NULL,NULL),(952,'2025-10-29 11:29:26',28,2,'NAVEGACION','Accedió a la gestión de usuarios registrados','2025-10-29 11:29:26','SISTEMA',NULL,NULL),(953,'2025-10-29 16:37:33',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 16:37:33','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(954,'2025-10-29 16:47:28',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-10-29 16:47:28','ADMIN',NULL,NULL),(955,'2025-10-29 16:47:35',21,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-10-29 16:47:35','SISTEMA',NULL,NULL),(956,'2025-10-29 16:47:46',21,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-10-29 16:47:46','ADMIN',NULL,NULL),(957,'2025-10-29 16:56:59',1,20,'INSERCION','Compra registrada #30 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 250.00','2025-10-29 16:56:59','SISTEMA',NULL,NULL),(958,'2025-10-29 16:58:31',1,20,'INSERCION','Compra registrada #31 - Proveedor: Distribuidora de Alimentos S.A. - Total: L 52.00','2025-10-29 16:58:31','SISTEMA',NULL,NULL),(959,'2025-10-29 17:00:58',1,19,'INSERT','Registró nueva materia prima: Cuajada','2025-10-29 17:00:58','SISTEMA',NULL,NULL),(960,'2025-10-29 17:03:26',10,5,'SOLICITUD_RECUPERACI','Solicitud de recuperación por correo. Expira: 2025-10-30 17:03:26','2025-10-29 17:03:26','SISTEMA_RECUPERACION',NULL,NULL),(961,'2025-10-29 17:03:29',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada a: denislo6@gmail.com','2025-10-29 17:03:29','SISTEMA',NULL,NULL),(962,'2025-10-29 17:03:29',10,5,'RECUPERACION_CORREO_','Contraseña temporal enviada por correo a: denislo6@gmail.com','2025-10-29 17:03:29','SISTEMA',NULL,NULL),(963,'2025-10-29 17:12:05',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 17:12:05','Nuevo Usuario del Sistema',NULL,NULL),(964,'2025-10-29 17:12:05',2,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 17:12:05','Nuevo Usuario del Sistema',NULL,NULL),(965,'2025-10-29 17:12:07',2,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-10-29 17:12:07','SISTEMA',NULL,NULL),(966,'2025-10-29 17:12:36',2,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-10-29 17:12:36','SISTEMA',NULL,NULL),(967,'2025-10-29 17:13:39',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-10-29 17:13:39','SISTEMA',NULL,NULL),(968,'2025-10-29 21:28:42',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 21:28:42','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(969,'2025-10-29 23:38:18',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-29 23:38:18','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(970,'2025-10-30 20:29:24',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-30 20:29:24','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(971,'2025-10-30 21:33:32',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-30 21:33:32','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(972,'2025-10-31 14:14:50',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-31 14:14:50','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(973,'2025-10-31 18:46:19',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-31 18:46:19','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(974,'2025-10-31 21:45:25',28,1,'LOGIN','Inicio de sesión exitoso','2025-10-31 21:45:25','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(975,'2025-10-31 23:34:27',1,20,'UPDATE','Editó materia prima: Harina de maíz → Harina de maíz (ID: 21)','2025-10-31 23:34:27','SISTEMA',NULL,NULL),(976,'2025-11-01 00:29:17',1,19,'INSERT','Registró nuevo producto de proveedor: Manteca','2025-11-01 00:29:17','ADMIN',NULL,NULL),(977,'2025-11-01 02:46:01',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-01 02:46:01','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(978,'2025-11-01 03:14:17',13,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-11-01 03:14:17','ADMIN',NULL,NULL),(979,'2025-11-01 03:14:45',13,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-11-01 03:14:45','SISTEMA',NULL,NULL),(980,'2025-11-01 03:14:58',33,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-11-01 03:14:58','ADMIN',NULL,NULL),(981,'2025-11-01 07:36:59',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-01 07:36:59','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(982,'2025-11-01 08:16:13',1,19,'INSERT','Registró nuevo producto de proveedor: Harina para repostería','2025-11-01 08:16:13','ADMIN',NULL,NULL),(983,'2025-11-01 08:18:08',1,19,'INSERT','Registró nuevo producto de proveedor: Harina de trigo todo uso','2025-11-01 08:18:08','ADMIN',NULL,NULL),(984,'2025-11-01 08:20:54',1,19,'INSERT','Registró nuevo producto de proveedor: Polvo de hornear','2025-11-01 08:20:54','ADMIN',NULL,NULL),(985,'2025-11-01 08:35:22',1,19,'INSERT','Registró nuevo producto de proveedor: Azúcar glass','2025-11-01 08:35:22','ADMIN',NULL,NULL),(986,'2025-11-01 08:36:34',1,19,'INSERT','Registró nuevo producto de proveedor: Canela en rama','2025-11-01 08:36:34','ADMIN',NULL,NULL),(987,'2025-11-01 08:37:23',1,19,'INSERT','Registró nuevo producto de proveedor: Esencia de anís','2025-11-01 08:37:23','ADMIN',NULL,NULL),(988,'2025-11-01 08:38:09',1,19,'INSERT','Registró nuevo producto de proveedor: Colorante en gel','2025-11-01 08:38:09','ADMIN',NULL,NULL),(989,'2025-11-01 08:39:20',1,19,'INSERT','Registró nuevo producto de proveedor: Chocolate para cobertura','2025-11-01 08:39:20','ADMIN',NULL,NULL),(990,'2025-11-01 08:39:59',1,19,'INSERT','Registró nuevo producto de proveedor: Cacao en polvo','2025-11-01 08:39:59','ADMIN',NULL,NULL),(991,'2025-11-01 08:41:04',1,19,'INSERT','Registró nuevo producto de proveedor: Chispas de chocolate','2025-11-01 08:41:04','ADMIN',NULL,NULL),(992,'2025-11-01 08:42:22',1,19,'INSERT','Registró nuevo producto de proveedor: Aceite vegetal','2025-11-01 08:42:22','ADMIN',NULL,NULL),(993,'2025-11-01 08:43:06',1,19,'INSERT','Registró nuevo producto de proveedor: Sal refinada','2025-11-01 08:43:06','ADMIN',NULL,NULL),(994,'2025-11-01 08:44:02',1,19,'INSERT','Registró nuevo producto de proveedor: Huevos extra grandes','2025-11-01 08:44:02','ADMIN',NULL,NULL),(995,'2025-11-01 08:44:58',1,19,'INSERT','Registró nuevo producto de proveedor: Leche evaporada','2025-11-01 08:44:58','ADMIN',NULL,NULL),(996,'2025-11-01 08:45:49',1,19,'INSERT','Registró nuevo producto de proveedor: Nuez moscada molida','2025-11-01 08:45:49','ADMIN',NULL,NULL),(997,'2025-11-01 08:46:46',1,19,'INSERT','Registró nuevo producto de proveedor: Jengibre en polvo','2025-11-01 08:46:46','ADMIN',NULL,NULL),(998,'2025-11-01 08:48:03',1,19,'INSERT','Registró nuevo producto de proveedor: Extracto de almendra','2025-11-01 08:48:03','ADMIN',NULL,NULL),(999,'2025-11-01 09:01:02',1,20,'UPDATE','Editó materia prima: Aceite vegetal → Aceite vegetal (ID: 29)','2025-11-01 09:01:02','SISTEMA',NULL,NULL),(1000,'2025-11-01 09:01:41',1,20,'UPDATE','Editó materia prima: Azúcar glass → Azúcar glass (ID: 25)','2025-11-01 09:01:41','SISTEMA',NULL,NULL),(1001,'2025-11-01 09:02:05',1,20,'UPDATE','Editó materia prima: Cacao en polvo → Cacao en polvo (ID: 27)','2025-11-01 09:02:05','SISTEMA',NULL,NULL),(1002,'2025-11-01 09:03:07',1,20,'UPDATE','Editó materia prima: Chispas de chocolate → Chispas de chocolate (ID: 28)','2025-11-01 09:03:07','SISTEMA',NULL,NULL),(1003,'2025-11-01 09:03:40',1,20,'UPDATE','Editó materia prima: Harina para repostería → Harina para repostería (ID: 23)','2025-11-01 09:03:40','SISTEMA',NULL,NULL),(1004,'2025-11-01 09:04:12',1,20,'UPDATE','Editó materia prima: Huevos blancos → Huevos blancos (ID: 22)','2025-11-01 09:04:12','SISTEMA',NULL,NULL),(1005,'2025-11-01 09:04:36',1,20,'UPDATE','Editó materia prima: Huevos extra grandes → Huevos extra grandes (ID: 31)','2025-11-01 09:04:36','SISTEMA',NULL,NULL),(1006,'2025-11-02 03:00:42',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 03:00:42','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1007,'2025-11-02 03:08:57',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 03:08:57','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1008,'2025-11-02 04:12:28',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:12:28','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1009,'2025-11-02 04:41:26',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:41:26','Nuevo Usuario del Sistema',NULL,NULL),(1010,'2025-11-02 04:44:46',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:44:46','Nuevo Usuario del Sistema',NULL,NULL),(1011,'2025-11-02 04:47:39',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:47:39','Nuevo Usuario del Sistema',NULL,NULL),(1012,'2025-11-02 04:47:39',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:47:39','Nuevo Usuario del Sistema',NULL,NULL),(1013,'2025-11-02 04:47:43',2,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-11-02 04:47:43','SISTEMA',NULL,NULL),(1014,'2025-11-02 04:48:22',2,NULL,'2FA_VERIFICADO','Autenticación en dos pasos completada exitosamente','2025-11-02 04:48:22','SISTEMA',NULL,NULL),(1015,'2025-11-02 04:48:57',2,6,'CAMBIAR_CONTRASENA','Contraseña cambiada exitosamente','2025-11-02 04:48:57','SISTEMA',NULL,NULL),(1016,'2025-11-02 04:50:49',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:50:49','Nuevo Usuario del Sistema',NULL,NULL),(1017,'2025-11-02 04:54:29',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 04:54:29','Nuevo Usuario del Sistema',NULL,NULL),(1020,'2025-11-02 05:59:43',2,23,'CREAR_PRODUCCION','Orden producción #5 - Cantidad: 20','2025-11-02 05:59:43','Nuevo Usuario del Sistema',NULL,NULL),(1021,'2025-11-02 06:33:12',2,23,'CREAR_PRODUCCION','Creó orden de producción #6 - Producto: Rosquilla Clásica - Cantidad: 2','2025-11-02 06:33:12','Nuevo Usuario del Sistema',NULL,NULL),(1022,'2025-11-02 06:41:48',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 06:41:48','Nuevo Usuario del Sistema',NULL,NULL),(1023,'2025-11-02 08:31:46',2,23,'INICIAR_PRODUCCION','Inició producción #5 - Producto: Rosquilla Clásica - Cantidad: 20.00','2025-11-02 08:31:46','Nuevo Usuario del Sistema',NULL,NULL),(1024,'2025-11-02 13:17:48',2,23,'FINALIZAR_PRODUCCION','Finalizó producción #5 - Producto: Rosquilla Clásica - Buenas: 15.00 - Pérdidas: 5.00 - Costo: L.73.','2025-11-02 13:17:48','Nuevo Usuario del Sistema',NULL,NULL),(1025,'2025-11-02 14:16:08',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 14:16:08','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1027,'2025-11-02 17:48:36',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-02 17:48:36','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1028,'2025-11-03 23:40:41',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-03 23:40:41','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1029,'2025-11-04 00:07:20',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 00:07:20','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1030,'2025-11-04 00:35:02',1,19,'INSERT','Registró nuevo producto de proveedor: Mantequilla crema','2025-11-04 00:35:02','ADMIN',NULL,NULL),(1031,'2025-11-04 01:37:45',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 01:37:45','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1032,'2025-11-04 01:38:25',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 01:38:25','Nuevo Usuario del Sistema',NULL,NULL),(1033,'2025-11-04 01:38:29',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 01:38:29','Nuevo Usuario del Sistema',NULL,NULL),(1034,'2025-11-04 01:46:13',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 01:46:13','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1035,'2025-11-04 02:37:56',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 02:37:56','SISTEMA',NULL,NULL),(1036,'2025-11-04 02:42:41',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 02:42:41','SISTEMA',NULL,NULL),(1037,'2025-11-04 02:56:56',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 02:56:56','SISTEMA',NULL,NULL),(1038,'2025-11-04 02:57:33',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 02:57:33','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1039,'2025-11-04 02:58:10',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 02:58:10','SISTEMA',NULL,NULL),(1040,'2025-11-04 03:02:05',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:02:05','SISTEMA',NULL,NULL),(1041,'2025-11-04 03:03:54',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:03:54','SISTEMA',NULL,NULL),(1042,'2025-11-04 03:06:22',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:06:22','SISTEMA',NULL,NULL),(1043,'2025-11-04 03:09:56',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:09:56','SISTEMA',NULL,NULL),(1044,'2025-11-04 03:10:43',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 03:10:43','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1045,'2025-11-04 03:11:31',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:11:31','SISTEMA',NULL,NULL),(1046,'2025-11-04 03:15:48',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:15:48','SISTEMA',NULL,NULL),(1047,'2025-11-04 03:15:52',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:15:52','SISTEMA',NULL,NULL),(1048,'2025-11-04 03:17:25',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:17:25','SISTEMA',NULL,NULL),(1049,'2025-11-04 03:18:14',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:18:14','SISTEMA',NULL,NULL),(1050,'2025-11-04 03:19:05',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:19:05','SISTEMA',NULL,NULL),(1051,'2025-11-04 03:25:20',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:25:20','SISTEMA',NULL,NULL),(1052,'2025-11-04 03:28:31',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:28:31','SISTEMA',NULL,NULL),(1053,'2025-11-04 03:29:06',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 03:29:06','SISTEMA',NULL,NULL),(1054,'2025-11-04 03:32:04',37,2,'CREAR_USUARIO','Usuario creado: MANUEL. Días vigencia: 360','2025-11-04 03:32:04','ADMIN',NULL,NULL),(1055,'2025-11-04 03:32:39',17,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-11-04 03:32:39','SISTEMA',NULL,NULL),(1056,'2025-11-04 03:33:03',17,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-11-04 03:33:03','ADMIN',NULL,NULL),(1057,'2025-11-04 03:33:16',17,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-11-04 03:33:16','ADMIN',NULL,NULL),(1058,'2025-11-04 03:44:19',1,19,'INSERT','Registró nuevo producto de proveedor: Levadura química','2025-11-04 03:44:19','ADMIN',NULL,NULL),(1059,'2025-11-04 03:53:01',1,20,'UPDATE','Editó materia prima: Aceite vegetal → Aceite vegetal (ID: 29)','2025-11-04 03:53:01','SISTEMA',NULL,NULL),(1060,'2025-11-04 03:55:25',28,23,'CREAR_PRODUCCION','Creó orden de producción #7 - Producto: Rosquilla Clásica - Cantidad: 22','2025-11-04 03:55:25','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1061,'2025-11-04 03:56:30',28,23,'INICIAR_PRODUCCION','Inició producción #7 - Producto: Rosquilla Clásica - Cantidad: 22.00','2025-11-04 03:56:30','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1062,'2025-11-04 03:59:36',28,23,'FINALIZAR_PRODUCCION','Finalizó producción #7 - Producto: Rosquilla Clásica - Buenas: 5.00 - Pérdidas: 17.00 - Costo: L.80.','2025-11-04 03:59:36','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1063,'2025-11-04 04:31:54',28,23,'CREAR_PRODUCCION','Creó orden de producción #8 - Producto: Rosquilla Glaseada - Cantidad: 2','2025-11-04 04:31:54','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1064,'2025-11-04 05:08:02',37,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 05:08:02','MANUEL ALEJANDRO TORRES',NULL,NULL),(1065,'2025-11-04 05:08:02',37,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 05:08:02','MANUEL ALEJANDRO TORRES',NULL,NULL),(1066,'2025-11-04 05:08:05',37,NULL,'2FA_INICIADO','Código 2FA enviado al correo','2025-11-04 05:08:05','SISTEMA',NULL,NULL),(1067,'2025-11-04 05:09:21',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 05:09:21','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1068,'2025-11-04 05:31:49',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 05:31:49','SISTEMA',NULL,NULL),(1069,'2025-11-04 05:32:11',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 05:32:11','SISTEMA',NULL,NULL),(1070,'2025-11-04 06:26:54',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 06:26:54','SISTEMA',NULL,NULL),(1071,'2025-11-04 06:27:30',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 06:27:30','SISTEMA',NULL,NULL),(1072,'2025-11-04 06:31:44',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 06:31:44','SISTEMA',NULL,NULL),(1073,'2025-11-04 09:20:49',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 09:20:49','Nuevo Usuario del Sistema',NULL,NULL),(1074,'2025-11-04 09:21:30',2,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 09:21:30','SISTEMA',NULL,NULL),(1075,'2025-11-04 09:54:25',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 09:54:25','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1076,'2025-11-04 15:43:48',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 15:43:48','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1077,'2025-11-04 15:44:25',28,NULL,'ACTUALIZAR_FOTO','Foto de perfil actualizada','2025-11-04 15:44:25','SISTEMA',NULL,NULL),(1078,'2025-11-04 23:14:09',1,14,'NAVEGACION','Accedió a gestión de inventario de productos','2025-11-04 23:14:09','SISTEMA',NULL,NULL),(1079,'2025-11-04 23:14:15',1,15,'NAVEGACION','Accedió a gestión de productos','2025-11-04 23:14:15','SISTEMA',NULL,NULL),(1080,'2025-11-05 02:53:53',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 02:53:53','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1081,'2025-11-05 02:57:44',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 02:57:44','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1082,'2025-11-05 03:04:19',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 03:04:19','Nuevo Usuario del Sistema',NULL,NULL),(1083,'2025-11-05 16:43:59',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 16:43:59','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1084,'2025-11-05 16:44:55',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 16:44:55','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1085,'2025-11-05 16:45:44',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 16:45:44','Nuevo Usuario del Sistema',NULL,NULL),(1086,'2025-11-05 16:45:59',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 16:45:59','Nuevo Usuario del Sistema',NULL,NULL),(1087,'2025-11-05 16:47:47',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 16:47:47','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1088,'2025-11-05 17:42:50',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 17:42:50','Nuevo Usuario del Sistema',NULL,NULL),(1089,'2025-11-05 18:17:45',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 18:17:45','Nuevo Usuario del Sistema',NULL,NULL),(1090,'2025-11-05 18:37:01',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 18:37:01','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1091,'2025-11-05 18:43:25',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 18:43:25','Nuevo Usuario del Sistema',NULL,NULL),(1092,'2025-11-04 20:27:01',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-04 20:27:01','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1093,'2025-11-05 20:43:38',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 20:43:38','Nuevo Usuario del Sistema',NULL,NULL),(1094,'2025-11-05 23:49:29',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-05 23:49:29','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1095,'2025-11-06 00:23:51',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-06 00:23:51','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1096,'2025-11-06 00:36:41',9,NULL,'ACTUALIZAR_USUARIO','Usuario actualizado','2025-11-06 00:36:41','SISTEMA',NULL,NULL),(1097,'2025-11-06 00:37:01',21,NULL,'CAMBIAR_ESTADO','Estado cambiado a Bloqueado','2025-11-06 00:37:01','ADMIN',NULL,NULL),(1098,'2025-11-06 00:37:18',9,10,'RESETEO_CONTRASENA_A','Contraseña reseteada por administrador','2025-11-06 00:37:18','ADMIN',NULL,NULL),(1099,'2025-11-07 09:11:34',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-07 09:11:34','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1100,'2025-11-07 21:45:20',28,1,'LOGIN','Inicio de sesión exitoso','2025-11-07 21:45:20','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1101,'2025-11-07 22:35:15',28,23,'INICIAR_PRODUCCION','Inició producción #6 - Producto: Rosquilla Clásica - Cantidad: 2.00','2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(1102,'2025-11-07 23:49:39',2,1,'LOGIN','Inicio de sesión exitoso','2025-11-07 23:49:39','Nuevo Usuario del Sistema',NULL,NULL),(1103,'2025-11-18 01:29:37',1,1,'LOGIN','Inicio de sesión exitoso','2025-11-18 01:29:37','Administrador del Sistema',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_bitacora` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `TGR_LOG_TBL_MS_BITACORA` BEFORE DELETE ON `tbl_ms_bitacora` FOR EACH ROW BEGIN
    INSERT INTO `TBL_MS_BITACORA_BACKUP` (
        `ID_BITACORA`, `FECHA`, `ID_USUARIO`, `ID_OBJETO`, `ACCION`, `DESCRIPCION`,
        `FECHA_CREACION`, `CREADO_POR`, `FECHA_BACKUP`, `MOTIVO_BACKUP`
    ) VALUES (
        OLD.`ID_BITACORA`, OLD.`FECHA`, OLD.`ID_USUARIO`, OLD.`ID_OBJETO`, 
        OLD.`ACCION`, OLD.`DESCRIPCION`, OLD.`FECHA_CREACION`, 
        OLD.`CREADO_POR`, NOW(), 'ELIMINACION_MANUAL'
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tbl_ms_bitacora_backup`
--

DROP TABLE IF EXISTS `tbl_ms_bitacora_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_bitacora_backup` (
  `ID_BACKUP` int(11) NOT NULL AUTO_INCREMENT,
  `ID_BITACORA` int(11) NOT NULL,
  `FECHA` datetime NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_OBJETO` int(11) DEFAULT NULL,
  `ACCION` varchar(20) NOT NULL,
  `DESCRIPCION` varchar(100) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT NULL,
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_BACKUP` datetime DEFAULT current_timestamp(),
  `MOTIVO_BACKUP` varchar(50) DEFAULT 'ELIMINACION_MANUAL',
  PRIMARY KEY (`ID_BACKUP`),
  KEY `TBL_MS_BITACORA_BACKUP_IBFK_1` (`ID_BITACORA`),
  KEY `TBL_MS_BITACORA_BACKUP_IBFK_2` (`ID_USUARIO`),
  CONSTRAINT `TBL_MS_BITACORA_BACKUP_IBFK_1` FOREIGN KEY (`ID_BITACORA`) REFERENCES `tbl_ms_bitacora` (`ID_BITACORA`),
  CONSTRAINT `TBL_MS_BITACORA_BACKUP_IBFK_2` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_bitacora_backup`
--

LOCK TABLES `tbl_ms_bitacora_backup` WRITE;
/*!40000 ALTER TABLE `tbl_ms_bitacora_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_ms_bitacora_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_historial_contrasena`
--

DROP TABLE IF EXISTS `tbl_ms_historial_contrasena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_historial_contrasena` (
  `ID_HISTORIAL` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `CONTRASENA` varchar(100) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_HISTORIAL`),
  KEY `TBL_MS_HISTORIAL_CONTRASENA_IBFK_1` (`ID_USUARIO`),
  CONSTRAINT `TBL_MS_HISTORIAL_CONTRASENA_IBFK_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_historial_contrasena`
--

LOCK TABLES `tbl_ms_historial_contrasena` WRITE;
/*!40000 ALTER TABLE `tbl_ms_historial_contrasena` DISABLE KEYS */;
INSERT INTO `tbl_ms_historial_contrasena` VALUES (1,1,'12345A_a','2025-10-15 19:45:23','SISTEMA',NULL,NULL),(2,2,'12345A_t','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(3,2,'12345A_wd','2025-10-15 21:57:02','SISTEMA_RECUPERACION',NULL,NULL),(4,3,'12345A_c','2025-10-15 23:45:04','SISTEMA_RECUPERACION',NULL,NULL),(5,3,'i(;yr6HZE.','2025-10-15 23:48:32','SISTEMA_RECUPERACION',NULL,NULL),(6,3,'m6BX%pv','2025-10-16 01:17:54','SISTEMA_RECUPERACION',NULL,NULL),(7,3,'D0Z^e','2025-10-16 01:19:52','SISTEMA_RECUPERACION',NULL,NULL),(8,4,'12345A_es','2025-10-16 01:28:28','SISTEMA',NULL,NULL),(9,4,'12345A_es','2025-10-16 01:29:59','SISTEMA_RECUPERACION',NULL,NULL),(10,4,'12345A_de','2025-10-16 01:30:44','SISTEMA',NULL,NULL),(11,4,'12345A_dr','2025-10-16 01:31:28','SISTEMA_RECUPERACION',NULL,NULL),(12,4,'54T@I!eiA4','2025-10-16 01:32:43','SISTEMA',NULL,NULL),(13,1,'12345A_b','2025-10-16 17:55:51','SISTEMA',NULL,NULL),(14,3,'3wY*N*nvN','2025-10-16 17:56:49','SISTEMA_RECUPERACION',NULL,NULL),(15,3,'NpU$t7WRG*','2025-10-16 21:09:34','SISTEMA_RECUPERACION',NULL,NULL),(16,3,'Ws2I5y!8a$','2025-10-16 21:15:27','SISTEMA_RECUPERACION',NULL,NULL),(17,3,'@OlAcT3wtT','2025-10-16 21:20:41','SISTEMA_RECUPERACION',NULL,NULL),(18,3,'#cghL4QsE','2025-10-16 21:22:19','SISTEMA_RECUPERACION',NULL,NULL),(19,3,'2^&fE64w','2025-10-16 21:22:31','SISTEMA_RECUPERACION',NULL,NULL),(20,3,'t8H#iPk4','2025-10-16 21:35:58','SISTEMA_RECUPERACION',NULL,NULL),(21,3,'3YwS*%K8','2025-10-16 21:37:06','SISTEMA_RECUPERACION',NULL,NULL),(22,3,'nUT5W4u$c','2025-10-16 21:44:37','SISTEMA_RECUPERACION',NULL,NULL),(23,3,'zPw$iJ8i','2025-10-16 21:50:47','SISTEMA_RECUPERACION',NULL,NULL),(24,3,'CV@lEx@8^5','2025-10-16 22:24:53','SISTEMA_RECUPERACION',NULL,NULL),(25,3,'X3Dq3&blN','2025-10-16 22:32:53','SISTEMA_RECUPERACION',NULL,NULL),(26,3,'H4*JOKSp1','2025-10-16 22:48:23','SISTEMA_RECUPERACION',NULL,NULL),(27,3,'C3^Lva*#J8','2025-10-16 23:10:44','SISTEMA_RECUPERACION',NULL,NULL),(28,3,'7@NffW1xz','2025-10-16 23:29:01','SISTEMA_RECUPERACION',NULL,NULL),(29,3,'eh5Omo!zVG','2025-10-16 23:36:35','SISTEMA_RECUPERACION',NULL,NULL),(30,2,'12345A_es','2025-10-16 23:50:34','SISTEMA_RECUPERACION',NULL,NULL),(31,2,'89svbX!QO','2025-10-17 00:00:10','SISTEMA_RECUPERACION',NULL,NULL),(32,2,'V1s%@!J!c','2025-10-17 00:20:09','SISTEMA_RECUPERACION',NULL,NULL),(33,2,'qF1hIC&rf','2025-10-17 00:31:10','SISTEMA_RECUPERACION',NULL,NULL),(34,2,'x4%7X&U$','2025-10-17 00:34:45','SISTEMA_RECUPERACION',NULL,NULL),(35,2,'2gR&7K$f','2025-10-17 00:48:29','SISTEMA_RECUPERACION',NULL,NULL),(36,2,'Z5AbpD0%','2025-10-17 00:50:34','SISTEMA_RECUPERACION',NULL,NULL),(37,3,'6!LkXxO@','2025-10-17 00:59:43','SISTEMA',NULL,NULL),(38,2,'kzNRx7YFD$','2025-10-17 01:12:03','SISTEMA_RECUPERACION',NULL,NULL),(39,2,'&4B2o7su','2025-10-17 01:26:48','SISTEMA_RECUPERACION',NULL,NULL),(40,1,'12345A_c','2025-10-17 01:38:51','SISTEMA',NULL,NULL),(41,2,'lW1g%nsJw','2025-10-17 01:48:04','SISTEMA_RECUPERACION',NULL,NULL),(42,2,'&Dj9K6bF','2025-10-17 02:05:42','SISTEMA_RECUPERACION',NULL,NULL),(43,2,'9%1jHiNqqg','2025-10-17 03:42:07','SISTEMA_RECUPERACION',NULL,NULL),(44,2,'4y$Q%I2Td','2025-10-17 18:21:20','SISTEMA_RECUPERACION',NULL,NULL),(45,2,'#2iF06Ps5','2025-10-17 19:21:14','SISTEMA_RECUPERACION',NULL,NULL),(46,2,'4C@qw3CHMf','2025-10-17 19:25:29','SISTEMA_RECUPERACION',NULL,NULL),(47,3,'Test123!','2025-10-17 19:52:32','SISTEMA_RECUPERACION',NULL,NULL),(48,2,'gDaa&#033','2025-10-17 23:09:55','SISTEMA_RECUPERACION',NULL,NULL),(49,2,'b9WoulR$','2025-10-18 00:38:40','SISTEMA_RECUPERACION',NULL,NULL),(50,3,'3kma!&&H','2025-10-18 00:52:51','SISTEMA_RECUPERACION',NULL,NULL),(51,4,'12345A_dr','2025-10-18 01:20:58','SISTEMA_RECUPERACION',NULL,NULL),(52,8,'12345A_c','2025-10-18 01:32:29','SISTEMA_RECUPERACION',NULL,NULL),(53,7,'12345A_c','2025-10-18 01:36:07','SISTEMA_RECUPERACION',NULL,NULL),(54,5,'3232A_a','2025-10-18 01:38:13','SISTEMA_RECUPERACION',NULL,NULL),(55,1,'12345A_de','2025-10-19 17:54:02','SISTEMA',NULL,NULL),(56,1,'12345A_wa','2025-10-19 18:29:06','SISTEMA',NULL,NULL),(57,1,'12345A_de','2025-10-19 18:35:55','SISTEMA',NULL,NULL),(58,1,'12345A_wa','2025-10-19 18:45:38','SISTEMA',NULL,NULL),(59,1,'12345A_dwr','2025-10-19 18:48:31','SISTEMA',NULL,NULL),(60,9,'12345A_de','2025-10-20 20:28:13','SISTEMA_RECUPERACION',NULL,NULL),(61,9,'NV$Cl9H7','2025-10-20 20:30:45','SISTEMA_RECUPERACION',NULL,NULL),(62,3,'0Ml1Tw$7Mc','2025-10-20 21:42:02','SISTEMA_RECUPERACION',NULL,NULL),(63,9,'m4X5yDQC$','2025-10-20 23:01:56','SISTEMA_RECUPERACION',NULL,NULL),(64,9,'Jj%6^$ur','2025-10-21 02:00:10','SISTEMA_RECUPERACION',NULL,NULL),(65,10,'12345A_de','2025-10-21 13:53:05','SISTEMA',NULL,NULL),(66,10,'12345A_wa','2025-10-21 14:46:54','SISTEMA',NULL,NULL),(67,10,'12345A_de','2025-10-21 16:23:18','SISTEMA_RECUPERACION',NULL,NULL),(68,10,'!V42XcMr*','2025-10-21 16:25:35','SISTEMA',NULL,NULL),(69,3,'@I1LLFGs','2025-10-21 16:32:02','SISTEMA_RECUPERACION',NULL,NULL),(70,10,'12345A_de','2025-10-23 21:50:36','SISTEMA_RECUPERACION',NULL,NULL),(71,1,'12345A_de','2025-10-23 22:37:43','SISTEMA',NULL,NULL),(72,1,'12345A_wa','2025-10-24 01:02:04','SISTEMA',NULL,NULL),(73,1,'12345A_w','2025-10-24 01:35:55','SISTEMA',NULL,NULL),(74,3,'El9nvW#O68','2025-10-24 03:45:08','SISTEMA_RECUPERACION',NULL,NULL),(75,3,'@m&RQM0^v','2025-10-24 04:05:35','SISTEMA_RECUPERACION',NULL,NULL),(76,1,'12345A_d','2025-10-25 00:03:58','SISTEMA',NULL,NULL),(77,3,'12345A_a','2025-10-25 00:07:03','SISTEMA_RECUPERACION',NULL,NULL),(78,1,'12345A_a','2025-10-25 00:29:35','SISTEMA',NULL,NULL),(79,1,'12345A_d','2025-10-25 00:39:03','SISTEMA',NULL,NULL),(80,1,'12345A_a','2025-10-25 01:33:04','SISTEMA',NULL,NULL),(81,1,'12345A_d','2025-10-25 03:07:28','SISTEMA',NULL,NULL),(82,8,'ujTx2*$6pq','2025-10-25 03:10:03','SISTEMA_RECUPERACION',NULL,NULL),(83,1,'12345A_w','2025-10-25 15:36:22','SISTEMA',NULL,NULL),(84,1,'12345A_d','2025-10-25 15:45:44','SISTEMA',NULL,NULL),(85,27,'$2y$10$c/pc7EsU3WKPAqSkOH1B3eD18Mk7oL3LKQmaAZ2u9AU8eQJhQIKli','2025-10-25 19:29:30','ADMIN',NULL,NULL),(86,21,'$2y$10$fzQAlrISCu2D/rB35C3KH.XsYLu9y2mJ3w/CWRdR4quEtsJlqp/Aa','2025-10-25 19:31:09','ADMIN',NULL,NULL),(87,21,'12345A_w','2025-10-25 19:43:33','ADMIN',NULL,NULL),(88,21,'12345A_r','2025-10-25 19:45:45','ADMIN',NULL,NULL),(89,21,'12345A_j','2025-10-25 19:53:16','ADMIN',NULL,NULL),(90,21,'12345A_dd','2025-10-25 19:59:28','ADMIN',NULL,NULL),(91,21,'12345A_aa','2025-10-25 20:01:15','ADMIN',NULL,NULL),(92,5,'3Qu51jf#','2025-10-25 20:09:23','ADMIN',NULL,NULL),(93,5,'12345A_w','2025-10-25 20:18:15','ADMIN',NULL,NULL),(94,5,'12345A_wda','2025-10-25 20:21:19','ADMIN',NULL,NULL),(95,5,'12345A_dw','2025-10-25 20:31:06','ADMIN',NULL,NULL),(96,5,'123Mm_02','2025-10-25 20:33:28','ADMIN',NULL,NULL),(97,23,'$2y$10$hOT8UtByYXI9BdiO4rPzUOgP1ClCg7EMEb75.9eZ9SMc1dNKIV0Ja','2025-10-25 20:55:04','ADMIN',NULL,NULL),(98,1,'12345A_w','2025-10-25 20:59:11','SISTEMA',NULL,NULL),(99,2,'q%2!eAjK&3','2025-10-25 22:15:26','SISTEMA_RECUPERACION',NULL,NULL),(100,2,'k^XT3T*9','2025-10-25 22:33:10','ADMIN',NULL,NULL),(101,2,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-25 23:08:40','SISTEMA',NULL,NULL),(102,2,'b6a94951ac47bb4c262429b8655305486e7039491dd3b6358c82a883096e0e83','2025-10-25 23:31:33','SISTEMA',NULL,NULL),(103,2,'e430e5c58db0390196522c39f8ac149ac5f0397866e1a2fb0b88ecf303f238ed','2025-10-25 23:41:53','SISTEMA',NULL,NULL),(104,2,'b6a94951ac47bb4c262429b8655305486e7039491dd3b6358c82a883096e0e83','2025-10-25 23:52:50','SISTEMA',NULL,NULL),(105,28,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-26 00:57:33','SISTEMA',NULL,NULL),(106,28,'e274eb59accf7f21390ae18147cb2db692193ab7548641ec68c6e4804ef00229','2025-10-26 01:04:22','SISTEMA',NULL,NULL),(107,28,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-26 01:10:44','SISTEMA',NULL,NULL),(108,7,'dvR4dzbYZ^','2025-10-26 01:22:05','ADMIN',NULL,NULL),(109,21,'12345A_b','2025-10-26 01:37:41','ADMIN',NULL,NULL),(110,2,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-26 13:34:40','SISTEMA',NULL,NULL),(111,10,'FWEbKg6%','2025-10-26 14:10:59','ADMIN',NULL,NULL),(112,10,'5848ec0c779d5fc29cd7077b3e92f93e5363582afd78c22d3ce3a22bc27cb3cd','2025-10-26 14:12:36','ADMIN',NULL,NULL),(113,10,'4c610e9c43283c2246c6e884bf8d64201d40e376309daa0c6cc47848cf9c21c4','2025-10-26 14:27:36','ADMIN',NULL,NULL),(114,5,'12345A_c','2025-10-26 17:08:20','ADMIN',NULL,NULL),(115,33,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-26 17:59:24','REGISTRO',NULL,NULL),(116,21,'12345A_ba','2025-10-27 07:09:00','ADMIN',NULL,NULL),(117,32,'e274eb59accf7f21390ae18147cb2db692193ab7548641ec68c6e4804ef00229','2025-10-27 07:12:01','SISTEMA',NULL,NULL),(118,33,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-28 03:15:36','ADMIN',NULL,NULL),(119,5,'5bdb70c25e6112b7db430d83cf2813c6ba7370d1b31a2fb95a2b3824be07dd51','2025-10-28 07:07:54','ADMIN',NULL,NULL),(120,21,'7b022cb3e6708395e6c7593d728278269dd3fcaf2e71d2e5674a5a90a0fff394','2025-10-28 18:13:18','ADMIN',NULL,NULL),(121,34,'1fa37f5c35d48193403b9babbb59a1f00703b9277ffdc9409f58e755f2a8d33a','2025-10-28 21:30:39','REGISTRO',NULL,NULL),(122,5,'06bbbaca6117528abc4cb5ccecef3ec06a0a5eac81fa16420c8404d389175a07','2025-10-28 21:41:20','ADMIN',NULL,NULL),(123,35,'d866d6c89d50fa452689a0b936466e8ffcfd008d078f605e8cb67b6818061cbb','2025-10-28 22:45:50','REGISTRO',NULL,NULL),(124,36,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-10-29 10:59:28','REGISTRO',NULL,NULL),(125,21,'3fa6928828bce3e26784ab26505853c2bcf62832f7c57e87e5e9ec6bddf2ef2d','2025-10-29 16:47:46','ADMIN',NULL,NULL),(126,10,'a72d9817a9a005aa54d95f6668801427b69a2b7322bb247b5098725282ff131e','2025-10-29 17:03:26','SISTEMA_RECUPERACION',NULL,NULL),(127,2,'e274eb59accf7f21390ae18147cb2db692193ab7548641ec68c6e4804ef00229','2025-10-29 17:13:39','SISTEMA',NULL,NULL),(128,13,'$2y$10$tQBcCQg.0oUf06mqd0JH7eSY3qdU1voCgSciwFJHHlTuvIvncIRny','2025-11-01 03:14:17','ADMIN',NULL,NULL),(129,2,'dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79','2025-11-02 04:48:57','SISTEMA',NULL,NULL),(130,17,'$2y$10$WjzDvxedwVpSsDs7b6rdY.arOZbHtBq5IRgItOpD8qnkCVFrjuxqm','2025-11-04 03:33:03','ADMIN',NULL,NULL),(131,9,'8bjFkX%IS','2025-11-06 00:37:18','ADMIN',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_historial_contrasena` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_objetos`
--

DROP TABLE IF EXISTS `tbl_ms_objetos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_objetos` (
  `ID_OBJETO` int(11) NOT NULL AUTO_INCREMENT,
  `OBJETO` varchar(100) NOT NULL,
  `TIPO_OBJETO` varchar(50) DEFAULT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `RUTA` varchar(255) DEFAULT NULL,
  `ICONO` varchar(50) DEFAULT NULL,
  `ESTADO` enum('ACTIVO','INACTIVO') DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_OBJETO`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_objetos`
--

LOCK TABLES `tbl_ms_objetos` WRITE;
/*!40000 ALTER TABLE `tbl_ms_objetos` DISABLE KEYS */;
INSERT INTO `tbl_ms_objetos` VALUES (1,'LOGIN','PANTALLA','Pantalla de inicio de sesión','/login.php','fa-sign-in-alt','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(2,'GESTION_USUARIOS','PANTALLA','Módulo Usuario - Todos los usuarios registrados','/gestion-usuarios.php','fa-users','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(3,'GESTION_ROLES','PANTALLA','Módulo Usuario - Gestión de roles del sistema','/roles.php','fa-user-shield','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(4,'ÓRDENES_DE_COMPRAS','PANTALLA','Ver todas los pedidos','/consultar-ordenes-pendientes.php','fa-clipboard-list','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(5,'RECUPERACION_CONTRASENA','PANTALLA','Recuperación de contraseña','/recuperar-password.php','fa-key','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(6,'CAMBIAR_CONTRASENA','PANTALLA','Módulo Usuario - Cambio de contraseña','/cambiar-password.php','fa-unlock-alt','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(7,'REGISTRO','PANTALLA','Módulo Usuario - Se registró un nuevo usuario','/registro.php','fa-user-plus','ACTIVO','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(8,'BITACORA','PANTALLA','Consulta de bitácora del sistema','/bitacora.php','fa-history','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(9,'CONFIGURAR_PREGUNTAS','PANTALLA','Configuración de preguntas de seguridad','/configurar-preguntas.php','fa-question-circle','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(10,'CREAR_USUARIO','PANTALLA','Módulo Usuario - Creación de nuevos usuarios','/crear-usuario.php','fa-user-plus','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(11,'EDITAR_USUARIO','PANTALLA','Edición de usuarios existentes','/editar-usuario.php','fa-user-edit','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(12,'DASHBOARD','PANTALLA','Dashboard principal del sistema','/dashboard.php','fa-home','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(13,'INICIO','PANTALLA','Página de inicio después del login','/inicio.php','fa-tachometer-alt','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(14,'compras','PANTALLA','Módulo completo de compras','/compras.php',NULL,'ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(15,'RECUPERAR_PASSWORD_PREGUNTAS','PANTALLA','Recuperación por preguntas de seguridad','/recuperar-password-preguntas.php','fa-key','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(16,'RESETEAR_CONTRASENA','PANTALLA','Resetear contraseña de usuario','/resetear-contrasena.php','fa-redo','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(17,'CONFIGURAR_2FA','PANTALLA','Configuración de autenticación de dos factores','/configurar-2fa.php','fa-mobile-alt','ACTIVO','2025-10-27 16:49:47','SISTEMA',NULL,NULL),(18,'GESTION_COMPRAS','PANTALLA','Gestión de compras del sistema','/compras.php','fa-shopping-cart','ACTIVO','2025-10-27 17:43:18','SISTEMA',NULL,NULL),(19,'GESTION_PROVEEDORES','PANTALLA','Gestión de proveedores','/gestion-proveedores.php','fa-truck','ACTIVO','2025-10-27 17:43:18','SISTEMA',NULL,NULL),(20,'REGISTRAR_COMPRA','ACCION','Registrar nueva compra','/registrar-compra.php','fa-plus-circle','ACTIVO','2025-10-27 17:43:18','SISTEMA',NULL,NULL),(21,'APROBAR_COMPRA','ACCION','Aprobar compra pendiente','/aprobar-compra.php','fa-check-circle','ACTIVO','2025-10-27 17:43:18','SISTEMA',NULL,NULL),(22,'CANCELAR_COMPRA','ACCION','Cancelar compra','/cancelar-compra.php','fa-times-circle','ACTIVO','2025-10-27 17:43:18','SISTEMA',NULL,NULL),(23,'Producción','MODULO','Módulo de gestión de producción de rosquillas y productos','/produccion.php','fa-industry','ACTIVO','2025-11-02 05:59:03','SISTEMA',NULL,NULL),(24,'RESPALDOS_SISTEMA','MODULO','Gestión y ejecución de respaldos automáticos y manuales del sistema','/backup.php','fa-database','ACTIVO','2025-11-04 23:13:16','SISTEMA',NULL,NULL),(25,'BITACORA_BACKUP','PROCESO','Respaldo automático de bitácora al depurar registros',NULL,'fa-cogs','ACTIVO','2025-11-04 23:13:16','SISTEMA',NULL,NULL),(26,'EVENTO_BACKUPS','EVENTO','Evento programado para ejecución y limpieza de respaldos automáticos',NULL,'fa-cogs','ACTIVO','2025-11-04 23:13:16','SISTEMA',NULL,NULL),(27,'GESTION_PERMISOS','PANTALLA','Gestión de permisos del sistema','/permisos-usuarios.php','fa-key','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(28,'PARAMETROS_SEGURIDAD','PANTALLA','Configuración de parámetros de seguridad','#','fa-user-lock','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(29,'PARAMETROS_SISTEMA','PANTALLA','Configuración de parámetros del sistema','#','fa-sliders-h','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(30,'GESTION_INVENTARIO','MODULO','Módulo de gestión de inventario','/inventario.php','fa-boxes','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(31,'GESTION_VENTAS','MODULO','Módulo de gestión de ventas','/ventas.php','fa-cash-register','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(32,'GESTION_REPORTES','MODULO','Módulo de gestión de reportes','/reportes.php','fa-chart-bar','ACTIVO','2025-11-05 01:57:29','SISTEMA',NULL,NULL),(33,'GESTION ','PANTALLA','Gestión de proveedores del sistema','','fa-truck','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(34,'CREAR_ORDEN_DE_PRODUCCION','PANTALLA','Crear producción','/crear-produccion.php','fa-clipboard-list','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(35,'CONSULTAR_COMPRAS','PANTALLA','Consulta de compras realizadas','/consultar-compras.php','fa-shopping-cart','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(36,'GESTION_MATERIA_PRIMA','PANTALLA','Gestión de materia prima','/gestion-materia-prima.php','fa-box-open','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(37,'REGISTRAR_MATERIA_PRIMA','PANTALLA','Registro de nueva materia prima','/registrar-materia-prima.php','fa-plus-circle','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(38,'EDITAR_MATERIA_PRIMA','PANTALLA','Edición de materia prima existente','/editar-materia-prima.php','fa-edit','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(39,'REGISTRAR_PROVEEDOR','PANTALLA','Registro de nuevo proveedor','/registrar-proveedor.php','fa-user-plus','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(40,'EDITAR_PROVEEDOR','PANTALLA','Edición de proveedor existente','/editar-proveedor.php','fa-user-edit','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(41,'GESTION_PRODUCTOS_PROVEEDOR','PANTALLA','Gestión de productos por proveedor','/gestion-productos-proveedor.php','fa-boxes','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(42,'EDITAR_PRODUCTOS_PROVEEDORES','PANTALLA','Edición de productos de proveedores','/editar-productos-proveedores.php','fa-edit','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(43,'RECIBIR_COMPRAS','PANTALLA','Recepción de compras realizadas','/recibir-compras.php','fa-check-circle','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(44,'DETALLE_COMPRA','PANTALLA','Detalle de compra específica','/detalle-compra.php','fa-eye','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(45,'ORDENES_FINALIZADAS','PANTALLA','Órdenes de compra finalizadas','/ordenes-finalizadas.php','fa-check-double','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(46,'ORDENES_CANCELADAS','PANTALLA','Órdenes de compra canceladas','/ordenes-canceladas.php','fa-times-circle','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(47,'CREAR_PRODUCCION','PANTALLA','Crear orden de producción','/crear-produccion.php','fa-industry','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(48,'GESTION_PRODUCCION','PANTALLA','Gestión de producción','/gestion-produccion.php','fa-cogs','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(49,'VER_RECETAS','PANTALLA','Visualización de recetas','/ver-recetas.php','fa-book','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(50,'GESTION_PRODUCTOS_ELABORADOS','PANTALLA','Todos los productos elaborados ','/gestion-productos.php','fa-box','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(51,'CREAR_RECETA','PANTALLA','Crear nueva receta','/crear-receta.php','fa-plus-square','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(52,'DETALLE_PRODUCCION','PANTALLA','Detalle de producción','/detalle-produccion.php','fa-info-circle','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(53,'EDITAR_PRODUCTO','PANTALLA','Edición de producto','/editar-producto.php','fa-edit','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(54,'FINALIZAR_PRODUCCION','PANTALLA','Finalizar orden de producción','/finalizar-produccion.php','fa-flag-checkered','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(55,'GESTION_INVENTARIO_PRODUCTOS','PANTALLA','Gestión de inventario de productos','/gestion-inventario-productos.php','fa-boxes','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(56,'GESTION_BACKUPS','PANTALLA','Gestión de respaldos del sistema','/gestion-backups.php','fa-database','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(57,'PERFIL_USUARIO','PANTALLA','Perfil del usuario','/perfil.php','fa-user','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(58,'USUARIOS_ASIGNAR','PANTALLA','Asignación de permisos a usuarios','/usuarios_asignar.php','fa-user-shield','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(59,'EXPORTAR_PDF_COMPRAS','ACCION','Exportar reportes de compras a PDF','/reporte_compras_pdf.php','fa-file-pdf','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(60,'GENERAR_PDF','ACCION','Generar documentos PDF','/generar_pdf.php','fa-file-export','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(61,'ORDENES_DE_COMPRAS','MODULO','Módulo de Compras - Ordenes pendientes ','/consultar-ordenes-pendientes.php','fa-shopping-cart','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(62,'MODULO_PRODUCCION','MODULO','Módulo completo de producción','#','fa-industry','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(63,'inventario','MODULO','Módulo completo de inventario','/inventario.php','fa-warehouse','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(64,'MODULO_USUARIOS','MODULO','Módulo completo de usuarios','#','fa-users','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL),(65,'MODULO_SEGURIDAD','MODULO','Módulo completo de seguridad','#','fa-shield-alt','ACTIVO','2025-11-05 18:54:31','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_objetos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_parametros`
--

DROP TABLE IF EXISTS `tbl_ms_parametros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_parametros` (
  `ID_PARAMETRO` int(11) NOT NULL AUTO_INCREMENT,
  `PARAMETRO` varchar(50) NOT NULL,
  `VALOR` varchar(100) NOT NULL,
  `ID_USUARIO` int(11) DEFAULT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PARAMETRO`),
  KEY `TBL_MS_PARAMETROS_IBFK_1` (`ID_USUARIO`),
  CONSTRAINT `TBL_MS_PARAMETROS_IBFK_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_parametros`
--

LOCK TABLES `tbl_ms_parametros` WRITE;
/*!40000 ALTER TABLE `tbl_ms_parametros` DISABLE KEYS */;
INSERT INTO `tbl_ms_parametros` VALUES (1,'ADMIN_INTENTOS_INVALIDOS','3',NULL,'Número máximo de intentos de login fallidos','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(2,'ADMIN_PREGUNTAS','3',NULL,'Número de preguntas de seguridad requeridas','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(3,'ADMIN_DIAS_VIGENCIA','360',NULL,'Días de vigencia de la contraseña','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(4,'MIN_CONTRASENA','5',NULL,'Longitud mínima de contraseña','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(5,'MAX_CONTRASENA','10',NULL,'Longitud máxima de contraseña','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(6,'HORAS_VIGENCIA_CONTRASENA_TEMPORAL','24',NULL,'Horas de vigencia para contraseñas temporales de recuperación','2025-10-15 23:20:10','SISTEMA',NULL,NULL),(7,'COMPRAS_IVA_PORCENTAJE','15',NULL,'Porcentaje de IVA aplicable a las compras','2025-10-27 17:43:01','SISTEMA',NULL,NULL),(8,'COMPRAS_STOCK_MINIMO_ALERTA','10',NULL,'Porcentaje mínimo de stock para generar alertas','2025-10-27 17:43:01','SISTEMA',NULL,NULL),(9,'COMPRAS_STOCK_MAXIMO_ALERTA','90',NULL,'Porcentaje máximo de stock para generar alertas','2025-10-27 17:43:01','SISTEMA',NULL,NULL),(10,'COMPRAS_DIAS_VALIDEZ_COTIZACION','30',NULL,'Días de validez para las cotizaciones de compra','2025-10-27 17:43:01','SISTEMA',NULL,NULL),(11,'COMPRAS_APROBACION_AUTOMATICA','0',NULL,'1=Aprobación automática, 0=Aprobación manual','2025-10-27 17:43:01','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_parametros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_permisos`
--

DROP TABLE IF EXISTS `tbl_ms_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_permisos` (
  `ID_PERMISO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_ROL` int(11) NOT NULL,
  `ID_OBJETO` int(11) NOT NULL,
  `PERMISO_CREACION` tinyint(1) DEFAULT 0,
  `PERMISO_ELIMINACION` tinyint(1) DEFAULT 0,
  `PERMISO_ACTUALIZACION` tinyint(1) DEFAULT 0,
  `PERMISO_CONSULTAR` tinyint(1) DEFAULT 0,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PERMISO`),
  KEY `TBL_MS_PERMISOS_IBFK_1` (`ID_ROL`),
  KEY `TBL_MS_PERMISOS_IBFK_2` (`ID_OBJETO`),
  CONSTRAINT `TBL_MS_PERMISOS_IBFK_1` FOREIGN KEY (`ID_ROL`) REFERENCES `tbl_ms_roles` (`ID_ROL`),
  CONSTRAINT `TBL_MS_PERMISOS_IBFK_2` FOREIGN KEY (`ID_OBJETO`) REFERENCES `tbl_ms_objetos` (`ID_OBJETO`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_permisos`
--

LOCK TABLES `tbl_ms_permisos` WRITE;
/*!40000 ALTER TABLE `tbl_ms_permisos` DISABLE KEYS */;
INSERT INTO `tbl_ms_permisos` VALUES (1,1,1,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(2,1,2,1,1,1,1,'2025-11-05 01:57:47','SISTEMA','2025-11-06 02:47:17','ADMINISTRADOR'),(3,1,3,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(4,1,4,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(5,1,5,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(6,1,6,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(7,1,7,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(8,1,8,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(9,1,9,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(10,1,10,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(11,1,11,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(12,1,12,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(13,1,13,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(14,1,14,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(15,1,15,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(16,1,16,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(17,1,17,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(18,1,18,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(19,1,19,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(20,1,20,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(21,1,21,1,1,1,1,'2025-11-05 01:57:47','SISTEMA','2025-11-06 02:45:01','ADMINISTRADOR'),(22,1,22,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(23,1,23,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(24,1,24,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(25,1,25,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(26,1,26,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(27,1,27,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(28,1,28,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(29,1,29,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(30,1,30,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(31,1,31,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(32,1,32,1,1,1,1,'2025-11-05 01:57:47','SISTEMA',NULL,NULL),(33,2,21,0,0,0,1,'2025-11-05 17:39:47','ADMINISTRADOR','2025-11-05 22:42:35','USER'),(34,2,22,0,0,0,1,'2025-11-05 17:39:55','ADMINISTRADOR','2025-11-05 22:42:32','USER'),(35,2,20,0,0,0,1,'2025-11-05 17:39:59','ADMINISTRADOR','2025-11-05 21:03:24','USER'),(36,2,13,0,0,0,1,'2025-11-05 17:42:09','ADMINISTRADOR','2025-11-05 22:42:45','USER'),(37,2,2,1,0,0,1,'2025-11-05 18:17:04','USER','2025-11-05 22:03:42','USER'),(38,2,19,0,0,0,1,'2025-11-05 18:42:10','ADMINISTRADOR','2025-11-05 23:32:02','USER'),(39,2,18,0,0,0,1,'2025-11-05 18:42:20','ADMINISTRADOR',NULL,NULL),(40,2,30,0,0,0,1,'2025-11-05 18:42:24','ADMINISTRADOR',NULL,NULL),(41,2,23,0,0,0,1,'2025-11-05 18:42:32','ADMINISTRADOR','2025-11-05 21:52:09','USER'),(42,2,8,0,0,0,1,'2025-11-05 18:44:39','USER','2025-11-05 23:35:55','USER'),(43,1,33,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(44,1,34,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(45,1,35,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(46,1,36,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(47,1,37,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(48,1,38,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(49,1,39,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(50,1,40,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(51,1,41,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(52,1,42,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(53,1,43,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(54,1,44,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(55,1,45,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(56,1,46,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(57,1,47,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(58,1,48,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(59,1,49,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(60,1,50,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(61,1,51,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(62,1,52,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(63,1,53,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(64,1,54,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(65,1,55,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(66,1,56,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(67,1,57,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(68,1,58,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(69,1,59,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(70,1,60,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(71,1,61,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(72,1,62,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(73,1,63,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(74,1,64,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(75,1,65,1,1,1,1,'2025-11-05 18:54:52','SISTEMA',NULL,NULL),(106,2,62,0,0,0,1,'2025-11-05 19:15:31','USER','2025-11-05 20:47:47','ADMINISTRADOR'),(107,2,24,0,0,0,0,'2025-11-05 19:16:35','USER','2025-11-07 22:53:27','ADMINISTRADOR'),(108,2,64,0,0,0,1,'2025-11-05 20:48:40','ADMINISTRADOR',NULL,NULL),(109,2,25,0,0,0,0,'2025-11-05 20:52:44','ADMINISTRADOR','2025-11-07 22:52:26','ADMINISTRADOR'),(110,2,49,0,0,0,1,'2025-11-05 20:52:50','ADMINISTRADOR','2025-11-05 23:16:58','USER'),(111,2,58,0,0,0,1,'2025-11-05 20:52:56','ADMINISTRADOR',NULL,NULL),(112,2,16,0,0,0,1,'2025-11-05 20:53:02','ADMINISTRADOR','2025-11-05 22:42:52','USER'),(113,2,33,0,0,0,1,'2025-11-05 20:53:19','ADMINISTRADOR','2025-11-05 22:30:32','USER'),(114,2,41,0,0,0,1,'2025-11-05 20:53:29','ADMINISTRADOR',NULL,NULL),(115,2,35,0,0,0,1,'2025-11-05 20:53:38','ADMINISTRADOR','2025-11-05 23:04:36','USER'),(116,2,50,0,0,0,1,'2025-11-05 20:54:38','ADMINISTRADOR','2025-11-05 23:24:08','USER'),(117,2,59,0,0,0,1,'2025-11-05 21:03:17','USER',NULL,NULL),(118,2,60,0,0,0,1,'2025-11-05 21:03:20','USER',NULL,NULL),(119,2,26,0,0,0,1,'2025-11-05 21:03:27','USER',NULL,NULL),(120,2,32,0,0,0,1,'2025-11-05 21:03:30','USER',NULL,NULL),(121,2,31,0,0,0,1,'2025-11-05 21:03:35','USER',NULL,NULL),(122,2,61,0,0,0,1,'2025-11-05 21:03:40','USER',NULL,NULL),(123,2,63,0,0,0,1,'2025-11-05 21:03:44','USER','2025-11-05 23:35:00','USER'),(124,2,65,0,0,0,1,'2025-11-05 21:03:48','USER',NULL,NULL),(125,2,6,0,0,0,1,'2025-11-05 21:03:57','USER','2025-11-05 21:45:36','USER'),(126,2,9,0,0,0,1,'2025-11-05 21:04:05','USER',NULL,NULL),(127,2,17,0,0,0,1,'2025-11-05 21:04:10','USER',NULL,NULL),(128,2,47,0,0,0,1,'2025-11-05 21:04:19','USER','2025-11-05 21:04:26','USER'),(129,2,34,0,0,0,1,'2025-11-05 21:04:31','USER','2025-11-05 23:12:10','USER'),(130,2,51,0,0,0,1,'2025-11-05 21:04:36','USER',NULL,NULL),(131,2,10,0,0,0,0,'2025-11-05 21:04:43','USER','2025-11-05 21:37:33','USER'),(132,2,44,0,0,0,1,'2025-11-05 21:04:49','USER','2025-11-05 21:04:57','USER'),(133,2,12,0,0,0,1,'2025-11-05 21:05:04','USER',NULL,NULL),(134,2,52,0,0,0,1,'2025-11-05 21:05:13','USER',NULL,NULL),(135,2,38,0,0,0,1,'2025-11-05 21:05:17','USER',NULL,NULL),(136,2,53,0,0,0,1,'2025-11-05 21:05:22','USER',NULL,NULL),(137,2,42,0,0,0,1,'2025-11-05 21:05:26','USER',NULL,NULL),(138,2,40,0,0,0,1,'2025-11-05 21:05:30','USER',NULL,NULL),(139,2,11,0,0,0,1,'2025-11-05 21:05:38','USER','2025-11-05 22:02:08','USER'),(140,2,56,0,0,0,1,'2025-11-05 21:05:43','USER','2025-11-05 21:05:56','USER'),(141,2,4,0,0,0,1,'2025-11-05 21:06:01','USER','2025-11-05 22:50:38','USER'),(142,2,54,0,0,0,1,'2025-11-05 21:06:05','USER',NULL,NULL),(143,2,55,0,0,0,1,'2025-11-05 21:06:12','USER',NULL,NULL),(144,2,36,0,0,0,1,'2025-11-05 21:06:19','USER','2025-11-05 23:07:44','USER'),(145,2,27,0,0,0,0,'2025-11-05 21:06:27','USER','2025-11-05 21:35:58','USER'),(146,2,48,0,0,0,1,'2025-11-05 21:06:36','USER','2025-11-05 23:15:28','USER'),(147,2,3,0,0,0,1,'2025-11-05 21:06:45','USER',NULL,NULL),(148,2,14,0,0,0,1,'2025-11-05 21:06:54','USER','2025-11-05 21:58:08','USER'),(149,2,1,0,0,0,1,'2025-11-05 21:07:01','USER',NULL,NULL),(150,2,46,0,0,0,1,'2025-11-05 21:07:07','USER',NULL,NULL),(151,2,28,0,0,0,1,'2025-11-05 21:07:13','USER','2025-11-05 21:07:30','USER'),(152,2,45,0,0,0,1,'2025-11-05 21:07:22','USER',NULL,NULL),(153,2,57,0,0,0,1,'2025-11-05 21:07:35','USER','2025-11-05 21:07:47','USER'),(154,2,29,0,0,0,1,'2025-11-05 21:07:42','USER',NULL,NULL),(155,2,43,0,0,0,1,'2025-11-05 21:07:55','USER',NULL,NULL),(156,2,5,0,0,0,1,'2025-11-05 21:08:02','USER',NULL,NULL),(157,2,15,0,0,0,1,'2025-11-05 21:08:10','USER',NULL,NULL),(158,2,37,0,0,0,1,'2025-11-05 21:08:18','USER',NULL,NULL),(159,2,39,0,0,0,1,'2025-11-05 21:08:26','USER',NULL,NULL),(160,2,7,0,0,0,1,'2025-11-05 21:08:35','USER',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_preguntas`
--

DROP TABLE IF EXISTS `tbl_ms_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_preguntas` (
  `ID_PREGUNTA` int(11) NOT NULL AUTO_INCREMENT,
  `PREGUNTA` varchar(255) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PREGUNTA`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_preguntas`
--

LOCK TABLES `tbl_ms_preguntas` WRITE;
/*!40000 ALTER TABLE `tbl_ms_preguntas` DISABLE KEYS */;
INSERT INTO `tbl_ms_preguntas` VALUES (1,'¿Cuál es el nombre de tu mascota?','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(2,'¿Cuál es tu color favorito?','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(3,'¿En qué ciudad naciste?','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(4,'¿Cuál es el nombre de tu madre?','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(5,'¿Cuál es tu comida favorita?','2025-10-15 15:18:19','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_preguntas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_respuestas`
--

DROP TABLE IF EXISTS `tbl_ms_respuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_respuestas` (
  `ID_RESPUESTA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_PREGUNTA` int(11) NOT NULL,
  `RESPUESTA` varchar(255) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_RESPUESTA`),
  KEY `TBL_MS_RESPUESTAS_IBFK_1` (`ID_USUARIO`),
  KEY `TBL_MS_RESPUESTAS_IBFK_2` (`ID_PREGUNTA`),
  CONSTRAINT `TBL_MS_RESPUESTAS_IBFK_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`),
  CONSTRAINT `TBL_MS_RESPUESTAS_IBFK_2` FOREIGN KEY (`ID_PREGUNTA`) REFERENCES `tbl_ms_preguntas` (`ID_PREGUNTA`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_respuestas`
--

LOCK TABLES `tbl_ms_respuestas` WRITE;
/*!40000 ALTER TABLE `tbl_ms_respuestas` DISABLE KEYS */;
INSERT INTO `tbl_ms_respuestas` VALUES (1,2,4,'ROSA','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(2,2,1,'JACK','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(3,2,2,'VERDE','2025-10-15 19:51:39','SISTEMA',NULL,NULL),(4,4,3,'COMAYAGUA','2025-10-16 01:28:28','SISTEMA',NULL,NULL),(5,4,5,'TACOS','2025-10-16 01:28:28','SISTEMA',NULL,NULL),(6,4,1,'SPIKE','2025-10-16 01:28:28','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_respuestas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_roles`
--

DROP TABLE IF EXISTS `tbl_ms_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_roles` (
  `ID_ROL` int(11) NOT NULL AUTO_INCREMENT,
  `ROL` varchar(30) NOT NULL,
  `DESCRIPCION` varchar(100) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_ROL`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_roles`
--

LOCK TABLES `tbl_ms_roles` WRITE;
/*!40000 ALTER TABLE `tbl_ms_roles` DISABLE KEYS */;
INSERT INTO `tbl_ms_roles` VALUES (1,'ADMINISTRADOR','Rol con todos los privilegios','2025-10-15 15:18:19','SISTEMA',NULL,NULL),(2,'USUARIO','Usuario del sistema','2025-10-15 15:18:19','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_ms_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ms_usuarios`
--

DROP TABLE IF EXISTS `tbl_ms_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ms_usuarios` (
  `ID_USUARIO` int(11) NOT NULL AUTO_INCREMENT,
  `NUMERO_IDENTIDAD` varchar(20) DEFAULT NULL,
  `USUARIO` varchar(15) NOT NULL,
  `NOMBRE_USUARIO` varchar(100) NOT NULL,
  `ESTADO_USUARIO` varchar(100) DEFAULT 'Activo',
  `CONTRASENA` varchar(100) NOT NULL,
  `ID_ROL` int(11) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_ULTIMA_CONEXION` datetime DEFAULT NULL,
  `PRIMER_INGRESO` int(11) DEFAULT 0,
  `FECHA_VENCIMIENTO` date DEFAULT NULL,
  `CORREO_ELECTRONICO` varchar(50) DEFAULT NULL,
  `RESETEO_CONTRASENA` tinyint(1) DEFAULT 0,
  `INTENTOS_INVALIDOS` int(11) DEFAULT 0,
  `HABILITAR_2FA` tinyint(1) DEFAULT 0,
  `FOTO_PERFIL` varchar(255) DEFAULT 'perfil.jpg',
  PRIMARY KEY (`ID_USUARIO`),
  UNIQUE KEY `USUARIO` (`USUARIO`),
  KEY `TBL_MS_USUARIOS_IBFK_1` (`ID_ROL`),
  CONSTRAINT `TBL_MS_USUARIOS_IBFK_1` FOREIGN KEY (`ID_ROL`) REFERENCES `tbl_ms_roles` (`ID_ROL`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ms_usuarios`
--

LOCK TABLES `tbl_ms_usuarios` WRITE;
/*!40000 ALTER TABLE `tbl_ms_usuarios` DISABLE KEYS */;
INSERT INTO `tbl_ms_usuarios` VALUES (1,'0000000000000','ADMIN','Administrador del Sistema','activo','12345A_ss',1,'2025-10-15 15:18:19','SISTEMA','2025-10-25 20:59:11','SISTEMA','2025-11-18 01:29:37',1,NULL,'lobjon2004@gmail.com',0,0,0,'perfil.jpg'),(2,'0801199901234','USER','Nuevo Usuario del Sistema','ACTIVO','dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79',2,'2025-10-15 15:18:19','ADMIN','2025-11-02 04:48:57','SISTEMA','2025-11-07 23:49:39',1,'2025-10-26','denisunah1206@gmail.com',0,0,0,'user_2_1762269690.jpg'),(3,'120620010032','DENIS','DENIS IRWIN LOPEZ','Bloqueado','36EF*Cjy5',2,'2025-10-15 19:48:10','ADMIN','2025-10-25 00:07:03','SISTEMA_RECUPERACION','2025-10-25 00:06:50',1,'2025-10-26','denislopez1206@gmail.com',1,2,0,'perfil.jpg'),(4,'5555555555','JOSE ','JOSE LUIS','ACTIVO','VCm*4z3f7@',2,'2025-10-16 01:22:47','ADMIN','2025-10-18 01:20:58','SISTEMA_RECUPERACION','2025-10-18 00:42:33',0,'2025-10-19','luisjosen306@gmai.com',1,0,0,'perfil.jpg'),(5,'3232323255555','DAMARIS','DAMARIS JANETH OSORIO SIERRA','ACTIVO','31acce588fa10ab9edcc04d8f87294d90b5aba71445814066293de959e905c30',2,'2025-10-16 02:18:48','ADMIN','2025-10-28 21:41:20','ADMIN',NULL,1,'2025-10-19','dama.osorio@unah.hn',1,0,0,'perfil.jpg'),(6,'5555555555','PEDRO','JORGE JORGE','ACTIVO','12345A_c',2,'2025-10-16 17:59:42','ADMIN',NULL,NULL,'2025-10-25 00:14:31',1,'2026-10-11','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(7,'32352225255','FREDDY','FREDDY ONAN GÓMEZ REYES','ACTIVO','12345A_ba',2,'2025-10-17 23:22:15','ADMIN','2025-10-26 01:22:05','ADMIN',NULL,1,'2025-10-19','fogomezr@unah.hn',1,0,0,'perfil.jpg'),(8,'5555555555','PABLO','PABLO JOSUE REYES SALGADO','ACTIVO','23T$8p*v',2,'2025-10-18 00:55:08','ADMIN','2025-10-25 03:10:03','SISTEMA_RECUPERACION','2025-10-18 00:57:58',1,'2025-10-26','pjreyess@unah.hn',1,1,0,'perfil.jpg'),(9,'555555232','DEDE','DEDE DEDE DEDE','ACTIVO','2f498011a92b77c0c5b0a5c45daa453a8db5ba545330092271603d2a5dc095f3',2,'2025-10-20 20:17:16','ADMIN','2025-11-06 00:37:18','ADMIN','2025-10-21 02:04:39',1,'2025-10-22','denislopez126@gmail.com',1,0,0,'perfil.jpg'),(10,'5512346575','DENISS','JORGE JORGE','ACTIVO','O%8yBz5dx6',2,'2025-10-20 22:59:21','ADMIN','2025-10-29 17:03:26','SISTEMA_RECUPERACION','2025-10-21 17:11:10',1,'2025-10-30','denislo6@gmail.com',1,2,0,'perfil.jpg'),(11,'22323239933','DEDER','DEDER DEDER DEDER','ACTIVO','$2y$10$sZiZtpJ.ng2/xtSo7jBw/uU6iqyAJS1ZAOvyIAoPFz8LrGoY1NgCO',1,'2025-10-23 23:10:30','ADMIN','2025-10-25 18:00:49','SISTEMA',NULL,1,'2026-10-18','deer1206@gmail.com',0,0,0,'perfil.jpg'),(12,'12062001222332','SEDA','SEDA SEDA SEDA','Nuevo','$2y$10$NzwZchsax5n36DQDuzxnb.l8ae8KOvlvlvyaLJy1BKOp7OUMMEyd2',2,'2025-10-23 23:33:17','ADMIN',NULL,NULL,NULL,1,'2026-10-18','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(13,'12033301002882222222','ADA','ADADA ADAD','Bloqueado','1dcd69574adcb3964510c8dec74d521c981a4f80b19011c471903ec66cf41cc0',1,'2025-10-23 23:39:53','ADMIN','2025-11-01 03:14:44','SISTEMA',NULL,1,'2026-10-18','adaada1206@gmail.com',1,0,0,'perfil.jpg'),(14,'555325355555','EDA','EDA','Bloqueado','$2y$10$i05e7W3fYrS1Ize.XBg8gOHaCUro3t9ymE.LL2jXwnNYe6Fszd6tC',2,'2025-10-23 23:41:51','ADMIN','2025-10-24 02:13:25','ADMIN',NULL,1,'2026-10-18','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(15,'33333333333333333333','EDERD','EDERD EDERD','Nuevo','$2y$10$3qafU.XWKe8MpRR4xh6eQOOj3H9hnAP8nZbS.V6b9BWTUOyiQmc52',2,'2025-10-23 23:49:45','ADMIN',NULL,NULL,NULL,1,'2026-10-18','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(16,'33223232323232323232','EDRAS','EDRAS EDRAS','Nuevo','$2y$10$GPEds7dwd/X8dhjSI6WKku9RrKZgVBQ2EGwcHJbrB3DtqrQnKv/wi',2,'2025-10-23 23:59:15','ADMIN',NULL,NULL,NULL,1,'2026-10-18','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(17,'3231111111','PIRLO','PILO PILO','Bloqueado','808788a4c09444f9d240467909f5697ff706ea848c0e88592be7b4ce1374d4d3',2,'2025-10-24 00:06:38','ADMIN','2025-11-04 03:33:16','ADMIN',NULL,1,'2026-10-19','denislop6@gmail.com',1,0,0,'perfil.jpg'),(18,'32062001033','KAREN','KAREN WALESKA','Nuevo','$2y$10$JJSM0qaaIGUEZzRe5nHESOn6EN1lUdQC8yQ0Sam75XoX4XF9G3r.G',2,'2025-10-24 00:08:29','ADMIN',NULL,NULL,NULL,1,'2026-10-19','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(19,'2553235325','JENI','JEMJEM','Bloqueado','$2y$10$KimpRB2Wy6dMCBplyk9/huzCBS26z8/V8atqW9z8AsIhK4FLyPZci',2,'2025-10-24 00:14:08','ADMIN','2025-10-25 01:58:05','ADMIN',NULL,1,'2026-10-19','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(20,'235232332332233','FERRERA','FERRERA','Nuevo','$2y$10$x8dzwILeKo7IrM27ovMuy.8CgX5IiIuCPlEhyK7CLOYvXiuAqqgym',1,'2025-10-24 00:15:45','ADMIN',NULL,NULL,NULL,1,'2026-10-19','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(21,'2352255555555555','AFERERA','FERRRERARR','Bloqueado','419b54fb3d04791780954632567901ba30b29c2f0f69a1fe28d252c5c87190de',2,'2025-10-24 00:22:23','ADMIN','2025-11-06 00:37:01','ADMIN',NULL,1,'2026-10-19','deni06@gmail.com',1,0,0,'perfil.jpg'),(22,'232232222223333253','FERRERERE','FEREREREREE','Nuevo','$2y$10$aOFVLUb1aI9CNxlwqMCfhu06Es7OMSGNqsF/O.V9UxQmuaDnzS3s6',2,'2025-10-24 00:23:17','ADMIN',NULL,NULL,NULL,1,'2026-10-19','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(23,'55552225255232','DDD','DEEEDD','ACTIVO','12_mAklp',2,'2025-10-24 00:24:16','ADMIN','2025-10-26 01:20:58','SISTEMA',NULL,1,'2026-10-19','de1206@gmail.com',1,0,0,'perfil.jpg'),(24,'32323553325325','YEYE','YEYE','ACTIVO','$2y$10$N1Qh5a2PBpnD/JZ4hShg6O77q/Puj8UpxXcjWLXdf0uPnoXduR4L.',2,'2025-10-24 00:32:11','ADMIN','2025-10-25 18:01:23','SISTEMA',NULL,0,'2026-10-19','yeye1206@gmail.com',0,1,0,'perfil.jpg'),(25,'555555253','JONI','JONI SAL','ACTIVO','$2y$10$zJgRfXUXdXABnFB6kR64IO4ACa8LoUnoeuBVaP6J/5h92omgbqVBq',2,'2025-10-25 02:02:02','ADMIN','2025-10-25 18:01:09','SISTEMA',NULL,1,'2026-10-20','demes1206@gmail.com',0,0,0,'perfil.jpg'),(26,'3355533355535222','ZZZZ','ZZZZZ','Nuevo','$2y$10$m1UPQ3z0YdvSx4bOoNRMPub9HV6flDvEo2rZX8QP9.IEvrn2R3owq',2,'2025-10-25 03:08:35','ADMIN',NULL,NULL,NULL,1,'2026-10-20','zzzzz1206@gmail.com',0,0,0,'perfil.jpg'),(27,'222777777777','HUBER','HUBERT','ACTIVO','12345A_w',1,'2025-10-25 15:47:48','ADMIN','2025-10-25 19:29:30','ADMIN',NULL,1,'2026-10-20','denislopez1206@gmail.com',1,1,0,'perfil.jpg'),(28,'99990000066664444455','ADMINISTRADOR','ADMINISTRADOR DEL SISTEMA','ACTIVO','dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79',1,'2025-10-25 23:59:02','ADMIN','2025-10-26 01:10:44','SISTEMA','2025-11-07 21:45:20',1,'2026-10-20','denislopez1206@gmail.com',0,0,0,'user_28_1762292665.jpg'),(29,'99932532532932533532','FRANCIS','HUBERT','Bloqueado','$2y$10$Il2x0ziPZXCPpORp360/WO8B1Gz59BQ3bux0Oazbwnnzod8Jkd1su',2,'2025-10-26 14:15:12','ADMIN',NULL,NULL,NULL,1,'2026-10-21','denislopez1206@gmail.com',0,3,0,'perfil.jpg'),(30,'99999999999333333322','MARITZA','MARITZA YANEZ','Nuevo','bae7ce467b68fe01a4e18715f6689161f51b779d7aa823070799e0b79cde2ec1',2,'2025-10-26 15:17:41','ADMIN',NULL,NULL,NULL,0,'2026-10-21','dswwask@gmail.com',0,0,0,'perfil.jpg'),(31,'2325325329935','MURILLO','MURILLO','Nuevo','d809508763e8f04faf9db914c178fc1f9f5ad32afe028de8d1fa01f3375b93a3',1,'2025-10-26 15:24:41','ADMIN',NULL,NULL,NULL,0,'2026-10-21','z1206@gmail.com',0,0,0,'perfil.jpg'),(32,'233323555559','ILMA','ILMA ONEYDA','ACTIVO','e274eb59accf7f21390ae18147cb2db692193ab7548641ec68c6e4804ef00229',2,'2025-10-26 15:27:31','ADMIN','2025-10-27 07:12:02','SISTEMA','2025-10-27 07:10:45',1,'2026-10-21','denislopez1206@gmail.com',0,0,0,'perfil.jpg'),(33,'2523225','ALVARO','ALVARO TORRESbbb','Bloqueado','1c9647ec22b991992f3d3ebfc88fe357ca840399f2c563e0413c4dc5d6fe9cd5',2,'2025-10-26 17:59:24','REGISTRO','2025-11-01 03:14:58','ADMIN',NULL,1,NULL,'torresalvarez@gmail.com',1,0,0,'perfil.jpg'),(34,'55555523','DENISWA','DEDE DEDE DEDE','Nuevo','1fa37f5c35d48193403b9babbb59a1f00703b9277ffdc9409f58e755f2a8d33a',2,'2025-10-28 21:30:39','REGISTRO',NULL,NULL,'2025-10-28 21:31:59',0,NULL,'denisl@gmail.com',0,0,0,'perfil.jpg'),(35,'55555555552','DEDEERERS','DEDEDEDEE EERSDR','Nuevo','d866d6c89d50fa452689a0b936466e8ffcfd008d078f605e8cb67b6818061cbb',2,'2025-10-28 22:45:50','REGISTRO',NULL,NULL,NULL,0,NULL,'denislrrr@gmail.com',0,0,0,'perfil.jpg'),(36,'555522252232532','MIRNA','MIRNA VILLEDA','Bloqueado','dbcdd8a51942ad61595873e639f5abb7051c636579a34a1b67ef3ed5a4505e79',2,'2025-10-29 10:59:28','REGISTRO',NULL,NULL,NULL,0,NULL,'mirna1206@gmail.com',0,0,0,'perfil.jpg'),(37,'1206200100203','MANUEL','MANUEL ALEJANDRO TORRES','Nuevo','e274eb59accf7f21390ae18147cb2db692193ab7548641ec68c6e4804ef00229',2,'2025-11-04 03:32:04','ADMIN',NULL,NULL,'2025-11-04 05:08:02',0,'2026-10-30','manu1206@gmail.com',0,1,0,'perfil.jpg');
/*!40000 ALTER TABLE `tbl_ms_usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_usuario_insert` AFTER INSERT ON `tbl_ms_usuarios` FOR EACH ROW BEGIN
    INSERT INTO tbl_alertas_sistema (
        TIPO_ALERTA, TITULO, DESCRIPCION, ID_REFERENCIA, TABLA_REFERENCIA, 
        NIVEL_URGENCIA, FECHA_EXPIRACION, CREADO_POR
    ) VALUES (
        'NUEVO_USUARIO',
        CONCAT('Nuevo usuario: ', NEW.USUARIO),
        CONCAT('Se ha registrado el usuario: ', NEW.NOMBRE_USUARIO, ' (', NEW.USUARIO, ')'),
        NEW.ID_USUARIO,
        'tbl_ms_usuarios',
        'BAJA',
        DATE_ADD(NOW(), INTERVAL 24 HOUR),
        'TRIGGER'
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tbl_perdidas_produccion`
--

DROP TABLE IF EXISTS `tbl_perdidas_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_perdidas_produccion` (
  `ID_PERDIDA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCCION` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `CANTIDAD_PERDIDA` decimal(10,2) NOT NULL,
  `MOTIVO_PERDIDA` varchar(50) NOT NULL,
  `DESCRIPCION` text DEFAULT NULL,
  `FECHA_PERDIDA` datetime DEFAULT current_timestamp(),
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PERDIDA`),
  KEY `ID_PRODUCCION` (`ID_PRODUCCION`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  KEY `MOTIVO_PERDIDA` (`MOTIVO_PERDIDA`),
  CONSTRAINT `fk_perdidas_produccion` FOREIGN KEY (`ID_PRODUCCION`) REFERENCES `tbl_produccion` (`ID_PRODUCCION`) ON UPDATE CASCADE,
  CONSTRAINT `fk_perdidas_producto` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`) ON UPDATE CASCADE,
  CONSTRAINT `fk_perdidas_usuario` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_perdidas_produccion`
--

LOCK TABLES `tbl_perdidas_produccion` WRITE;
/*!40000 ALTER TABLE `tbl_perdidas_produccion` DISABLE KEYS */;
INSERT INTO `tbl_perdidas_produccion` VALUES (1,5,1,2,5.00,'DEF_CALIDA','Quemadas','2025-11-02 13:17:48','2025-11-02 13:17:48','Nuevo Usuario del Sistema',NULL,NULL),(2,7,1,28,17.00,'PROCESO','Descuido del personal','2025-11-04 03:59:36','2025-11-04 03:59:36','ADMINISTRADOR DEL SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_perdidas_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_produccion`
--

DROP TABLE IF EXISTS `tbl_produccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_produccion` (
  `ID_PRODUCCION` int(11) NOT NULL AUTO_INCREMENT,
  `ID_USUARIO` int(11) NOT NULL,
  `ID_ESTADO_PRODUCCION` int(11) NOT NULL,
  `ID_PRODUCTO` int(11) DEFAULT NULL,
  `FECHA_INICIO` datetime DEFAULT current_timestamp(),
  `FECHA_FIN` datetime DEFAULT NULL,
  `OBSERVACION` varchar(255) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  `CANTIDAD_PLANIFICADA` decimal(10,2) NOT NULL,
  `CANTIDAD_REAL` decimal(10,2) DEFAULT NULL,
  `COSTO_TOTAL` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`ID_PRODUCCION`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  KEY `ID_ESTADO_PRODUCCION` (`ID_ESTADO_PRODUCCION`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_produccion_ibfk_1` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`),
  CONSTRAINT `tbl_produccion_ibfk_2` FOREIGN KEY (`ID_ESTADO_PRODUCCION`) REFERENCES `tbl_estado_produccion` (`ID_ESTADO_PRODUCCION`),
  CONSTRAINT `tbl_produccion_ibfk_3` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_produccion`
--

LOCK TABLES `tbl_produccion` WRITE;
/*!40000 ALTER TABLE `tbl_produccion` DISABLE KEYS */;
INSERT INTO `tbl_produccion` VALUES (1,1,2,NULL,'2024-10-02 08:00:00','2024-10-02 12:00:00','Producción de rosquillas clásicas','2025-10-27 10:30:07','SISTEMA',NULL,NULL,0.00,NULL,0.00),(2,1,2,NULL,'2024-10-03 08:00:00','2024-10-03 13:00:00','Producción de rosquillas de chocolate','2025-10-27 10:30:07','SISTEMA',NULL,NULL,0.00,NULL,0.00),(3,1,2,NULL,'2024-10-06 08:00:00','2024-10-06 11:30:00','Producción mixta','2025-10-27 10:30:07','SISTEMA',NULL,NULL,0.00,NULL,0.00),(4,2,1,1,'2025-11-02 05:54:04',NULL,'Producción especial ','2025-11-02 05:54:04','Nuevo Usuario del Sistema',NULL,NULL,20.00,NULL,0.00),(5,2,3,1,'2025-11-02 05:59:43','2025-11-02 13:17:48','Orden especial ','2025-11-02 05:59:43','Nuevo Usuario del Sistema','2025-11-02 13:17:48','Nuevo Usuario del Sistema',20.00,15.00,73.00),(6,2,2,1,'2025-11-02 06:33:12',NULL,'ddd','2025-11-02 06:33:12','Nuevo Usuario del Sistema','2025-11-07 22:35:15','ADMINISTRADOR DEL SISTEMA',2.00,NULL,0.00),(7,28,3,1,'2025-11-04 03:55:25','2025-11-04 03:59:36','Orden para Bazar el Sábado','2025-11-04 03:55:25','ADMINISTRADOR DEL SISTEMA','2025-11-04 03:59:36','ADMINISTRADOR DEL SISTEMA',22.00,5.00,80.30),(8,28,1,2,'2025-11-04 04:31:54',NULL,'dvsd','2025-11-04 04:31:54','ADMINISTRADOR DEL SISTEMA',NULL,NULL,2.00,NULL,0.00);
/*!40000 ALTER TABLE `tbl_produccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_producto`
--

DROP TABLE IF EXISTS `tbl_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_producto` (
  `ID_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `PRECIO` decimal(10,2) NOT NULL,
  `ID_UNIDAD_MEDIDA` int(11) NOT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  `CANTIDAD` decimal(10,2) DEFAULT 0.00,
  `MINIMO` decimal(10,2) DEFAULT 0.00,
  `MAXIMO` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`ID_PRODUCTO`),
  KEY `ID_UNIDAD_MEDIDA` (`ID_UNIDAD_MEDIDA`),
  CONSTRAINT `tbl_producto_ibfk_1` FOREIGN KEY (`ID_UNIDAD_MEDIDA`) REFERENCES `tbl_unidad_medida` (`ID_UNIDAD_MEDIDA`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_producto`
--

LOCK TABLES `tbl_producto` WRITE;
/*!40000 ALTER TABLE `tbl_producto` DISABLE KEYS */;
INSERT INTO `tbl_producto` VALUES (1,'Rosquilla Clásica','Rosquilla tradicional espolvoreada con azúcar',12.00,4,'ACTIVO','2025-10-27 10:01:56','SISTEMA','2025-11-04 10:42:17','ADMINISTRADOR DEL SISTEMA',10.00,50.00,300.00),(2,'Rosquilla Glaseada','Rosquilla con glaseado blanco brillante',15.00,4,'ACTIVO','2025-10-27 10:01:56','SISTEMA',NULL,NULL,0.00,90.00,300.00),(3,'Rosquilla de Chocolate','Rosquilla bañada en chocolate semiamargo',18.00,4,'ACTIVO','2025-10-27 10:01:56','SISTEMA',NULL,NULL,0.00,80.00,200.00),(4,'Rosquilla de Canela','\'Rosquilla con mezcla de canela y azúcar',14.00,4,'ACTIVO','2025-10-27 10:01:56','SISTEMA',NULL,NULL,0.00,40.00,200.00);
/*!40000 ALTER TABLE `tbl_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_proveedor`
--

DROP TABLE IF EXISTS `tbl_proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_proveedor` (
  `ID_PROVEEDOR` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(100) NOT NULL,
  `CONTACTO` varchar(100) DEFAULT NULL,
  `TELEFONO` varchar(20) DEFAULT NULL,
  `CORREO` varchar(50) DEFAULT NULL,
  `DIRECCION` varchar(255) DEFAULT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PROVEEDOR`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_proveedor`
--

LOCK TABLES `tbl_proveedor` WRITE;
/*!40000 ALTER TABLE `tbl_proveedor` DISABLE KEYS */;
INSERT INTO `tbl_proveedor` VALUES (1,'Distribuidora de Alimentos S.A.','Juan Carlos Rodríguez','2233-4455','ventasdistribuidora.@gmail.com','Boulevard Morazán, Tegucigalpa','ACTIVO','2025-10-27 10:01:56','SISTEMA','2025-11-01 00:50:47','SISTEMA'),(2,'Lácteos Honduras','María Elena Santos','2244-5566','info@lacteoshonduras.com','Colonia Trejo, San Pedro Sula','ACTIVO','2025-10-27 10:01:56','SISTEMA','2025-10-28 21:20:53','SISTEMA'),(3,'Dulcería La Esperanza','Carlos Antonio Mejía','2255-6677','contacto@dulceriaesperanza.com','Barrio Abajo, Comayagua','ACTIVO','2025-10-27 10:01:56','SISTEMA',NULL,NULL),(7,'Lacthosa SA','Denis Irwin','504 9569-1381','denislopez1206@gmail.com','Comayagüela M.C','ACTIVO','2025-10-28 05:40:05','SISTEMA','2025-10-31 19:02:01','SISTEMA'),(8,'Mercadito','Denis Irwin López','504 9316-9128','denislopez@gmail.com','Col. Flor del Campo Zona 2','ACTIVO','2025-10-28 21:23:06','SISTEMA','2025-10-28 21:36:31','SISTEMA'),(9,'Yummies','Andrea Rashell','504 9369-1281','de@gmail.com','Col. Flor del Campo Zona 2','ACTIVO','2025-10-29 16:54:37','SISTEMA',NULL,NULL),(10,'NutreFood HN','Alberto Álvarez','50493691281','denislopez1206@gmail.com','Col. Flor del Campo Zona 2','ACTIVO','2025-11-01 03:56:43','SISTEMA',NULL,NULL),(11,'Harineras de Honduras S.A','Roberto Martínez','2234-5678','ventasharinerashn.@gmail.com','Zona Industrial, Tegucigalpa','ACTIVO','2025-11-01 08:05:55','SISTEMA',NULL,NULL),(12,'Dulces y Especias S.A.','Sofia Ramírez','2278-9012','infodulcesyespecias.@gmail.com','Barrio La Bolsa, San Pedro Sula','ACTIVO','2025-11-01 08:07:13','SISTEMA',NULL,NULL),(13,'Chocolates Maya','Miguel Ángel Reyes','2256-7890','pedidoschocolatesmaya.@gmail.com','Carretera a Valle de Ángeles','ACTIVO','2025-11-01 08:08:44','SISTEMA',NULL,NULL),(14,'Distribuidora La Favorita','Ana Lucia Castro','2245-6789','ventaslafavorita.@gmail.com','Colonia Palmira, Tegucigalpa','ACTIVO','2025-11-01 08:09:58','SISTEMA',NULL,NULL),(15,'Importadora de Alimentos','Jorge Luis Herrera','2289-0123','importacionesalimentoshn@hotmail.com','Puerto Cortés','ACTIVO','2025-11-01 08:11:23','SISTEMA',NULL,NULL),(16,'Distribuidora Carrasco S.A.','Cesar Andrés Carrasco','93691281','denislopez1206@gmail.com','Col. Flor del Campo Zona 2','ACTIVO','2025-11-04 03:46:59','SISTEMA','2025-11-04 03:47:37','SISTEMA');
/*!40000 ALTER TABLE `tbl_proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_proveedor_productos`
--

DROP TABLE IF EXISTS `tbl_proveedor_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_proveedor_productos` (
  `ID_PROVEEDOR_PRODUCTO` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PROVEEDOR` int(11) NOT NULL,
  `ID_UNIDAD_MEDIDA` int(11) NOT NULL,
  `NOMBRE_PRODUCTO` varchar(100) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `PRECIO_UNITARIO` decimal(10,2) NOT NULL,
  `ESTADO` varchar(20) DEFAULT 'ACTIVO',
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_PROVEEDOR_PRODUCTO`),
  KEY `ID_PROVEEDOR` (`ID_PROVEEDOR`),
  KEY `ID_UNIDAD_MEDIDA` (`ID_UNIDAD_MEDIDA`),
  CONSTRAINT `fk_proveedor_productos_proveedor` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `tbl_proveedor` (`ID_PROVEEDOR`),
  CONSTRAINT `tbl_proveedor_productos_ibfk_1` FOREIGN KEY (`ID_UNIDAD_MEDIDA`) REFERENCES `tbl_unidad_medida` (`ID_UNIDAD_MEDIDA`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_proveedor_productos`
--

LOCK TABLES `tbl_proveedor_productos` WRITE;
/*!40000 ALTER TABLE `tbl_proveedor_productos` DISABLE KEYS */;
INSERT INTO `tbl_proveedor_productos` VALUES (1,1,5,'Harina de trigo premium','Harina de trigo para panadería',16.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(2,1,5,'Azúcar refinada','Azúcar blanca refinada',13.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(3,1,5,'Mantequilla sin sal','Mantequilla 100% natural',17.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(4,2,3,'Leche entera','Leche pasteurizada',12.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(5,2,5,'Quesillo artesanal','Quesillo fresco',62.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(6,2,5,'Cuajada tradicional','Cuajada cremosa',52.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(7,3,2,'Esencia de vainilla','Esencia natural para repostería',13.00,'ACTIVO','2025-10-30 02:55:50','2025-10-31 00:17:46','SISTEMA','SISTEMA'),(8,3,2,'Canela molida','Canela en polvo de alta calidad',49.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(9,7,3,'Leche Láctea','Leche ultrapasteurizada',11.80,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(10,7,5,'Queso blanco','Queso fresco',62.50,'ACTIVO','2025-10-30 02:55:50','2025-11-04 03:40:24','SISTEMA','SISTEMA'),(11,8,5,'Harina de maíz','Harina para tortillas',12.00,'ACTIVO','2025-10-30 02:55:50','2025-11-04 00:30:15','SISTEMA','SISTEMA'),(12,8,4,'Huevos blancos','Huevos grade A',5.60,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(13,9,2,'Levadura instantánea','Levadura en polvo',15.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(14,9,3,'Colorante alimenticio','Colorante rojo',25.00,'ACTIVO','2025-10-30 02:55:50',NULL,'SISTEMA',NULL),(15,8,5,'Manteca','Manteca vegetal',25.00,'ACTIVO','2025-11-01 00:29:17','2025-11-01 03:10:04','ADMIN','SISTEMA'),(16,11,1,'Harina para repostería','Harina especial para donas y rosquillas',28.00,'ACTIVO','2025-11-01 08:16:13',NULL,'ADMIN',NULL),(17,11,1,'Harina de trigo todo uso','Harina multipropósito',25.50,'ACTIVO','2025-11-01 08:18:08',NULL,'ADMIN',NULL),(18,11,1,'Polvo de hornear','Polvo para hornear de alta calidad',45.00,'ACTIVO','2025-11-01 08:20:54',NULL,'ADMIN',NULL),(19,12,1,'Azúcar glass','Azúcar pulverizada para glaseados',32.00,'ACTIVO','2025-11-01 08:35:22',NULL,'ADMIN',NULL),(20,12,1,'Canela en rama','Canela de Ceilán premium',85.00,'ACTIVO','2025-11-01 08:36:34',NULL,'ADMIN',NULL),(21,12,1,'Esencia de anís','Esencia natural para repostería',38.00,'ACTIVO','2025-11-01 08:37:23',NULL,'ADMIN',NULL),(22,12,1,'Colorante en gel','Colorante profesional para alimentos',42.00,'ACTIVO','2025-11-01 08:38:09',NULL,'ADMIN',NULL),(23,13,1,'Chocolate para cobertura','&#039;Chocolate semiamargo para bañar',65.00,'ACTIVO','2025-11-01 08:39:20',NULL,'ADMIN',NULL),(24,13,2,'Cacao en polvo','Cacao 100% natural',53.00,'ACTIVO','2025-11-01 08:39:59','2025-11-04 03:40:06','ADMIN','SISTEMA'),(25,13,1,'Chispas de chocolate','Chips de chocolate semidulce',48.00,'ACTIVO','2025-11-01 08:41:04',NULL,'ADMIN',NULL),(26,14,3,'Aceite vegetal','Aceite para freír rosquillas',35.00,'ACTIVO','2025-11-01 08:42:22',NULL,'ADMIN',NULL),(27,14,1,'Sal refinada','Sal fina para repostería',8.50,'ACTIVO','2025-11-01 08:43:06',NULL,'ADMIN',NULL),(28,14,4,'Huevos extra grandes','Huevos grade AA',6.20,'ACTIVO','2025-11-01 08:44:02',NULL,'ADMIN',NULL),(29,14,3,'Leche evaporada','Leche evaporada completa',28.00,'ACTIVO','2025-11-01 08:44:58',NULL,'ADMIN',NULL),(30,15,1,'Nuez moscada molida','Especia para dar sabor',92.00,'ACTIVO','2025-11-01 08:45:49',NULL,'ADMIN',NULL),(31,15,1,'Jengibre en polvo','Jengibre molido para galletas',78.00,'ACTIVO','2025-11-01 08:46:46',NULL,'ADMIN',NULL),(32,15,1,'Extracto de almendra','Extracto puro de almendra',65.00,'ACTIVO','2025-11-01 08:48:03',NULL,'ADMIN',NULL),(33,2,5,'Mantequilla crema','Mantequilla Olancho',32.00,'ACTIVO','2025-11-04 00:35:02',NULL,'ADMIN',NULL),(34,8,2,'Levadura química','Sobre pequeño',5.00,'ACTIVO','2025-11-04 03:44:19',NULL,'ADMIN',NULL);
/*!40000 ALTER TABLE `tbl_proveedor_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_recepcion_compra`
--

DROP TABLE IF EXISTS `tbl_recepcion_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_recepcion_compra` (
  `ID_RECEPCION` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PROVEEDOR` int(11) NOT NULL,
  `ID_USUARIO` int(11) NOT NULL,
  `TOTAL_ORDEN` decimal(10,2) NOT NULL,
  `FECHA_RECEPCION` datetime DEFAULT current_timestamp(),
  `ESTADO_RECEPCION` varchar(20) DEFAULT 'PENDIENTE',
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `MOTIVO_CANCELACION` varchar(255) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_RECEPCION`),
  KEY `ID_PROVEEDOR` (`ID_PROVEEDOR`),
  KEY `ID_USUARIO` (`ID_USUARIO`),
  CONSTRAINT `tbl_recepcion_compra_ibfk_2` FOREIGN KEY (`ID_PROVEEDOR`) REFERENCES `tbl_proveedor` (`ID_PROVEEDOR`),
  CONSTRAINT `tbl_recepcion_compra_ibfk_3` FOREIGN KEY (`ID_USUARIO`) REFERENCES `tbl_ms_usuarios` (`ID_USUARIO`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_recepcion_compra`
--

LOCK TABLES `tbl_recepcion_compra` WRITE;
/*!40000 ALTER TABLE `tbl_recepcion_compra` DISABLE KEYS */;
INSERT INTO `tbl_recepcion_compra` VALUES (1,1,1,26.00,'2025-10-30 21:54:30','FINALIZADA','','SISTEMA','2025-10-30 21:54:30',NULL,NULL,NULL),(2,7,1,59.00,'2025-10-30 22:39:23','FINALIZADA','','SISTEMA','2025-10-30 22:39:23',NULL,NULL,NULL),(3,8,1,54.00,'2025-10-30 22:48:47','FINALIZADA','','SISTEMA','2025-10-30 22:48:47',NULL,NULL,NULL),(4,1,1,26.00,'2025-10-30 23:47:00','FINALIZADA','','SISTEMA','2025-10-30 23:47:00',NULL,NULL,NULL),(5,2,1,104.00,'2025-10-30 23:50:05','CANCELADA','','SISTEMA','2025-10-30 23:50:05','Hacen falta cantidades de los productos','2025-10-31 00:53:00','SISTEMA'),(6,3,1,26.00,'2025-10-31 00:17:46','CANCELADA','','SISTEMA','2025-10-31 00:17:46','bede','2025-10-31 01:37:47','SISTEMA'),(7,8,1,37.20,'2025-10-31 01:33:32','FINALIZADA','','SISTEMA','2025-10-31 01:33:32',NULL,NULL,NULL),(8,1,1,26.00,'2025-10-31 01:37:28','FINALIZADA','','SISTEMA','2025-10-31 01:37:28',NULL,NULL,NULL),(9,9,1,150.00,'2025-10-31 18:44:42','FINALIZADA','','SISTEMA','2025-10-31 18:44:42',NULL,NULL,NULL),(10,1,1,65.00,'2025-10-31 18:46:57','CANCELADA','des','SISTEMA','2025-10-31 18:46:57','No llegó el pedido','2025-11-04 00:39:01','SISTEMA'),(11,11,1,370.00,'2025-11-01 08:51:49','FINALIZADA','','SISTEMA','2025-11-01 08:51:49',NULL,NULL,NULL),(12,12,1,160.00,'2025-11-01 08:52:33','FINALIZADA','','SISTEMA','2025-11-01 08:52:33',NULL,NULL,NULL),(13,13,1,353.00,'2025-11-01 08:55:17','FINALIZADA','','SISTEMA','2025-11-01 08:55:17',NULL,NULL,NULL),(14,14,1,592.00,'2025-11-01 08:56:49','FINALIZADA','','SISTEMA','2025-11-01 08:56:49',NULL,NULL,NULL),(15,2,1,100.00,'2025-11-04 00:37:24','FINALIZADA','','SISTEMA','2025-11-04 00:37:24',NULL,NULL,NULL),(16,12,1,560.00,'2025-11-04 03:49:23','CANCELADA','Para mañana','SISTEMA','2025-11-04 03:49:23','No llegó','2025-11-04 03:51:35','SISTEMA'),(17,2,1,160.00,'2025-11-04 03:50:08','PENDIENTE','vvas','SISTEMA','2025-11-04 03:50:08',NULL,NULL,NULL);
/*!40000 ALTER TABLE `tbl_recepcion_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_receta`
--

DROP TABLE IF EXISTS `tbl_receta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_receta` (
  `ID_RECETA` int(11) NOT NULL AUTO_INCREMENT,
  `ID_PRODUCTO` int(11) NOT NULL,
  `ID_MATERIA_PRIMA` int(11) NOT NULL,
  `CANTIDAD_NECESARIA` decimal(10,2) NOT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_RECETA`),
  KEY `ID_PRODUCTO` (`ID_PRODUCTO`),
  KEY `ID_MATERIA_PRIMA` (`ID_MATERIA_PRIMA`),
  CONSTRAINT `tbl_receta_ibfk_1` FOREIGN KEY (`ID_PRODUCTO`) REFERENCES `tbl_producto` (`ID_PRODUCTO`),
  CONSTRAINT `tbl_receta_ibfk_2` FOREIGN KEY (`ID_MATERIA_PRIMA`) REFERENCES `tbl_materia_prima` (`ID_MATERIA_PRIMA`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_receta`
--

LOCK TABLES `tbl_receta` WRITE;
/*!40000 ALTER TABLE `tbl_receta` DISABLE KEYS */;
INSERT INTO `tbl_receta` VALUES (43,1,23,0.08,'2025-11-01 09:24:21','SISTEMA',NULL,NULL),(44,1,24,0.01,'2025-11-01 09:24:21','SISTEMA',NULL,NULL),(45,1,25,0.03,'2025-11-01 09:24:21','SISTEMA',NULL,NULL),(46,2,23,0.08,'2025-11-01 09:25:45','SISTEMA',NULL,NULL),(47,2,23,0.00,'2025-11-01 09:25:45','SISTEMA',NULL,NULL),(48,2,25,0.04,'2025-11-01 09:25:45','SISTEMA',NULL,NULL),(49,4,29,0.05,'2025-11-03 20:04:39','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(50,4,19,0.02,'2025-11-03 20:04:39','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(51,4,22,2.00,'2025-11-03 20:04:39','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(52,4,24,0.09,'2025-11-03 20:04:39','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(53,3,23,5.00,'2025-11-04 04:02:38','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(54,3,20,0.50,'2025-11-04 04:02:38','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(55,3,24,5.00,'2025-11-04 04:02:38','ADMINISTRADOR DEL SISTEMA',NULL,NULL),(56,3,29,0.50,'2025-11-04 04:02:38','ADMINISTRADOR DEL SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_receta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_unidad_medida`
--

DROP TABLE IF EXISTS `tbl_unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_unidad_medida` (
  `ID_UNIDAD_MEDIDA` int(11) NOT NULL AUTO_INCREMENT,
  `UNIDAD` varchar(20) NOT NULL,
  `DESCRIPCION` varchar(50) DEFAULT NULL,
  `FECHA_CREACION` datetime DEFAULT current_timestamp(),
  `CREADO_POR` varchar(50) DEFAULT NULL,
  `FECHA_MODIFICACION` datetime DEFAULT NULL,
  `MODIFICADO_POR` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_UNIDAD_MEDIDA`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_unidad_medida`
--

LOCK TABLES `tbl_unidad_medida` WRITE;
/*!40000 ALTER TABLE `tbl_unidad_medida` DISABLE KEYS */;
INSERT INTO `tbl_unidad_medida` VALUES (1,'KG','Kilogramos','2025-10-27 10:01:56','SISTEMA',NULL,NULL),(2,'GR','Gramos','2025-10-27 10:01:56','SISTEMA',NULL,NULL),(3,'LT','Litros','2025-10-27 10:01:56','SISTEMA',NULL,NULL),(4,'UN','Unidades','2025-10-27 10:01:56','SISTEMA',NULL,NULL),(5,'LB','Libras','2025-10-27 10:01:56','SISTEMA',NULL,NULL);
/*!40000 ALTER TABLE `tbl_unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-18  1:47:03
