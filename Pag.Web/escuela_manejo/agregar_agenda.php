<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

require_once 'verificar_rol.php';

// Función para mensajes de error amigables
function mostrarError($mensaje) {
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>$mensaje</div>";
    $_SESSION['datos_formulario'] = $_POST; // Guardar datos para repoblar formulario
    header("Location: agenda.php");
    exit;
}


try {
    // CONEXIÓN A LA BASE DE DATOS
    require_once 'conexion.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // ===== VALIDACIONES DE DATOS ===== //
        
        // 1. Validar campos obligatorios
        $camposRequeridos = [
            'rfc_emp' => 'RFC Empleado',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'exam_teo' => 'Examen Teórico',
            'exam_prac' => 'Examen Práctico'
        ];
        
        foreach ($camposRequeridos as $campo => $nombre) {
            if (empty($_POST[$campo])) {
                mostrarError("El campo $nombre es requerido");
            }
        }

        // 2. Validar formato de RFC (solo formato básico)
        $rfc = strtoupper(trim($_POST['rfc_emp']));
        if (!preg_match('/^[A-Z]{4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
            mostrarError("El RFC es invalido");
        }

        // 3. Validar fecha (formato YYYY-MM-DD y que sea válida)
        $fecha = $_POST['fecha'];
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            mostrarError("Formato de fecha inválido (use AAAA-MM-DD)");
        }
        
        $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
            mostrarError("La fecha ingresada no es válida");
        }

        // 4. Validar hora (formato HH:MM)
        if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $_POST['hora'])) {
            mostrarError("Formato de hora inválido (use HH:MM en formato 24 horas)");
        }

        // 5. Validar calificaciones (0-100)
        $exam_teo = (int)$_POST['exam_teo'];
        $exam_prac = (int)$_POST['exam_prac'];
        
        if ($exam_teo < 0 || $exam_teo > 100) {
            mostrarError("La calificación del examen teórico debe ser entre 0 y 100");
        }
        
        if ($exam_prac < 0 || $exam_prac > 100) {
            mostrarError("La calificación del examen práctico debe ser entre 0 y 100");
        }

        // 6. Validar RFC Cliente (si se proporciona)
        if (!empty($_POST['rfc_cliente'])) {
            $rfc_cliente = strtoupper(trim($_POST['rfc_cliente']));
            if (!preg_match('/^[A-Z]{4}\d{6}[A-Z0-9]{3}$/', $rfc_cliente)) {
                mostrarError("El RFC del cliente es inválido");
            }
        }

        // ===== INTENTAR INSERTAR ===== //
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO AGENDA 
            (RFC_EMP, FECHA, HORA, RFC_CLIENTE, ACTIVIDAD, KM_RECORRIDOS, 
             NOTAS, EXAM_TEO, EXAM_PRAC, NOTAS_RESULTADO) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
        $stmt->execute([
            $rfc,
            $fecha,
            $_POST['hora'],
            !empty($_POST['rfc_cliente']) ? $_POST['rfc_cliente'] : null,
            !empty($_POST['actividad']) ? $_POST['actividad'] : null,
            !empty($_POST['km_recorridos']) ? (int)$_POST['km_recorridos'] : null,
            !empty($_POST['notas']) ? $_POST['notas'] : null,
            $exam_teo,
            $exam_prac,
            !empty($_POST['notas_resultado']) ? $_POST['notas_resultado'] : null
        ]);
        
        $pdo->commit();
        $_SESSION['mensaje'] = "<div class='alert alert-success'>Registro agregado correctamente</div>";
        unset($_SESSION['datos_formulario']); // Limpiar datos del formulario

    }
} catch (PDOException $e) {
    // Manejar errores de base de datos con mensajes amigables
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $mensajeError = "Ocurrió un error al guardar los datos";
    
    // Detectar errores comunes de SQL y traducirlos
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        $mensajeError = "Ya existe un registro con el mismo RFC, fecha y hora";
    } elseif (strpos($e->getMessage(), 'constraint fails') !== false) {
        $mensajeError = "Datos inválidos: verifique los RFCs ingresados";
    }
    
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>$mensajeError</div>";
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['mensaje'] = "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
}

header("Location: agenda.php");
exit;
?>