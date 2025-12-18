<?php
// Mensaje para el resultado de la inserción
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message_content = $_POST['message'];

    // Validar campos
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_content)) {
        // Insertar los datos en la base de datos, estableciendo idEstado a 1 (pendiente)
        $stmt = $conx->prepare("INSERT INTO contacto (nombre, email, asunto, mensaje, idEstado) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message_content);

        if ($stmt->execute()) {
            // Si la inserción fue exitosa
            $message = '<div class="alert alert-success" role="alert">¡Mensaje enviado correctamente!</div>';
        } else {
            // Si hubo un error en la inserción
            $message = '<div class="alert alert-danger" role="alert">Hubo un error al enviar tu mensaje. Por favor, intenta de nuevo.</div>';
        }
        $stmt->close();
    } else {
        // Si algún campo está vacío
        $message = '<div class="alert alert-warning" role="alert">Por favor, llena todos los campos.</div>';
    }
}
?>

<!--================Breadcrumb Area =================-->
<section class="breadcrumb_area">
    <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background=""></div>
    <div class="container">
        <div class="page-cover text-start">
            <h2 class="page-cover-tittle">Contacta con nosotros</h2>
            <ol class="breadcrumb">
                <li><a href="index.html">Inicio</a></li>
                <li class="active">Contáctanos</li>
            </ol>
        </div>
    </div>
</section>
<!--================Breadcrumb Area =================-->

<!--================Contact Area =================-->
<section class="contact_area section_gap">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="contact_info">
                    <div class="info_item">
                        <i class="lnr lnr-home"></i>
                        <h6>Madrid, España</h6>
                        <p>Calle Ficticia, 123</p>
                    </div>
                    <div class="info_item">
                        <i class="lnr lnr-phone-handset"></i>
                        <h6><a href="tel:+34900123456">+34 900 123 456</a></h6>
                        <p>Lunes a Viernes de 9am a 6pm</p>
                    </div>
                    <div class="info_item">
                        <i class="lnr lnr-envelope"></i>
                        <h6><a href="mailto:contacto@tusitio.com">contacto@tusitio.com</a></h6>
                        <p>¡Envíanos tu consulta cuando quieras!</p>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Mostrar mensajes de éxito o error -->
                <?php if ($message): ?>
                    <div class="col-12">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de contacto -->
                <form class="row contact_form" action="" method="post" id="contactForm" novalidate="novalidate">
                    <!-- Campos de información personal -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Introduce tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Introduce tu email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Asunto</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Introduce el asunto" required>
                        </div>
                    </div>

                    <!-- Campos de mensaje -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea class="form-control" name="message" id="message" rows="6" placeholder="Introduce tu mensaje" required></textarea>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <div class="col-md-12 text-right">
                        <button type="submit" value="submit" class="btn theme_btn button_hover">Enviar mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<!--================Contact Area =================-->
