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
    <title><?php echo OtakuTatto; ?> - Noticias</title> <!-- Muestra el nombre del sitio (definido en config.php) en el título de la página -->
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <!-- Barra de navegación -->
    <?php mostrarNavegacion('login'); ?>
    
    <main>
        <h1>Noticias</h1>
        </main>

    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
</body>
</html>