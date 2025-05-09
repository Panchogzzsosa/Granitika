<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);
$estado = $data['estado'] ?? '';

if ($id <= 0 || !in_array($estado, ['completada', 'pendiente'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$stmt = $conn->prepare("UPDATE ventas SET estado = ? WHERE id = ?");
$stmt->bind_param('si', $estado, $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado.']);
}
$stmt->close();
$conn->close();
?> 