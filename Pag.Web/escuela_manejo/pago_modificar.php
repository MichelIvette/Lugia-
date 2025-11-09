<?php
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfc_cliente = $_POST['rfc_cliente'] ?? '';
    $reembolso = $_POST['reembolso'] ?? '';

    if ($rfc_cliente && $reembolso !== '') {
        try {
            $stmt = $pdo->prepare("UPDATE PAGOS SET REEMBOLSO = ? WHERE RFC_CLIENTE = ?");
            $stmt->execute([$reembolso, $rfc_cliente]);
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
