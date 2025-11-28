<!-- Carrito de Compras -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">Carrito de Compras</h6>
    </div>
    <div class="card-body">
        <div id="carritoVacio" class="text-center py-4">
            <p class="text-muted">El carrito está vacío</p>
        </div>
        
        <div id="carritoLleno" style="display: none;">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCarrito">
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select id="selectCliente" class="form-select form-select-sm" required>
                        <option value="">-- Seleccionar Cliente --</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-sm btn-link mt-4" id="btnNuevoCliente" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                        + Nuevo Cliente
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Método de Pago</label>
                    <select id="selectMetodoPago" class="form-select form-select-sm" required>
                        <option value="">-- Seleccionar Método --</option>
                    </select>
                </div>
            </div>

            <div class="bg-light p-3 rounded">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-2"><strong>Total Items:</strong> <span id="totalItems">0</span></p>
                        <p class="mb-0"><strong>Total Venta:</strong></p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-2"><span id="cantidadItems">0</span> productos</p>
                        <h5 class="text-success">S/. <span id="totalVenta">0.00</span></h5>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-grid gap-2">
                <button class="btn btn-success btn-lg" id="btnConfirmarVenta">
                    <i class="bx bx-check"></i> Confirmar Venta
                </button>
                <button class="btn btn-danger btn-sm" id="btnVaciarCarrito">
                    Vaciar Carrito
                </button>
            </div>
        </div>
    </div>
</div>
