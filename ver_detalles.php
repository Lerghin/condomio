<?php require_once('./verificar_session.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Recibo Mensual - Sistema de Condominio</title>
    <link rel="stylesheet" type="text/css" href="./styles.css">
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
            text-align: center;
            font-weight: bold;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
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

        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .recibo-details {
            margin-bottom: 20px;
        }

        .recibo-details h2 {
            color: #0056b3;
            margin-bottom: 10px;
        }

        .detalle {
            margin-bottom: 10px;
        }

        .btn-print {
            background-color: #0056b3;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-print:hover {
            background-color: #003d80;
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

        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <header style="text-align: center; font-weight: bold;">
        <h1>Sistema de Condominio</h1>
        <nav style="display:flex; justify-content:start;">
            <ul>
            <li><a id='inicio-btn' href="#">Inicio</a></li>
                <?php if ($_SESSION['rol'] !== 'user') : ?>

                    <li><a href="ver_pagos.php">Pagos</a></li>
                    <li><a href="registro.php">Registro</a></li>

                <?php endif; ?>
                <li><a href="ver_gastos.php">Gastos Mensuales</a></li>
                <li><a href="?logout=true">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <div class="recibo-details">
                <h2>Detalles del Recibo Mensual</h2>
                <?php
                require 'conection.php';
                // Obtener el ID del recibo desde la URL
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $recibo_id = $_GET['id'];

                    // Consulta SQL para obtener los detalles del recibo
                    $sql_recibo = "SELECT * FROM recibos_mensuales WHERE id = $recibo_id";
                    $result_recibo = $conn->query($sql_recibo);

                    if ($result_recibo->num_rows > 0) {
                        $row_recibo = $result_recibo->fetch_assoc();
                        echo "<div class='detalle'><strong>ID:</strong> " . $row_recibo['id'] . "</div>";
                        echo "<div class='detalle'><strong>Mes:</strong> " . $row_recibo['mes'] . "</div>";
                        echo "<div class='detalle'><strong>Año:</strong> " . $row_recibo['anno'] . "</div>";
                        echo "<div class='detalle'><strong>Monto Total:</strong> " . $row_recibo['monto_total'] . "</div>";

                        // Mostrar imagen del recibo
                        if (!empty($row_recibo['imagen_ruta'])) {
                            echo "<div class='detalle'><strong>Imagen del Recibo:</strong></div>";
                            echo "<img src='" . $row_recibo['imagen_ruta'] . "' alt='Imagen del Recibo'>";
                        }

                        // Consulta SQL para obtener los detalles de gastos asociados al recibo
                        $sql_gastos = "SELECT * FROM detalles_gastos WHERE recibo_id = $recibo_id";
                        $result_gastos = $conn->query($sql_gastos);

                        if ($result_gastos->num_rows > 0) {
                            echo "<h3>Detalles de Gastos</h3>";
                            echo "<table>";
                            echo "<tr><th>ID</th><th>Descripción</th><th>Monto</th><th>Imagen</th></tr>";
                            while ($row_gastos = $result_gastos->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row_gastos["id"] . "</td>";
                                echo "<td>" . $row_gastos["descripcion"] . "</td>";
                                echo "<td>" . $row_gastos["monto"] . "</td>";

                                // Mostrar imagen del gasto
                                if (!empty($row_gastos['imagen_ruta'])) {
                                    echo "<td><img src='" . $row_gastos['imagen_ruta'] . "' alt='Imagen del Gasto'></td>";
                                } else {
                                    echo "<td>No hay imagen</td>";
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No se encontraron detalles de gastos para este recibo.";
                        }
                    } else {
                        echo "No se encontró el recibo solicitado.";
                    }
                } else {
                    echo "Parámetro de ID no válido.";
                }

                $conn->close();
                ?>
            </div>

            <!-- Botón para imprimir recibo -->
            <a class="btn btn-primary" href="imprimir_recibo.php?id=<?php echo $_GET['id']; ?>" target="_blank">Imprimir Recibo en PDF</a>
            <button type="button" class='btn btn-secondary'><a href="ver_gastos.php" style="text-decoration: none; color: inherit;">Atras</a></button>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>

    <script>
        // JavaScript para manejar la redirección del botón "Inicio"
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el rol del usuario desde una variable JavaScript definida por PHP
            const userRole = "<?php echo $_SESSION['rol']; ?>";

            // Obtener el botón "Inicio"
            const inicioBtn = document.getElementById('inicio-btn');

            // Definir el comportamiento al hacer clic en el botón "Inicio"
            inicioBtn.addEventListener('click', function() {
                if (userRole === 'user') {
                    window.location.href = 'index_user.php';
                } else {
                    window.location.href = 'index.php';
                }
            });
        });
    </script>

</body>

</html>