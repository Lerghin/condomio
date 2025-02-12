<?php
require_once('./verificar_session.php');
require 'conection.php';

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$cedula = $_POST['cedula'];
$unidad = $_POST['unidad'];
$telefono = $_POST['telefono'];
$residente_id = $_POST['residente-id'];

if (empty($residente_id)) {
    // Si no hay residente-id, es una inserción (agregar residente)
    // Verificar si la unidad es única antes de insertar
    $check_sql = "SELECT COUNT(*) as count FROM residentes WHERE unidad = '$unidad'";
    $check_result = $conn->query($check_sql);
    $row = $check_result->fetch_assoc();
    if ($row['count'] > 0) {
        // Mostrar alerta de error porque la unidad no es única
        echo "<script>alert('Ya está registrada la unidad');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    } else {
        // Insertar el residente
        $sql = "INSERT INTO residentes (nombre, apellido, cedula, unidad, telefono) VALUES ('$nombre','$apellido','$cedula', '$unidad', '$telefono')";

        if ($conn->query($sql) === TRUE) {
            // Redirigir según el rol del usuario
            if ($_SESSION['rol'] === 'user') {
                header("Location: index_user.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            echo "Error al agregar residente: " . $conn->error;
        }
    }
} else {
    // Si hay residente-id, es una actualización (editar residente)
    $sql = "UPDATE residentes SET nombre='$nombre', apellido='$apellido', cedula='$cedula', unidad='$unidad', telefono='$telefono' WHERE id=$residente_id";

    if ($conn->query($sql) === TRUE) {
        // Redirigir según el rol del usuario
        if ($_SESSION['rol'] === 'user') {
            header("Location: index_user.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        echo "Error al actualizar datos del residente: " . $conn->error;
    }
}

$conn->close();
?>
