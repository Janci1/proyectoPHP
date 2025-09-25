<?php
session_start();
include 'config.php';
include 'helpers.php';

// Solo administradores pueden procesar esto
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php?error=acceso_denegado');
    exit();
}

$conexion = conectarDB();

try {
    // === Lógica para CREAR NUEVO USUARIO ===
    if (isset($_POST['crear_usuario'])) {
        $nombre = filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_STRING);
        $apellidos = filter_var($_POST['apellidos'] ?? '', FILTER_SANITIZE_STRING);
        $usuario = filter_var($_POST['usuario'] ?? '', FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $telefono = filter_var($_POST['telefono'] ?? '', FILTER_SANITIZE_STRING);
        $fecha_nacimiento = filter_var($_POST['fecha_nacimiento'] ?? '', FILTER_SANITIZE_STRING);
        $direccion = filter_var($_POST['direccion'] ?? '', FILTER_SANITIZE_STRING);
        $sexo = filter_var($_POST['sexo'] ?? '', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        $rol = filter_var($_POST['rol'] ?? '', FILTER_SANITIZE_STRING);

        // Validaciones de campos obligatorios
        if (empty($nombre) || empty($apellidos) || empty($email) || empty($password) || empty($rol) || empty($fecha_nacimiento) || empty($direccion) || empty($sexo) || empty($usuario)) {
            header('Location: ../usuarios-administracion.php?status=error_campos');
            exit();
        }

        // Comprobamos si el email o el nombre de usuario ya existen
        $stmt_check = $conexion->prepare("SELECT idUser FROM users_login WHERE email = ? OR usuario = ?");
        $stmt_check->bind_param("ss", $email, $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $stmt_check->bind_result($idUser);
            $stmt_check->fetch();
            $stmt_check->close();

            // Re-ejecutar para saber cuál de los dos existe
            $stmt_check_email_exists = $conexion->prepare("SELECT idUser FROM users_login WHERE email = ?");
            $stmt_check_email_exists->bind_param("s", $email);
            $stmt_check_email_exists->execute();
            $stmt_check_email_exists->store_result();
            if ($stmt_check_email_exists->num_rows > 0) {
                header('Location: ../usuarios-administracion.php?status=email_existe');
                exit();
            }
            $stmt_check_email_exists->close();

            $stmt_check_usuario_exists = $conexion->prepare("SELECT idUser FROM users_login WHERE usuario = ?");
            $stmt_check_usuario_exists->bind_param("s", $usuario);
            $stmt_check_usuario_exists->execute();
            $stmt_check_usuario_exists->store_result();
            if ($stmt_check_usuario_exists->num_rows > 0) {
                 header('Location: ../usuarios-administracion.php?status=usuario_existe');
                exit();
            }
            $stmt_check_usuario_exists->close();
        }

        $stmt_check->close();

        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar en users_login primero para obtener el idUser
        $stmt_login = $conexion->prepare("INSERT INTO users_login (email, password, rol, usuario) VALUES (?, ?, ?, ?)");
        $stmt_login->bind_param("ssss", $email, $hashed_password, $rol, $usuario);
        $stmt_login->execute();
        $idUser = $stmt_login->insert_id;
        $stmt_login->close();

        if ($idUser) {
            // Insertar en users_data usando el idUser que acabamos de obtener
            $stmt_data = $conexion->prepare("INSERT INTO users_data (idUser, nombre, apellidos, email, telefono, fecha_nacimiento, direccion, sexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_data->bind_param("isssssss", $idUser, $nombre, $apellidos, $email, $telefono, $fecha_nacimiento, $direccion, $sexo);
            $stmt_data->execute();
            $stmt_data->close();
            header('Location: ../usuarios-administracion.php?status=usuario_creado');
            exit();
        } else {
            // Si falla el insert en users_login
            header('Location: ../usuarios-administracion.php?status=error_db');
            exit();
        }
    } elseif (isset($_POST['borrar_usuario']) && !empty($_POST['borrar_usuario'])) {
        // === Lógica para BORRAR USUARIO ===
        $id_usuario_a_borrar = filter_var($_POST['borrar_usuario'], FILTER_SANITIZE_NUMBER_INT);
        // Eliminar de users_data
        $stmt_data = $conexion->prepare("DELETE FROM users_data WHERE idUser = ?");
        $stmt_data->bind_param("i", $id_usuario_a_borrar);
        $stmt_data->execute();
        $stmt_data->close();
        // Eliminar de users_login
        $stmt_login = $conexion->prepare("DELETE FROM users_login WHERE idUser = ?");
        $stmt_login->bind_param("i", $id_usuario_a_borrar);
        $stmt_login->execute();
        $stmt_login->close();
        header('Location: ../usuarios-administracion.php?status=usuario_borrado');
        exit();
    } elseif (isset($_POST['guardar_cambios']) && isset($_POST['usuarios']) && is_array($_POST['usuarios'])) {
        // === Lógica para GUARDAR TODOS LOS CAMBIOS DE USUARIOS ===
        foreach ($_POST['usuarios'] as $idUser => $datos) {
            $idUser = filter_var($idUser, FILTER_SANITIZE_NUMBER_INT);
            $nombre = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_STRING);
            $email = filter_var($datos['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $telefono = filter_var($datos['telefono'] ?? '', FILTER_SANITIZE_STRING);
            $rol = filter_var($datos['rol'] ?? '', FILTER_SANITIZE_STRING);
            
            // Actualizar users_data
            if (!empty($nombre) || !empty($email) || !empty($telefono)) {
                $stmt_data = $conexion->prepare("UPDATE users_data SET nombre = ?, email = ?, telefono = ? WHERE idUser = ?");
                $stmt_data->bind_param("sssi", $nombre, $email, $telefono, $idUser);
                $stmt_data->execute();
                $stmt_data->close();
            }

            // Actualizar users_login
            if (!empty($rol)) {
                $stmt_login = $conexion->prepare("UPDATE users_login SET rol = ? WHERE idUser = ?");
                $stmt_login->bind_param("si", $rol, $idUser);
                $stmt_login->execute();
                $stmt_login->close();
            }
        }
        header('Location: ../usuarios-administracion.php?status=cambios_guardados');
        exit();
    } else {
        header('Location: ../usuarios-administracion.php?status=no_accion');
        exit();
    }
} catch (mysqli_sql_exception $e) {
    error_log("Error al procesar usuarios: " . $e->getMessage());
    header('Location: ../usuarios-administracion.php?status=error_db');
    exit();
} finally {
    if ($conexion) {
        $conexion->close();
    }
}
?>