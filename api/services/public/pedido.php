<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/pedido_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            // Acción para agregar un producto al carrito de compras.
            case 'createDetail':
                // Validar y sanitizar datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Comenzar el pedido
                if (!$pedido->startOrder()) {
                    $result['error'] = 'Ocurrió un problema al iniciar el pedido';
                } elseif (
                    !$pedido->setProducto($_POST['idProducto']) ||
                    !$pedido->setCantidad($_POST['cantidadProducto'])
                ) {
                    $result['error'] = $pedido->getDataError();
                } else {
                    // Verificar existencias del producto
                    $productoId = $_POST['idProducto'];
                    $cantidadProducto = $_POST['cantidadProducto'];

                    // Asumir que existe un método para verificar existencias
                    $existencias = $pedido->checkProductExistencias($productoId);

                    if ($cantidadProducto > $existencias) {
                        $result['error'] = 'No hay suficientes existencias del producto';
                    } elseif ($pedido->createDetail()) {
                        $result['status'] = 1;
                        $result['message'] = 'Producto agregado correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al agregar el producto';
                    }
                }
                break;

            // Acción para crear una tarjeta.
            case 'createTarget':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pedido->createTarget(
                        $_POST['tipo_targeta'],
                        $_POST['tipo_uso'],
                        $_POST['numero_targeta'],
                        $_POST['nombre_targeta'],
                        $_POST['fecha_expiracion'],
                        $_POST['codigo_verificacion'],
                        $_SESSION['idCliente']
                    )
                ) {
                    $result['error'] = 'Ocurrió un problema al agregar la tarjeta';
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Tarjeta agregada correctamente';
                }
                break;
            // Acción para obtener los números de las tarjetas.
            case 'getCardNumbers':
                $result['dataset'] = $pedido->getCardNumbers($_SESSION['idCliente']);
                if ($result['dataset']) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al obtener los números de las tarjetas';
                }
                break;
            // Acción para obtener los productos agregados en el carrito de compras.
            case 'readDetail':
                if (!$pedido->getOrder()) {
                    $result['error'] = 'No ha agregado productos al carrito';
                } elseif ($result['dataset'] = $pedido->readDetail()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen productos en el carrito';
                }
                break;
            // Acción para actualizar la cantidad de un producto en el carrito de compras.
            case 'updateDetail':
                $_POST = Validator::validateForm($_POST);

                // Validar datos recibidos
                if (
                    !$pedido->setIdDetalle($_POST['idDetalle']) or
                    !$pedido->setCantidad($_POST['cantidadProducto'])
                ) {
                    $result['error'] = $pedido->getDataError();
                } else {
                    // Obtener las existencias actuales del producto
                    $sqlGetProduct = 'SELECT id_producto, cantidad_producto FROM tb_detalles_pedidos WHERE id_detalle = ? AND id_pedido = ?';
                    $paramsGetProduct = array($_POST['idDetalle'], $_SESSION['idPedido']);
                    $currentDetail = Database::getRow($sqlGetProduct, $paramsGetProduct);

                    if (!$currentDetail) {
                        $result['error'] = 'Detalle del pedido no encontrado.';
                    } else {
                        $idProducto = $currentDetail['id_producto'];
                        $cantidadAnterior = $currentDetail['cantidad_producto'];

                        // Obtener existencias actuales del producto
                        $sqlGetStock = 'SELECT existencias_producto FROM tb_productos WHERE id_producto = ?';
                        $paramsGetStock = array($idProducto);
                        $productStock = Database::getRow($sqlGetStock, $paramsGetStock);

                        if (!$productStock) {
                            $result['error'] = 'Error al obtener las existencias del producto.';
                        } else {
                            $existencias = $productStock['existencias_producto'];
                            $diferenciaCantidad = $_POST['cantidadProducto'] - $cantidadAnterior;

                            // Validar si la cantidad solicitada no excede las existencias
                            if ($diferenciaCantidad > 0 && $diferenciaCantidad > $existencias) {
                                $result['error'] = 'La cantidad solicitada excede las existencias disponibles.';
                            } else {
                                // Actualizar el detalle
                                if ($pedido->updateDetail()) {
                                    $result['status'] = 1;
                                    $result['message'] = 'Cantidad modificada correctamente';
                                } else {
                                    $result['error'] = 'Ocurrió un problema al modificar la cantidad';
                                }
                            }
                        }
                    }
                }
                break;
            // Acción para remover un producto del carrito de compras.
            case 'deleteDetail':
                if (!$pedido->setIdDetalle($_POST['idDetalle'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($pedido->deleteDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto removido correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al remover el producto';
                }
                break;

            // Acción para finalizar el carrito de compras.
            case 'finishOrder':
                if ($pedido->finishOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pedido iniciado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al iniciar el pedido';
                }
                break;
            // Acción para eliminar el carrito de compras.
            case 'deleteOrder':
                if ($pedido->deleteOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Carrito borrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al borrar el carrito';
                }
                break;

            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando un cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'createDetail':
                $result['error'] = 'Debe iniciar sesión para agregar el producto al carrito';
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
        }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print (json_encode($result));
} else {
    print (json_encode('Recurso no disponible'));
}

