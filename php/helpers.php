<?php
// mostrar la barra de navegación

function mostrarNavegacion($pagina_actual = '') {
    // Definir enlaces comunes para todos
    $enlacesComunes = [
        'INICIO' => 'index.php',
        'NOTICIAS' => 'noticias.php',
    ];

    // Enlaces solo para visitantes (no logueados)
    $enlacesVisitante = [
        'Iniciar sesion' => 'login.php',
        'Registrarse' => 'registro.php'
    ];

    // Enlaces para usuarios logueados (user o admin)
    $enlacesLogueado = [
        'Citaciones' => 'citaciones.php', // Para usuarios normales
        'Mi Perfil' => 'perfil.php',
        'Cerrar Sesión' => 'php/logout.php'
    ];

    // Enlaces solo para administradores
    $enlacesAdmin = [
        'Noticias-Administracion' => 'noticias-administracion.php',
        'Citaciones-Administracion' => 'citas-administracion.php',
        'Usuarios-Administracion' => 'usuarios-administracion.php',
        'Incidencias' => 'incidencias.php' ,
    ];

    // Determinar el rol del usuario
    $rol = $_SESSION['rol'] ?? 'visitante'; // 'visitante' si no hay sesión o rol

    echo '<link rel="stylesheet" href="css/helpers.css">';
    echo '<header>';
    echo '<nav>';
    echo '<div class="contenedorPrincipal">';
    echo '<a href="index.php"><img src="img/otakutattoo.png" alt="Logo" class="logo"/></a>';
    echo '<ul class="linksRol">';
    // Lógica para mostrar enlaces según el rol
    if ($rol === 'admin') {
        echo '<li class="linksDerecha">'; 
        echo '<li><a href="perfil.php"><img src="img/perfilAdmin.jpeg" alt="Perfil" class="imagenPerfil"></a>'; // Para imagen
        echo '<li><div class = linksAdmin>';
        echo '<a href="' . $enlacesLogueado['Mi Perfil'] . '">Mi Perfil</a>';
        echo '<a href="' . $enlacesLogueado['Cerrar Sesión'] . '">Cerrar Sesión</a>';
        echo '</div>';
        echo '</li>';
    } elseif ($rol === 'user') {
         echo '<li class="linksDerecha">'; 
        echo '<li><a href="perfil.php"><img src="img/perfilUsuario.jpeg" alt="Perfil" class="imagenPerfil"></a>'; // Para imagen
        echo '<li><div class = linksUsuario>';
        echo '<a href="' . $enlacesLogueado['Mi Perfil'] . '">Mi Perfil</a>';
        echo '<a href="' . $enlacesLogueado['Cerrar Sesión'] . '">Cerrar Sesión</a>';
        echo '</div>';
        echo '</li>';
    }elseif ($rol === 'visitante'){
        echo '<li class="linksDerecha">'; // Para alinear a la derecha
        echo '<li><a href="perfil.php"><img src="img/perfilDefault.jpeg" alt="Perfil" class="imagenPerfil"></a>'; // Para imagen
        echo '<li><div class = linksVisitante>';
        echo '<a href="' . $enlacesVisitante['Iniciar sesion'] . '">Iniciar Sesión</a>';
        echo '<a href="' . $enlacesVisitante['Registrarse'] . '">Registrarse</a></li>'; // Esto podría ir junto a Iniciar Sesión
        echo '</div>';
        echo '</li>';
    } 
    echo '</ul>';    
    echo '</div>'; 


    echo '<ul class="navegador">';

    // Mostrar enlaces comunes
    foreach ($enlacesComunes as $texto => $url) {
        $claseActiva = ($pagina_actual === strtolower(str_replace('.php', '', $url))) ? 'class="active"' : '';
        echo '<li><a href="' . $url . '" ' . $claseActiva . '>' . $texto . '</a></li>';
    }

    // Lógica para mostrar enlaces según el rol
    if ($rol === 'admin') {
        // Enlaces de administración
        foreach ($enlacesAdmin as $texto => $url) {
            $claseActiva = ($pagina_actual === strtolower(str_replace('.php', '', $url))) ? 'class="active"' : '';
            echo '<li><a href="' . $url . '" ' . $claseActiva . '>' . $texto . '</a></li>';
        }
        

    } elseif ($rol === 'user') {
        // Enlaces de usuario normal (citas, perfil, cerrar sesión)
        $claseCitas = ($pagina_actual === 'Citaciones') ? 'class="active"' : '';
        echo '<li><a href="' . $enlacesLogueado['Citaciones'] . '" ' . $claseCitas . '>CITACIONES</a></li>';
        echo '<li><a href="nosotros.php">NOSOTROS</a></li>';
        echo '<li><a href="contacto.php">CONTACTO</a></li>';
    } else { // Visitante
        // Enlaces para visitantes
        echo '<li><a href="nosotros.php">NOSOTROS</a></li>';
        echo '<li><a href="contacto.php">CONTACTO</a></li>';
        

    }

    echo '</ul>';
    echo '</nav>';
    echo '</header>';
}

// Función para mostrar el pie de página
function mostrarPieDePagina() {
    echo '<footer>';
    echo '    <p>&copy; ' . date("Y") . ' ' . OtakuTatto . '</p>'; // Usa NOMBRE_SITIO de config.php
    echo '</footer>';
    echo '</div>';
}
?>