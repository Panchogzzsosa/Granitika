<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granatika - Compras</title>
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
                    <a class="nav-link" href="ventas.php">
                        <i class="bi bi-cart"></i> Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="compras.php">
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
                <h1 class="h2">Compras</h1>
                <button class="btn btn-primary d-flex align-items-center gap-2" id="btnAgregarCompra">
                    <i class="bi bi-plus-circle"></i> Registrar compra
                </button>
            </div>
            <!-- Cards de resumen -->
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Total Gastado</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(total) as total FROM compras WHERE estado = 'completada'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo "$" . number_format($row['total'] ?? 0, 2);
                                ?>
                            </h2>
                            <span class="badge bg-light text-primary mt-2"><i class="bi bi-cash-stack"></i> Completadas</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Compras Hoy</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(total) as total FROM compras WHERE DATE(fecha_compra) = CURDATE() AND estado = 'completada'";
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
                            <h6 class="card-title mb-0">Compras Mes</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(total) as total FROM compras WHERE MONTH(fecha_compra) = MONTH(CURDATE()) AND YEAR(fecha_compra) = YEAR(CURDATE()) AND estado = 'completada'";
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
                                $query = "SELECT COUNT(*) as total FROM compras WHERE estado = 'pendiente'";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                ?>
                            </h2>
                            <span class="badge bg-light text-danger mt-2"><i class="bi bi-clock"></i> Por pagar</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" id="filtrosCompras">
                        <div class="col-md-4 col-12">
                            <label for="filtroProveedor" class="form-label">Proveedor</label>
                            <input type="text" class="form-control" id="filtroProveedor" name="proveedor" placeholder="Buscar por proveedor">
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
            <!-- Tabla de compras -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
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
                                $query = "SELECT id, proveedor, producto, cantidad, total, fecha_compra, estado FROM compras ORDER BY fecha_compra DESC";
                                $result = $conn->query($query);
                                if ($result->num_rows === 0) {
                                    echo '<tr><td colspan="7" class="text-center text-muted">No se encontraron resultados.</td></tr>';
                                } else {
                                    while($row = $result->fetch_assoc()) {
                                        $checked = $row['estado'] == 'completada' ? 'checked' : '';
                                        $estadoLabel = $row['estado'] == 'completada' ? 'Completada' : 'Pendiente';
                                        $estadoClass = $row['estado'] == 'completada' ? 'text-success' : 'text-warning';
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['proveedor']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['producto']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['cantidad']) . "</td>";
                                        echo "<td>$" . number_format($row['total'], 2) . "</td>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['fecha_compra'])) . "</td>";
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
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Toast de notificación -->
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
                <div id="toastCompras" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Acción realizada correctamente.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            </div>
            <!-- Modal para agregar compra (estructura) -->
            <div class="modal fade" id="modalAgregarCompra" tabindex="-1" aria-labelledby="modalAgregarCompraLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formAgregarCompra">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarCompraLabel">Registrar compra</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="proveedor" class="form-label">Proveedor</label>
                                    <input type="text" class="form-control" id="proveedor" name="proveedor" required>
                                </div>
                                <div class="mb-3">
                                    <label for="producto" class="form-label">Producto</label>
                                    <select class="form-select" id="producto" name="producto" required>
                                        <option value="">Seleccione un producto</option>
                                    </select>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="cantidad" class="form-label">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="number" class="form-control" id="total" name="total" min="0" step="0.01" required>
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
            <!-- Modal de confirmación de eliminación -->
            <div class="modal fade" id="modalConfirmarEliminarCompra" tabindex="-1" aria-labelledby="modalConfirmarEliminarCompraLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalConfirmarEliminarCompraLabel">Confirmar eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar esta compra? Esta acción no se puede deshacer.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="btnConfirmarEliminarCompra">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal para ver detalles de la compra -->
            <div class="modal fade" id="modalVerCompra" tabindex="-1" aria-labelledby="modalVerCompraLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalVerCompraLabel">Detalles de la compra</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body" id="detalleCompraBody">
                            <!-- Aquí se cargan los detalles -->
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
        // Cargar productos al abrir el modal
        document.getElementById('modalAgregarCompra').addEventListener('show.bs.modal', function () {
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha').value = hoy;
            document.getElementById('cantidad').value = '';
            document.getElementById('total').value = '';
            // Cargar productos del inventario
            fetch('api/obtener_productos.php')
                .then(res => res.json())
                .then(productos => {
                    const select = document.getElementById('producto');
                    select.innerHTML = '<option value="">Seleccione un producto</option>';
                    productos.forEach(producto => {
                        select.innerHTML += `<option value="${producto.id}" data-nombre="${producto.nombre}">${producto.nombre}</option>`;
                    });
                });
        });

        // Mostrar modal al hacer clic en "Registrar compra"
        document.getElementById('btnAgregarCompra').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalAgregarCompra'));
            modal.show();
        });

        // Enviar formulario de compra por AJAX
        document.getElementById('formAgregarCompra').addEventListener('submit', function(e) {
            e.preventDefault();
            const proveedor = document.getElementById('proveedor').value.trim();
            const producto_id = document.getElementById('producto').value;
            const producto_nombre = document.getElementById('producto').options[document.getElementById('producto').selectedIndex].dataset.nombre;
            const cantidad = document.getElementById('cantidad').value;
            const total = document.getElementById('total').value;
            const fecha = document.getElementById('fecha').value;
            const estado = document.getElementById('estado').value;

            fetch('api/registrar_compra.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ proveedor, producto_id, producto_nombre, cantidad, total, fecha, estado })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('modalAgregarCompra')).hide();
                    // Limpiar formulario
                    document.getElementById('formAgregarCompra').reset();
                    // Mostrar toast de éxito
                    mostrarToast('Compra registrada correctamente.', 'success');
                    // Actualizar tabla
                    filtrarComprasAuto();
                } else {
                    mostrarToast(data.message || 'Error al registrar la compra.', 'danger');
                }
            })
            .catch(() => {
                mostrarToast('Error de conexión al registrar la compra.', 'danger');
            });
        });

        // Función para mostrar toast
        function mostrarToast(mensaje, tipo = 'primary') {
            const toast = document.getElementById('toastCompras');
            toast.className = `toast align-items-center text-bg-${tipo} border-0`;
            toast.querySelector('.toast-body').textContent = mensaje;
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        // Filtrado automático y por botón igual que en ventas
        document.getElementById('filtrosCompras').addEventListener('submit', function(e) {
            e.preventDefault();
            filtrarComprasAuto();
        });
        function filtrarComprasAuto() {
            const proveedor = document.getElementById('filtroProveedor').value;
            const estado = document.getElementById('filtroEstado').value;
            const fecha = document.getElementById('filtroFecha').value;
            fetch('api/filtrar_compras.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ proveedor, estado, fecha })
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
                    const checked = row.estado === 'completada' ? 'checked' : '';
                    const estadoLabel = row.estado === 'completada' ? 'Completada' : 'Pendiente';
                    const estadoClass = row.estado === 'completada' ? 'text-success' : 'text-warning';
                    tbody.innerHTML += `<tr>
                        <td>${row.proveedor}</td>
                        <td>${row.producto}</td>
                        <td>${row.cantidad}</td>
                        <td>$${parseFloat(row.total).toFixed(2)}</td>
                        <td>${row.fecha_formateada}</td>
                        <td>
                            <div class='form-check form-switch d-flex align-items-center gap-2'>
                                <input class='form-check-input estado-switch' type='checkbox' role='switch' data-id='${row.id}' ${checked}>
                                <span class='fw-bold ${estadoClass} estado-label'>${estadoLabel}</span>
                            </div>
                        </td>
                        <td>
                            <button class='btn btn-sm btn-outline-secondary me-1' title='Ver' data-id='${row.id}'><i class='bi bi-eye'></i></button>
                            <button class='btn btn-sm btn-outline-danger' title='Eliminar' data-id='${row.id}'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>`;
                });
            });
        }
        // Eventos automáticos para filtrar
        document.getElementById('filtroProveedor').addEventListener('input', filtrarComprasAuto);
        document.getElementById('filtroEstado').addEventListener('change', filtrarComprasAuto);
        document.getElementById('filtroFecha').addEventListener('input', filtrarComprasAuto);

        // Agregar el JS para manejar el cambio de estado con el switch:
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('estado-switch')) {
                const id = e.target.dataset.id;
                const estado = e.target.checked ? 'completada' : 'pendiente';
                const label = e.target.closest('.form-check').querySelector('.estado-label');
                fetch('api/cambiar_estado_compra.php', {
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

        let compraAEliminar = null;
        // Mostrar modal al hacer clic en eliminar
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-outline-danger')) {
                const btn = e.target.closest('.btn-outline-danger');
                compraAEliminar = btn.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminarCompra'));
                modal.show();
            }
        });
        // Confirmar eliminación
        document.getElementById('btnConfirmarEliminarCompra').addEventListener('click', function() {
            if (!compraAEliminar) return;
            fetch('api/eliminar_compra.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: compraAEliminar })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    mostrarToast('Compra eliminada correctamente.', 'success');
                    filtrarComprasAuto();
                } else {
                    mostrarToast(data.message || 'No se pudo eliminar la compra.', 'danger');
                }
            })
            .catch(() => {
                mostrarToast('Error de conexión al eliminar la compra.', 'danger');
            });
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminarCompra')).hide();
            compraAEliminar = null;
        });

        // Ver detalles de la compra
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-outline-secondary')) {
                const btn = e.target.closest('.btn-outline-secondary');
                const compraId = btn.dataset.id;
                fetch('api/obtener_compra.php?id=' + compraId)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const c = data.data;
                            let html = '';
                            if (c.imagen) {
                                html += `<div class='text-center mb-3'><img src='${c.imagen}' alt='Imagen producto' class='img-fluid rounded' style='max-height:180px;'></div>`;
                            }
                            html += `<strong>Proveedor:</strong> ${c.proveedor}<br>`;
                            html += `<strong>Producto:</strong> ${c.producto}<br>`;
                            html += `<strong>Cantidad:</strong> ${c.cantidad}<br>`;
                            html += `<strong>Total:</strong> $${parseFloat(c.total).toFixed(2)}<br>`;
                            // Parsear fecha manualmente para evitar desfase de zona horaria
                            const partes = c.fecha_compra.split('-'); // [YYYY, MM, DD]
                            const fechaFormateada = partes[2] + '/' + partes[1] + '/' + partes[0];
                            html += `<strong>Fecha:</strong> ${fechaFormateada}<br>`;
                            html += `<strong>Estado:</strong> <span class='badge bg-${c.estado === 'completada' ? 'success' : 'warning'}'>${c.estado.charAt(0).toUpperCase() + c.estado.slice(1)}</span><br>`;
                            document.getElementById('detalleCompraBody').innerHTML = html;
                            new bootstrap.Modal(document.getElementById('modalVerCompra')).show();
                        } else {
                            document.getElementById('detalleCompraBody').innerHTML = `<span class='text-danger'>No se pudieron cargar los detalles de la compra.</span>`;
                            new bootstrap.Modal(document.getElementById('modalVerCompra')).show();
                        }
                    })
                    .catch(() => {
                        document.getElementById('detalleCompraBody').innerHTML = `<span class='text-danger'>No se pudieron cargar los detalles de la compra.</span>`;
                        new bootstrap.Modal(document.getElementById('modalVerCompra')).show();
                    });
            }
        });
    });
    </script>
</body>
</html>
