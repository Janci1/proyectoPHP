<?php
session_start();
include '../php/config.php';
$conexion = conectarDB();

// Asegúrate de que el acceso es a través de POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contacto.php');
    exit();
}

$idUser = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : NULL;
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$asunto = htmlspecialchars(trim($_POST['asunto']));
$mensaje = htmlspecialchars(trim($_POST['mensaje']));

// Validar campos obligatorios y formato de email
if (empty($email) || empty($asunto) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../contacto.php?status=error_campos');
    exit();
}

try {
    // Preparar la consulta para insertar los datos en la tabla de incidencias
    $stmt = $conexion->prepare("INSERT INTO incidencias (idUser, email, asunto, mensaje) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $idUser, $email, $asunto, $mensaje);

    if ($stmt->execute()) {
        header('Location: ../contacto.php?status=exito');
    } else {
        error_log("Error al insertar la incidencia: " . $stmt->error);
        header('Location: ../contacto.php?status=error_db');
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Error al procesar el contacto: " . $e->getMessage());
    header('Location: ../contacto.php?status=error_db');
} finally {
    if ($conexion) {
        $conexion->close();
    }
}