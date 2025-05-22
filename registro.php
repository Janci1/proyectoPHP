<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer
?>

<!DOCTYPE html> <!-- Inicio del documento HTML -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Registro</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <!-- Barra de navegación -->
    <?php mostrarNavegacion('login'); ?>
    
    <main>
        <h1>Registro de Usuario</h1>

        <?php
        // Verificar si hay un mensaje de error en la URL
        if (isset($_GET['error'])) {
            $error_message = '';
            switch ($_GET['error']) {
                case 'campos_obligatorios':
                    $error_message = 'Por favor, rellena todos los campos obligatorios.';
                    break;
                case 'email_o_usuario_existente':
                    $error_message = 'El email o el nombre de usuario ya están registrados.';
                    break;
                case 'db_fallo':
                    $error_message = 'Hubo un problema al intentar registrarte. Por favor, inténtalo de nuevo más tarde.';
                    break;
                default:
                    $error_message = 'Ha ocurrido un error desconocido.';
                    break;
            }
            echo '<p style="color: red; font-weight: bold;">' . $error_message . '</p>';
        }
        ?>

        <form action="php/procesar_registro.php" method="post">  <!-- Formulario que envía datos a 'procesar_registro.php' -->
            <label for="nombre">Nombre:</label>    
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>
            
            <label for="usuario">Nombre de Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="telefono">Telefono:</label>
            <input type="text" id="telefono" name="telefono" required>
            
            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            
            <label for="direccion">Direccion:</label>
            <input type="text" id="direccion" name="direccion" required>

            <select id="sexo" name="sexo"> 
                <option value="">Selecciona...</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
            </select>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registrarse</button>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        </form>
    </main>

    <?php mostrarPieDePagina(); ?> 

    <script src="js/app.js"></script>
</html>