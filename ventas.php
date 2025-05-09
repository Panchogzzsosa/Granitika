<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granatika - Ventas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">
                <img src="img/favicon.png" alt="Logo Granatika">
            </div>
            <div class="brand-title">Granatika</div>
            <div class="brand-version">v1.0</div>
        </div>
        <div class="position-sticky pt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="inventario.php">
                        <i class="bi bi-box"></i> Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="ventas.php">
                        <i class="bi bi-cart"></i> Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="compras.php">
                        <i class="bi bi-truck"></i> Compras
                    </a>
                </li>
            </ul>
            <div class="cerrar-sesion">
                <a href="logout.php" class="nav-link text-danger fw-bold" style="display: flex; align-items: center; gap: 0.7rem;">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
            </div>
        </div>
    </nav>
    <main id="mainContent">
        <div class="container-fluid">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Ventas</h1>
                <button class="btn btn-primary d-flex align-items-center gap-2" id="btnAgregarVenta">
                    <i class="bi bi-plus-circle"></i> Registrar venta
                </button>
            </div>
            <!-- Cards de resumen -->
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Total Ventas</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                require_once 'config/database.php';
                                $query = "SELECT SUM(total) as total FROM ventas WHERE estado = 'completada'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo "$" . number_format($row['total'] ?? 0, 2);
                                ?>
                            </h2>
                            <span class="badge bg-light text-primary mt-2"><i class="bi bi-graph-up-arrow"></i> Completadas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Ventas Hoy</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo "$" . number_format($row['total'] ?? 0, 2);
                                ?>
                            </h2>
                            <span class="badge bg-light text-success mt-2"><i class="bi bi-calendar-day"></i> Hoy</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Ventas Mes</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(total) as total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) AND estado = 'completada'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo "$" . number_format($row['total'] ?? 0, 2);
                                ?>
                            </h2>
                            <span class="badge bg-light text-warning mt-2"><i class="bi bi-calendar3"></i> Este mes</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Pendientes</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM ventas WHERE estado = 'pendiente'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                ?>
                                <span id="ventasPendientes"><?php echo $row['total']; ?></span>
                            </h2>
                            <span class="badge bg-light text-danger mt-2"><i class="bi bi-clock"></i> Por cobrar</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" id="filtrosVentas">
                        <div class="col-md-4 col-12">
                            <label for="filtroCliente" class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="filtroCliente" name="cliente" placeholder="Buscar por cliente">
                        </div>
                        <div class="col-md-3 col-6">
                            <label for="filtroEstado" class="form-label">Estado</label>
                            <select class="form-select" id="filtroEstado" name="estado">
                                <option value="">Todos</option>
                                <option value="completada">Completada</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label for="filtroFecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="filtroFecha" name="fecha">
                        </div>
                        <div class="col-md-2 col-12 d-grid">
                            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Tabla de ventas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT v.id, v.cliente, v.total, v.fecha, v.estado, 
                                                 i.nombre AS producto, d.cantidad 
                                          FROM ventas v
                                          JOIN detalles_venta d ON v.id = d.venta_id
                                          JOIN inventario i ON d.inventario_id = i.id
                                          ORDER BY v.fecha DESC";
                                $result = $conn->query($query);
                                while($row = $result->fetch_assoc()) {
                                    $checked = $row['estado'] == 'completada' ? 'checked' : '';
                                    $estadoLabel = $row['estado'] == 'completada' ? 'Completada' : 'Pendiente';
                                    $estadoClass = $row['estado'] == 'completada' ? 'text-success' : 'text-warning';
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['cantidad']) . " m²</td>";
                                    echo "<td>$" . number_format($row['total'], 2) . "</td>";
                                    echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
                                    echo "<td>";
                                    echo "<div class='form-check form-switch d-flex align-items-center gap-2'>";
                                    echo "<input class='form-check-input estado-switch' type='checkbox' role='switch' data-id='" . $row['id'] . "' $checked>";
                                    echo "<span class='fw-bold $estadoClass estado-label'>" . $estadoLabel . "</span>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-outline-secondary me-1' title='Ver' data-id='" . $row['id'] . "'><i class='bi bi-eye'></i></button>";
                                    echo "<button class='btn btn-sm btn-outline-danger' title='Eliminar' data-id='" . $row['id'] . "'><i class='bi bi-trash'></i></button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Toast de notificación -->
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <div id="toastVentas" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Acción realizada correctamente.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            </div>
            <!-- Modal para agregar venta (estructura mejorada) -->
            <div class="modal fade" id="modalAgregarVenta" tabindex="-1" aria-labelledby="modalAgregarVentaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formAgregarVenta">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarVentaLabel">Registrar venta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="cliente" class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" name="cliente" required>
                                </div>
                                <div class="mb-3">
                                    <label for="producto" class="form-label">Producto</label>
                                    <select class="form-select" id="producto" name="producto" required>
                                        <option value="">Selecciona un producto</option>
                                        <?php
                                        $q = "SELECT id, nombre, cantidad, precio_unitario FROM inventario WHERE cantidad > 0 ORDER BY nombre";
                                        $r = $conn->query($q);
                                        while($p = $r->fetch_assoc()) {
                                            echo "<option value='{$p['id']}' data-stock='{$p['cantidad']}' data-precio='{$p['precio_unitario']}'>" . htmlspecialchars($p['nombre']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <div id="stockDisponible" class="form-text text-success"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad (m²)</label>
                                    <input type="number" class="form-control" id="cantidadVenta" name="cantidad" min="0.01" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="number" class="form-control" id="total" name="total" min="0" step="0.01" required readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado" required>
                                        <option value="completada">Completada</option>
                                        <option value="pendiente">Pendiente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal de confirmación para eliminar venta -->
            <div class="modal fade" id="modalConfirmarEliminarVenta" tabindex="-1" aria-labelledby="modalConfirmarEliminarVentaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalConfirmarEliminarVentaLabel">Eliminar venta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar esta venta?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="btnConfirmarEliminarVenta">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal para ver detalles de la venta -->
            <div class="modal fade" id="modalVerVenta" tabindex="-1" aria-labelledby="modalVerVentaLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalVerVentaLabel">Detalles de la venta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body" id="detalleVentaBody">
                            <!-- Aquí se mostrarán los detalles -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Abrir modal al hacer clic en el botón
        const btnAgregar = document.getElementById('btnAgregarVenta');
        const modalAgregarVenta = new bootstrap.Modal(document.getElementById('modalAgregarVenta'));
        
        if (btnAgregar) {
            btnAgregar.addEventListener('click', function() {
                const hoy = new Date();
                const yyyy = hoy.getFullYear();
                const mm = String(hoy.getMonth() + 1).padStart(2, '0');
                const dd = String(hoy.getDate()).padStart(2, '0');
                const fechaActual = `${yyyy}-${mm}-${dd}`;
                document.getElementById('formAgregarVenta').reset();
                document.getElementById('fecha').value = fechaActual;
                document.getElementById('stockDisponible').textContent = '';
                document.getElementById('total').value = '';
                modalAgregarVenta.show();
            });
        }

        // Mostrar stock y precio al seleccionar producto
        const selectProducto = document.getElementById('producto');
        const inputCantidad = document.getElementById('cantidadVenta');
        const inputTotal = document.getElementById('total');
        const stockDisponible = document.getElementById('stockDisponible');
        let precioUnitario = 0;
        let stock = 0;

        selectProducto.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            stock = parseFloat(option.getAttribute('data-stock')) || 0;
            precioUnitario = parseFloat(option.getAttribute('data-precio')) || 0;
            if (stock > 0) {
                stockDisponible.textContent = `Stock disponible: ${stock} m²`;
                stockDisponible.classList.remove('text-danger');
                stockDisponible.classList.add('text-success');
            } else {
                stockDisponible.textContent = 'Sin stock disponible';
                stockDisponible.classList.remove('text-success');
                stockDisponible.classList.add('text-danger');
            }
            inputCantidad.value = '';
            inputTotal.value = '';
        });

        // Calcular total y validar cantidad
        inputCantidad.addEventListener('input', function() {
            const cantidad = parseFloat(this.value) || 0;
            if (cantidad > stock) {
                this.value = stock;
                inputTotal.value = (stock * precioUnitario).toFixed(2);
            } else {
                inputTotal.value = (cantidad * precioUnitario).toFixed(2);
            }
        });

        // Manejar envío del formulario
        document.getElementById('formAgregarVenta').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (parseFloat(inputCantidad.value) > stock) {
                inputCantidad.value = stock;
                inputTotal.value = (stock * precioUnitario).toFixed(2);
                stockDisponible.textContent = `No puedes vender más de ${stock} m²`;
                stockDisponible.classList.remove('text-success');
                stockDisponible.classList.add('text-danger');
                return false;
            }

            const formData = new FormData(this);
            fetch('api/guardar_venta.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const toastEl = document.getElementById('toastVentas');
                const toast = new bootstrap.Toast(toastEl);
                
                if (data.success) {
                    toastEl.classList.remove('text-bg-danger');
                    toastEl.classList.add('text-bg-primary');
                    toastEl.querySelector('.toast-body').textContent = 'Venta registrada correctamente.';
                    modalAgregarVenta.hide();
                    setTimeout(() => { location.reload(); }, 1200);
                } else {
                    toastEl.classList.remove('text-bg-primary');
                    toastEl.classList.add('text-bg-danger');
                    toastEl.querySelector('.toast-body').textContent = 'Error al registrar la venta: ' + data.message;
                }
                toast.show();
            })
            .catch(error => {
                console.error('Error:', error);
                const toastEl = document.getElementById('toastVentas');
                const toast = new bootstrap.Toast(toastEl);
                toastEl.classList.remove('text-bg-primary');
                toastEl.classList.add('text-bg-danger');
                toastEl.querySelector('.toast-body').textContent = 'Error al registrar la venta';
                toast.show();
            });
        });

        let ventaAEliminar = null;
        const modalEliminarVenta = new bootstrap.Modal(document.getElementById('modalConfirmarEliminarVenta'));
        document.querySelectorAll('.btn-outline-danger').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                ventaAEliminar = this.getAttribute('data-id');
                modalEliminarVenta.show();
            });
        });
        document.getElementById('btnConfirmarEliminarVenta').addEventListener('click', function() {
            if (!ventaAEliminar) return;
            fetch('api/eliminar_venta.php?id=' + ventaAEliminar, {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(data => {
                const toastEl = document.getElementById('toastVentas');
                const toast = new bootstrap.Toast(toastEl);
                if (data.success) {
                    toastEl.classList.remove('text-bg-danger');
                    toastEl.classList.add('text-bg-primary');
                    toastEl.querySelector('.toast-body').textContent = 'Venta eliminada correctamente.';
                    toast.show();
                    modalEliminarVenta.hide();
                    setTimeout(() => { location.reload(); }, 1200);
                } else {
                    toastEl.classList.remove('text-bg-primary');
                    toastEl.classList.add('text-bg-danger');
                    toastEl.querySelector('.toast-body').textContent = 'Error al eliminar la venta: ' + (data.message || '');
                    toast.show();
                }
            })
            .catch(() => {
                const toastEl = document.getElementById('toastVentas');
                const toast = new bootstrap.Toast(toastEl);
                toastEl.classList.remove('text-bg-primary');
                toastEl.classList.add('text-bg-danger');
                toastEl.querySelector('.toast-body').textContent = 'Error de red al eliminar la venta';
                toast.show();
            });
        });

        // Ver detalles de venta
        document.querySelectorAll('.btn-outline-secondary').forEach(btn => {
            btn.addEventListener('click', function() {
                const ventaId = this.closest('tr').querySelector('.btn-outline-danger').getAttribute('data-id');
                fetch('api/obtener_venta.php?id=' + ventaId)
                    .then(res => res.json())
                    .then(data => {
                        let html = `<strong>Cliente:</strong> ${data.cliente}<br>`;
                        html += `<strong>Fecha:</strong> ${data.fecha}<br>`;
                        html += `<strong>Estado:</strong> ${data.estado}<br>`;
                        html += `<strong>Total:</strong> $${parseFloat(data.total).toFixed(2)}<br>`;
                        if (data.detalles && data.detalles.length > 0) {
                            html += `<hr><strong>Productos vendidos:</strong><ul>`;
                            data.detalles.forEach(det => {
                                html += `<li>${det.producto_nombre} - ${det.cantidad} m² x $${parseFloat(det.precio_unitario).toFixed(2)} = $${parseFloat(det.subtotal).toFixed(2)}</li>`;
                            });
                            html += `</ul>`;
                        }
                        document.getElementById('detalleVentaBody').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('modalVerVenta')).show();
                    })
                    .catch(() => {
                        document.getElementById('detalleVentaBody').innerHTML = '<span class="text-danger">No se pudieron cargar los detalles de la venta.</span>';
                        new bootstrap.Modal(document.getElementById('modalVerVenta')).show();
                    });
            });
        });

        // Filtros de ventas
        document.getElementById('filtrosVentas').addEventListener('submit', function(e) {
            e.preventDefault();
            const cliente = document.getElementById('filtroCliente').value;
            const estado = document.getElementById('filtroEstado').value;
            const fecha = document.getElementById('filtroFecha').value;
            fetch('api/filtrar_ventas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cliente, estado, fecha })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron resultados.</td></tr>';
                    return;
                }
                data.forEach(row => {
                    const badge = row.estado === 'completada' ? 'success' : 'warning';
                    tbody.innerHTML += `<tr>
                        <td>${row.cliente}</td>
                        <td>${row.producto}</td>
                        <td>${parseFloat(row.cantidad).toFixed(2)} m²</td>
                        <td>$${parseFloat(row.total).toFixed(2)}</td>
                        <td>${row.fecha_formateada}</td>
                        <td><span class='badge bg-${badge}'>${row.estado.charAt(0).toUpperCase() + row.estado.slice(1)}</span></td>
                        <td>
                            <button class='btn btn-sm btn-outline-secondary me-1' title='Ver' data-id='${row.id}'><i class='bi bi-eye'></i></button>
                            <button class='btn btn-sm btn-outline-danger' title='Eliminar' data-id='${row.id}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>`;
                });
            });
        });

        function filtrarVentasAuto() {
            const cliente = document.getElementById('filtroCliente').value;
            const estado = document.getElementById('filtroEstado').value;
            const fecha = document.getElementById('filtroFecha').value;
            fetch('api/filtrar_ventas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cliente, estado, fecha })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron resultados.</td></tr>';
                    return;
                }
                data.forEach(row => {
                    const badge = row.estado === 'completada' ? 'success' : 'warning';
                    tbody.innerHTML += `<tr>
                        <td>${row.cliente}</td>
                        <td>${row.producto}</td>
                        <td>${parseFloat(row.cantidad).toFixed(2)} m²</td>
                        <td>$${parseFloat(row.total).toFixed(2)}</td>
                        <td>${row.fecha_formateada}</td>
                        <td><span class='badge bg-${badge}'>${row.estado.charAt(0).toUpperCase() + row.estado.slice(1)}</span></td>
                        <td>
                            <button class='btn btn-sm btn-outline-secondary me-1' title='Ver' data-id='${row.id}'><i class='bi bi-eye'></i></button>
                            <button class='btn btn-sm btn-outline-danger' title='Eliminar' data-id='${row.id}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>`;
                });
            });
        }

        // Eventos automáticos para filtrar
        const filtroCliente = document.getElementById('filtroCliente');
        const filtroEstado = document.getElementById('filtroEstado');
        const filtroFecha = document.getElementById('filtroFecha');
        filtroCliente.addEventListener('input', filtrarVentasAuto);
        filtroEstado.addEventListener('change', filtrarVentasAuto);
        filtroFecha.addEventListener('input', filtrarVentasAuto);

        // Agregar el JS para manejar el cambio de estado con el switch:
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('estado-switch')) {
                const id = e.target.dataset.id;
                const estado = e.target.checked ? 'completada' : 'pendiente';
                const label = e.target.closest('.form-check').querySelector('.estado-label');
                fetch('api/cambiar_estado_venta.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, estado })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = estado === 'completada' ? 'Completada' : 'Pendiente';
                        label.className = 'fw-bold ' + (estado === 'completada' ? 'text-success' : 'text-warning') + ' estado-label';
                        mostrarToast('Estado actualizado correctamente.', 'success');
                    } else {
                        mostrarToast(data.message || 'Error al actualizar el estado.', 'danger');
                    }
                })
                .catch(() => {
                    mostrarToast('Error de conexión al actualizar el estado.', 'danger');
                });
            }
        });
    });
    </script>
</body>
</html> 