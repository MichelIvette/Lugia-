<h1 align="center">Lugia</h1>   

**START & GO**: una escuela de manejo.

## Descripción
Es una plataforma web diseñada para una escuela de manejo. Su objetivo es gestionar de forma eficiente las operaciones administrativas de la escuela de manejo **START & GO**.

---

## Requisitos del Sistema

**Software necesario:**
- PHP 8.4.4
- MySQL Workbench 8.0
- Navegador moderno (Chrome, Firefox, Edge)
- Servidor local (IIS sobre Windows recomendado)
- Git + GitHub 

**Configuraciones necesarias en `php.ini`:**
```ini
extension=php_gd2.dll
extension=php_mbstring.dll
```
> Estas extensiones son necesarias para la generación de PDFs y el manejo correcto de caracteres especiales.

---

## Tecnologías Utilizadas  

- PHP: Backend, conexión a base de datos, sesiones, controladores.  
- JavaScript (JS): Comportamiento dinámico, validaciones y control de la interfaz.  
- HTML: Estructura y contenido del sistema.  
- CSS + Bootstrap: Diseño visual de la interfaz. 
- MySQL: Gestión de la base de datos.  
- IIS (Internet Information Services): Servidor web sobre Windows.

---

## Tablas de la Base de Datos

- **empleados**: Información del personal operativo y administrativo.
- **clientes**: Datos de personas inscritas para cursos.
- **usuarios**: Para nombres de usuarios y contraseñas.
- **contratacion**: Los paquetes de clases que se ofertan.
- **agenda**: Citas, horarios y clases programadas.

 **Diagrama E-R del sistema:**  
[Ver Diagrama E-R]([https://github.com/MichelIvette/WebEqipo11/blob/main/ESCUELA-DE-MANEJO/Documentaci%C3%B3n_t%C3%A9cnica/Diagrama%20E-R.png](https://github.com/MichelIvette/Lugia-/blob/main/Pag.Web/BasesDeDatos/Diagrama%20E-R.jpg))

---

## Funcionalidades Principales

- Registro, inicio de sesión y control de acceso por rol.
- Gestión de empleados: agregar, modificar y eliminar personal.
- Administración de autos: asignar vehículos y controlar disponibilidad.
- Registro y seguimiento de clientes (alumnos).
- Agenda: programación y consulta de horarios para las clases.
- Contrataciones: Paquetes ofertados.
- Generación de estados de cuenta en PDF.
- Generación de gráficas de reportes de clases y exámenes.
- Modo claro/oscuro adaptable a preferencias del usuario.
- Sistema de ayuda.
-  Conexión a la base de datos: El sistema establece una conexión centralizada a la base de datos MySQL a través del archivo conexion.php, ubicado en el directorio principal del proyecto. Este archivo contiene los parámetros necesarios para autenticar la conexión con el servidor y es reutilizado por todos los módulos del sistema.  Ver conexión.php (https://github.com/MichelIvette/Lugia-/blob/main/Pag.Web/escuela_manejo/conexion.php)

---

## Autores

- [@Ivette](https://github.com/MichelIvette)
- [@Michelle](https://github.com/ItsMichh) 
- [@Osva](https://github.com/Osvadeb)
- [@José Carlos](https://github.com/)  

---

## Estado del proyecto

En desarrollo activo  
Última actualización: septiembre 2025  

---

## Licencia


Este proyecto es de uso educativo bajo los términos del **MIT License**.
