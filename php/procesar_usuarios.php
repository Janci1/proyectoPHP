<?php
session_start();
include 'config.php'; // Incluye el archivo de configuración
include 'helpers.php'; //navegador y footer

//Solo administradores pueden procesar esto
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php?error=acceso_denegado');
    exit();
}

$conexion = conectarDB();

try {
    //BORRAR USUARIO
    // Detectamos si se ha pulsado un botón de "Borrar"
    if (isset($_POST['borrar_usuario']) && !empty($_POST['borrar_usuario'])) {
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

        // Redirigir de vuelta a la página de administración con un mensaje de éxito
        header('Location: ../usuarios-administracion.php?status=usuario_borrado');
        exit();

    } elseif (isset($_POST['guardar_cambios']) && isset($_POST['usuarios']) && is_array($_POST['usuarios'])) {
        // === 3. Lógica para GUARDAR TODOS LOS CAMBIOS DE USUARIOS ===
        foreach ($_POST['usuarios'] as $idUser => $datos) {
            $idUser = filter_var($idUser, FILTER_SANITIZE_NUMBER_INT);
            $nombre = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_STRING);
            $email = filter_var($datos['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $telefono = filter_var($datos['telefono'] ?? '', FILTER_SANITIZE_STRING); // Considerar validación de número
            $rol = filter_var($datos['rol'] ?? '', FILTER_SANITIZE_STRING);

            // Validaciones recibidos
            if (empty($nombre) || empty($email) || empty($telefono) || empty($rol)) {
                continue; // Saltar a la siguiente iteración del bucle
            }

            // Actualizar users_data
            $stmt_data = $conexion->prepare("UPDATE users_data SET nombre = ?, email = ?, telefono = ? WHERE idUser = ?");
            $stmt_data->bind_param("sssi", $nombre, $email, $telefono, $idUser);
            $stmt_data->execute();
            $stmt_data->close(); // Cerrar el statement después de usarlo

            // Actualizar users_login
            $stmt_login = $conexion->prepare("UPDATE users_login SET rol = ? WHERE idUser = ?");
            $stmt_login->bind_param("si", $rol, $idUser);
            $stmt_login->execute();
            $stmt_login->close(); // Cerrar el statement
        }

        // Redirigir de vuelta a la página de administración con un mensaje de éxito
        header('Location: ../usuarios-administracion.php?status=cambios_guardados');
        exit();

    } else {
        // Si se accede sin una acción válida (ni borrar ni guardar)
        header('Location: ../usuarios-administracion.php?status=no_accion');
        exit();
    }

} catch (mysqli_sql_exception $e) {
    // Capturar cualquier error de la base de datos
    error_log("Error al procesar usuarios: " . $e->getMessage()); // Registrar el error
    header('Location: ../usuarios-administracion.php?status=error_db');
    exit();
} finally {
    // Asegurarse de que la conexión a la base de datos se cierre
    if ($conexion) {
        $conexion->close();
    }
}
?>