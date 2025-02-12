<?php
session_start();

// Verificar si el usuario no está logueado y redirigir al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Verificar si el usuario tiene el rol adecuado para acceder a la página
function verificarRol($rolesPermitidos) {
    if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
        // Redirigir a una página de acceso denegado si el rol no es permitido
        header('Location: acceso_denegado.php');
        exit;
    }
}

// Función para cerrar sesión
function logout() {
    $_SESSION = array(); // Vaciar todas las variables de sesión
    session_destroy(); // Destruir la sesión
    header('Location: login.php'); // Redirigir al login
    exit;
}

// Si se recibe un parámetro 'logout', se llama a la función logout()
if (isset($_GET['logout'])) {
    logout();
}
?>
