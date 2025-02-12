<?php 
require_once('./verificar_session.php'); 
require 'conection.php';

// Función para validar y obtener el ID del pago
function getPaymentId() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        return intval($_GET['id']);
    }
    return null;
}

// Función para obtener los detalles del pago y generar el PDF
function generatePaymentPDF($id, $conn) {
    // Consultar el pago en la base de datos
    $sql = "SELECT * FROM pagos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Incluir la biblioteca FPDF
        require('fpdf/fpdf.php');

        // Crear un nuevo documento PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Detalles del pago
        $row = $result->fetch_assoc();
        $unidad = $row['unidad'];
        $fecha = $row['fecha'];
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $cedula = $row['cedula'];
        $monto = $row['monto'];
        $montoBs = $row['monto_bs'];
        $referencia = $row['referencia'];
        $tipo = $row['tipo'];

        // Estilos personalizados para el PDF
        $pdf->SetFillColor(92, 148, 219); // Azul
        $pdf->SetTextColor(255, 255, 255); // Blanco
        $pdf->SetFont('Arial', 'B', 16);

        // Título del PDF
        $pdf->Cell(0, 10, "Detalles del Pago", 0, 1, 'C', true);
        $pdf->Ln(5);

        // Contenido del PDF
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetFillColor(233, 240, 248); // Azul claro para fondo de celdas
        $pdf->SetTextColor(0, 0, 0); // Negro para texto

        $pdf->Cell(40, 10, "Unidad:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($unidad), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Fecha:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($fecha), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Nombre:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode("$nombre $apellido"), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Cedula:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($cedula), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Monto:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($monto), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Monto en Bs:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($montoBs), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Referencia:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($referencia), 0, 1, 'L', true);

        $pdf->Cell(40, 10, "Tipo de Pago:", 0, 0, 'L');
        $pdf->Cell(0, 10, utf8_decode($tipo), 0, 1, 'L', true);

        // Salida del PDF
        $pdf->Output();
    } else {
        echo "No se encontró el pago.";
    }
}

// Verificar la conexión y ejecutar la generación del PDF si se proporciona el ID del pago
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$idPago = getPaymentId();
if ($idPago !== null) {
    generatePaymentPDF($idPago, $conn);
} else {
    echo "ID de pago no válido.";
}

$conn->close();
?>
