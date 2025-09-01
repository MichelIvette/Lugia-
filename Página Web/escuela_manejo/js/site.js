// Mensaje de bienvenida
setTimeout(() => {
  const bienvenida = document.getElementById('bienvenida');
  if (bienvenida) {
    bienvenida.classList.remove('show');
    setTimeout(() => bienvenida.remove(), 500);
  }
}, 3000);

// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
const mainContent = document.getElementById('mainContent');

if (menuToggle && sidebar && mainContent) {
  menuToggle.addEventListener('click', function () {
    const isActive = sidebar.classList.toggle('active');
    mainContent.classList.toggle('sidebar-open', isActive);
    document.body.classList.toggle('no-scroll', isActive);
  });

  document.addEventListener('click', function (event) {
    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
      sidebar.classList.remove('active');
      mainContent.classList.remove('sidebar-open');
      document.body.classList.remove('no-scroll');
    }
  });
}

// Funcionalidad principal
document.addEventListener('DOMContentLoaded', function() {
  // Elementos del DOM
  const formEliminar = document.getElementById('formEliminar');
  const btnEliminar = document.getElementById('btnEliminar');
  const btnModificar = document.getElementById('btnModificar');
  const checkboxes = document.querySelectorAll('.fila-checkbox');
  const modalConfirmarEliminar = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
  const confirmarEliminarBtn = document.getElementById('confirmarEliminarBtn');
  const busquedaTabla = document.getElementById('busquedaTabla');

  // Función para mostrar/ocultar botones según selección
  function actualizarBotones() {
    const seleccionados = document.querySelectorAll('.fila-checkbox:checked');
    btnEliminar.style.display = seleccionados.length > 0 ? 'inline-block' : 'none';
    btnModificar.style.display = seleccionados.length === 1 ? 'inline-block' : 'none';
  }

  // Event listeners para checkboxes
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', actualizarBotones);
  });

  // Event listener para el botón eliminar (mostrar modal)
  if (btnEliminar) {
    btnEliminar.addEventListener('click', function(e) {
      e.preventDefault();
      modalConfirmarEliminar.show();
    });
  }

  // Event listener para confirmar eliminación
  if (confirmarEliminarBtn) {
    confirmarEliminarBtn.addEventListener('click', function() {
      // Agregar el campo hidden para indicar la acción de eliminar
      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = 'eliminar';
      hiddenInput.value = '1';
      formEliminar.appendChild(hiddenInput);
      
      formEliminar.submit();
    });
  }

  // Cargar datos para modificación cuando se selecciona una fila
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const seleccionados = document.querySelectorAll('.fila-checkbox:checked');
      
      if (seleccionados.length === 1) {
        const fila = this.closest('tr');
        const celdas = fila.querySelectorAll('td');

        const datosEmpleado = {
          rfc: celdas[1].innerText.trim(),
          nombre: celdas[2].innerText.trim(),
          ap: celdas[3].innerText.trim(),
          am: celdas[4].innerText.trim(),
          puesto: celdas[5].innerText.trim(),
          turno: celdas[6].innerText.trim(),
          descansos: celdas[7].innerText.trim().split(','),
          sexo: celdas[8].innerText.trim(),
          fecha_nac: celdas[9].innerText.trim(),
          tel: celdas[10].innerText.trim(),
          calle: celdas[11].innerText.trim(),
          numero: celdas[12].innerText.trim(),
          colonia: celdas[13].innerText.trim(),
          alcaldia: celdas[14].innerText.trim()
        };

        // Poblar el formulario del modal
        document.querySelector('#modalmodificarEmpleado input[name="rfc"]').value = datosEmpleado.rfc;
        document.querySelector('#modalmodificarEmpleado input[name="nombre"]').value = datosEmpleado.nombre;
        document.querySelector('#modalmodificarEmpleado input[name="ap"]').value = datosEmpleado.ap;
        document.querySelector('#modalmodificarEmpleado input[name="am"]').value = datosEmpleado.am;
        document.querySelector('#modalmodificarEmpleado input[name="puesto"]').value = datosEmpleado.puesto;
        document.querySelector('#modalmodificarEmpleado select[name="turno"]').value = datosEmpleado.turno;
        document.querySelector('#modalmodificarEmpleado select[name="sexo"]').value = datosEmpleado.sexo;
        document.querySelector('#modalmodificarEmpleado input[name="fecha_nac"]').value = datosEmpleado.fecha_nac;
        document.querySelector('#modalmodificarEmpleado input[name="tel"]').value = datosEmpleado.tel;
        document.querySelector('#modalmodificarEmpleado input[name="calle"]').value = datosEmpleado.calle;
        document.querySelector('#modalmodificarEmpleado input[name="numero"]').value = datosEmpleado.numero;
        document.querySelector('#modalmodificarEmpleado input[name="colonia"]').value = datosEmpleado.colonia;
        document.querySelector('#modalmodificarEmpleado input[name="alcaldia"]').value = datosEmpleado.alcaldia;

        // Actualizar checkboxes de descanso
        const dias = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
        dias.forEach(dia => {
          const cb = document.querySelector(`#modalmodificarEmpleado input[type="checkbox"][value="${dia}"]`);
          if (cb) cb.checked = datosEmpleado.descansos.includes(dia);
        });
      }
    });
  });

  // Búsqueda en tabla
  if (busquedaTabla) {
    busquedaTabla.addEventListener('input', function() {
      const input = this.value.toLowerCase();
      const rows = document.querySelectorAll('.tabla-profesional tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
      });
    });
  }

  // Ejecutar la función al cargar por si ya hay checkboxes seleccionados
  actualizarBotones();
});