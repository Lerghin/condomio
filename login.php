<?php
session_start(); // Iniciar sesión PHP

// Verificar si el usuario ya está autenticado y redirigir si es necesario
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
}

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conection.php';
    // Validar el usuario (debes mejorar la seguridad y lógica de autenticación)
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $contrasena = trim($_POST['contrasena']);

    // Consulta para verificar el usuario
    $stmt = $conn->prepare("SELECT id, nombre, contrasena, rol, residente_id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuario encontrado, verificar contraseña
        $row = $result->fetch_assoc();
        $stored_password = $row['contrasena'];

        // Verificación de la contraseña usando password_verify
        if (password_verify($contrasena, $stored_password)) {
            // Iniciar sesión y redirigir al usuario
            $_SESSION['loggedin'] = true;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['email'] = $email;
            $_SESSION['rol'] = $row['rol']; // Almacenar el rol del usuario
            
            if ($row['rol'] == 'admin') {
                header('Location: index.php');
            } elseif ($row['rol'] == 'user') {
                // Almacenar residente_id en la sesión para los usuarios con rol 'user'
                $_SESSION['residente_id'] = $row['residente_id'];
                header('Location: index_user.php?residente_id=' . $row['residente_id']);
            }
            exit;
        } else {
            $error = "Nombre de usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Nombre de usuario o contraseña incorrectos.";
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema de Condominio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f5f9;
            background-image: url("https://lirp.cdn-website.com/20adca72/dms3rep/multi/opt/construccion+de+edificios+en+monterrey-960w.jpg");
        }

        .container {
            max-width: 400px;
            margin: 20px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Iniciar sesión</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="text" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>
            <div style="display: flex; justify-content:center">
                <button type="submit" class="btn btn-primary btn-block">Iniciar sesión</button>
            </div>
        </form>
    </div>

    <!-- Scripts de Bootstrap (jQuery y Popper.js necesarios para Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

