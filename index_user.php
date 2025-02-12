
<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario está autenticado y tiene rol 'user'
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] !== 'user') {
    header('Location: login.php');
    exit;
}

require 'conection.php';

// Obtener residente_id de la sesión o de la URL
$residente_id = isset($_SESSION['residente_id']) ? intval($_SESSION['residente_id']) : (isset($_GET['residente_id']) ? intval($_GET['residente_id']) : 0);

// Consulta para obtener los datos del residente
if ($residente_id > 0) {
    $stmt = $conn->prepare("SELECT id, nombre, apellido, unidad, telefono FROM residentes WHERE id = ?");
    $stmt->bind_param("i", $residente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $residente = $result->fetch_assoc();
    } else {
        $residente = null; // O manejar el caso en que no se encuentran datos
    }
    $stmt->close();
} else {
    $residente = null; // O manejar el caso en que no se proporciona residente_id
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Usuario - Sistema de Condominio</title>
    <link rel="stylesheet" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f5f9;
        }

        header {
            background-color: #0056b3;
            color: #fff;
            padding: 20px;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav li {
            margin-right: 20px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #0056b3;
        }

        main {
            flex-grow: 1;
            padding: 20px;
        }

        footer {
            background-color: #0056b3;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #0056b3;
            color: #fff;
        }

        td {
            background-color: #e9f0f7;
        }

        .btn-success {
            color: #fff;
            background-color: #28a745;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .debt-info {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #e9f0f7;
            text-align: center;
            font-size: 1.2em;
            color: #0056b3;
            font-weight: bold;
        }

        .table-container {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <header style="text-align: center; font-weight: bold;">
        <h1>Sistema de Condominio</h1>
        <nav>
            <ul>
                <li><a href="index_user.php">Inicio</a></li>
                <li>  <a href="ver_pagos_unidad.php?unidad=<?php echo urlencode($residente['unidad']); ?>">Pagos</a></li>
                <li><a href="ver_gastos.php">Gastos Mensuales</a></li>
              
            </ul>
        </nav>
    </header>
    <main>
        <h2>Bienvenido, <?php echo htmlspecialchars($residente['nombre']); ?></h2>

        <div class="table-container">
            <?php if ($residente): ?>
                <h3>Detalles del Residente</h3>
                <table>
                   
                    <tr>
                        <th>Nombre</th>
                        <td><?php echo htmlspecialchars($residente['nombre']); ?></td>
                    </tr>
                    <tr>
                        <th>Apellido</th>
                        <td><?php echo htmlspecialchars($residente['apellido']); ?></td>
                    </tr>
                    <tr>
                        <th>Unidad</th>
                        <td><a href="ver_pagos_unidad.php?unidad=<?php echo urlencode($residente['unidad']); ?>"><?php echo htmlspecialchars($residente['unidad']); ?></a></td>
                    </tr>
                    <tr>
                        <th>Teléfono</th>
                        <td><?php echo htmlspecialchars($residente['telefono']); ?></td>
                    </tr>
                </table>
                <div class="mt-3">
                    <a class="btn btn-success" href="editar_residente.php?id=<?php echo $residente['id']; ?>">Editar</a>
                </div>
            <?php else: ?>
                <p>No se encontraron datos del residente.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>
</body>

</html>
