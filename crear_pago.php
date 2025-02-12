<?php require_once('./verificar_session.php');
verificarRol(['admin']); ?>

<?php
// Inicializar variables para controlar la alerta de éxito y mensajes de error
$pagoExitoso = false;
$errorMensaje = "";

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conection.php';
    // Obtener los datos del formulario
    $fecha = $_POST['fecha'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cedula = $_POST['cedula'];
    $monto = $_POST['monto'];
    $referencia = $_POST['referencia'];
    $tipo = $_POST['tipo'];
    $unidad = $_POST['unidad'];
    $montoBS = $_POST['monto_bs'];

    // Verificar si la unidad existe
    $unidadExiste = false;
    $sqlVerificarUnidad = "SELECT * FROM residentes WHERE unidad = '$unidad'";
    $resultado = $conn->query($sqlVerificarUnidad);

    if ($resultado->num_rows > 0) {
        $unidadExiste = true;
    }

    if ($unidadExiste) {
        // Insertar el pago en la base de datos
        $sql = "INSERT INTO pagos (fecha, nombre, apellido, cedula, monto, monto_bs, referencia, tipo, unidad) VALUES ('$fecha', '$nombre', '$apellido', '$cedula', '$monto', '$montoBS', '$referencia', '$tipo', '$unidad')";

        if ($conn->query($sql) === TRUE) {
            // Obtener el ID del residente
            $sqlResidenteID = "SELECT id FROM residentes WHERE unidad = '$unidad'";
            $resultResidenteID = $conn->query($sqlResidenteID);
            if ($resultResidenteID->num_rows > 0) {
                $rowResidenteID = $resultResidenteID->fetch_assoc();
                $residenteID = $rowResidenteID['id'];

                // Actualizar la deuda del residente
                $sqlUpdateDeuda = "UPDATE deudas_residentes SET monto_deuda = monto_deuda - '$monto' WHERE residente_id = '$residenteID'";
                if ($conn->query($sqlUpdateDeuda) === TRUE) {
                    // Indicar que el pago fue exitoso
                    $pagoExitoso = true;
                } else {
                    $errorMensaje = "Error al actualizar la deuda del residente: " . $conn->error;
                }
            } else {
                $errorMensaje = "Error: No se encontró el residente asociado a la unidad.";
            }
        } else {
            $errorMensaje = "Error al crear el pago: " . $conn->error;
        }
    } else {
        // La unidad no existe
        $errorMensaje = "No existe la unidad, debe registrarla.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Agregar Pago - Sistema de Condominio</title>
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

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .form-container h2 {
            margin-bottom: 20px;
        }

        .form-container .form-group {
            margin-bottom: 15px;
        }

        .form-container .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-container .form-group input,
        .form-container .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container .form-group input[type="date"] {
            padding: 8px;
        }

        .form-container .btn-submit {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container .btn-submit:hover {
            background-color: #0056b3;
        }

        .alerta-error {
            color: red;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <header>
        <h1>Sistema de Condominio</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="ver_pagos.php">Pagos</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <h2>Agregar Pago</h2>
            <?php if ($errorMensaje) : ?>
                <p class="alerta-error"><?php echo $errorMensaje; ?></p>
            <?php endif; ?>
            <form action="crear_pago.php" method="POST">
                <div class="form-group">
                    <label for="fecha">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" required>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre del Depositante:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido del Depositante:</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
                <div class="form-group">
                    <label for="cedula">Cédula:</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                <div class="form-group">
                    <label for="monto">Monto en USD :</label>
                    <input type="number" id="monto" name="monto" step="0.01" required>
                </div>
                <div id="tasaDolar">
                <input type="hidden" id="montoBS" name="monto_bs" value="">
                   
                    <span>Tasa del dólar en bolívares: <span id="spanTasa">Cargando...</span></span>
                    <br>
                    <span>Monto en BS: <span id="spanMontoBS">0</span></span>
                </div>
                <div class="form-group">
                    <label for="referencia">Referencia:</label>
                    <input type="text" id="referencia" name="referencia" required>
                </div>
              

                <div class="form-group">
                    <label for="tipo">Tipo de Pago:</label>
                    <select name="tipo" id="tipo">
                        <option value="Bs Efectivo">Bolívares en Efectivo</option>
                        <option value="Bs Transferencia">Bolívares en Transferencia</option>
                        <option value="dolares en efectivo">Dólares en Efectivo</option>
                        <option value="dolares en Transferencia">Dólares en Transferencia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="unidad">Unidad:</label>
                    <input type="text" id="unidad" name="unidad" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Pago</button>
                <button type="button" class='btn btn-secondary'><a href="ver_pagos.php" style="text-decoration: none; color: inherit;">Cancelar</a></button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Verificar si el pago fue exitoso
        fetch('https://v6.exchangerate-api.com/v6/8ee293f7c8b83cfe4baa699c/latest/USD')
        .then(response => response.json())
        .then(data => {
            const tasaDolar = data.conversion_rates.VES;
            document.getElementById('spanTasa').textContent = tasaDolar.toFixed(2);

            // Calcular y mostrar el monto en bolívares según la tasa
            document.getElementById('monto').addEventListener('input', function() {
                const montoUSD = parseFloat(this.value);
                const montoBS = montoUSD * tasaDolar;
                document.getElementById('spanMontoBS').textContent = montoBS.toFixed(2);
                document.getElementById('montoBS').value = montoBS.toFixed(2); // Agregar esta línea para enviar el monto BS al formulario
            });
        })
        .catch(error => {
            console.error('Error al obtener la tasa de cambio:', error);
        });

        <?php if ($pagoExitoso) : ?>
            // Mostrar alerta y redirigir
            alert('Pago Registrado exitosamente');
            window.location.href = 'ver_pagos.php';
        <?php endif; ?>
    </script>
</body>

</html>
