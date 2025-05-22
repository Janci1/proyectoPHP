document.addEventListener('DOMContentLoaded', function() {
    const perfil = document.getElementById('perfilFijo');
    const perfilEditar = document.getElementById('perfilEditable');
    const botonEditar = document.getElementById('botonParaEditar');
    const botonCancelar = document.getElementById('botonParaCancelar');

    if (botonEditar && perfil && perfilEditar) {
        botonEditar.addEventListener('click', function() {
            perfil.style.display = 'none';
            perfilEditar.style.display = 'block';
        });
    }

    if (botonCancelar && perfil && perfilEditar) {
        botonCancelar.addEventListener('click', function() {
            perfilEditar.style.display = 'none';
            perfil.style.display = 'block';
        });
    }
});