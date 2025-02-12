<?php require_once('./verificar_session.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Gastos Mensuales - Sistema de Condominio</title>
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
            width: 300px;
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
    </style>
</head>

<body>
<header style="text-align: center; font-weight: bold;">
        <h1>Sistema de Condominio</h1>
        <nav style="display:flex; justify-content:start;">
            <ul>
                <li><a id='inicio-btn' href="#">Inicio</a></li>
                <?php if ($_SESSION['rol'] !== 'user'): ?>
            <li><a href="ver_pagos.php">Pagos</a></li>
            <li><a href="registro.php">Registro</a></li>
        <?php endif; ?>
                <li><a href="?logout=true">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Ver Gastos Mensuales</h2>

        <!-- Formulario para buscar gastos por descripción -->
        <div class="search-form">
            <input type="text" id="search-input" class="search-input" placeholder="Buscar por Mes">
            <button type="button" class="btn btn-secondary btn-search" onclick="buscarGastos()">Buscar</button>
        </div>

        <!-- Botón para registrar recibo mensual -->
        <?php if ($_SESSION['rol'] !== 'user'): ?>
            <div class="botonAdd">
                <a class="btn btn-success add" href="crear_gastos.php">Registrar Recibo Mensual</a>
            </div>
        <?php endif; ?>

        <!-- Tabla de gastos mensuales -->
        <div id="content">
            <!-- Aquí se actualizarán los resultados -->
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>

    <script>
        function buscarGastos() {
            const mes = document.getElementById('search-input').value;
            fetch('buscar_gastos.php?mes=' + mes)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Cargar todos los gastos al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            buscarGastos();
        });

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
