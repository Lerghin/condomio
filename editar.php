<?php require_once('./verificar_session.php');

// Eliminar la verificación de rol específica
verificarRol(['admin', 'user']);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Editar Residente</title>
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        main {
            width: 100%;
            max-width: 600px;
            /* Ajusta el ancho máximo según tu preferencia */
            margin: auto;
            padding: 20px;
            box-sizing: border-box;
            height: 85vh;
        }

        form {
            width: 100%;
        }

        form input,
        form button {
            width: calc(100% - 20px);
            margin-bottom: 10px;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 10px;
        }

        header {
            background-color: #0056b3;
            color: #fff;
            padding: 20px;
        }

        footer {
            background-color: #0056b3;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <header>
        <h1>Editar Residente</h1>
        <nav>
            <ul>
                <li><a id="inicio-btn">Inicio</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div>
            <h2>Editar Información del Residente</h2>
            <form id="residente-form" action="guardar.php" method="POST">
                <?php
                require 'conection.php';

                // Obtener el ID del residente desde la URL
                $residente_id = $_GET['id'];

                // Consulta SQL para obtener los datos del residente por su ID
                $sql = "SELECT id, nombre, apellido, cedula, unidad, telefono FROM residentes WHERE id = $residente_id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    // Mostrar los campos del formulario con los datos del residente
                    echo '<input type="hidden" id="residente-id" name="residente-id" value="' . $row['id'] . '">';
                    echo '<label for="nombre">Nombre:</label>';
                    echo '<input type="text" id="nombre" name="nombre" value="' . $row['nombre'] . '" required>';
                    echo '<label for="apellido">Apellido:</label>';
                    echo '<input type="text" id="apellido" name="apellido" value="' . $row['apellido'] . '" required>';
                    echo '<label for="cedula">Cédula:</label>';
                    echo '<input type="text" id="cedula" name="cedula" value="' . $row['cedula'] . '" required>';

                    // Si el usuario es de tipo 'user', deshabilita el campo 'unidad'
                    if ($_SESSION['rol'] === 'user') {
                        echo '<label for="unidad">Unidad:</label>';
                        echo '<input type="text" id="unidad" name="unidad" value="' . $row['unidad'] . '" required readonly>';
                        echo '<small>Este campo no puede ser modificado.</small>';
                        echo '<br>';
                    } else {
                        echo '<label for="unidad">Unidad:</label>';
                        echo '<input type="text" id="unidad" name="unidad" value="' . $row['unidad'] . '" required>';
                    }

                    echo '<label for="telefono">Teléfono:</label>';
                    echo '<input type="tel" id="telefono" name="telefono" value="' . $row['telefono'] . '" required>';
                } else {
                    echo "No se encontraron datos del residente.";
                }

                $conn->close();
                ?>
                <button type="submit" class='btn btn-success'>Guardar Cambios</button>
                <button type="button" id="cancelar-btn" class='btn btn-primary'>Cancelar</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>
    <script>
        // JavaScript para manejar la redirección del botón "Cancelar"
        document.addEventListener('DOMContentLoaded', function() {
            // Obtener el rol del usuario desde una variable JavaScript definida por PHP
            const userRole = "<?php echo $_SESSION['rol']; ?>";

            // Obtener el botón cancelar
            const cancelarBtn = document.getElementById('cancelar-btn');

            // Definir el comportamiento al hacer clic en cancelar
            cancelarBtn.addEventListener('click', function() {
                if (userRole === 'user') {
                    window.location.href = 'index_user.php';
                } else {
                    window.location.href = 'index.php';
                }
            });

            const inicioBtn = document.getElementById('inicio-btn');

            // Definir el comportamiento al hacer clic en cancelar
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