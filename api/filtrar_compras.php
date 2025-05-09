<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$proveedor = $data['proveedor'] ?? '';
$estado = $data['estado'] ?? '';
$fecha = $data['fecha'] ?? '';

$where = [];
$params = [];
$types = '';

if ($proveedor !== '') {
    $where[] = "proveedor LIKE ?";
    $params[] = "%$proveedor%";
    $types .= 's';
}

if ($estado !== '') {
    $where[] = "estado = ?";
    $params[] = $estado;
    $types .= 's';
}

if ($fecha !== '') {
    $where[] = "DATE(fecha_compra) = ?";
    $params[] = $fecha;
    $types .= 's';
}

$sql = "SELECT id, proveedor, producto, cantidad, total, fecha_compra, estado FROM compras";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY fecha_compra DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$compras = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha_formateada'] = date('d/m/Y', strtotime($row['fecha_compra']));
    $compras[] = $row;
}

echo json_encode($compras);
$stmt->close();
$conn->close(); 