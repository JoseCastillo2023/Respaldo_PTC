<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../api/libraries/phpmailer651/src/Exception.php';
require __DIR__ . '/../../api/libraries/phpmailer651/src/PHPMailer.php';
require __DIR__ . '/../../api/libraries/phpmailer651/src/SMTP.php';
require __DIR__ . '/../../api/helpers/database.php';

header('Content-Type: application/json');

// Función para generar un código aleatorio
function generateRecoveryCode($length = 6)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $charactersLength - 1)];
    }
    return $code;
}

// Verificar el método de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => false, 'message' => ''];

    $clienteEmail = $_POST['clienteEmail'] ?? null;

    if ($clienteEmail) {
        // Verificar si el correo electrónico existe en la base de datos
        $queryCheckEmail = "SELECT correo_cliente FROM tb_clientes WHERE correo_cliente = ?";
        $emailExists = Database::getRow($queryCheckEmail, [$clienteEmail]);

        if (!$emailExists) {
            $response['message'] = 'Correo electrónico no registrado.';
            echo json_encode($response);
            exit;
        }

        // Generar un código de recuperación
        $recoveryCode = generateRecoveryCode();
        $expirationDate = date('Y-m-d H:i:s', strtotime('+1 hour')); // El código vence en 1 hora

        // Actualizar el código de recuperación y la fecha de expiración en la base de datos
        $query = "UPDATE tb_clientes SET codigo_recuperacion = ?, fecha_expiracion_codigo = ? WHERE correo_cliente = ?";
        $values = [$recoveryCode, $expirationDate, $clienteEmail];
        if (!Database::executeRow($query, $values)) {
            $response['message'] = 'Error al actualizar el código de recuperación.';
            echo json_encode($response);
            exit;
        }

        // Enviar el código de recuperación por correo electrónico
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sportdevelopment7@gmail.com'; // Tu correo electrónico de Gmail
            $mail->Password = 'oatk qcui omre ihbn'; // Tu contraseña o contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('sportdevelopment7@gmail.com', 'Sport Development');
            $mail->addAddress($clienteEmail); // Correo electrónico del cliente

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Código de Recuperación de Contraseña';
            $mail->Body = "Tu código de recuperación es: <strong>$recoveryCode</strong>. Este código vence en 1 hora.";
            $mail->AltBody = "Tu código de recuperación es: $recoveryCode. Este código vence en 1 hora.";

            $mail->send();
            $response['status'] = true;
            $response['message'] = 'Código de recuperación enviado con éxito.';
        } catch (Exception $e) {
            $response['message'] = 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
        }

        echo json_encode($response);
        exit;
    }

    $response['message'] = 'Correo electrónico no proporcionado.';
    echo json_encode($response);
}
