# 📦 MÓDULO DE VENTAS - DOCUMENTACIÓN

## 📋 Descripción General

El **Módulo de Ventas** es un sistema completo para registrar ventas de productos categorizados (Maíz, Golosinas, Bebidas) con integración automática de:
- Gestión de clientes
- Generación de facturas
- Control de inventario
- Registro de movimientos en cardex
- Métodos de pago seleccionables

## 🏗️ Estructura de Archivos

```
src/
├── config/modulo_ventas/
│   └── VentasConfig.php          # Configuración centralizada
├── models/modulo_ventas/
│   ├── ventasModel.php           # Lógica de ventas y facturas
│   └── clienteModel.php          # Gestión de clientes
├── controllers/modulo_ventas/
│   └── ventasController.php      # Controlador principal
├── routes/modulo_ventas/
│   └── ventas.php                # Router de endpoints
└── Views/modulo_ventas/
    ├── registrar-venta.php       # Vista principal
    └── partials/
        ├── modal_nuevo_cliente.php
        ├── carrito.php
        └── grid_productos.php
```

## 🔄 Flujo de Venta (5 Pasos)

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. GESTIÓN DEL CLIENTE                                          │
│    - Buscar por DNI                                             │
│    - Si no existe: Registrar nuevo cliente                      │
└────────────────────┬────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. GENERAR FACTURA                                              │
│    - INSERT en tbl_factura                                      │
│    - ID_CLIENTE, ID_METODO_PAGO, TOTAL, FECHA_VENTA, ESTADO    │
└────────────────────┬────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. REGISTRAR DETALLES DE FACTURA                                │
│    - Verificar stock de cada producto                           │
│    - INSERT en tbl_detalle_factura                              │
│    - ID_FACTURA, ID_PRODUCTO, CANTIDAD, PRECIO_VENTA, SUBTOTAL │
└────────────────────┬────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. REGISTRAR MOVIMIENTO EN CARDEX (SALIDA)                      │
│    - INSERT en tbl_cardex_producto                              │
│    - TIPO_MOVIMIENTO = 'SALIDA'                                 │
│    - REFERENCIA: 'Venta - Factura #ID'                          │
└────────────────────┬────────────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────────────┐
│ 5. ACTUALIZAR INVENTARIO PRODUCTO                               │
│    - UPDATE tbl_inventario_producto                             │
│    - CANTIDAD -= cantidad_vendida                               │
│    - MODIFICADO_POR, FECHA_MODIFICACION                         │
└─────────────────────────────────────────────────────────────────┘
```

## 🔌 Endpoints API

Todas las rutas usan: `/src/routes/modulo_ventas/ventas.php?caso=...`

### **Productos**

#### Obtener Categorías
```
GET /src/routes/modulo_ventas/ventas.php?caso=obtenerCategorias
Response:
{
  "success": true,
  "data": [
    {"id": "MAIZ", "nombre": "Productos de Maíz"},
    {"id": "GOLOSINAS", "nombre": "Golosinas"},
    {"id": "BEBIDAS", "nombre": "Bebidas"}
  ]
}
```

#### Obtener Productos por Categoría
```
POST /src/routes/modulo_ventas/ventas.php?caso=obtenerProductosPorCategoria
Body: { "categoria": "MAIZ" }

Response:
{
  "success": true,
  "data": [
    {
      "ID_PRODUCTO": 1,
      "NOMBRE": "Maíz Blanco",
      "DESCRIPCION": "...",
      "PRECIO": 2.50,
      "CANTIDAD": 100,
      "MINIMO": 20,
      "MAXIMO": 500
    }
  ]
}
```

### **Clientes**

#### Buscar Cliente por DNI
```
POST /src/routes/modulo_ventas/ventas.php?caso=buscarClientePorDNI
Body: { "dni": "12345678" }

Response:
{
  "success": true,
  "data": {
    "ID_CLIENTE": 1,
    "NOMBRE": "Juan",
    "APELLIDO": "Pérez",
    "DNI": "12345678",
    "TELEFONO": "987654321",
    "CORREO": "juan@email.com",
    "DIRECCION": "Calle Principal 123"
  }
}
```

#### Buscar Clientes (nombre/apellido/DNI)
```
POST /src/routes/modulo_ventas/ventas.php?caso=buscarClientes
Body: { "busqueda": "juan" }

Response: array de clientes
```

#### Crear Nuevo Cliente
```
POST /src/routes/modulo_ventas/ventas.php?caso=crearClienteNuevo
Body: {
  "NOMBRE": "Carlos",
  "APELLIDO": "García",
  "DNI": "87654321",
  "TELEFONO": "987654321",
  "CORREO": "carlos@email.com",
  "DIRECCION": "Calle Secundaria 456"
}

Response:
{
  "success": true,
  "data": { "id_cliente": 2 },
  "message": "Cliente creado exitosamente"
}
```

### **Métodos de Pago**

#### Obtener Métodos de Pago
```
GET /src/routes/modulo_ventas/ventas.php?caso=obtenerMetodosPago

Response:
{
  "success": true,
  "data": [
    {"ID_METODO_PAGO": 1, "METODO_PAGO": "Efectivo", "DESCRIPCION": "..."},
    {"ID_METODO_PAGO": 2, "METODO_PAGO": "Tarjeta", "DESCRIPCION": "..."}
  ]
}
```

### **Ventas**

#### Crear Venta (Función Principal)
```
POST /src/routes/modulo_ventas/ventas.php?caso=crearVenta
Body: {
  "ID_CLIENTE": 1,
  "ID_METODO_PAGO": 1,
  "TOTAL": 125.50,
  "ITEMS": [
    {
      "ID_PRODUCTO": 1,
      "NOMBRE": "Maíz Blanco",
      "CANTIDAD": 2,
      "PRECIO": 2.50
    },
    {
      "ID_PRODUCTO": 3,
      "NOMBRE": "Caramelo",
      "CANTIDAD": 5,
      "PRECIO": 25.00
    }
  ]
}

Response:
{
  "success": true,
  "data": { "id_factura": 42 },
  "message": "Venta registrada exitosamente"
}
```

#### Obtener Detalles de Factura
```
GET /src/routes/modulo_ventas/ventas.php?caso=obtenerDetallesFactura&id_factura=42

Response:
{
  "success": true,
  "data": {
    "ID_FACTURA": 42,
    "TOTAL_VENTA": 125.50,
    "FECHA_VENTA": "2025-11-15 10:30:00",
    "CLIENTE_NOMBRE": "Juan",
    "CLIENTE_DNI": "12345678",
    "METODO_PAGO": "Efectivo",
    "DETALLES": [
      {
        "ID_DETALLE_FACTURA": 1,
        "PRODUCTO_NOMBRE": "Maíz Blanco",
        "CANTIDAD": 2,
        "PRECIO_VENTA": 2.50,
        "SUBTOTAL": 5.00
      }
    ]
  }
}
```

#### Listar Facturas Recientes
```
GET /src/routes/modulo_ventas/ventas.php?caso=listarFacturas&limite=20

Response: array de facturas
```

## 📊 Tablas de Base de Datos Utilizadas

| Tabla | Propósito |
|-------|-----------|
| `tbl_factura` | Registro principal de ventas |
| `tbl_detalle_factura` | Items de cada venta |
| `tbl_cliente` | Datos de clientes |
| `tbl_metodo_pago` | Métodos de pago disponibles |
| `tbl_producto` | Catálogo de productos |
| `tbl_inventario_producto` | Stock actual de productos |
| `tbl_cardex_producto` | Historial de movimientos |
| `tbl_ms_usuarios` | Usuario que registra venta (CREADO_POR) |

## 🎨 Vista Principal (registrar-venta.php)

### Componentes:

1. **Botones de Categoría** (Top-Center)
   - Productos de Maíz
   - Golosinas
   - Bebidas

2. **Grid de Productos**
   - Tarjetas dinámicas con imagen (si aplica)
   - Nombre, descripción, precio
   - Indicador de stock
   - Botón "Agregar al Carrito"

3. **Carrito de Compras** (Sidebar derecho)
   - Tabla con items
   - Editar cantidad
   - Eliminar items
   - Selector de cliente
   - Selector de método de pago
   - Total de venta
   - Botón "Confirmar Venta"

4. **Modal de Nuevo Cliente**
   - Campos: Nombre, Apellido, DNI, Teléfono, Correo, Dirección

## 🔐 Seguridad Implementada

✅ **Validación de Sesión**: Solo usuarios autenticados  
✅ **Sanitización de Entrada**: `Security::sanitizeInput()`  
✅ **Prepared Statements**: Previene SQL injection  
✅ **Transacciones**: Rollback automático si hay error  
✅ **Error Logging**: Registro en `php-error.log`  
✅ **Gestión de Stock**: Verificación antes de vender  

## ⚠️ Manejo de Errores

- **400**: Validación fallida
- **401**: Usuario no autenticado
- **404**: Recurso no encontrado
- **405**: Método HTTP no permitido
- **500**: Error del servidor (registrado en logs)

## 🚀 Cómo Usar

### 1. Acceder al Módulo
```
http://localhost/src/Views/modulo_ventas/registrar-venta.php
```

### 2. Seleccionar Categoría
Presiona uno de los 3 botones de categoría para cargar productos

### 3. Agregar Productos al Carrito
- Click en "Agregar" en el producto
- Ajusta cantidad con +/-
- El producto se añade al carrito

### 4. Gestionar Cliente
- Opción A: Buscar cliente existente por DNI
- Opción B: Crear nuevo cliente en modal

### 5. Seleccionar Método de Pago
Elige de la lista desplegable

### 6. Confirmar Venta
Click en "Confirmar Venta" para:
- Crear factura
- Registrar detalles
- Actualizar inventario
- Registrar cardex

## 📱 Validaciones Frontend

✅ Carrito no vacío  
✅ Cliente seleccionado  
✅ Método de pago seleccionado  
✅ Stock suficiente  
✅ Cantidad válida  

## 🔧 Extensiones Futuras

- [ ] Impresión/descarga de factura (PDF)
- [ ] Buscar cliente en el sistema existente
- [ ] Descuento por cliente/producto
- [ ] Devoluciones/anulación de ventas
- [ ] Reportes de ventas
- [ ] Integración de pago (POS, transferencia)

## 📞 Soporte

Para problemas o consultas, revisar logs en:
- `src/logs/php-error.log`
- Consola del navegador (F12)

---

**Última actualización**: 15 de noviembre de 2025  
**Versión**: 1.0.0  
**Estado**: ✅ Completo
