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
$citas = [];
$usuarios = [];
$mensajeEstado = '';

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
    // Obtener usuarios
    $stmtUsuarios = $conexion->prepare("SELECT idUser, nombre, apellidos FROM users_data WHERE idUser IN (SELECT idUser FROM users_login WHERE rol = 'user') ORDER BY nombre");
    $stmtUsuarios->execute();
    $resultadoUsuarios = $stmtUsuarios->get_result();
    while ($fila = $resultadoUsuarios->fetch_assoc()) {
        $usuarios[] = $fila;
    }
    $stmtUsuarios->close();

    // Obtener todas las citas
    $stmtCitas = $conexion->prepare("SELECT c.idCita, c.fecha_cita, c.hora_cita, c.motivo_cita, ud.nombre, ud.apellidos FROM citas c JOIN users_data ud ON c.idUser = ud.idUser ORDER BY c.fecha_cita DESC, c.hora_cita DESC");
    $stmtCitas->execute();
    $resultadoCitas = $stmtCitas->get_result();
    while ($fila = $resultadoCitas->fetch_assoc()) {
        $citas[] = $fila;
    }
    $stmtCitas->close();

} catch (mysqli_sql_exception $e) {
    error_log("Error al cargar citas de administracion: " . $e->getMessage());
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
    <title><?php echo OtakuTatto; ?> - Administrar Citas</title>
    <link rel="stylesheet" href="css/noticiasAdministracion.css">
</head>
<body>

    <?php mostrarNavegacion('admin'); ?>
    
    <main>
        <h1>Administrar Citas</h1>
        <?php echo $mensajeEstado; ?>

        <div class="botonNuevaNoticia">
            <button id="agendarCitaAdmin" class="botonPrincipal">Agendar Nueva Cita</button>
        </div>

        <div id="formularioCita" class="formularioNoticia" style="display: none;">
            <h2>Agendar Cita para Usuario</h2>
            <form action="php/procesar_cita.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="return_to" value="citas-administracion.php">

                <label for="selectUser">Seleccionar Usuario:</label>
                <select id="selectUser" name="idUser" required>
                    <?php if (empty($usuarios)): ?>
                        <option value="">No hay usuarios</option>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo htmlspecialchars($usuario['idUser']); ?>">
                                <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <label for="fechaCita">Fecha deseada:</label>
                <input type="date" id="fechaCita" name="fecha_cita" required min="<?php echo date('Y-m-d'); ?>">

                <label for="horaCita">Hora preferida:</label>
                <input type="time" id="horaCita" name="hora_cita" required>

                <label for="motivoCita">Motivo de la cita:</label>
                <textarea id="motivoCita" name="motivo_cita" rows="5" required></textarea>

                <button type="submit" class="botonGuardar">Agendar Cita</button>
                <button type="button" id="cancelarCita" class="botonCancelar">Cancelar</button>
            </form>
        </div>

        <div class="listadoNoticias">
            <h2>Todas las Citas Agendadas</h2>
            <?php if (empty($citas)): ?>
                <p>No hay citas agendadas en este momento.</p>
            <?php else: ?>
                <?php foreach ($citas as $cita): ?>
                    <div class="contenedorNoticiaAdmin">
                                                <article class="noticiaFija" id="citaFija-<?php echo $cita['idCita']; ?>">
                            <div class="noticiaContenido">
                                <h3>Para: <?php echo htmlspecialchars($cita['nombre'] . ' ' . $cita['apellidos']); ?></h3>
                                <p class="noticiaMeta">
                                    Fecha: <?php echo date('d/m/Y', strtotime($cita['fecha_cita'])); ?> a las <?php echo date('h:i A', strtotime($cita['hora_cita'])); ?>
                                </p>
                                <p class="noticiaTexto">Motivo: <?php echo nl2br(htmlspecialchars($cita['motivo_cita'])); ?></p>
                            </div>
                            <div class="noticiaBotones">
                                <button class="editarCita botonGuardar" data-id="<?php echo htmlspecialchars($cita['idCita']); ?>">Editar</button>
                                <form action="php/procesar_cita.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres borrar esta cita?');" style="display:inline;">
                                    <input type="hidden" name="accion" value="borrar">
                                    <input type="hidden" name="idCita" value="<?php echo htmlspecialchars($cita['idCita']); ?>">
                                    <input type="hidden" name="return_to" value="citas-administracion.php">
                                    <button type="submit" class="botonBorrar">Borrar</button>
                                </form>
                            </div>
                        </article>

                                                <div id="citaEditable-<?php echo $cita['idCita']; ?>" class="formularioNoticia" style="display: none;">
                            <h2>Editar Cita</h2>
                            <form action="php/procesar_cita.php" method="POST">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="idCita" value="<?php echo htmlspecialchars($cita['idCita']); ?>">
                                <input type="hidden" name="return_to" value="citas-administracion.php">

                                <label for="fechaCitaEdit">Fecha:</label>
                                <input type="date" id="fechaCitaEdit" name="fecha_cita" value="<?php echo htmlspecialchars($cita['fecha_cita']); ?>" required>

                                <label for="horaCitaEdit">Hora:</label>
                                <input type="time" id="horaCitaEdit" name="hora_cita" value="<?php echo htmlspecialchars($cita['hora_cita']); ?>" required>

                                <label for="motivoCitaEdit">Motivo:</label>
                                <textarea id="motivoCitaEdit" name="motivo_cita" rows="5" required><?php echo htmlspecialchars($cita['motivo_cita']); ?></textarea>

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
            // Agendar cita
            const botonAgendar = document.getElementById('agendarCitaAdmin');
            const formulario = document.getElementById('formularioCita');
            const botonCancelar = document.getElementById('cancelarCita');

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

            // Editar y cancelar
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