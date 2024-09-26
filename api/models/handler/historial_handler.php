<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
 */
class HistorialHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
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
        $this->estado = 'Entregado';
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
            // Se obtiene el ultimo valor insertado de la llave primaria en la tabla pedido.
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
        // Primero, insertar el detalle del pedido con una subconsulta para obtener el precio del producto.
        $sql = 'INSERT INTO tb_detalles_pedidos(id_producto, precio_producto, cantidad_producto, id_pedido)
            VALUES(?, (SELECT precio_producto FROM tb_productos WHERE id_producto = ?), ?, ?)';
        $params = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idPedido']);

        if (Database::executeRow($sql, $params)) {
            // Si la inserción del detalle es exitosa, reducir las existencias del producto.
            $sql = 'UPDATE tb_productos SET existencias_producto = existencias_producto - ? WHERE id_producto = ?';
            $params = array($this->cantidad, $this->producto);
            return Database::executeRow($sql, $params);
        } else {
            return false;
        }
    }

    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readDetail()
{
    $this->estado = 'Entregado';
    $sql = 'SELECT id_detalle, nombre_producto, tb_detalles_pedidos.precio_producto, 
                   tb_detalles_pedidos.cantidad_producto, tb_productos.imagen_producto, 
                   tb_pedidos.fecha_registro, tb_pedidos.direccion_pedido
            FROM tb_detalles_pedidos
            INNER JOIN tb_pedidos USING(id_pedido)
            INNER JOIN tb_productos USING(id_producto)
            WHERE id_pedido IN (
                SELECT id_pedido 
                FROM tb_pedidos 
                WHERE estado_pedido = ? AND id_cliente = ?
            )';
    $params = array($this->estado, $_SESSION['idCliente']);
    return Database::getRows($sql, $params);
}

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        $this->estado = 'Entregado';
        $sql = 'UPDATE tb_pedidos
            SET estado_pedido = ?
            WHERE id_pedido = ?';
        $params = array($this->estado, $_SESSION['idPedido']);

        if (Database::executeRow($sql, $params)) {
            // Si la actualización del estado del pedido es exitosa, proceder a actualizar las existencias de los productos.

            // Seleccionar todos los detalles de pedidos para el pedido finalizado.
            $sql = 'SELECT id_producto, cantidad_producto
                FROM tb_detalles_pedidos
                WHERE id_pedido = ?';
            $params = array($_SESSION['idPedido']);

            // Obtener todos los detalles del pedido finalizado.
            $result = Database::getRows($sql, $params);

            if ($result) {
                // Recorrer cada detalle de pedido y actualizar las existencias del producto.
                foreach ($result as $row) {
                    $sql = 'UPDATE tb_productos
                        SET existencias_producto = existencias_producto - ?
                        WHERE id_producto = ?';
                    $params = array($row['cantidad_producto'], $row['id_producto']);
                    Database::executeRow($sql, $params);
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE tb_detalles_pedidos
                SET cantidad_producto = ?
                WHERE id_detalle = ? AND id_pedido = ?';
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        $sql = 'DELETE FROM tb_detalles_pedidos
                WHERE id_detalle = ? AND id_pedido = ?';
        $params = array($this->id_detalle, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }

    public function deleteOrder()
    {
        $sql = 'UPDATE tb_pedidos
            SET estado_pedido = ?
            WHERE estado_pedido = ?';
        $params = array('Historial', 'Entregado');
        return Database::executeRow($sql, $params);
    }


}
