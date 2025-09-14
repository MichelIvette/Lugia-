<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["rfc"])) {
    $rfc = $_POST["rfc"];
    $nombre = $_POST["nombre"];
    $ap = $_POST["ap"];
    $am = $_POST["am"];
    $puesto = $_POST["puesto"];
    $turno = $_POST["turno"];
    $descansos_array = $_POST['descansos'] ?? [];
    $descansos = implode(',', $descansos_array);
    $sexo = $_POST["sexo"];
    $fecha_nac = $_POST["fecha_nac"];
    $tel = $_POST["tel"];
    $calle = $_POST["calle"];
    $numero = $_POST["numero"];
    $colonia = $_POST["colonia"];
    $alcaldia = $_POST["alcaldia"];

    try {
        $sql = "UPDATE Empleados SET 
                    NOMB_EMP = :nombre,
                    AP_EMP = :ap,
                    AM_EMP = :am,
                    PUESTO = :puesto,
                    TURNO = :turno,
                    DESCANSOS = :descansos,
                    SEXO = :sexo,
                    FECHA_NAC = :fecha_nac,
                    TEL_PERSONAL = :tel,
                    CALLE = :calle,
                    NUMERO = :numero,
                    COLONIA = :colonia,
                    ALCALDIA = :alcaldia
                WHERE RFC_EMP = :rfc";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':ap' => $ap,
            ':am' => $am,
            ':puesto' => $puesto,
            ':turno' => $turno,
            ':descansos' => $descansos,
            ':sexo' => $sexo,
            ':fecha_nac' => $fecha_nac,
            ':tel' => $tel,
            ':calle' => $calle,
            ':numero' => $numero,
            ':colonia' => $colonia,
            ':alcaldia' => $alcaldia,
            ':rfc' => $rfc
        ]);

        $_SESSION["mensaje"] = "
        <div class='alert alert-success alert-dismissible fade show m-3' role='alert'>
            Empleado actualizado correctamente.
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    } catch (PDOException $e) {
        $_SESSION["mensaje"] = "
        <div class='alert alert-danger alert-dismissible fade show m-3' role='alert'>
            Error al actualizar el empleado: " . $e->getMessage() . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
    }

    header("Location: empleados.php");
    exit;
}
?>
