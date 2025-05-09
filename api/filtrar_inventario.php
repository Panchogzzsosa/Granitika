<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$nombre = $data['nombre'] ?? '';
$tipo = $data['tipo'] ?? '';
$estado = $data['estado'] ?? '';

$where = [];
$params = [];
$types = '';

if ($nombre !== '') {
    $where[] = "nombre LIKE ?";
    $params[] = "%$nombre%";
    $types .= 's';
}
if ($tipo !== '') {
    $where[] = "tipo = ?";
    $params[] = $tipo;
    $types .= 's';
}
if ($estado !== '') {
    $where[] = "estado = ?";
    $params[] = $estado;
    $types .= 's';
}

$sql = "SELECT id, nombre, tipo, cantidad, unidad_medida, precio_unitario, estado, imagen FROM inventario";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY nombre";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);
$stmt->close();
$conn->close();
?> 