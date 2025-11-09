<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];

// Conexión a la base de datos
require_once 'conexion.php';
require_once 'verificar_rol.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pagos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
  <link rel="stylesheet" href="css/site.css">
</head>

<body>
  <div class="wrapper">
    <?php include 'menu.php'; ?>

    <main class="main-content" id="mainContent">
      <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-container">
          <?= $_SESSION['mensaje'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
      <?php endif; ?>

      <div class="welcome">
        <h2>Pagos</h2>
        <div>
          <button type="button" class="btn btn-success fab-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregarPago" title="Agregar pago">
            <i class="fas fa-plus"></i>
          </button>
          <button type="button" class="btn btn-success fab-modificar" id="btnModificar" data-bs-toggle="modal" data-bs-target="#modalModificarPago" title="Modificar reembolso" style="display:none;">
            <i class="fas fa-edit"></i>
          </button>
        </div>
      </div>

      <div class="esp mb-3">
        <input type="text" id="busquedaTabla" class="form-control" placeholder="Búsqueda general en la tabla...">
      </div>

      <div class="contenedor-scroll">
        <table border="1" class="table table-striped tabla-profesional">
          <thead class="table-dark">
            <tr>
              <th></th>
              <th>RFC Cliente</th>
              <th>Fecha de Pago</th>
              <th>Tipo de Contratación</th>
              <th>Total de Pago</th>
              <th>Forma de Pago</th>
              <th>Reembolso</th>
              <th>RFC Empleado</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM PAGOS");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='seleccionados[]' value='".htmlspecialchars($row['RFC_CLIENTE'])."' class='fila-checkbox'></td>";
                    echo "<td>".htmlspecialchars($row['RFC_CLIENTE'])."</td>";
                    echo "<td>".htmlspecialchars($row['FECHA_PAGO'])."</td>";
                    echo "<td>".htmlspecialchars($row['TIPO_CONTRATACION'])."</td>";
                    echo "<td>".htmlspecialchars($row['TOTAL_PAGO'])."</td>";
                    echo "<td>".htmlspecialchars($row['FORMA_PAGO'])."</td>";
                    echo "<td>".htmlspecialchars($row['REEMBOLSO'])."</td>";
                    echo "<td>".htmlspecialchars($row['RFC_EMP'])."</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='8' class='text-danger'>Error al cargar pagos: ".htmlspecialchars($e->getMessage())."</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Modal Agregar Pago -->
    <div class="modal fade" id="modalAgregarPago" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="pago_agregar.php">
            <div class="modal-header bg-warning">
              <h5 class="modal-title">Agregar Pago</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label for="rfc_cliente" class="form-label">RFC Cliente (en mayúsculas)</label>
                <input type="text" class="form-control" name="rfc_cliente" id="rfc_cliente" required
                  pattern="^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$"
                  title="Debe contener 4 letras, 6 números (fecha) y 3 caracteres alfanuméricos (homoclave)">
              </div>

              <div class="col-md-6">
                <label class="form-label">Fecha de Pago</label>
                <input type="date" class="form-control" name="fecha_pago"
                  max="<?= date('Y-m-d') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Tipo de Contratación</label>
                <select class="form-select" name="tipo_contratacion" required>
                  <option value="" disabled selected></option>
                  <option value="BÁSICO">BÁSICO</option>
                  <option value="INTERMEDIO">INTERMEDIO</option>
                  <option value="PREMIUM">PREMIUM</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Total Pago</label>
                <select class="form-select" name="total_pago" required>
                  <option value="" disabled selected></option>
                  <option value="2000">2000</option>
                  <option value="3500">3500</option>
                  <option value="5000">5000</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Forma de Pago</label>
                <select class="form-select" name="forma_pago" required>
                  <option value="" disabled selected></option>
                  <option value="EFECTIVO">EFECTIVO</option>
                  <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                  <option value="DEBITO">DEBITO</option>
                  <option value="CREDITO">CREDITO</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Reembolso (1 = Sí, 0 = No)</label>
                <input type="number" class="form-control" name="reembolso" min="0" max="1" required>
              </div>

              <div class="col-md-6">
                <label for="rfc_empleado" class="form-label">RFC Empleado (Recepcionista)</label>
                <input type="text" class="form-control" name="rfc_empleado" id="rfc_empleado" required
                  pattern="^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$"
                  title="Debe contener 4 letras, 6 números (fecha) y 3 caracteres alfanuméricos (homoclave)">
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-tertiary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Editar Reembolso -->
    <div class="modal fade" id="modalModificarPago" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="pago_modificar.php">
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title">Modificar Reembolso</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">RFC Cliente</label>
                <input type="text" class="form-control" name="rfc_cliente" id="rfc_cliente_mod" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">Reembolso (1 = Sí, 0 = No)</label>
                <input type="number" class="form-control" name="reembolso" id="reembolso_mod" min="0" max="1" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.fila-checkbox');
    const btnModificar = document.getElementById('btnModificar');
    const buscador = document.getElementById('busquedaTabla');
    let seleccionado = null;

    // --- Manejador de selección ---
    checkboxes.forEach(chk => {
      chk.addEventListener('change', () => {
        checkboxes.forEach(c => c !== chk && (c.checked = false)); // Solo uno
        seleccionado = chk.checked ? chk.closest('tr') : null;
        btnModificar.style.display = seleccionado ? 'inline-block' : 'none';
      });
    });

    // --- Llenar modal de modificación ---
    btnModificar.addEventListener('click', () => {
      if (!seleccionado) return;
      const celdas = seleccionado.querySelectorAll('td');
      document.getElementById('rfc_cliente_mod').value = celdas[1].textContent.trim();
      document.getElementById('reembolso_mod').value = celdas[6].textContent.trim();
    });

    // --- Buscador general ---
    buscador.addEventListener('input', function() {
      const valor = this.value.toLowerCase();
      document.querySelectorAll('.tabla-profesional tbody tr').forEach(fila => {
        const textoFila = fila.textContent.toLowerCase();
        fila.style.display = textoFila.includes(valor) ? '' : 'none';
      });
    });

    // --- Menú lateral (hamburguesa) ---
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const mainContent = document.getElementById('mainContent');

    if (menuToggle && sidebar && mainContent) {
      menuToggle.addEventListener('click', function() {
        const isActive = sidebar.classList.toggle('active');
        mainContent.classList.toggle('sidebar-open', isActive);
        document.body.classList.toggle('no-scroll', isActive);
      });

      document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
          sidebar.classList.remove('active');
          mainContent.classList.remove('sidebar-open');
          document.body.classList.remove('no-scroll');
        }
      });
    }
  });
  </script>
</body>
</html>
