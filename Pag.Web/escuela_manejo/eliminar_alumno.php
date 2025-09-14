<?php
session_start();

if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: alumnos.php");
    exit;
}

require_once 'verificar_rol.php';
require_once 'conexion.php';

try {
    if (!isset($_POST['seleccionados'])) {
        $_SESSION['mensaje'] = "
            <div class='alert alert-warning alert-dismissible fade show m-3' role='alert'>
                No se seleccionaron alumnos para eliminar
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        header("Location: alumnos.php");
        exit;
    }

    $data = is_array($_POST['seleccionados']) ? $_POST['seleccionados'] : [$_POST['seleccionados']];
    if (empty($data)) {
        $_SESSION['mensaje'] = "
            <div class='alert alert-warning alert-dismissible fade show m-3' role='alert'>
                No se seleccionaron alumnos para eliminar
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        header("Location: alumnos.php");
        exit;
    }

    // Validar formatos de RFC
    foreach ($data as $rfc) {
        if (!preg_match('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
            $_SESSION['mensaje'] = "
                <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
                    Formato de RFC no válido: " . htmlspecialchars($rfc) . "
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            header("Location: alumnos.php");
            exit;
        }
    }

    $placeholders = implode(',', array_fill(0, count($data), '?'));
    $sql = "DELETE FROM CLIENTES WHERE RFC_CLIENTE IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);

    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        $_SESSION['mensaje'] = $rowCount > 1 
            ? "
                <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
                    $rowCount alumnos eliminados correctamente
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>"
            : "
                <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
                    Alumno eliminado correctamente
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
    } else {
        $_SESSION['mensaje'] = "
            <div class='alert alert-info alert-dismissible fade show m-3' role='alert'>
                No se encontraron alumnos para eliminar
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }

} catch (PDOException $e) {
    $_SESSION['mensaje'] = "
        <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
            Error al eliminar alumno(s): " . $e->getMessage() . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
} catch (Exception $e) {
    $_SESSION['mensaje'] = "
        <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
            Error: " . $e->getMessage() . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
}

header("Location: alumnos.php");
exit;