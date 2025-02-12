

<?php
// Conexión a la base de datos
require 'conection.php';

// Obtener el ID del residente desde la solicitud GET
$residente_id = $_GET['id'];

// Consulta SQL para obtener los datos del residente por su ID
$sql = "SELECT id, nombre, unidad, telefono FROM residentes WHERE id = $residente_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Extraer los datos del residente y devolverlos como JSON
    $row = $result->fetch_assoc();
    $residente = array(
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'apellido' => $row['apellido'],
        'cedula' => $row['cedula'],
        'unidad' => $row['unidad'],
        'telefono' => $row['telefono']
    );
    echo json_encode($residente);
} else {
    // Si no se encontraron resultados, devolver un objeto vacío o un mensaje de error
    echo json_encode(array()); // Devolver un objeto JSON vacío
}

$conn->close();
?>
