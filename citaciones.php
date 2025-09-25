<?php
session_start();
include 'php/config.php';
include 'php/helpers.php';

// Control de acceso para usuarios
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'user') {
    header('Location: login.php?error=acceso_restringido');
    exit();
}

$conexion = conectarDB();
$citas = [];
$mensajeEstado = '';
$idUser = $_SESSION['idUser'];

// Mensajes de estado
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'cita_creada') {
        $mensajeEstado = '<p class="mensajeExito">Cita agendada correctamente.</p>';
    } elseif ($_GET['status'] == 'cita_editada') {
        $mensajeEstado = '<p class="mensajeExito">Cita modificada correctamente.</p>';
    } elseif ($_GET['status'] == 'cita_borrada') {
        $mensajeEstado = '<p class="mensajeExito">Cita borrada correctamente.</p>';
    } elseif ($_GET['status'] == 'error_db') {
        $mensajeEstado = '<p class="mensajeError">Error de base de datos.</p>';
    } elseif ($_GET['status'] == 'error_campos') {
        $mensajeEstado = '<p class="mensajeError">Faltan campos obligatorios.</p>';
    }
}

try {
    // Obtener las citas del usuario actual
    $stmtCitas = $conexion->prepare("SELECT idCita, fecha_cita, hora_cita, motivo_cita FROM citas WHERE idUser = ? ORDER BY fecha_cita DESC, hora_cita DESC");
    $stmtCitas->bind_param("i", $idUser);
    $stmtCitas->execute();
    $resultadoCitas = $stmtCitas->get_result();
    while ($fila = $resultadoCitas->fetch_assoc()) {
        $citas[] = $fila;
    }
    $stmtCitas->close();

} catch (mysqli_sql_exception $e) {
    error_log("Error al cargar citas de usuario: " . $e->getMessage());
    $mensajeEstado = '<p class="mensajeError">Error al cargar datos.</p>';
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
    <title><?php echo OtakuTatto; ?> - Mis Citas</title>
    <link rel="stylesheet" href="css/citaciones.css">
</head>
<body>

    <?php mostrarNavegacion('user'); ?>
    
    <main>
        <h1>Mis Citas Agendadas</h1>
        <?php echo $mensajeEstado; ?>

        <div class="botonNuevaNoticia">
            <button id="agendarNuevaCita" class="botonPrincipal">Agendar Nueva Cita</button>
        </div>

        <div id="formularioNuevaCita" class="formularioNoticia" style="display: none;">
            <h2>Agendar Nueva Cita</h2>
            <form action="php/procesar_cita.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="idUser" value="<?php echo htmlspecialchars($idUser); ?>">
                <input type="hidden" name="return_to" value="citaciones.php">

                <label for="fechaCita">Fecha deseada:</label>
                <input type="date" id="fechaCita" name="fecha_cita" required min="<?php echo date('Y-m-d'); ?>">

                <label for="horaCita">Hora preferida:</label>
                <input type="time" id="horaCita" name="hora_cita" required>

                <label for="motivoCita">Motivo de la cita:</label>
                <textarea id="motivoCita" name="motivo_cita" rows="5" required></textarea>

                <button type="submit" class="botonGuardar">Agendar Cita</button>
                <button type="button" id="cancelarNuevaCita" class="botonCancelar">Cancelar</button>
            </form>
        </div>

        <div class="listadoNoticias">
            <h2>Citas Agendadas</h2>
            <?php if (empty($citas)): ?>
                <p>No tienes citas agendadas en este momento.</p>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <div class="contenedorNoticiaAdmin">
                        <article class="noticiaFija" id="citaFija-<?php echo $cita['idCita']; ?>">
                            <div class="noticiaContenido">
                                <p class="noticiaMeta">
                                    Fecha: <?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?> a las <?php echo date('h:i A', strtotime($cita['hora_cita'])); ?>
                                </p>
                                <p class="noticiaTexto">Motivo: <?php echo nl2br(htmlspecialchars($cita['motivo_cita'])); ?></p>
                            </div>
                            <div class="noticiaBotones">
                                <?php
                                $fecha_cita_pasada = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_cita']);
                                $hoy = new DateTime();
                                if ($fecha_cita_pasada > $hoy):
                                ?>
                                    <button class="editarCita botonGuardar" data-id="<?php echo htmlspecialchars($cita['idCita']); ?>">Editar</button>
                                    <form action="php/procesar_cita.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres borrar esta cita?');" style="display:inline;">
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="idCita" value="<?php echo htmlspecialchars($cita['idCita']); ?>">
                                        <input type="hidden" name="return_to" value="citaciones.php">
                                        <button type="submit" class="botonBorrar">Borrar</button>
                                    </form>
                                <?php else: ?>
                                    <p>Cita Finalizada</p>
                                <?php endif; ?>
                            </div>
                        </article>

                        <div id="citaEditable-<?php echo $cita['idCita']; ?>" class="formularioNoticia" style="display: none;">
                            <h2>Editar Cita</h2>
                            <form action="php/procesar_cita.php" method="POST">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="idCita" value="<?php echo htmlspecialchars($cita['idCita']); ?>">
                                <input type="hidden" name="return_to" value="citaciones.php">

                                <label for="fechaCitaEdit-<?php echo $cita['idCita']; ?>">Fecha:</label>
                                <input type="date" id="fechaCitaEdit-<?php echo $cita['idCita']; ?>" name="fecha_cita" value="<?php echo htmlspecialchars($cita['fecha_cita']); ?>" required min="<?php echo date('Y-m-d'); ?>">

                                <label for="horaCitaEdit-<?php echo $cita['idCita']; ?>">Hora:</label>
                                <input type="time" id="horaCitaEdit-<?php echo $cita['idCita']; ?>" name="hora_cita" value="<?php echo htmlspecialchars($cita['hora_cita']); ?>" required>

                                <label for="motivoCitaEdit-<?php echo $cita['idCita']; ?>">Motivo:</label>
                                <textarea id="motivoCitaEdit-<?php echo $cita['idCita']; ?>" name="motivo_cita" rows="5" required><?php echo htmlspecialchars($cita['motivo_cita']); ?></textarea>

                                <button type="submit" class="botonGuardar">Guardar Cambios</button>
                                <button type="button" class="cancelarEditar botonCancelar" data-id="<?php echo htmlspecialchars($cita['idCita']); ?>">Cancelar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php mostrarPieDePagina(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica para el formulario de agendar nueva cita
            const botonAgendar = document.getElementById('agendarNuevaCita');
            const formulario = document.getElementById('formularioNuevaCita');
            const botonCancelar = document.getElementById('cancelarNuevaCita');

            if (botonAgendar && formulario && botonCancelar) {
                botonAgendar.addEventListener('click', function() {
                    formulario.style.display = 'block';
                    this.style.display = 'none';
                });

                botonCancelar.addEventListener('click', function() {
                    formulario.style.display = 'none';
                    botonAgendar.style.display = 'block';
                });
            }

            // Lógica para los botones de editar y cancelar de cada cita
            const botonesEditar = document.querySelectorAll('.editarCita');
            const botonesCancelarEditar = document.querySelectorAll('.cancelarEditar');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const idCita = this.dataset.id;
                    const vistaFija = document.getElementById(`citaFija-${idCita}`);
                    const vistaEditable = document.getElementById(`citaEditable-${idCita}`);

                    if (vistaFija && vistaEditable) {
                        vistaFija.style.display = 'none';
                        vistaEditable.style.display = 'block';
                    }
                });
            });

            botonesCancelarEditar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const idCita = this.dataset.id;
                    const vistaFija = document.getElementById(`citaFija-${idCita}`);
                    const vistaEditable = document.getElementById(`citaEditable-${idCita}`);

                    if (vistaFija && vistaEditable) {
                        vistaFija.style.display = 'block';
                        vistaEditable.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>