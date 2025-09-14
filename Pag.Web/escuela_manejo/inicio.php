<?php
session_start();

require_once 'verificar_rol.php';

function esValida($usuario) {
    try {
        // CONEXIÓN A LA BASE DE DATOS
        require_once 'conexion.php';
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE usuario_nombre = :usuario");
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function creaSesion($nombre) {
    $archivoLog = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sesiones.log';
    $mensaje = "[" . date("Y-m-d H:i:s") . "] Usuario '$nombre' inició sesión.\n";
    file_put_contents($archivoLog, $mensaje, FILE_APPEND);
}

// 1. ¿Hay sesión?
if (isset($_SESSION["activa"]) && $_SESSION["activa"] === true) {
    if (esValida($_SESSION["usuario"])) {
        // Refresca cookie si la tenía
        if (isset($_COOKIE["usuario_recordado"])) {
            setcookie("usuario_recordado", $_SESSION["usuario"], time() + (7 * 24 * 60 * 60), "/");
        } else {
            setcookie("usuario_recordado", "", time() - 3600, "/");
        }

        creaSesion($_SESSION["usuario"]);
        header("Location: dentro.php");
        exit;
    }
}

// 2. ¿Hay cookie?
if (isset($_COOKIE["usuario_recordado"])) {
    $usuario = $_COOKIE["usuario_recordado"];
    if (esValida($usuario)) {
        $_SESSION["activa"] = true;
        $_SESSION["usuario"] = $usuario;
        creaSesion($usuario);

        // Refrescar cookie
        setcookie("usuario_recordado", $usuario, time() + (7 * 24 * 60 * 60), "/");

        header("Location: dentro.php");
        exit;
    } else {
        setcookie("usuario_recordado", "", time() - 3600, "/");
    }
}

// 3. No hay sesión ni cookie válida: ir a login
header("Location: login.php");
exit;
?>
