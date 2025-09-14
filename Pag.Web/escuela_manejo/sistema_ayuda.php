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
  <title>Ayuda</title>
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




      <h1 class="mb-4 text-center">Sistema de Ayuda</h1>


      <section class="mb-5">
        <h2>¿Cómo usar el sistema?</h2>
        <p><strong>Start & Go</strong> es un sistema diseñado para optimizar la operación y administración de la escuela de manejo con una interfaz intuitiva y funcionalidades robustas que permiten tener control total sobre los procesos clave del centro educativo, garantizando eficiencia, seguridad y una mejor experiencicia de servicio.</p>

        
      </section>

      <section class="mb-5">
        <h2>Preguntas Frecuentes (Administradores)</h2>
        <div class="accordion" id="faqAccordion">

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq1-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
                 ¿Cómo puedo llevar un control eficiente del personal que labora?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" aria-labelledby="faq1-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Para gestionar correctamente a los empleados de la empresa, es necesario iniciar sesión con un usuario con privilegios de administrador, una vez dentro del sistema, dirígete al apartado de <strong>"Empleados"</strong>  desde el menú principal, dentro podrás visualizar los registros y realizar las siguientes acciones:

                <ul><li>Registrar nuevos empleados, ingresando su información personal y laboral.</li>
                <li>Editar datos existentes, en caso de que sea necesario actualizar información como el puesto, turno, dirección, entre otros.</li>

                <li>Eliminar empleados, si han dejado de laborar en la empresa, asegurando así que los registros se mantengan actualizados.</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq2-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
               ¿Es posible modificar los registros una vez creados?

              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
               Sí, es posible realizar modificaciones en todos los registros del sistema; para hacerlo, debes seleccionar el registro que deseas actualizar, hacer clic en el botón de editar, realizar los cambios necesarios y luego presionar guardar cambios. Después, verás un mensaje que confirma que las modificaciones se han realizado correctamente.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq3-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                ¿La interfaz del sistema luce diferente al usarlo en navegadores distintos?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                El sistema está diseñado para ser responsivo y funcionar correctamente en todo tipo de dispositivos y navegadores; sin embargo, debido a las constantes actualizaciones de los navegadores, pueden presentarse algunas diferencias en la visualización, por lo que recomendamos utilizar Google Chrome para garantizar una mejor experiencia de uso.
              </div>
            </div>
          </div>


          <div class="accordion-item">
            <h2 class="accordion-header" id="faq3-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq3">
                ¿Cómo funciona la búsqueda en las tablas?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faq3-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                La búsqueda en las tablas funciona filtrando en tiempo real los datos que coinciden con las palabras ingresadas en el campo de búsqueda; esta búsqueda se realiza en cada una de las celdas, por lo que los resultados mostrados corresponden a los registros que contienen alguna coincidencia.
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq3-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq3">
                ¿Cómo genero un archivo de los estados de cuenta?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" aria-labelledby="faq3-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Para generar un archivo con los estados de cuenta, primero debes ingresar a la sección de reportes, seleccionar el mes del que deseas obtener el documento y luego hacer clic en el botón ubicado al lado derecho, esto abrirá una nueva pestaña con los datos actualizados hasta el momento. Es importante tener en cuenta que la información mostrada corresponde a los registros de la tabla de clientes.
              </div>
            </div>
          </div>
        </div>
</section>

<section class="mb-5">
        <h2>Preguntas Frecuentes (Recepcionistas)</h2>
        <div class="accordion" id="faqAccordion">

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq1-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq101" aria-expanded="false" aria-controls="faq1">
                 ¿Cómo actualizo el tipo de contratación de un cliente?
              </button>
            </h2>
            <div id="faq101" class="accordion-collapse collapse" aria-labelledby="faq1-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Para actualizar el tipo de contratación de un cliente en específico, primero debes seleccionar la fila del registro correspondiente y hacer clic en el botón de editar; luego, ingresa la nueva información en el campo correspondiente y, finalmente, haz clic en guardar cambios para aplicar la actualización.
              </div>
            </div>
          </div>


          <div class="accordion-item">
            <h2 class="accordion-header" id="faq1-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq102" aria-expanded="false" aria-controls="faq1">
                 ¿Qué hacer cuando un alumno no presenta su examen de manejo?
              </button>
            </h2>
            <div id="faq102" class="accordion-collapse collapse" aria-labelledby="faq1-header" data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Si un cliente no se presenta a su examen de manejo, es necesario actualizar su registro en el sistema, indicando en el apartado de resultados que no se presentó y que, por lo tanto, no aprobó el examen. Además, se recomienda dejar una anotación en el campo de notas para dejar constancia de la situación y facilitar un posible seguimiento o reprogramación.
              </div>
            </div>
          </div>
</section>




      <section class="contacto-soporte bg-light p-4 rounded shadow-sm mb-5">
  <h2 class="text-center mb-4"><i class="fas fa-headset text-primary"></i> Contacto y Soporte</h2>
  <p class="text-center">¿Tienes dudas o necesitas ayuda? Nuestro equipo de soporte está disponible para asistirte:</p>

        <center>
          <strong>Horario de atención:</strong> Lunes a Viernes, 9:00 a.m. - 6:00 p.m.
        </li>
        <li class="list-group-item">
          <i class="fas fa-envelope text-primary me-2"></i>
          <strong>Email:</strong> <a href="mailto:soporte@startandgo.com">soporte@gmail.com</a>
        </li>
        <li class="list-group-item">
          <i class="fas fa-phone text-primary me-2"></i>
          <strong>Teléfono:</strong> <a href="tel:+525512345678">55 1234 5678</a>
        </li>
        
      </ul>
      </center>

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
