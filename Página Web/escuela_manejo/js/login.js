//Limpiar datos de login
document.getElementById('loginForm').addEventListener('submit', function() {
    setTimeout(() => {
        this.reset();
    }, 50);
});
/*Redireccionar página si hay sesión 
cambios efectuados 03-06-2025*/
document.body.style.display = 'none';

/*oculta el contenido y lo deja en blanco*/
window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        // Oculta TODO el contenido de la página
        document.documentElement.style.display = 'none';
        setTimeout(() => {
            window.location.reload();
        }, 150);
    } else {
        document.body.classList.remove('no-mostrar');
    }
});





/*Ocultar y mostrar contraseña con icono
31-05-2025 login*/
function togglePassword() {
    const input = document.getElementById("clave");
    const icon = document.getElementById("eyeIcon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
