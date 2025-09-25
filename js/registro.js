document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');

    if (form) {
        // El resto de la lógica de validación
        form.addEventListener('submit', function(event) {
            // Aquí se ejecuta la validación y el event.preventDefault() si hay errores
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');

    if (form) {
        form.addEventListener('submit', function(event) {
            // Limpiar todos los mensajes de error anteriores
            document.querySelectorAll('.error-message').forEach(span => {
                span.textContent = '';
            });

            const nombre = document.getElementById('nombre');
            const apellidos = document.getElementById('apellidos');
            const email = document.getElementById('email');
            const telefono = document.getElementById('telefono');
            const fechaNacimiento = document.getElementById('fecha_nacimiento');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');

            let tieneErrores = false;

            // Validación de campos obligatorios
            if (!nombre.value.trim() || !apellidos.value.trim() || !email.value.trim() || !password.value || !passwordConfirm.value) {
                document.getElementById('error-nombre').textContent = 'Todos los campos obligatorios deben ser rellenados.';
                tieneErrores = true;
            }

            // Validar formato de datos
            const regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (nombre.value.trim() && !regexLetras.test(nombre.value.trim())) {
                document.getElementById('error-nombre').textContent = 'Solo se permiten letras y espacios.';
                tieneErrores = true;
            }
            if (apellidos.value.trim() && !regexLetras.test(apellidos.value.trim())) {
                document.getElementById('error-apellidos').textContent = 'Solo se permiten letras y espacios.';
                tieneErrores = true;
            }

            if (email.value.trim() && !/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(email.value.trim())) {
                document.getElementById('error-email').textContent = 'Formato de email inválido.';
                tieneErrores = true;
            }
            
            if (telefono.value.trim() && !/^[0-9]{9}$/.test(telefono.value.trim())) {
                document.getElementById('error-telefono').textContent = 'El teléfono debe tener 9 dígitos.';
                tieneErrores = true;
            }

            if (fechaNacimiento.value) {
                const fecha_valida = new Date(fechaNacimiento.value);
                const hoy = new Date();
                if (isNaN(fecha_valida.getTime()) || fecha_valida > hoy) {
                    document.getElementById('error-fecha').textContent = 'La fecha de nacimiento no es válida.';
                    tieneErrores = true;
                }
            }

            if (password.value !== passwordConfirm.value) {
                document.getElementById('error-password_confirm').textContent = 'Las contraseñas no coinciden.';
                tieneErrores = true;
            }
            if (password.value.length < 6) {
                document.getElementById('error-password').textContent = 'Mínimo 6 caracteres.';
                tieneErrores = true;
            }

            if (tieneErrores) {
                event.preventDefault();
            }
        });
    }
});