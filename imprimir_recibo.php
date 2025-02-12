<?php
require_once('./verificar_session.php');
require('./fpdf/fpdf.php'); // Incluir la biblioteca FPDF
require 'conection.php'; // Conexión a la base de datos

class PDF extends FPDF
{
    // Cabecera del PDF
    function Header()
    {
        // Logo y título
        $this->Image('logo.png', 10, 6, 30); // Añadir un logo (opcional)
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Recibo Mensual', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pie de página del PDF
    function Footer()
    {
        // Número de página
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Función para imprimir los detalles del recibo y gastos
    function DetallesRecibo($conn, $recibo_id)
    {
        // Consulta SQL para obtener los detalles del recibo
        $sql_recibo = "SELECT * FROM recibos_mensuales WHERE id = $recibo_id";
        $result_recibo = $conn->query($sql_recibo);

        if ($result_recibo->num_rows > 0) {
            $row_recibo = $result_recibo->fetch_assoc();
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Informacion del Recibo', 0, 1, 'L');
            $this->SetFont('Arial', '', 12);
            
            // Información del recibo en una sola línea
            $this->Cell(30, 10, 'ID del Recibo: ' . $row_recibo['id'] . ' | Mes: ' . $row_recibo['mes'] . ' | Anio: ' . $row_recibo['anno'] . ' | Monto Total: ' . number_format($row_recibo['monto_total'], 2, ',', '.') . ' USD', 0, 1);
            
            $this->Ln(10);

            // Detalles de gastos
            $this->Cell(0, 10, 'Detalles de Gastos', 0, 1, 'L');
            $sql_gastos = "SELECT * FROM detalles_gastos WHERE recibo_id = $recibo_id";
            $result_gastos = $conn->query($sql_gastos);

            if ($result_gastos->num_rows > 0) {
                // Encabezados de la tabla
                $this->SetFillColor(0, 86, 179); // Color de fondo azul para encabezados
                $this->SetTextColor(255); // Color de texto blanco
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(140, 10, 'Descripcion', 1, 0, 'C', true);
                $this->Cell(40, 10, 'Monto', 1, 1, 'C', true);

                // Filas de la tabla
                $this->SetFillColor(224, 235, 255); // Restaurar color de fondo original
                $this->SetTextColor(0); // Restaurar color de texto original
                $this->SetFont('Arial', '', 12);
                $fill = false;

                while ($row_gastos = $result_gastos->fetch_assoc()) {
                    $this->Cell(140, 10, $row_gastos["descripcion"], 1, 0, 'L', $fill);
                    $this->Cell(40, 10, number_format($row_gastos["monto"], 2, ',', '.') . ' USD', 1, 1, 'R', $fill);
                    $fill = !$fill;
                }
            } else {
                $this->Cell(0, 10, 'No se encontraron detalles de gastos para este recibo.', 0, 1);
            }
        } else {
            $this->Cell(0, 10, 'No se encontró el recibo solicitado.', 0, 1);
        }
    }
}

// Creación del objeto PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->DetallesRecibo($conn, $_GET['id']); // Llama a la función para imprimir detalles de recibo y gastos
$pdf->Output();
$conn->close();
?>
