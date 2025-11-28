<!-- Grid de Productos -->
<div id="productosGrid" class="row g-3" style="min-height: 400px;">
    <div class="col-12 text-center py-5">
        <p class="text-muted">Seleccione una categoría para ver productos</p>
    </div>
</div>

<template id="tarjetaProductoTemplate">
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 producto-card" style="cursor: pointer; transition: all 0.3s;">
            <div class="card-body">
                <h6 class="card-title text-truncate producto-nombre"></h6>
                <p class="card-text text-muted small producto-descripcion" style="min-height: 40px; overflow: hidden;"></p>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-info producto-stock"></span>
                    <span class="h6 mb-0 text-success producto-precio">S/. 0.00</span>
                </div>

                <div class="input-group input-group-sm mb-2" style="display: none;">
                    <button class="btn btn-outline-danger btn-sm btnMenos" type="button">-</button>
                    <input type="number" class="form-control text-center cantidadProducto" value="1" min="1" readonly>
                    <button class="btn btn-outline-success btn-sm btnMas" type="button">+</button>
                </div>

                <button class="btn btn-primary btn-sm w-100 btnAgregarCarrito">
                    <i class="bx bx-cart-add"></i> Agregar
                </button>
            </div>
        </div>
    </div>
</template>
