<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

// Procesar modificación de alumno
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE CLIENTES SET
            RFC_CLIENTE = :rfc,
            TIPO_CONTRATACION = :tipo_contratacion,
            NOMB_CLI = :nombre,
            AP_CLI = :apellido_paterno,
            AM_CLI = :apellido_materno,
            FECHA_NAC = :fecha_nac,
            CALLE = :calle,
            NUMERO = :numero,
            COLONIA = :colonia,
            ALCALDIA = :alcaldia,
            PERMISO = :permiso,
            OBSERVACIONES = :observaciones,
            TOTAL_PAGO = :total_pago,
            FORMA_PAGO = :forma_pago,
            REEMBOLSO = :reembolso,
            USUARIO = :usuario,
            DOMINIO = :dominio,
            FECHA_PAGO = :fecha_pago
        WHERE RFC_CLIENTE = :rfc_original";


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
            ':fecha_pago' => $_POST['fecha_pago'] ?? null,
            ':total_pago' => $_POST['total_pago'] ?? 0,
            ':forma_pago' => $_POST['forma_pago'] ?? null,
            ':reembolso' => $_POST['reembolso'] ?? 0,
            ':usuario' => $_POST['usuario'] ?? null,
            ':dominio' => $_POST['dominio'] ?? null,
            ':rfc_original' => $_POST['rfc_original']
        ]);

        $_SESSION['mensaje'] = "
            <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
                Alumno modificado correctamente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    } catch (PDOException $e) {
       $_SESSION['mensaje'] = "
        <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
            Error al modificar alumno: " . $e->getMessage() . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }
}

header("Location: alumnos.php");
exit;
?>