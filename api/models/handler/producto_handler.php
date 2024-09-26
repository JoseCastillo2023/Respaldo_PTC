<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
 */
class ProductoHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $precio = null;
    protected $existencias = null;
    protected $imagen = null;
    protected $categoria = null;
    protected $estado = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/productos/';

    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, nombre, estado_producto
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                WHERE nombre_producto LIKE ? OR descripcion_producto LIKE ?
                ORDER BY nombre_producto';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_productos(nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, estado_producto, id_categoria, id_administrador)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->descripcion, $this->precio, $this->existencias, $this->imagen, $this->estado, $this->categoria, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, nombre, estado_producto
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                ORDER BY nombre_producto';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_producto, nombre_producto, descripcion_producto, precio_producto, existencias_producto, imagen_producto, id_categoria, estado_producto
                FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen_producto
                FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_productos
                SET imagen_producto = ?, nombre_producto = ?, descripcion_producto = ?, precio_producto = ?, estado_producto = ?, id_categoria = ?
                WHERE id_producto = ?';
        $params = array($this->imagen, $this->nombre, $this->descripcion, $this->precio, $this->estado, $this->categoria, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_productos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function readProductosCategoria()
    {
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, existencias_producto
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                WHERE id_categoria = ? AND estado_producto = true
                ORDER BY nombre_producto';
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }

    public function productosCategoria()
    {
        $sql = 'SELECT nombre_producto, precio_producto, estado_producto
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                WHERE id_categoria = ?
                ORDER BY nombre_producto';
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }

    public function readComments()
    {
        $sql = 'SELECT c.id_comentario, c.id_producto, c.id_cliente, 
                       c.calificacion_producto, c.comentario_producto, 
                       c.fecha_valoracion, c.estado_comentario,
                       cl.nombre_cliente, cl.apellido_cliente
                FROM tb_comentarios AS c
                INNER JOIN tb_productos AS p ON c.id_producto = p.id_producto
                INNER JOIN tb_clientes AS cl ON c.id_cliente = cl.id_cliente
                WHERE c.id_producto = ?';

        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    public function addComments($idProducto, $calificacionProducto, $comentarioProducto)
    {
        // Obtén el ID del cliente de la sesión
        session_start();
        if (!isset($_SESSION['idCliente'])) {
            return ['status' => 0, 'error' => 'No hay sesión de cliente iniciada.'];
        }

        $idCliente = $_SESSION['idCliente'];

        // Consulta SQL para insertar un nuevo comentario
        $sql = 'INSERT INTO tb_comentarios (id_producto, id_cliente, calificacion_producto, comentario_producto, fecha_valoracion, estado_comentario)
            VALUES (?, ?, ?, ?, NOW(), ?)';

        // Parámeteros para la consulta
        $params = array($idProducto, $idCliente, $calificacionProducto, $comentarioProducto, 1); // El estado del comentario puede ser '1' para activo, ajusta según tus necesidades.

        // Ejecuta la consulta y retorna el resultado
        return Database::executeRow($sql, $params);
    }

    public function averageRating()
    {
        // SQL para obtener el promedio de calificaciones del producto.
        $sql = 'SELECT AVG(CAST(c.calificacion_producto AS UNSIGNED)) AS calificacion_promedio
                FROM tb_comentarios AS c
                WHERE c.id_producto = ?';

        $params = array($this->id);
        $result = Database::getRow($sql, $params);

        if ($result) {
            // Obtener el promedio calculado.
            $promedio = $result['calificacion_promedio'];
            // Redondear el promedio al entero más cercano.
            $promedioRedondeado = round($promedio);

            // Actualizar el campo de calificación promedio del producto.
            $this->updateAverageRating($promedioRedondeado);

            return $promedioRedondeado;
        } else {
            return null; // No hay comentarios para el producto.
        }
    }

    private function updateAverageRating($calificacionPromedio)
    {
        $sql = 'UPDATE tb_productos
                SET calificacion_promedio = ?
                WHERE id_producto = ?';
        $params = array($calificacionPromedio, $this->id);
        return Database::executeRow($sql, $params);
    }

}
