<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn->begin_transaction();
        
        try {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $proveedor = $_POST['proveedor'];
            $fecha_entrega = $_POST['fecha_entrega'] ? $_POST['fecha_entrega'] : null;
            $total = $_POST['total'];
            $notas = $_POST['notas'];
            $productos = $_POST['productos'];
            $cantidades = $_POST['cantidades'];
            $precios = $_POST['precios'];
            
            if ($id) {
                // Actualizar compra existente
                $query = "UPDATE compras SET 
                         proveedor = ?, fecha_entrega = ?, total = ?, notas = ? 
                         WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssdsi", $proveedor, $fecha_entrega, $total, $notas, $id);
                $stmt->execute();
                
                // Eliminar detalles anteriores
                $query = "DELETE FROM detalles_compra WHERE compra_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
            } else {
                // Insertar nueva compra
                $query = "INSERT INTO compras (proveedor, fecha_compra, fecha_entrega, total, estado, notas) 
                         VALUES (?, CURDATE(), ?, ?, 'pendiente', ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssds", $proveedor, $fecha_entrega, $total, $notas);
                $stmt->execute();
                $id = $conn->insert_id;
            }
            
            // Insertar detalles de compra
            $query = "INSERT INTO detalles_compra (compra_id, inventario_id, cantidad, precio_unitario, subtotal) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            for ($i = 0; $i < count($productos); $i++) {
                $inventario_id = $productos[$i];
                $cantidad = $cantidades[$i];
                $precio_unitario = $precios[$i];
                $subtotal = $cantidad * $precio_unitario;
                
                $stmt->bind_param("iiddd", $id, $inventario_id, $cantidad, $precio_unitario, $subtotal);
                $stmt->execute();
            }
            
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    } else {
        throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 