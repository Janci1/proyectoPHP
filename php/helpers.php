<?php
// mostrar la barra de navegación

function mostrarNavegacion($pagina_actual = '') {
    // Definir enlaces comunes para todos
    $enlaces_comunes = [
        'Inicio' => 'index.php',
        'Noticias' => 'noticias.php',
    ];

    // Enlaces solo para visitantes (no logueados)
    $enlaces_visitante = [
        'Iniciar sesion' => 'login.php',
        'Registrarse' => 'registro.php'
    ];

    // Enlaces para usuarios logueados (user o admin)
    $enlaces_logueado = [
        'Citaciones' => 'citaciones.php', // Para usuarios normales
        'Mi Perfil' => 'perfil.php',
        'Cerrar Sesión' => 'php/logout.php'
    ];

    // Enlaces solo para administradores
    $enlaces_admin = [
        'Noticias-Administracion' => 'noticias-administracion.php',
        'Citaciones-Administracion' => 'citas-administracion.php',
        'Usuarios-Administracion' => 'usuarios-administracion.php',
    ];

    // Determinar el rol del usuario
    $rol = $_SESSION['rol'] ?? 'visitante'; // 'visitante' si no hay sesión o rol

    echo '<header>';
    echo '<nav>';
    echo '<div class="contenedorPrincipal">';
    echo '<img src="img/otakutattoo.png" alt="Logo" class="logo" style="height:70px"/>';
    echo '<ul cass="linksUsuario">';
    // Lógica para mostrar enlaces según el rol
    if ($rol === 'admin') {
        echo '<li class="linksDerecha">'; 
        echo '<a href="perfil.php"><img src="ruta/a/imagen_perfil_default.png" alt="Perfil" class="imagenPerfil"></a>'; // Para imagen
        echo '<a href="' . $enlaces_logueado['Mi Perfil'] . '">Mi Perfil</a> / <a href="' . $enlaces_logueado['Cerrar Sesión'] . '">Cerrar Sesión</a>';
        echo '</li>';
    } elseif ($rol === 'user') {
         echo '<li class="linksDerecha">'; 
        echo '<a href="perfil.php"><img src="ruta/a/imagen_perfil_default.png" alt="Perfil" class="imagenPerfil"></a>'; // Para imagen
        echo '<div class = linksUser>';
        echo '<a href="' . $enlaces_logueado['Mi Perfil'] . '">Mi Perfil</a>';
        echo '<a href="' . $enlaces_logueado['Cerrar Sesión'] . '">Cerrar Sesión</a>';
        echo '</div>';
        echo '</li>';
    }elseif ($rol === 'visitante'){
        echo '<li class="linksDerecha">'; // Para alinear a la derecha
        echo '<a href="' . $enlaces_visitante['Iniciar sesion'] . '">Iniciar Sesión</a>';
        echo '</li>';
        echo '<li><a href="' . $enlaces_visitante['Registrarse'] . '">Registrarse</a></li>'; // Esto podría ir junto a Iniciar Sesión
    } 
    echo '</ul>';    
    echo '</div>'; 


    echo '<ul class="navegador">';

    // Mostrar enlaces comunes
    foreach ($enlaces_comunes as $texto => $url) {
        $clase_activa = ($pagina_actual === strtolower(str_replace('.php', '', $url))) ? 'class="active"' : '';
        echo '<li><a href="' . $url . '" ' . $clase_activa . '>' . $texto . '</a></li>';
    }

    // Lógica para mostrar enlaces según el rol
    if ($rol === 'admin') {
        // Enlaces de administración
        foreach ($enlaces_admin as $texto => $url) {
            $clase_activa = ($pagina_actual === strtolower(str_replace('.php', '', $url))) ? 'class="active"' : '';
            echo '<li><a href="' . $url . '" ' . $clase_activa . '>' . $texto . '</a></li>';
        }
        

    } elseif ($rol === 'user') {
        // Enlaces de usuario normal (citas, perfil, cerrar sesión)
        $clase_citas = ($pagina_actual === 'Citaciones') ? 'class="active"' : '';
        echo '<li><a href="' . $enlaces_logueado['Citaciones'] . '" ' . $clase_citas . '>Citaciones</a></li>';
        echo '<li><a href="nosotros.php">Nosotros</a></li>';
        echo '<li><a href="contacto.php">Contacto</a></li>';
    } else { // Visitante
        // Enlaces para visitantes
        echo '<li><a href="nosotros.php">Nosotros</a></li>';
        echo '<li><a href="contacto.php">Contacto</a></li>';
        

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
}
?>