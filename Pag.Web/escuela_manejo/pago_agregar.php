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
        $sql = "INSERT INTO PAGOS (
                    RFC_CLIENTE, 
                    FECHA_PAGO, 
                    TIPO_CONTRATACION, 
                    TOTAL_PAGO, 
                    FORMA_PAGO, 
                    REEMBOLSO                  
                ) VALUES (
                    :rfc_cliente, 
                    :fecha_pago, 
                    :tipo_contratacion, 
                    :total_pago, 
                    :forma_pago, 
                    :reembolso
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':rfc_cliente'       => strtoupper(trim($_POST['rfc_cliente'])),
            ':fecha_pago'        => $_POST['fecha_pago'],
            ':tipo_contratacion' => $_POST['tipo_contratacion'],
            ':total_pago'        => $_POST['total_pago'],
            ':forma_pago'        => $_POST['forma_pago'],
            ':reembolso'         => $_POST['reembolso']
        ]);

        $_SESSION['mensaje'] = "
            <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
                Pago registrado correctamente.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), '1062')) {
            $_SESSION['mensaje'] = "
                <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
                    ⚠️ Ya existe un pago registrado con ese RFC de cliente.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } else {
            $_SESSION['mensaje'] = "
                <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
                     Error al registrar el pago: " . htmlspecialchars($e->getMessage()) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        }
    }
}

// Redirigir de regreso a la página principal de pagos
header("Location: pagos.php");
exit;
?>
