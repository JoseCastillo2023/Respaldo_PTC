<?php
// Se incluye la clase para validar los datos de entrada.
require_once ('../../helpers/validator.php');
// Se incluye la clase padre.
require_once ('../../models/handler/grafico_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class GraficoData extends GraficoHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
