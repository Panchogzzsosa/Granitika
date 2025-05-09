<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$pregunta = strtolower(trim($data['pregunta'] ?? ''));

if (!$pregunta) {
    echo json_encode(['respuesta' => 'Por favor, escribe una pregunta.']);
    exit;
}

// Saludos y ayuda
if (preg_match('/^(hola|buen[oa]s? (d[ií]as|tardes|noches)|hey|qué tal|saludos)/i', $pregunta)) {
    $respuesta = "¡Hola! 👋 Soy tu asistente Granatika. Puedes preguntarme cosas como:\n"
        . "- ¿Cuánto stock hay de [producto]?\n"
        . "- ¿Qué productos están agotados?\n"
        . "- ¿Cuántos productos hay en inventario?\n"
        . "- ¿Quién fue el último cliente?\n"
        . "- ¿Cuáles son mis ventas?\n"
        . "- ¿Cuánto le he vendido a [cliente]?\n"
        . "- ¿A quién le vendí?\n"
        . "¡Hazme una pregunta!";
}

// Respuestas por reglas
$respuesta = 'No entendí tu pregunta. Puedes preguntarme por el stock de un producto, productos agotados, ventas, etc.';

// 6. ¿A quién le vendí? ¿A quién fue la última venta?
if (preg_match('/(a qui[eé]n le vend[ií]|a quien fue la [úu]ltima venta|cliente de la [úu]ltima venta|a quien vend[ií])/i', $pregunta)) {
    $result = $conn->query("SELECT cliente, fecha FROM ventas ORDER BY fecha DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $respuesta = "La última venta fue a {$row['cliente']} el " . date('d/m/Y', strtotime($row['fecha'])) . ".";
    } else {
        $respuesta = "No hay ventas registradas.";
    }
}
// 1. ¿Cuánto stock/cantidad hay de X?
else if (preg_match('/de\s+([\w\sáéíóúñ]+)$/i', $pregunta, $matches)) {
    $producto = trim($matches[1]);
    // Buscar por nombre
    $stmt = $conn->prepare("SELECT cantidad FROM inventario WHERE nombre LIKE CONCAT('%', ?, '%') LIMIT 1");
    $stmt->bind_param('s', $producto);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $respuesta = "Actualmente tienes {$row['cantidad']} unidades de $producto en inventario.";
    } else {
        // Si no lo encuentra por nombre, busca por tipo
        $stmt = $conn->prepare("SELECT SUM(cantidad) as total FROM inventario WHERE tipo LIKE CONCAT('%', ?, '%')");
        $stmt->bind_param('s', $producto);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row && $row['total'] > 0) {
            $respuesta = "Actualmente tienes {$row['total']} unidades del tipo $producto en inventario.";
        } else {
            $respuesta = "No encontré el producto o tipo '$producto' en el inventario.";
        }
        $stmt->close();
    }
}
// 2. ¿Qué productos están agotados?
elseif (preg_match('/(agotad[ao]s?|sin stock|no hay)/i', $pregunta)) {
    $result = $conn->query("SELECT nombre FROM inventario WHERE cantidad = 0");
    $agotados = [];
    while ($row = $result->fetch_assoc()) {
        $agotados[] = $row['nombre'];
    }
    if ($agotados) {
        $respuesta = "Productos agotados: " . implode(', ', $agotados);
    } else {
        $respuesta = "¡No tienes productos agotados!";
    }
}
// 3. ¿Cuántos productos hay en inventario?
elseif (preg_match('/(cu[aá]ntos|total)[^\\w]*(productos|inventario)/i', $pregunta)) {
    $result = $conn->query("SELECT COUNT(*) as total FROM inventario WHERE cantidad > 0");
    $row = $result->fetch_assoc();
    $respuesta = "Tienes {$row['total']} productos en inventario.";
}
// 4. ¿Quién fue el último cliente?
elseif (preg_match('/(últim[oa] cliente|quién compr[oó])/i', $pregunta)) {
    $result = $conn->query("SELECT cliente, fecha FROM ventas ORDER BY fecha DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $respuesta = "El último cliente fue {$row['cliente']} el " . date('d/m/Y', strtotime($row['fecha'])) . ".";
    } else {
        $respuesta = "No hay ventas registradas.";
    }
}
// 7. ¿Cuánto le he vendido a X? ¿Cuántas ventas tengo con X?
if (preg_match('/(cu[aá]nt[ao] le he vendido a|cu[aá]ntas ventas tengo con|total vendido a|ventas a)\s+([\w\sáéíóúñ]+)/i', $pregunta, $matches)) {
    $cliente = trim($matches[2]);
    $stmt = $conn->prepare("SELECT id, total, fecha FROM ventas WHERE TRIM(cliente) LIKE CONCAT('%', ?, '%') AND estado = 'completada'");
    $stmt->bind_param('s', $cliente);
    $stmt->execute();
    $result = $stmt->get_result();
    $ventas = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $total += $row['total'];
        // Buscar productos de cada venta
        $stmt2 = $conn->prepare("SELECT i.nombre, dv.cantidad FROM detalles_venta dv JOIN inventario i ON dv.inventario_id = i.id WHERE dv.venta_id = ?");
        $stmt2->bind_param('i', $row['id']);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $productos = [];
        while ($prod = $res2->fetch_assoc()) {
            $productos[] = $prod['nombre'] . " (" . $prod['cantidad'] . ")";
        }
        $stmt2->close();
        $ventas[] = "Venta el " . date('d/m/Y', strtotime($row['fecha'])) . ": " . implode(', ', $productos) . " ($" . number_format($row['total'], 2) . ")";
    }
    $num = count($ventas);
    if ($num > 0) {
        $respuesta = "Le has vendido a $cliente $num veces por un total de $" . number_format($total, 2) . ".\n" . implode("\n", $ventas);
    } else {
        // Sugerir clientes existentes
        $clientes = [];
        $res = $conn->query("SELECT DISTINCT TRIM(cliente) as cliente FROM ventas");
        while ($rowc = $res->fetch_assoc()) {
            $clientes[] = $rowc['cliente'];
        }
        $respuesta = "Nunca le has vendido a $cliente. Clientes registrados: " . implode(', ', $clientes);
    }
    $stmt->close();
}
// 5. ¿Cuáles son mis ventas? ¿Cuánto he vendido?
else if (preg_match('/(ventas|vendido|total de ventas|mis ventas)/i', $pregunta)) {
    $result = $conn->query("SELECT SUM(total) as total_ventas, COUNT(*) as num_ventas FROM ventas WHERE estado = 'completada'");
    $row = $result->fetch_assoc();
    $total = number_format($row['total_ventas'] ?? 0, 2);
    $num = $row['num_ventas'] ?? 0;
    $respuesta = "Has realizado $num ventas por un total de $$total.";
}

echo json_encode(['respuesta' => $respuesta]);
$conn->close();
?> 