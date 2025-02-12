<?php require_once('./verificar_session.php');

verificarRol(['admin']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Condominio</title>
    <link rel="stylesheet" type="text/css" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script.js"></script>
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

        th, td {
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

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 60vh;
        }
        #resident-form{
            display: flex;
            flex-direction: column;
            max-width: 100%;
            align-items: center;
            
        }
        #resident-form>input{
            border-radius: 10px;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #333;
        }

        @media (max-width: 768px) {
            main {
                padding: 10px;
            }

            .modal-content {
                width: 80%;
            }
        }
        
        /* Estilos específicos para el botón de agregar residente */
        .botonAdd {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .add {
            font-weight: bold;
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
    </style>
</head>
<body>
<header style="text-align: center; font-weight: bold;">
        <h1>Sistema de Condominio</h1>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="ver_pagos.php">Pagos</a></li>
                <li><a href="ver_gastos.php">Gastos Mensuales</a></li>
                <li><a href="registro.php">Registro de Usuarios</a></li> 
                <li><a href="usuarios.php">Usuarios</a></li> <!-- Agregar enlace de registro -->
                <li><a href="?logout=true">Cerrar sesión</a></li>
             
            </ul>
        </nav>
    </header>
    <main>
        <div class="search-form">
            <form id="search-form">
                <input type="text" id="search" class="search-input" placeholder="Buscar por unidad">
                <button type="button" class="btn btn-secondary  btn-search" onclick="searchResidente()">Buscar</button>
            </form>
        </div>
        <div class="botonAdd">
            <button class="btn btn-success add" onclick="showModal('add-resident')">Agregar Residente</button>
        </div>
        <h2>Bienvenido al Sistema de Condominio</h2>
        <div id="content">
            <?php
            // Conexión a la base de datos
            require 'conection.php';
           
            // Consulta SQL para obtener la lista de residentes
            $sql = "SELECT * FROM residentes";
            $result = $conn->query($sql);

            // Mostrar los datos de los residentes
            if ($result->num_rows > 0) {
                echo "<table id='residentes-table'>";
                echo "<tr><th>Nombre</th><th>Apellido</th><th>Cedula</th><th>Unidad</th><th>Teléfono</th><th>Acciones</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                  
                    echo "<td>" . $row["nombre"] . "</td>";
                    echo "<td>" . $row["apellido"] . "</td>";
                    echo "<td>" . $row["cedula"] . "</td>";
                    echo "<td><a href='ver_pagos.php?unidad=" . urlencode($row["unidad"]) . "'>" . $row["unidad"] . "</a></td>";
                    echo "<td>" . $row["telefono"] . "</td>";
                    echo "<td>";
                    echo "<a class='btn btn-success' href='editar.php?id=" . $row["id"] . "'>Editar</a>";
                   // echo "<button class='btn btn-danger' onclick='deleteResidente(" . $row["id"] . ")'>Eliminar</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No se encontraron resultados.";
            }

            $conn->close();
            ?>
        </div>
       
    </main>
    <footer>
        <p>&copy; 2024 Sistema de Condominio. Todos los derechos reservados.</p>
    </footer>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="hideModal()">&times;</span>
            <h2 id="modal-title"></h2>
            <form id="resident-form" action="guardar.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required>
                <label for="cedula">cedula:</label>
                <input type="text" id="cedula" name="cedula" required>
                <label for="unidad">Unidad:</label>
                <input type="text" id="unidad" name="unidad" required>
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>
                <input type="hidden" id="residente-id" name="residente-id">
                <button class="btn btn-success" type="submit">Guardar</button>
            </form>
        </div>
    </div>

    <!-- Script para confirmar eliminación y búsqueda -->
    <script>
        function deleteResidente(id) {
            if (confirm('¿Estás seguro de querer eliminar este residente?')) {
                // Realizar una solicitud AJAX para eliminar el residente
                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'eliminar_residente.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Recargar la página o actualizar la lista de residentes después de eliminar
                        location.reload(); // Esto recarga la página
                        // Puedes implementar una actualización parcial de la lista si prefieres
                    }
                };
                xhr.send('id=' + id);
            }
        }

        function searchResidente() {
            let input = document.getElementById('search').value.toUpperCase();
            let table = document.getElementById('residentes-table');
            let tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Comenzamos en 1 para omitir la fila de encabezados
                let td = tr[i].getElementsByTagName('td')[3]; // Columna de Unidad
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().indexOf(input) > -1 ? '' : 'none';
                }
            }
        }
       
        // Llamar a la función de alerta en la carga de la página
        window.onload = function() {
            // showSuccessAlert();
        };
    </script>
</body>
</html>
