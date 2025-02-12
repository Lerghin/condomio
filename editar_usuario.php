<?php
require_once('./verificar_session.php');
verificarRol(['admin']);
require 'conection.php';

// Obtener el ID del usuario desde la URL
$user_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;
    $rol = $_POST['rol'];

    // Construir consulta de actualización
    $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', rol='$rol'";
    if ($contrasena) {
        // Usar password_hash() para asegurar la contraseña
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql .= ", contrasena='$hashed_password'";
    }
    $sql .= " WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    // Obtener los datos actuales del usuario
    $sql = "SELECT * FROM usuarios WHERE id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "No se encontró el usuario.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Sistema de Condominio</title>
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
    <h1>Editar Usuario</h1>
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $user_id; ?>">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
        </div>
        <div>
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div>
            <label for="contrasena">Nueva Contraseña (dejar en blanco para no cambiar):</label>
            <input type="password" id="contrasena" name="contrasena">
        </div>
        <div class="radio-group">
            <label>Rol:</label>
            <div>
                <input type="radio" id="user" name="rol" value="user" <?php echo ($user['rol'] == 'user') ? 'checked' : ''; ?>>
                <label for="user">Usuario</label>
            </div>
            <div>
                <input type="radio" id="admin" name="rol" value="admin" <?php echo ($user['rol'] == 'admin') ? 'checked' : ''; ?>>
                <label for="admin">Administrador</label>
            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            <button type="submit">Actualizar</button>
        </div>
    </form>
</main>
<footer>
    <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
</footer>
</body>
</html>

