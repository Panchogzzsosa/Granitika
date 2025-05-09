<?php
header('Content-Type: application/json');
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nombre = trim($_POST['nombre'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$cantidad = floatval($_POST['cantidad'] ?? 0);
$unidad_medida = 'm2';
$precio_unitario = floatval($_POST['precio_unitario'] ?? 0);

if (!$id || !$nombre || !$tipo) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Calcular estado automáticamente
if ($cantidad == 0) {
    $estado = 'agotado';
} elseif ($cantidad < 10) {
    $estado = 'bajo_stock';
} else {
    $estado = 'disponible';
}

// Obtener la imagen actual
$stmt = $conn->prepare("SELECT imagen FROM inventario WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$imagen_actual = $row['imagen'] ?? null;
$stmt->close();

// Procesar imagen si se sube una nueva
$imagen = $imagen_actual;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = uniqid() . '_' . time() . '.' . $ext;
    $ruta_destino = 'img/' . $nombre_archivo;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
        $imagen = $ruta_destino;
        if ($imagen_actual && file_exists($imagen_actual) && $imagen_actual !== $imagen) {
            @unlink($imagen_actual);
        }
    }
}

$stmt = $conn->prepare("UPDATE inventario SET nombre=?, tipo=?, cantidad=?, unidad_medida=?, precio_unitario=?, estado=?, imagen=? WHERE id=?");
$stmt->bind_param('ssdssssi', $nombre, $tipo, $cantidad, $unidad_medida, $precio_unitario, $estado, $imagen, $id);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el producto']);
} 