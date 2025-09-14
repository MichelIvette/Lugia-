<?php
session_start();
if (!isset($_SESSION["activa"])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION["usuario"];

// CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

require_once 'verificar_rol.php';
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Alumnos</title>
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
        <h2>Alumnos</h2>

      <!--Modal eliminar-->
        <form id="formEliminar" method="POST" action="eliminar_alumno.php">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="submit" id="btnEliminar" name="eliminar" class="btn btn-danger fab-eliminar" title="Eliminar seleccionados" style="display:none;">
              <i class="fas fa-trash-alt"></i>
            </button>
            <button type="button" class="btn btn-success fab-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregarAlumno" title="Agregar alumno">
              <i class="fas fa-plus"></i>
            </button>
            <button type="button" class="btn btn-success fab-modificar" id="btnModificar" data-bs-toggle="modal" data-bs-target="#modalModificarAlumno" title="Modificar alumno" style="display:none;">
              <i class="fas fa-edit"></i>
            </button>
          </div>

          <div class="esp mb-3">
            <input type="text" id="busquedaTabla" class="form-control" placeholder="Búsqueda general en la tabla...">
          </div>

          <div class="contenedor-scroll">
            <table border="1" class="table table-striped tabla-profesional">
              <thead class="table-dark">
                <tr>
                  <th></th>
                  <th>RFC</th>
                  <th>Tipo Contratación</th>
                  <th>Nombre</th>
                  <th>Apellido Paterno</th>
                  <th>Apellido Materno</th>
                  <th>Fecha de Nacimiento</th>
                  <th>Calle</th>
                  <th>Número</th>
                  <th>Colonia</th>
                  <th>Alcaldía</th>
                  <th>Permiso</th>
                  <th>Observaciones</th>
                  <th>Fecha de pago</th>
                  <th>Total Pago</th>
                  <th>Forma de Pago</th>
                  <th>Reembolso</th>
                  <th>Correo</th>       
                </tr>
              </thead>
              <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM CLIENTES");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' name='seleccionados[]' value='".htmlspecialchars($row['RFC_CLIENTE'])."' class='fila-checkbox'></td>";
                        echo "<td>".htmlspecialchars($row['RFC_CLIENTE'])."</td>";
                        echo "<td>".htmlspecialchars($row['TIPO_CONTRATACION'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['NOMB_CLI'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['AP_CLI'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['AM_CLI'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['FECHA_NAC'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['CALLE'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['NUMERO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['COLONIA'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['ALCALDIA'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['PERMISO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['OBSERVACIONES'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['FECHA_PAGO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['TOTAL_PAGO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['FORMA_PAGO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['REEMBOLSO'] ?? '')."</td>";
                        echo "<td>".htmlspecialchars($row['CORREO'] ?? '')."</td>";                     
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='18' class='text-danger'>Error al cargar alumnos: ".htmlspecialchars($e->getMessage())."</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </main>

    <!-- Modal Agregar Alumno -->
    <div class="modal fade" id="modalAgregarAlumno" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="agregar_alumno.php">
            <div class="modal-header bg-warning">
              <h5 class="modal-title">Agregar Alumno</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label for="rfc_cliente" class="form-label">RFC (en mayúscualas)</label>
                <input type="text" class="form-control" name="rfc_cliente" id="rfc_cliente" required
                  pattern="^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$"
                  title="Debe contener 4 letras, 6 números (fecha) y 3 caracteres alfanuméricos (homoclave)">
              </div>
              <div class="col-md-6">
                <label class="form-label">Tipo de Contratación</label>
                <select class="form-select" name="tipo_contratacion" required>
                  <option value=""disabled selected></option>
                  <option value="BÁSICO">BÁSICO</option>
                  <option value="INTERMEDIO">INTERMEDIO</option>
                  <option value="PREMIUM">PREMIUM</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nomb_cli" required pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                title="Solo letras y espacios (mínimo 2 caracteres)">
              </div>
              <div class="col-md-4">
                <label class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control" name="ap_cli" required pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                title="Solo letras y espacios (mínimo 2 caracteres)">
              </div>
              <div class="col-md-4">
                <label class="form-label">Apellido Materno</label>
                <input type="text" class="form-control" name="am_cli" required pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                title="Solo letras y espacios (mínimo 2 caracteres)">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" name="fecha_nac" value="<?= htmlspecialchars($_POST['fecha_nac'] ?? '') ?>" 
                  min="1900-01-01"                     
                  max="<?= date('Y-m-d') ?>"             
                  required
                  oninvalid="this.setCustomValidity('Verifique, la fecha introducida no es correcta')"
                  oninput="this.setCustomValidity('')">
              </div>
              <div class="col-md-6">
                <label class="form-label">Permiso</label>
                <select class="form-select" name="permiso" required>
                  <option value=""disabled selected></option>
                  <option value="Sí">Sí</option>
                  <option value="No">No</option>
                  <option value="No">En tramite</option>
                </select>
              </div>
              <div class="col-12 mb-2">
              <hr> <!-- opcional, para una línea separadora -->
              <strong>Dirección</strong>
            </div>
              <div class="col-md-6">
                <label class="form-label">Calle</label>
                <input type="text" class="form-control" name="calle" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="text" class="form-control" name="numero" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Colonia</label>
                <input type="text" class="form-control" name="colonia" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Alcaldía</label>
                <input type="text" class="form-control" name="alcaldia" required>
              </div>
              <hr> <!-- opcional, para una línea separadora -->
                <div class="col-md-6">
                  <label class="form-label" >Fecha de Pago</label>
                  <input type="date" class="form-control" name="fecha_pago" value="<?= htmlspecialchars($_POST['fecha_pago'] ?? '') ?>" 
                  min="1900-01-01"                     
                  max="<?= date('Y-m-d') ?>"             
                  required
                  oninvalid="this.setCustomValidity('Verifique, la fecha introducida no es correcta')"
                  oninput="this.setCustomValidity('')">
                </div>
               <div class="col-md-6">
                  <label class="form-label">Total Pago</label>
                  <div class="input-group">   
                    <input type="number" class="form-control" name="total_pago" placeholder="Ej. 1500">
                  </div>
                </div>
              <div class="col-md-6">
                <label class="form-label">Forma de Pago</label>
                <select class="form-select" name="forma_pago" required>
                  <option value=""disabled selected></option>
                  <option value="EFECTIVO">EFECTIVO</option>
                  <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                  <option value="DEBITO">DEBITO</option>
                  <option value="CREDITO">CREDITO</option>
                  
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Reembolso (1=Si y 0=No)</label>
                <div class="input-group">
                  
                  <input type="number" class="form-control" name="reembolso" step="1" min="0" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="text" class="form-control" name="correo">
              </div>
    
              <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" rows="2"></textarea>
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

    <!-- Modal Modificar Alumno -->
    <div class="modal fade" id="modalModificarAlumno" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form method="POST" action="modificar_alumno.php">
            <div class="modal-header bg-warning">
              <h5 class="modal-title">Modificar Alumno</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row g-3">
              <input type="hidden" name="rfc_original">

              <div class="col-md-6">
                <label class="form-label">RFC (en mayúscualas)</label>
                <input type="text" class="form-control" name="rfc_cliente" required pattern="^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$"
                  title="Debe contener 4 letras, 6 números (fecha) y 3 caracteres alfanuméricos (homoclave)">
              </div>
              <div class="col-md-6">
                <label class="form-label">Tipo de Contratación</label>
                <input type="text" class="form-control" name="tipo_contratacion" required>
                
              </div>
              <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nomb_cli" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control" name="ap_cli" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Apellido Materno</label>
                <input type="text" class="form-control" name="am_cli">
              </div>
              <div class="col-md-6">
                <label class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" name="fecha_nac" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Permiso</label>
                <input type="text" class="form-control" name="permiso">
              </div>
              <div class="col-12 mb-2">
              <hr> <!-- opcional, para una línea separadora -->
              <strong>Dirección</strong>
            </div>
              <div class="col-md-6">
                <label class="form-label">Calle</label>
                <input type="text" class="form-control" name="calle" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="text" class="form-control" name="numero" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Colonia</label>
                <input type="text" class="form-control" name="colonia" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Alcaldía</label>
                <input type="text" class="form-control" name="alcaldia" required>
              </div>
              <hr> <!-- opcional, para una línea separadora -->
                <div class="col-md-6">
                  <label class="form-label">Fecha de Pago</label>
                  <input type="date" class="form-control" name="fecha_pago">
                </div>
              <div class="col-md-6">
                <label class="form-label">Total Pago</label>
                <input type="number" class="form-control" name="total_pago" >
              </div>
              <div class="col-md-6">
                <label class="form-label">Forma de Pago</label>
                <input type="text" class="form-control" name="forma_pago">
              </div>
              <div class="col-md-6">
                <label class="form-label">Reembolso</label>
                <input type="number" class="form-control" name="reembolso" step="1" min="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="text" class="form-control" name="correo">
              </div>
              
              <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" rows="2"></textarea>
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
<!-- Modal Confirmar Eliminación Alumnos con estilo igual al modal de empleados -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">

      <!-- encabezado -->
      <div class="modal-header bg-danger text-white rounded-top-4">
        <div class="d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill fs-3 me-2"></i>
          <div>
            <h5 class="modal-title mb-0 fw-bold" id="modalEliminarLabel">Confirmar Eliminación</h5>
            <small class="fw-normal">Operación crítica - Requiere confirmación</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- cuerpo -->
      <div class="modal-body">
        <div class="d-flex align-items-start mb-3">
          <i class="bi bi-info-circle-fill text-primary fs-4 me-2 mt-1"></i>
          <div>
            <p class="fw-semibold mb-1">¿Estás seguro de eliminar los alumnos seleccionados?</p>
          </div>
        </div>
        <div class="alert alert-warning d-flex align-items-center p-2 mb-0" role="alert">
          <i class="bi bi-exclamation-circle-fill me-2"></i>
          <div>
            Esta acción no se puede deshacer y afectará los registros permanentemente.
          </div>
        </div>
      </div>

      <!-- footer -->
      <div class="modal-footer justify-content-between px-4 pb-4">
        <button type="button" class="btn btn-tertiary rounded-pill px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Cancelar
        </button>
        <button type="button" id="confirmarEliminarBtn" class="btn btn-danger rounded-pill px-4">
          <i class="bi bi-trash-fill me-1"></i> Eliminar Permanentemente
        </button>
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
          const modal = document.getElementById('modalModificarAlumno');
          modal.querySelector('input[name="rfc_cliente"]').value = celdas[1].textContent;
          modal.querySelector('input[name="tipo_contratacion"]').value = celdas[2].textContent;
          modal.querySelector('input[name="nomb_cli"]').value = celdas[3].textContent;
          modal.querySelector('input[name="ap_cli"]').value = celdas[4].textContent;
          modal.querySelector('input[name="am_cli"]').value = celdas[5].textContent;
          modal.querySelector('input[name="fecha_nac"]').value = celdas[6].textContent;
          modal.querySelector('input[name="calle"]').value = celdas[7].textContent;
          modal.querySelector('input[name="numero"]').value = celdas[8].textContent;
          modal.querySelector('input[name="colonia"]').value = celdas[9].textContent;
          modal.querySelector('input[name="alcaldia"]').value = celdas[10].textContent;
          modal.querySelector('input[name="permiso"]').value = celdas[11].textContent;
          modal.querySelector('textarea[name="observaciones"]').value = celdas[12].textContent;
          modal.querySelector('input[name="fecha_pago"]').value = celdas[13].textContent;
          modal.querySelector('input[name="total_pago"]').value = celdas[14].textContent;
          modal.querySelector('input[name="forma_pago"]').value = celdas[15].textContent;
          modal.querySelector('input[name="reembolso"]').value = celdas[16].textContent;
          modal.querySelector('input[name="correo"]').value = celdas[17].textContent;
          
          
          // Campo oculto para RFC original
          modal.querySelector('input[name="rfc_original"]').value = celdas[1].textContent;
        }
      });
    });

    // Confirmación de eliminación
document.getElementById('btnEliminar').addEventListener('click', function(e) {
    e.preventDefault();
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    modal.show();
});

document.getElementById('confirmarEliminarBtn').addEventListener('click', function() {
    // Obtener todos los checkboxes seleccionados
    const checkboxes = document.querySelectorAll('.fila-checkbox:checked');
    
    // Verificar que haya al menos uno seleccionado
    if (checkboxes.length > 0) {
        document.getElementById('formEliminar').submit();
    } else {
        // Ocultar el modal si no hay selección
        bootstrap.Modal.getInstance(document.getElementById('modalConfirmarEliminar')).hide();
    }
});

    // Búsqueda en tabla
    document.getElementById('busquedaTabla').addEventListener('input', function() {
      const valor = this.value.toLowerCase();
      document.querySelectorAll('.tabla-profesional tbody tr').forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(valor) ? '' : 'none';
      });
    });

    // Funcionalidad del menú hamburguesa
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

    // Inicializar botones
    actualizarBotones();
  });
  </script>
</body>
</html>