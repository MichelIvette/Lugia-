<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];

require_once 'verificar_rol.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Bienvenido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/site.css">

</head>
<body>
  
    <?php include 'menu.php'; ?>

    <main class="main-content" id="mainContent">   
      <?php
      // Mostrar mensaje bienvenida solo una vez
      if (!isset($_SESSION['bienvenida_mostrada'])) {
          $_SESSION['bienvenida_mostrada'] = true;
      ?>
      <div id="bienvenida" class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>¡Bienvenido, <?= htmlspecialchars($usuario) ?>!</strong> Has iniciado sesión correctamente.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php } ?>

      <!--Body-->
      <section class="hero">
        <img src="img/clase1.jpg" alt="Clase de manejo" class="hero-img">
        <div class="hero-text">
          <h1>Start & Go</h1>
          <p>Tu camino seguro comienza aquí <br> La escuela de manejo que te prepara para la vida.</p>
        </div>
      </section>

      <br>
      <section class="intro py-4 px-3 bg-light rounded shadow-sm mb-5 text-center">
  <h2 class="mb-3">¿Quiénes somos?</h2>
  <p>Somos una escuela de manejo comprometida con tu seguridad y aprendizaje, contamos con instructores certificados, vehículos modernos y programas personalizados para que obtengas tu licencia con confianza.</p>
  <p>Nuestro objetivo es ayudarte a convertirte en un conductor responsable y seguro en cualquier situación vial.</p>
</section>

<br><br>
<section class="services d-flex justify-content-around text-center mb-5 flex-wrap">
  <div class="service-card px-3 mb-4" style="max-width: 250px;">
    <i class="fas fa-car fa-3x mb-2 text-primary"></i>
    <h4>Misión</h4>
    <p>Formar conductores responsables y seguros mediante enseñanza personalizada, ética y profesionalismo.</p>
  </div>
  <div class="service-card px-3 mb-4" style="max-width: 250px;">
    <i class="fas fa-road fa-3x mb-2 text-primary"></i>
    <h4>Visión</h4>
    <p>Ser la escuela de manejo líder a nivel regional, reconocida por la excelencia en formación vial y calidad humana.</p>
  </div>
  <div class="service-card px-3 mb-4" style="max-width: 250px;">
    <i class="fas fa-certificate fa-3x mb-2 text-primary"></i>
    <h4>Valores de la Empresa</h4>
    <p>
      <ul class="text-start px-3" style="display: inline-block;">
        <li>Responsabilidad</li>
        <li>Compromiso</li>
        <li>Paciencia</li>
        <li>Respeto</li>
        <li>Excelencia</li>
      </ul>
    </p>
  </div>
</section>

    </main>

    <footer>
      <p>&copy; 2025 Start & Go. Todos los derechos reservados.</p>
    </footer>
  </div>

  <script src="js/site.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
