<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer

$conexion = conectarDB();

$noticias = []; // Array para almacenar las noticias

try {
    // Preparar la consulta para las noticias, ordenadas por alguna especiificacion.
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
        ORDER BY noticias.fecha DESC;
    ");
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $noticias[] = $row;
        }
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    error_log("Error al cargar noticias: " . $e->getMessage());
    
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
    <title><?php echo OtakuTatto; ?> - Noticias</title> 
    <link rel="stylesheet" href="css/noticias.css">
</head>
<body>

    <!-- Barra de navegación -->
    <?php mostrarNavegacion('login'); ?>
    
    <main>
        <h1>NOTICIAS</h1>
        <?php if (empty($noticias)): ?>
            <p>No hay noticias disponibles en este momento.</p>
            <!-- Como no habia noticias en la base de datos puse esto -->
        <?php else: ?>
            <?php foreach ($noticias as $noticia): ?> <!-- para cada noticia que encuentre -->
                <div class="contenedorNoticia">
                    <article class="noticia">
                        <img  class="noticiaImagen" src="<?php echo htmlspecialchars($noticia['imagen']); ?>"  alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                        <!-- ubicacion de la imagen y descripcion de la misma usando titulo como referencia -->
                        <div class="noticiaContenido">
                            <!--el titulo de la notica desde la base de datos -->
                            <h2><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                            
                            <!--el contenido de la notica desde la base de datos -->
                            <p class="noticiaTexto"><?php echo nl2br(htmlspecialchars($noticia['texto'])); ?></p>
                            
                            <!--la fecha y el autor de la notica desde la base de datos -->
                            <p class="noticiaMeta">
                                Publicado el: <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                <?php if (!empty($noticia['autor_nombre'])): ?>
                                    por: <?php echo htmlspecialchars($noticia['autor_nombre'] . ' ' . $noticia['autor_apellido']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </article>
                    </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <!-- footer -->
    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
</body>
</html>