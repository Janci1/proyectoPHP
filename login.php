<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo OtakuTatto; ?> - Login</title> <!-- Establece el título de la página, usando una constante PHP -->
        <link rel="stylesheet" href="../css/estilos.css"> 
    </head>
    <body>

        <!-- Barra de navegación -->
        <?php mostrarNavegacion('login'); ?>
        
        <main> 
            <h1>Inicio de Sesión</h1> 

            <?php
            // Verificar si hay un mensaje de error en la URL
            if (isset($_GET['error'])) {
                $error_message = '';
                switch ($_GET['error']) {
                    case 'credenciales_invalidas':
                        $error_message = 'Usuario o contraseña incorrectos. Por favor, inténtalo de nuevo.';
                        break;
                    case 'db_fallo':
                        $error_message = 'Hubo un problema al intentar iniciar sesión. Por favor, inténtalo de nuevo más tarde.';
                        break;
                    case 'no_autenticado': // Para cuando un usuario no logueado intenta acceder a una página restringida
                        $error_message = 'Necesitas iniciar sesión para acceder a esta página.';
                        break;
                    default:
                        $error_message = 'Ha ocurrido un error desconocido.';
                        break;
                }
                echo '<p style="color: red; font-weight: bold;">' . $error_message . '</p>';
            }

            // Verificar si viene de un registro exitoso
            if (isset($_GET['registro']) && $_GET['registro'] === 'exito') {
                echo '<p style="color: green; font-weight: bold;">¡Registro exitoso! Ya puedes iniciar sesión.</p>';
            }
            ?>

            <form action="php/procesar_login.php" method="post">  <!-- Formulario para el inicio de sesión. Los datos se enviarán a 'procesar_login.php' -->
                <label for="usuario">Usuario:</label> 
                <input type="text" id="usuario" name="usuario" required> 

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Iniciar Sesión</button>
                <p>¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
            </form>
        </main>

        <?php mostrarPieDePagina(); ?>

        <script src="js/app.js"></script> 
    </body>
</html>