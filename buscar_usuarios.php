<?php
require 'conection.php';

// Obtener el término de búsqueda
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Consulta SQL para buscar usuarios y su unidad correspondiente, ordenado alfabéticamente por nombre
$sql = "
    SELECT 
        usuarios.id, 
        usuarios.nombre, 
        usuarios.email, 
        usuarios.rol, 
        residentes.unidad 
    FROM 
        usuarios 
    LEFT JOIN 
        residentes 
    ON 
        usuarios.residente_id = residentes.id 
    WHERE 
        usuarios.nombre LIKE '%$query%' 
        OR usuarios.email LIKE '%$query%'
    ORDER BY 
        usuarios.nombre ASC"; // Ordenar alfabéticamente por nombre

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table class='table table-striped'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Correo Electrónico</th><th>Rol</th><th>Unidad</th><th>Acciones</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["nombre"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["rol"] . "</td>";
        echo "<td><a href='ver_pagos.php?unidad=" . urlencode($row["unidad"]) . "'>" . $row["unidad"] . "</a></td>";
        echo "<td><a href='editar_usuario.php?id=" . $row["id"] . "' class='btn btn-secondary'>Editar</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron usuarios.";
}

$conn->close();
?>
