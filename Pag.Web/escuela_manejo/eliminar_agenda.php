<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

require_once 'verificar_rol.php';

// Función para mostrar errores amigables
function mostrarError($mensaje) {
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>$mensaje</div>";
    header("Location: agenda.php");
    exit;
}

try {
    // CONEXIÓN A LA BASE DE DATOS
    require_once 'conexion.php';

    

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["seleccionados"])) {
        
        // Verificar que hay registros seleccionados
        if (empty($_POST["seleccionados"])) {
            mostrarError("No se seleccionaron registros para eliminar");
        }

        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM AGENDA WHERE RFC_EMP = ? AND FECHA = ? AND HORA = ?");
        $eliminados = 0;
        
        foreach ($_POST["seleccionados"] as $registro) {
            // Validar y separar los valores
            if (substr_count($registro, '|') !== 2) {
                $pdo->rollBack();
                mostrarError("Formato de datos inválido");
            }
            
            list($rfc, $fecha, $hora) = explode('|', $registro);
            
            // Validar datos básicos
            if (empty($rfc) || empty($fecha) || empty($hora)) {
                $pdo->rollBack();
                mostrarError("Datos incompletos para eliminar");
            }
            
            $stmt->execute([$rfc, $fecha, $hora]);
            $eliminados += $stmt->rowCount();
        }
        
        $pdo->commit();
        
        if ($eliminados > 0) {
            $_SESSION['mensaje'] = "<div class='alert alert-success'>$eliminados registro(s) eliminado(s) correctamente</div>";
        } else {
            $_SESSION['mensaje'] = "<div class='alert alert-warning'>No se encontraron registros para eliminar</div>";
        }
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Mensajes amigables para errores comunes
    $mensaje = "Ocurrió un error al intentar eliminar los registros";
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        $mensaje = "No se pueden eliminar registros con datos relacionados (primero elimine los registros asociados)";
    }
    
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>$mensaje</div>";
}

header("Location: agenda.php");
exit;
?>