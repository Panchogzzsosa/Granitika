<?php
require_once 'config/database.php';
$nombre = 'Test';
$tipo = 'granito';
$cantidad = 1;
$unidad_medida = 'm2';
$estado = 'disponible';
$imagen = '';
$stmt = $conn->prepare("INSERT INTO inventario (nombre, tipo, cantidad, unidad_medida, estado, imagen) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssdsss', $nombre, $tipo, $cantidad, $unidad_medida, $estado, $imagen);
$ok = $stmt->execute();
if ($ok) {
    echo 'OK';
} else {
    echo $stmt->error;
}
$stmt->close();
$conn->close();
?>
