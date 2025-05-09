<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$cliente = isset($input['cliente']) ? trim($input['cliente']) : '';
$estado = isset($input['estado']) ? trim($input['estado']) : '';
$fecha = isset($input['fecha']) ? trim($input['fecha']) : '';

$where = [];
$params = [];
$types = '';

if ($cliente !== '') {
    $where[] = 'LOWER(TRIM(v.cliente)) = ?';
    $params[] = strtolower(trim($cliente));
    $types .= 's';
}
if ($estado !== '') {
    $where[] = 'v.estado = ?';
    $params[] = $estado;
    $types .= 's';
}
if ($fecha !== '') {
    $where[] = 'DATE(v.fecha) = ?';
    $params[] = $fecha;
    $types .= 's';
}

$sql = "SELECT v.id, v.cliente, v.total, v.fecha, v.estado, i.nombre AS producto, d.cantidad
        FROM ventas v
        JOIN detalles_venta d ON v.id = d.venta_id
        JOIN inventario i ON d.inventario_id = i.id";
if (count($where) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY v.fecha DESC';

$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$ventas = [];
while ($row = $result->fetch_assoc()) {
    $row['fecha_formateada'] = date('d/m/Y', strtotime($row['fecha']));
    $ventas[] = $row;
}
echo json_encode($ventas);
$stmt->close();
$conn->close(); 