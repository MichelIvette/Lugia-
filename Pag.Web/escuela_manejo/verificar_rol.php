<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

$paginaActual = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION['rol'] ?? null;

// Definir las páginas permitidas para cada rol
$paginasPermitidas = [];

if ($rol === 'recepcionista') {
    $paginasPermitidas = [
        'dentro.php',
        'agenda.php',
        'agregar_agenda.php',
        'modificar_agenda.php',
        'eliminar_agenda.php',
        'alumnos.php',
        'modificar_alumno.php',
        'eliminar_alumno.php',
        'agregar_alumno.php',
        'sistema_ayuda.php'
    ];
}

// Si el rol es recepcionista y la página actual no está en las permitidas, redirigir
if ($rol === 'recepcionista' && !in_array($paginaActual, $paginasPermitidas)) {
    header("Location: dentro.php");
    exit;
}
?>