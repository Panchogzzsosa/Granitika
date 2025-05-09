<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../config/database.php';
header('Content-Type: application/json');

$query = "SELECT id, nombre, cantidad AS stock, precio_unitario FROM inventario ORDER BY nombre ASC";
$result = $conn->query($query);

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'stock' => $row['stock'],
        'precio_unitario' => $row['precio_unitario']
    ];
}

echo json_encode($productos);
$conn->close();
?> 