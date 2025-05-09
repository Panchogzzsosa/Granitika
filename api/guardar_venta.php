<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn->begin_transaction();
        
        try {
            $cliente = $_POST['cliente'];
            $producto_id = $_POST['producto'];
            $cantidad = $_POST['cantidad'];
            $total = $_POST['total'];
            $fecha = $_POST['fecha'];
            $estado = $_POST['estado'];
            
            // Insertar nueva venta
            $query = "INSERT INTO ventas (cliente, fecha, total, estado) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssds", $cliente, $fecha, $total, $estado);
            $stmt->execute();
            $venta_id = $conn->insert_id;
            
            // Insertar detalle de venta
            $query = "INSERT INTO detalles_venta (venta_id, inventario_id, cantidad, precio_unitario, subtotal) 
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            // Obtener precio unitario del inventario
            $query_precio = "SELECT precio_unitario FROM inventario WHERE id = ?";
            $stmt_precio = $conn->prepare($query_precio);
            $stmt_precio->bind_param("i", $producto_id);
            $stmt_precio->execute();
            $result_precio = $stmt_precio->get_result();
            $row_precio = $result_precio->fetch_assoc();
            $precio_unitario = $row_precio['precio_unitario'];
            
            $subtotal = $cantidad * $precio_unitario;
            
            $stmt->bind_param("iiddd", $venta_id, $producto_id, $cantidad, $precio_unitario, $subtotal);
            $stmt->execute();
            
            // Actualizar inventario
            $query_update = "UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bind_param("di", $cantidad, $producto_id);
            $stmt_update->execute();
            
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