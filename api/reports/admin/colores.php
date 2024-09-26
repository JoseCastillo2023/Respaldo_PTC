<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once ('../../helpers/report.php');
// Se incluyen las clases para el acceso a datos de coloress.
require_once ('../../models/data/colores_data.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Colores Registrados');
// Se instancia el modelo Cliente para obtener los datos.

// Se instancia el modelo colores para obtener los datos.
$coloresmodel = new ColoresData;
// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($datacolores = $coloresmodel->readAll()) {
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(36, 92, 157);
    $pdf->setTextColor(255, 255, 255);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);

    // Encabezados
    $pdf->cell(50, 10, 'ID', 1, 0, 'C', 1);
    $pdf->cell(140, 10, 'Nombre', 1, 1, 'C', 1); // Cambiado a 140 y con salto de línea

    // Se establece la fuente para los datos de los coloress.
    $pdf->setFont('Arial', '', 11);
    // Recorremos los datos de los coloress
    foreach ($datacolores as $colores) {
        $pdf->setTextColor(0, 0, 0);

        // ID del colores
        $pdf->cell(50, 10, $colores['id_color'], 1, 0, 'C');

        // Nombre
        $pdf->cell(140, 10, $pdf->encodeString($colores['nombre']), 1, 1, 'C'); // Cambiado a 140 y con salto de línea
    }
} else {
    // Si no hay coloress registrados
    $pdf->cell(190, 10, $pdf->encodeString('No hay colores registrados'), 1, 1, 'C'); // Cambiado el ancho y con salto de línea
}

// Se llama implícitamente al método footer() y se envía el documento al navegador web.
$pdf->output('I', 'Colores.pdf');
?>