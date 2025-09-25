<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer

$conexion = conectarDB();

$ultimas_noticias = []; // Array para almacenar las noticias

try {
    $stmt = $conexion->prepare("
        SELECT
            noticias.idNoticia,
            noticias.titulo,
            noticias.imagen,
            noticias.texto,
            noticias.fecha,
            users_data.nombre AS autor_nombre,
            users_data.apellidos AS autor_apellido
        FROM noticias
        JOIN users_data ON noticias.idUser = users_data.idUser
        ORDER BY noticias.fecha DESC
        LIMIT 2;
    ");
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $ultimas_noticias[] = $row;
        }
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    error_log("Error al cargar las últimas noticias en index.php: " . $e->getMessage());
} finally {
    if ($conexion) {
        $conexion->close();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo OtakuTatto; ?> - Inicio</title>  
        <link rel="stylesheet" href="css/index.css">  
    </head>
    <body>

        <!-- Barra de navegación -->
        <?php mostrarNavegacion('login'); ?>
        
        <main>
            <section class="galeria">
                <h1>NUESTROS TRABAJOS</h1>
                <div class="imagenesGaleria">
                    <img src="img/tattoos/onePiece1.png"
                         alt="One Piece"
                         data-titulo="One Piece"
                         data-desc="Nuestros tatuajes de One Piece son personales a gusto de nuestros clientes"
                         data-sub1="img/tattoos/onePiece2.png"
                         data-sub2="img/tattoos/onePiece3.png"
                         data-sub3="img/tattoos/onePiece4.png">
                    <img src="img/tattoos/Bleach1.png" 
                         alt="Bleach"
                         data-titulo="Bleach"
                         data-desc="Colecciona el orgullo de la Sociedad de Almas con nuestros diseños de Bleach."
                         data-sub1="img/tattoos/Bleach2.png"
                         data-sub2="img/tattoos/Bleach3.png"
                         data-sub3="img/tattoos/Bleach4.png">
                    <img src="img/tattoos/Kimetsu1.png" 
                         alt="Kimetsu no Yaiba"
                         data-titulo="Kimetsu no Yaiba"
                         data-desc="Llevas un pedazo de la valentía del Cuerpo de Exterminador de Demonios."
                         data-sub1="img/tattoos/Kimetsu2.png"
                         data-sub2="img/tattoos/Kimetsu3.png"
                         data-sub3="img/tattoos/Kimetsu4.png">
                    <img src="img/tattoos/naruto1.png" 
                         alt="Naruto"
                         data-titulo="Naruto"
                         data-desc="Nuestros diseños de Naruto están hechos para que muestres tu propia historia."
                         data-sub1="img/tattoos/naruto2.png"
                         data-sub2="img/tattoos/naruto3.png"
                         data-sub3="img/tattoos/naruto4.png">
                </div>
            </section>
            <section class="ultimasNoticias">
                <h1>NOTICIAS</h1>
                <?php if (!empty($ultimas_noticias)): ?>
                    <div class="noticiasContainer">
                        <?php foreach ($ultimas_noticias as $noticia): ?>
                            <article class="noticiaResumen">
                                <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" 
                                     class="noticiaImagen">
                                <div class="noticiaContenido">
                                    <h3><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                    <p><?php echo nl2br(htmlspecialchars(substr($noticia['texto'], 0, 600))) . '...'; // Mostrar los primeros 150 caracteres ?></p>                                    
                                    <a href="noticias.php#noticia-<?php echo $noticia['idNoticia']; ?>" class="leer-mas">Leer más</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No hay noticias recientes disponibles en este momento.</p>
                <?php endif; ?>
            </section>
            <section class="nosotros">
                <h1>NOSOTROS</h1>
                <div class="nosotrosContenedor">
                    <div class="nosotrosContenido">
                        <img src="img/empresa/studio.jpg" alt="imagen del estudio">
                        <div class="nosotrosTexto">
                        <p>¡Atención, otakus, gamers y frikis de corazón! ¿Sueñas con llevar a tu personaje favorito de anime, ese icónico símbolo de tu saga de videojuegos o un elemento único de tu universo geek en la piel? Nosotros, transformamos tu pasión en arte permanente. Olvídate de los diseños genéricos; aquí hablamos tu idioma y entendemos cada referencia. ¡Prepárate para que ese tatuaje de Dragon Ball, Pokémon, tu waifu o lo que sea que te flipe, sea tan épico como tú lo imaginas! </p><p>
                        En nuestro estudio, no solo plasmamos arte en tu piel, sino que nos dedicamos con profesionalidad y pasión a cada diseño. Entendemos que tu tatuaje es una expresión única, por eso ponemos un cuidado meticuloso en cada detalle, desde la higiene impecable hasta la precisión artística. Nuestro compromiso es que te lleves una pieza de la que te sientas orgulloso, sabiendo que cada línea y cada sombra reflejan nuestra dedicación y el amor por lo que hacemos.
                        </p>
                        </div>
                    </div>
                </div>
                <div class="nosotrosContenedor">
                    <h1>NUESTROS LOCALES</h1>
                    <div >
                        <article class="nosotrosContenido">
                            <img src="img/empresa/studioLA.jpeg" alt="Local en Los Ángeles">
                            <div class="nosotrosTexto">
                                <p> Ubicado en el corazón de Los Ángeles, Tinta Celestial es nuestro local insignia en la costa oeste. Con un diseño vanguardista inspirado en el arte callejero y la cultura pop, es el lugar perfecto para los amantes del anime y los cómics. Nuestros artistas especializados en tatuajes de estilo japonés y neo-tradicional están listos para dar vida a tus personajes favoritos.</p>
                            </div>
                        </article>

                        <article class="nosotrosContenido">
                            <img src="img/empresa/studioBarcelona.png" alt="Local en Barcelona" class="imagen-local">
                            <div class="nosotrosTexto">
                                <p>En el vibrante barrio Gótico de Barcelona, se encuentra "Dragón de Papel". Este estudio, con un ambiente acogedor y un toque rústico, se ha convertido en un referente para los diseños delicados y minimalistas. Nuestros artistas fusionan el arte del tatuaje tradicional con elementos del manga, creando piezas únicas que cuentan una historia.</p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>
        </main>

        <?php mostrarPieDePagina(); ?>

        <div id="galleryModal" class="modal">
            <span class="closeButton">&times;</span>
            <div class="modalContent">
                <span class="modal-arrow left" id="prevButton">&#10094;</span> 
                
                <img id="modalMainImage" src="" alt="Imagen principal">
                
                <span class="modal-arrow right" id="nextButton">&#10095;</span> 
                <div class="textoModal">
                    <h3 id="modalTitle"></h3>
                    <p id="modalDescription"></p>
                </div>
            </div>
        </div>
        
        <script src="js/app.js"></script>
        <script src="js/galeria.js"></script> 
    </body>

</html>