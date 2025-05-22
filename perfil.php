<?php
session_start(); 
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer

// Redirigir si el usuario no está logueado (acceso mediante marcas, favoritos o copy&paste)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?error=no_autenticado');
    exit();
}

// Obtener el ID del usuario
$id_usuario = $_SESSION['idUser'];

// Conectar a la base de datos
$conexion = conectarDB();

// ejecutar la consulta para obtener los datos del usuario
$stmt = $conexion->prepare("SELECT ud.*, ul.usuario FROM users_data ud JOIN users_login ul ON ud.idUser = ul.idUser WHERE ud.idUser = ?");
$stmt->bind_param("i", $id_usuario);  // 'i' para entero (idUser)
$stmt->execute();
$resultado = $stmt->get_result();

$datos_usuario = null;
if ($resultado->num_rows === 1) {
    $datos_usuario = $resultado->fetch_assoc();
} else {
    //mostrar un mensaje de error
    header('Location: login.php?error=usuario_no_encontrado');
    exit();
}

// Cerrar la conexión y el statement
$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Mi Perfil</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

    <?php mostrarNavegacion('perfil'); // 'perfil' para resaltar en el nav ?>

    <main>
        <h1>Mi Perfil</h1>

        <?php
        // Mensajes de éxito o error para la edición (futura implementación)
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<p style="color: green; font-weight: bold;">¡Perfil actualizado correctamente!</p>';
            } elseif ($_GET['status'] == 'error') {
                echo '<p style="color: red; font-weight: bold;">Error al actualizar el perfil. Por favor, inténtalo de nuevo.</p>';
            }
        }
        ?>

        <div id="perfilFijo" class="perfilVisual" style="display: block;">
            <?php if ($datos_usuario): ?>
                <div class="profile-info">
                    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($datos_usuario['usuario']); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($datos_usuario['nombre']); ?></p>
                    <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($datos_usuario['apellidos']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($datos_usuario['email']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($datos_usuario['telefono']); ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($datos_usuario['fecha_nacimiento']); ?></p>
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($datos_usuario['direccion']); ?></p>
                    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($datos_usuario['sexo']); ?></p>
                    <button id="botonParaEditar" class="botonEditar">Editar Perfil</button>
                </div>
            <?php else: ?>
                <p>No se pudieron cargar tus datos. Por favor, inicia sesión de nuevo.</p>
            <?php endif; ?>
        </div>

        <div id="perfilEditable" class="formularioPerfil" style="display: none;">
            <h2>Editar Información</h2>
            <form action="php/procesar_editar_perfil.php" method="post">
                <label for="editarNombre">Nombre:</label>
                <input type="text" id="editarNombre" name="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" required>

                <label for="editarApellidos">Apellidos:</label>
                <input type="text" id="editarApellidos" name="apellidos" value="<?php echo htmlspecialchars($datos_usuario['apellidos']); ?>" required>

                <label for="editarEmail">Email:</label>
                <input type="email" id="editarEmail" name="email" value="<?php echo htmlspecialchars($datos_usuario['email']); ?>" required>
                
                <label for="editarTelefono">Teléfono:</label>
                <input type="text" id="editarTelefono" name="telefono" value="<?php echo htmlspecialchars($datos_usuario['telefono']); ?>" required>
                
                <label for="editarFechaNaciemiento">Fecha de Nacimiento:</label>
                <input type="date" id="editarFechaNaciemiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($datos_usuario['fecha_nacimiento']); ?>" required>
                
                <label for="editarDireccion">Dirección:</label>
                <input type="text" id="editarDireccion" name="direccion" value="<?php echo htmlspecialchars($datos_usuario['direccion']); ?>">

                <label for="editarSexo">Sexo:</label>
                <select id="editarSexo" name="sexo"> 
                    <option value="Masculino" <?php echo ($datos_usuario['sexo'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="Femenino" <?php echo ($datos_usuario['sexo'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                    <option value="Otro" <?php echo ($datos_usuario['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    <option value="" <?php echo (empty($datos_usuario['sexo']) || $datos_usuario['sexo'] == '') ? 'selected' : ''; ?>>No especificado</option>
                </select>

                <button type="submit" id="botonParaGuardar" class="botonGuardar">Guardar Cambios</button>
                <button type="button" id="botonParaCancelar" class="botonCancelar">Cancelar</button>
            </form>
        </div>
    </main>

    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
</body>
</html>