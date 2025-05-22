<?php
include 'config.php'; // Incluye el archivo de configuración DB

// Recibir los datos del formulario
$usuario_ingresado = $_POST['usuario'] ?? '';
$password_ingresada = $_POST['password'] ?? '';

// Validar que los campos no estén vacíos
if (empty($usuario_ingresado) || empty($password_ingresada)) {
    header('Location: ../login.php?error=campos_vacios');
    exit();
}

// Conectar a la base de datos
$conexion = conectarDB();

// Buscar el usuario en la tabla users_login
$stmt = $conexion->prepare("SELECT ul.idUser, ul.usuario, ul.password, ul.rol FROM users_login ul WHERE ul.usuario = ?");
$stmt->bind_param("s", $usuario_ingresado);
$stmt->execute();
$resultado = $stmt->get_result(); // Obtener los resultados de la consulta

if ($resultado->num_rows === 1) {
    // Si el usuario existe, verificar la contraseña
    $usuario_db = $resultado->fetch_assoc(); // Obtener los datos del usuario de la DB

    // verificar contraseñas encriptadas
    if (password_verify($password_ingresada, $usuario_db['password'])) {
        // Contraseña correcta: Iniciar sesión
        session_start(); 

        $_SESSION['loggedin'] = true; // Variable para saber si el usuario está logueado
        $_SESSION['idUser'] = $usuario_db['idUser']; // Guardar el ID del usuario en la sesión
        $_SESSION['usuario'] = $usuario_db['usuario']; // Guardar el nombre de usuario
        $_SESSION['rol'] = $usuario_db['rol']; // Guardar el rol del usuario

        //Redirigir según el rol
        if ($_SESSION['rol'] === 'admin') {
            header('Location: ../index.php'); // Página para administradores 
        } else { // Rol 'user'
            header('Location: ../noticias.php'); // Página de perfil de usuario 
        }
        exit();

    } else {
        // Contraseña incorrecta
        header('Location: ../login.php?error=credenciales_invalidas');
        exit();
    }
} else {
    // Usuario no encontrado
    header('Location: ../login.php?error=credenciales_invalidas');
    exit();
}
?>