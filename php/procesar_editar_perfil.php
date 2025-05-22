<?php
session_start();
include 'config.php';

// Redirigir si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php?error=no_autenticado');
    exit();
}
// Obtener el usuario
$id_usuario = $_SESSION['idUser'];

// Recibir los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$sexo = $_POST['sexo'] ?? '';

// Validar datos (básico, puedes añadir más validaciones)
if (empty($nombre) || empty($apellidos) || empty($email) || empty($telefono) || empty($fecha_nacimiento)) {
    header('Location: ../perfil.php?status=error'); // O un mensaje de error
    exit();
}

// Conectar a la base de datos
$conexion = conectarDB();

try {
    // Preparar la consulta UPDATE para users_data
    $stmt = $conexion->prepare("UPDATE users_data SET nombre = ?, apellidos = ?, email = ?, telefono = ?, fecha_nacimiento = ?, direccion = ?, sexo = ? WHERE idUser = ?");
    
    $stmt->bind_param("sssssssi", $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo, $id_usuario);
    
    $stmt->execute();
    
    header('Location: ../perfil.php?status=success');
    exit();

} catch (Exception $e) {
    header('Location: ../perfil.php?status=error');
    exit();
} finally {
    if (isset($stmt)) { // Asegurarse de que $stmt se haya creado antes de cerrarlo
        $stmt->close();
    }
    $conexion->close();
}
?>