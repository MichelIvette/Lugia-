<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Vehículos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/site.css">
</head>
<body>
  <div class="wrapper">
    <?php include 'menu.php'; ?>

    <main class="main-content" id="mainContent">
      <div class="welcome">
        <h2>Vehículos</h2>
        <div class="contenedor-scroll">
          <table border="1" class="table table-striped tabla-profesional">
            <thead class="table-dark">
              <tr>
                <th>Matrícula</th>
                <th>RFC Empleado</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th>Fecha de Última Inspección</th>
                <th>Estatus</th>
              </tr>
            </thead>
            <tbody>
              <?php
              try {
                  $stmt = $pdo->query("SELECT MATRICULA, RFC_EMP, MODELO, ANIO, COLOR, FECHA_ULT_INSP, ESTATUS FROM AUTOS_ASIGNADOS");
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      echo "<tr>";
                      foreach ($row as $valor) {
                          echo "<td>" . htmlspecialchars($valor ?? '') . "</td>";
                      }
                      echo "</tr>";
                  }
              } catch (PDOException $e) {
                  echo "<tr><td colspan='7' class='text-danger'>Error al cargar vehículos: " . $e->getMessage() . "</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 Start & Go. Todos los derechos reservados.</p>
    </footer>
  </div>

  <script src="js/site.js"></script>
</body>
</html>
