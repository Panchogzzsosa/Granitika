<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Obtener datos de la venta
        $query = "SELECT * FROM ventas WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($venta = $result->fetch_assoc()) {
            // Obtener detalles de la venta
            $query = "SELECT dv.*, i.nombre as producto_nombre 
                     FROM detalles_venta dv 
                     JOIN inventario i ON dv.inventario_id = i.id 
                     WHERE dv.venta_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $detalles = [];
            while ($row = $result->fetch_assoc()) {
                $detalles[] = $row;
            }
            
            $venta['detalles'] = $detalles;
            echo json_encode($venta);
        } else {
            throw new Exception("Venta no encontrada");
        }
    } else {
        throw new Exception("ID no proporcionado");
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 