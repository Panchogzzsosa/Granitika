<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        $query = "SELECT * FROM inventario WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'producto' => $row
            ]);
        } else {
            throw new Exception("Producto no encontrado");
        }
    } else {
        throw new Exception("ID no proporcionado");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 