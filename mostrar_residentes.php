<?php require_once('./verificar_session.php'); ?>
<?php
 require 'conection.php';
 

// Consulta SQL para obtener la lista de residentes
$sql = "SELECT * FROM residentes";
$result = $conn->query($sql);

// Mostrar los datos de los residentes en forma de tabla HTML
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Unidad</th><th>Teléfono</th><th>Acciones</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["nombre"]."</td><td>".$row["unidad"]."</td><td>".$row["telefono"]."</td><td><a href='javascript:void(0);' onclick='showModalEdit(".$row["id"].")'>Editar</a> | <a href='javascript:void(0);' onclick='eliminarResidente(".$row["id"].")'>Eliminar</a></td></tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

$conn->close();
?>
