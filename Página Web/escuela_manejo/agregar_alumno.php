<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

// Procesar formulario de agregar alumno
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO CLIENTES (
            RFC_CLIENTE, TIPO_CONTRATACION, NOMB_CLI, AP_CLI, AM_CLI, 
            FECHA_NAC, CALLE, NUMERO, COLONIA, ALCALDIA, 
            PERMISO, OBSERVACIONES, TOTAL_PAGO, FORMA_PAGO, 
            REEMBOLSO, USUARIO, DOMINIO
        ) VALUES (
            :rfc, :tipo_contratacion, :nombre, :apellido_paterno, :apellido_materno,
            :fecha_nac, :calle, :numero, :colonia, :alcaldia,
            :permiso, :observaciones, :total_pago, :forma_pago,
            :reembolso, :usuario, :dominio
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
            ':dominio' => $_POST['dominio'] ?? null
        ]);

        $_SESSION['mensaje'] = "<div class='alert alert-success'>Alumno agregado correctamente</div>";
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "<div class='alert alert-danger'>Error al agregar alumno: " . $e->getMessage() . "</div>";
    }
}

header("Location: alumnos.php");
exit;
?>