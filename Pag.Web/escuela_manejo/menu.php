<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];
$paginaActual = basename($_SERVER['PHP_SELF']);
$rol = $_SESSION["rol"] ?? 'admin'; 

require_once 'verificar_rol.php';
?>

<header>
  <button class="menu-btn" id="menuToggle"><i class="fas fa-bars" title="Menú principal"></i></button>
  <img src="img/Logo_rectangular.jpg" class="logo-container" alt="Logo claro" id="logoClaro">
<img src="img/Logo_rectangular_oscuro.png" class="logo-container" alt="Logo oscuro" id="logoOscuro" style="display:none;">

  <div class="header-icons" style="display: flex; align-items: center;">
  <a href="sistema_ayuda.php" title="Ayuda" class="icon-foquito">
    <i class="fas fa-lightbulb"></i>
  </a>
</div>
</header>

<aside class="sidebar" id="sidebar">
  <ul class="sidebar-menu">
    <li class="menu_subrayado <?php echo ($paginaActual == 'dentro.php') ? 'active' : ''; ?>">
      <a href="dentro.php"><i class="fas fa-home"></i> Inicio</a>
    </li>
        <?php if ($rol === 'admin'): ?>
    <li class="menu_subrayado <?= ($paginaActual == 'empleados.php') ? 'active' : '' ?>">
        <a href="empleados.php"><i class="fa-solid fa-user-tie"></i> Empleados</a>
    </li>
    <?php endif; ?>
    <li class="menu_subrayado <?= ($paginaActual == 'alumnos.php') ? 'active' : '' ?>">
        <a href="alumnos.php"><i class="fas fa-users"></i> Alumnos</a>
    </li>

     <li class="menu_subrayado <?= ($paginaActual == 'pagos.php') ? 'active' : '' ?>">
        <a href="pagos.php"><i class="fas fa-money-bill-wave"></i> Pagos</a>
    </li>
    
    <li class="menu_subrayado <?= ($paginaActual == 'agenda.php') ? 'active' : '' ?>">
        <a href="agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a>
    </li>
    
    <?php if ($rol === 'admin'): ?>
    <li class="menu_subrayado <?= ($paginaActual == 'reportes.php') ? 'active' : '' ?>">
        <a href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
    </li>
    <?php endif; ?>
    <li><a href="#" id="toggle-theme"><i class="fas fa-adjust"></i> Cambiar tema</a></li>
    <li class="salir">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </li>
  </ul>
</aside>

<!-- -------------------------CAMBIAR TEMA----------------------------------------------- -->


<script>
  document.addEventListener("DOMContentLoaded", () => {
    const logoClaro = document.getElementById("logoClaro");
    const logoOscuro = document.getElementById("logoOscuro");
    const btnToggle = document.getElementById("toggle-theme");

    // 1. Aplicar tema guardado
    const temaGuardado = localStorage.getItem("theme");
    if (temaGuardado === "dark") {
      document.body.classList.add("dark-mode");
      logoClaro.style.display = "none";
      logoOscuro.style.display = "block";
    } else {
      document.body.classList.remove("dark-mode");
      logoClaro.style.display = "block";
      logoOscuro.style.display = "none";
    }

    // 2. Al hacer clic en "Cambiar tema"
    btnToggle.addEventListener("click", (e) => {
      e.preventDefault();
      const isDark = document.body.classList.toggle("dark-mode");
      localStorage.setItem("theme", isDark ? "dark" : "light");

      // Mostrar el logo correspondiente
      logoClaro.style.display = isDark ? "none" : "block";
      logoOscuro.style.display = isDark ? "block" : "none";
    });
  });
</script>