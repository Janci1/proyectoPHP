🎨 OtakuTattoo - Gestión de Citas y Noticias
🎯 Finalidad del Proyecto
OtakuTattoo es una aplicación web full-stack diseñada para gestionar un estudio de tatuajes de forma eficiente. Este proyecto permite a los usuarios registrarse y solicitar citas, mientras que los administradores pueden gestionar usuarios, administrar noticias y blog, y manejar todas las solicitudes. La finalidad es digitalizar y optimizar los procesos de un estudio de tatuajes, mejorando la comunicación con los clientes y la organización interna.

✨ Características Principales
Gestión de Usuarios: Roles de usuario diferenciados (admin y cliente) para controlar el acceso y las funcionalidades.

Sistema de Citas: Los clientes pueden solicitar y gestionar sus citas.

Módulo de Noticias: Un blog o sección de noticias donde los administradores pueden crear, editar y eliminar publicaciones con imágenes.

Validación de Formularios: Uso de validación tanto en el lado del cliente como en el del servidor para garantizar la integridad de los datos.

Control de Versiones: Proyecto gestionado con Git y alojado en GitHub.

Estructura del Proyecto: Código limpio y modular, con separación de lógica de presentación.

🛠️ Tecnologías y Herramientas Utilizadas
Este proyecto pone en práctica una serie de tecnologías y buenas prácticas de desarrollo web, combinando lo siguiente:

Frontend:

HTML5 y CSS3: Para la estructura y el diseño de la interfaz de usuario.

JavaScript: Para la interactividad, validación de formularios en el cliente y la manipulación del DOM.

Backend:

PHP: Como lenguaje de programación del lado del servidor.

MySQL: Como sistema de gestión de bases de datos relacional para almacenar toda la información del proyecto (usuarios, noticias, citas, etc.).

Herramientas:

Git y GitHub: Para el control de versiones y colaboración.

XAMPP / MAMP: Para crear un entorno de servidor local (Apache y MySQL).

🚀 Cómo Poner en Marcha el Proyecto en Local
Sigue estos sencillos pasos para tener una copia del proyecto funcionando en tu entorno local.

1. Requisitos Previos
Necesitas tener instalado un entorno de servidor local que incluya PHP y MySQL. Las opciones más comunes son:

XAMPP: Para Windows, macOS y Linux.

MAMP: Para macOS.

WAMP: Para Windows.

Además, necesitarás un editor de código como Visual Studio Code y un navegador web.

2. Instalación
Clonar el Repositorio:
Abre tu terminal o Git Bash y ejecuta el siguiente comando para clonar el proyecto en tu máquina.

git clone [https://github.com/tu-usuario/nombre-de-tu-repositorio.git](https://github.com/tu-usuario/nombre-de-tu-repositorio.git)

Configurar la Base de Datos:

Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP/MAMP.

Accede a phpMyAdmin desde tu navegador (normalmente en http://localhost/phpmyadmin).

Crea una nueva base de datos llamada otakutattoo.

Importa el archivo otakutattoo.sql que se encuentra en la carpeta raíz de este repositorio.

Configurar la Conexión a la Base de Datos:

Ve a la carpeta php/ dentro de tu proyecto.

Abre el archivo config.php con tu editor de código.

Edita las credenciales de la base de datos si es necesario (el nombre de usuario y la contraseña por defecto de XAMPP/MAMP suelen ser root y una cadena vacía, respectivamente).

<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Tu nombre de usuario
define('DB_PASSWORD', ''); // Tu contraseña
define('DB_NAME', 'otakutattoo');
define('OtakuTatto', 'OtakuTatto');
?>

Colocar el Proyecto en el Servidor Local:

Copia todos los archivos y carpetas del proyecto.

Pégalos en el directorio raíz de tu servidor web:

XAMPP: C:\xampp\htdocs\ (Windows) o /Applications/XAMPP/htdocs/ (macOS).

MAMP: /Applications/MAMP/htdocs/.

Abrir el Proyecto en tu Navegador:

Abre tu navegador web y ve a la dirección http://localhost/nombre-del-proyecto/.

¡Listo! El proyecto debería estar funcionando.

📄 Estructura del Proyecto
css/: Hojas de estilo CSS.

img/: Imágenes y otros recursos visuales del proyecto.

js/: Archivos JavaScript para la funcionalidad del frontend.

php/: Scripts PHP para la lógica del backend, conexiones a la base de datos y procesamiento de datos.

index.php: La página principal del sitio.

login.php y registro.php: Páginas de autenticación de usuarios.

noticias-administracion.php: Página para la gestión del blog y noticias.

usuarios-administracion.php: Página para la gestión de usuarios por el administrador.

... y otros archivos correspondientes a las vistas de tu aplicación.
