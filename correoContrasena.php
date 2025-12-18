<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Enviar correo con la nueva contraseña
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'conexion.php';

    $email = trim($_POST['email']);

    // Verificar si el correo existe en la base de datos
    $query = $conx->prepare("SELECT idUsuario FROM usuario WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        // Si el correo existe, generar una nueva contraseña aleatoria de 8 caracteres
        $nuevaContrasena = generateRandomPassword(8);
        $contrasenaEncriptada = password_hash($nuevaContrasena, PASSWORD_DEFAULT);

        // Obtener el ID de usuario
        $usuario = $result->fetch_assoc();
        $idUsuario = $usuario['idUsuario'];

        // Actualizar la contraseña en la base de datos
        $updateQuery = $conx->prepare("UPDATE usuario SET contrasena = ? WHERE idUsuario = ?");
        $updateQuery->bind_param("si", $contrasenaEncriptada, $idUsuario);
        $updateQuery->execute();

        if ($updateQuery->affected_rows === 1) {

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'nereaferluq@gmail.com';  
                $mail->Password = 'nyua njgj yahz qcsw'; 
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('nereaferluq@gmail.com', 'Recuperar Contraseña');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Nueva Contraseña';
                $mail->Body    = "<p>Hola, hemos generado una nueva contraseña para ti: <strong>$nuevaContrasena</strong></p>
                                  <p><strong>Importante cambiar la contraseña lo antes posible. Por su seguridad</strong></p>";

                $mail->send();

                // Redirigir a login con mensaje de éxito
                echo "<script>alert('Tu nueva contraseña ha sido enviada a tu correo.'); window.location.href='login.php';</script>";
                exit;
            } catch (Exception $e) {
                // Si falla el envío del correo, mantener la contraseña actual
                echo "<script>alert('Error al enviar el correo. Intenta de nuevo.'); window.location.href='recordarContrasena.php';</script>";
                exit;
            }
        } else {
            // Si no se pudo actualizar la contraseña
            echo "<script>alert('Error al actualizar la contraseña. Intenta de nuevo.'); window.location.href='recordarContrasena.php';</script>";
            exit;
        }
    } else {
        // Si el correo no está registrado
        echo "<script>alert('El correo electrónico no está registrado.'); window.location.href='recordarContrasena.php';</script>";
        exit;
    }
}

function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}
?>
