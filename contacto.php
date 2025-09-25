<?php
session_start();
include 'php/config.php';
include 'php/helpers.php';

$mensajeEstado = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'exito') {
        $mensajeEstado = '<p class="mensajeExito">¡Gracias! Tu mensaje ha sido enviado correctamente.</p>';
    } elseif ($_GET['status'] === 'error_campos') {
        $mensajeEstado = '<p class="mensajeError">Error: Por favor, completa todos los campos correctamente.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Contacto</title>
    <link rel="stylesheet" href="css/contacto.css">
    <link rel="stylesheet" href="css/noticiasAdministracion.css">
</head>
<body>

    <?php 
    $rol_usuario = 'visitante';
    if (isset($_SESSION['rol'])) {
        $rol_usuario = $_SESSION['rol'];
    }
    mostrarNavegacion($rol_usuario);
    ?>

    <main>
        <h1>CONTACTO</h1>
        <p class="introduccion-form">
            ¿Tienes alguna duda, sugerencia o simplemente quieres saludar? ¡Estamos aquí para ti! Usa este formulario para comunicarte directamente con nuestro equipo. Responderemos tan pronto como nos sea posible.
        </p>

        <?php echo $mensajeEstado; ?>
        
        <form action="php/procesar_contacto_db.php" method="POST" class="formulario-contacto">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="asunto">Asunto:</label>
            <input type="text" id="asunto" name="asunto" required>

            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="8" required></textarea>

            <button type="submit" class="botonPrincipal">Enviar Mensaje</button>
        </form>
    </main>

    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
</body>
</html>