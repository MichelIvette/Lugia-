<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';
require_once 'verificar_rol.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO CLIENTES (
            RFC_CLIENTE, TIPO_CONTRATACION, NOMB_CLI, AP_CLI, AM_CLI, 
            FECHA_NAC, CALLE, NUMERO, COLONIA, ALCALDIA, 
            PERMISO, OBSERVACIONES, TOTAL_PAGO, FORMA_PAGO, 
            REEMBOLSO, USUARIO, DOMINIO, FECHA_PAGO
        ) VALUES (
            :rfc, :tipo_contratacion, :nombre, :apellido_paterno, :apellido_materno,
            :fecha_nac, :calle, :numero, :colonia, :alcaldia,
            :permiso, :observaciones, :total_pago, :forma_pago,
            :reembolso, :usuario, :dominio, :fecha_pago
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':rfc' => $_POST['rfc_cliente'],
            ':tipo_contratacion' => $_POST['tipo_contratacion'],
            ':nombre' => $_POST['nomb_cli'],
            ':apellido_paterno' => $_POST['ap_cli'],
            ':apellido_materno' => $_POST['am_cli'] ?? null,
            ':fecha_nac' => $_POST['fecha_nac'],
            ':calle' => $_POST['calle'],
            ':numero' => $_POST['numero'],
            ':colonia' => $_POST['colonia'],
            ':alcaldia' => $_POST['alcaldia'],
            ':permiso' => $_POST['permiso'] ?? null,
            ':observaciones' => $_POST['observaciones'] ?? null,
            ':total_pago' => $_POST['total_pago'] ?? 0,
            ':forma_pago' => $_POST['forma_pago'] ?? null,
            ':reembolso' => $_POST['reembolso'] ?? 0,
            ':usuario' => $_POST['usuario'] ?? null,
            ':dominio' => $_POST['dominio'] ?? null,
            ':fecha_pago' => $_POST['fecha_pago'] ?? null
        ]);

        $_SESSION['mensaje'] = "
            <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
                Alumno agregado correctamente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), '1062')) {
            // Error de clave duplicada (RFC repetido)
            $_SESSION['mensaje'] = "
                <div class='rfc-duplicado alert alert-danger alert-dismissible fade show m-3' role='alert'>
                    ⚠️ Ya existe un alumno con ese RFC.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            $_SESSION['mensaje'] = "
                <div class='rfc-duplicado alert alert-danger alert-dismissible fade show m-3' role='alert'>
                    ❌ Error al agregar alumno.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";

        }
    }
}

// Redirigir a la página principal de alumnos
header("Location: alumnos.php");
exit;
?>
