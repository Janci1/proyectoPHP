<?php
session_start();
include 'config.php';

// Control de acceso para administradores
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../login.php?error=acceso_restringido');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = conectarDB();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'borrar') {
        $idIncidencia = $_POST['idIncidencia'] ?? null;

        if ($idIncidencia) {
            $stmt = $conexion->prepare("DELETE FROM incidencias WHERE idIncidencia = ?");
            $stmt->bind_param("i", $idIncidencia);
            $stmt->execute();
            $stmt->close();
            $conexion->close();
            header('Location: ../incidencias.php?status=incidencia_borrada');
            exit();
        }
    }
}
header('Location: ../incidencias.php?status=error_accion');
exit();
?>