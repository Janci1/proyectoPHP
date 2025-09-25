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
        <title><?php echo OtakuTatto; ?> - Nosotros</title>  <link rel="stylesheet" href="css/nosotros.css">  
    </head>
    <body>

        <!-- Barra de navegación -->
        <?php mostrarNavegacion('login'); ?>
        
        <main>
            <h1>NOSOTROS</h1>
            <div class="nosotrosContenedor">
                    <div class="nosotrosContenido">
                        <img class="nosotrosImagen" src="img/empresa/studio.jpg" alt="imagen del estudio">
                        <div class="nosotrosTexto">
                        <p>¡Atención, otakus, gamers y frikis de corazón! ¿Sueñas con llevar a tu personaje favorito de anime, ese icónico símbolo de tu saga de videojuegos o un elemento único de tu universo geek en la piel? Nosotros, transformamos tu pasión en arte permanente. Olvídate de los diseños genéricos; aquí hablamos tu idioma y entendemos cada referencia. ¡Prepárate para que ese tatuaje de Dragon Ball, Pokémon, tu waifu o lo que sea que te flipe, sea tan épico como tú lo imaginas! </p><p>
                        En nuestro estudio, no solo plasmamos arte en tu piel, sino que nos dedicamos con profesionalidad y pasión a cada diseño. Entendemos que tu tatuaje es una expresión única, por eso ponemos un cuidado meticuloso en cada detalle, desde la higiene impecable hasta la precisión artística. Nuestro compromiso es que te lleves una pieza de la que te sientas orgulloso, sabiendo que cada línea y cada sombra reflejan nuestra dedicación y el amor por lo que hacemos.
                        </p>
                        </div>
                    </div>
            </div>            
        </main>

        <?php mostrarPieDePagina(); ?>
        
        <script src="js/app.js"></script>  
    </body>
</html>