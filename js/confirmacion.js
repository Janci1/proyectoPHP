document.addEventListener('DOMContentLoaded', function() {
            // Selecciona todos los botones de borrar
            const botonesBorrar = document.querySelectorAll('.botonBorrarUsuario');

            botonesBorrar.forEach(function(boton) {
                boton.addEventListener('click', function(event) {
                    // Previene el envío del formulario por defecto
                    event.preventDefault();

                    const idUsuario = this.value; // Obtiene el ID del usuario del valor del botón
                    const nombreUsuario = this.closest('tr').querySelector('td:nth-child(1)').textContent.trim(); //nombre de usuario 

                    // Muestra confirmación
                    const confirmar = confirm(`¿Estás seguro de que quieres borrar al usuario "${nombreUsuario}" (ID: ${idUsuario})? Esta acción es irreversible.`);

                    if (confirmar) {
                        const formTemporal = document.createElement('form');
                        formTemporal.method = 'POST';
                        formTemporal.action = 'php/procesar_usuario.php';

                        const inputBorrar = document.createElement('input');
                        inputBorrar.type = 'hidden';
                        inputBorrar.name = 'borrar_usuario';
                        inputBorrar.value = idUsuario;
                        formTemporal.appendChild(inputBorrar);

                        document.body.appendChild(formTemporal);
                        formTemporal.submit();
                    }
                });
            });

            // Opcional: Ocultar el mensaje de estado después de unos segundos
            const mensajeEstado = document.querySelector('.mensaje-estado');
            if (mensajeEstado) {
                setTimeout(() => {
                    mensajeEstado.style.opacity = '0';
                    setTimeout(() => mensajeEstado.remove(), 500); // Eliminar después de la transición
                }, 5000); // Ocultar después de 5 segundos
            }
        });
