<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer

// === Protección de la página ===
// Redirigir si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?error=no_autenticado');
    exit();
}// Redirigir si el usuario no tiene rol de administrador
if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php?error=acceso_denegado'); // O a una página de error de acceso
    exit();
}

// Conectar a la base de datos
$conexion = conectarDB();

// Obtener todos los usuarios con sus datos personales y rol
$stmt = $conexion->prepare("SELECT ud.idUser, ud.nombre, ud.apellidos, ud.email, ud.telefono, ul.usuario, ul.rol FROM users_data ud JOIN users_login ul ON ud.idUser = ul.idUser ORDER BY ul.usuario ASC");
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($fila = $resultado->fetch_assoc()) {
    $usuarios[] = $fila;
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Administración de Usuarios</title>
    <link rel="stylesheet" href="css/estilos.css"> 
</head>
<body>
    <?php mostrarNavegacion('usuarios-administracion'); ?>

    <main class="contenedor">
        <h1>Administración de Usuarios</h1>

         <?php
        if (isset($_GET['status'])) {
            $status = htmlspecialchars($_GET['status']);
            echo '<div class="mensaje-estado">';
            switch ($status) {
                case 'usuario_borrado':
                    echo '<p class="mensaje-exito"> El usuario ha sido borrado correctamente.</p>';
                    break;
                case 'cambios_guardados':
                    echo '<p class="mensaje-exito"> Los cambios se han guardado correctamente.</p>';
                    break;
                case 'no_accion':
                    echo '<p class="mensaje-info"> No se realizó ninguna acción.</p>';
                    break;
                case 'error_db':
                    echo '<p class="mensaje-error"> Hubo un error en la base de datos.</p>';
                    break;
                case 'acceso_denegado':
                    echo '<p class="mensaje-error"> Acceso denegado. No tienes permisos para ver esta página.</p>';
                    break;
                default:
                    echo '<p class="mensaje-info"> Estado desconocido.</p>';
            }
            echo '</div>';
        }
        ?>

        <form action="php/procesar_usuarios.php" method="POST">
            <table class="tablaUsuarios">
                <thead>
                    <tr class="encabezadoUsuarios">
                        <th>Usuario (Login)</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="cuerpoUsuarios">
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>                                
                                <td>
                                    <?php echo htmlspecialchars($usuario['usuario']); ?>
                                </td>
                                <td>
                                    <input type="text" name="usuarios[<?php echo $usuario['idUser']; ?>][nombre]" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </td>
                                <td>
                                    <input type="email" name="usuarios[<?php echo $usuario['idUser']; ?>][email]" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="usuarios[<?php echo $usuario['idUser']; ?>][telefono]" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" required>
                                </td>
                                <td>
                                    <select name="usuarios[<?php echo $usuario['idUser']; ?>][rol]">
                                        <option value="user" <?php echo ($usuario['rol'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                                        <option value="admin" <?php echo ($usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="borrar_usuario" value="<?php echo $usuario['idUser']; ?>" class="botonBorrarUsuario">Borrar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="submit" name="guardar_cambios" class="botonGuardar">Guardar Cambios</button>
        </form>
        
        <a href="crear-usuario.php" class="botonNuevo">Crear Nuevo Usuario</a>


    </main>

        <?php mostrarPieDePagina(); ?>
        <script src="js/confirmacion.js"></script>  

    </body>
</html>