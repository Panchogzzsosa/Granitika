<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $nombre = $_POST['nombre'];
        $tipo = $_POST['tipo'];
        $cantidad = $_POST['cantidad'];
        $unidad_medida = $_POST['unidad_medida'];
        $precio_unitario = $_POST['precio_unitario'];
        
        // Determinar el estado basado en la cantidad
        $estado = 'disponible';
        if ($cantidad <= 0) {
            $estado = 'agotado';
        } elseif ($cantidad < 10) {
            $estado = 'bajo_stock';
        }

        // Manejar la subida de imagen
        $imagen = 'default.jpg'; // Valor por defecto
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
                    UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente',
                    UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
                    UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco',
                    UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
                ];
                $errorMessage = isset($errorMessages[$_FILES['imagen']['error']]) 
                    ? $errorMessages[$_FILES['imagen']['error']] 
                    : 'Error desconocido al subir el archivo';
                throw new Exception($errorMessage);
            }

            $file = $_FILES['imagen'];
            
            // Verificar el tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Tipo de archivo no permitido. Solo se permiten imágenes JPG, PNG y GIF.");
            }
            
            // Verificar el tamaño del archivo (máximo 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("El archivo es demasiado grande. El tamaño máximo permitido es 5MB.");
            }
            
            // Generar nombre único para el archivo
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $targetPath = '../img/productos/' . $fileName;
            
            // Verificar si el directorio existe y tiene permisos
            if (!is_dir('../img/productos')) {
                if (!mkdir('../img/productos', 0777, true)) {
                    throw new Exception("No se pudo crear el directorio para las imágenes.");
                }
            }
            
            if (!is_writable('../img/productos')) {
                throw new Exception("El directorio de imágenes no tiene permisos de escritura.");
            }
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $imagen = $fileName;
                
                // Si estamos actualizando y hay una imagen anterior que no es la default, eliminarla
                if ($id) {
                    $query = "SELECT imagen FROM inventario WHERE id = ? AND imagen != 'default.jpg'";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $oldImage = '../img/productos/' . $row['imagen'];
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }
                }
            } else {
                throw new Exception("Error al mover el archivo subido. Verifica los permisos del directorio.");
            }
        }
        
        if ($id) {
            // Actualizar producto existente
            $query = "UPDATE inventario SET 
                     nombre = ?, tipo = ?, cantidad = ?, unidad_medida = ?, 
                     precio_unitario = ?, estado = ?, imagen = ? 
                     WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdssssi", $nombre, $tipo, $cantidad, $unidad_medida, 
                            $precio_unitario, $estado, $imagen, $id);
        } else {
            // Insertar nuevo producto
            $query = "INSERT INTO inventario (nombre, tipo, cantidad, unidad_medida, 
                     precio_unitario, estado, imagen, fecha_ingreso) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdssss", $nombre, $tipo, $cantidad, $unidad_medida, 
                            $precio_unitario, $estado, $imagen);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Error al guardar el producto en la base de datos: " . $stmt->error);
        }
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 