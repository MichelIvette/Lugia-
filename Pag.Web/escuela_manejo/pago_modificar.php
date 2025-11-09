<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfc_cliente = $_POST['rfc_cliente'] ?? '';
    $fecha_pago = $_POST['fecha_pago'] ?? '';
    $reembolso = $_POST['reembolso'] ?? '';

    if ($rfc_cliente && $fecha_pago && $reembolso !== '') {
        try {
            $stmt = $pdo->prepare("UPDATE PAGOS SET REEMBOLSO = ? WHERE RFC_CLIENTE = ? AND FECHA_PAGO = ?");
            $stmt->execute([$reembolso, $rfc_cliente, $fecha_pago]);

            $_SESSION['mensaje'] = '<div class="alert alert-success">Reembolso actualizado correctamente.</div>';
        } catch (PDOException $e) {
            $_SESSION['mensaje'] = '<div class="alert alert-danger">Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    } else {
        $_SESSION['mensaje'] = '<div class="alert alert-warning">Datos incompletos.</div>';
    }

    header("Location: pagos.php");
    exit;
}

?>
