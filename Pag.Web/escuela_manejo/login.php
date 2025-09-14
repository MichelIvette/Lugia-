<?php
session_start();
if (isset($_SESSION["activa"]) && $_SESSION["activa"] === true) {
    header("Location: dentro.php");
    exit;
}

function validaLogin($nombre, $clave) {
    try {
        require_once 'conexion.php';
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE usuario_nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario && md5($clave) === $usuario["usuario_clave"]) {
            return $usuario;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function creaSesion($nombre) {
    $archivoLog = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sesiones.log';
    $mensaje = "[" . date("Y-m-d H:i:s") . "] Usuario '$nombre' inició sesión.\n";
    file_put_contents($archivoLog, $mensaje, FILE_APPEND);
}

// Si viene POST, procesamos login
if (isset($_POST["nombre"]) && isset($_POST["clave"])) {
    $usuarioValidado = validaLogin($_POST["nombre"], $_POST["clave"]);
    if ($usuarioValidado) {
        $_SESSION["activa"] = true;
        $_SESSION["usuario"] = $_POST["nombre"];
        
        // CORRECCIÓN: Usar 'usuario_nombre' como identificador de rol
        $_SESSION["rol"] = $usuarioValidado['usuario_nombre'];
        
        creaSesion($_POST["nombre"]);

        if (isset($_POST["recordarme"])) {
            setcookie("usuario_recordado", $_POST["nombre"], time() + (7 * 24 * 60 * 60), "/");
        } else {
            setcookie("usuario_recordado", "", time() - 3600, "/");
        }

        header("Location: dentro.php");
        exit;
    } else {
        $error = "Usuario y/o contraseña incorrectos.";
    }
}

if (!isset($_SESSION["activa"]) && isset($_COOKIE["usuario_recordado"])) {
    $nombre = $_COOKIE["usuario_recordado"];

    try {
        require_once 'conexion.php';
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE usuario_nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION["activa"] = true;
            $_SESSION["usuario"] = $nombre;
            
            // CORRECCIÓN: Usar 'usuario_nombre' como identificador de rol
            $_SESSION["rol"] = $usuario['usuario_nombre'];

            setcookie("usuario_recordado", $nombre, time() + (7 * 24 * 60 * 60), "/");

            header("Location: dentro.php");
            exit;
        } else {
            setcookie("usuario_recordado", "", time() - 3600, "/");
        }
    } catch (PDOException $e) {
        // Manejar error
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!--2-06-2025 meta-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/icono.png" type="image/png">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--31-05-2025 Mostra y ocultar contraseña-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<!--02-06-2025 Eliminación de vh-->
<body class="bg-light d-flex justify-content-center align-items-center">
    <div class="card p-4 shadow login-card">
    <!-- 31-05-2025 Círculo con imagen -->
    <div class="circle-above-login">
        <img src="img/icono.png" alt="Logo">
    </div>
    
    <div class="login-header">
        <h3 class="text-center mb-4">Iniciar Sesión</h3>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <!--se agrego ID -->
    <form method="post" id="loginForm">
        <div class="mb-3">
            <label for="nombre" class="form-label">Usuario</label>

            <div class="input-group">
            <input type="text" class="form-control" id="nombre" name="nombre"placeholder="Ingrese su usuario" required>
             <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            
        </div>
        <div class="mb-3">
            <label for="clave" class="form-label">Contraseña</label>

            <!--31-05-2025 Mostrar y ocultar contraseña, placeholder, span-->
            <div class="input-group"><!--1-->
                 <input type="password" class="form-control" id="clave" name="clave" placeholder="Ingrese su contraseña" required> 
                 <span class="input-group-text password-toggle" onclick="togglePassword()">
                        <i id="eyeIcon" class="fas fa-eye-slash"></i>
                </span>
            </div> 
            
        </div>
         <!--31-05-2025 br y texto: mantener la sesión iniciada-->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="recordarme" name="recordarme">
            <label class="form-check-label" for="recordarme">Mantener la sesión iniciada</label>
        </div>
        <!--31-05-2025 <i>, class-->
        
        <button type="submit" class="btn-ingresar"> 
            <i class="fas fa-sign-in-alt me-2"></i>Ingresar
        </button>
    </form>
</div>

    <!--3-05-2025 Script-->

<script src="js/login.js"></script>
</body>
</html>