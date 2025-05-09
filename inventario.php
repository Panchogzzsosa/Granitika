<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granatika - Inventario</title>
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
                    <a class="nav-link active" href="inventario.php">
                        <i class="bi bi-box"></i> Inventario
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ventas.php">
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
                <h1 class="h2">Inventario</h1>
                <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                    <i class="bi bi-plus-circle"></i> Agregar producto
                </button>
            </div>
            <!-- Cards de resumen -->
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Total Productos</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                require_once 'config/database.php';
                                $query = "SELECT COUNT(*) as total FROM inventario";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['total'] ?? 0);
                                ?>
                            </h2>
                            <span class="badge bg-light text-primary mt-2"><i class="bi bi-box"></i> En inventario</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Stock Total</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT SUM(cantidad) as total FROM inventario";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['total'] ?? 0, 2);
                                ?>
                            </h2>
                            <span class="badge bg-light text-success mt-2"><i class="bi bi-boxes"></i> Unidades</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Bajo Stock</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM inventario WHERE cantidad < 10 AND cantidad > 0";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['total'] ?? 0);
                                ?>
                            </h2>
                            <span class="badge bg-light text-warning mt-2"><i class="bi bi-exclamation-triangle"></i> Alerta</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-0">Sin Stock</h6>
                            <h2 class="card-text mt-2">
                                <?php
                                $query = "SELECT COUNT(*) as total FROM inventario WHERE cantidad = 0";
                                $result = $conn->query($query);
                                $row = $result->fetch_assoc();
                                echo number_format($row['total'] ?? 0);
                                ?>
                            </h2>
                            <span class="badge bg-light text-danger mt-2"><i class="bi bi-x-circle"></i> Agotados</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3 align-items-end" id="filtrosInventario">
                        <div class="col-md-4 col-12">
                            <label for="filtroNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="filtroNombre" name="nombre" placeholder="Buscar por nombre">
                        </div>
                        <div class="col-md-3 col-6">
                            <label for="filtroTipo" class="form-label">Tipo</label>
                            <select class="form-select" id="filtroTipo" name="tipo">
                                <option value="">Todos</option>
                                <option value="marmol">Mármol</option>
                                <option value="granito">Granito</option>
                                <option value="cuarzo">Cuarzo</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label for="filtroEstado" class="form-label">Estado</label>
                            <select class="form-select" id="filtroEstado" name="estado">
                                <option value="">Todos</option>
                                <option value="disponible">Disponible</option>
                                <option value="agotado">Agotado</option>
                                <option value="bajo_stock">Bajo stock</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12 d-grid">
                            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Tabla de inventario -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Precio unitario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT id, nombre, tipo, cantidad, unidad_medida, precio_unitario, estado, imagen FROM inventario ORDER BY nombre";
                                $result = $conn->query($query);
                                while($row = $result->fetch_assoc()) {
                                    $cantidad = floatval($row['cantidad']);
                                    if ($cantidad == 0) {
                                        $estado = 'Sin stock';
                                        $stockClass = 'danger';
                                    } elseif ($cantidad < 10) {
                                        $estado = 'Bajo stock';
                                        $stockClass = 'warning';
                                    } else {
                                        $estado = 'Disponible';
                                        $stockClass = 'success';
                                    }
                                    echo "<tr>";
                                    echo "<td><img src='" . htmlspecialchars($row['imagen'] ?? 'img/no-image.png') . "' class='rounded' alt='Producto' width='40'></td>";
                                    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tipo']) . "</td>";
                                    echo "<td><span class='badge bg-$stockClass'>" . number_format($cantidad, 2) . "</span></td>";
                                    echo "<td>m2</td>";
                                    echo "<td>$" . number_format($row['precio_unitario'], 2) . "</td>";
                                    echo "<td><span class='badge bg-$stockClass'>" . $estado . "</span></td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-sm btn-outline-secondary me-1 btn-editar-producto' data-id='" . $row['id'] . "' title='Editar'><i class='bi bi-pencil'></i></button>";
                                    echo "<button class='btn btn-sm btn-outline-danger btn-eliminar-producto' data-id='" . $row['id'] . "' title='Eliminar'><i class='bi bi-trash'></i></button>";
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
                <div id="toastInventario" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Acción realizada correctamente.
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
                    </div>
                </div>
            </div>
            <!-- Modal para agregar producto -->
            <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formAgregarProducto" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar producto</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="">Selecciona un tipo</option>
                                        <option value="marmol">Mármol</option>
                                        <option value="granito">Granito</option>
                                        <option value="cuarzo">Cuarzo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" step="0.01" class="form-control" id="cantidad" name="cantidad" min="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="precio_unitario" class="form-label">Precio unitario</label>
                                    <input type="number" step="0.01" class="form-control" id="precio_unitario" name="precio_unitario" min="0" required>
                                </div>
                                <div class="mb-3">
                                    <label for="imagen" class="form-label">Imagen</label>
                                    <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
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
            <!-- Modal de confirmación para eliminar producto -->
            <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalConfirmarEliminarLabel">Eliminar producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar este producto?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="assets/js/main.js"></script> -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let productoAEliminar = null;
        let productoAEditar = null;

        // Eliminar producto
        document.querySelectorAll('.btn-eliminar-producto').forEach(btn => {
            btn.addEventListener('click', function() {
                productoAEliminar = this.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
                modal.show();
            });
        });
        document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
            if (!productoAEliminar) return;
            fetch('api/eliminar_producto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: productoAEliminar })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    const toastEl = document.getElementById('toastInventario');
                    const toast = new bootstrap.Toast(toastEl);
                    toastEl.classList.remove('text-bg-primary');
                    toastEl.classList.add('text-bg-danger');
                    toastEl.querySelector('.toast-body').textContent = data.error || 'Error al eliminar producto';
                    toast.show();
                }
            })
            .catch(() => {
                const toastEl = document.getElementById('toastInventario');
                const toast = new bootstrap.Toast(toastEl);
                toastEl.classList.remove('text-bg-primary');
                toastEl.classList.add('text-bg-danger');
                toastEl.querySelector('.toast-body').textContent = 'Error de red al eliminar producto';
                toast.show();
            });
        });

        // Editar producto
        document.querySelectorAll('.btn-editar-producto').forEach(btn => {
            btn.addEventListener('click', function() {
                productoAEditar = this.dataset.id;
                fetch('obtener_producto.php?id=' + productoAEditar)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Rellenar el modal con los datos
                            document.getElementById('nombre').value = data.producto.nombre;
                            document.getElementById('tipo').value = data.producto.tipo;
                            document.getElementById('cantidad').value = data.producto.cantidad;
                            document.getElementById('precio_unitario').value = data.producto.precio_unitario;
                            // No se rellena imagen por seguridad
                            const modal = new bootstrap.Modal(document.getElementById('modalAgregarProducto'));
                            modal.show();
                            document.getElementById('formAgregarProducto').setAttribute('data-editar', productoAEditar);
                        } else {
                            alert('No se pudo cargar el producto');
                        }
                    });
            });
        });

        // Guardar producto (nuevo o editado)
        document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const editarId = this.getAttribute('data-editar');
            let url = editarId ? 'editar_producto.php' : 'agregar_producto.php';
            if (editarId) formData.append('id', editarId);
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const toastEl = document.getElementById('toastInventario');
                    const toast = new bootstrap.Toast(toastEl);
                    toastEl.classList.remove('text-bg-danger');
                    toastEl.classList.add('text-bg-primary');
                    toastEl.querySelector('.toast-body').textContent = editarId ? 'Producto editado correctamente.' : 'Producto registrado correctamente.';
                    toast.show();
                    this.reset();
                    setTimeout(() => { location.reload(); }, 1200);
                } else {
                    const toastEl = document.getElementById('toastInventario');
                    const toast = new bootstrap.Toast(toastEl);
                    toastEl.classList.remove('text-bg-primary');
                    toastEl.classList.add('text-bg-danger');
                    if (data.error && data.error.includes('Ya existe un producto')) {
                        toastEl.querySelector('.toast-body').textContent = 'No se puede guardar: ya existe un producto con ese nombre.';
                    } else {
                        toastEl.querySelector('.toast-body').textContent = 'Error al guardar producto';
                    }
                    toast.show();
                }
            });
            this.removeAttribute('data-editar');
        });

        // Filtrado de inventario
        function filtrarInventario() {
            const nombre = document.getElementById('filtroNombre').value;
            const tipo = document.getElementById('filtroTipo').value;
            const estado = document.getElementById('filtroEstado').value;
            fetch('api/filtrar_inventario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre, tipo, estado })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No se encontraron resultados.</td></tr>';
                    return;
                }
                data.forEach(row => {
                    let cantidad = parseFloat(row.cantidad);
                    let stockClass = 'success';
                    let estadoTxt = 'Disponible';
                    if (cantidad == 0) {
                        estadoTxt = 'Sin stock';
                        stockClass = 'danger';
                    } else if (cantidad < 10) {
                        estadoTxt = 'Bajo stock';
                        stockClass = 'warning';
                    }
                    tbody.innerHTML += `<tr>
                        <td><img src='${row.imagen || 'img/no-image.png'}' class='rounded' alt='Producto' width='40'></td>
                        <td>${row.nombre}</td>
                        <td>${row.tipo}</td>
                        <td><span class='badge bg-${stockClass}'>${cantidad.toFixed(2)}</span></td>
                        <td>m2</td>
                        <td>$${parseFloat(row.precio_unitario).toFixed(2)}</td>
                        <td><span class='badge bg-${stockClass}'>${estadoTxt}</span></td>
                        <td>
                            <button class='btn btn-sm btn-outline-secondary me-1 btn-editar-producto' data-id='${row.id}' title='Editar'><i class='bi bi-pencil'></i></button>
                            <button class='btn btn-sm btn-outline-danger btn-eliminar-producto' data-id='${row.id}' title='Eliminar'><i class='bi bi-trash'></i></button>
                        </td>
                    </tr>`;
                });
            });
        }
        document.getElementById('filtrosInventario').addEventListener('submit', function(e) {
            e.preventDefault();
            filtrarInventario();
        });
        document.getElementById('filtroNombre').addEventListener('input', filtrarInventario);
        document.getElementById('filtroTipo').addEventListener('change', filtrarInventario);
        document.getElementById('filtroEstado').addEventListener('change', filtrarInventario);
    });
    </script>
</body>
</html> 