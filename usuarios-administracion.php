<?php
session_start();
include 'php/config.php';
include 'php/helpers.php';

// === Protección de la página ===
// Redirigir si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?error=no_autenticado');
    exit();
}// Redirigir si el usuario no tiene rol de administrador
if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php?error=acceso_denegado');
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
    <link rel="stylesheet" href="css/usuariosAdministracion.css"> 
</head>
<body>
    <?php mostrarNavegacion('usuarios-administracion'); ?>

    <main>
        <h1>Administración de Usuarios</h1>
        <div class="contenedor">
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
                case 'usuario_creado':
                    echo '<p class="mensaje-exito"> El usuario ha sido creado correctamente.</p>';
                    break;
                case 'error_campos':
                    echo '<p class="mensaje-error"> Faltan campos obligatorios en el formulario.</p>';
                    break;
                case 'email_existe':
                    echo '<p class="mensaje-error"> El correo electrónico ya está registrado. Por favor, utiliza otro.</p>';
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
            <button id="botonCrearUsuario" class="botonNuevo">Crear Usuario</button>        

            <div id="formularioCrearUsuario">
                <h2>Crear Nuevo Usuario</h2>
                    <form action="php/procesar_usuarios.php" method="post" id="registroForm">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                        <span id="error-nombre" class="error-message"></span>

                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" required>
                        <span id="error-apellidos" class="error-message"></span>

                        <label for="usuario">Nombre de Usuario:</label>
                        <input type="text" id="usuario" name="usuario" required>
                        <span id="error-usuario" class="error-message"></span>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                        <span id="error-email" class="error-message"></span>
                        
                        <label for="telefono">Telefono:</label>
                        <input type="tel" id="telefono" name="telefono">
                        <span id="error-telefono" class="error-message"></span>

                        <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        <span id="error-fecha" class="error-message"></span>

                        <label for="direccion">Direccion:</label>
                        <input type="text" id="direccion" name="direccion">
                        
                        <label for="sexo">Sexo:</label>
                        <select id="sexo" name="sexo"> 
                            <option value="">Selecciona...</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                        
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                        <span id="error-password" class="error-message"></span>

                        <label for="password_confirm">Confirmar Contraseña:</label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                        <span id="error-password_confirm" class="error-message"></span>
                        
                        <button type="submit" class="botonGuardar">Crear Usuario</button>
                        <button type="button" id="cancelarCrearUsuario" class="botonCancelar">Cancelar</button>
                    </form>
            </div>
        </form>
        </div>
    </main>

    <?php mostrarPieDePagina(); ?>

    <script src="js/app.js"></script>
    <script src="js/registro.js"></script>
    <script src="js/confirmacion.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonCrear = document.getElementById('botonCrearUsuario');
            const formulario = document.getElementById('formularioCrearUsuario');
            const botonCancelar = document.getElementById('cancelarCrearUsuario');
            
            botonCrear.addEventListener('click', function() {
                formulario.style.display = 'block';
                botonCrear.style.display = 'none';
            });
            
            botonCancelar.addEventListener('click', function() {
                formulario.style.display = 'none';
                botonCrear.style.display = 'block';
            });
        });
    </script>
</body>
</html>