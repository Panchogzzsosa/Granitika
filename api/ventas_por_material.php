<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$query = "SELECT tipo_material, COUNT(*) as total 
          FROM ventas 
          WHERE estado = 'completada' 
          GROUP BY tipo_material";

$result = $conn->query($query);

$labels = [];
$values = [];

while($row = $result->fetch_assoc()) {
    $labels[] = ucfirst($row['tipo_material']);
    $values[] = $row['total'];
}

echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?> 