<?php
// Conexión a la base de datos
require 'conection.php';

// Consulta para obtener todos los recibos mensuales o filtrar por mes si se proporciona
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';

if ($mes) {
    // Consulta SQL para obtener los recibos mensuales por mes
    $sql = "SELECT * FROM recibos_mensuales WHERE mes LIKE '%$mes%'";
} else {
    // Consulta SQL para obtener todos los recibos mensuales
    $sql = "SELECT * FROM recibos_mensuales";
}

$result = $conn->query($sql);

// Mostrar los datos de los recibos mensuales
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Mes</th><th>Año</th><th>Monto Total</th><th>Detalles</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["mes"] . "</td>";
        echo "<td>" . $row["anno"] . "</td>";
        echo "<td>" . $row["monto_total"] . "USD" ."</td>";
        echo "<td><a href='ver_detalles.php?id=" . $row["id"] . "' class='btn btn-info'>Ver Detalles</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron recibos mensuales.";
}

$conn->close();
?>
