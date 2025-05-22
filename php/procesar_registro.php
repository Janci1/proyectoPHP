<?php
include './config.php'; 

// Recibir los datos del formulario
$nombre = $_POST['nombre'] ?? ''; // Uso del operador null coalescing para evitar errores si no se envía el campo
$apellidos = $_POST['apellidos'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$sexo = $_POST['sexo'] ?? '';
$password = $_POST['password'] ?? '';

// Validar los datos (Esto es una validación básica)
if (empty($nombre) || empty($apellidos) || empty($email) || empty($usuario) || empty($password) || empty($telefono) || empty($fecha_nacimiento) || empty($direccion)) {
    // Redirigir al formulario de registro con un mensaje de error
    header('Location: ../registro.php?error=campos_obligatorios');
    exit(); // Terminar la ejecución para evitar que el script continúe
}

// Encriptar la contraseña
$password_encriptada = password_hash($password, PASSWORD_DEFAULT);

// Conectar a la base de datos
$conexion = conectarDB();

// Verificar si el email o el usuario ya existen para evitar duplicados
$stmt_check = $conexion->prepare("SELECT email, usuario FROM users_data ud JOIN users_login ul ON ud.idUser = ul.idUser WHERE ud.email = ? OR ul.usuario = ?");
$stmt_check->bind_param("ss", $email, $usuario);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Si ya existe un usuario con ese email o nombre de usuario
    header('Location: ../registro.php?error=email_o_usuario_existente');
    exit();
}
$stmt_check->close();

// Iniciar una transacción para asegurar que ambas inserciones (users_data y users_login) se realicen correctamente o ninguna
$conexion->begin_transaction();

// Insertar datos en la tabla users_data con segurida anti ataques (ayuda de internet)
try {
    $stmt_data = $conexion->prepare("INSERT INTO users_data (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?)");

   $stmt_data->bind_param(
        "sssssss", // 7 's' para 7 variables (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo)
        $nombre, 
        $apellidos, 
        $email, 
        $telefono, 
        $fecha_nacimiento, 
        $direccion, 
        $sexo
    );
    $stmt_data->execute();
    $id_usuario = $conexion->insert_id; // Obtener el ID del usuario recién insertado

    // Insertar datos en la tabla users_login
    $stmt_login = $conexion->prepare("INSERT INTO users_login (idUser, usuario, password, rol) VALUES (?, ?, ?, ?)");
    $rol_defecto = 'user'; // Por defecto, registramos como 'user'
    $stmt_login->bind_param("isss", $id_usuario, $usuario, $password_encriptada, $rol_defecto);
    $stmt_login->execute();

    $conexion->commit(); // Confirmar la transacción
    // Redirigir al login después de un registro exitoso
    header('Location: ../login.php?registro=exito');
    exit();

} catch (Exception $e) {
    $conexion->rollback(); // Deshacer la transacción si algo falla
    // Mostrar un mensaje de error genérico o redirigir con un error
    // echo "Error al registrar el usuario: " . $e->getMessage();
    header('Location: ../registro.php?error=db_fallo');
    exit();
} finally {
    $conexion->close(); // Cerrar la conexión
}
?>