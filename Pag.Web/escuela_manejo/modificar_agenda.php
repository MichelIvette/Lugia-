<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

try {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $stmt = $pdo->prepare("UPDATE AGENDA SET 
                              RFC_EMP = ?, 
                              FECHA = ?, 
                              HORA = ?, 
                              RFC_CLIENTE = ?, 
                              ACTIVIDAD = ?, 
                              KM_RECORRIDOS = ?, 
                              NOTAS = ?, 
                              EXAM_TEO = ?, 
                              EXAM_PRAC = ?, 
                              NOTAS_RESULTADO = ?
                              WHERE RFC_EMP = ? AND FECHA = ? AND HORA = ?");
        
        // Validar calificaciones (0-100)
        $exam_teo = max(0, min(100, (int)$_POST['exam_teo']));
        $exam_prac = max(0, min(100, (int)$_POST['exam_prac']));
        
        $stmt->execute([
            $_POST['rfc_emp'],
            $_POST['fecha'],
            $_POST['hora'],
            $_POST['rfc_cliente'] ?? null,
            $_POST['actividad'] ?? null,
            $_POST['km_recorridos'] ?? null,
            $_POST['notas'] ?? null,
            $exam_teo,
            $exam_prac,
            $_POST['notas_resultado'] ?? null,
            $_POST['rfc_original'],
            $_POST['fecha_original'],
            $_POST['hora_original']
        ]);
        
        $_SESSION['mensaje'] = "<div class='alert alert-success'>Registro actualizado correctamente</div>";
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
}
header("Location: agenda.php");
exit;
?>