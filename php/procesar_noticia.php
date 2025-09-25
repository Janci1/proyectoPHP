<?php
session_start();
include 'config.php';
include 'helpers.php'; 

// Redirigir si el usuario no está logueado o no es administrador
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['rol'] !== 'admin') {
      header('Location: ../index.php?error=acceso_denegado');
      exit();
}

// Conectar a la base de datos
$conexion = conectarDB();

$accion = $_POST['accion'] ?? ''; // 'crear', 'actualizar', 'borrar'

try {
    switch ($accion) {
            case 'crear':
                  $titulo = $_POST['titulo'] ?? '';
                  $texto = $_POST['texto'] ?? '';
                  $idUser = $_POST['idUser'] ?? $_SESSION['idUser'];
            $imagen_path = 'img/noticias/pre.jpeg'; // Por defecto

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                // Directorio donde se guardarán las imágenes.
                // Asegúrate de que este directorio existe y tiene permisos de escritura.
                $directorio_subidas = "../img/noticias/";
                if (!is_dir($directorio_subidas)) {
                    mkdir($directorio_subidas, 0777, true);
                }
                
                $nombre_archivo = uniqid() . '-' . basename($_FILES['imagen']['name']);
                $ruta_completa = $directorio_subidas . $nombre_archivo;
                $tipo_archivo = strtolower(pathinfo($ruta_completa, PATHINFO_EXTENSION));

                // Validación de tipo de archivo
                $permitidos = array("jpg", "jpeg", "png", "gif");
                if (!in_array($tipo_archivo, $permitidos)) {
                    header('Location: ../noticias-administracion.php?status=error_imagen_tipo');
                    exit();
                }

                // Validación de tamaño (5MB máximo)
                if ($_FILES['imagen']['size'] > 5000000) { 
                    header('Location: ../noticias-administracion.php?status=error_imagen_tamano');
                    exit();
                }

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                    $imagen_path = 'img/noticias/' . $nombre_archivo;
                } else {
                    header('Location: ../noticias-administracion.php?status=error_imagen_subida');
                    exit();
                }
            }

                  if (empty($titulo) || empty($texto) || !is_numeric($idUser) || $idUser <= 0) { 
                        header('Location: ../noticias-administracion.php?status=error_campos');
                        exit();
                  }

            $fecha = date('Y-m-d H:i:s');
                  $stmt = $conexion->prepare("INSERT INTO noticias (idUser, titulo, imagen, texto, fecha) VALUES (?, ?, ?, ?, ?)");
                  $stmt->bind_param("issss", $idUser, $titulo, $imagen_path, $texto, $fecha);
                  $stmt->execute();
                  $stmt->close();
                  header('Location: ../noticias-administracion.php?status=noticia_creada');
                  exit();

            case 'actualizar':
                  $idNoticia = $_POST['idNoticia'] ?? '';
                  $titulo = $_POST['titulo'] ?? '';
                  $texto = $_POST['texto'] ?? '';
            
            // Lógica para actualizar la imagen
            $imagen_path = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $directorio_subidas = "../img/noticias/";
                if (!is_dir($directorio_subidas)) {
                    mkdir($directorio_subidas, 0777, true);
                }
                
                $nombre_archivo = uniqid() . '-' . basename($_FILES['imagen']['name']);
                $ruta_completa = $directorio_subidas . $nombre_archivo;
                $tipo_archivo = strtolower(pathinfo($ruta_completa, PATHINFO_EXTENSION));

                // Validación de tipo de archivo
                $permitidos = array("jpg", "jpeg", "png", "gif");
                if (!in_array($tipo_archivo, $permitidos)) {
                    header('Location: ../noticias-administracion.php?status=error_imagen_tipo');
                    exit();
                }

                // Validación de tamaño (5MB máximo)
                if ($_FILES['imagen']['size'] > 5000000) { 
                    header('Location: ../noticias-administracion.php?status=error_imagen_tamano');
                    exit();
                }

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
                    $imagen_path = 'img/noticias/' . $nombre_archivo;
                } else {
                    header('Location: ../noticias-administracion.php?status=error_imagen_subida');
                    exit();
                }
            }


                  if (empty($idNoticia) || empty($titulo) || empty($texto)) {
                        header('Location: ../noticias-administracion.php?status=error_campos');
                        exit();
                  }

            // Lógica para actualizar la noticia con o sin imagen
            if ($imagen_path) {
                // Con imagen nueva
                $stmt = $conexion->prepare("UPDATE noticias SET titulo = ?, texto = ?, imagen = ? WHERE idNoticia = ?");
                $stmt->bind_param("sssi", $titulo, $texto, $imagen_path, $idNoticia);
            } else {
                // Sin imagen nueva
                $stmt = $conexion->prepare("UPDATE noticias SET titulo = ?, texto = ? WHERE idNoticia = ?");
                $stmt->bind_param("ssi", $titulo, $texto, $idNoticia);
            }

                  $stmt->execute();
                  $stmt->close();
                  header('Location: ../noticias-administracion.php?status=noticia_actualizada');
                  exit();

            case 'borrar':
            // ... (el código para borrar es el mismo)
                  $idNoticia = $_POST['idNoticia'] ?? '';

                  if (empty($idNoticia)) {
                        header('Location: ../noticias-administracion.php?status=error_id_noticia');
                        exit();
                  }

                  $stmt = $conexion->prepare("DELETE FROM noticias WHERE idNoticia = ?");
                  $stmt->bind_param("i", $idNoticia);
                  $stmt->execute();
                  $stmt->close();
                  header('Location: ../noticias-administracion.php?status=noticia_borrada');
                  exit();

            default:
                  header('Location: ../noticias-administracion.php?status=error');
                  exit();
      }
} catch (mysqli_sql_exception $e) {
      error_log("Error en procesamiento de noticias: " . $e->getMessage());
      header('Location: ../noticias-administracion.php?status=error_db');
      exit();
} finally {
      if ($conexion) {
            $conexion->close();
      }
}
?>