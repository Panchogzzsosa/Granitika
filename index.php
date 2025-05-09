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
                                            <th>Cantidad Actual</th>
                                            <th>Stock Mínimo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT nombre, cantidad, stock_minimo, estado 
                                                 FROM inventario 
                                                 WHERE cantidad <= stock_minimo 
                                                 ORDER BY cantidad ASC LIMIT 5";
                                        $result = $conn->query($query);
                                        while($row = $result->fetch_assoc()) {
                                            $estadoClass = $row['cantidad'] == 0 ? 'danger' : 'warning';
                                            echo "<tr>";
                                            echo "<td>" . $row['nombre'] . "</td>";
                                            echo "<td>" . $row['cantidad'] . "</td>";
                                            echo "<td>" . $row['stock_minimo'] . "</td>";
                                            echo "<td><span class='badge bg-$estadoClass'>" . 
                                                 ($row['cantidad'] == 0 ? 'Agotado' : 'Bajo Stock') . 
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

    <!-- Chatbot -->
    <div id="chatbot-container" style="position:fixed;bottom:20px;right:20px;z-index:999999;">
        <!-- Botón del chatbot -->
        <button id="chatbot-toggle" style="
            width:70px;
            height:70px;
            border-radius:50%;
            background:#0088cc;
            border:none;
            color:white;
            font-size:28px;
            cursor:pointer;
            box-shadow:0 4px 15px rgba(0,0,0,0.3);
            display:flex;
            align-items:center;
            justify-content:center;
            transition:all 0.3s ease;
            position:relative;
        "
        title="Haz clic para chatear con el asistente">
            <i class="bi bi-robot"></i>
            <span style="
                position:absolute;
                bottom:-25px;
                left:50%;
                transform:translateX(-50%);
                background:rgba(0,0,0,0.8);
                color:white;
                padding:4px 8px;
                border-radius:4px;
                font-size:12px;
                white-space:nowrap;
                opacity:0;
                transition:opacity 0.3s;
                pointer-events:none;
            ">Haz clic para chatear</span>
        </button>

        <!-- Ventana del chat -->
        <div id="chatbot-window" style="
            position:absolute;
            bottom:80px;
            right:0;
            width:350px;
            height:450px;
            background:white;
            border-radius:15px;
            box-shadow:0 5px 25px rgba(0,0,0,0.2);
            display:none;
            flex-direction:column;
        ">
            <!-- Encabezado -->
            <div style="
                padding:15px 20px;
                background:#0088cc;
                color:white;
                border-radius:15px 15px 0 0;
                display:flex;
                justify-content:space-between;
                align-items:center;
            ">
                <span style="font-weight:bold;font-size:1.1rem;">Granatika Bot</span>
                <button id="chatbot-close" style="
                    background:none;
                    border:none;
                    color:white;
                    cursor:pointer;
                    font-size:24px;
                    padding:0;
                    width:30px;
                    height:30px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                ">×</button>
            </div>

            <!-- Mensajes -->
            <div id="chatbot-messages" style="
                flex:1;
                padding:20px;
                overflow-y:auto;
                background:#f5f7fa;
            ">
                <div style="
                    background:#e5eaf1;
                    padding:12px 16px;
                    border-radius:15px;
                    margin-bottom:15px;
                    max-width:85%;
                    font-size:0.95rem;
                    line-height:1.4;
                ">
                    ¡Hola! 👋 Soy tu asistente Granatika. Puedes preguntarme cosas como:<br>
                    - ¿Cuánto stock hay de [producto]?<br>
                    - ¿Qué productos están agotados?<br>
                    - ¿Cuántos productos hay en inventario?<br>
                    - ¿Quién fue el último cliente?<br>
                    - ¿Cuáles son mis ventas?<br>
                    - ¿Cuánto le he vendido a [cliente]?<br>
                    - ¿A quién le vendí?<br>
                    ¡Hazme una pregunta!
                </div>
            </div>

            <!-- Formulario de entrada -->
            <form id="chatbot-form" style="
                padding:15px 20px;
                background:white;
                border-top:1px solid #eee;
                display:flex;
                gap:10px;
            ">
                <input type="text" id="chatbot-input" placeholder="Escribe tu pregunta..." style="
                    flex:1;
                    padding:10px 15px;
                    border:1px solid #ddd;
                    border-radius:8px;
                    font-size:0.95rem;
                " required>
                <button type="submit" style="
                    background:#0088cc;
                    color:white;
                    border:none;
                    padding:10px 15px;
                    border-radius:8px;
                    cursor:pointer;
                    font-size:1.1rem;
                ">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        </div>
    </div>

    <style>
    /* Forzar visibilidad de todos los elementos fijos */
    *[style*="position:fixed"],
    *[style*="position: fixed"] {
      z-index: 2147483647 !important;
      display: block !important;
      opacity: 1 !important;
      visibility: visible !important;
    }
    </style>

    <script>
    // Chatbot
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotWindow = document.getElementById('chatbot-window');
        const chatbotClose = document.getElementById('chatbot-close');
        const chatbotForm = document.getElementById('chatbot-form');
        const chatbotInput = document.getElementById('chatbot-input');
        const chatbotMessages = document.getElementById('chatbot-messages');

        // Mostrar tooltip al hover
        chatbotToggle.addEventListener('mouseenter', function() {
            this.querySelector('span').style.opacity = '1';
        });
        chatbotToggle.addEventListener('mouseleave', function() {
            this.querySelector('span').style.opacity = '0';
        });

        // Mostrar/ocultar ventana
        chatbotToggle.addEventListener('click', () => {
            chatbotWindow.style.display = 'flex';
            chatbotInput.focus();
        });

        chatbotClose.addEventListener('click', () => {
            chatbotWindow.style.display = 'none';
        });

        // Enviar mensaje
        chatbotForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const message = chatbotInput.value.trim();
            if (!message) return;

            // Agregar mensaje del usuario
            addMessage(message, 'user');
            chatbotInput.value = '';

            // Enviar al backend
            fetch('api/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pregunta: message })
            })
            .then(res => res.json())
            .then(data => {
                addMessage(data.respuesta, 'bot');
            })
            .catch(() => {
                addMessage('Lo siento, hubo un error al procesar tu consulta.', 'bot');
            });
        });

        // Función para agregar mensajes
        function addMessage(text, type) {
            const div = document.createElement('div');
            div.style.cssText = `
                padding: 12px 16px;
                border-radius: 15px;
                margin-bottom: 15px;
                max-width: 85%;
                word-wrap: break-word;
                font-size: 0.95rem;
                line-height: 1.4;
                ${type === 'user' ? `
                    background: #0088cc;
                    color: white;
                    margin-left: auto;
                    border-radius: 15px 15px 0 15px;
                ` : `
                    background: #e5eaf1;
                    color: #333;
                    margin-right: auto;
                    border-radius: 15px 15px 15px 0;
                `}
            `;
            div.textContent = text;
            chatbotMessages.appendChild(div);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        // Cerrar al hacer clic fuera
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#chatbot-container')) {
                chatbotWindow.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html> 