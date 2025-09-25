document.addEventListener('DOMContentLoaded', function() {
    // ---- Lógica para el botón "Crear Nueva Noticia" ----
    const botonNuevaNoticia = document.getElementById('nuevaNoticia');
    const formularioNuevaNoticia = document.getElementById('formularioNoticia');
    const cancelarNuevaNoticia = document.getElementById('cancelarNoticia');

    if (botonNuevaNoticia && formularioNuevaNoticia && cancelarNuevaNoticia) {
        botonNuevaNoticia.addEventListener('click', function() {
            formularioNuevaNoticia.style.display = 'block';
            botonNuevaNoticia.style.display = 'none'; // Ocultar el botón "Crear"
        });

        cancelarNuevaNoticia.addEventListener('click', function() {
            formularioNuevaNoticia.style.display = 'none';
            botonNuevaNoticia.style.display = 'block'; // Mostrar de nuevo el botón "Crear"
        });
    }

    // ---- Lógica para los botones "Editar" y "Cancelar" de cada noticia ----
    const botonesEditarNoticia = document.querySelectorAll('.editarNoticia');
    const botonesCancelarEditar = document.querySelectorAll('.cancelarEditar');

    botonesEditarNoticia.forEach(boton => {
        boton.addEventListener('click', function() {
            const idNoticia = this.dataset.id; // Obtiene el ID de la noticia
            const noticiaFija = document.getElementById(`noticiaFija-${idNoticia}`);
            const noticiaEditable = document.getElementById(`noticiaEditable-${idNoticia}`);

            if (noticiaFija && noticiaEditable) {
                noticiaFija.style.display = 'none';
                noticiaEditable.style.display = 'block';
            }
        });
    });

    botonesCancelarEditar.forEach(boton => {
        boton.addEventListener('click', function() {
            const idNoticia = this.dataset.id; // Obtiene el ID de la noticia
            const noticiaFija = document.getElementById(`noticiaFija-${idNoticia}`);
            const noticiaEditable = document.getElementById(`noticiaEditable-${idNoticia}`);

            if (noticiaFija && noticiaEditable) {
                noticiaFija.style.display = 'block';
                noticiaEditable.style.display = 'none';
            }
        });
    });
});