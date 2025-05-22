<?php
define('DB_HOST', '127.0.0.1'); //definimos el host de la base de datos de infinityfree
define('DB_USER', 'root'); //definimos el usuario de la base de datos DB = data base
define('DB_PASSWORD', ''); //definimos la contraseña
define('DB_NAME', 'otakutattoo'); //definimos el nombre 

//creamos una funcion para conetar la base de datos
function conectarDB(){
    $conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if (!mysqli_set_charset($conexion, "utf8mb4")) {
        die("Error al conectar la base de datos: " . mysqli_connect_error());
    }
    
    return $conexion;
}

define('OtakuTatto','OtakuTattoo');

?>