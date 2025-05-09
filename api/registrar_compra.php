<?php
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$proveedor = trim($data['proveedor'] ?? '');
$total = floatval($data['total'] ?? 0);
$fecha = $data['fecha'] ?? '';
$estado = $data['estado'] ?? '';
$producto_id = intval($data['producto_id'] ?? 0);
$cantidad = intval($data['cantidad'] ?? 0);

// Obtener el nombre del producto por id
$producto_nombre = '';
if ($producto_id > 0) {
    $stmt = $conn->prepare("SELECT nombre FROM inventario WHERE id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $producto_nombre = $row['nombre'];
    }
    $stmt->close();
}

if ($proveedor === '' || $total <= 0 || $fecha === '' || ($estado !== 'completada' && $estado !== 'pendiente') || 
    $producto_id <= 0 || $producto_nombre === '' || $cantidad <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
    exit;
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Insertar la compra
    $stmt = $conn->prepare("INSERT INTO compras (proveedor, total, fecha_compra, estado, producto, cantidad) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssi", $proveedor, $total, $fecha, $estado, $producto_nombre, $cantidad);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al registrar la compra");
    }
    
    // Si la compra está completada, actualizar el inventario
    if ($estado === 'completada') {
        $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad + ? WHERE id = ?");
        $stmt->bind_param("ii", $cantidad, $producto_id);
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar el inventario");
        }
    }
    
    // Confirmar transacción
    $conn->commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 