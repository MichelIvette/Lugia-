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



// Variables para mensajes
$errorMensaje = "";
$exitoMensaje = "";
$accion = "";  // Variable para controlar la acción realizada

// Procesar formulario de agregar empleado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar"])) {
    $accion = "agregar";

    $rfc = $_POST["rfc"];
    $nombre = $_POST["nombre"];
    $ap = $_POST["ap"];
    $am = $_POST["am"];
    $puesto = $_POST["puesto"];
    $turno = $_POST["turno"];
    // Convertir arreglo de descansos a string
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
        // Primero verificar si el RFC ya existe
        $sql_verificar = "SELECT COUNT(*) FROM Empleados WHERE RFC_EMP = ?";
        $stmt_verificar = $pdo->prepare($sql_verificar);
        $stmt_verificar->execute([$rfc]);
        
        if ($stmt_verificar->fetchColumn() > 0) {
            // El RFC ya existe, mostrar mensaje específico
            $errorMensaje = "El RFC $rfc ya está registrado en el sistema.";
        } else {
            // El RFC no existe, proceder con la inserción
            $sql = "INSERT INTO Empleados (RFC_EMP, NOMB_EMP, AP_EMP, AM_EMP, PUESTO, TURNO, DESCANSOS, SEXO, FECHA_NAC, TEL_PERSONAL, CALLE, NUMERO, COLONIA, ALCALDIA)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $rfc, $nombre, $ap, $am, $puesto, $turno, $descansos, $sexo,
                $fecha_nac, $tel, $calle, $numero, $colonia, $alcaldia
            ]);
            $exitoMensaje = "Empleado agregado exitosamente.";
        }
    } catch (PDOException $e) {
        // Manejar otros posibles errores de la base de datos
        $errorMensaje = "Error al procesar la solicitud: " . $e->getMessage();
    }
}

// Procesar eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
    $accion = "eliminar";

    $rfcSeleccionados = $_POST['eliminar_rfc'] ?? [];

    if (!empty($rfcSeleccionados)) {
        try {
            // Crear placeholders (?, ?, ?, ...) según cuántos RFCs haya
            $placeholders = implode(',', array_fill(0, count($rfcSeleccionados), '?'));

            $sql = "DELETE FROM Empleados WHERE RFC_EMP IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($rfcSeleccionados);

            $exitoMensaje = "Empleado(s) eliminado(s) exitosamente.";
        } catch (PDOException $e) {
            $errorMensaje = "Error al eliminar empleado(s): " . $e->getMessage();
        }
    } else {
        $errorMensaje = "No se seleccionaron empleados para eliminar.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Empleados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="icon" href="img/icono.png" type="image/png"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <link rel="stylesheet" href="css/site.css"/>
</head>
<body>
  <div class="wrapper">
    <?php include 'menu.php'; ?>
    <main class="main-content" id="mainContent">
      
      <?php //Mensaje de modificar_empleados.php
        if (isset($_SESSION["mensaje"])) {
            echo $_SESSION["mensaje"];
            unset($_SESSION["mensaje"]);
        }
      ?>

      <!--mensajes de Errores al crear-->
      <?php if ($errorMensaje): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($errorMensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

        <!--Mensajes de éxito al crear-->
        <?php if ($exitoMensaje): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($exitoMensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
        <?php endif; ?>

      <div class="welcome">
        <h2>Empleados</h2>
        <div class="contenedor-scroll">
          <form method="POST" id="formEliminar">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <button type="submit" id="btnEliminar" name="eliminar" class="btn btn-danger fab-eliminar" title="Eliminar empleados seleccionados" style="display:none;">
                <i class="fas fa-user-minus"></i>
              </button>

              <button type="button" class="btn btn-success fab-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregarEmpleado" title="Agregar empleado">
                <i class="fas fa-user-plus"></i>
              </button>

              
               <!-------------------------------------------------------------->
              <button type="button" class="btn btn-success fab-modificar" id="btnModificar" data-bs-toggle="modal" data-bs-target="#modalmodificarEmpleado" title="Modificar empleado" style="display:none;">
                <i class="fas fa-edit"></i>
              </button>
            </div>
            <!-----------------BUSQUEDA EN TABLA------------------------------------>
               <div class="mb-3">
                  <input type="text" id="busquedaTabla" class="form-control" placeholder="Búsqueda general en la tabla...">
                </div>

            <!-------------------------------------------------------------->
            <table border="1" class="table table-striped tabla-profesional">
              <thead class="table-dark">
                <tr>
                  <th></th>
                  <th>RFC</th>
                  <th>Nombre(s)</th>
                  <th>Apellido paterno</th>
                  <th>Apellido materno</th>
                  <th>Puesto</th>
                  <th>Turno</th>
                  <th>Días de descanso</th>
                  <th>Sexo</th>
                  <th>Fecha de nacimiento</th>
                  <th>Teléfono personal</th>
                  <th>Dirección:<br>Calle</th>
                  <th>Número</th>
                  <th>Colonia</th>
                  <th>Alcaldía</th>
                </tr>
              </thead>
              <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT RFC_EMP, NOMB_EMP, AP_EMP, AM_EMP, PUESTO, TURNO, DESCANSOS, SEXO, FECHA_NAC, TEL_PERSONAL, CALLE, NUMERO, COLONIA, ALCALDIA FROM Empleados");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td><input type='checkbox' class='fila-checkbox' name='eliminar_rfc[]' value='" . htmlspecialchars($row['RFC_EMP']) . "'></td>";
                        foreach ($row as $valor) {
                            echo "<td>" . htmlspecialchars($valor) . "</td>";
                        }
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='15' class='text-danger'>Error al cargar empleados: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </form>
        </div>
      </div>
    </main>

    <footer>
      <p>&copy; 2025 Start & Go. Todos los derechos reservados.</p>
    </footer>
<!-- Modal Modificar Empleado -->
<div class="modal fade" id="modalmodificarEmpleado" tabindex="-1" aria-labelledby="modalmodificarEmpleadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="formModificarEmpleado" method="POST" action="modificar_empleado.php">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="modalmodificarEmpleadoLabel">Modificar Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="rfc" class="form-label">RFC (en mayúsculas)</label>
              <input type="text" class="form-control" name="rfc" required readonly>
            </div>
            <div class="col-md-4">
              <label for="nombre" class="form-label">Nombre(s)</label>
              <input type="text" class="form-control" name="nombre" required
                pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                title="Solo letras y espacios (mínimo 2 caracteres)">
            </div>
            <div class="col-md-4">
              <label for="ap" class="form-label">Apellido Paterno</label>
              <input type="text" class="form-control" name="ap" required
                    pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                title="Solo letras y espacios (mínimo 2 caracteres)">
            </div>
            <div class="col-md-4">
              <label for="am" class="form-label">Apellido Materno</label>
              <input type="text" class="form-control" name="am" required
                  pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
                  title="Solo letras y espacios (mínimo 2 caracteres)">
            </div>
            <div class="col-md-4">
              <label for="puesto" class="form-label">Puesto</label>
              <input type="text" class="form-control" name="puesto" required>
            </div>
            <div class="col-md-4">
              <label for="turno" class="form-label">Turno</label>
              <select class="form-select" name="turno" required>
                <option value=""disabled selected></option>
                <option value="MATUTINO">Matutino</option>
                <option value="VESPERTINO">Vespertino</option>
                
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Días de Descanso</label>
              <div class="d-flex flex-wrap gap-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="LUNES" id="desc_lun">
                  <label class="form-check-label" for="desc_lun">Lunes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="MARTES" id="desc_mar">
                  <label class="form-check-label" for="desc_mar">Martes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="MIERCOLES" id="desc_mie">
                  <label class="form-check-label" for="desc_mie">Miércoles</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="JUEVES" id="desc_jue">
                  <label class="form-check-label" for="desc_jue">Jueves</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="VIERNES" id="desc_vie">
                  <label class="form-check-label" for="desc_vie">Viernes</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="SABADO" id="desc_sab">
                  <label class="form-check-label" for="desc_sab">Sábado</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="descansos[]" value="DOMINGO" id="desc_dom">
                  <label class="form-check-label" for="desc_dom">Domingo</label>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <label for="sexo" class="form-label">Sexo</label>
              <select class="form-select" name="sexo" required>
                <option value=""disabled selected></option>
                <option value="MASCULINO">Masculino</option>
                <option value="FEMENINO">Femenino</option>
              </select>
            </div>
           <div class="col-md-4">
              <label for="fecha_nac" class="form-label">Fecha de Nacimiento</label>
              <input type="date" 
                  class="form-control" 
                  name="fecha_nac" 
                  value="<?= htmlspecialchars($_POST['fecha_nac'] ?? '') ?>" 
                  min="1900-01-01"                     
                  max="<?= date('Y-m-d') ?>"             
                  required
                  oninvalid="this.setCustomValidity('Verifique, la fecha introducida no es correcta')"
                  oninput="this.setCustomValidity('')">
            </div>
            <div class="col-md-4">
              <label for="tel" class="form-label">Teléfono</label>
              <input type="text" class="form-control" name="tel"
                  value="<?=htmlspecialchars($_POST['tel'] ?? '')?>"
                  required
                  pattern="^\d{10}$"
                  maxlength="10"
                  title="Debe contener exactamente 10 dígitos numéricos">
            </div>
            <!-- Encabezado de sección -->
            <div class="col-12 mb-2">
              <hr> <!-- opcional, para una línea separadora -->
              <strong>Dirección</strong>
            </div>
            <div class="col-md-4">
              <label for="calle" class="form-label">Calle</label>
              <input type="text" class="form-control" name="calle" required>
            </div>
            <div class="col-md-4">
              <label for="numero" class="form-label">Número</label>
              <input type="text" class="form-control" name="numero" required>
            </div>
            <div class="col-md-4">
              <label for="colonia" class="form-label">Colonia</label>
              <input type="text" class="form-control" name="colonia" required>
            </div>
            <div class="col-md-4">
              <label for="alcaldia" class="form-label">Alcaldía</label>
              <input type="text" class="form-control" name="alcaldia" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-tertiary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- Modal para agregar empleado -->
<div class="modal fade" id="modalAgregarEmpleado" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header bg-warning">
          <h5 class="modal-title" id="tituloModal">Agregar Nuevo Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <!-- Mensajes de error o éxito -->
        <div class="modal-body row g-3">

          <?php if (!empty($errorMensaje) && $accion === "agregar"): ?>
            <div class="alert alert-danger w-100" role="alert">
              <?= htmlspecialchars($errorMensaje) ?>
            </div>
          <?php elseif (!empty($exitoMensaje) && $accion === "agregar"): ?>
            <div class="alert alert-success w-100" role="alert">
              <?= htmlspecialchars($exitoMensaje) ?>
            </div>
          <?php endif; ?>

          <!-- Campos del formulario -->
          <div class="col-md-6">
            <label class="form-label">RFC (en mayúsculas)</label>
            <input type="text" class="form-control" name="rfc" required
            pattern="^[A-ZÑ&]{4}\d{6}[A-Z0-9]{3}$"
            title="Debe contener 4 letras, 6 números (fecha) y 3 caracteres alfanuméricos (homoclave)">
          </div>
          <div class="col-md-6"><label class="form-label">Nombre(s)</label><input type="text" class="form-control" name="nombre" required
          pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
          title="Solo letras y espacios (mínimo 2 caracteres)"></div>

          <div class="col-md-6"><label class="form-label">Apellido Paterno</label><input type="text" class="form-control" name="ap" required pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
          title="Solo letras y espacios (mínimo 2 caracteres)">
          </div>
          <div class="col-md-6"><label class="form-label">Apellido Materno</label><input type="text" class="form-control" name="am" required
          pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]{2,}$"
          title="Solo letras y espacios (mínimo 2 caracteres)">
          </div>
          <div class="col-md-6"><label class="form-label">Puesto</label><input type="text" class="form-control" name="puesto" required
          pattern="^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]{2,}$"
          title="Solo se admiten caracteres del a-z"></div>
          <div class="col-md-6">
            <label class="form-label">Turno</label>
            <select name="turno" class="form-select" required>
              <option value=""disabled selected></option>
              <option value="MATUTINO" <?php if (($_POST['turno'] ?? '') === 'MATUTINO') echo 'selected'; ?>>MATUTINO</option>
              <option value="VESPERTINO" <?php if (($_POST['turno'] ?? '') === 'VESPERTINO') echo 'selected'; ?>>VESPERTINO</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label d-block">Días de descanso</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="LUNES" id="descLunes" <?php if (in_array('LUNES', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descLunes">Lunes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="MARTES" id="descMartes" <?php if (in_array('MARTES', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descMartes">Martes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="MIERCOLES" id="descMiercoles" <?php if (in_array('MIERCOLES', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descMiercoles">Miércoles</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="JUEVES" id="descJueves" <?php if (in_array('JUEVES', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descJueves">Jueves</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="VIERNES" id="descViernes" <?php if (in_array('VIERNES', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descViernes">Viernes</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="SABADO" id="descSabado" <?php if (in_array('SABADO', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descSabado">Sábado</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="descansos[]" value="DOMINGO" id="descDomingo" <?php if (in_array('DOMINGO', $_POST['descansos'] ?? [])) echo 'checked'; ?>>
              <label class="form-check-label" for="descDomingo">Domingo</label>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Sexo</label>
            <select name="sexo" class="form-select" required>
              <option value=""disabled selected></option>
              <option value="MASCULINO" <?php if (($_POST['sexo'] ?? '') === 'MASCULINO') echo 'selected'; ?>>MASCULINO</option>
              <option value="FEMENINO" <?php if (($_POST['sexo'] ?? '') === 'FEMENINO') echo 'selected'; ?>>FEMENINO</option>
            </select>
          </div>
          <div class="col-md-6">
              <label class="form-label">Fecha de nacimiento</label>
              <input 
                  type="date" 
                  class="form-control" 
                  name="fecha_nac" 
                  value="<?= htmlspecialchars($_POST['fecha_nac'] ?? '') ?>" 
                  min="1900-01-01"                     
                  max="<?= date('Y-m-d') ?>"             
                  required
                  oninvalid="this.setCustomValidity('Verifique, La fecha introducida no es correcta')"
                  oninput="this.setCustomValidity('')">
          </div>
          <div class="col-md-6">
            <label class="form-label">Teléfono personal</label>
            <input type="text" class="form-control" name="tel"
                  value="<?=htmlspecialchars($_POST['tel'] ?? '')?>"
                  required
                  pattern="^\d{10}$"
                  maxlength="10"
                  title="Debe contener exactamente 10 dígitos numéricos">
          </div>
          <!-- Encabezado de sección -->
            <div class="col-12 mb-2">
              <hr> <!-- opcional, para una línea separadora -->
              <strong>Dirección</strong>
            </div>
          <div class="col-md-6"><label class="form-label">Calle</label><input type="text" class="form-control" name="calle" value="<?=htmlspecialchars($_POST['calle'] ?? '')?>" required></div>
          <div class="col-md-3"><label class="form-label">Número</label><input type="text" class="form-control" name="numero" value="<?=htmlspecialchars($_POST['numero'] ?? '')?>" required></div>
          <div class="col-md-6"><label class="form-label">Colonia</label><input type="text" class="form-control" name="colonia" value="<?=htmlspecialchars($_POST['colonia'] ?? '')?>" required></div>
          <div class="col-md-6"><label class="form-label">Alcaldía</label><input type="text" class="form-control" name="alcaldia" value="<?=htmlspecialchars($_POST['alcaldia'] ?? '')?>" required></div>

        </div>

        <div class="modal-footer">
          
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="agregar" class="btn btn-tertiary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>


  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/site.js"></script>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      
      <!-- encabezado de recuadro eliminar  -->
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

      <!-- Cuerpo de eliminar -->
      <div class="modal-body">
        <div class="d-flex align-items-start mb-3">
          <i class="bi bi-info-circle-fill text-primary fs-4 me-2 mt-1"></i>
          <div>
            <p class="fw-semibold mb-1">¿Estás seguro de querer eliminar los registros seleccionados?</p>
            
          </div>
        </div>
        <div class="alert alert-warning d-flex align-items-center p-2 mb-0" role="alert">
          <i class="bi bi-exclamation-circle-fill me-2"></i>
          <div>
            Esta acción no se puede deshacer y afectará los registros permanentemente.
          </div>
        </div>
      </div>

      <div class="modal-footer justify-content-between px-4 pb-4">
        <button type="button" class="btn btn-tertiary rounded-pill px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1 "></i> Cancelar
        </button>
        <button type="button" id="confirmarEliminarBtn" class="btn btn-danger rounded-pill px-4">
          <i class="bi bi-trash-fill me-1"></i> Eliminar Permanentemente
        </button>
      </div>
    </div>
  </div>
</div>
</body>
</html>