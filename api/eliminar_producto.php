<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : 0;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

// Obtener la imagen actual
$stmt = $conn->prepare("SELECT imagen FROM inventario WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$imagen = $row['imagen'] ?? null;
$stmt->close();

// Eliminar el producto
$stmt = $conn->prepare("DELETE FROM inventario WHERE id = ?");
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    // Eliminar la imagen si existe
    if ($imagen && file_exists($imagen)) {
        @unlink($imagen);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el producto']);
}
?> 