<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Granatika - Dashboard</title>
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
                    <a class="nav-link active" href="index.php">
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
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnHoy">Hoy</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSemana">Esta Semana</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnMes">Este Mes</button>
                    </div>
                </div>
            </div>

            <!-- Cards de Resumen -->
            <div class="row mb-4 g-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
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
                                </div>
                                <i class="bi bi-graph-up-arrow fs-1"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-light text-primary">
                                    <i class="bi bi-arrow-up"></i> 12% vs mes anterior
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Productos en Stock</h6>
                                    <h2 class="card-text mt-2">
                                        <?php
                                        $query = "SELECT COUNT(*) as total FROM inventario WHERE estado = 'disponible'";
                                        $result = $conn->query($query);
                                        $row = $result->fetch_assoc();
                                        echo $row['total'];
                                        ?>
                                    </h2>
                                </div>
                                <i class="bi bi-box-seam fs-1"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-light text-success">
                                    <i class="bi bi-check-circle"></i> Stock Actualizado
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Compras Pendientes</h6>
                                    <h2 class="card-text mt-2">
                                        <?php
                                        $query = "SELECT COUNT(*) as total FROM compras WHERE estado = 'pendiente'";
                                        $result = $conn->query($query);
                                        $row = $result->fetch_assoc();
                                        echo $row['total'];
                                        ?>
                                    </h2>
                                </div>
                                <i class="bi bi-truck fs-1"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-light text-warning">
                                    <i class="bi bi-clock"></i> Requieren Atención
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Productos Agotados</h6>
                                    <h2 class="card-text mt-2">
                                        <?php
                                        $query = "SELECT COUNT(*) as total FROM inventario WHERE estado = 'agotado'";
                                        $result = $conn->query($query);
                                        $row = $result->fetch_assoc();
                                        echo $row['total'];
                                        ?>
                                    </h2>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-light text-danger">
                                    <i class="bi bi-exclamation-circle"></i> Necesitan Reposición
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Tablas -->
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Ventas por Tipo de Material</h5>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-chart-period="week">Semana</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-chart-period="month">Mes</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-chart-period="year">Año</button>
                                </div>
                            </div>
                            <canvas id="ventasChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Distribución de Ventas</h5>
                            <canvas id="ventasPieChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablas de Datos -->
            <div class="row mt-4 g-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Últimas Ventas</h5>
                                <a href="ventas.php" class="btn btn-sm btn-primary">Ver Todas</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT cliente, total, fecha, estado FROM ventas 
                                                 ORDER BY fecha DESC LIMIT 5";
                                        $result = $conn->query($query);
                                        while($row = $result->fetch_assoc()) {
                                            $estadoClass = $row['estado'] == 'completada' ? 'success' : 'warning';
                                            echo "<tr>";
                                            echo "<td>" . $row['cliente'] . "</td>";
                                            echo "<td>$" . number_format($row['total'], 2) . "</td>";
                                            echo "<td>" . date('d/m/Y', strtotime($row['fecha'])) . "</td>";
                                            echo "<td><span class='badge bg-$estadoClass'>" . ucfirst($row['estado']) . "</span></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Productos con Bajo Stock</h5>
                                <a href="inventario.php" class="btn btn-sm btn-primary">Ver Inventario</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Stock Actual</th>
                                            <th>Stock Mínimo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT nombre, stock, stock_minimo, estado 
                                                 FROM inventario 
                                                 WHERE stock <= stock_minimo 
                                                 ORDER BY stock ASC LIMIT 5";
                                        $result = $conn->query($query);
                                        while($row = $result->fetch_assoc()) {
                                            $estadoClass = $row['stock'] == 0 ? 'danger' : 'warning';
                                            echo "<tr>";
                                            echo "<td>" . $row['nombre'] . "</td>";
                                            echo "<td>" . $row['stock'] . "</td>";
                                            echo "<td>" . $row['stock_minimo'] . "</td>";
                                            echo "<td><span class='badge bg-$estadoClass'>" . 
                                                 ($row['stock'] == 0 ? 'Agotado' : 'Bajo Stock') . 
                                                 "</span></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Botón flotante del chatbot -->
    <button id="chatbot-boton"
      style="position:fixed;bottom:24px;right:24px;z-index:99999;background:#0088cc;color:#fff;border:none;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.2);font-size:2rem;cursor:pointer;">
      <i class="bi bi-robot"></i>
    </button>

    <button style="position:fixed;bottom:10px;right:10px;z-index:99999;background:red;color:white;">PRUEBA</button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
    // Gráfico de Ventas por Tipo de Material
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    const ventasChart = new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            datasets: [{
                label: 'Ventas Totales',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Gráfico de Distribución de Ventas
    const ventasPieCtx = document.getElementById('ventasPieChart').getContext('2d');
    const ventasPieChart = new Chart(ventasPieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Granito', 'Mármol', 'Cuarzo', 'Otros'],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Filtros de fecha
    document.querySelectorAll('[data-chart-period]').forEach(button => {
        button.addEventListener('click', function() {
            const period = this.dataset.chartPeriod;
            // Aquí iría la lógica para actualizar los datos según el período
            console.log('Cambiando período a:', period);
        });
    });
    </script>
</body>
</html> 