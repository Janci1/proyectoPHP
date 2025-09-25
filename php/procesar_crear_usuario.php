<?php
session_start();
include 'config.php'; 
include 'helpers.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php?error=acceso_denegado'); // Redirigir a index o login
    exit();
}

// Recibir los datos del formulario
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$rol = $_POST['rol'] ?? 'user'; 

// Asegurarse de que el rol es 'user' o 'admin'
if ($rol !== 'user' && $rol !== 'admin') {
    $rol = 'user'; // Fallback seguro
}


// Validar campos 
if (empty($usuario) || empty($password) || empty($nombre) || empty($apellidos) || empty($email)) {
    header('Location: ../crear-usuario.php?status=error_campos');
    exit();
}


// Encriptar la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Conectar a la base de datos
$conexion = conectarDB();

// Verificar si el email o el usuario ya existen para evitar duplicados
try{
    $stmt_check = $conexion->prepare("SELECT ud.email, ul.usuario FROM users_data ud JOIN users_login ul ON ud.idUser = ul.idUser WHERE ud.email = ? OR ul.usuario = ?");
    $stmt_check->bind_param("ss", $email, $usuario);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header('Location: ../crear-usuario.php?status=error_usuario_email_existente');
        exit();
    }
    $stmt_check->close();

    $conexion->begin_transaction();


    // Insertar datos en la tabla users_data
    $stmt_data = $conexion->prepare("INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt_data->bind_param(
        "sssssss", // 7 's' para 7 variables
        $nombre,
        $apellidos,
        $email,
        $telefono,
        $fecha_nacimiento,
        $direccion,
        $sexo
    );
    $stmt_data->execute();
    $id_usuario_generado = $conexion->insert_id; // Obtener el ID del usuario nuevo
    $stmt_data->close();


    // Insertar datos en la tabla login
    $stmt_login = $conexion->prepare("INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?, ?, ?, ?)");

    $stmt_login->bind_param(
        "isss", 
        $id_usuario_generado, 
        $usuario, 
        $hashed_password, 
        $rol
    );
    $stmt_login->execute();
    $stmt_login->close();

    $conexion->commit(); // Confirmar 

    header('Location: ../crear-usuario.php?status=success');
    exit();

} catch (mysqli_sql_exception $e) {
    $conexion->rollback();
    error_log("Error al crear usuario por admin: " . $e->getMessage());
    header('Location: ../crear-usuario.php?status=error_db');
    exit();
} finally {
    if ($conexion) {
        $conexion->close();
    }
}
?>