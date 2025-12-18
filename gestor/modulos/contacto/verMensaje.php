<?php
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtener ID del mensaje
$idContacto = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos del contacto
$query = $conx->prepare("SELECT c.nombre, c.email, c.mensaje, c.fechaCreacion, c.asunto, e.nombreEstado, c.idEstado as estado
                        FROM contacto c 
                        JOIN estado e ON c.idEstado = e.idEstado 
                        WHERE c.idContacto = ?");
$query->bind_param("i", $idContacto);
$query->execute();
$result = $query->get_result();
$contacto = $result->fetch_assoc();

// Si no existe redireccionar
if (!$contacto) {
    echo "<script>alert('Mensaje no encontrado'); window.location.href='index.php?mod=listaContactos';</script>";
    exit;
}

// Marcar como LEÍDO solo si el estado es PENDIENTE
if ($contacto['estado'] == 1) { // 1 es el ID para PENDIENTE
    $updateLeido = $conx->prepare("UPDATE contacto SET idEstado = (SELECT idEstado FROM estado WHERE nombreEstado = 'LEIDO') WHERE idContacto = ?");
    $updateLeido->bind_param("i", $idContacto);
    $updateLeido->execute();
}

// Procesar respuesta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $contacto['estado'] != 3) { // Si no está FINALIZADO
    $respuesta = trim($_POST['respuesta']);

    if (!empty($respuesta)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nereaferluq@gmail.com';  
            $mail->Password = 'nyua njgj yahz qcsw'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('nereafernandezluque@gmail.com', 'Soporte');
            $mail->addAddress($contacto['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Respuesta a tu consulta: ' . htmlspecialchars($contacto['asunto']); // Asunto actualizado
            $mail->Body    = "<p>Hola <strong>{$contacto['nombre']}</strong>,</p>
                              <p>Gracias por contactarnos. Esta es nuestra respuesta a tu mensaje:</p>
                              <blockquote style='border-left: 3px solid #ccc; padding-left: 10px; color: #555;'>{$contacto['mensaje']}</blockquote>
                              <p><strong>Respuesta:</strong><br>{$respuesta}</p>
                              <p>Un saludo,<br>El equipo de soporte.</p>";

            $mail->send();

            // Actualizar estado a FINALIZADO
            $updateFinalizado = $conx->prepare("UPDATE contacto SET idEstado = (SELECT idEstado FROM estado WHERE nombreEstado = 'FINALIZADO') WHERE idContacto = ?");
            $updateFinalizado->bind_param("i", $idContacto);
            $updateFinalizado->execute();

            echo "<script>alert('Respuesta enviada correctamente.'); window.location.href='index.php?mod=contacto';</script>";
            exit;

        } catch (Exception $e) {
            echo "<script>alert('Error al enviar el correo: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Debes escribir una respuesta.');</script>";
    }
}
?>

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-center">Ver y Responder Mensaje</h4>
            <div class="row">
                <!-- Datos del contacto -->
                <div class="col-md-6">
                    <form>
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($contacto['nombre']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($contacto['email']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i', strtotime($contacto['fechaCreacion'])); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Asunto</label> <!-- Nuevo campo Asunto -->
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($contacto['asunto']); ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Mensaje</label>
                            <textarea class="form-control" rows="6" disabled><?php echo htmlspecialchars($contacto['mensaje']); ?></textarea>
                        </div>
                    </form>
                </div>

                <!-- Formulario de respuesta -->
                <div class="col-md-6">
                    <?php if ($contacto['estado'] == 3) { ?>
                        <!-- Mostrar mensaje si el estado es FINALIZADO -->
                        <div class="alert alert-info">
                            <strong>ESTADO DE LA CONSULTA FINALIZADO</strong>
                        </div>
                        <a href="index.php?mod=contacto" class="btn btn-secondary btn-block mt-2">Volver</a>
                    <?php } else { ?>
                        <form method="POST">
                            <div class="form-group">
                                <label>Responder Mensaje</label>
                                <textarea name="respuesta" class="form-control" rows="10" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mt-3">Responder Mensaje</button>
                            <a href="index.php?mod=contacto" class="btn btn-secondary btn-block mt-2">Cancelar</a>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
