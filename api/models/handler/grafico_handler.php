<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
 */
class GraficoHandler
{

    public function cantidadClientePorFecha()
    {
        $sql = 'SELECT DATE(fecha_registro) AS fecha, COUNT(id_cliente) AS cantidad
        FROM tb_clientes
        GROUP BY DATE(fecha_registro)
        ORDER BY fecha ASC;
        ';
        return Database::getRows($sql);
    }

    public function cantidadProductosCategoria()
    {
        $sql = 'SELECT nombre, COUNT(id_producto) cantidad
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                GROUP BY nombre ORDER BY cantidad DESC LIMIT 5';
        return Database::getRows($sql);
    }

    public function porcentajeProductosCategoria()
    {
        $sql = 'SELECT nombre, ROUND((COUNT(id_producto) * 100.0 / (SELECT COUNT(id_producto) FROM tb_productos)), 2) porcentaje
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                GROUP BY nombre ORDER BY porcentaje DESC';
        return Database::getRows($sql);
    }

    public function graficoPedido()
    {
        $sql = 'SELECT estado_pedido, COUNT(id_pedido) AS cantidad
        FROM tb_pedidos
        GROUP BY estado_pedido;;
        ';
        return Database::getRows($sql);
    }

    public function graficoVenta()
    {
        $sql = 'SELECT DATE(p.fecha_registro) AS fecha, COUNT(dp.id_producto) AS ventas
            FROM tb_pedidos p
            JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
            WHERE p.estado_pedido = "Entregado"
            GROUP BY DATE(p.fecha_registro)
            ORDER BY fecha ASC;';
        return Database::getRows($sql);
    }

}
