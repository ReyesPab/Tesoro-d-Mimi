<?php

/**
 * Configuración del Módulo de Ventas
 */

namespace modulo_ventas\config;

class VentasConfig
{
    /**
     * Categorías de productos
     */
    const CATEGORIAS = [
        'MAIZ' => [
            'nombre' => 'Productos de Maíz',
            'icono' => 'bx-leaf',
            'pattern' => '%maiz%'
        ],
        'GOLOSINAS' => [
            'nombre' => 'Golosinas',
            'icono' => 'bx-candy',
            'pattern' => '%golosina%'
        ],
        'BEBIDAS' => [
            'nombre' => 'Bebidas',
            'icono' => 'bxs-drink',
            'pattern' => '%bebida%'
        ]
    ];

    /**
     * Estados de factura
     */
    const ESTADOS_FACTURA = [
        'PAGADA' => 'Pagada',
        'PENDIENTE' => 'Pendiente',
        'ANULADA' => 'Anulada'
    ];

    /**
     * Tipos de movimiento en cardex
     */
    const TIPOS_MOVIMIENTO = [
        'ENTRADA' => 'Entrada de stock',
        'SALIDA' => 'Salida por venta',
        'AJUSTE' => 'Ajuste de inventario'
    ];

    /**
     * Obtener patrón de búsqueda para categoría
     */
    public static function getPatron($categoria)
    {
        return self::CATEGORIAS[strtoupper($categoria)]['pattern'] ?? '%';
    }

    /**
     * Obtener nombre de categoría
     */
    public static function getNombreCategoria($categoria)
    {
        return self::CATEGORIAS[strtoupper($categoria)]['nombre'] ?? $categoria;
    }
}
