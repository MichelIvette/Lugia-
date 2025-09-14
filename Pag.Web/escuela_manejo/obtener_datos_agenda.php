<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

header('Content-Type: application/json');

try {
    
    $fechaInicio = $_POST['fechaInicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin = $_POST['fechaFin'] ?? date('Y-m-d');
    $tipoReporte = $_POST['tipoReporte'] ?? 'actividades';

    // Datos para el gráfico de actividades por día
    $stmtActividades = $pdo->prepare("
        SELECT DATE(FECHA) as dia, COUNT(*) as total
        FROM AGENDA
        WHERE FECHA BETWEEN :fechaInicio AND :fechaFin
        GROUP BY DATE(FECHA)
        ORDER BY DATE(FECHA)
    ");
    $stmtActividades->execute(['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]);
    $actividades = $stmtActividades->fetchAll(PDO::FETCH_ASSOC);

    // Datos para el gráfico de resultados
    $stmtResultados = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN EXAM_TEO >= 70 THEN 1 ELSE 0 END) as aprobados_teo,
            SUM(CASE WHEN EXAM_TEO < 70 THEN 1 ELSE 0 END) as reprobados_teo,
            SUM(CASE WHEN EXAM_PRAC >= 70 THEN 1 ELSE 0 END) as aprobados_prac,
            SUM(CASE WHEN EXAM_PRAC < 70 THEN 1 ELSE 0 END) as reprobados_prac
        FROM AGENDA
        WHERE FECHA BETWEEN :fechaInicio AND :fechaFin
    ");
    $stmtResultados->execute(['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]);
    $resultados = $stmtResultados->fetch(PDO::FETCH_ASSOC);

    // Datos para la tabla resumen
    $stmtResumen = $pdo->prepare("
        SELECT 
            DATE(FECHA) as fecha,
            COUNT(*) as total_actividades,
            SUM(CASE WHEN ACTIVIDAD LIKE '%examen%' THEN 1 ELSE 0 END) as examenes_teoricos,
            SUM(CASE WHEN ACTIVIDAD LIKE '%práctico%' THEN 1 ELSE 0 END) as examenes_practicos,
            AVG((EXAM_TEO + EXAM_PRAC)/2) as promedio_calificacion
        FROM AGENDA
        WHERE FECHA BETWEEN :fechaInicio AND :fechaFin
        GROUP BY DATE(FECHA)
        ORDER BY DATE(FECHA) DESC
    ");
    $stmtResumen->execute(['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]);
    $resumen = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);

    // Preparar respuesta JSON
    $response = [
        'labels' => array_column($actividades, 'dia'),
        'actividades' => array_column($actividades, 'total'),
        'resultados' => [
            $resultados['aprobados_teo'],
            $resultados['reprobados_teo'],
            $resultados['aprobados_prac'],
            $resultados['reprobados_prac']
        ],
        'detalle' => $resumen
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener datos: ' . $e->getMessage()]);
}