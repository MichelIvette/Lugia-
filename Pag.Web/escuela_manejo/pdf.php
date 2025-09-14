<?php
// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

// Obtener mes desde GET (formato: "2025-06")
$mesSeleccionado = $_GET['mes'] ?? date('Y-m');  // por defecto el mes actual
$inicioMes = $mesSeleccionado . "-01";
$finMes = date("Y-m-t", strtotime($inicioMes));  // último día del mes

// Establecer idioma en español para fechas
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
$mesFormateado = strftime("%B de %Y", strtotime($inicioMes));
$mesFormateado = ucfirst($mesFormateado); // Primera letra en mayúscula

// CONSULTA FILTRADA POR MES
$sql = "SELECT RFC_CLIENTE, NOMB_CLI, AP_CLI, AM_CLI, FECHA_PAGO, TOTAL_PAGO, FORMA_PAGO, REEMBOLSO 
        FROM CLIENTES 
        WHERE FECHA_PAGO BETWEEN :inicio AND :fin
        ORDER BY FECHA_PAGO ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['inicio' => $inicioMes, 'fin' => $finMes]);

// Calcular totales
$totalIngresos = 0;
$totalReembolsos = 0;
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($datos as $fila) {
    if ($fila['REEMBOLSO']) {
        $totalReembolsos += $fila['TOTAL_PAGO'];
    } else {
        $totalIngresos += $fila['TOTAL_PAGO'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        header img {
            width: 100px;
        }
        h1 {
            margin-top: 10px;
            font-size: 24px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #666;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #dcdcdc;
        }
        .totales {
            display: flex;
            justify-content: flex-start;
            gap: 40px;
            margin-top: 20px;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div style="text-align: center; margin-bottom: 30px;">
    <img src="Logo.jpg" alt="Logo Empresa" style="width: 100px;"><br>
    <h1 style="font-size: 24px; color: #333;">
        ESTADO DE CUENTA - <?= $mesFormateado ?>
    </h1>
</div>

<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>RFC</th>
            <th>Fecha de Pago</th>
            <th>Monto</th>
            <th>Forma de Pago</th>
            <th>Reembolso</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($datos as $fila): ?>
        <tr>
            <td><?= htmlspecialchars($fila['NOMB_CLI'] . ' ' . $fila['AP_CLI'] . ' ' . $fila['AM_CLI']) ?></td>
            <td><?= htmlspecialchars($fila['RFC_CLIENTE']) ?></td>
            <td><?= htmlspecialchars($fila['FECHA_PAGO']) ?></td>
            <td>$<?= number_format($fila['TOTAL_PAGO'], 2) ?></td>
            <td><?= htmlspecialchars($fila['FORMA_PAGO']) ?></td>
            <td><?= $fila['REEMBOLSO'] ? 'Sí' : 'No' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="totales">
    <div>Total de Ingresos: <span style="color: green;">$<?= number_format($totalIngresos, 2) ?></span></div>
    <div>Total de Reembolsos: <span style="color: red;">$<?= number_format($totalReembolsos, 2) ?></span></div>
</div>

</body>
</html>
