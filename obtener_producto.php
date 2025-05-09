<?php
header('Content-Type: application/json');
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT id, nombre, tipo, cantidad, unidad_medida, estado FROM inventario WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'producto' => $row
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
}
$stmt->close();
$conn->close(); 