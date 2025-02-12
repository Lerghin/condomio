<?php require_once('./verificar_session.php');

?>
<!DOCTYPE html>
<html>

<head>
    <title>Ver Pagos - Sistema de Condominio</title>
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-input {
            padding: 10px;
            width: 200px;
            margin-right: 10px;
        }

        .date-input {
            padding: 10px;
            width: 150px;
            margin-right: 10px;
        }

        .btn-search {
            padding: 10px 20px;
        }

        .botonAdd {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .add {
            font-weight: bold;
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
    </style>
</head>

<body>
    <header style="text-align: center; font-weight: bold;">
        <h1>Sistema de Condominio</h1>
        <nav>
            <ul>
                <li><a href="index_user.php">Inicio</a></li>
                <li><a href="ver_pagos_unidad.php">Pagos</a></li>
                <li><a href="?logout=true">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Ver Pagos</h2>

        <!-- Formulario para buscar pagos por unidad y fecha específica -->
        <div class="search-form">
            <form action="ver_pagos.php" method="GET" onsubmit="return validarFormulario()">
                <input type="text" name="unidad" class="search-input" placeholder="Buscar por unidad" value="<?php echo htmlspecialchars(isset($_GET['unidad']) ? $_GET['unidad'] : ''); ?>">
                <input type="date" name="fecha" class="date-input" value="<?php echo htmlspecialchars(isset($_GET['fecha']) ? $_GET['fecha'] : ''); ?>">
                <button type="submit" class="btn btn-secondary btn-search">Buscar</button>
            </form>
        </div>

        <div class="debt-info">
            <?php
            // Conexión a la base de datos
            require 'conection.php';
            // Consultar la deuda actual si se proporciona una unidad
            $unidad = isset($_GET['unidad']) ? $conn->real_escape_string($_GET['unidad']) : '';

            if ($unidad) {
                // Consulta SQL para obtener la deuda total de la unidad específica
                $sql_deuda = "SELECT SUM(monto_deuda) as deuda_total 
                              FROM deudas_residentes 
                              JOIN residentes ON deudas_residentes.residente_id = residentes.id 
                              WHERE residentes.unidad = '$unidad'";

                $result_deuda = $conn->query($sql_deuda);

                if ($result_deuda->num_rows > 0) {
                    $row_deuda = $result_deuda->fetch_assoc();
                    $deuda_total = $row_deuda['deuda_total'];

                    if ($deuda_total > 0) {
                        echo "Deuda total para la unidad $unidad: " . number_format($deuda_total, 2) . " USD";
                    } else if ($deuda_total < 0) {
                        echo "La unidad $unidad tiene saldo a favor de: " . number_format(abs($deuda_total), 2) . " USD";
                    } else {
                        echo "La unidad $unidad no tiene deudas pendientes.";
                    }
                } else {
                    echo "No se encontraron registros de deuda para la unidad $unidad.";
                }
            } else {
                echo " ";
            }
            ?>
        </div>

        <div class="botonAdd" style="display: flex; justify-content:space-evenly;">
            <a class="btn btn-success add" href="crear_pago_user.php">Registrar Pago</a>
   
        </div>

        <div id="content">
            <?php
            // Consultar los pagos según la unidad y la fecha específica proporcionados
            $sql = "SELECT * FROM pagos WHERE 1=1"; // Condición siempre verdadera para facilitar agregar filtros

            if ($unidad) {
                $sql .= " AND unidad = '$unidad'";
            }

            $fecha = isset($_GET['fecha']) ? $conn->real_escape_string($_GET['fecha']) : '';

            if ($fecha) {
                $sql .= " AND fecha = '$fecha'";
            }

            $result = $conn->query($sql);

            // Mostrar los datos de los pagos
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Unidad</th><th>Fecha</th><th>Nombre</th><th>Apellido</th><th>Cédula</th><th>Monto en USD</th><th>Monto en BS</th><th>Referencia</th><th>Tipo</th><th>Acciones</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["unidad"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fecha"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["apellido"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cedula"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["monto"]) . " USD" . "</td>";
                    echo "<td>" . htmlspecialchars($row["monto_bs"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["referencia"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["tipo"]) . "</td>";
                    echo "<td>";

                    echo "<a href='generar_pdf.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-secondary'>Generar PDF</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No se encontraron pagos.";
            }

            $conn->close();
            ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>
  
</body>


</html>