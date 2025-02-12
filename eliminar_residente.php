<?php
// Conexión a la base de datos
require 'conection.php';

// Obtener el ID del residente a eliminar
$residente_id = $_POST['id'];

// Consulta SQL para eliminar al residente
$sql = "DELETE FROM residentes WHERE id = $residente_id";

if ($conn->query($sql) === TRUE) {
    echo "Residente eliminado correctamente.";
} else {
    echo "Error al eliminar residente: " . $conn->error;
}

$conn->close();
?>
