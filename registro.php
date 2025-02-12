<?php
require_once('./verificar_session.php');
verificarRol(['admin']);

// Procesamiento del formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Requerir archivo de conexión (ajusta la ruta si es necesario)
    require 'conection.php'; // Asegúrate de que 'conection.php' esté en el directorio correcto

    // Recibir y limpiar datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena']; // Contraseña del usuario
    $rol = $_POST['rol']; // Obtener el rol seleccionado
    $unidad = $_POST['unidad']; // Obtener la unidad/departamento

    // Hash de la contraseña
    $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Si se proporciona una unidad, obtener su residente_id
        $residente_id = null;
        if (!empty($unidad)) {
            $sql = "SELECT id FROM residentes WHERE unidad = '$unidad'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $residente_id = $row['id'];
            } else {
                // Si la unidad no existe, insertarla en la tabla de residentes
                $sql = "INSERT INTO residentes (unidad) VALUES ('$unidad')";
                if ($conn->query($sql) === TRUE) {
                    $residente_id = $conn->insert_id;
                } else {
                    throw new Exception("Error: " . $sql . "<br>" . $conn->error);
                }
            }
        }

        // Insertar el usuario en la tabla usuarios
        $sql = "INSERT INTO usuarios (nombre, email, contrasena, rol, residente_id) VALUES ('$nombre', '$email', '$hashed_password', '$rol', " . ($residente_id ? "'$residente_id'" : "NULL") . ")";
        if ($conn->query($sql) === TRUE) {
            // Confirmar la transacción
            $conn->commit();
            header('Location: index.php');
            exit;
        } else {
            throw new Exception("Error: " . $sql . "<br>" . $conn->error);
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo $e->getMessage();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema de Condominio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #0056b3;
            color: #fff;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #003d80;
        }

        footer {
            background-color: #0056b3;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .radio-group label {
            margin-bottom: 5px;
        }

        .radio-group input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>Registro de Usuario</h1>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="ver_pagos.php">Pagos</a></li>
            <li><a href="ver_gastos.php">Gastos Mensuales</a></li>
            <li><a href="registro.php">Registro</a></li>
            <li><a href="?logout=true">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>
<main>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <div>
            <span>Si es administrador no necesita llenar este campo</span>
            <label for="unidad">Unidad o Departamento:</label>
            <input type="text" id="unidad" name="unidad">
        </div>
        <div class="radio-group">
            <label>Rol:</label>
            <div>
                <input type="radio" id="user" name="rol" value="user" checked>
                <label for="user">Usuario</label>
            </div>
            <div>
                <input type="radio" id="admin" name="rol" value="admin">
                <label for="admin">Administrador</label>
            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            <button type="submit">Registrar</button>
        </div>
    </form>
</main>
<footer>
    <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
</footer>
</body>
</html>
