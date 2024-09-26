<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');

/*
 *   Clase para manejar el comportamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
 */
class PedidoHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    private $idPedido;
    protected $id_tarjeta = null;
    protected $id_pedido = null;
    protected $id_detalle = null;
    protected $cliente = null;
    protected $producto = null;
    protected $cantidad = null;
    protected $estado = null;

    /*
     *   ESTADOS DEL PEDIDO
     *   Pendiente (valor por defecto en la base de datos). Pedido en proceso y se puede modificar el detalle.
     *   Finalizado. Pedido terminado por el cliente y ya no es posible modificar el detalle.
     *   Entregado. Pedido enviado al cliente.
     *   Anulado. Pedido cancelado por el cliente después de ser finalizado.
     */

    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    // Método para verificar si existe un pedido en proceso con el fin de iniciar o continuar una compra.
    public function getOrder()
    {
        $this->estado = 'Pendiente';
        $sql = 'SELECT id_pedido
                FROM tb_pedidos
                WHERE estado_pedido = ? AND id_cliente = ?';
        $params = array($this->estado, $_SESSION['idCliente']);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['idPedido'] = $data['id_pedido'];
            return true;
        } else {
            return false;
        }
    }

    // Método para iniciar un pedido en proceso.
    public function startOrder()
    {
        if ($this->getOrder()) {
            return true;
        } else {
            $sql = 'INSERT INTO tb_pedidos(direccion_pedido, id_cliente)
                    VALUES((SELECT direccion_cliente FROM tb_clientes WHERE id_cliente = ?), ?)';
            $params = array($_SESSION['idCliente'], $_SESSION['idCliente']);
            // Se obtiene el último valor insertado de la llave primaria en la tabla pedido.
            if ($_SESSION['idPedido'] = Database::getLastRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para agregar un producto al carrito de compras.
    public function createDetail()
    {

        try {
            // Verificar si la cantidad solicitada es menor o igual a las existencias disponibles
            $sqlCheckStock = 'SELECT existencias_producto FROM tb_productos WHERE id_producto = ?';
            $paramsCheckStock = array($this->producto);
            $result = Database::getRow($sqlCheckStock, $paramsCheckStock);
    
            if (!$result) {
                throw new Exception("Producto no encontrado.");
            }
    
            $existenciasDisponibles = $result['existencias_producto'];
    
            if ($this->cantidad > $existenciasDisponibles) {
                throw new Exception("La cantidad solicitada excede las existencias disponibles.");
            }
    
            // Actualizar las existencias del producto
            $sqlUpdateStock = 'UPDATE tb_productos SET existencias_producto = existencias_producto - ? WHERE id_producto = ?';
            $paramsUpdateStock = array($this->cantidad, $this->producto);
            Database::executeRow($sqlUpdateStock, $paramsUpdateStock);
    
            // Insertar el detalle del pedido
            $sqlInsertDetail = 'INSERT INTO tb_detalles_pedidos (id_producto, precio_producto, cantidad_producto, id_pedido)
                                VALUES (?, (SELECT precio_producto FROM tb_productos WHERE id_producto = ?), ?, ?)';
            $paramsInsertDetail = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idPedido']);
            Database::executeRow($sqlInsertDetail, $paramsInsertDetail);

            
            return true;
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            throw $e;
        }
    }
    

    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readDetail()
    {
        $sql = 'SELECT id_detalle, nombre_producto, tb_detalles_pedidos.precio_producto, tb_detalles_pedidos.cantidad_producto, tb_productos.imagen_producto
                FROM tb_detalles_pedidos
                INNER JOIN tb_pedidos USING(id_pedido)
                INNER JOIN tb_productos USING(id_producto)
                WHERE id_pedido = ?';
        $params = array($_SESSION['idPedido']);
        return Database::getRows($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        $this->estado = 'En camino';
        $sql = 'UPDATE tb_pedidos
            SET estado_pedido = ?
            WHERE id_pedido = ?';
        $params = array($this->estado, $_SESSION['idPedido']);
    
        // Actualizar el estado del pedido a 'Entregado'
        if (Database::executeRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }
    

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
    
        try {
            // Obtener la cantidad anterior del producto en el carrito
            $sqlGetPreviousQuantity = 'SELECT cantidad_producto, id_producto FROM tb_detalles_pedidos WHERE id_detalle = ? AND id_pedido = ?';
            $paramsGetPreviousQuantity = array($this->id_detalle, $_SESSION['idPedido']);
            $result = Database::getRow($sqlGetPreviousQuantity, $paramsGetPreviousQuantity);
    
            if (!$result) {
                throw new Exception("Detalle del pedido no encontrado.");
            }
    
            $cantidadAnterior = $result['cantidad_producto'];
            $idProducto = $result['id_producto'];
    
            // Calcular la diferencia en la cantidad
            $diferenciaCantidad = $this->cantidad - $cantidadAnterior;
    
            // Actualizar las existencias del producto
            if ($diferenciaCantidad > 0) {
                // Si la cantidad ha aumentado, disminuir las existencias
                $sqlUpdateStockDecrease = 'UPDATE tb_productos SET existencias_producto = existencias_producto - ? WHERE id_producto = ?';
                $paramsUpdateStockDecrease = array($diferenciaCantidad, $idProducto);
                Database::executeRow($sqlUpdateStockDecrease, $paramsUpdateStockDecrease);
            } elseif ($diferenciaCantidad < 0) {
                // Si la cantidad ha disminuido, aumentar las existencias
                $sqlUpdateStockIncrease = 'UPDATE tb_productos SET existencias_producto = existencias_producto + ? WHERE id_producto = ?';
                $paramsUpdateStockIncrease = array(abs($diferenciaCantidad), $idProducto);
                Database::executeRow($sqlUpdateStockIncrease, $paramsUpdateStockIncrease);
            }
    
            // Actualizar el detalle del pedido con la nueva cantidad
            $sqlUpdateDetail = 'UPDATE tb_detalles_pedidos SET cantidad_producto = ? WHERE id_detalle = ? AND id_pedido = ?';
            $paramsUpdateDetail = array($this->cantidad, $this->id_detalle, $_SESSION['idPedido']);
            Database::executeRow($sqlUpdateDetail, $paramsUpdateDetail);
    
            return true;
        } catch (Exception $e) {

            throw $e;
        }
    }
    

// Método para obtener las existencias del producto
    public function getProductStock($idDetalle)
    {
        $sql = 'SELECT p.existencias_producto
                FROM tb_detalles_pedidos dp
                JOIN tb_productos p ON dp.id_producto = p.id_producto
                WHERE dp.id_detalle = ?';
        $params = array($idDetalle);
        $result = Database::getRow($sql, $params);
        return $result ? $result['existencias_producto'] : false;
    }

    public function checkProductExistencias($productoId)
{
    $sql = 'SELECT existencias_producto FROM tb_productos WHERE id_producto = ?';
    $params = array($productoId);
    $result = Database::getRow($sql, $params);
    
    if ($result) {
        return $result['existencias_producto'];
    } else {
        return 0; // Producto no encontrado o sin existencias
    }
}



    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        try {
            // Obtener la cantidad del producto en el detalle del pedido
            $sqlGetQuantity = 'SELECT cantidad_producto, id_producto FROM tb_detalles_pedidos WHERE id_detalle = ? AND id_pedido = ?';
            $paramsGetQuantity = array($this->id_detalle, $_SESSION['idPedido']);
            $result = Database::getRow($sqlGetQuantity, $paramsGetQuantity);
    
            if (!$result) {
                throw new Exception("Detalle del pedido no encontrado.");
            }
    
            $cantidadProducto = $result['cantidad_producto'];
            $idProducto = $result['id_producto'];
    
            // Actualizar las existencias del producto
            $sqlUpdateStock = 'UPDATE tb_productos SET existencias_producto = existencias_producto + ? WHERE id_producto = ?';
            $paramsUpdateStock = array($cantidadProducto, $idProducto);
            Database::executeRow($sqlUpdateStock, $paramsUpdateStock);
    
            // Eliminar el detalle del pedido
            $sqlDeleteDetail = 'DELETE FROM tb_detalles_pedidos WHERE id_detalle = ? AND id_pedido = ?';
            $paramsDeleteDetail = array($this->id_detalle, $_SESSION['idPedido']);
            Database::executeRow($sqlDeleteDetail, $paramsDeleteDetail);
    
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function deleteOrder()
    {
        $sql = 'DELETE FROM tb_pedidos
            WHERE estado_pedido = ?';
        $params = array('Pendiente');
        return Database::executeRow($sql, $params);
    }

    // Método para crear una tarjeta
    // Método para obtener los números de tarjeta basados en el id_cliente
    public function getCardNumbers($id_cliente)
    {
        $sql = 'SELECT id_tarjeta, numero_tarjeta FROM tb_tarjetas WHERE id_cliente = ?';
        $params = array($id_cliente);
        return Database::getRows($sql, $params); // Obtiene todos los números de tarjeta del cliente
    }

    // Método para crear una nueva tarjeta de pago
    public function createTarget($tipo_tarjeta, $tipo_uso, $numero_tarjeta, $nombre_tarjeta, $fecha_expiracion, $codigo_verificacion, $id_cliente)
    {
        $sql = 'INSERT INTO tb_tarjetas(tipo_tarjeta, tipo_uso, numero_tarjeta, nombre_tarjeta, fecha_expiracion, codigo_verificacion, id_cliente) 
                VALUES(?, ?, ?, ?, ?, ?, ?)';
        $params = array($tipo_tarjeta, $tipo_uso, $numero_tarjeta, $nombre_tarjeta, $fecha_expiracion, $codigo_verificacion, $id_cliente);
        return Database::executeRow($sql, $params);
    }

    public function readByClientAndStatus($id_cliente, $estado_pedido)
    {
        $sql = "SELECT p.id_pedido, p.direccion_pedido, p.fecha_registro, c.nombre_cliente, c.apellido_cliente, c.telefono_cliente, c.direccion_cliente, c.dui_cliente, c.correo_cliente
                FROM tb_pedidos p
                INNER JOIN tb_clientes c ON p.id_cliente = c.id_cliente
                WHERE p.estado_pedido = ? AND p.id_cliente = ?";
        $params = array($estado_pedido, $id_cliente);

        return Database::getRows($sql, $params);
    }

    // Método para obtener los detalles de un pedido
    public function readByPedido()
    {
        $sql = 'SELECT dp.*, p.nombre_producto 
            FROM tb_detalles_pedidos dp 
            JOIN tb_productos p ON dp.id_producto = p.id_producto 
            WHERE dp.id_pedido = ?';
        $params = array($this->id_pedido);
        return Database::getRows($sql, $params);
    }
}
