<?php
session_start();
include 'php/config.php';
include 'php/helpers.php';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?error=no_autenticado');
    exit();
}

// Redirigir si el usuario no tiene rol de administrador
if ($_SESSION['rol'] !== 'admin') {
    header('Location: index.php?error=acceso_denegado'); // O a una página de error de acceso
    exit();
}

// Mensajes de estado (de procesar_registro.php)
$mensaje = '';
$clase_mensaje = '';
if (isset($_GET['status'])) {
    $status = htmlspecialchars($_GET['status']);
    switch ($status) {
        case 'success':
            $mensaje = 'Usuario creado correctamente.';
            $clase_mensaje = 'mensaje-exito';
            break;
        case 'error_campos':
            $mensaje = 'Error: Por favor, rellena todos los campos obligatorios.';
            $clase_mensaje = 'mensaje-error';
            break;
        case 'error_email_existente':
            $mensaje = 'Error: El email ya está registrado.';
            $clase_mensaje = 'mensaje-error';
            break;
        case 'error_usuario_existente':
            $mensaje = 'Error: El nombre de usuario ya existe.';
            $clase_mensaje = 'mensaje-error';
            break;
        case 'error_db':
            $mensaje = 'Error de base de datos. Inténtalo de nuevo.';
            $clase_mensaje = 'mensaje-error';
            break;
        default:
            $mensaje = 'Ha ocurrido un error desconocido.';
            $clase_mensaje = 'mensaje-info';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Crear Nuevo Usuario</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <?php mostrarNavegacion('crear-usuario'); ?>

    <main class="contenedor">
        <h1>Crear Nuevo Usuario</h1>

        <?php if ($mensaje): ?>
            <div class="mensaje-estado <?php echo $clase_mensaje; ?>">
                <p><?php echo $mensaje; ?></p>
            </div>
        <?php endif; ?>

        <form action="php/procesar_crear_usuario.php" method="POST" class="formulario-registro">
            <input type="hidden" name="desde_admin" value="true"> <label for="usuario">Nombre de Usuario (Login):</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono">

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion">

            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo">
                <option value="">Selecciona</option>
                <option value="Masculino">Masculino</option>
                <option value="Femenino">Femenino</option>
                <option value="Otro">Otro</option>
            </select>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="user">Usuario Normal</option>
                <option value="admin">Administrador</option>
            </select>

            <button type="submit" class="botonGuardar">Crear Usuario</button>
        </form>
    </main>

    <?php mostrarPieDePagina(); ?>
</body>
</html>