<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

// Procesar eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
    try {
        $rfcSeleccionados = $_POST['seleccionados'] ?? [];
        if (!empty($rfcSeleccionados)) {
            $placeholders = implode(',', array_fill(0, count($rfcSeleccionados), '?'));
            $sql = "DELETE FROM AGENDA WHERE CONCAT(RFC_EMP, '|', FECHA, '|', HORA) IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($rfcSeleccionados);
            $_SESSION['mensaje'] = "<div class='alert alert-success'>Registro(s) eliminado(s) correctamente</div>";
            header("Location: agenda.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['mensaje'] = "<div class='alert alert-danger'>Error al eliminar: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Agenda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="img/icono.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
  
  <link rel="stylesheet" href="css/site.css">
</head>
<body>
  
    <?php include 'menu.php'; ?>

    <main class="main-content" id="mainContent">
      <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert-container">
          <?= $_SESSION['mensaje'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
      <?php endif; ?>

      <div class="welcome">
        <h2>Agenda</h2>

        <form id="formEliminar" method="POST" action="eliminar_agenda.php">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="submit" id="btnEliminar" name="eliminar" class="btn btn-danger fab-eliminar" title="Eliminar seleccionados" style="display:none;">
              <i class="fas fa-trash-alt"></i>
            </button>
            <button type="button" class="btn btn-success fab-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregarAgenda" title="Agregar registro">
              <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-success fab-modificar" id="btnModificar" data-bs-toggle="modal" data-bs-target="#modalModificarAgenda" title="Modificar registro" style="display:none;">
              <i class="fas fa-edit"></i>
            </button>
          </div>

          <div class="mb-3">
            <input type="text" id="busquedaTabla" class="form-control" placeholder="Buscar en la tabla...">
          </div>

          <div class="contenedor-scroll">
            <table class="table table-striped tabla-profesional">
              <thead class="table-dark">
                <tr>
                  <th></th>
                  <th>RFC Empleado</th>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>RFC Cliente</th>
                  <th>Actividad</th>
                  <th>Km Recorridos</th>
                  <th>Notas</th>
                  <th>Ex. Teórico</th>
                  <th>Ex. Práctico</th>
                  <th>Resultado</th>
                </tr>
              </thead>
              <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM AGENDA");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        $valorCheckbox = htmlspecialchars($row['RFC_EMP']).'|'.htmlspecialchars($row['FECHA']).'|'.htmlspecialchars($row['HORA']);
                        echo "<td><input type='checkbox' name='seleccionados[]' value='$valorCheckbox' class='fila-checkbox'></td>";
                        echo "<td>".htmlspecialchars($row['RFC_EMP'])."</td>";
                        echo "<td>".htmlspecialchars($row['FECHA'])."</td>";
                        echo "<td>".htmlspecialchars($row['HORA'])."</td>";
                        echo "<td>".htmlspecialchars($row['RFC_CLIENTE'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['ACTIVIDAD'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['KM_RECORRIDOS'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['NOTAS'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['EXAM_TEO'] ?? '0')."</td>";
                        echo "<td>".htmlspecialchars($row['EXAM_PRAC'] ?? '0')."</td>";
                        echo "<td>".htmlspecialchars($row['NOTAS_RESULTADO'] ?? '')."</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='11' class='text-danger'>Error al cargar agenda: ".htmlspecialchars($e->getMessage())."</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </main>

    <!-- Modal Agregar Agenda -->
    <div class="modal fade" id="modalAgregarAgenda" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="agregar_agenda.php">
            <div class="modal-header bg-warning">
              <h5 class="modal-title">Agregar Registro</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label class="form-label">RFC Empleado</label>
                <input type="text" class="form-control" name="rfc_emp" required maxlength="13">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Hora</label>
                <input type="time" class="form-control" name="hora" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">RFC Cliente</label>
                <input type="text" class="form-control" name="rfc_cliente" maxlength="13">
              </div>
              <div class="col-md-6">
                <label class="form-label">Actividad</label>
                <input type="text" class="form-control" name="actividad">
              </div>
              <div class="col-md-6">
                <label class="form-label">KM Recorridos</label>
                <input type="number" class="form-control" name="km_recorridos" min="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Ex. Teórico (0-100)</label>
                <input type="number" class="form-control" name="exam_teo" min="0" max="100" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Ex. Práctico (0-100)</label>
                <input type="number" class="form-control" name="exam_prac" min="0" max="100" required>
              </div>
              <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea class="form-control" name="notas" rows="2" maxlength="50"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Notas Resultado</label>
                <textarea class="form-control" name="notas_resultado" rows="2" maxlength="50"></textarea>
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

    <!-- Modal Modificar Agenda -->
    <div class="modal fade" id="modalModificarAgenda" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="modificar_agenda.php">
            <div class="modal-header bg-warning">
              <h5 class="modal-title">Modificar Registro</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
              <input type="hidden" name="rfc_original">
              <input type="hidden" name="fecha_original">
              <input type="hidden" name="hora_original">

              <div class="col-md-6">
                <label class="form-label">RFC Empleado</label>
                <input type="text" class="form-control" name="rfc_emp" required maxlength="13">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Hora</label>
                <input type="time" class="form-control" name="hora" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">RFC Cliente</label>
                <input type="text" class="form-control" name="rfc_cliente" maxlength="13">
              </div>
              <div class="col-md-6">
                <label class="form-label">Actividad</label>
                <input type="text" class="form-control" name="actividad">
              </div>
              <div class="col-md-6">
                <label class="form-label">KM Recorridos</label>
                <input type="number" class="form-control" name="km_recorridos" min="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Ex. Teórico (0-100)</label>
                <input type="number" class="form-control" name="exam_teo" min="0" max="100" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Ex. Práctico (0-100)</label>
                <input type="number" class="form-control" name="exam_prac" min="0" max="100" required>
              </div>
              <div class="col-12">
                <label class="form-label">Notas</label>
                <textarea class="form-control" name="notas" rows="2" maxlength="50"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Notas Resultado</label>
                <textarea class="form-control" name="notas_resultado" rows="2" maxlength="50"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-tertiary">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Confirmar Eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ¿Estás seguro de eliminar los registros seleccionados?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" id="confirmarEliminarBtn" class="btn btn-danger">Eliminar</button>
          </div>
        </div>
      </div>
    </div>
                
    <footer>
      <p>&copy; 2025 Start & Go. Todos los derechos reservados.</p>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar botones de acción
    const actualizarBotones = () => {
      const seleccionados = document.querySelectorAll('.fila-checkbox:checked');
      document.getElementById('btnEliminar').style.display = seleccionados.length > 0 ? 'block' : 'none';
      document.getElementById('btnModificar').style.display = seleccionados.length === 1 ? 'block' : 'none';
    };

    // Eventos para checkboxes
    document.querySelectorAll('.fila-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        actualizarBotones();
        
        // Cargar datos para modificación
        if (this.checked && document.querySelectorAll('.fila-checkbox:checked').length === 1) {
          const fila = this.closest('tr');
          const celdas = fila.querySelectorAll('td');
          
          // Llenar formulario de modificación
          const modal = document.getElementById('modalModificarAgenda');
          modal.querySelector('input[name="rfc_emp"]').value = celdas[1].textContent;
          modal.querySelector('input[name="fecha"]').value = celdas[2].textContent;
          modal.querySelector('input[name="hora"]').value = celdas[3].textContent;
          
          // Campos ocultos para clave original
          modal.querySelector('input[name="rfc_original"]').value = celdas[1].textContent;
          modal.querySelector('input[name="fecha_original"]').value = celdas[2].textContent;
          modal.querySelector('input[name="hora_original"]').value = celdas[3].textContent;
          
          // Resto de campos
          modal.querySelector('input[name="rfc_cliente"]').value = celdas[4].textContent;
          modal.querySelector('input[name="actividad"]').value = celdas[5].textContent;
          modal.querySelector('input[name="km_recorridos"]').value = celdas[6].textContent;
          modal.querySelector('input[name="exam_teo"]').value = celdas[8].textContent;
          modal.querySelector('input[name="exam_prac"]').value = celdas[9].textContent;
          modal.querySelector('textarea[name="notas"]').value = celdas[7].textContent;
          modal.querySelector('textarea[name="notas_resultado"]').value = celdas[10].textContent;
        }
      });
    });

    // Confirmación de eliminación
    document.getElementById('btnEliminar').addEventListener('click', function(e) {
      e.preventDefault();
      new bootstrap.Modal(document.getElementById('modalConfirmarEliminar')).show();
    });

    document.getElementById('confirmarEliminarBtn').addEventListener('click', function() {
      document.getElementById('formEliminar').submit();
    });

    // Búsqueda en tabla
    document.getElementById('busquedaTabla').addEventListener('input', function() {
      const valor = this.value.toLowerCase();
      document.querySelectorAll('.tabla-profesional tbody tr').forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(valor) ? '' : 'none';
      });
    });

    // Inicializar botones
    actualizarBotones();
  });
  </script>
   <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del menú hamburguesa
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const mainContent = document.getElementById('mainContent');

    if (menuToggle && sidebar && mainContent) {
      menuToggle.addEventListener('click', function() {
        const isActive = sidebar.classList.toggle('active');
        //mainContent.classList.toggle('sidebar-open', isActive);
        //document.body.classList.toggle('no-scroll', isActive);
      });

      document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
          sidebar.classList.remove('active');
          mainContent.classList.remove('sidebar-open');
          document.body.classList.remove('no-scroll');
        }
      });
    }

    // ... (resto del JavaScript previo permanece igual) ...
  });
  </script>
</body>
</html>