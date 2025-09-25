<?php
session_start();
include 'config.php';
include 'helpers.php';

// Control de acceso para usuarios logueados
if (!isset($_SESSION['idUser'])) {
    header('Location: ../login.php?error=acceso_restringido');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../citaciones.php');
    exit();
}

$conexion = conectarDB();

$accion = $_POST['accion'] ?? '';
$idCita = $_POST['idCita'] ?? '';
$idUser = $_SESSION['idUser'];
$return_to = $_POST['return_to'] ?? 'citaciones.php';

try {
    // solo se aplica a los casos de editar y borrar.
    if ($accion !== 'crear') {
        $stmtCheck = $conexion->prepare("SELECT fecha_cita, hora_cita, idUser FROM citas WHERE idCita = ?");
        $stmtCheck->bind_param("i", $idCita);
        $stmtCheck->execute();
        $resultadoCheck = $stmtCheck->get_result();
        $cita = $resultadoCheck->fetch_assoc();
        $stmtCheck->close();
    
        if (!$cita) {
            $_SESSION['mensajeError'] = "La cita no existe.";
            header("Location: ../" . $return_to);
            exit();
        }

        // Si es un usuario
        if ($_SESSION['rol'] !== 'admin' && $cita['idUser'] !== $idUser) {
            $_SESSION['mensajeError'] = "La cita no te pertenece.";
            header("Location: ../" . $return_to);
            exit();
        }

        // Valida que la cita no esté en el pasado
        $fechaCita = new DateTime($cita['fecha_cita'] . ' ' . $cita['hora_cita']);
        $hoy = new DateTime();
        if ($fechaCita < $hoy) {
            $_SESSION['mensajeError'] = "No puedes modificar ni borrar citas que ya han pasado.";
            header("Location: ../" . $return_to);
            exit();
        }
    }

    //la lógica para cada acción
    switch ($accion) {
        case 'crear':
            $idUserCita = $_POST['idUser'] ?? $idUser;
            $fechaCita = $_POST['fecha_cita'] ?? '';
            $horaCita = $_POST['hora_cita'] ?? '';
            $motivoCita = $_POST['motivo_cita'] ?? '';

            if (empty($fechaCita) || empty($horaCita) || empty($motivoCita)) {
                $_SESSION['mensajeError'] = "Faltan campos obligatorios.";
                // Redirección para el caso de error
                header("Location: ../" . $return_to . "?status=error_campos");
                exit();
            }

            $stmt = $conexion->prepare("INSERT INTO citas (idUser, fecha_cita, hora_cita, motivo_cita) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $idUserCita, $fechaCita, $horaCita, $motivoCita);
            if ($stmt->execute()) {
                $_SESSION['mensajeExito'] = "Cita agendada correctamente.";
                // Redirección para el caso de éxito
                header("Location: ../" . $return_to . "?status=cita_creada");
            } else {
                $_SESSION['mensajeError'] = "Error al agendar la cita.";
                // Redirección para el caso de error
                header("Location: ../" . $return_to . "?status=error_db");
            }
            $stmt->close();
            break;

        case 'editar':
            $fechaCita = $_POST['fecha_cita'] ?? '';
            $horaCita = $_POST['hora_cita'] ?? '';
            $motivoCita = $_POST['motivo_cita'] ?? '';
            
            if (empty($fechaCita) || empty($horaCita) || empty($motivoCita)) {
                $_SESSION['mensajeError'] = "Faltan campos obligatorios.";
                // Redirección para el caso de error
                header("Location: ../" . $return_to . "?status=error_campos");
                exit();
            }
            
            $stmt = $conexion->prepare("UPDATE citas SET fecha_cita = ?, hora_cita = ?, motivo_cita = ? WHERE idCita = ?");
            $stmt->bind_param("sssi", $fechaCita, $horaCita, $motivoCita, $idCita);
            if ($stmt->execute()) {
                $_SESSION['mensajeExito'] = "Cita modificada correctamente.";
                // Redirección para el caso de éxito
                header("Location: ../" . $return_to . "?status=cita_editada");
            } else {
                $_SESSION['mensajeError'] = "Error al modificar la cita.";
                // Redirección para el caso de error
                header("Location: ../" . $return_to . "?status=error_db");
            }
            $stmt->close();
            break;
            
        case 'borrar':
            $stmt = $conexion->prepare("DELETE FROM citas WHERE idCita = ?");
            $stmt->bind_param("i", $idCita);
            if ($stmt->execute()) {
                $_SESSION['mensajeExito'] = "Cita borrada correctamente.";
                // Redirección para el caso de éxito
                header("Location: ../" . $return_to . "?status=cita_borrada");
            } else {
                $_SESSION['mensajeError'] = "Error al borrar la cita.";
                // Redirección para el caso de error
                header("Location: ../" . $return_to . "?status=error_db");
            }
            $stmt->close();
            break;

        default:
            $_SESSION['mensajeError'] = "Acción no válida.";
            // Redirección por defecto para el caso de error
            header("Location: ../" . $return_to);
            break;
    }

    $conexion->close();
    exit();

} catch (mysqli_sql_exception $e) {
    error_log("Error en procesar_cita.php: " . $e->getMessage());
    $_SESSION['mensajeError'] = "Error de base de datos.";
    if (isset($conexion)) {
        $conexion->close();
    }
    // Redirección para el caso de error de base de datos
    header("Location: ../" . $return_to . "?status=error_db");
    exit();
}