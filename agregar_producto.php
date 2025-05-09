<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$cantidad = floatval($_POST['cantidad'] ?? 0);
$unidad_medida = 'm2';
$precio_unitario = floatval($_POST['precio_unitario'] ?? 0);
$fecha_ingreso = date('Y-m-d');
$descripcion = '';

if (!$nombre || !$tipo) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Validar que no exista un producto con el mismo nombre
$stmt = $conn->prepare("SELECT id FROM inventario WHERE nombre = ? LIMIT 1");
$stmt->bind_param('s', $nombre);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Ya existe un producto con ese nombre']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Asegurar que la carpeta img/ existe
$dir_img = __DIR__ . '/img';
if (!is_dir($dir_img)) {
    mkdir($dir_img, 0777, true);
}

$imagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombre_archivo = uniqid() . '_' . time() . '.' . $ext;
    $ruta_destino = 'img/' . $nombre_archivo;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/' . $ruta_destino)) {
        $imagen = $ruta_destino;
    }
}

// Calcular estado automáticamente
if ($cantidad == 0) {
    $estado = 'agotado';
} elseif ($cantidad < 10) {
    $estado = 'bajo_stock';
} else {
    $estado = 'disponible';
}

$stmt = $conn->prepare("INSERT INTO inventario (nombre, tipo, cantidad, unidad_medida, precio_unitario, descripcion, fecha_ingreso, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssdssssss', $nombre, $tipo, $cantidad, $unidad_medida, $precio_unitario, $descripcion, $fecha_ingreso, $estado, $imagen);
$ok = $stmt->execute();
if (!$ok) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
} else {
    echo json_encode(['success' => true]);
}
$stmt->close();
$conn->close();
// Sugerencia: Para máxima seguridad, agrega este índice único en tu base de datos:
// ALTER TABLE inventario ADD UNIQUE(nombre); 