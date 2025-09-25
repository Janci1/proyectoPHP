<?php
session_start();
include 'php/config.php';
include 'php/helpers.php';

// Control de acceso
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php?error=acceso_restringido');
    exit();
}

$conexion = conectarDB();
$incidencias = [];

try {
    // Unir las tablas para obtener el nombre y email del usuario
    $query = "
        SELECT 
            i.idIncidencia, 
            i.asunto, 
            i.mensaje, 
            i.fecha_creacion, 
            ud.nombre AS nombre_usuario, 
            ud.apellidos AS apellido_usuario, 
            ud.email AS email_usuario
        FROM incidencias i
        LEFT JOIN users_data ud ON i.idUser = ud.idUser
        ORDER BY i.fecha_creacion DESC
    ";
    $resultado = $conexion->query($query);
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $incidencias[] = $fila;
        }
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error al cargar incidencias: " . $e->getMessage());
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
    <title><?php echo OtakuTatto; ?> - Incidencias</title>
    <link rel="stylesheet" href="css/incidencias.css">
</head>
<body>

    <?php mostrarNavegacion('admin'); ?>

    <main>
        <h1>Buzón de Incidencias</h1>
        <div class="listadoNoticias">
            <?php if (empty($incidencias)): ?>
                <p>No hay mensajes en el buzón en este momento.</p>
            <?php else: ?>
                <?php foreach ($incidencias as $incidencia): ?>
                    <article class="noticiaFija">
                        <div class="noticiaContenido">
                            <h2><?php echo htmlspecialchars($incidencia['asunto']); ?></h2>
                            <p class="noticiaMeta">
                                Recibido el: <?php echo date('d/m/Y', strtotime($incidencia['fecha_creacion'])); ?>
                            </p>
                            <p class="noticiaTexto"><?php echo nl2br(htmlspecialchars($incidencia['mensaje'])); ?></p>
                            <p class="noticiaMeta">
                                Enviado por: <?php echo htmlspecialchars($incidencia['nombre_usuario'] ?? 'Anónimo'); ?> (<?php echo htmlspecialchars($incidencia['email_usuario'] ?? 'N/A'); ?>)
                            </p>
                        </div>
                        <div class="noticiaBotones">
                            <form action="php/procesar_incidencia.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres borrar esta incidencia?');" style="display:inline;">
                                <input type="hidden" name="accion" value="borrar">
                                <input type="hidden" name="idIncidencia" value="<?php echo htmlspecialchars($incidencia['idIncidencia']); ?>">
                                <button type="submit" class="botonBorrar">Eliminar</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
</body>
</html>