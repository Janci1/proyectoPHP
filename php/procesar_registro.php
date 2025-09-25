<<?php
session_start();
include 'config.php'; 
include 'helpers.php';

//Validar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro.php');
    exit();
}

//Recibir los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$direccion = trim($_POST['direccion'] ?? '');
$sexo = $_POST['sexo'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

//Realizar validaciones de datos
$errores = [];

// Validación de campos obligatorios
// La "dirección" y el "sexo" no son obligatorios
if (empty($nombre) || empty($apellidos) || empty($email) || empty($usuario) || empty($password) || empty($telefono) || empty($fecha_nacimiento)) {
    $_SESSION['errores_registro'] = ['Por favor, rellena todos los campos obligatorios.'];
    header('Location: ../registro.php?status=validacion_fallida');
    exit();
}

//Validación de formato de datos
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $nombre)) {
    $errores[] = "El nombre solo puede contener letras y espacios.";
}
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $apellidos)) {
    $errores[] = "Los apellidos solo pueden contener letras y espacios.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del email no es válido.";
}
if (!preg_match("/^[0-9]{9}$/", $telefono)) {
    $errores[] = "El teléfono debe contener 9 dígitos numéricos.";
}
if ($password !== $password_confirm) {
    $errores[] = "Las contraseñas no coinciden.";
}
if (strlen($password) < 6) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
}
// Validación de fecha de nacimiento
$fecha_nacimiento_obj = DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
$hoy = new DateTime();
if ($fecha_nacimiento_obj === false || $fecha_nacimiento_obj > $hoy) {
    $errores[] = "La fecha de nacimiento no es válida.";
}

//Si hay errores de validación, redirigir
if (count($errores) > 0) {
    $_SESSION['errores_registro'] = $errores;
    header('Location: ../registro.php?status=validacion_fallida');
    exit();
}

//Conectar a la base de datos y verificar si el email o usuario ya existen
$conexion = conectarDB();

$stmt_check = $conexion->prepare("SELECT email FROM users_data WHERE email = ? UNION ALL SELECT usuario FROM users_login WHERE usuario = ?");
$stmt_check->bind_param("ss", $email, $usuario);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $_SESSION['errores_registro'] = ['El email o el nombre de usuario ya están registrados.'];
    header('Location: ../registro.php?status=email_o_usuario_existente');
    exit();
}
$stmt_check->close();

//Inserción de datos con transacciones
$conexion->begin_transaction();

try {
    //Insertar en users_data
    $stmt_data = $conexion->prepare("INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_data->bind_param("sssssss", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo);
    $stmt_data->execute();
    $id_usuario = $conexion->insert_id;
    $stmt_data->close();

    // Insertar en users_login
    $stmt_login = $conexion->prepare("INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?, ?, ?, ?)");
    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);
    $rol_defecto = 'user';
    $stmt_login->bind_param("isss", $id_usuario, $usuario, $password_encriptada, $rol_defecto);
    $stmt_login->execute();
    $stmt_login->close();

    $conexion->commit();
    
    $_SESSION['mensaje_registro'] = "Registro completado con éxito. Ya puedes iniciar sesión.";
    header('Location: ../login.php?status=registro_exitoso');
    exit();

} catch (Exception $e) {
    $conexion->rollback();
    error_log("Error al registrar el usuario: " . $e->getMessage());
    $_SESSION['errores_registro'] = ["Hubo un problema al intentar registrarte. Por favor, inténtalo de nuevo más tarde."];
    header('Location: ../registro.php?status=db_fallo');
    exit();
} finally {
    $conexion->close();
}
?>