<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $id = null;
        if (isset($_DELETE['id'])) {
            $id = $_DELETE['id'];
        } elseif (isset($_GET['id'])) {
            $id = $_GET['id'];
        }
        if ($id) {
            $conn->begin_transaction();
            
            try {
                // Obtener detalles de la venta para restaurar el inventario
                $query = "SELECT inventario_id, cantidad FROM detalles_venta WHERE venta_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Restaurar cantidades en el inventario
                while ($row = $result->fetch_assoc()) {
                    $query_update = "UPDATE inventario SET cantidad = cantidad + ? WHERE id = ?";
                    $stmt_update = $conn->prepare($query_update);
                    $stmt_update->bind_param("di", $row['cantidad'], $row['inventario_id']);
                    $stmt_update->execute();
                }
                
                // Eliminar detalles de la venta
                $query = "DELETE FROM detalles_venta WHERE venta_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                // Eliminar la venta
                $query = "DELETE FROM ventas WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
        } else {
            throw new Exception("ID no proporcionado");
        }
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 