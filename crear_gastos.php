<?php
require_once('./verificar_session.php');
verificarRol(['admin']);

// Inicializar variables
$pagoExitoso = false;
$errorMensaje = '';

// Definir tipos de archivos permitidos
$tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];

// Procesar el formulario para agregar recibo mensual
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conection.php';
    
    // Obtener datos del formulario
    $mes = $_POST['mes'];
    $anno = $_POST['anno'];
    $monto_total = $_POST['monto_total'];
    $monto_total_bs = $_POST['monto_total_bs'];

    // Obtener la cantidad de residentes
    $sql_residentes = "SELECT COUNT(*) AS total_residentes FROM residentes";
    $result_residentes = $conn->query($sql_residentes);
    $row_residentes = $result_residentes->fetch_assoc();
    $total_residentes = $row_residentes['total_residentes'];

    // Calcular la deuda por residente
    $deuda_por_residente = ($total_residentes > 0) ? $monto_total / $total_residentes : 0;

    // Manejo de la imagen principal
    $imagenRuta = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $nombreImagen = $_FILES['imagen']['name'];
        $tmpImagen = $_FILES['imagen']['tmp_name'];
        $tipoImagen = $_FILES['imagen']['type'];

        // Verificar el tipo de archivo
        if (!in_array($tipoImagen, $tiposPermitidos)) {
            $errorMensaje = 'Tipo de archivo no permitido. Solo se permiten imágenes JPEG, PNG y GIF.';
            exit;
        }

        // Definir carpeta destino
        $carpetaDestino = 'uploads/';
        $rutaImagen = $carpetaDestino . basename($nombreImagen);

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($tmpImagen, $rutaImagen)) {
            $imagenRuta = $rutaImagen;
        } else {
            $errorMensaje = 'Error al subir la imagen.';
            exit;
        }
    }

    // Insertar el recibo mensual en la base de datos usando consulta preparada
    $stmt = $conn->prepare("INSERT INTO recibos_mensuales (mes, anno, monto_total, monto_total_bs, imagen_ruta) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $mes, $anno, $monto_total, $monto_total_bs, $imagenRuta);

    if ($stmt->execute()) {
        $recibo_id = $stmt->insert_id;

        // Insertar detalles de gastos
        foreach ($_POST['descripcion_gasto'] as $key => $descripcion) {
            $monto = $_POST['monto_gasto'][$key];
            $imagenGastoRuta = '';
            
            // Manejo de la imagen de detalle de gasto
            if (isset($_FILES['imagen_gasto']) && isset($_FILES['imagen_gasto']['name'][$key]) && $_FILES['imagen_gasto']['error'][$key] == UPLOAD_ERR_OK) {
                $nombreImagenGasto = $_FILES['imagen_gasto']['name'][$key];
                $tmpImagenGasto = $_FILES['imagen_gasto']['tmp_name'][$key];
                $tipoImagenGasto = $_FILES['imagen_gasto']['type'][$key];

                // Verificar el tipo de archivo
                if (!in_array($tipoImagenGasto, $tiposPermitidos)) {
                    $errorMensaje = 'Tipo de archivo no permitido para la imagen del gasto. Solo se permiten imágenes JPEG, PNG y GIF.';
                    exit;
                }

                // Definir ruta de la imagen de gasto
                $rutaImagenGasto = 'uploads/gastos/' . basename($nombreImagenGasto);

                // Mover el archivo al directorio de destino
                if (move_uploaded_file($tmpImagenGasto, $rutaImagenGasto)) {
                    $imagenGastoRuta = $rutaImagenGasto;
                } else {
                    $errorMensaje = 'Error al subir la imagen del gasto.';
                    exit;
                }
            }

            // Insertar detalle de gasto usando consulta preparada
            $stmt_detalle = $conn->prepare("INSERT INTO detalles_gastos (recibo_id, descripcion, monto, imagen_ruta) VALUES (?, ?, ?, ?)");
            $stmt_detalle->bind_param("isds", $recibo_id, $descripcion, $monto, $imagenGastoRuta);
            $stmt_detalle->execute();
        }

        // Insertar deudas por residente
        $sql_residentes = "SELECT id FROM residentes";
        $result_residentes = $conn->query($sql_residentes);
        while ($row_residente = $result_residentes->fetch_assoc()) {
            $residente_id = $row_residente['id'];
            $stmt_deuda = $conn->prepare("INSERT INTO deudas_residentes (residente_id, recibo_id, monto_deuda) VALUES (?, ?, ?)");
            $stmt_deuda->bind_param("iid", $residente_id, $recibo_id, $deuda_por_residente);
            $stmt_deuda->execute();
        }

        // Mensaje de éxito
        $pagoExitoso = true;
    } else {
        $errorMensaje = "Error al agregar el recibo mensual: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibos Mensuales - Condominio</title>
    <style>
        /* Estilos mejorados */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 90%;
            margin: auto;
        }

        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .btn-remove {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }

        .btn-add {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-add:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Recibos Mensuales por Apartamento - Condominio</h1>

        <?php if ($pagoExitoso) : ?>
            <div class="alert alert-success">
                Recibo mensual agregado correctamente.
            </div>
        <?php elseif ($errorMensaje) : ?>
            <div class="alert alert-danger">
                <?php echo $errorMensaje; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Agregar Nuevo Recibo Mensual</legend>
                <div>
                    <label for="mes">Mes:</label>
                    <input type="text" id="mes" name="mes" required>
                </div>
                <div>
                    <label for="anno">Año:</label>
                    <input type="number" id="anno" name="anno" required>
                </div>
                <div>
                    <label for="monto_total">Monto Total en USD:</label>
                    <input type="number" id="monto_total" name="monto_total" step="0.01" required>
                </div>
                <div>
                    <label>Tasa de Cambio (USD a VES): </label>
                    <span id="spanTasa"></span>
                </div>
                <div>
                    <label for="monto_total_bs">Monto Total en VES:</label>
                    <span id="spanMontoBS"></span>
                    <input type="hidden" id="monto_total_bs" name="monto_total_bs" value="">
                </div>

                <fieldset>
                    <legend>Detalles de Gastos</legend>
                    <table id="tabla_detalles">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción del Gasto</th>
                                <th>Monto del Gasto</th>
                                <th>Imagen del Gasto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="date" name="fecha_gasto[]" required></td>
                                <td><input type="text" name="descripcion_gasto[]" placeholder="Descripción del gasto" required></td>
                                <td><input type="number" name="monto_gasto[]" step="0.01" placeholder="Monto del gasto" required></td>
                                <td><input type="file" name="imagen_gasto[]"></td>
                                <td><button type="button" class="btn-remove" onclick="eliminarFila(this)">Eliminar</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn-add" onclick="agregarFila()">Agregar Gasto</button>
                </fieldset>
            </fieldset>

            <button type="submit" class="btn-submit">Agregar Recibo Mensual</button>
        </form>
        <button type="button" class='btn-submit'><a href="ver_gastos.php" style="text-decoration: none; color: inherit;">Cancelar</a></button>
    </div>

    <script>
        // Función para agregar una fila en la tabla de detalles de gastos
        function agregarFila() {
            const tabla = document.getElementById('tabla_detalles').getElementsByTagName('tbody')[0];
            const fila = document.createElement('tr');
            fila.innerHTML = `<td><input type="date" name="fecha_gasto[]" required></td>
                              <td><input type="text" name="descripcion_gasto[]" placeholder="Descripción del gasto" required></td>
                              <td><input type="number" name="monto_gasto[]" step="0.01" placeholder="Monto del gasto" required></td>
                              <td><input type="file" name="imagen_gasto[]"></td>
                              <td><button type="button" class="btn-remove" onclick="eliminarFila(this)">Eliminar</button></td>`;
            tabla.appendChild(fila);
            calcularYValidarSumaMontos();
        }

        // Función para eliminar una fila de la tabla de detalles de gastos
        function eliminarFila(boton) {
            boton.parentNode.parentNode.remove();
            calcularYValidarSumaMontos();
        }

        // Función para calcular y validar la suma de los montos
        function calcularYValidarSumaMontos() {
            const montos = document.querySelectorAll('input[name="monto_gasto[]"]');
            let sumaMontos = 0;
            montos.forEach(input => {
                sumaMontos += parseFloat(input.value || 0); // Convertir a número y manejar vacíos como 0
            });

            const montoTotalUSD = parseFloat(document.getElementById('monto_total').value);
            const montoTotalBS = parseFloat(document.getElementById('monto_total_bs').value);
            const btnSubmit = document.querySelector('.btn-submit');

            if (sumaMontos > montoTotalUSD || sumaMontos > montoTotalBS) {
                alert('La suma de los montos de los detalles de gastos no puede ser mayor al monto total en USD o BS.');
                btnSubmit.disabled = true; // Desactivar el botón de submit
                return false;
            } else {
                btnSubmit.disabled = false; // Habilitar el botón de submit si la validación pasa
                return true;
            }
        }

        // Event listener para calcular la suma de montos cuando se cambia un valor
        document.getElementById('tabla_detalles').addEventListener('input', calcularYValidarSumaMontos);

        // Obtener la tasa de cambio del dólar a bolívares desde la API
        fetch('https://v6.exchangerate-api.com/v6/8ee293f7c8b83cfe4baa699c/latest/USD')
            .then(response => response.json())
            .then(data => {
                const tasaDolar = data.conversion_rates.VES;
                document.getElementById('spanTasa').textContent = tasaDolar.toFixed(2);

                // Calcular y mostrar el monto en bolívares según la tasa
                document.getElementById('monto_total').addEventListener('input', function() {
                    const montoUSD = parseFloat(this.value);
                    const montoBS = montoUSD * tasaDolar;
                    document.getElementById('spanMontoBS').textContent = montoBS.toFixed(2);
                    document.getElementById('monto_total_bs').value = montoBS.toFixed(2); // Agregar esta línea para enviar el monto BS al formulario
                    calcularYValidarSumaMontos(); // Validar automáticamente al cambiar el monto total
                });
            })
            .catch(error => {
                console.error('Error al obtener la tasa de cambio:', error);
            });

        // Validar antes de enviar el formulario
        document.querySelector('form').addEventListener('submit', function(event) {
            if (!calcularYValidarSumaMontos()) {
                event.preventDefault(); // Evitar que el formulario se envíe si la validación no pasa
            }
        });

        <?php if ($pagoExitoso) : ?>
            // Mostrar alerta y redirigir
            console.log('Recibo mensual agregado correctamente');
            window.location.href = 'ver_gastos.php';
        <?php endif; ?>
    </script>

</body>

</html>

