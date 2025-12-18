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

// Obtener el ID de la compra desde el parámetro GET
$idCompra = $_GET['idCompra'] ?? null;

if (!$idCompra) {
    die("ID de compra no proporcionado.");
}

// Obtener los datos de la compra
$sql = "
    SELECT c.idCompra, c.numeroEntradas, c.importeTotal, c.fecha, c.hora, u.email, u.nombre AS nombreCliente, u.apellidos, 
           e.nombre AS nombreEspectaculo, e.duracion, s.numeroSala, f.nombre AS foto
    FROM compra c
    JOIN usuario u ON c.idUsuario = u.idUsuario
    JOIN espectaculo e ON c.idEspectaculo = e.idEspectaculo
    JOIN sala s ON s.idSala = e.sala
    LEFT JOIN foto f ON f.idEspectaculo = e.idEspectaculo
    WHERE c.idCompra = ?
";
$stmt = $conx->prepare($sql);
$stmt->bind_param("i", $idCompra);
$stmt->execute();
$datosCompra = $stmt->get_result()->fetch_assoc();

if (!$datosCompra) {
    die("Compra no encontrada.");
}

// Extraer los datos
$correoCliente = $datosCompra['email'];
$nombreCompleto = $datosCompra['nombreCliente'] . ' ' . $datosCompra['apellidos'];
$nombreEspectaculo = $datosCompra['nombreEspectaculo'];
$duracion = $datosCompra['duracion'];
$sala = $datosCompra['numeroSala'];
$fecha = $datosCompra['fecha'];
$hora = $datosCompra['hora'];
$numeroEntradas = $datosCompra['numeroEntradas'];
$importeTotal = $datosCompra['importeTotal'];
$portada = $datosCompra['foto']; // Ruta de la imagen de la portada

// --------- Generar QR en memoria y guardarlo como archivo PNG ---------
$qrData = "Compra #$idCompra\nCliente: $nombreCompleto\nEspectáculo: $nombreEspectaculo\nFecha: $fecha\nHora: $hora\nSala: $sala";
$tmpQR = tempnam(sys_get_temp_dir(), 'qr') . '.png';  // Agregar la extensión .png

QRcode::png($qrData, $tmpQR, QR_ECLEVEL_L, 4);  // Tamaño de QR ajustado a 4

// --------- Generar PDF en memoria ----------

// 1. Definir UTF-8 en el PDF
$pdf = new FPDF();
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// Asegúrate de usar una fuente que soporte UTF-8
$pdf->SetFont('Arial', 'B', 16);

// Define el texto en color negro
$pdf->SetTextColor(0, 0, 0);

// Título en la parte superior, antes de la imagen
$pdf->Cell(0, 10, utf8_decode('Confirmación de Compra'), 0, 1, 'C');  // UTF-8 para título
$pdf->Ln(10);

// 2. Controlar el tamaño de la imagen (evitar que cubra el texto)
$portadaPath = 'public/' . $portada;
if (file_exists($portadaPath)) {
    // Redimensionar la imagen para que se ajuste correctamente a la página
    $pdf->Image($portadaPath, 10, 30, 190, 80); // 190 mm de ancho, 60 mm de alto
}

// Espaciado después de la imagen
$pdf->Ln(90);  // Esto asegura que el contenido no se solape con la imagen de portada

// Información de la compra
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Nombre: " . utf8_decode($nombreCompleto), 0, 1);  // UTF-8 para texto
$pdf->Cell(0, 10, "Espectaculo: " . utf8_decode($nombreEspectaculo), 0, 1);  // Ahora "Espectáculo" debería mostrarse correctamente
$pdf->Cell(0, 10, "Fecha: " . utf8_decode($fecha), 0, 1);
$pdf->Cell(0, 10, "Hora: " . utf8_decode($hora), 0, 1);
$pdf->Cell(0, 10, "Sala: " . utf8_decode($sala), 0, 1);
$pdf->Cell(0, 10, "Entradas: " . utf8_decode($numeroEntradas), 0, 1);

// --------- Insertar QR encima del recuadro del importe ----------
// Ajustar la posición del QR a la parte superior derecha de la página
$pdf->SetXY(140, 120);  // Ajustar Y manualmente a 40, para que esté cerca de la parte superior del PDF
$pdf->Image($tmpQR, $pdf->GetX(), $pdf->GetY(), 40, 40);  // Devolvemos el tamaño del QR original

// Recuadro con el importe total
$pdf->Ln(70);  // Espacio para evitar solapamiento con el QR
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255); // Color de fondo para el recuadro
$pdf->Cell(0, 10, utf8_decode("Importe total: " . number_format($importeTotal, 2) . " Euros"), 0, 1, 'C', true);

// Eliminar el archivo temporal después de usarlo
unlink($tmpQR);

// --------- Enviar PDF directamente al navegador ---------
$pdf->Output('I', 'compra_' . $idCompra . '.pdf');
?>
