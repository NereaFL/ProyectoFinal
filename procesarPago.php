<?php
require 'FPDF/fpdf.php';
require 'phpqrcode/qrlib.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
require 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$idUsuario = $_SESSION['usuario'];
$idEspectaculo = $_POST['idEspectaculo'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora = $_POST['hora'] ?? null;
$numeroEntradas = $_POST['numeroEntradas'] ?? null;
$importeTotal = $_POST['importeTotal'] ?? null;
$paypalTransactionId = $_POST['paypalTransactionId'] ?? null;

if (!$idEspectaculo || !$fecha || !$hora || !$numeroEntradas || !$importeTotal || !$paypalTransactionId) {
    die("Faltan datos de la compra o transacción PayPal.");
}

if ($importeTotal < 0.50) {
    die("El monto total debe ser mayor o igual a 0.50 €.");
}

// Validar transacción con PayPal
$clientId = 'AdcjPmQnjCXJZT7Ye5bBYA6ib4Eh87K0jOPXZk4p2ChexfMvdJ3lsV-OmobiHpFsA3zEz6cJ-O2GaNJ6';
$secret = 'ED4878o7R8Caq_zSPrLpAar4Y5io2fPB4p6izlcQ8pZpheICGYkcF_hRkNCj2eZVfTOswfThu0-HTFRh';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json", "Accept-Language: en_US"]);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) die("Error al autenticar con PayPal");

$data = json_decode($response, true);
$accessToken = $data['access_token'] ?? null;
if (!$accessToken) die("No se pudo obtener el token de acceso");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$paypalTransactionId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);

$response = curl_exec($ch);
curl_close($ch);
$orderData = json_decode($response, true);

if (!isset($orderData['status']) || $orderData['status'] !== 'COMPLETED') {
    die("La transacción de PayPal no está completada.");
}

$amountPaid = $orderData['purchase_units'][0]['amount']['value'];
if (floatval($amountPaid) !== floatval($importeTotal)) {
    die("El importe pagado no coincide.");
}

// Verificar entradas disponibles
$stmt = $conx->prepare("SELECT capacidad FROM sala WHERE idSala = (SELECT sala FROM espectaculo WHERE idEspectaculo = ?)");
$stmt->bind_param("i", $idEspectaculo);
$stmt->execute();
$capacidad = $stmt->get_result()->fetch_assoc()['capacidad'] ?? 0;

$stmt = $conx->prepare("SELECT SUM(numeroEntradas) AS total FROM compra WHERE idEspectaculo = ? AND fecha = ? AND hora = ?");
$stmt->bind_param("iss", $idEspectaculo, $fecha, $hora);
$stmt->execute();
$vendidas = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

if (($vendidas + $numeroEntradas) > $capacidad) {
    die("Entradas agotadas para esta función.");
}

// Insertar la compra
$sql = "INSERT INTO compra (idUsuario, idEspectaculo, fecha, hora, numeroEntradas, importeTotal)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conx->prepare($sql);
$stmt->bind_param("iissid", $idUsuario, $idEspectaculo, $fecha, $hora, $numeroEntradas, $importeTotal);
$stmt->execute();
$idCompra = $stmt->insert_id;

// Obtener datos del cliente y espectáculo
$sql = "
    SELECT u.email, u.nombre as nombreCliente, u.apellidos, e.nombre AS nombreEspectaculo, e.duracion, s.numeroSala, f.nombre
    FROM usuario u
    JOIN espectaculo e ON e.idEspectaculo = ?
    JOIN sala s ON s.idSala = e.sala
    LEFT JOIN foto f ON f.idEspectaculo = e.idEspectaculo AND f.portada = 1
    WHERE u.idUsuario = ?
";
$stmt = $conx->prepare($sql);
$stmt->bind_param("ii", $idEspectaculo, $_SESSION['usuario']);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

$correoCliente = $datos['email'];
$nombreCompleto = $datos['nombreCliente'] . ' ' . $datos['apellidos'];
$nombreEspectaculo = $datos['nombreEspectaculo'];
$duracion = $datos['duracion'];
$sala = $datos['numeroSala'];
$portada = $datos['nombre'];

$qrData = "Compra #$idCompra\nCliente: $nombreCompleto\nEspectáculo: $nombreEspectaculo\nFecha: $fecha\nHora: $hora\nSala: $sala";
$tmpQR = tempnam(sys_get_temp_dir(), 'qr') . '.png';
QRcode::png($qrData, $tmpQR, QR_ECLEVEL_L, 4);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(0, 0, 0);

$portadaPath = 'public/' . $portada;
if (file_exists($portadaPath)) {
    $pdf->Image($portadaPath, 10, 10, 190, 80);
}

$pdf->Ln(80);
$pdf->Cell(0, 10, utf8_decode('Confirmación de Compra'), 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Nombre: " . utf8_decode($nombreCompleto), 0, 1);
$pdf->Cell(0, 10, "Espectaculo: " . utf8_decode($nombreEspectaculo), 0, 1);
$pdf->Cell(0, 10, "Fecha: " . utf8_decode($fecha), 0, 1);
$pdf->Cell(0, 10, "Hora: " . utf8_decode($hora), 0, 1);
$pdf->Cell(0, 10, "Sala: " . utf8_decode($sala), 0, 1);
$pdf->Cell(0, 10, "Entradas: " . utf8_decode($numeroEntradas), 0, 1);
$pdf->SetXY(140, 120);
$pdf->Image($tmpQR, $pdf->GetX(), $pdf->GetY(), 40, 40);
$pdf->Ln(50);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(0, 10, utf8_decode("Importe total: " . number_format($importeTotal, 2) . " Euros"), 0, 1, 'C', true);
unlink($tmpQR);
$pdfString = $pdf->Output('', 'S');

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nereaferluq@gmail.com';
    $mail->Password = 'nyua njgj yahz qcsw';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('nereaferluq@gmail.com', 'Teatro');
    $mail->CharSet = 'UTF-8';
    $mail->addAddress($correoCliente, $nombreCompleto);
    $mail->isHTML(true);
    $mail->Subject = 'Confirmación de tu compra';
    $mail->Body = "<p>Gracias por tu compra, <strong>$nombreCompleto</strong>.<br>Adjuntamos el PDF con los detalles de tu reserva.</p>";
    $mail->addStringAttachment($pdfString, 'entradas.pdf');
    $mail->send();
    header("Location: index.php?mod=graciasCompra&idCompra=$idCompra");
    exit;
} catch (Exception $e) {
    die("Error al enviar el email: {$mail->ErrorInfo}");
}
?>
