<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

try {
    $conn->begin_transaction();
    
    // Obtener detalles de la compra para actualizar el inventario
    $query = "SELECT producto, cantidad FROM compras WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Restar la cantidad del inventario
        $query_update = "UPDATE inventario SET cantidad = cantidad - ? WHERE nombre = ?";
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param("ds", $row['cantidad'], $row['producto']);
        $stmt_update->execute();
    }
    
    // Eliminar la compra
    $stmt = $conn->prepare("DELETE FROM compras WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la compra: ' . $e->getMessage()]);
}
$stmt->close();
$conn->close();
?> 