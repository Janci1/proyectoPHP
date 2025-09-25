<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer
$conexion = conectarDB();

$noticias = []; // Array para almacenar las noticias
$mensajeEstado = ''; //mensajes de error

if (isset($_GET['status'])) {
    $status = htmlspecialchars($_GET['status']);
    switch ($status) {
        case 'success':
            $mensajeEstado = '<p class="mensajeExito">Noticia creada</p>';
            break;
        case 'error':
            $mensajeEstado = '<p class="mensajeError">Ha ocurrido un error.</p>';
            break;
        case 'noticia_borrada':
            $mensajeEstado = '<p class="mensajeExito">Noticia borrada correctamente.</p>';
            break;
        case 'noticia_actualizada':
            $mensajeEstado = '<p class="mensajeExito">Noticia actualizada correctamente.</p>';
            break;
        case 'noticia_creada':
            $mensajeEstado = '<p class="mensajeExito">Nueva noticia creada correctamente.</p>';
            break;
        case 'error_campos':
            $mensajeEstado = '<p class="mensajeError">Error: Faltan campos obligatorios.</p>';
            break;
        case 'error_id_noticia':
            $mensajeEstado = '<p class="mensajeError">Error: ID de noticia no proporcionado.</p>';
            break;
        case 'error_db':
            $mensajeEstado = '<p class="mensajeError">Error de base de datos</p>';
            break;
        case 'error_imagen_tipo':
            $mensajeEstado = '<p class="mensajeError">Error: Formato de imagen no válido. Solo se permiten JPG, JPEG, PNG y GIF.</p>';
            break;
        case 'error_imagen_tamano':
            $mensajeEstado = '<p class="mensajeError">Error: La imagen es demasiado grande. El tamaño máximo permitido es de 5MB.</p>';
            break;
        case 'error_imagen_subida':
            $mensajeEstado = '<p class="mensajeError">Error: No se pudo subir la imagen.</p>';
            break;
        case 'error_noticia_no_encontrada':
            $mensajeEstado = '<p class="mensajeError">Error: No se encontró la noticia a actualizar.</p>';
            break;
        default:
            // Sin mensaje de estado
            break;
    }
}

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
    error_log("Error al cargar noticias en administración: " . $e->getMessage());
    $mensajeEstado = '<p class="mensajeError">Error al cargar las noticias. Contacta al administrador.</p>';
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
    <title><?php echo OtakuTatto; ?> - Administrar noticias</title>     <link rel="stylesheet" href="css/noticiasAdministracion.css">
</head>
<body>

        <?php mostrarNavegacion('admin'); ?>
    
    <main>
        <h1>Administración de Noticias</h1>

        <?php echo $mensajeEstado; // Mostrar mensajes de estado aquí ?>

        <div class="botonNuevaNoticia">
            <button id="nuevaNoticia" class="botonPrincipal">Crear Nueva Noticia</button>
        </div>

        <div id="formularioNoticia" class="formularioNoticia" style="display: none;">
            <h2>Crear Nueva Noticia</h2>
            <form action="php/procesar_noticia.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="idUser" value="<?php echo htmlspecialchars($_SESSION['idUser'] ?? ''); ?>"> 

                <label for="nuevoTitulo">Título:</label>
                <input type="text" id="nuevoTitulo" name="titulo" required>

                <label for="nuevoTexto">Contenido:</label>
                <textarea id="nuevoTexto" name="texto" rows="10" required></textarea>
                
                <label for="nuevaImagen">Seleccionar Foto:</label>
                <input type="file" name="imagen" id="nuevaImagen">

                <button type="submit" class="botonGuardar">Guardar Noticia</button>
                <button type="button" id="cancelarNoticia" class="botonCancelar">Cancelar</button>
            </form>
        </div>

        <div class="listadoNoticias">
            <?php if (empty($noticias)): ?>
                <p>No hay noticias para administrar en este momento.</p>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): ?>
                    <div class="contenedorNoticiaAdmin">
                        <article class="noticiaFija" id="noticiaFija-<?php echo $noticia['idNoticia']; ?>">
                            <img class="noticiaImagen" src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            <div class="noticiaContenido">
                                <h2><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                                <p class="noticiaTexto"><?php echo nl2br(htmlspecialchars($noticia['texto'])); ?></p>
                                <p class="noticiaMeta">
                                    Publicado el: <?php echo date('d/m/Y', strtotime($noticia['fecha'])); ?>
                                    <?php if (!empty($noticia['autor_nombre'])): ?>
                                        por: <?php echo htmlspecialchars($noticia['autor_nombre'] . ' ' . $noticia['autor_apellido']); ?>
                                    <?php endif; ?>
                                </p>
                                <div class="noticiaAcciones">
                                    <button class="editarNoticia botonSecundario" data-id="<?php echo $noticia['idNoticia']; ?>">Editar</button>
                                    <form action="php/procesar_noticia.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="idNoticia" value="<?php echo $noticia['idNoticia']; ?>">
                                        <button type="submit" class="botonBorrarNoticia botonPeligro">Borrar</button>
                                    </form>
                                </div>
                            </div>
                        </article>

                        <div class="noticiaEditable" id="noticiaEditable-<?php echo $noticia['idNoticia']; ?>" style="display: none;">
                            <h2>Editar Noticia</h2>
                            <form action="php/procesar_noticia.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="hidden" name="idNoticia" value="<?php echo htmlspecialchars($noticia['idNoticia']); ?>">

                                <label for="editarTitulo-<?php echo $noticia['idNoticia']; ?>">Título:</label>
                                <input type="text" id="editarTitulo-<?php echo $noticia['idNoticia']; ?>" name="titulo" value="<?php echo htmlspecialchars($noticia['titulo']); ?>" required>

                                <label for="editarTexto-<?php echo $noticia['idNoticia']; ?>">Contenido:</label>
                                <textarea id="editarTexto-<?php echo $noticia['idNoticia']; ?>" name="texto" rows="10" required><?php echo htmlspecialchars($noticia['texto']); ?></textarea>
                                
                                <label for="editarImagen-<?php echo $noticia['idNoticia']; ?>">Cambiar Foto:</label>
                                <input type="file" name="imagen" id="editarImagen-<?php echo $noticia['idNoticia']; ?>">

                                <button type="submit" class="botonGuardar">Guardar Cambios</button>
                                <button type="button" class="cancelarEditar botonCancelar" data-id="<?php echo $noticia['idNoticia']; ?>">Cancelar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php mostrarPieDePagina(); ?>
    <script>
        // DEBUG: Muestra el ID de usuario de la sesión que se renderizó en el HTML
        const idUserSesion = "<?php echo htmlspecialchars($_SESSION['idUser'] ?? 'NULL_O_VACIO_PHP'); ?>";
        console.log("DEBUG Noticias Admin (JS): ID de usuario de la sesión: ", idUserSesion);
    </script>

    <script src="js/app.js"></script>
    <script src="js/noticias.js"></script>
</body>
</html>