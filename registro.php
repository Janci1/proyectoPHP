<?php
session_start();
include 'php/config.php'; // Incluye el archivo de configuración
include 'php/helpers.php'; //navegador y footer
?>

<!DOCTYPE html> <!-- Inicio del documento HTML -->
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo OtakuTatto; ?> - Registro</title>
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>

    <?php mostrarNavegacion('login'); ?>
    
    <main>
        <h1>Registro de Usuario</h1>

        <?php
        if (isset($_SESSION['errores_registro'])): ?>
            <div class="alert-error">
                <?php foreach ($_SESSION['errores_registro'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['errores_registro']); // Limpia la sesión para el próximo intento ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_registro'])): ?>
            <div class="alert-success">
                <p><?php echo htmlspecialchars($_SESSION['mensaje_registro']); ?></p>
            </div>
            <?php unset($_SESSION['mensaje_registro']); ?>
        <?php endif; ?>
            <form action="php/procesar_registro.php" method="post" id="registroForm">
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

                <button type="submit">Registrarse</button>
                <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
            </form>
    </main>

    <?php mostrarPieDePagina(); ?> 

    <script src="js/app.js"></script>
    <script src="js/registro.js"></script>
</body>
</html>