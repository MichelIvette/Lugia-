<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';

// Obtener datos para los gráficos
function obtenerDatosClasesPorDia($pdo, $fechaInicio, $fechaFin) {
    $stmt = $pdo->prepare("SELECT FECHA, COUNT(*) as total FROM AGENDA 
                          WHERE FECHA BETWEEN ? AND ?
                          GROUP BY FECHA ORDER BY FECHA");
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerResultadosExamenes($pdo, $fechaInicio, $fechaFin) {
    $stmt = $pdo->prepare("SELECT 
                          SUM(CASE WHEN EXAM_TEO >= 70 THEN 1 ELSE 0 END) as aprobados_teo,
                          SUM(CASE WHEN EXAM_TEO < 70 THEN 1 ELSE 0 END) as reprobados_teo,
                          SUM(CASE WHEN EXAM_PRAC >= 70 THEN 1 ELSE 0 END) as aprobados_prac,
                          SUM(CASE WHEN EXAM_PRAC < 70 THEN 1 ELSE 0 END) as reprobados_prac
                          FROM AGENDA 
                          WHERE FECHA BETWEEN ? AND ?");
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerActividadesPorEmpleado($pdo, $fechaInicio, $fechaFin) {
    $stmt = $pdo->prepare("SELECT RFC_EMP, COUNT(*) as total FROM AGENDA 
                          WHERE FECHA BETWEEN ? AND ?
                          GROUP BY RFC_EMP ORDER BY total DESC LIMIT 5");
    $stmt->execute([$fechaInicio, $fechaFin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar parámetros de fechas
$fechaInicio = $_POST['fechaInicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fechaFin = $_POST['fechaFin'] ?? date('Y-m-d');

$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reportes de Agenda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <link rel="stylesheet" href="css/site.css"/>
  <link rel="icon" href="img/icono.png" type="image/png"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  

</head>
<body>
  <?php include 'menu.php'; ?>
  
  <main class="main-content" id="mainContent">
    <div class="container-fluid">
      <CENTER><h2 class="mb-4">Reportes de Clases y Exámenes</h2></CENTER>

      <!-- Formulario para seleccionar mes y generar estado de cuenta -->
<div class="card shadow mb-4">
  <div class="card-body">
    <!-- Si se quiere que se habra en la misma página -->
    <!--<form action="estado_cuenta.php" method="GET" class="row g-3 align-items-end">-->
      <!-- Abre el estado de cuenta en una nueva pestaña -->
      <form action="estado_cuenta.php" method="GET" class="row g-3 align-items-end" target="_blank">

      <div class="col-md-4">
        <label for="mes" class="form-label">Selecciona el Mes</label>
        <input type="month" id="mes" name="mes" class="form-control" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-secondary w-100">
          <i class="fas fa-file-invoice-dollar me-2"></i> Generar Estado de Cuenta
        </button>
      </div>
    </form>
  </div>
</div>


      <!-- Filtros simplificados -->
      <div class="card shadow mb-4">
        <div class="card-body">
          <form id="filtrosReporte" class="row g-3" method="POST">
            <div class="col-md-4">
              <label for="fechaInicio" class="form-label">Fecha Inicio</label>
              <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" 
                     value="<?= htmlspecialchars($fechaInicio) ?>">
            </div>
            <div class="col-md-4">
              <label for="fechaFin" class="form-label">Fecha Fin</label>
              <input type="date" id="fechaFin" name="fechaFin" class="form-control" 
                     value="<?= htmlspecialchars($fechaFin) ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="submit" class="btn btn-secondary w-100">
                <i class="fas fa-filter me-2"></i> Filtrar
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="card shadow h-100">
            <div class="card-header encabezado-custom">
              <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Clases por Día</h5>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="graficoClases"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card shadow h-100">
            <div class="card-header encabezado-custom">
              <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Resultados de Exámenes</h5>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="graficoExamenes"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla -->
      <div class="card shadow mb-4">
        <div class="card-header encabezado-custom">
          <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Instructores con más clases</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="encabezado-oscuro">Instructor</th>
                  <th class="encabezado-oscuro">Clases Impartidas</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $instructores = obtenerActividadesPorEmpleado($pdo, $fechaInicio, $fechaFin);
                foreach ($instructores as $instructor): ?>
                <tr>
                  <td><?= htmlspecialchars($instructor['RFC_EMP']) ?></td>
                  <td><?= htmlspecialchars($instructor['total']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  // Menú hamburguesa
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const mainContent = document.getElementById('mainContent');

    if (menuToggle && sidebar && mainContent) {
      menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-open');

        const icon = this.querySelector('i');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
      });

      document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && e.target !== menuToggle && !menuToggle.contains(e.target)) {
          sidebar.classList.remove('active');
          mainContent.classList.remove('sidebar-open');
          const icon = menuToggle.querySelector('i');
          if (icon.classList.contains('fa-times')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        }
      });

      function handleResize() {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('active');
          mainContent.classList.remove('sidebar-open');
          const icon = menuToggle.querySelector('i');
          if (icon.classList.contains('fa-times')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        }
      }

      window.addEventListener('resize', handleResize);
      handleResize();
    }

    // Datos para gráficos desde PHP
    const clasesPorDia = <?= json_encode(obtenerDatosClasesPorDia($pdo, $fechaInicio, $fechaFin)) ?>;
    const resultadosExamenes = <?= json_encode(obtenerResultadosExamenes($pdo, $fechaInicio, $fechaFin)) ?>;

    // Gráfico de Clases por Día
    const ctxClases = document.getElementById('graficoClases');
    new Chart(ctxClases, {
      type: 'bar',
      data: {
        labels: clasesPorDia.map(item => item.FECHA),
        datasets: [{
          label: 'Clases por Día',
          data: clasesPorDia.map(item => item.total),
          backgroundColor: 'rgba(54, 162, 235, 0.7)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });

    // Gráfico de Resultados de Exámenes
    const ctxExamenes = document.getElementById('graficoExamenes');
    new Chart(ctxExamenes, {
      type: 'pie',
      data: {
        labels: ['Aprobados Teórico', 'Reprobados Teórico', 'Aprobados Práctico', 'Reprobados Práctico'],
        datasets: [{
          data: [
            resultadosExamenes.aprobados_teo,
            resultadosExamenes.reprobados_teo,
            resultadosExamenes.aprobados_prac,
            resultadosExamenes.reprobados_prac
          ],
          backgroundColor: [
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)'
          ],
          borderColor: [
            'rgba(75, 192, 192, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });
</script>

   <footer>
      <p>&copy; 2025 Start & Go. Todos los derechos reservados.</p>
    </footer>


</body>
</html>